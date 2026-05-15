<?php

namespace App\Http\Requests\Intranet;

use App\Enums\IntranetRole;
use App\Models\Grade;
use App\Models\Section;
use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(IntranetRole::Administrador->value) ?? false;
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'educational_level_id' => ['required', 'integer', 'exists:educational_levels,id'],
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'title' => ['required', 'string', 'max:120'],
            'period' => ['required', 'string', 'max:60'],
            'evaluated_at' => ['required', 'date'],
            'max_score' => ['required', 'numeric', 'min:1', 'max:20'],
            'weight' => ['required', 'numeric', 'min:0.1', 'max:5'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $grade = Grade::query()->find((int) $this->input('grade_id'));
            $section = Section::query()->find((int) $this->input('section_id'));

            if ($grade && (int) $grade->educational_level_id !== (int) $this->input('educational_level_id')) {
                $validator->errors()->add('grade_id', 'El grado no pertenece al nivel seleccionado.');
            }
            if ($section && (int) $section->grade_id !== (int) $this->input('grade_id')) {
                $validator->errors()->add('section_id', 'La sección no pertenece al grado seleccionado.');
            }
        });
    }
}
