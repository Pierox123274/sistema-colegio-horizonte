<?php

namespace App\Http\Requests\Intranet;

use App\Enums\PaymentEntryStatus;
use App\Enums\PaymentMethod;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Pension;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Payment::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['guardian_id', 'enrollment_id', 'pension_id', 'payment_code'] as $key) {
            if ($this->input($key) === '') {
                $this->merge([$key => null]);
            }
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_code' => ['nullable', 'string', 'max:60', Rule::unique('payments', 'payment_code')],
            'student_id' => ['required', 'exists:students,id'],
            'guardian_id' => ['nullable', 'exists:guardians,id'],
            'enrollment_id' => ['nullable', 'exists:enrollments,id'],
            'pension_id' => ['nullable', 'exists:pensions,id'],
            'payment_concept_id' => ['required', 'exists:payment_concepts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::in(PaymentMethod::values())],
            'paid_at' => ['required', 'date'],
            'status' => ['sometimes', Rule::in(PaymentEntryStatus::values())],
            'observations' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $studentId = (int) $this->input('student_id');

            if ($this->input('enrollment_id')) {
                $enrollment = Enrollment::query()->find((int) $this->input('enrollment_id'));
                if (! $enrollment || (int) $enrollment->student_id !== $studentId) {
                    $validator->errors()->add('enrollment_id', 'La matrícula no corresponde al estudiante.');
                }
            }

            if ($this->input('pension_id')) {
                $pension = Pension::query()->with('enrollment')->find((int) $this->input('pension_id'));
                if (! $pension || (int) $pension->enrollment->student_id !== $studentId) {
                    $validator->errors()->add('pension_id', 'La pensión no corresponde al estudiante.');
                }
                if ($pension && $this->input('enrollment_id')
                    && (int) $pension->enrollment_id !== (int) $this->input('enrollment_id')) {
                    $validator->errors()->add('enrollment_id', 'La matrícula no coincide con la pensión seleccionada.');
                }
            }
        });
    }
}
