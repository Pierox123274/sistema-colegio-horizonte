<?php

namespace App\Services;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\Assignment;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\User;
use App\Models\VirtualClassroom;
use Illuminate\Support\Facades\DB;

final class TeacherAICopilotService
{
    public function __construct(
        private readonly AIGenerationService $generation,
        private readonly AcademicMemoryService $memory,
        private readonly AssignmentService $assignments,
        private readonly AcademicRiskAnalysisService $risk,
    ) {}

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function generateExam(User $teacher, array $input): array
    {
        $instruction = $this->buildExamInstruction($input);

        return $this->generation->generateStructured(
            $teacher,
            'exam_generator',
            'exam_generator',
            'exam_generate',
            $instruction,
            array_merge($this->memory->forTeacher($teacher), ['params' => $input]),
            fn (): array => $this->localExamFallback($input),
        );
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function generateAssignment(User $teacher, array $input): array
    {
        $instruction = 'Genera una tarea escolar con: '
            .json_encode($input, JSON_UNESCAPED_UNICODE);

        return $this->generation->generateStructured(
            $teacher,
            'assignment_generator',
            'assignment_generator',
            'assignment_generate',
            $instruction,
            array_merge($this->memory->forTeacher($teacher), ['params' => $input]),
            fn (): array => $this->localAssignmentFallback($input),
        );
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function generateRubric(User $teacher, array $input): array
    {
        $instruction = 'Genera una rúbrica analítica para: '
            .json_encode($input, JSON_UNESCAPED_UNICODE);

        return $this->generation->generateStructured(
            $teacher,
            'rubric_generator',
            'rubric_generator',
            'rubric_generate',
            $instruction,
            array_merge($this->memory->forTeacher($teacher), ['params' => $input]),
            fn (): array => $this->localRubricFallback($input),
        );
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function generateSessionPlan(User $teacher, array $input): array
    {
        $instruction = 'Planifica una sesión de aprendizaje: '
            .json_encode($input, JSON_UNESCAPED_UNICODE);

        return $this->generation->generateStructured(
            $teacher,
            'planner_assistant',
            'planner_assistant',
            'session_plan_generate',
            $instruction,
            array_merge($this->memory->forTeacher($teacher), ['params' => $input]),
            fn (): array => $this->localSessionFallback($input),
        );
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function generateFeedback(User $teacher, array $input): array
    {
        $instruction = 'Genera feedback académico constructivo (texto breve en JSON {"feedback":""}): '
            .json_encode($input, JSON_UNESCAPED_UNICODE);

        return $this->generation->generateStructured(
            $teacher,
            'teacher_copilot',
            'teacher_copilot',
            'feedback_generate',
            $instruction,
            $this->memory->forTeacher($teacher),
            fn (): array => ['feedback' => 'Buen avance. Refuerza la organización de ideas y revisa los criterios de la rúbrica antes de la entrega final.'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function predictiveInsights(User $teacher): array
    {
        $rows = $this->risk->studentsAtRiskForTeacher($teacher);
        $flags = [
            'desmotivacion' => 0,
            'baja_participacion' => 0,
            'abandono_lms' => 0,
        ];
        foreach ($rows as $row) {
            foreach ($row['risk']['flags'] ?? [] as $flag) {
                if ($flag === 'promedio_bajo') {
                    $flags['desmotivacion']++;
                }
                if ($flag === 'asistencia_baja') {
                    $flags['baja_participacion']++;
                }
            }
        }

        $interventions = [];
        if ($flags['desmotivacion'] > 0) {
            $interventions[] = 'Programar refuerzo formativo en áreas con promedio bajo.';
        }
        if ($flags['baja_participacion'] > 0) {
            $interventions[] = 'Contactar a familias con patrón de asistencia irregular.';
        }

        return [
            'students_visible' => count($rows),
            'high_risk' => collect($rows)->filter(fn (array $r): bool => ($r['risk']['level'] ?? '') === 'alto')->count(),
            'flags' => $flags,
            'suggested_interventions' => $interventions,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $questions
     * @return array{created: int, ids: list<int>}
     */
    public function exportQuestionsToBank(User $teacher, int $subjectId, array $questions): array
    {
        $created = [];
        DB::transaction(function () use ($questions, $subjectId, &$created): void {
            foreach ($questions as $q) {
                $bank = $this->createQuestionBankEntry($subjectId, $q);
                $this->attachMultipleChoiceOptions($bank, $q);
                $created[] = $bank->id;
            }
        });

        return ['created' => count($created), 'ids' => $created];
    }

    /**
     * @param  array<string, mixed>  $q
     */
    private function createQuestionBankEntry(int $subjectId, array $q): QuestionBank
    {
        $type = $this->mapQuestionType((string) ($q['type'] ?? 'multiple_choice'));
        $difficulty = $this->mapDifficulty((string) ($q['difficulty'] ?? 'medium'));

        return QuestionBank::query()->create([
            'subject_id' => $subjectId,
            'topic' => (string) ($q['topic'] ?? 'IA — generado'),
            'question_type' => $type,
            'difficulty' => $difficulty,
            'competencies' => $q['competencies'] ?? [],
            'stem' => (string) ($q['stem'] ?? $q['question'] ?? 'Pregunta'),
            'explanation' => (string) ($q['explanation'] ?? ''),
            'true_false_answer' => $type === QuestionType::TrueFalse ? (bool) ($q['correct_answer'] ?? true) : null,
            'short_answer_expected' => $type === QuestionType::ShortAnswer ? (string) ($q['correct_answer'] ?? '') : null,
            'is_active' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $q
     */
    private function attachMultipleChoiceOptions(QuestionBank $bank, array $q): void
    {
        if ($bank->question_type !== QuestionType::MultipleChoice || ! isset($q['options']) || ! is_array($q['options'])) {
            return;
        }

        foreach (array_values($q['options']) as $i => $opt) {
            $label = is_array($opt) ? (string) ($opt['label'] ?? chr(65 + $i)) : chr(65 + $i);
            $body = is_array($opt) ? (string) ($opt['body'] ?? $opt['text'] ?? '') : (string) $opt;
            $isCorrect = is_array($opt)
                ? (bool) ($opt['is_correct'] ?? false)
                : ($i === (int) ($q['correct_index'] ?? 0));

            QuestionOption::query()->create([
                'question_bank_id' => $bank->id,
                'sort_order' => $i + 1,
                'label' => $label,
                'body' => $body,
                'is_correct' => $isCorrect,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function exportToAssignment(User $teacher, VirtualClassroom $classroom, array $payload): Assignment
    {
        $description = (string) ($payload['instructions'] ?? $payload['description'] ?? '');
        if (! empty($payload['objectives']) && is_array($payload['objectives'])) {
            $description .= "\n\nObjetivos:\n- ".implode("\n- ", $payload['objectives']);
        }

        return $this->assignments->createAssignment($teacher, $classroom, [
            'title' => (string) ($payload['title'] ?? 'Tarea generada con IA'),
            'description' => $description,
            'max_score' => 20,
            'due_at' => null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $input
     */
    private function buildExamInstruction(array $input): string
    {
        return sprintf(
            'Genera %d preguntas tipo %s, dificultad %s, grado %s, competencias: %s. Tema: %s.',
            (int) ($input['question_count'] ?? 5),
            (string) ($input['question_types'] ?? 'multiple_choice'),
            (string) ($input['difficulty'] ?? 'medium'),
            (string) ($input['grade'] ?? 'general'),
            implode(', ', (array) ($input['competencies'] ?? ['comprensión'])),
            (string) ($input['topic'] ?? 'evaluación formativa'),
        );
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function localExamFallback(array $input): array
    {
        $count = min(10, max(1, (int) ($input['question_count'] ?? 3)));
        $questions = [];
        for ($i = 1; $i <= $count; $i++) {
            $questions[] = [
                'stem' => "Pregunta {$i} sobre ".($input['topic'] ?? 'el tema indicado'),
                'type' => 'multiple_choice',
                'difficulty' => $input['difficulty'] ?? 'medium',
                'competencies' => $input['competencies'] ?? ['comprensión lectora'],
                'options' => [
                    ['label' => 'A', 'body' => 'Opción correcta', 'is_correct' => true],
                    ['label' => 'B', 'body' => 'Opción distractora', 'is_correct' => false],
                    ['label' => 'C', 'body' => 'Opción distractora 2', 'is_correct' => false],
                ],
                'explanation' => 'Revise el contenido trabajado en clase.',
            ];
        }

        return ['questions' => $questions];
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function localAssignmentFallback(array $input): array
    {
        $topic = (string) ($input['topic'] ?? 'tema de estudio');

        return [
            'title' => 'Tarea: '.$topic,
            'instructions' => "Elabore un trabajo sobre {$topic} aplicando lo visto en clase.",
            'objectives' => ['Comprender el tema', 'Aplicar estrategias de estudio'],
            'criteria' => ['Claridad', 'Precisión', 'Presentación'],
            'resources' => ['Cuaderno', 'Material de clase'],
            'estimated_minutes' => 45,
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function localRubricFallback(array $input): array
    {
        $title = (string) ($input['title'] ?? 'Rúbrica de evaluación');

        return [
            'title' => $title,
            'criteria' => [
                [
                    'name' => 'Dominio del contenido',
                    'weight' => 40,
                    'levels' => [
                        ['label' => 'Inicio', 'descriptor' => 'Reconoce ideas básicas con apoyo.'],
                        ['label' => 'Proceso', 'descriptor' => 'Explica con precisión parcial.'],
                        ['label' => 'Logro', 'descriptor' => 'Demuestra dominio consistente.'],
                    ],
                ],
                [
                    'name' => 'Organización',
                    'weight' => 30,
                    'levels' => [
                        ['label' => 'Inicio', 'descriptor' => 'Estructura incompleta.'],
                        ['label' => 'Proceso', 'descriptor' => 'Estructura adecuada con detalles.'],
                        ['label' => 'Logro', 'descriptor' => 'Organización clara y coherente.'],
                    ],
                ],
                [
                    'name' => 'Presentación',
                    'weight' => 30,
                    'levels' => [
                        ['label' => 'Inicio', 'descriptor' => 'Presentación básica.'],
                        ['label' => 'Proceso', 'descriptor' => 'Presentación cuidada.'],
                        ['label' => 'Logro', 'descriptor' => 'Presentación impecable.'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function localSessionFallback(array $input): array
    {
        return [
            'session_title' => (string) ($input['topic'] ?? 'Sesión de aprendizaje'),
            'duration_minutes' => (int) ($input['duration_minutes'] ?? 90),
            'objectives' => ['Activar saberes previos', 'Desarrollar competencia indicada'],
            'activities' => ['Lluvia de ideas', 'Trabajo guiado', 'Puesta en común'],
            'dynamics' => ['Trabajo en parejas', 'Rotación de roles'],
            'evidence' => ['Producto grupal', 'Rúbrica de observación'],
            'strategies' => ['Andamiaje', 'Retroalimentación formativa'],
        ];
    }

    private function mapQuestionType(string $raw): QuestionType
    {
        return match ($raw) {
            'true_false', 'true-false' => QuestionType::TrueFalse,
            'short_answer', 'short-answer' => QuestionType::ShortAnswer,
            default => QuestionType::MultipleChoice,
        };
    }

    private function mapDifficulty(string $raw): QuestionDifficulty
    {
        return match (strtolower($raw)) {
            'easy', 'facil', 'fácil', 'basic' => QuestionDifficulty::Basic,
            'hard', 'dificil', 'difícil', 'advanced' => QuestionDifficulty::Advanced,
            default => QuestionDifficulty::Intermediate,
        };
    }
}
