<?php

namespace App\Http\Requests\Intranet;

use App\Models\CashRegister;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OpenCashRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', CashRegister::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'opening_notes' => ['nullable', 'string'],
        ];
    }
}
