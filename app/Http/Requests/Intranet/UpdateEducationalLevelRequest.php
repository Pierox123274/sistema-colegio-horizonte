<?php

namespace App\Http\Requests\Intranet;

use App\Models\EducationalLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEducationalLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var EducationalLevel $level */
        $level = $this->route('educational_level');

        return $this->user()?->can('update', $level) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var EducationalLevel $level */
        $level = $this->route('educational_level');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('educational_levels', 'code')->ignore($level->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
