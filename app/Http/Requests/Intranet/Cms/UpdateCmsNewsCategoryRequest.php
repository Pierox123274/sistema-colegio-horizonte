<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Models\Cms\CmsNewsCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCmsNewsCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $category = $this->route('category');

        return $category instanceof CmsNewsCategory && ($this->user()?->can('update', $category) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var CmsNewsCategory $category */
        $category = $this->route('category');

        return [
            'name' => ['sometimes', 'string', 'max:120'],
            'slug' => ['sometimes', 'string', 'max:120', 'alpha_dash', Rule::unique('cms_news_categories', 'slug')->ignore($category->id)],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
