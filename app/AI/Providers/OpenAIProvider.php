<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProviderInterface;
use App\AI\DTO\AIChatResult;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class OpenAIProvider implements AIProviderInterface
{
    public function chat(array $messages, ?string $model = null): AIChatResult
    {
        $apiKey = config('ai.openai.api_key');
        if (! is_string($apiKey) || $apiKey === '') {
            Log::error('openai_error', [
                'phase' => 'config',
                'api_key_configured' => false,
                'model' => (string) config('ai.openai.model', 'gpt-4o-mini'),
            ]);

            return AIChatResult::failure(
                'No hay clave de API configurada para OpenAI. Revise OPENAI_API_KEY en el entorno seguro del servidor.',
                (string) config('ai.openai.model', 'gpt-4o-mini'),
                'missing_api_key',
            );
        }

        $baseUrl = rtrim((string) config('ai.openai.base_url', 'https://api.openai.com/v1'), '/');
        $model ??= (string) config('ai.openai.model', 'gpt-4o-mini');
        $timeout = (int) config('ai.openai.timeout_seconds', 45);
        $retries = (int) config('ai.openai.retries', 2);
        $maxOut = (int) config('ai.openai.max_output_tokens', 900);

        $endpoint = "{$baseUrl}/chat/completions";

        /** Chat Completions (compatible con gpt-4o-mini; usar max_completion_tokens según API actual). */
        $requestBody = [
            'model' => $model,
            'messages' => $messages,
            'max_completion_tokens' => $maxOut,
        ];

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout($timeout)
                ->retry($retries, 500, throw: false)
                ->post($endpoint, $requestBody);

            if (! $response->successful()) {
                $json = $response->json();
                $openaiCode = $this->openAiErrorCode($json);
                $this->logOpenAiHttpFailure(
                    response: $response,
                    model: $model,
                    endpoint: $endpoint,
                    timeout: $timeout,
                    apiKeyConfigured: true,
                    openaiCode: $openaiCode,
                );

                $generic = 'El servicio de IA no está disponible en este momento. Intente más tarde o contacte a secretaría.';
                $userMessage = $this->formatUserFacingFailure($generic, $json, $response->status(), null);

                return AIChatResult::failure(
                    $userMessage,
                    $model,
                    $openaiCode ?? 'http_'.$response->status(),
                );
            }

            $json = $response->json();
            $content = data_get($json, 'choices.0.message.content');
            if (! is_string($content) || $content === '') {
                $fr = data_get($json, 'choices.0.finish_reason');
                Log::error('openai_error', [
                    'phase' => 'empty_content',
                    'endpoint' => $endpoint,
                    'model' => $model,
                    'http_status' => $response->status(),
                    'finish_reason' => $fr,
                    'body_snippet' => mb_substr($response->body(), 0, 1500),
                ]);

                $generic = 'Respuesta vacía del proveedor de IA.';
                $userMessage = config('app.debug')
                    ? $generic.' [finish_reason: '.(is_string($fr) ? $fr : 'n/a').']'
                    : $generic;

                return AIChatResult::failure($userMessage, $model, 'empty_response');
            }

            return new AIChatResult(
                content: trim($content),
                success: true,
                model: (string) ($json['model'] ?? $model),
                rawMeta: [
                    'usage' => $json['usage'] ?? null,
                ],
            );
        } catch (Throwable $e) {
            $this->logOpenAiTransportException($e, $model, $endpoint, $timeout);

            $generic = 'No se pudo conectar con el servicio de IA.';
            $msg = config('app.debug')
                ? $generic.' ['.get_class($e).': '.$e->getMessage().']'
                : $generic;

            $code = $e instanceof ConnectionException ? 'connection' : 'transport';

            return AIChatResult::failure($msg, $model, $code);
        }
    }

    /**
     * @param  array<string, mixed>|null  $json
     */
    private function openAiErrorCode(?array $json): ?string
    {
        $code = data_get($json, 'error.code');
        if (is_string($code) && $code !== '') {
            return $code;
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|null  $json
     */
    private function formatUserFacingFailure(string $generic, ?array $json, int $httpStatus, ?Throwable $e): string
    {
        if (! config('app.debug')) {
            return $generic;
        }

        $code = data_get($json, 'error.code');
        $openaiMessage = data_get($json, 'error.message');
        $type = data_get($json, 'error.type');

        $bits = [];
        if (is_string($code) && $code !== '') {
            $bits[] = $code;
        }
        if (is_string($type) && $type !== '') {
            $bits[] = $type;
        }
        if (is_string($openaiMessage) && $openaiMessage !== '') {
            $bits[] = mb_substr($openaiMessage, 0, 500);
        }
        if ($bits === [] && $e !== null) {
            $bits[] = get_class($e);
            $bits[] = mb_substr($e->getMessage(), 0, 300);
        }
        if ($bits === []) {
            $bits[] = 'HTTP '.$httpStatus;
        }

        return $generic.' ['.implode(' · ', $bits).']';
    }

    private function logOpenAiHttpFailure(
        Response $response,
        string $model,
        string $endpoint,
        int $timeout,
        bool $apiKeyConfigured,
        ?string $openaiCode,
    ): void {
        $body = $response->body();
        Log::error('openai_error', [
            'phase' => 'http_error',
            'endpoint' => $endpoint,
            'model' => $model,
            'http_status' => $response->status(),
            'openai_error_code' => $openaiCode ?? data_get($response->json(), 'error.code'),
            'openai_error_type' => data_get($response->json(), 'error.type'),
            'openai_error_message' => data_get($response->json(), 'error.message'),
            'body_snippet' => mb_substr($body, 0, 2000),
            'timeout_seconds' => $timeout,
            'api_key_configured' => $apiKeyConfigured,
        ]);
    }

    private function logOpenAiTransportException(Throwable $e, string $model, string $endpoint, int $timeout): void
    {
        Log::error('openai_error', [
            'phase' => 'transport',
            'endpoint' => $endpoint,
            'model' => $model,
            'timeout_seconds' => $timeout,
            'exception_class' => get_class($e),
            'exception_message' => $e->getMessage(),
            'api_key_configured' => true,
        ]);
    }
}
