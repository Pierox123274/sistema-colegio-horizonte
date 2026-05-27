# API de integraciones (interna)

No es REST pública; el panel admin consume Inertia:

- `GET /intranet/integrations` — estado proveedores.  
- `POST /intranet/integrations/webhooks/{id}/replay` — reintento manual.

Generadores IA usan JSON POST bajo sesión docente (`/teacher/ai-copilot/*/generate`).

Preparación checkout pagos vía `PaymentIntegrationService` (sin UI pública de cobro obligatoria aún).
