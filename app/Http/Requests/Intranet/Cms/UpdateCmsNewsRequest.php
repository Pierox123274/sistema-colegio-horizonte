<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Enums\CmsPublicationStatus;
use App\Models\Cms\CmsNews;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCmsNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $news = $this->route('news');

        return $news instanceof CmsNews && ($this->user()?->can('update', $news) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var CmsNews $news */
        $news = $this->route('news');

        return [
            'category_id' => ['nullable', 'integer', 'exists:cms_news_categories,id'],
            'slug' => ['sometimes', 'string', 'max:120', 'alpha_dash', Rule::unique('cms_news', 'slug')->ignore($news->id)],
            'title' => ['sometimes', 'string', 'max:200'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['sometimes', 'string'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['boolean'],
            'status' => ['sometimes', Rule::in(CmsPublicationStatus::values())],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string', 'max:500'],
            'robots_index' => ['boolean'],
        ];
    }
}
