<?php

namespace App\Http\Requests\Intranet;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', InventoryMovement::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', Rule::in(InventoryMovementType::values())],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'reason' => ['required', 'string', 'max:160'],
            'observations' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $type = (string) $this->input('type');
            if ($type !== InventoryMovementType::Salida->value) {
                return;
            }

            $product = Product::query()->find((int) $this->input('product_id'));
            if ($product === null) {
                return;
            }

            $quantity = (float) $this->input('quantity');
            if (((float) $product->current_stock - $quantity) < -0.0001) {
                $validator->errors()->add('quantity', 'La salida no puede dejar stock negativo.');
            }
        });
    }
}
