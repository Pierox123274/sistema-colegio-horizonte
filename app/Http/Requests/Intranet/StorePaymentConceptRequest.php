<?php

namespace App\Http\Requests\Intranet;

use App\Enums\PaymentConceptType;
use App\Models\PaymentConcept;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentConceptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PaymentConcept::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:60', Rule::unique('payment_concepts', 'code')],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'default_amount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', Rule::in(PaymentConceptType::values())],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
