<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Models\Cms\CmsGallery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCmsGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', CmsGallery::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'max:120', 'alpha_dash', Rule::unique('cms_galleries', 'slug')],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['nullable', 'string', 'max:80'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
