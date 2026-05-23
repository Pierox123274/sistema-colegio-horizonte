<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Models\Cms\CmsTestimonial;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCmsTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        $testimonial = $this->route('testimonial');

        return $testimonial instanceof CmsTestimonial && ($this->user()?->can('update', $testimonial) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:120'],
            'role' => ['sometimes', 'string', 'max:120'],
            'org' => ['nullable', 'string', 'max:200'],
            'quote' => ['sometimes', 'string', 'max:2000'],
            'photo_path' => ['nullable', 'string', 'max:500'],
            'is_visible' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
