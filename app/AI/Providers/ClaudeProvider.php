<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProviderInterface;
use App\AI\DTO\AIChatResult;

/** Reservado para Anthropic Claude. */
final class ClaudeProvider implements AIProviderInterface
{
    public function chat(array $messages, ?string $model = null): AIChatResult
    {
        return AIChatResult::failure(
            'El proveedor Claude aún no está implementado en esta versión.',
            'claude',
            'not_implemented',
        );
    }
}
