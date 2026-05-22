# Arquitectura del Tutor IA institucional (Fase 21)

## Objetivo

Proveer un **tutor académico** y **analítica asistida por IA** sin acoplar el dominio a un proveedor concreto, manteniendo **auditoría**, **rate limiting**, **caché** y **sin persistir prompts completos**.

## Capas

| Capa | Ubicación | Rol |
|------|-----------|-----|
| Contrato | `app/AI/Contracts/AIProviderInterface.php` | API mínima (`chat(messages, model?)`) |
| Proveedor OpenAI | `app/AI/Providers/OpenAIProvider.php` | HTTP (`Http::withToken`), timeouts, reintentos, logs sin PII |
| Proveedores reserva | `OllamaProvider`, `GeminiProvider`, `ClaudeProvider` | Stubs documentados para expansión futura |
| Falso / apagado | `NullAIProvider` | Cuando `AI_TUTOR_ENABLED=false` o sin flujo activo |
| Orquestación | `App\Services\AITutorService` | Chat estudiante, resúmenes, caché, auditoría |
| Riesgo / reglas | `AcademicRiskAnalysisService` | Heurísticas locales (notas + asistencia) |
| Recomendaciones regla | `StudentRecommendationService` | Sugerencias pedagógicas sin IA externa |
| Jobs | `Generate*InsightsJob` | Colas: calientan caché estudiante/docente/institución |

## Configuración (`config/ai.php`)

- `AI_PROVIDER` (ej. `openai`).
- `OPENAI_API_KEY`, `OPENAI_MODEL` (por defecto `gpt-4o-mini`).
- `AI_TUTOR_ENABLED`, `AI_CACHE_TTL`, `AI_RATE_LIMIT_PER_MINUTE`.

**Nunca** versionar claves; solo variables de entorno en el servidor.

## Seguridad

- **Throttle** `throttle:ai` por usuario/IP (`AppServiceProvider`).
- **Sanitización** básica en `AIPromptSanitizer` (longitud, patrones de inyección).
- **Auditoría**: acción `ai_query`, módulo `ai`, metadata con `prompt_sha256` (hash), sin texto íntegro del mensaje.
- **Política** `AIPolicy` + `App\Support\AIDashboard`: estudiante/admin tutor; docente/admin insights; **solo administrador** panel institucional.

## Frontend

- Estudiante: `Student/AITutor`, `Student/Recommendations`.
- Docente: `Teacher/AIInsights`, `Teacher/StudentsRisk`.
- Administrador: `Intranet/AIAnalytics/Index`.

## Programación

- `routes/console.php`: `GenerateInstitutionInsightsJob` diario (refresca narrativa institucional en caché).

## Pruebas

- `tests/Feature/AI/AITutorTest.php` (HTTP fake OpenAI, permisos, auditoría).
- BDD: `tests/Bdd/features/ai_tutor.feature`.
- Cypress: `cypress/e2e/ai-tutor.cy.ts` (smoke invitado).

## Relación con aprendizaje adaptativo (Fase 22)

La **Fase 22** implementa diagnósticos, banco de preguntas, recomendaciones por reglas y rutas de aprendizaje usando **solo dominio Laravel y servicios locales** (`AdaptiveDiagnosticService`, `AdaptiveAnalyticsService`, `DiagnosticExamAccessService`). El sistema **funciona con `AI_TUTOR_ENABLED=false`** o sin `OPENAI_API_KEY`: no hay acoplamiento del flujo obligatorio al proveedor de chat.

**Creación de exámenes**: la rendición es solo para estudiantes; la creación y el banco quedan bajo `DiagnosticExamPolicy` / `QuestionBankPolicy` y asignaciones docentes o rol administrador. El portal docente agrupa **Panel pedagógico**, **Diagnósticos** y **Riesgo académico** (este último reutiliza `AIPolicy::useTeacherInsights` para coherencia de acceso con insights IA).

**Separación**: el tutor conversacional (este documento) y el motor adaptativo comparten datos académicos agregados (notas, asistencia, perfiles) pero viven en servicios distintos; la analítica adaptativa institucional incluye `intranet/adaptive-analytics` y el catálogo `intranet/adaptive/*`, independiente del panel `intranet/ai-analytics`.

**Evolución futura (opcional)**: generación automática de ítems o explicaciones vía `AIProviderInterface` puede enriquecer el banco o el feedback al estudiante sin reemplazar el algoritmo base de puntuación y clasificación; cualquier llamada seguiría auditoría `ai_query` y límites `throttle:ai`, no el flujo crítico del examen.

## Relación con aula virtual / LMS (Fase 23)

El módulo LMS (`AssignmentService`, `OnlineExamService`) usa **`LMSAdaptiveIntegrationService`** para actualizar debilidades en `StudentAdaptiveProfile` y crear `LearningRecommendation` cuando una tarea se califica por debajo del umbral (60 %) o cuando un examen online finaliza con puntaje bajo. No depende del tutor conversacional: las reglas son locales, alineadas con la Fase 22.

El **tutor IA** puede consumir en el futuro el mismo contexto (tareas pendientes, resultados de exámenes del aula) vía agregados de `LMSDashboardService` y datos de matrícula; hoy la retroalimentación docente en entregas es texto libre en `AssignmentSubmission.teacher_feedback`.
