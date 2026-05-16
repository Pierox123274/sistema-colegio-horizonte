<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Enums\DiagnosticAttemptStatus;
use App\Enums\DiagnosticExamMode;
use App\Enums\LearningRecommendationSource;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\DiagnosticAttempt;
use App\Models\DiagnosticExam;
use App\Models\LearningRecommendation;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\Student;
use App\Models\StudentAdaptiveProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Motor de exámenes adaptativos y diagnóstico sin dependencia de IA externa.
 *
 * Algoritmo adaptativo: tras cada respuesta se sube la dificultad si acierta (hasta avanzado)
 * o se baja si falla (hasta básico); las siguientes preguntas se eligen al azar dentro del
 * mismo curso (subject) y dificultad actual, con búsqueda en niveles vecinos si no hay stock.
 */
final class AdaptiveDiagnosticService
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly AcademicRiskAnalysisService $risk,
        private readonly StudentRecommendationService $recommendations,
        private readonly DiagnosticExamAccessService $examAccess,
    ) {}

    public function canStartExam(Student $student, DiagnosticExam $exam): bool
    {
        if (! $exam->is_active) {
            return false;
        }

        if (! $this->examAccess->studentCanAccessExam($student, $exam)) {
            return false;
        }

        if (! $exam->prevent_retake_after_completion) {
            return true;
        }

        return ! $exam->attempts()
            ->where('student_id', $student->id)
            ->where('status', DiagnosticAttemptStatus::Completed)
            ->exists();
    }

    public function startAttempt(Student $student, User $user, DiagnosticExam $exam): DiagnosticAttempt
    {
        if (! $this->examAccess->studentCanAccessExam($student, $exam)) {
            throw new \RuntimeException('No tienes acceso a este diagnóstico.');
        }

        if (! $this->canStartExam($student, $exam)) {
            throw new \RuntimeException('Este diagnóstico no admite un nuevo intento.');
        }

        $adaptiveState = null;
        if ($exam->mode === DiagnosticExamMode::Adaptive) {
            $first = $this->pickAdaptiveQuestion($exam, [], QuestionDifficulty::Intermediate);
            if ($first === null) {
                throw new \RuntimeException('No hay preguntas disponibles para el modo adaptativo.');
            }
            $adaptiveState = [
                'step' => 0,
                'current_question_id' => $first->id,
                'current_difficulty' => $first->difficulty->value,
                'asked_ids' => [],
                'correct' => 0,
                'wrong' => 0,
            ];
        }

        return DiagnosticAttempt::query()->create([
            'student_id' => $student->id,
            'user_id' => $user->id,
            'diagnostic_exam_id' => $exam->id,
            'status' => DiagnosticAttemptStatus::InProgress,
            'mode_snapshot' => $exam->mode,
            'adaptive_state' => $adaptiveState,
            'answers' => [],
            'started_at' => now(),
        ]);
    }

    /**
     * @return array<string, mixed>|null Pregunta actual serializada o null si no aplica
     */
    public function peekCurrentQuestionPayload(DiagnosticAttempt $attempt): ?array
    {
        $exam = $attempt->diagnosticExam()->firstOrFail();

        if ($attempt->status !== DiagnosticAttemptStatus::InProgress) {
            return null;
        }

        if ($exam->mode === DiagnosticExamMode::Fixed) {
            $answers = $this->normalizeAnswerKeys($attempt->answers ?? []);
            $question = $this->nextFixedQuestion($exam, $answers);
        } else {
            $state = $attempt->adaptive_state ?? [];
            $qid = $state['current_question_id'] ?? null;
            $question = $qid ? QuestionBank::query()->with('options')->find($qid) : null;
        }

        return $question ? $this->serializeQuestionForUi($question) : null;
    }

    /**
     * @param  array<string, mixed>|bool|string|int|null  $rawAnswer
     */
    public function submitAnswer(DiagnosticAttempt $attempt, mixed $rawAnswer): void
    {
        $exam = $attempt->diagnosticExam()->firstOrFail();
        if ($attempt->status !== DiagnosticAttemptStatus::InProgress) {
            throw new \RuntimeException('Intento ya cerrado.');
        }

        DB::transaction(function () use ($attempt, $rawAnswer, $exam): void {
            $answers = $this->normalizeAnswerKeys($attempt->answers ?? []);

            if ($exam->mode === DiagnosticExamMode::Fixed) {
                $question = $this->nextFixedQuestion($exam, $answers);
                if ($question === null) {
                    throw new \RuntimeException('No hay pregunta pendiente.');
                }
            } else {
                $state = $attempt->adaptive_state ?? [];
                $qid = (int) ($state['current_question_id'] ?? 0);
                $question = QuestionBank::query()->with('options')->findOrFail($qid);
            }

            if (array_key_exists((string) $question->id, $answers)) {
                throw new \RuntimeException('Pregunta ya respondida.');
            }

            $ok = $this->gradeAnswer($question, $rawAnswer);

            $answers[(string) $question->id] = [
                'correct' => $ok,
                'value' => $rawAnswer,
            ];
            $attempt->answers = $answers;

            if ($exam->mode === DiagnosticExamMode::Adaptive) {
                $this->advanceAdaptive($attempt, $exam, $question, $ok);
            } else {
                $this->maybeCompleteFixed($attempt, $exam);
            }

            $attempt->save();
        });
    }

    /**
     * JSON decoded keys may be int or string; normalize so orden fijo y duplicados no rompen.
     *
     * @param  array<int|string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function normalizeAnswerKeys(array $raw): array
    {
        $out = [];
        foreach ($raw as $key => $value) {
            $out[(string) $key] = $value;
        }

        return $out;
    }

    private function maybeCompleteFixed(DiagnosticAttempt $attempt, DiagnosticExam $exam): void
    {
        $next = $this->nextFixedQuestion($exam, $this->normalizeAnswerKeys($attempt->answers ?? []));
        if ($next === null) {
            $this->finalizeAttempt($attempt, $exam);
        }
    }

    private function advanceAdaptive(DiagnosticAttempt $attempt, DiagnosticExam $exam, QuestionBank $answered, bool $wasCorrect): void
    {
        $state = $attempt->adaptive_state ?? [];
        $asked = $state['asked_ids'] ?? [];
        $asked[] = $answered->id;
        $state['asked_ids'] = array_values(array_unique($asked));

        $diff = QuestionDifficulty::from($state['current_difficulty'] ?? QuestionDifficulty::Intermediate->value);
        if ($wasCorrect) {
            $state['correct'] = ($state['correct'] ?? 0) + 1;
            $state['current_difficulty'] = QuestionDifficulty::fromIndex(min(2, $diff->index() + 1))->value;
        } else {
            $state['wrong'] = ($state['wrong'] ?? 0) + 1;
            $state['current_difficulty'] = QuestionDifficulty::fromIndex(max(0, $diff->index() - 1))->value;
        }

        $state['step'] = ($state['step'] ?? 0) + 1;
        $max = max(1, (int) $exam->adaptive_question_count);

        if ($state['step'] >= $max) {
            $state['current_question_id'] = null;
            $attempt->adaptive_state = $state;
            $this->finalizeAttempt($attempt, $exam);

            return;
        }

        $next = $this->pickAdaptiveQuestion(
            $exam,
            $state['asked_ids'],
            QuestionDifficulty::from($state['current_difficulty'])
        );

        if ($next === null) {
            $state['current_question_id'] = null;
            $attempt->adaptive_state = $state;
            $this->finalizeAttempt($attempt, $exam);

            return;
        }

        $state['current_question_id'] = $next->id;
        $state['current_difficulty'] = $next->difficulty->value;
        $attempt->adaptive_state = $state;
    }

    private function finalizeAttempt(DiagnosticAttempt $attempt, DiagnosticExam $exam): void
    {
        $started = $attempt->started_at;
        $user = $attempt->user;

        if ($exam->mode === DiagnosticExamMode::Fixed) {
            [$score, $weakness] = $this->scoreFixedAttempt($attempt, $exam);
        } else {
            [$score, $weakness] = $this->scoreAdaptiveAttempt($attempt);
        }

        $level = $this->classifyLevel($score, $exam);

        $attempt->score_percent = $score;
        $attempt->classified_level = $level;
        $attempt->weakness_by_topic = $weakness;
        $attempt->status = DiagnosticAttemptStatus::Completed;
        $attempt->completed_at = now();
        $attempt->duration_seconds = $started ? $started->diffInSeconds($attempt->completed_at ?? now()) : null;

        $student = $attempt->student;

        $this->syncRecommendationsAndProfile($student, $attempt, $score, $level, $weakness);

        $this->audit->log(
            AuditAction::Assessment,
            AuditModule::AdaptiveLearning,
            $user,
            DiagnosticAttempt::class,
            $attempt->id,
            'Finalización de diagnóstico adaptativo',
            null,
            null,
            AuditResult::Success,
            context: [
                'exam_id' => $exam->id,
                'score_percent' => $score,
                'classified_level' => $level,
                'mode' => $exam->mode->value,
            ],
        );
    }

    /**
     * @return array{0: float, 1: array<string, float>}
     */
    private function scoreFixedAttempt(DiagnosticAttempt $attempt, DiagnosticExam $exam): array
    {
        $answers = $this->normalizeAnswerKeys($attempt->answers ?? []);
        $questions = $exam->questions()->get();
        $earned = 0;
        $total = 0;
        /** @var array<string, float> $weak */
        $weak = [];

        foreach ($questions as $q) {
            $pivot = $q->pivot;
            $pts = (int) ($pivot->points ?? 1);
            $total += $pts;
            $entry = $answers[(string) $q->id] ?? null;
            $ok = is_array($entry) && ($entry['correct'] ?? false);
            if ($ok) {
                $earned += $pts;
            } else {
                $weak[$q->topic] = ($weak[$q->topic] ?? 0) + 1;
            }
        }

        $score = $total > 0 ? round(100 * $earned / $total, 2) : 0.0;

        return [$score, $weak];
    }

    /**
     * @return array{0: float, 1: array<string, float>}
     */
    private function scoreAdaptiveAttempt(DiagnosticAttempt $attempt): array
    {
        $answers = $this->normalizeAnswerKeys($attempt->answers ?? []);
        $weak = [];
        $correct = 0;
        $total = count($answers);

        foreach ($answers as $qid => $entry) {
            $q = QuestionBank::query()->find((int) $qid);
            if ($q === null) {
                continue;
            }
            if (! empty($entry['correct'])) {
                $correct++;
            } else {
                $weak[$q->topic] = ($weak[$q->topic] ?? 0) + 1;
            }
        }

        $score = $total > 0 ? round(100 * $correct / $total, 2) : 0.0;

        return [$score, $weak];
    }

    /**
     * @param  array<string, float>  $weakTopics
     */
    private function syncRecommendationsAndProfile(
        Student $student,
        DiagnosticAttempt $attempt,
        float $score,
        string $level,
        array $weakTopics,
    ): void {
        LearningRecommendation::query()
            ->where('student_id', $student->id)
            ->whereIn('source', [
                LearningRecommendationSource::Diagnostic->value,
                LearningRecommendationSource::Rule->value,
            ])
            ->delete();

        foreach ($weakTopics as $topic => $count) {
            if ($count < 1) {
                continue;
            }
            LearningRecommendation::query()->create([
                'student_id' => $student->id,
                'source' => LearningRecommendationSource::Diagnostic,
                'title' => 'Reforzar tema: '.$topic,
                'body' => 'Se detectaron dificultades en este tema durante el diagnóstico. Repasa conceptos clave y practica ejercicios guiados en clase.',
                'topic' => $topic,
                'priority' => min(5, 2 + (int) $count),
                'estimated_weeks_to_improve' => $this->estimateWeeks($score),
                'meta' => ['attempt_id' => $attempt->id, 'miss_count' => $count],
            ]);
        }

        $risk = $this->risk->studentRisk($student);
        foreach ($this->recommendations->ruleBasedRecommendations($student, $risk) as $i => $tip) {
            LearningRecommendation::query()->create([
                'student_id' => $student->id,
                'source' => LearningRecommendationSource::Rule,
                'title' => 'Hábito y acompañamiento '.($i + 1),
                'body' => $tip,
                'topic' => null,
                'priority' => 2,
                'estimated_weeks_to_improve' => $this->estimateWeeks($score),
                'meta' => ['risk_level' => $risk['level'] ?? null],
            ]);
        }

        $path = [];
        foreach ($weakTopics as $topic => $count) {
            $path[] = [
                'topic' => $topic,
                'status' => 'pending',
                'percent' => 0,
                'priority' => $count,
            ];
        }
        $path[] = [
            'topic' => 'Consolidación general (meta de nivel)',
            'status' => 'in_progress',
            'percent' => (int) min(100, max(0, $score)),
            'priority' => 1,
        ];

        StudentAdaptiveProfile::query()->updateOrCreate(
            ['student_id' => $student->id],
            [
                'last_classified_level' => $level,
                'last_diagnostic_score' => $score,
                'weakness_topics' => array_keys($weakTopics),
                'learning_path' => $path,
                'last_diagnostic_at' => now(),
            ]
        );
    }

    public function classifyLevel(float $scorePercent, ?DiagnosticExam $exam = null): string
    {
        $basic = (int) ($exam?->threshold_basic_percent ?? 40);
        $inter = (int) ($exam?->threshold_intermediate_percent ?? 70);
        $basic = max(0, min(98, $basic));
        $inter = max($basic + 1, min(99, $inter));

        if ($scorePercent < $basic) {
            return QuestionDifficulty::Basic->value;
        }
        if ($scorePercent < $inter) {
            return QuestionDifficulty::Intermediate->value;
        }

        return QuestionDifficulty::Advanced->value;
    }

    private function estimateWeeks(float $score): int
    {
        $w = (int) round(8 - ($score / 100) * 6);

        return max(2, min(8, $w));
    }

    /**
     * @param  array<int>  $askedIds
     */
    private function pickAdaptiveQuestion(DiagnosticExam $exam, array $askedIds, QuestionDifficulty $preferred): ?QuestionBank
    {
        $queryBase = function () use ($exam, $askedIds) {
            return QuestionBank::query()
                ->where('is_active', true)
                ->when($askedIds !== [], fn ($q) => $q->whereNotIn('id', $askedIds))
                ->when($exam->subject_id, fn ($q) => $q->where('subject_id', $exam->subject_id));
        };

        $try = $preferred->index();
        foreach ([0, 1, -1, 2, -2] as $delta) {
            $idx = max(0, min(2, $try + $delta));
            $d = QuestionDifficulty::fromIndex($idx);
            $q = $queryBase()->where('difficulty', $d)->inRandomOrder()->first();
            if ($q !== null) {
                return $q;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $normalizedAnswers
     */
    private function nextFixedQuestion(DiagnosticExam $exam, array $normalizedAnswers): ?QuestionBank
    {
        $answered = array_map('strval', array_keys($normalizedAnswers));
        $questions = $exam->questions()->get();
        foreach ($questions as $q) {
            if (! in_array((string) $q->id, $answered, true)) {
                return $q;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|bool|string|int|null  $raw
     */
    public function gradeAnswer(QuestionBank $question, mixed $raw): bool
    {
        return match ($question->question_type) {
            QuestionType::TrueFalse => $this->gradeTrueFalse($question, $raw),
            QuestionType::ShortAnswer => $this->gradeShort($question, $raw),
            QuestionType::MultipleChoice => $this->gradeMultipleChoice($question, $raw),
        };
    }

    private function gradeTrueFalse(QuestionBank $question, mixed $raw): bool
    {
        $val = $this->parseBoolAnswer($raw);
        if ($val === null) {
            return false;
        }

        return $question->true_false_answer === $val;
    }

    private function parseBoolAnswer(mixed $raw): ?bool
    {
        if (is_bool($raw)) {
            return $raw;
        }
        if (is_int($raw) || is_float($raw)) {
            return ((int) $raw) === 1;
        }
        if (is_string($raw)) {
            $s = mb_strtolower(trim($raw));
            if (in_array($s, ['1', 'true', 'verdadero', 'v', 'sí', 'si', 'yes'], true)) {
                return true;
            }
            if (in_array($s, ['0', 'false', 'falso', 'f', 'no'], true)) {
                return false;
            }
        }

        return null;
    }

    private function gradeShort(QuestionBank $question, mixed $raw): bool
    {
        $expected = $question->short_answer_expected;
        if (! is_string($expected) || $expected === '') {
            return false;
        }
        if (! is_string($raw) && ! is_numeric($raw)) {
            return false;
        }

        return mb_strtolower(trim((string) $raw)) === mb_strtolower(trim($expected));
    }

    private function gradeMultipleChoice(QuestionBank $question, mixed $raw): bool
    {
        $id = is_numeric($raw) ? (int) $raw : 0;
        if ($id < 1) {
            return false;
        }

        $opt = QuestionOption::query()->where('question_bank_id', $question->id)->whereKey($id)->first();

        return $opt !== null && $opt->is_correct;
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeQuestionForUi(QuestionBank $q): array
    {
        $q->loadMissing('options');

        return [
            'id' => $q->id,
            'stem' => $q->stem,
            'topic' => $q->topic,
            'question_type' => $q->question_type->value,
            'difficulty' => $q->difficulty->value,
            'competencies' => $q->competencies ?? [],
            'options' => $q->options->map(fn (QuestionOption $o) => [
                'id' => $o->id,
                'label' => $o->label,
                'body' => $o->body,
            ])->values()->all(),
        ];
    }
}
