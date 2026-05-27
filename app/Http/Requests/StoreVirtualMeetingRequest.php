<?php

namespace App\Http\Requests;

use App\Enums\MeetingProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVirtualMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'meeting_type' => ['required', 'string'],
            'provider' => ['nullable', 'string', Rule::in(array_column(MeetingProvider::cases(), 'value'))],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'virtual_classroom_id' => ['nullable', 'exists:virtual_classrooms,id'],
            'join_url' => ['nullable', 'string', 'url', 'max:2048'],
            'waiting_room_enabled' => ['boolean'],
            'recording_allowed' => ['boolean'],
            'is_recurring' => ['boolean'],
            'is_private' => ['boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $provider = $this->resolvedProvider();
            $joinUrl = trim((string) $this->input('join_url', ''));

            if ($this->requiresManualJoinUrl($provider) && $joinUrl === '') {
                $validator->errors()->add(
                    'join_url',
                    'Debe pegar el enlace de la reunión creado en Google Meet, Zoom o Teams.'
                );
            }
        });
    }

    public function resolvedProvider(): MeetingProvider
    {
        $value = $this->input('provider');

        if ($value === null || $value === '') {
            return MeetingProvider::Manual;
        }

        return MeetingProvider::from($value);
    }

    private function requiresManualJoinUrl(MeetingProvider $provider): bool
    {
        if ($provider === MeetingProvider::Manual) {
            return true;
        }

        if (in_array($provider, [MeetingProvider::Zoom, MeetingProvider::Teams], true)) {
            return true;
        }

        // Google Meet sin API: enlace pegado es el flujo principal; fallback solo si queda vacío.
        if ($provider === MeetingProvider::GoogleMeet) {
            return ! $this->googleMeetFallbackAvailable();
        }

        return true;
    }

    private function googleMeetFallbackAvailable(): bool
    {
        $code = config('meetings.google_meet.configured_room_code');

        return is_string($code) && $code !== '';
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);
        $validated['provider'] = $this->resolvedProvider()->value;

        return $validated;
    }
}
