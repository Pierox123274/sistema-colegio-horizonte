<?php

namespace App\Http\Requests\Intranet;

use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Section;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Attendance::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'educational_level_id' => ['required', 'integer', 'exists:educational_levels,id'],
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'attendance_date' => ['required', 'date'],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.student_id' => ['required', 'integer', 'exists:students,id'],
            'entries.*.status' => ['required', Rule::in(AttendanceStatus::values())],
            'entries.*.observation' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $section = Section::query()->with('grade')->find((int) $this->input('section_id'));
            if (! $section) {
                return;
            }

            if ((int) $section->grade_id !== (int) $this->input('grade_id')) {
                $validator->errors()->add('grade_id', 'La sección no pertenece al grado seleccionado.');
            }

            if ((int) $section->grade->educational_level_id !== (int) $this->input('educational_level_id')) {
                $validator->errors()->add('educational_level_id', 'El grado no pertenece al nivel seleccionado.');
            }

            $year = AcademicYear::query()->find((int) $this->input('academic_year_id'));
            $attendanceDate = (string) $this->input('attendance_date');
            if (
                $year !== null
                && ($attendanceDate < $year->starts_at?->toDateString() || $attendanceDate > $year->ends_at?->toDateString())
            ) {
                $validator->errors()->add('attendance_date', 'La fecha de asistencia está fuera del año académico seleccionado.');
            }

            $studentIds = collect($this->input('entries', []))
                ->pluck('student_id')
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->values();

            $duplicateStudentIds = $studentIds->duplicates()->unique()->values();
            if ($duplicateStudentIds->isNotEmpty()) {
                $validator->errors()->add('entries', 'No se puede enviar el mismo estudiante más de una vez en el registro masivo.');
            }

            $enrolledIds = Enrollment::query()
                ->where('academic_year_id', (int) $this->input('academic_year_id'))
                ->where('educational_level_id', (int) $this->input('educational_level_id'))
                ->where('grade_id', (int) $this->input('grade_id'))
                ->where('section_id', (int) $this->input('section_id'))
                ->whereIn('status', $this->activeEnrollmentStatuses())
                ->whereIn('student_id', $studentIds)
                ->pluck('student_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $notEnrolled = array_diff($studentIds->all(), $enrolledIds);
            if ($notEnrolled !== []) {
                $validator->errors()->add('entries', 'Solo se puede registrar asistencia para estudiantes matriculados en la sección seleccionada.');
            }
        });
    }

    /**
     * @return list<string>
     */
    private function activeEnrollmentStatuses(): array
    {
        return array_values(array_filter(
            EnrollmentStatus::values(),
            fn (string $value): bool => EnrollmentStatus::from($value)->blocksConcurrentEnrollment()
        ));
    }
}
