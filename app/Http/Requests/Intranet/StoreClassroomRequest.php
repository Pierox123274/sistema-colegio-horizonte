<?php

namespace App\Http\Requests\Intranet;

use App\Models\Classroom;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Classroom::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('section_id') === '' || $this->input('section_id') === null) {
            $this->merge(['section_id' => null]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'section_id' => ['nullable', 'exists:sections,id'],
            'code' => ['required', 'string', 'max:50', Rule::unique('classrooms', 'code')],
            'name' => ['required', 'string', 'max:255'],
            'floor' => ['nullable', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1', 'max:65535'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
