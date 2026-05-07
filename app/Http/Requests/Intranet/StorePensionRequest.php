<?php

namespace App\Http\Requests\Intranet;

use App\Enums\PensionStatus;
use App\Models\Pension;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePensionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Pension::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'enrollment_id' => ['required', 'exists:enrollments,id'],
            'payment_concept_id' => ['required', 'exists:payment_concepts,id'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:1990', 'max:2100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'status' => ['required', Rule::in(PensionStatus::values())],
            'observations' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $exists = Pension::query()
                ->where('enrollment_id', $this->input('enrollment_id'))
                ->where('month', $this->input('month'))
                ->where('year', $this->input('year'))
                ->exists();
            if ($exists) {
                $validator->errors()->add('month', 'Ya existe una pensión para esta matrícula en el mes y año indicados.');
            }
        });
    }
}
