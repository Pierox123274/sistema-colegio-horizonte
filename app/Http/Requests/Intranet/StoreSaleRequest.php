<?php

namespace App\Http\Requests\Intranet;

use App\Enums\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Sale::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['student_id', 'guardian_id'] as $key) {
            if ($this->input($key) === '') {
                $this->merge([$key => null]);
            }
        }

        if ($this->input('student_id') !== null) {
            $this->merge(['student_id' => (int) $this->input('student_id')]);
        }

        if ($this->input('guardian_id') !== null) {
            $this->merge(['guardian_id' => (int) $this->input('guardian_id')]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['nullable', 'exists:students,id'],
            'guardian_id' => ['nullable', 'exists:guardians,id'],
            'payment_method' => ['required', Rule::in(PaymentMethod::values())],
            'sold_at' => ['required', 'date'],
            'observations' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $items = $this->input('items', []);
            foreach ($items as $index => $item) {
                $product = Product::query()->find((int) ($item['product_id'] ?? 0));
                if (! $product) {
                    continue;
                }
                if (! $product->is_active) {
                    $validator->errors()->add("items.$index.product_id", 'No se puede vender un producto inactivo.');
                }
                $qty = (float) ($item['quantity'] ?? 0);
                if ((float) $product->current_stock < $qty - 0.0001) {
                    $validator->errors()->add("items.$index.quantity", 'La cantidad excede el stock disponible.');
                }
            }

            $studentId = $this->input('student_id');
            $guardianId = $this->input('guardian_id');
            if ($studentId !== null && $guardianId !== null) {
                $linked = Student::query()
                    ->whereKey((int) $studentId)
                    ->whereHas('guardians', fn ($q) => $q->where('guardians.id', (int) $guardianId))
                    ->exists();

                if (! $linked) {
                    $validator->errors()->add('guardian_id', 'El apoderado no pertenece al estudiante seleccionado.');
                }
            }
        });
    }
}
