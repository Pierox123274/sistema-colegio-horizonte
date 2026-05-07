<?php

namespace App\Http\Requests\Intranet;

use App\Enums\PaymentConceptType;
use App\Models\PaymentConcept;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentConceptRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var PaymentConcept $concept */
        $concept = $this->route('payment_concept');

        return $this->user()?->can('update', $concept) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var PaymentConcept $concept */
        $concept = $this->route('payment_concept');

        return [
            'code' => ['required', 'string', 'max:60', Rule::unique('payment_concepts', 'code')->ignore($concept->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'default_amount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', Rule::in(PaymentConceptType::values())],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
