<?php

namespace App\Services;

use App\AI\Contracts\AIProviderInterface;
use App\AI\DTO\AIChatResult;
use App\AI\Providers\ClaudeProvider;
use App\AI\Providers\GeminiProvider;
use App\AI\Providers\NullAIProvider;
use App\AI\Providers\OllamaProvider;
use App\AI\Providers\OpenAIProvider;
use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Models\Student;
use App\Models\User;
use App\Support\AIPromptSanitizer;
use Illuminate\Support\Facades\Cache;

/**
 * Orquestación del tutor IA: proveedor desacoplado, caché, auditoría sin prompts completos.
 */
final class AITutorService
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly AcademicRiskAnalysisService $risk,
        private readonly StudentRecommendationService $recommendations,
        private readonly StudentContextService $studentContext,
    ) {}

    public function provider(): AIProviderInterface
    {
        if (! config('ai.tutor_enabled')) {
            return new NullAIProvider;
        }

        return match (config('ai.provider')) {
            'openai' => app(OpenAIProvider::class),
            'ollama' => app(OllamaProvider::class),
            'gemini' => app(GeminiProvider::class),
            'claude' => app(ClaudeProvider::class),
            default => app(OpenAIProvider::class),
        };
    }

    /**
     * @return array{reply: string, success: bool, model: string, error_code: ?string, cached: bool}
     */
    public function studentChat(User $user, Student $student, string $rawMessage): array
    {
        if (! config('ai.tutor_enabled')) {
            $r = AIChatResult::disabled();
            $this->logAiAudit($user, $student, 'student_chat', $r, AIPromptSanitizer::hashPayload('off', (string) $user->id));

            return [
                'reply' => $r->content,
                'success' => $r->success,
                'model' => $r->model,
                'error_code' => $r->errorCode,
                'cached' => false,
            ];
        }

        $maxLen = (int) config('ai.max_user_message_length', 2000);
        $message = AIPromptSanitizer::sanitizeUserMessage($rawMessage, $maxLen);
        $promptHash = AIPromptSanitizer::hashPayload($message, (string) $student->id);

        $cacheKey = $this->chatCacheKey('student_chat', $promptHash, $student->id);
        if ($cached = Cache::get($cacheKey)) {
            $this->audit->log(
                AuditAction::AiQuery,
                AuditModule::Ai,
                $user,
                Student::class,
                $student->id,
                'Consulta IA tutor (caché)',
                null,
                null,
                AuditResult::Success,
                context: [
                    'prompt_sha256' => $promptHash,
                    'cache_hit' => true,
                    'provider' => config('ai.provider'),
                ],
            );

            return [...$cached, 'cached' => true];
        }

        $risk = $this->risk->studentRisk($student);
        $system = (string) config('ai.system_prompts.student_tutor');
        $ctx = $this->studentContext->dashboardStats($student);
        $enrollment = $this->studentContext->currentEnrollmentPayload($student);

        $contextBlock = json_encode([
            'estudiante' => $student->only(['id', 'code']),
            'matricula_resumen' => $enrollment,
            'metricas_portal' => $ctx,
            'riesgo_heuristico' => $risk,
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $messages = [
            ['role' => 'system', 'content' => $system."\n\nContexto académico agregado (JSON): ".$contextBlock],
            ['role' => 'user', 'content' => $message],
        ];

        $result = $this->provider()->chat($messages);
        $payload = [
            'reply' => $result->content,
            'success' => $result->success,
            'model' => $result->model,
            'error_code' => $result->errorCode,
            'cached' => false,
        ];

        $this->logAiAudit($user, $student, 'student_chat', $result, $promptHash);

        if ($result->success) {
            Cache::put($cacheKey, $payload, (int) config('ai.cache_ttl_seconds', 3600));
        }

        return $payload;
    }

    /**
     * @return array{risk: array, recommendations: list<string>, ai_summary: ?string, generated_at: string}
     */
    public function studentInsightBundle(Student $student, bool $forceRefresh = false): array
    {
        $ttl = (int) config('ai.cache_ttl_seconds', 3600);
        $key = $this->insightCacheKey($student->id);

        if (! $forceRefresh && Cache::has($key)) {
            /** @var array{risk: array, recommendations: list<string>, ai_summary: ?string, generated_at: string} */
            return Cache::get($key);
        }

        $risk = $this->risk->studentRisk($student);
        $recs = $this->recommendations->ruleBasedRecommendations($student, $risk);
        $summary = null;

        if (config('ai.tutor_enabled') && $this->openAiConfigured()) {
            $result = $this->provider()->chat([
                ['role' => 'system', 'content' => (string) config('ai.system_prompts.student_tutor')],
                ['role' => 'user', 'content' => 'Genera un resumen motivador breve (máx. 8 líneas) y cierra con una frase de ánimo. Datos: '
                    .json_encode(['riesgo' => $risk, 'recomendaciones_regla' => $recs], JSON_UNESCAPED_UNICODE)],
            ]);
            $summary = $result->success ? $result->content : null;
        }

        $bundle = [
            'risk' => $risk,
            'recommendations' => $recs,
            'ai_summary' => $summary,
            'generated_at' => now()->toIso8601String(),
        ];

        Cache::put($key, $bundle, $ttl);

        return $bundle;
    }

    /**
     * @param  list<array<string, mixed>>  $riskRows
     * @return array{aggregate: array<string, int>, ai_summary: ?string}
     */
    public function teacherSectionInsight(User $teacher, array $riskRows): array
    {
        $aggregate = [
            'total' => count($riskRows),
            'alto' => collect($riskRows)->filter(fn ($r) => ($r['risk']['level'] ?? '') === 'alto')->count(),
            'medio' => collect($riskRows)->filter(fn ($r) => ($r['risk']['level'] ?? '') === 'medio')->count(),
            'bajo' => collect($riskRows)->filter(fn ($r) => ($r['risk']['level'] ?? '') === 'bajo')->count(),
        ];

        $ai = null;
        if (config('ai.tutor_enabled') && $this->openAiConfigured()) {
            $result = $this->provider()->chat([
                ['role' => 'system', 'content' => (string) config('ai.system_prompts.teacher_assistant')],
                ['role' => 'user', 'content' => 'Estrategias pedagógicas breves según datos agregados (sin otros contextos): '
                    .json_encode(['conteo' => $aggregate, 'muestra' => array_slice($riskRows, 0, 10)], JSON_UNESCAPED_UNICODE)],
            ]);
            $ai = $result->success ? $result->content : null;
        }

        return ['aggregate' => $aggregate, 'ai_summary' => $ai];
    }

    /**
     * @return array{overview: array, ai_summary: ?string}
     */
    public function institutionNarrative(array $overview): array
    {
        $ai = null;
        if (config('ai.tutor_enabled') && $this->openAiConfigured()) {
            $result = $this->provider()->chat([
                ['role' => 'system', 'content' => (string) config('ai.system_prompts.institution_analyst')],
                ['role' => 'user', 'content' => 'Tendencias y acciones sugeridas para dirección académica. Datos agregados: '
                    .json_encode($overview, JSON_UNESCAPED_UNICODE)],
            ]);
            $ai = $result->success ? $result->content : null;
        }

        return ['overview' => $overview, 'ai_summary' => $ai];
    }

    public function warmStudentCache(Student $student): void
    {
        $this->studentInsightBundle($student, true);
    }

    public function warmTeacherCache(User $teacher): void
    {
        $rows = $this->risk->studentsAtRiskForTeacher($teacher);
        $ttl = (int) config('ai.cache_ttl_seconds', 3600);
        $payload = $this->teacherSectionInsight($teacher, $rows);
        Cache::put($this->teacherInsightCacheKey($teacher->id), $payload, $ttl);
    }

    public function warmInstitutionCache(): void
    {
        $overview = $this->risk->institutionOverview();
        $ttl = (int) config('ai.cache_ttl_seconds', 3600);
        $payload = $this->institutionNarrative($overview);
        Cache::put($this->institutionInsightCacheKey(), $payload, $ttl);
    }

    public function openAiConfigured(): bool
    {
        $key = config('ai.openai.api_key');

        return is_string($key) && $key !== '';
    }

    public function teacherInsightCacheKey(int $userId): string
    {
        $p = config('ai.cache_keys.prefix', 'ai:');

        return $p.config('ai.cache_keys.teacher_insights', 'insights.teacher').":{$userId}";
    }

    public function institutionInsightCacheKey(): string
    {
        $p = config('ai.cache_keys.prefix', 'ai:');

        return $p.config('ai.cache_keys.institution_insights', 'insights.institution');
    }

    private function insightCacheKey(int $studentId): string
    {
        $p = config('ai.cache_keys.prefix', 'ai:');

        return $p.config('ai.cache_keys.student_insights', 'insights.student').":{$studentId}";
    }

    private function chatCacheKey(string $type, string $hash, int $studentId): string
    {
        $p = config('ai.cache_keys.prefix', 'ai:');

        return "{$p}chat:{$type}:{$studentId}:{$hash}";
    }

    private function logAiAudit(User $user, Student $student, string $operation, AIChatResult $result, string $promptHash): void
    {
        $this->audit->log(
            AuditAction::AiQuery,
            AuditModule::Ai,
            $user,
            Student::class,
            $student->id,
            'Consulta IA tutor estudiante',
            null,
            null,
            $result->success ? AuditResult::Success : AuditResult::Error,
            context: [
                'operation' => $operation,
                'prompt_sha256' => $promptHash,
                'provider' => config('ai.provider'),
                'response_length' => strlen($result->content),
                'model' => $result->model,
                'error_code' => $result->errorCode,
            ],
        );
    }
}
