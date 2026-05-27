# Integraciones externas (Fase 32)

Arquitectura desacoplada para conectar Horizonte con servicios reales sin romper módulos existentes.

## Estructura

```
app/Integrations/
  Contracts/          # Interfaces por dominio
  Providers/          # Implementaciones + Null*
  DTO/                # Objetos de transferencia
  Exceptions/
  Services/           # Registry, health, webhooks, mail, calendar, payments
```

## Feature flags (`config/integrations.php`)

| Variable | Descripción |
|----------|-------------|
| `INTEGRATIONS_ENABLED` | Master switch |
| `INTEGRATION_CALENDAR_ENABLED` | Google Calendar (enlaces / futuro OAuth) |
| `INTEGRATION_MESSAGING_ENABLED` | WhatsApp (stub) |
| `INTEGRATION_PAYMENTS_ENABLED` | Pasarela (Mercado Pago / Culqi) |
| `INTEGRATION_PUSH_ENABLED` | Firebase push (stub) |
| `INTEGRATION_EXTERNAL_STORAGE` | Documentar uso S3/R2 |
| `INTEGRATION_WEBHOOKS_ENABLED` | Endpoints `/webhooks/*` |

## Proveedores

| Dominio | Interface | Implementaciones |
|---------|-----------|------------------|
| Calendario | `CalendarProviderInterface` | `GoogleCalendarProvider`, `NullCalendarProvider` |
| Mensajería | `MessagingProviderInterface` | `WhatsAppProvider`, `NullMessagingProvider` |
| Pagos | `PaymentGatewayInterface` | `MercadoPagoProvider`, `CulqiProvider`, `NullPaymentGateway` |
| Push | `PushNotificationProviderInterface` | `FirebasePushProvider`, `NullPushProvider` |
| Videoclases | `MeetingProviderInterface` (existente) | Meet / Zoom / Teams + metadata `api_ready` |

## Webhooks

Rutas públicas (CSRF excluido vía grupo web estándar — usar firma):

- `POST /webhooks/payments`
- `POST /webhooks/mercadopago`
- `POST /webhooks/calendar`

Cabecera: `X-Webhook-Signature` = HMAC-SHA256 del body con secreto de entorno.

Logs: tabla `integration_webhook_logs`. Replay desde `/intranet/integrations`.

## Correo

- `InstitutionMailService`: registro en `integration_email_logs`, reintentos vía `RetryInstitutionEmailJob`.
- Layout premium: `resources/views/emails/layouts/institutional.blade.php`.

## Panel admin

`/intranet/integrations` — tarjetas de proveedor, health, webhooks y correo recientes.

## Almacenamiento externo

Sin cambiar el disco local por defecto. Para producción:

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=...
AWS_BUCKET=...
INTEGRATION_EXTERNAL_STORAGE=true
```

Compatible con R2/Spaces configurando el driver `s3` de Laravel.

## Seguridad

- Secretos solo en `.env` (nunca en UI ni logs completos).
- Payload de webhooks resumido en BD.
- Auditoría módulo `integrations` en sync de calendario y replay.

## Pruebas

- `tests/Feature/Integrations/ExternalIntegrationsTest.php`
- BDD: `external_integrations.feature`
- Cypress: `external-integrations.cy.ts`
