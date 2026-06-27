<?php

namespace App\Services;

use App\Enums\OnlineExamGradingMode;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Evaluation;
use App\Models\GradeRecord;
use App\Models\OnlineExam;
use App\Models\OnlineExamAttempt;
use App\Models\VirtualClassroom;

/**
 * Sincroniza calificaciones del LMS (tareas y exámenes online) con grade_records.
 */
final class LMSGradeSyncService
{
    public function syncAssignmentSubmission(AssignmentSubmission $submission): void
    {
        if (! config('lms.grade_sync.enabled', true)) {
            return;
        }

        $submission->loadMissing('assignment.virtualClassroom.section.grade');

        if ($submission->score === null) {
            return;
        }

        $assignment = $submission->assignment;
        $evaluation = $this->resolveEvaluationForAssignment($assignment);
        $recordedBy = $submission->reviewed_by_user_id ?? $assignment->created_by_user_id;

        $this->upsertGradeRecord(
            evaluation: $evaluation,
            studentId: $submission->student_id,
            score: (float) $submission->score,
            recordedByUserId: $recordedBy,
            observations: $this->assignmentObservation($submission),
        );
    }

    public function syncOnlineExamAttempt(OnlineExamAttempt $attempt): void
    {
        if (! config('lms.grade_sync.enabled', true)) {
            return;
        }

        $attempt->loadMissing('onlineExam.virtualClassroom.section.grade');

        if ($attempt->score_percent === null) {
            return;
        }

        $exam = $attempt->onlineExam;

        if ($exam->grading_mode === OnlineExamGradingMode::Manual && (float) $attempt->score_percent <= 0) {
            return;
        }

        $evaluation = $this->resolveEvaluationForOnlineExam($exam);
        $maxScore = (float) $evaluation->max_score;
        $score = round(((float) $attempt->score_percent / 100) * $maxScore, 2);
        $recordedBy = $attempt->user_id ?? $exam->created_by_user_id;

        $this->upsertGradeRecord(
            evaluation: $evaluation,
            studentId: $attempt->student_id,
            score: $score,
            recordedByUserId: $recordedBy,
            observations: $this->onlineExamObservation($attempt),
        );
    }

    private function resolveEvaluationForAssignment(Assignment $assignment): Evaluation
    {
        if ($assignment->evaluation_id !== null) {
            return Evaluation::query()->findOrFail($assignment->evaluation_id);
        }

        $evaluation = $this->createEvaluation(
            classroom: $assignment->virtualClassroom,
            title: 'LMS — Tarea #'.$assignment->id.': '.$assignment->title,
            maxScore: (float) $assignment->max_score,
            evaluatedAt: ($assignment->due_at ?? now())->toDateString(),
            createdByUserId: $assignment->created_by_user_id,
        );

        $assignment->forceFill(['evaluation_id' => $evaluation->id])->save();

        return $evaluation;
    }

    private function resolveEvaluationForOnlineExam(OnlineExam $exam): Evaluation
    {
        if ($exam->evaluation_id !== null) {
            return Evaluation::query()->findOrFail($exam->evaluation_id);
        }

        $maxScore = (float) config('lms.grade_sync.online_exam_max_score', 20);
        $evaluatedAt = ($exam->available_until ?? $exam->available_from ?? now())->toDateString();

        $evaluation = $this->createEvaluation(
            classroom: $exam->virtualClassroom,
            title: 'LMS — Examen #'.$exam->id.': '.$exam->title,
            maxScore: $maxScore,
            evaluatedAt: $evaluatedAt,
            createdByUserId: $exam->created_by_user_id,
        );

        $exam->forceFill(['evaluation_id' => $evaluation->id])->save();

        return $evaluation;
    }

    private function createEvaluation(
        VirtualClassroom $classroom,
        string $title,
        float $maxScore,
        string $evaluatedAt,
        int $createdByUserId,
    ): Evaluation {
        $classroom->loadMissing('section.grade');
        $section = $classroom->section;
        $grade = $section->grade;

        return Evaluation::query()->create([
            'subject_id' => $classroom->subject_id,
            'academic_year_id' => $classroom->academic_year_id,
            'educational_level_id' => $grade->educational_level_id,
            'grade_id' => $grade->id,
            'section_id' => $classroom->section_id,
            'title' => $title,
            'period' => (string) config('lms.grade_sync.period', 'LMS'),
            'evaluated_at' => $evaluatedAt,
            'max_score' => $maxScore,
            'weight' => (float) config('lms.grade_sync.default_weight', 1),
            'is_active' => true,
            'created_by_user_id' => $createdByUserId,
        ]);
    }

    private function upsertGradeRecord(
        Evaluation $evaluation,
        int $studentId,
        float $score,
        int $recordedByUserId,
        ?string $observations,
    ): void {
        $normalized = min((float) $evaluation->max_score, max(0, $score));

        GradeRecord::query()->updateOrCreate(
            [
                'evaluation_id' => $evaluation->id,
                'student_id' => $studentId,
            ],
            [
                'score' => $normalized,
                'observations' => $observations,
                'recorded_by_user_id' => $recordedByUserId,
            ],
        );
    }

    private function assignmentObservation(AssignmentSubmission $submission): string
    {
        $feedback = trim((string) $submission->teacher_feedback);

        return $feedback !== ''
            ? 'Sincronizado desde LMS (tarea). '.$feedback
            : 'Sincronizado desde LMS (tarea).';
    }

    private function onlineExamObservation(OnlineExamAttempt $attempt): string
    {
        return sprintf(
            'Sincronizado desde LMS (examen online). Puntaje: %s%%.',
            number_format((float) $attempt->score_percent, 2),
        );
    }
}
