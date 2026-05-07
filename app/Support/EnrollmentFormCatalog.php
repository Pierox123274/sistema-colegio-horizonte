<?php

namespace App\Support;

use App\Enums\EnrollmentStatus;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\EducationalLevel;
use App\Models\Grade;
use App\Models\Section;

final class EnrollmentFormCatalog
{
    /**
     * Catálogo para formularios Inertia (matrícula): selects encadenados.
     *
     * @return array<string, mixed>
     */
    public static function build(): array
    {
        $levels = EducationalLevel::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        $grades = Grade::query()
            ->where('is_active', true)
            ->orderBy('educational_level_id')
            ->orderBy('order')
            ->get(['id', 'educational_level_id', 'code', 'name']);

        $gradesByLevel = [];
        foreach ($grades as $g) {
            $gradesByLevel[(string) $g->educational_level_id][] = [
                'value' => (string) $g->id,
                'label' => $g->code.' — '.$g->name,
            ];
        }

        $sections = Section::query()
            ->where('is_active', true)
            ->orderBy('grade_id')
            ->orderBy('code')
            ->get(['id', 'grade_id', 'code', 'name']);

        $sectionsByGrade = [];
        foreach ($sections as $s) {
            $sectionsByGrade[(string) $s->grade_id][] = [
                'value' => (string) $s->id,
                'label' => $s->code.' — '.$s->name,
            ];
        }

        $classrooms = Classroom::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'section_id', 'code', 'name']);

        $classroomsBySection = [];
        $classroomsWithoutSection = [];
        foreach ($classrooms as $c) {
            $row = [
                'value' => (string) $c->id,
                'label' => $c->code.' — '.$c->name,
            ];
            if ($c->section_id === null) {
                $classroomsWithoutSection[] = $row;
            } else {
                $classroomsBySection[(string) $c->section_id][] = $row;
            }
        }

        return [
            'academic_years' => AcademicYear::query()
                ->orderByDesc('year')
                ->get(['id', 'name', 'year'])
                ->map(fn (AcademicYear $y): array => [
                    'value' => (string) $y->id,
                    'label' => $y->name.' ('.$y->year.')',
                ])
                ->values()
                ->all(),
            'levels' => $levels->map(fn (EducationalLevel $l): array => [
                'value' => (string) $l->id,
                'label' => $l->code.' — '.$l->name,
            ])->values()->all(),
            'grades_by_level' => $gradesByLevel,
            'sections_by_grade' => $sectionsByGrade,
            'classrooms_by_section' => $classroomsBySection,
            'classrooms_without_section' => $classroomsWithoutSection,
            'statuses' => EnrollmentStatus::options(),
        ];
    }
}
