<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Enums\PaymentEntryStatus;
use App\Enums\PaymentMethod;
use App\Enums\PensionStatus;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\GradeRecord;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use BackedEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

final class StudentContextService
{
    public function activeAcademicYear(): ?AcademicYear
    {
        return AcademicYear::query()->where('is_active', true)->first();
    }

    public function isEstudiantePortalScoped(User $user): bool
    {
        return $user->hasRole(IntranetRole::Estudiante->value)
            && ! $user->hasRole(IntranetRole::Administrador->value);
    }

    public function resolveStudentFor(User $user): ?Student
    {
        return Student::query()
            ->where('user_id', $user->id)
            ->first();
    }

    public function requireStudentFor(User $user): Student
    {
        $student = $this->resolveStudentFor($user);

        if ($student === null) {
            abort(403, 'No tiene una ficha de estudiante vinculada a su cuenta.');
        }

        return $student;
    }

    /**
     * Estudiante del portal: obligatorio para rol Estudiante; opcional para Administrador (supervisión).
     */
    public function portalStudentFor(User $user): ?Student
    {
        if ($this->isEstudiantePortalScoped($user)) {
            return $this->requireStudentFor($user);
        }

        return $this->resolveStudentFor($user);
    }

    /**
     * @return array{student: Student|null, portal_scoped: bool, has_student: bool, empty_message: string}
     */
    public function portalContext(User $user): array
    {
        $student = $this->portalStudentFor($user);

        return [
            'student' => $student,
            'portal_scoped' => $this->isEstudiantePortalScoped($user),
            'has_student' => $student !== null,
            'empty_message' => $this->emptyPortalMessage($user),
        ];
    }

    public function emptyPortalMessage(User $user): string
    {
        if ($this->isEstudiantePortalScoped($user)) {
            return 'Su cuenta no tiene una ficha de estudiante vinculada. Comuníquese con secretaría.';
        }

        return 'Modo supervisión: vincule un usuario a una ficha de estudiante para previsualizar datos personales.';
    }

    /**
     * @return array<string, mixed>
     */
    public function studentCard(Student $student): array
    {
        return [
            'id' => $student->id,
            'code' => $student->code,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'full_name' => $student->fullName(),
            'document_type' => $student->document_type->value,
            'document_number' => $student->document_number,
            'birth_date' => $student->birth_date->format('Y-m-d'),
            'gender' => $student->gender->value,
            'status' => $student->status->value,
            'email' => $student->email,
            'phone' => $student->phone,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function currentEnrollmentPayload(Student $student): ?array
    {
        $year = $this->activeAcademicYear();

        $query = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', EnrollmentStatus::Matriculado->value)
            ->with([
                'academicYear:id,name,year,is_active',
                'educationalLevel:id,name',
                'grade:id,name',
                'section:id,name',
                'classroom:id,name',
            ])
            ->orderByDesc('id');

        if ($year !== null) {
            $enrollment = (clone $query)->where('academic_year_id', $year->id)->first();
            if ($enrollment !== null) {
                return $this->formatEnrollment($enrollment);
            }
        }

        $latest = $query->first();

        return $latest !== null ? $this->formatEnrollment($latest) : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboardStats(Student $student): array
    {
        $gradeCount = GradeRecord::query()->where('student_id', $student->id)->count();
        $attendanceCount = Attendance::query()->where('student_id', $student->id)->count();
        $paymentCount = Payment::query()->where('student_id', $student->id)->count();

        $recentGrades = GradeRecord::query()
            ->with(['evaluation:id,title,period,subject_id', 'evaluation.subject:id,name'])
            ->where('student_id', $student->id)
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn (GradeRecord $record): array => [
                'id' => $record->id,
                'score' => (string) $record->score,
                'subject' => $record->evaluation?->subject?->name,
                'evaluation' => $record->evaluation?->title,
                'period' => $record->evaluation?->period,
            ])
            ->all();

        return [
            'grade_records_count' => $gradeCount,
            'attendance_records_count' => $attendanceCount,
            'payments_count' => $paymentCount,
            'recent_grades' => $recentGrades,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function guardiansForProfile(Student $student): array
    {
        return $student->guardians()
            ->orderByPivot('is_primary', 'desc')
            ->orderByPivot('emergency_priority')
            ->get()
            ->map(fn ($guardian): array => [
                'id' => $guardian->id,
                'full_name' => $guardian->fullName(),
                'relationship' => $guardian->pivot->relationship,
                'phone' => $guardian->phone,
                'email' => $guardian->email,
                'is_primary' => (bool) $guardian->pivot->is_primary,
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function academicHistoryEnrollments(Student $student): array
    {
        return Enrollment::query()
            ->where('student_id', $student->id)
            ->with([
                'academicYear:id,name,year',
                'educationalLevel:id,name',
                'grade:id,name',
                'section:id,name',
            ])
            ->orderByDesc('academic_year_id')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Enrollment $enrollment): array => [
                'id' => $enrollment->id,
                'enrollment_code' => $enrollment->enrollment_code,
                'status' => $enrollment->status->value,
                'enrollment_date' => $enrollment->enrollment_date->format('Y-m-d'),
                'academic_year' => $enrollment->academicYear?->only(['id', 'name', 'year']),
                'educational_level' => $enrollment->educationalLevel?->only(['id', 'name']),
                'grade' => $enrollment->grade?->only(['id', 'name']),
                'section' => $enrollment->section?->only(['id', 'name']),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function portalLinks(): array
    {
        return [
            'dashboard' => route('student.dashboard', absolute: false),
            'grades' => route('student.grades.index', absolute: false),
            'attendance' => route('student.attendance.index', absolute: false),
            'payments' => route('student.payments.index', absolute: false),
            'profile' => route('student.profile.show', absolute: false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attendancePortalFor(Student $student, Request $request): array
    {
        $filters = [
            'date_from' => (string) $request->query('date_from', ''),
            'date_to' => (string) $request->query('date_to', ''),
            'status' => (string) $request->query('status', ''),
            'section_id' => (string) $request->query('section_id', ''),
        ];

        $baseQuery = Attendance::query()
            ->where('student_id', $student->id)
            ->with([
                'section:id,name',
                'grade:id,name',
                'recordedBy:id,name',
            ]);

        if ($filters['date_from'] !== '') {
            $baseQuery->whereDate('attendance_date', '>=', $filters['date_from']);
        }
        if ($filters['date_to'] !== '') {
            $baseQuery->whereDate('attendance_date', '<=', $filters['date_to']);
        }
        if ($filters['status'] !== '' && in_array($filters['status'], AttendanceStatus::values(), true)) {
            $baseQuery->where('status', $filters['status']);
        }
        if ($filters['section_id'] !== '' && ctype_digit($filters['section_id'])) {
            $baseQuery->where('section_id', (int) $filters['section_id']);
        }

        $metrics = $this->attendanceMetrics((clone $baseQuery)->get());

        $history = (clone $baseQuery)
            ->orderByDesc('attendance_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Attendance $row): array => [
                'id' => $row->id,
                'attendance_date' => $row->attendance_date->format('Y-m-d'),
                'attendance_date_label' => $row->attendance_date->translatedFormat('d/m/Y'),
                'status' => $row->status->value,
                'status_label' => $this->attendanceStatusLabel($row->status),
                'observation' => $row->observation,
                'section' => $row->section?->only(['id', 'name']),
                'grade' => $row->grade?->only(['id', 'name']),
                'recorded_by' => $row->recordedBy?->only(['id', 'name']),
            ]);

        return [
            'history' => $history,
            'metrics' => $metrics,
            'filters' => $filters,
            'catalog' => [
                'statuses' => AttendanceStatus::options(),
                'sections' => $this->attendanceSectionOptionsFor($student),
            ],
        ];
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public function attendanceSectionOptionsFor(Student $student): array
    {
        return Attendance::query()
            ->where('student_id', $student->id)
            ->whereNotNull('section_id')
            ->with('section:id,name')
            ->get()
            ->pluck('section')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->map(fn ($section): array => [
                'value' => (string) $section->id,
                'label' => $section->name,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function paymentsPortalFor(Student $student, PaymentService $paymentService): array
    {
        $financial = $paymentService->studentFinancialSummary($student->id);
        $pensionsRaw = $financial['pensions'];

        $pensions = collect($pensionsRaw)
            ->map(fn (array $row): array => $this->formatPensionRow($row))
            ->sortByDesc(fn (array $row): string => sprintf('%04d-%02d', (int) $row['year'], (int) $row['month']))
            ->values()
            ->all();

        $summary = $this->paymentsSummaryFromPensions($pensionsRaw);

        $payments = Payment::query()
            ->where('student_id', $student->id)
            ->with('paymentConcept:id,name,code')
            ->orderByDesc('paid_at')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Payment $payment): array => $this->formatPaymentRow($payment));

        return [
            'summary' => $summary,
            'pensions' => $pensions,
            'payments' => $payments,
            'has_pending_pensions' => $summary['pending_count'] > 0,
        ];
    }

    /**
     * @param  Collection<int, GradeRecord>  $rows
     * @return array<string, mixed>
     */
    public function gradeMetrics(Collection $rows): array
    {
        if ($rows->isEmpty()) {
            return [
                'course_average' => 0,
                'general_average' => 0,
            ];
        }

        return [
            'course_average' => round((float) $rows->avg('score'), 2),
            'general_average' => round((float) $rows->avg('score'), 2),
        ];
    }

    /**
     * @param  Collection<int, Attendance>  $rows
     * @return array<string, mixed>
     */
    public function attendanceMetrics(Collection $rows): array
    {
        $total = $rows->count();
        if ($total === 0) {
            return [
                'total' => 0,
                'attendance_percentage' => 0,
                'present_count' => 0,
                'late_count' => 0,
                'absence_count' => 0,
                'justified_count' => 0,
            ];
        }

        $countByStatus = fn (string $status): int => $rows
            ->filter(fn (Attendance $row): bool => $this->attendanceStatusValue($row) === $status)
            ->count();

        $presentes = $countByStatus(AttendanceStatus::Presente->value);
        $tardes = $countByStatus(AttendanceStatus::Tarde->value);
        $faltas = $countByStatus(AttendanceStatus::Falta->value);
        $justificados = $countByStatus(AttendanceStatus::Justificado->value);

        return [
            'total' => $total,
            'attendance_percentage' => round((($presentes + $justificados) / max(1, $total)) * 100, 2),
            'present_count' => $presentes,
            'late_count' => $tardes,
            'absence_count' => $faltas,
            'justified_count' => $justificados,
        ];
    }

    private function attendanceStatusValue(Attendance $row): string
    {
        $status = $row->status;

        return $status instanceof BackedEnum ? $status->value : (string) $status;
    }

    private function attendanceStatusLabel(AttendanceStatus $status): string
    {
        return collect(AttendanceStatus::options())
            ->firstWhere('value', $status->value)['label'] ?? $status->value;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function formatPensionRow(array $row): array
    {
        $status = (string) ($row['status'] ?? '');
        $statusEnum = PensionStatus::tryFrom($status);

        return [
            ...$row,
            'period_label' => $this->periodLabel((int) $row['month'], (int) $row['year']),
            'due_date_label' => $this->formatDateLabel((string) $row['due_date']),
            'amount_label' => $this->formatMoney((string) $row['amount']),
            'pending_label' => $this->formatMoney((string) $row['pending']),
            'status_label' => $statusEnum !== null
                ? (collect(PensionStatus::options())->firstWhere('value', $statusEnum->value)['label'] ?? $status)
                : $status,
        ];
    }

    private function formatPaymentRow(Payment $payment): array
    {
        $method = $payment->payment_method instanceof BackedEnum
            ? $payment->payment_method->value
            : (string) $payment->payment_method;
        $status = $payment->status instanceof BackedEnum
            ? $payment->status->value
            : (string) $payment->status;

        return [
            'id' => $payment->id,
            'payment_code' => $payment->payment_code,
            'amount' => (string) $payment->amount,
            'amount_label' => $this->formatMoney((string) $payment->amount),
            'paid_at' => $payment->paid_at?->toIso8601String(),
            'paid_at_label' => $payment->paid_at?->translatedFormat('d/m/Y H:i') ?? '—',
            'status' => $status,
            'status_label' => collect(PaymentEntryStatus::options())->firstWhere('value', $status)['label'] ?? $status,
            'payment_method' => $method,
            'payment_method_label' => collect(PaymentMethod::options())->firstWhere('value', $method)['label'] ?? $method,
            'payment_concept' => $payment->paymentConcept?->only(['id', 'name', 'code']),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $pensionsRaw
     * @return array<string, int|float|string>
     */
    private function paymentsSummaryFromPensions(array $pensionsRaw): array
    {
        $collection = collect($pensionsRaw);
        $active = $collection->reject(fn (array $p): bool => ($p['status'] ?? '') === PensionStatus::Anulado->value);

        $paidStatuses = [PensionStatus::Pagado->value];
        $pendingStatuses = [
            PensionStatus::Pendiente->value,
            PensionStatus::Parcial->value,
            PensionStatus::Vencido->value,
        ];

        $paid = $active->whereIn('status', $paidStatuses);
        $pending = $active->whereIn('status', $pendingStatuses);

        return [
            'total_count' => $active->count(),
            'paid_count' => $paid->count(),
            'pending_count' => $pending->count(),
            'pending_amount' => round((float) $pending->sum(fn (array $p): float => (float) ($p['pending'] ?? 0)), 2),
            'pending_amount_label' => $this->formatMoney((string) round((float) $pending->sum(fn (array $p): float => (float) ($p['pending'] ?? 0)), 2)),
        ];
    }

    private function periodLabel(int $month, int $year): string
    {
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $name = $months[$month] ?? "Mes {$month}";

        return "{$name} {$year}";
    }

    private function formatDateLabel(string $isoDate): string
    {
        if ($isoDate === '') {
            return '—';
        }

        try {
            return Carbon::parse($isoDate)->translatedFormat('d/m/Y');
        } catch (\Throwable) {
            return $isoDate;
        }
    }

    private function formatMoney(string $amount): string
    {
        return 'S/ '.number_format((float) $amount, 2);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatEnrollment(Enrollment $enrollment): array
    {
        return [
            'id' => $enrollment->id,
            'enrollment_code' => $enrollment->enrollment_code,
            'status' => $enrollment->status->value,
            'enrollment_date' => $enrollment->enrollment_date->format('Y-m-d'),
            'academic_year' => $enrollment->academicYear?->only(['id', 'name', 'year', 'is_active']),
            'educational_level' => $enrollment->educationalLevel?->only(['id', 'name']),
            'grade' => $enrollment->grade?->only(['id', 'name']),
            'section' => $enrollment->section?->only(['id', 'name']),
            'classroom' => $enrollment->classroom?->only(['id', 'name']),
        ];
    }
}
