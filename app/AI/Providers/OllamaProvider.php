<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProviderInterface;
use App\AI\DTO\AIChatResult;

/**
 * Reservado para integración local (Ollama). Implementar en una fase futura.
 */
final class OllamaProvider implements AIProviderInterface
{
    public function chat(array $messages, ?string $model = null): AIChatResult
    {
        return AIChatResult::failure(
            'El proveedor Ollama aún no está activo. Use AI_PROVIDER=openai o implemente OllamaProvider.',
            'ollama',
            'not_implemented',
        );
    }
}
