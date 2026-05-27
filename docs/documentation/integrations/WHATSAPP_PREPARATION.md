# WhatsApp (preparación)

## Estado

`WhatsAppProvider` stub — **sin API paga activa**.

## Variables (futuro)

```
INTEGRATION_MESSAGING_ENABLED=false
WHATSAPP_ENABLED=false
WHATSAPP_API_TOKEN=
WHATSAPP_PHONE_NUMBER_ID=
```

## Casos de uso previstos

Pagos, asistencia, tareas, comunicados, emergencias (`config/messaging.php` use_cases).

## Fallback

`NullMessagingProvider` — no envía mensajes; registra en log de integraciones.
