<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Enums\CmsPublicationStatus;
use App\Models\Cms\CmsNews;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCmsNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', CmsNews::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:cms_news_categories,id'],
            'slug' => ['required', 'string', 'max:120', 'alpha_dash', Rule::unique('cms_news', 'slug')],
            'title' => ['required', 'string', 'max:200'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['boolean'],
            'status' => ['required', Rule::in(CmsPublicationStatus::values())],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string', 'max:500'],
            'robots_index' => ['boolean'],
        ];
    }
}
