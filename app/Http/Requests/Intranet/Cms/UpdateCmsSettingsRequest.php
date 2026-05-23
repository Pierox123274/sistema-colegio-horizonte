<?php

namespace App\Http\Requests\Intranet\Cms;

use App\Models\Cms\CmsSetting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCmsSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', CmsSetting::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'settings' => ['required', 'array'],
            'settings.school_name' => ['nullable', 'array'],
            'settings.school_tagline' => ['nullable', 'array'],
            'settings.contact' => ['nullable', 'array'],
            'settings.social' => ['nullable', 'array'],
        ];
    }
}
