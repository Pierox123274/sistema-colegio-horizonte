<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProviderInterface;
use App\AI\DTO\AIChatResult;

/** Proveedor interno cuando la IA está deshabilitada o sin credenciales. */
final class NullAIProvider implements AIProviderInterface
{
    public function chat(array $messages, ?string $model = null): AIChatResult
    {
        return AIChatResult::disabled();
    }
}
