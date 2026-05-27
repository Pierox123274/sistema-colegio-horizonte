# Modelo de auditoría

## Tabla audit_logs

| Columna | Uso |
|---------|-----|
| user_id | Quién |
| action | Enum: create, update, ai_query, … |
| module | Enum: students, ai, lms, … |
| entity_type / entity_id | Recurso afectado |
| old_values / new_values | Cambios JSON |
| result / severity | Éxito, info, warning |
| **metadata** | Contexto adicional (ej. hash prompt IA, cache_hit) |
| ip_address, user_agent | Origen |
| created_at | Cuándo |

## Importante

La IA y integraciones guardan datos sensibles **solo como hash o resumen** en `metadata`, no prompts completos.

## login_attempts

Intentos de acceso para monitoreo de seguridad.

## user_sessions

Sesiones activas para revocación y limpieza programada.
