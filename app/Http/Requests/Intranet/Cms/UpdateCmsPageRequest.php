<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Enums\CmsPublicationStatus;
use App\Models\Cms\CmsPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCmsPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $page = $this->route('page');

        return $page instanceof CmsPage && ($this->user()?->can('update', $page) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var CmsPage $page */
        $page = $this->route('page');

        return [
            'slug' => ['sometimes', 'string', 'max:120', 'alpha_dash', Rule::unique('cms_pages', 'slug')->ignore($page->id)],
            'title' => ['sometimes', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:300'],
            'hero_image' => ['nullable', 'string', 'max:500'],
            'hero_title' => ['nullable', 'string', 'max:200'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string', 'max:500'],
            'robots_index' => ['boolean'],
            'status' => ['sometimes', Rule::in(CmsPublicationStatus::values())],
            'published_at' => ['nullable', 'date'],
            'template' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
