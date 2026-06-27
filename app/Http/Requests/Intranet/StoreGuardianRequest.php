<?php

namespace App\Http\Requests\Intranet;

use App\Enums\DocumentType;
use App\Enums\GuardianRelationshipType;
use App\Models\Guardian;
use App\Rules\UniqueDocumentNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuardianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Guardian::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('students') || ! is_array($this->input('students'))) {
            return;
        }

        $filtered = array_values(array_filter(
            $this->input('students'),
            fn ($row): bool => is_array($row) && ! empty($row['student_id']),
        ));
        $this->merge(['students' => $filtered]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'document_type' => ['required', Rule::enum(DocumentType::class)],
            'document_number' => ['nullable', 'string', 'max:32', new UniqueDocumentNumber(Guardian::class)],
            'relationship_type' => ['required', Rule::enum(GuardianRelationshipType::class)],
            'phone' => ['required', 'string', 'max:32'],
            'secondary_phone' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'workplace' => ['nullable', 'string', 'max:255'],
            'is_emergency_contact' => ['sometimes', 'boolean'],
            'students' => ['nullable', 'array'],
            'students.*.student_id' => ['required', 'integer', 'distinct', 'exists:students,id'],
            'students.*.relationship' => ['required', Rule::enum(GuardianRelationshipType::class)],
            'students.*.is_primary' => ['sometimes', 'boolean'],
            'students.*.is_financial_responsible' => ['sometimes', 'boolean'],
            'students.*.emergency_priority' => ['nullable', 'integer', 'min:1', 'max:99'],
            'students.*.observations' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
