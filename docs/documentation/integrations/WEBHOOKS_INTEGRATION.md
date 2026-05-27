# Webhooks

## Rutas

`POST /webhooks/payments`, `/mercadopago`, `/calendar`

## Firma

`INTEGRATION_WEBHOOK_PAYMENT_SECRET` + cabecera `X-Webhook-Signature`.

## Administración

Logs en panel integraciones; replay limitado por `INTEGRATION_WEBHOOK_MAX_REPLAY`.
