# Arquitectura de flujos y eventos

## Propósito

Documentar **jobs**, **scheduler** y comunicación asíncrona entre módulos.

## Jobs representativos

- Respaldos institucionales  
- Resúmenes diarios por correo  
- Alertas académicas y financieras  
- Escaneo de salud de seguridad  
- Recordatorios de reuniones  
- Notificaciones del sistema  
- Reintentos de correo (`RetryInstitutionEmailJob`)  
- Insights IA en cola (institución)

## Scheduler (`routes/console.php`)

Tareas diarias/periódicas: purga auditoría, respaldos, métricas, alertas, recordatorios meetings cada 15 min.

## Flujo de notificación

Evento de negocio → `UserNotificationService` / Mail → cola → log en `integration_email_logs` si aplica.

## Decisiones

- Cola **no sync** en producción.  
- Heartbeat de scheduler en caché para health check.

## Relación

DevOps (`deployment/QUEUE_AND_SCHEDULER_GUIDE.md`), notificaciones (diagrama dedicado).
