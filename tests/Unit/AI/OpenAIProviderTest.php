<?php

namespace Tests\Unit\AI;

use App\AI\Providers\OpenAIProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenAIProviderTest extends TestCase
{
    public function test_chat_uses_chat_completions_endpoint_with_max_completion_tokens(): void
    {
        Config::set('ai.openai.api_key', 'sk-test-key');
        Config::set('ai.openai.base_url', 'https://api.openai.com/v1');
        Config::set('ai.openai.model', 'gpt-4o-mini');
        Config::set('ai.openai.timeout_seconds', 30);
        Config::set('ai.openai.retries', 0);
        Config::set('ai.openai.max_output_tokens', 100);

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Hola desde prueba']],
                ],
                'model' => 'gpt-4o-mini',
            ], 200),
        ]);

        $provider = app(OpenAIProvider::class);
        $result = $provider->chat([['role' => 'user', 'content' => 'Hola']]);

        $this->assertTrue($result->success);
        $this->assertSame('Hola desde prueba', $result->content);

        Http::assertSent(function ($request): bool {
            $data = $request->data();

            return str_contains((string) $request->url(), '/chat/completions')
                && ($data['max_completion_tokens'] ?? null) === 100
                && ! isset($data['max_tokens']);
        });
    }

    public function test_http_error_maps_openai_error_code_and_logs(): void
    {
        Config::set('ai.openai.api_key', 'sk-test-key');
        Config::set('ai.openai.retries', 0);

        Http::fake([
            'api.openai.com/*' => Http::response([
                'error' => [
                    'message' => 'Incorrect API key provided',
                    'type' => 'invalid_request_error',
                    'code' => 'invalid_api_key',
                ],
            ], 401),
        ]);

        Config::set('app.debug', false);

        $provider = app(OpenAIProvider::class);
        $result = $provider->chat([['role' => 'user', 'content' => 'x']]);

        $this->assertFalse($result->success);
        $this->assertSame('invalid_api_key', $result->errorCode);
        $this->assertStringContainsString('no está disponible', $result->content);
    }
}
