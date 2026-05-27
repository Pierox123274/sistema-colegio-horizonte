<?php

namespace App\Support;

use App\Enums\IntranetRole;
use App\Models\User;

final class TeacherNavigation
{
    /**
     * Menú lateral del portal docente (Docente y Administrador para supervisión).
     *
     * @return list<array{label: string, href: string|null, icon: string, disabled: bool, activeRoutes?: list<string>, children?: list<array<string, mixed>>}>
     */
    public static function items(?User $user): array
    {
        if ($user === null || ! $user->hasAnyRole([
            IntranetRole::Docente->value,
            IntranetRole::Administrador->value,
        ])) {
            return [];
        }

        return [
            [
                'label' => 'Inicio',
                'href' => route('teacher.dashboard', absolute: false),
                'icon' => 'layout-dashboard',
                'disabled' => false,
                'activeRoutes' => ['teacher.dashboard'],
            ],
            [
                'label' => 'Académico',
                'href' => null,
                'icon' => 'school',
                'disabled' => false,
                'children' => [
                    [
                        'label' => 'Mis asignaciones',
                        'href' => route('teacher.assignments.index', absolute: false),
                        'icon' => 'book-marked',
                        'disabled' => false,
                        'activeRoutes' => ['teacher.assignments.index'],
                    ],
                    [
                        'label' => 'Asistencia',
                        'href' => route('teacher.attendance.index', absolute: false),
                        'icon' => 'calendar-check',
                        'disabled' => false,
                        'activeRoutes' => [
                            'teacher.attendance.index',
                            'teacher.attendance.create',
                            'teacher.attendance.section-date',
                        ],
                    ],
                    [
                        'label' => 'Notas',
                        'href' => route('teacher.grades.index', absolute: false),
                        'icon' => 'clipboard-check',
                        'disabled' => false,
                        'activeRoutes' => ['teacher.grades.index', 'teacher.grades.records'],
                    ],
                    [
                        'label' => 'Estudiantes',
                        'href' => route('teacher.students.index', absolute: false),
                        'icon' => 'users',
                        'disabled' => false,
                        'activeRoutes' => ['teacher.students.index', 'teacher.students.show'],
                    ],
                    [
                        'label' => 'Aula virtual',
                        'href' => route('teacher.classrooms.index', absolute: false),
                        'icon' => 'door-open',
                        'disabled' => false,
                        'activeRoutes' => [
                            'teacher.classrooms.index',
                            'teacher.classrooms.create',
                            'teacher.classrooms.store',
                            'teacher.classrooms.show',
                            'teacher.classrooms.assignments.store',
                            'teacher.classrooms.submissions.grade',
                            'teacher.classrooms.exams.store',
                        ],
                    ],
                    [
                        'label' => 'Calendario',
                        'href' => route('teacher.calendar.index', absolute: false),
                        'icon' => 'calendar-days',
                        'disabled' => false,
                        'activeRoutes' => ['teacher.calendar.index'],
                    ],
                    [
                        'label' => 'Videoclases',
                        'href' => route('teacher.meetings.index', absolute: false),
                        'icon' => 'video',
                        'disabled' => false,
                        'activeRoutes' => [
                            'teacher.meetings.index',
                            'teacher.meetings.create',
                            'teacher.meetings.store',
                            'teacher.meetings.show',
                            'teacher.meetings.join',
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Inteligencia académica',
                'href' => null,
                'icon' => 'brain',
                'disabled' => false,
                'children' => [
                    [
                        'label' => 'Panel pedagógico',
                        'href' => route('teacher.pedagogical-panel.index', absolute: false),
                        'icon' => 'layout-grid',
                        'disabled' => false,
                        'activeRoutes' => [
                            'teacher.pedagogical-panel.index',
                            'teacher.adaptive-learning.index',
                        ],
                    ],
                    [
                        'label' => 'Diagnósticos',
                        'href' => route('teacher.diagnostics.index', absolute: false),
                        'icon' => 'clipboard-list',
                        'disabled' => false,
                        'activeRoutes' => [
                            'teacher.diagnostics.index',
                            'teacher.diagnostics.create',
                            'teacher.diagnostics.store',
                            'teacher.diagnostics.show',
                            'teacher.diagnostics.results',
                            'teacher.diagnostic-results.index',
                        ],
                    ],
                    [
                        'label' => 'Riesgo académico',
                        'href' => route('teacher.academic-risk.index', absolute: false),
                        'icon' => 'alert-triangle',
                        'disabled' => false,
                        'activeRoutes' => [
                            'teacher.academic-risk.index',
                            'teacher.students-risk.index',
                        ],
                    ],
                    [
                        'label' => 'Analítica',
                        'href' => route('teacher.analytics.index', absolute: false),
                        'icon' => 'bar-chart-3',
                        'disabled' => false,
                        'activeRoutes' => ['teacher.analytics.index'],
                    ],
                    [
                        'label' => 'IA — Insights docente',
                        'href' => route('teacher.ai-insights.index', absolute: false),
                        'icon' => 'sparkles',
                        'disabled' => false,
                        'activeRoutes' => ['teacher.ai-insights.index'],
                    ],
                    [
                        'label' => 'Copiloto IA',
                        'href' => route('teacher.ai-copilot.index', absolute: false),
                        'icon' => 'bot',
                        'disabled' => false,
                        'activeRoutes' => [
                            'teacher.ai-copilot.index',
                            'teacher.ai-copilot.exams',
                            'teacher.ai-copilot.assignments',
                            'teacher.ai-copilot.rubrics',
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Comunicación',
                'href' => null,
                'icon' => 'megaphone',
                'disabled' => false,
                'children' => [
                    [
                        'label' => 'Comunicados',
                        'href' => route('teacher.announcements.index', absolute: false),
                        'icon' => 'megaphone',
                        'disabled' => false,
                        'activeRoutes' => [
                            'teacher.announcements.index',
                            'teacher.announcements.show',
                        ],
                    ],
                    [
                        'label' => 'Reportes',
                        'href' => route('teacher.reports.index', absolute: false),
                        'icon' => 'file-bar-chart',
                        'disabled' => false,
                        'activeRoutes' => ['teacher.reports.index'],
                    ],
                ],
            ],
        ];
    }
}
