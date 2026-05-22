<?php

namespace App\Services;

use App\Enums\AssignmentSubmissionStatus;
use App\Enums\LearningRecommendationSource;
use App\Models\AssignmentSubmission;
use App\Models\LearningRecommendation;
use App\Models\OnlineExamAttempt;
use App\Models\Student;
use App\Models\StudentAdaptiveProfile;

/**
 * Integración LMS ↔ aprendizaje adaptativo (Fase 22).
 */
final class LMSAdaptiveIntegrationService
{
    public function onAssignmentGraded(AssignmentSubmission $submission): void
    {
        $submission->loadMissing('assignment');
        $max = (float) $submission->assignment->max_score;
        if ($max <= 0 || $submission->score === null) {
            return;
        }

        $percent = round(((float) $submission->score / $max) * 100, 2);
        $student = Student::query()->find($submission->student_id);
        if ($student === null) {
            return;
        }

        if ($percent < 60) {
            $this->upsertWeaknessFlag($student, 'Tareas con bajo rendimiento');
            LearningRecommendation::query()->create([
                'student_id' => $student->id,
                'source' => LearningRecommendationSource::Rule,
                'title' => 'Refuerzo tras tarea calificada',
                'body' => 'Su entrega en «'.$submission->assignment->title.'» obtuvo '.$percent.'%. Revise el material del aula y la ruta de aprendizaje.',
                'topic' => 'Tareas',
                'priority' => 2,
                'estimated_weeks_to_improve' => 2,
            ]);
        }
    }

    public function onOnlineExamCompleted(OnlineExamAttempt $attempt): void
    {
        if ($attempt->score_percent === null) {
            return;
        }

        $student = Student::query()->find($attempt->student_id);
        if ($student === null) {
            return;
        }

        $score = (float) $attempt->score_percent;
        $profile = StudentAdaptiveProfile::query()->firstOrNew(['student_id' => $student->id]);
        $profile->last_diagnostic_score = $score;
        if ($score < 40) {
            $profile->last_classified_level = 'basic';
            $this->upsertWeaknessFlag($student, 'Evaluación online');
        } elseif ($score < 70) {
            $profile->last_classified_level = 'intermediate';
        } else {
            $profile->last_classified_level = 'advanced';
        }
        $profile->last_diagnostic_at = now();
        $profile->save();
    }

    public function countOverdueAssignmentsForStudent(Student $student): int
    {
        return AssignmentSubmission::query()
            ->where('student_id', $student->id)
            ->where('status', AssignmentSubmissionStatus::Overdue)
            ->count();
    }

    private function upsertWeaknessFlag(Student $student, string $topic): void
    {
        $profile = StudentAdaptiveProfile::query()->firstOrNew(['student_id' => $student->id]);
        $weak = $profile->weakness_topics ?? [];
        $weak[$topic] = ($weak[$topic] ?? 0) + 1;
        $profile->weakness_topics = $weak;
        $profile->save();
    }
}
