<?php

namespace App\Http\Requests\Intranet;

use App\Models\ProductCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var ProductCategory|null $category */
        $category = $this->route('product_category');

        return $category !== null && ($this->user()?->can('update', $category) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var ProductCategory $category */
        $category = $this->route('product_category');

        return [
            'code' => ['required', 'string', 'max:60', Rule::unique('product_categories', 'code')->ignore($category->id)],
            'name' => ['required', 'string', 'max:160', Rule::unique('product_categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
