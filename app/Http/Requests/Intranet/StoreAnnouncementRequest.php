<?php

namespace App\Http\Requests\Intranet;

use App\Enums\AnnouncementAudienceType;
use App\Enums\AnnouncementPriority;
use App\Models\Announcement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Announcement::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string', 'max:20000'],
            'priority' => ['required', Rule::in(AnnouncementPriority::values())],
            'audience_type' => ['required', Rule::in(AnnouncementAudienceType::values())],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
            'recipient_user_ids' => [
                Rule::requiredIf(fn () => $this->input('audience_type') === AnnouncementAudienceType::CustomUsers->value),
                'array',
            ],
            'recipient_user_ids.*' => ['integer', 'exists:users,id'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'título',
            'content' => 'contenido',
            'priority' => 'prioridad',
            'audience_type' => 'audiencia',
            'starts_at' => 'fecha de publicación',
            'ends_at' => 'fecha de expiración',
            'recipient_user_ids' => 'destinatarios',
            'attachment' => 'adjunto',
        ];
    }
}
