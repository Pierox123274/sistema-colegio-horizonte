<?php

namespace App\Http\Requests\Intranet;

use App\Models\Section;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Section $section */
        $section = $this->route('section');

        return $this->user()?->can('update', $section) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Section $section */
        $section = $this->route('section');
        $gradeId = $this->input('grade_id');

        return [
            'grade_id' => ['required', 'exists:grades,id'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sections', 'code')
                    ->where(fn ($q) => $q->where('grade_id', $gradeId))
                    ->ignore($section->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:65535'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
