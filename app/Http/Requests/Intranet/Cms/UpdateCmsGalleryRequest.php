<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Models\Cms\CmsGallery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCmsGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $gallery = $this->route('gallery');

        return $gallery instanceof CmsGallery && ($this->user()?->can('update', $gallery) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var CmsGallery $gallery */
        $gallery = $this->route('gallery');

        return [
            'title' => ['sometimes', 'string', 'max:200'],
            'slug' => ['sometimes', 'string', 'max:120', 'alpha_dash', Rule::unique('cms_galleries', 'slug')->ignore($gallery->id)],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['nullable', 'string', 'max:80'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
