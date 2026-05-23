<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Models\Cms\CmsMedia;
use Illuminate\Foundation\Http\FormRequest;

class StoreCmsMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', CmsMedia::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
            'alt' => ['nullable', 'string', 'max:200'],
        ];
    }
}
