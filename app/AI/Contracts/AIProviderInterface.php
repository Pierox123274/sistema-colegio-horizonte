<?php

namespace App\AI\Contracts;

use App\AI\DTO\AIChatResult;

interface AIProviderInterface
{
    /**
     * @param  list<array{role: string, content: string}>  $messages
     */
    public function chat(array $messages, ?string $model = null): AIChatResult;
}
