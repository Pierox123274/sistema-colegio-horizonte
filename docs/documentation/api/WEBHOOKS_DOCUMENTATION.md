# Webhooks

| Método | Ruta | Propósito |
|--------|------|-----------|
| POST | `/webhooks/payments` | Pagos genérico |
| POST | `/webhooks/mercadopago` | Mercado Pago |
| POST | `/webhooks/calendar` | Eventos calendario |

## Seguridad

Cabecera `X-Webhook-Signature`: HMAC-SHA256 del body con secreto en `.env`.

Variables: `INTEGRATION_WEBHOOK_PAYMENT_SECRET`, `MERCADOPAGO_WEBHOOK_SECRET`, etc.

## Logs

Tabla `integration_webhook_logs`; replay desde panel admin.
