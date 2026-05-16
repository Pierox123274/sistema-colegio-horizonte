<?php

namespace App\Http\Requests\AI;

use App\Support\AIDashboard;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentAiMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('useStudentTutor', AIDashboard::class) ?? false;
    }

    /**
     * @return array<string, list<string>|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:'.(int) config('ai.max_user_message_length', 2000)],
        ];
    }
}
