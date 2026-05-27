# Arquitectura de integraciones externas

## Propósito

Conectar servicios externos con **contratos, fallbacks y configuración por entorno** (Fase 32).

## Componentes (`app/Integrations/`)

| Dominio | Interface | Providers |
|---------|-----------|-----------|
| Calendario | `CalendarProviderInterface` | Google, Null |
| Mensajería | `MessagingProviderInterface` | WhatsApp (stub), Null |
| Pagos | `PaymentGatewayInterface` | Mercado Pago, Culqi, Null |
| Push | `PushNotificationProviderInterface` | Firebase (stub), Null |

Servicios: `IntegrationRegistry`, `IntegrationHealthService`, `WebhookService`, `CalendarIntegrationService`, `InstitutionMailService`.

## Flujo webhook

`POST /webhooks/*` → validación firma HMAC (si hay secreto) → log en `integration_webhook_logs` → parseo por proveedor.

## Estado actual

- **Operativo:** enlaces calendario, logs SMTP, panel `/intranet/integrations`.  
- **Preparado:** cobro real, WhatsApp API, push completo, OAuth Calendar bidireccional.

## Relación

- **Meetings** — metadata `api_ready` en videoclases.  
- Referencia: `docs/INTEGRATIONS.md`.
