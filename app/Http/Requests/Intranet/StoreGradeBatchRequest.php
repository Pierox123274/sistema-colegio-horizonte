<?php

namespace App\Http\Requests\Intranet;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Models\Enrollment;
use App\Models\Evaluation;
use Illuminate\Foundation\Http\FormRequest;

class StoreGradeBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Docente->value,
        ]) ?? false;
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'evaluation_id' => ['required', 'integer', 'exists:evaluations,id'],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.student_id' => ['required', 'integer', 'exists:students,id'],
            'entries.*.score' => ['required', 'numeric', 'min:0', 'max:20'],
            'entries.*.observations' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Evaluation|null $evaluation */
            $evaluation = Evaluation::query()->find((int) $this->input('evaluation_id'));

            if (! $evaluation) {
                return;
            }

            $studentIds = collect($this->input('entries', []))
                ->pluck('student_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->values();

            if ($studentIds->count() !== $studentIds->unique()->count()) {
                $validator->errors()->add('entries', 'No se permiten estudiantes duplicados en el registro.');
            }

            $validIds = Enrollment::query()
                ->where('academic_year_id', $evaluation->academic_year_id)
                ->where('section_id', $evaluation->section_id)
                ->whereIn('status', array_values(array_filter(
                    EnrollmentStatus::values(),
                    fn (string $value): bool => EnrollmentStatus::from($value)->blocksConcurrentEnrollment()
                )))
                ->whereIn('student_id', $studentIds->all())
                ->pluck('student_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $missingIds = $studentIds->reject(fn (int $id) => in_array($id, $validIds, true));
            if ($missingIds->isNotEmpty()) {
                $validator->errors()->add('entries', 'Hay estudiantes sin matrícula activa en la sección y año de la evaluación.');
            }
        });
    }
}
