<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Models\Cms\CmsHeroSlide;
use Illuminate\Foundation\Http\FormRequest;

class StoreCmsHeroSlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', CmsHeroSlide::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'image_path' => ['nullable', 'string', 'max:500'],
            'badge' => ['nullable', 'string', 'max:80'],
            'cta_primary_label' => ['nullable', 'string', 'max:80'],
            'cta_primary_url' => ['nullable', 'string', 'max:500'],
            'cta_secondary_label' => ['nullable', 'string', 'max:80'],
            'cta_secondary_url' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }
}
