# Base de datos — IA

No hay tabla de “conversaciones” persistentes por defecto (privacidad).

## Trazabilidad

- `audit_logs` con `module = ai`, `action = ai_query`.  
- Campo `metadata`: `prompt_sha256`, `cache_hit`, `provider`, `action` (tipo generación).

## Caché

Respuestas IA en driver de caché Laravel (clave prefijo `ai:`), no en tablas de negocio.
