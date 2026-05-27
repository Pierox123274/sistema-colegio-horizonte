<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;

/**
 * Contexto académico minimizado para prompts IA (sin PII sensible ni datos clínicos).
 */
final class AcademicMemoryService
{
    public function __construct(
        private readonly StudentContextService $studentContext,
        private readonly AcademicRiskAnalysisService $risk,
        private readonly TeacherContextService $teacherContext,
        private readonly StudentRecommendationService $recommendations,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function forStudent(Student $student): array
    {
        $risk = $this->risk->studentRisk($student);
        $stats = $this->studentContext->dashboardStats($student);
        $enrollment = $this->studentContext->currentEnrollmentPayload($student);

        return [
            'student' => $student->only(['id', 'code']),
            'enrollment' => $enrollment,
            'metrics' => [
                'grade_records' => $stats['grade_records_count'] ?? 0,
                'attendance_records' => $stats['attendance_records_count'] ?? 0,
            ],
            'risk_heuristic' => [
                'level' => $risk['level'],
                'score' => $risk['score'],
                'flags' => $risk['flags'],
            ],
            'recommendations' => array_slice($this->recommendations->ruleBasedRecommendations($student, $risk), 0, 5),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forTeacher(User $teacher): array
    {
        $assignments = $this->teacherContext->activeAssignmentsFor($teacher);
        $sections = $assignments->map(fn ($a) => [
            'section_id' => $a->section_id,
            'section' => $a->section?->name,
            'grade' => $a->section?->grade?->name,
            'subject' => $a->subject?->name,
            'subject_id' => $a->subject_id,
        ])->unique('section_id')->values()->take(12)->all();

        $riskRows = $this->risk->studentsAtRiskForTeacher($teacher);
        $highRisk = collect($riskRows)->filter(fn (array $r): bool => ($r['risk']['level'] ?? '') === 'alto')->count();

        return [
            'teacher_id' => $teacher->id,
            'sections' => $sections,
            'risk_summary' => [
                'visible_students' => count($riskRows),
                'high_risk_count' => $highRisk,
            ],
        ];
    }
}
