# Integración SMTP

## Variables

```
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=
```

## Logs

`MAIL_DELIVERY_LOG_ENABLED=true` → tabla `integration_email_logs`.  
Reintentos: `RetryInstitutionEmailJob`.

## Health

Panel integraciones y `/intranet/system/health`.
