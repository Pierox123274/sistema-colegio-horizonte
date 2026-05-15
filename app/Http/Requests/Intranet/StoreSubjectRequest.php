<?php

namespace App\Http\Requests\Intranet;

use App\Enums\IntranetRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubjectRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:30', Rule::unique('subjects', 'code')],
            'name' => ['required', 'string', 'max:120', Rule::unique('subjects', 'name')],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
