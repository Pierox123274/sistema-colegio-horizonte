<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Models\Cms\CmsNewsCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCmsNewsCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', CmsNewsCategory::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', 'alpha_dash', Rule::unique('cms_news_categories', 'slug')],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
