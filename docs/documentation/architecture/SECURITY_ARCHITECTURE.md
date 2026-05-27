# Arquitectura de seguridad

## Propósito

Proteger datos académicos y personales mediante **autenticación, autorización, auditoría y hardening** de producción.

## Componentes

- **Spatie Permission** — roles: Administrador, Secretaria, Docente, Estudiante, Apoderado.  
- **Policies y Gates** — por modelo y dashboards (`SecurityDashboard`, `AIDashboard`, etc.).  
- **Middleware** — sesión activa, cabeceras HTTP, detección de acceso sospechoso.  
- **Auditoría** — `audit_logs`, `login_attempts`, `user_sessions`.  
- **Auditoría IA** — módulo `ai`, metadatos JSON en `metadata`.

## Flujo de autorización

Request → `auth` → `role:` o Policy → 403 si no autorizado → acción registrada en audit si aplica.

## Tecnologías

Laravel Sanctum/session, Breeze, enums de severidad/resultado en auditoría.

## Decisiones técnicas

- Principio de **mínimo privilegio** por rol.  
- **No almacenar** contraseñas ni API keys en documentación ni logs de aplicación en claro.

## Relación con otros módulos

Transversal a ERP, LMS, CMS, IA e integraciones (webhooks firmados).
