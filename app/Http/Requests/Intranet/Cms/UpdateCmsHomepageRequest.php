<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Models\Cms\CmsSection;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCmsHomepageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', CmsSection::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sections' => ['required', 'array'],
            'sections.*.section_key' => ['required', 'string', 'max:80'],
            'sections.*.title' => ['nullable', 'string', 'max:200'],
            'sections.*.payload' => ['nullable', 'array'],
            'sections.*.is_active' => ['boolean'],
            'sections.*.sort_order' => ['integer', 'min:0'],
        ];
    }
}
