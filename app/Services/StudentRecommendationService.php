<?php

namespace App\Services;

use App\Models\Student;

/**
 * Recomendaciones pedagógicas basadas en reglas institucionales (sin sustituir a la IA).
 */
final class StudentRecommendationService
{
    /**
     * @param  array<string, mixed>  $risk  Salida de AcademicRiskAnalysisService::studentRisk
     * @return list<string>
     */
    public function ruleBasedRecommendations(Student $student, array $risk): array
    {
        $tips = [
            'Organiza una rutina de estudio fija y revisa el material de cada curso al menos tres veces por semana.',
            'Ante dudas, participa en clase y consulta a tu docente en el horario de aula acordado.',
        ];

        $level = $risk['level'] ?? 'bajo';

        if ($level === 'alto') {
            $tips[] = 'Tu promedio o asistencia requieren atención prioritaria: prioriza materias con menor rendimiento y busca apoyo académico con tu docente o tutoría del aula.';
            $tips[] = 'Evita dejar tareas acumuladas: divide objetivos diarios pequeños y verificables.';
        } elseif ($level === 'medio') {
            $tips[] = 'Hay margen de mejora: revisa los cursos donde tus calificaciones están por debajo del promedio del aula y prepara un plan de repaso.';
        } else {
            $tips[] = 'Mantén el hábito de revisión y participa activamente para consolidar tu buen desempeño.';
        }

        foreach ($risk['flags'] ?? [] as $flag) {
            if ($flag === 'asistencia_baja') {
                $tips[] = 'La puntualidad y la asistencia regular se correlacionan con mejores resultados: ajusta horarios de salida y avisa con anticipación si hay inconvenientes.';
            }
            if ($flag === 'ausentismo_relevante') {
                $tips[] = 'El número de inasistencias es elevado: recupera temas con apuntes compartidos por el aula y coordinación con secretaría si hubo situaciones justificadas.';
            }
        }

        return array_values(array_unique($tips));
    }
}
