<?php

namespace App\Http\Requests\Intranet;

use App\Enums\PensionStatus;
use App\Models\Pension;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePensionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Pension $pension */
        $pension = $this->route('pension');

        return $this->user()?->can('update', $pension) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Pension $pension */
        $pension = $this->route('pension');

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
            /** @var Pension $pension */
            $pension = $this->route('pension');
            $exists = Pension::query()
                ->where('enrollment_id', $this->input('enrollment_id'))
                ->where('month', $this->input('month'))
                ->where('year', $this->input('year'))
                ->whereKeyNot($pension->id)
                ->exists();
            if ($exists) {
                $validator->errors()->add('month', 'Ya existe una pensión para esta matrícula en el mes y año indicados.');
            }
        });
    }
}
