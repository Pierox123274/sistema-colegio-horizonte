<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Models\Cms\CmsGallery;
use Illuminate\Foundation\Http\FormRequest;

class UploadCmsGalleryImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $gallery = $this->route('gallery');

        return $gallery instanceof CmsGallery && ($this->user()?->can('uploadImages', $gallery) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array'],
            'images.*.image_path' => ['required_with:images', 'string', 'max:500'],
            'images.*.caption' => ['nullable', 'string', 'max:300'],
            'images.*.category' => ['nullable', 'string', 'max:80'],
            'images.*.sort_order' => ['integer', 'min:0'],
            'images.*.is_active' => ['boolean'],
        ];
    }
}
