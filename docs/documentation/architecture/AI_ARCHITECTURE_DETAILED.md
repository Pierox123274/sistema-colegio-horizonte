# Arquitectura IA — Detalle institucional

## Propósito

Asistencia pedagógica **opcional y controlada**: tutor estudiante, insights docente, analítica institucional y copiloto generativo (Fase 31).

## Componentes

| Componente | Rol |
|------------|-----|
| `AIProviderInterface` | Contrato de chat/completión |
| `OpenAIProvider` | Implementación HTTP |
| `NullAIProvider` | Fallback sin API |
| `AITutorService` | Chat, caché, auditoría (hash prompt) |
| `AIGenerationService` | JSON estructurado (exámenes, tareas, rúbricas) |
| `TeacherAICopilotService` | Copiloto docente + exports |
| `StudentLearningCoachService` | Resumen, mini quiz, práctica |
| `AcademicRiskAnalysisService` | Riesgo heurístico (reglas locales) |
| `AdvancedAIAnalyticsService` | Métricas desde `audit_logs.metadata` |

## Flujo estudiante

`/student/ai-tutor` → throttle `ai` → contexto académico minimizado → proveedor → respuesta + gamificación opcional.

## Flujo docente

`/teacher/ai-copilot/*` → generadores → revisión humana → export a banco de preguntas o tarea LMS.

## Seguridad

- `AI_TUTOR_ENABLED` y flags por módulo en `config/ai.php`.  
- Sin diagnósticos médicos en prompts institucionales.  
- Auditoría: acción `ai_query`, metadatos en columna `metadata` (no prompt completo).

## Relación con otros módulos

- **Adaptive** — export de preguntas a `QuestionBank`.  
- **LMS** — export de tareas a `Assignment`.  
- Documento técnico complementario: `docs/AI_ARCHITECTURE.md` (repositorio).
