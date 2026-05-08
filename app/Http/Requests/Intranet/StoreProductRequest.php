<?php

namespace App\Http\Requests\Intranet;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    private const PRODUCT_TYPES = ['uniforme', 'buzo', 'polo', 'casaca', 'pantalon', 'agenda', 'libro', 'util', 'otro'];

    private const PRODUCT_SIZES = ['4', '6', '8', '10', '12', '14', 'S', 'M', 'L', 'XL', 'unico', 'no_aplica'];

    private const GENDER_TARGETS = ['varon', 'mujer', 'unisex', 'no_aplica'];

    public function authorize(): bool
    {
        return $this->user()?->can('create', Product::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'code' => ['required', 'string', 'max:60', Rule::unique('products', 'code')],
            'name' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'product_type' => ['required', Rule::in(self::PRODUCT_TYPES)],
            'size' => ['required', Rule::in(self::PRODUCT_SIZES)],
            'color' => ['nullable', 'string', 'max:50'],
            'gender_target' => ['required', Rule::in(self::GENDER_TARGETS)],
            'unit' => ['required', 'string', 'max:40'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $type = (string) $this->input('product_type');
            $size = (string) $this->input('size');

            if (in_array($type, ['agenda', 'libro'], true) && ! in_array($size, ['unico', 'no_aplica'], true)) {
                $validator->errors()->add('size', 'Agendas y libros deben usar talla único o no_aplica.');
            }

            if (in_array($type, ['uniforme', 'buzo', 'polo', 'casaca', 'pantalon'], true) && $size === 'no_aplica') {
                $validator->errors()->add('size', 'Los productos de vestir requieren talla válida (no no_aplica).');
            }
        });
    }
}
