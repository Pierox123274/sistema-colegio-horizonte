# Trazabilidad (audit trail)

## Qué se registra

- Altas/bajas/cambios en entidades sensibles.  
- Consultas IA (`ai_query`) con metadata.  
- Acciones de seguridad y permisos.

## Consulta

Intranet → módulo de auditoría / seguridad (administrador).

## Retención

Comando programado `institution:purge-old-audit-logs` según `DEVOPS_AUDIT_LOG_RETENTION_DAYS`.

## Campos útiles

- `metadata` — JSON (cache_hit, prompt_sha256, acción IA).  
- No incluye contraseñas ni tokens.
