# Caso de uso — Docente con copiloto y herramientas IA

## Identificación

| Campo | Valor |
|-------|--------|
| **ID** | UC-AI-002 |
| **Nombre** | Generar apoyo pedagógico con copiloto IA |
| **Módulo** | IA institucional (Fase 31) |

## Actores

| Tipo | Actor |
|------|--------|
| **Principal** | Docente (`IntranetRole::Docente`) |
| **Secundarios** | Proveedor IA (OpenAI/Ollama/Gemini vía `AIProviderInterface`), Administrador (analítica agregada) |

## Objetivo

Asistir al docente en la creación de rúbricas, actividades y retroalimentación pedagógica mediante el copiloto IA, con trazabilidad en auditoría y límites de tasa (`throttle:ai`).

## Precondiciones

1. Usuario con rol Docente autenticado.
2. Variable `AI_PROVIDER` configurada; si no hay clave API, el sistema usa `NullAIProvider` (modo degradado).
3. Acceso a rutas `/teacher/ai-copilot/*`.

## Flujo principal

1. El docente abre **Copiloto IA** (`/teacher/ai-copilot`).
2. Selecciona una herramienta (rúbricas, actividades, etc.).
3. Completa el formulario contextual (curso, tema, criterios).
4. El sistema invoca `TeacherAICopilotService` / `AIGenerationService` → proveedor configurado.
5. La respuesta se muestra en UI (`AIStreamingCard` o bloque de resultado).
6. Se registra evento en `audit_logs` (módulo IA, metadata de contexto).

## Flujos alternativos

| ID | Condición | Comportamiento |
|----|-----------|----------------|
| FA-1 | Sin API key | Mensaje institucional; contenido de ejemplo o vacío según `NullAIProvider`. |
| FA-2 | Proveedor Ollama local | Respuesta vía `OllamaProvider` si el servicio local está activo. |
| FA-3 | Límite de throttle excedido | HTTP 429; reintento posterior. |

## Excepciones

| ID | Excepción | Respuesta |
|----|-----------|-----------|
| EX-1 | Timeout del proveedor | Error amigable; no se persiste borrador automático salvo que el usuario guarde manualmente. |
| EX-2 | Rol no autorizado | 403 en middleware `role:Docente`. |

## Postcondiciones

- Registro de auditoría con resultado éxito/fallo.
- El docente puede copiar o adaptar el texto generado en su planificación.
- **Resultado esperado:** reducción de tiempo en diseño instruccional con supervisión humana obligatoria.

## Evidencia técnica

- `tests/Feature/AI/AdvancedAIFeaturesTest.php`, `tests/Bdd/features/advanced_ai.feature`, Cypress `advanced-ai.cy.ts`.
