<?php

namespace App\Http\Requests\Intranet;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Enrollment::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['guardian_id', 'classroom_id'] as $key) {
            if ($this->input($key) === '') {
                $this->merge([$key => null]);
            }
        }

        if ($this->input('enrollment_code') === '') {
            $this->merge(['enrollment_code' => null]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'enrollment_code' => ['nullable', 'string', 'max:60', Rule::unique('enrollments', 'enrollment_code')],
            'student_id' => ['required', 'exists:students,id'],
            'guardian_id' => ['nullable', 'exists:guardians,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'educational_level_id' => ['required', 'exists:educational_levels,id'],
            'grade_id' => [
                'required',
                Rule::exists('grades', 'id')->where(fn ($q) => $q->where(
                    'educational_level_id',
                    (int) $this->input('educational_level_id'),
                )),
            ],
            'section_id' => [
                'required',
                Rule::exists('sections', 'id')->where(fn ($q) => $q->where(
                    'grade_id',
                    (int) $this->input('grade_id'),
                )),
            ],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'enrollment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(EnrollmentStatus::values())],
            'observations' => ['nullable', 'string'],
        ];
    }
}
