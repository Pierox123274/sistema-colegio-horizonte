<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProviderInterface;
use App\AI\DTO\AIChatResult;

/** Reservado para Google Gemini. */
final class GeminiProvider implements AIProviderInterface
{
    public function chat(array $messages, ?string $model = null): AIChatResult
    {
        return AIChatResult::failure(
            'El proveedor Gemini aún no está implementado en esta versión.',
            'gemini',
            'not_implemented',
        );
    }
}
