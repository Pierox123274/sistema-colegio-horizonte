<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Enums\StudentStatus;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use App\Support\AnalyticsDashboard;
use Illuminate\Support\Facades\Gate;

final class AnalyticsService
{
    public function __construct(
        private readonly AcademicAnalyticsService $academic,
        private readonly FinancialAnalyticsService $financial,
        private readonly InventoryAnalyticsService $inventory,
        private readonly AnnouncementService $announcements,
        private readonly TeacherContextService $teacherContext,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function executivePayload(User $user, array $filters): array
    {
        $payload = [
            'filters' => $filters,
            'catalog' => $this->filterCatalog(),
            'permissions' => [
                'financial' => Gate::forUser($user)->allows('viewFinancial', AnalyticsDashboard::class),
                'inventory' => Gate::forUser($user)->allows('viewInventory', AnalyticsDashboard::class),
                'users' => Gate::forUser($user)->allows('viewUsersMetrics', AnalyticsDashboard::class),
            ],
        ];

        if (Gate::forUser($user)->allows('viewExecutive', AnalyticsDashboard::class)) {
            $payload['academic'] = [
                'summary' => $this->academic->summary($filters),
                'performance_by_section' => $this->academic->performanceBySection($filters),
                'top_students' => $this->academic->topStudents($filters),
                'risk_students' => $this->academic->riskStudents($filters),
                'attendance_trend' => $this->academic->attendanceTrend($filters),
                'attendance_distribution' => $this->academic->attendanceStatusDistribution($filters),
            ];
        }

        if (Gate::forUser($user)->allows('viewFinancial', AnalyticsDashboard::class)) {
            $payload['financial'] = [
                'summary' => $this->financial->summary($filters),
                'income_trend' => $this->financial->incomeTrend($filters),
                'pension_distribution' => $this->financial->pensionStatusDistribution($filters),
                'recent_payments' => $this->financial->recentPayments($filters),
            ];
        }

        if (Gate::forUser($user)->allows('viewInventory', AnalyticsDashboard::class)) {
            $payload['inventory'] = [
                'summary' => $this->inventory->summary($filters),
                'low_stock' => $this->inventory->lowStockProducts(),
                'top_products' => $this->inventory->topProducts($filters),
                'sales_trend' => $this->inventory->salesTrend($filters),
            ];
        }

        if (Gate::forUser($user)->allows('viewUsersMetrics', AnalyticsDashboard::class)) {
            $payload['users'] = $this->usersSummary($filters);
        }

        $payload['announcements'] = $this->announcementsSummary($user);

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function teacherPayload(User $user, array $filters): array
    {
        $sectionIds = $this->teacherContext->activeSectionIdsFor($user);

        return [
            'filters' => $filters,
            'catalog' => $this->filterCatalog(),
            'section_ids' => $sectionIds,
            'has_assignments' => $sectionIds !== [],
            'academic' => [
                'summary' => $this->academic->summary($filters, $sectionIds),
                'performance_by_section' => $this->academic->performanceBySection($filters, $sectionIds),
                'top_students' => $this->academic->topStudents($filters, $sectionIds),
                'risk_students' => $this->academic->riskStudents($filters, $sectionIds),
                'attendance_trend' => $this->academic->attendanceTrend($filters, $sectionIds),
                'attendance_distribution' => $this->academic->attendanceStatusDistribution($filters, $sectionIds),
                'recent_evaluations' => $this->academic->recentEvaluations($filters, $sectionIds),
                'most_absences' => $this->academic->studentsWithMostAbsences($filters, $sectionIds),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function reportPayload(User $user, string $type, array $filters): array
    {
        $sectionIds = null;
        if ($user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasRole(IntranetRole::Administrador->value)
        ) {
            $sectionIds = $this->teacherContext->activeSectionIdsFor($user);
        }

        return match ($type) {
            'academic', 'grades' => [
                'type' => $type,
                'title' => $type === 'grades' ? 'Reporte de notas' : 'Reporte académico',
                'summary' => $this->academic->summary($filters, $sectionIds),
                'performance_by_section' => $this->academic->performanceBySection($filters, $sectionIds),
                'top_students' => $this->academic->topStudents($filters, $sectionIds, 10),
                'risk_students' => $this->academic->riskStudents($filters, $sectionIds, 15),
            ],
            'attendance' => [
                'type' => $type,
                'title' => 'Reporte de asistencia',
                'summary' => ['attendance_average' => $this->academic->summary($filters, $sectionIds)['attendance_average']],
                'attendance_trend' => $this->academic->attendanceTrend($filters, $sectionIds),
                'attendance_distribution' => $this->academic->attendanceStatusDistribution($filters, $sectionIds),
                'most_absences' => $this->academic->studentsWithMostAbsences($filters, $sectionIds, 20),
            ],
            'financial' => [
                'type' => $type,
                'title' => 'Reporte financiero',
                'summary' => $this->financial->summary($filters),
                'income_trend' => $this->financial->incomeTrend($filters),
                'pension_distribution' => $this->financial->pensionStatusDistribution($filters),
                'recent_payments' => $this->financial->recentPayments($filters, 20),
            ],
            'sales' => [
                'type' => $type,
                'title' => 'Reporte de ventas',
                'summary' => $this->inventory->summary($filters),
                'top_products' => $this->inventory->topProducts($filters, 15),
                'sales_trend' => $this->inventory->salesTrend($filters),
            ],
            'inventory' => [
                'type' => $type,
                'title' => 'Reporte de inventario',
                'summary' => $this->inventory->summary($filters),
                'low_stock' => $this->inventory->lowStockProducts(25),
                'top_products' => $this->inventory->topProducts($filters, 10),
            ],
            default => abort(404),
        };
    }

    /**
     * @return list<string>
     */
    public function allowedReportTypes(User $user): array
    {
        $types = [];

        if (Gate::forUser($user)->allows('viewExecutive', AnalyticsDashboard::class)) {
            $types = array_merge($types, ['academic', 'attendance', 'grades']);
        }

        if (Gate::forUser($user)->allows('viewFinancial', AnalyticsDashboard::class)) {
            $types[] = 'financial';
        }

        if (Gate::forUser($user)->allows('viewInventory', AnalyticsDashboard::class)) {
            $types = array_merge($types, ['sales', 'inventory']);
        }

        if ($user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasRole(IntranetRole::Administrador->value)
        ) {
            return ['academic', 'attendance', 'grades'];
        }

        return array_values(array_unique($types));
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, int>
     */
    public function usersSummary(array $filters): array
    {
        $yearId = $this->academic->resolveAcademicYearId($filters);

        $activeEnrollments = 0;
        if ($yearId !== null) {
            $activeEnrollments = Enrollment::query()
                ->where('academic_year_id', $yearId)
                ->where('status', EnrollmentStatus::Matriculado->value)
                ->count();
        }

        return [
            'total_users' => User::query()->where('is_active', true)->count(),
            'active_teachers' => User::query()
                ->where('is_active', true)
                ->whereHas('roles', fn ($q) => $q->where('name', IntranetRole::Docente->value))
                ->count(),
            'active_students' => Student::query()
                ->where('status', StudentStatus::Activo->value)
                ->count(),
            'active_enrollments' => $activeEnrollments,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function announcementsSummary(User $user): array
    {
        return [
            'unread_count' => $this->announcements->unreadCountFor($user),
            'recent' => $this->announcements->recentForUser($user, 5)
                ->map(fn ($a) => $this->announcements->cardPayload($a, $user))
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function filterCatalog(): array
    {
        $years = AcademicYear::query()->orderByDesc('year')->get(['id', 'name', 'year', 'is_active']);

        return [
            'academic_years' => $years->map(fn (AcademicYear $y): array => [
                'value' => (string) $y->id,
                'label' => $y->name.' ('.$y->year.')'.($y->is_active ? ' — Activo' : ''),
            ])->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $request
     * @return array<string, mixed>
     */
    public function normalizeFilters(array $request): array
    {
        return [
            'academic_year_id' => $request['academic_year_id'] ?? '',
            'date_from' => $request['date_from'] ?? '',
            'date_to' => $request['date_to'] ?? '',
            'section_id' => $request['section_id'] ?? '',
        ];
    }
}
