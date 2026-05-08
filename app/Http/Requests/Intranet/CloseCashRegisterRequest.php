<?php

namespace App\Http\Requests\Intranet;

use App\Models\CashRegister;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CloseCashRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        $cashRegister = $this->route('cash_register');

        return $cashRegister instanceof CashRegister
            && ($this->user()?->can('close', $cashRegister) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'closing_notes' => ['nullable', 'string'],
        ];
    }
}
