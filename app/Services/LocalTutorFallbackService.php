<?php

namespace App\Services;

use App\Models\Student;

/**
 * Respuestas pedagógicas locales cuando el proveedor externo de IA no está disponible.
 */
final class LocalTutorFallbackService
{
    public function __construct(
        private readonly AcademicRiskAnalysisService $risk,
        private readonly StudentRecommendationService $recommendations,
        private readonly StudentContextService $studentContext,
    ) {}

    /**
     * @return array{reply: string, success: bool, model: string, error_code: null, cached: bool, fallback: bool}
     */
    public function studentChat(Student $student, string $message): array
    {
        $risk = $this->risk->studentRisk($student);
        $tips = $this->recommendations->ruleBasedRecommendations($student, $risk);
        $enrollment = $this->studentContext->currentEnrollmentPayload($student);
        $gradeLabel = is_array($enrollment['grade'] ?? null)
            ? ($enrollment['grade']['name'] ?? null)
            : null;
        $sectionLabel = is_array($enrollment['section'] ?? null)
            ? ($enrollment['section']['name'] ?? null)
            : null;

        $normalized = mb_strtolower(trim($message));
        $reply = $this->replyForMessage($normalized, $risk, $tips, $gradeLabel, $sectionLabel);

        return [
            'reply' => $reply,
            'success' => true,
            'model' => 'local-fallback',
            'error_code' => null,
            'cached' => false,
            'fallback' => true,
        ];
    }

    /**
     * @param  array<string, mixed>  $risk
     * @param  list<string>  $tips
     */
    private function replyForMessage(
        string $normalized,
        array $risk,
        array $tips,
        ?string $gradeLabel,
        ?string $sectionLabel,
    ): string {
        if ($this->isGreeting($normalized)) {
            return $this->greetingReply($risk, $gradeLabel);
        }

        if ($this->matchesAny($normalized, ['nota', 'promedio', 'calificacion', 'calificación', 'rendimiento'])) {
            return $this->performanceReply($risk, $tips);
        }

        if ($this->matchesAny($normalized, ['asistencia', 'falta', 'inasistencia', 'puntual'])) {
            return $this->attendanceReply($risk, $tips);
        }

        if ($this->matchesAny($normalized, ['tarea', 'deberes', 'trabajo', 'entregar', 'plazo'])) {
            return "Para organizar tus tareas:\n\n"
                ."1. Anota cada entrega con fecha en tu agenda.\n"
                ."2. Divide el trabajo en bloques de 25–30 minutos.\n"
                ."3. Revisa los criterios de evaluación antes de empezar.\n"
                ."4. Si te atoras, prepara preguntas concretas para tu docente.\n\n"
                .$this->tipBlock($tips);
        }

        if ($this->matchesAny($normalized, ['examen', 'prueba', 'evaluacion', 'evaluación', 'estudiar', 'repasar'])) {
            return "Plan de repaso sugerido:\n\n"
                ."1. Identifica los tres temas más importantes del curso.\n"
                ."2. Elabora un resumen con tus propias palabras.\n"
                ."3. Resuelve ejercicios similares a los vistos en clase.\n"
                ."4. Duerme bien la noche anterior al examen.\n\n"
                .$this->tipBlock($tips);
        }

        if ($this->matchesAny($normalized, ['matematica', 'matemática', 'numeros', 'números', 'algebra', 'álgebra'])) {
            return "Consejos para matemáticas:\n\n"
                ."• Practica con ejercicios graduales: primero los más sencillos.\n"
                ."• Escribe cada paso; no saltes operaciones intermedias.\n"
                ."• Si un problema es largo, dibuja o esquematiza la información.\n"
                ."• Corrige tus errores revisando dónde cambió el razonamiento.\n\n"
                .$this->tipBlock($tips);
        }

        if ($this->matchesAny($normalized, ['comunicado', 'comunicación', 'mensaje', 'aviso'])) {
            return "Revisa la sección «Comunicados» del portal para avisos oficiales del colegio. "
                .'Para dudas administrativas, coordina con secretaría o tu tutor de aula.';
        }

        $contextLine = '';
        if ($gradeLabel !== null) {
            $contextLine = "Veo que estás en {$gradeLabel}";
            if ($sectionLabel !== null) {
                $contextLine .= " (sección {$sectionLabel})";
            }
            $contextLine .= ". ";
        }

        return $contextLine
            ."En este momento trabajo en modo asistente local (el servicio en la nube no está disponible), "
            ."pero puedo orientarte con estrategias de estudio basadas en tu rendimiento registrado.\n\n"
            .$this->tipBlock($tips)
            ."\n\nSi tu consulta es muy específica de un curso, anótala y consúltala con tu docente en el horario de clase.";
    }

    /**
     * @param  array<string, mixed>  $risk
     */
    private function greetingReply(array $risk, ?string $gradeLabel): string
    {
        $level = (string) ($risk['level'] ?? 'bajo');
        $levelText = match ($level) {
            'alto' => 'Tu seguimiento académico indica que conviene priorizar el repaso y la asistencia regular.',
            'medio' => 'Hay buen margen para mejorar si mantienes una rutina de estudio constante.',
            default => 'Vas por buen camino; sigue consolidando tus hábitos de estudio.',
        };

        $gradePart = $gradeLabel !== null ? " Estás matriculado en {$gradeLabel}." : '';

        return "¡Hola! Soy tu tutor académico institucional.{$gradePart}\n\n"
            ."Ahora mismo respondo en modo local porque el servicio de IA en la nube no está disponible "
            ."(por ejemplo, cuota agotada o sin conexión). Aun así puedo ayudarte con organización, repaso y hábitos de estudio.\n\n"
            .$levelText
            ."\n\n¿Sobre qué materia o tema te gustaría trabajar hoy?";
    }

    /**
     * @param  array<string, mixed>  $risk
     * @param  list<string>  $tips
     */
    private function performanceReply(array $risk, array $tips): string
    {
        $avg = $risk['average'] ?? null;
        $samples = (int) ($risk['grade_samples'] ?? 0);

        $intro = $samples > 0 && $avg !== null
            ? "Según los registros del portal, tu promedio aproximado es {$avg} ({$samples} calificaciones en muestra).\n\n"
            : "Aún no hay suficientes calificaciones registradas para calcular un promedio en el portal.\n\n";

        return $intro.$this->tipBlock($tips);
    }

    /**
     * @param  array<string, mixed>  $risk
     * @param  list<string>  $tips
     */
    private function attendanceReply(array $risk, array $tips): string
    {
        $pct = $risk['attendance_pct'] ?? null;
        $records = (int) ($risk['attendance_records'] ?? 0);

        $intro = $records > 0 && $pct !== null
            ? "Tu asistencia estimada es del {$pct}% según los registros del aula.\n\n"
            : "Aún no hay suficientes registros de asistencia para un cálculo preciso.\n\n";

        return $intro.$this->tipBlock($tips);
    }

    /**
     * @param  list<string>  $tips
     */
    private function tipBlock(array $tips): string
    {
        $selected = array_slice($tips, 0, 3);

        return "Recomendaciones para ti:\n"
            .implode("\n", array_map(static fn (string $t): string => "• {$t}", $selected));
    }

    private function isGreeting(string $normalized): bool
    {
        return $normalized === ''
            || $this->matchesAny($normalized, ['hola', 'buenos dias', 'buenos días', 'buenas tardes', 'buenas noches', 'hey', 'saludos']);
    }

    /**
     * @param  list<string>  $needles
     */
    private function matchesAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
