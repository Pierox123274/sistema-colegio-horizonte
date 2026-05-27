<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;

final class StudentLearningCoachService
{
    public function __construct(
        private readonly AIGenerationService $generation,
        private readonly AcademicMemoryService $memory,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function adaptiveExplanation(User $user, Student $student, string $topic, string $detail = ''): array
    {
        $instruction = "Explica de forma adaptada el tema: {$topic}. ".($detail !== '' ? "Detalle: {$detail}" : '');

        $result = $this->generation->generateStructured(
            $user,
            'student_learning_coach',
            'student_learning_coach',
            'student_explanation',
            $instruction,
            $this->memory->forStudent($student),
            fn (): array => [
                'explanation' => "Repasa {$topic} con tus apuntes. Divide el tema en tres ideas clave y elabora un ejemplo propio.",
            ],
        );

        if ($result['data'] === null && $result['raw'] !== null) {
            $result['data'] = ['explanation' => $result['raw']];
            $result['success'] = true;
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function miniQuiz(User $user, Student $student, string $topic, int $count = 3): array
    {
        $instruction = "Genera {$count} preguntas de opción múltiple sobre: {$topic}.";

        return $this->generation->generateStructured(
            $user,
            'student_learning_coach',
            'student_learning_coach',
            'student_mini_quiz',
            $instruction,
            $this->memory->forStudent($student),
            fn (): array => [
                'items' => [
                    [
                        'question' => "¿Cuál es una idea central de {$topic}?",
                        'options' => ['Idea A', 'Idea B', 'Idea C'],
                        'correct_index' => 0,
                        'explanation' => 'Revisa tu material de clase.',
                    ],
                ],
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(User $user, Student $student, string $topic): array
    {
        $instruction = "Resume en puntos clave (JSON {\"summary_points\":[]}): {$topic}";

        return $this->generation->generateStructured(
            $user,
            'student_learning_coach',
            'student_learning_coach',
            'student_summary',
            $instruction,
            $this->memory->forStudent($student),
            fn (): array => [
                'summary_points' => [
                    'Identifica las ideas principales del tema.',
                    'Relaciona con ejemplos de clase.',
                    'Elabora preguntas de repaso.',
                ],
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function practicePrompt(User $user, Student $student, string $topic): array
    {
        $instruction = "Propón 3 ejercicios de práctica breve (JSON {\"exercises\":[]}): {$topic}";

        return $this->generation->generateStructured(
            $user,
            'student_learning_coach',
            'student_learning_coach',
            'student_practice',
            $instruction,
            $this->memory->forStudent($student),
            fn (): array => [
                'exercises' => [
                    'Define con tus palabras el concepto principal.',
                    'Resuelve un ejemplo guiado del cuaderno.',
                    'Explica el tema a un compañero en 2 minutos.',
                ],
            ],
        );
    }
}
