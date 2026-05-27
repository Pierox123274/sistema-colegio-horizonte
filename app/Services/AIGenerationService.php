<?php

namespace App\Services;

use App\AI\DTO\AIChatResult;
use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Models\User;
use App\Support\AIPromptSanitizer;
use Illuminate\Support\Facades\Cache;

/**
 * Generación estructurada IA: JSON, caché, auditoría (hash, sin prompt completo).
 */
final class AIGenerationService
{
    public function __construct(
        private readonly AITutorService $tutor,
        private readonly AuditService $audit,
    ) {}

    public function moduleEnabled(string $module): bool
    {
        if (! config('ai.tutor_enabled')) {
            return false;
        }

        return (bool) config("ai.modules.{$module}", true);
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{
     *   success: bool,
     *   data: array<string, mixed>|null,
     *   raw: string|null,
     *   model: string,
     *   cached: bool,
     *   fallback: bool,
     *   error_code: string|null
     * }
     */
    public function generateStructured(
        User $user,
        string $module,
        string $promptKey,
        string $action,
        string $userInstruction,
        array $context = [],
        ?callable $localFallback = null,
    ): array {
        if (! $this->moduleEnabled($module)) {
            $data = $localFallback !== null ? $localFallback() : null;

            return [
                'success' => $data !== null,
                'data' => $data,
                'raw' => null,
                'model' => 'none',
                'cached' => false,
                'fallback' => true,
                'error_code' => 'disabled',
            ];
        }

        $maxLen = (int) config('ai.max_user_message_length', 2000);
        $instruction = AIPromptSanitizer::sanitizeUserMessage($userInstruction, $maxLen);
        $contextJson = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $promptHash = AIPromptSanitizer::hashPayload($action, $promptKey, $instruction, $contextJson);

        $cacheKey = config('ai.cache_keys.prefix', 'ai:').'gen:'.$action.':'.$promptHash;
        if ($cached = Cache::get($cacheKey)) {
            $this->auditGeneration($user, $action, $promptHash, true, true);

            return [...$cached, 'cached' => true];
        }

        $system = (string) config("ai.system_prompts.{$promptKey}", config('ai.system_prompts.teacher_copilot'));
        $messages = [
            ['role' => 'system', 'content' => $system."\n\nContexto pedagógico (JSON, minimizado): ".$contextJson],
            ['role' => 'user', 'content' => $instruction],
        ];

        $result = $this->tutor->provider()->chat($messages);
        $parsed = $result->success ? $this->parseJsonFromContent($result->content) : null;

        if ($parsed === null && $localFallback !== null) {
            $parsed = $localFallback();
            $result = new AIChatResult(
                json_encode($parsed, JSON_UNESCAPED_UNICODE) ?: '{}',
                true,
                $result->model !== 'n/a' ? $result->model : 'local-fallback',
            );
        }

        $payload = [
            'success' => $result->success && $parsed !== null,
            'data' => $parsed,
            'raw' => $result->content,
            'model' => $result->model,
            'cached' => false,
            'fallback' => ! $result->success || $parsed === null,
            'error_code' => $result->errorCode,
        ];

        $this->auditGeneration($user, $action, $promptHash, $result->success, false);

        if ($payload['success']) {
            Cache::put($cacheKey, $payload, (int) config('ai.generation_cache_ttl_seconds', 1800));
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function parseJsonFromContent(string $content): ?array
    {
        $text = trim($content);
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/u', $text, $matches)) {
            $text = trim($matches[1]);
        }

        $decoded = json_decode($text, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function auditGeneration(User $user, string $action, string $promptHash, bool $providerSuccess, bool $cacheHit): void
    {
        $this->audit->log(
            AuditAction::AiQuery,
            AuditModule::Ai,
            $user,
            User::class,
            $user->id,
            'Generación IA: '.$action,
            null,
            null,
            $providerSuccess ? AuditResult::Success : AuditResult::Failure,
            context: [
                'prompt_sha256' => $promptHash,
                'cache_hit' => $cacheHit,
                'provider' => config('ai.provider'),
                'action' => $action,
            ],
        );
    }
}
