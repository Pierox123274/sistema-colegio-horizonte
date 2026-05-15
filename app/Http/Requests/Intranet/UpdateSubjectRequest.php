<?php

namespace App\Http\Requests\Intranet;

use App\Enums\IntranetRole;
use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
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
        /** @var Subject $subject */
        $subject = $this->route('subject');

        return [
            'code' => ['required', 'string', 'max:30', Rule::unique('subjects', 'code')->ignore($subject->id)],
            'name' => ['required', 'string', 'max:120', Rule::unique('subjects', 'name')->ignore($subject->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
