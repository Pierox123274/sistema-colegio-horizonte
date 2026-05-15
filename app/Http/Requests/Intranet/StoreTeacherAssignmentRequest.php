<?php

namespace App\Http\Requests\Intranet;

use App\Enums\IntranetRole;
use App\Models\Grade;
use App\Models\Section;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeacherAssignmentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->input('subject_id') === '' || $this->input('subject_id') === null) {
            $this->merge(['subject_id' => null]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()?->can('create', TeacherAssignment::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'academic_year_id' => ['required', 'integer', Rule::exists('academic_years', 'id')],
            'educational_level_id' => ['required', 'integer', Rule::exists('educational_levels', 'id')],
            'grade_id' => ['required', 'integer', Rule::exists('grades', 'id')],
            'section_id' => ['required', 'integer', Rule::exists('sections', 'id')],
            'subject_id' => ['nullable', 'integer', Rule::exists('subjects', 'id')],
            'is_tutor' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $user = User::query()->find((int) $this->input('user_id'));
            if ($user === null || ! $user->hasRole(IntranetRole::Docente->value)) {
                $validator->errors()->add('user_id', 'Solo se pueden asignar usuarios con rol Docente.');
            }

            $section = Section::query()->find((int) $this->input('section_id'));
            if ($section === null) {
                return;
            }

            if ((int) $section->grade_id !== (int) $this->input('grade_id')) {
                $validator->errors()->add('section_id', 'La sección no pertenece al grado indicado.');
            }

            $grade = Grade::query()->find((int) $this->input('grade_id'));
            if ($grade !== null && (int) $grade->educational_level_id !== (int) $this->input('educational_level_id')) {
                $validator->errors()->add('grade_id', 'El grado no pertenece al nivel educativo indicado.');
            }
        });
    }
}
