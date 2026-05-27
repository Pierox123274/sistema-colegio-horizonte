# Caso de uso — Centro de notificaciones institucionales

## Identificación

| Campo | Valor |
|-------|--------|
| **ID** | UC-NOT-006 |
| **Nombre** | Recibir y gestionar notificaciones del sistema |
| **Módulo** | Notificaciones (Fase 28) |

## Actores

| Tipo | Actor |
|------|--------|
| **Principal** | Cualquier usuario intranet autenticado |
| **Secundarios** | Jobs (`SendAcademicRemindersJob`, etc.), SMTP (`InstitutionMailService`) |

## Objetivo

Centralizar avisos académicos, financieros y del sistema con preferencias por usuario y lectura/no lectura.

## Precondiciones

1. Tablas `user_notifications` y preferencias migradas.
2. Usuario autenticado.

## Flujo principal

1. Un evento de negocio dispara `UserNotificationService::notify(...)`.
2. Se persiste notificación en bandeja del usuario.
3. El usuario abre **Centro de notificaciones** (`/notifications` o panel en layout).
4. Marca como leída o ajusta preferencias (`UserNotificationPreference`).
5. Jobs programados envían recordatorios según categoría/prioridad.

## Flujos alternativos

| ID | Condición | Comportamiento |
|----|-----------|----------------|
| FA-1 | Email habilitado en preferencias | Envío vía cola + SMTP. |
| FA-2 | Push Firebase | **Preparado** — `NullPushProvider` si no hay credenciales. |

## Excepciones

| ID | Excepción | Respuesta |
|----|-----------|-----------|
| EX-1 | SMTP no configurado | Notificación in-app; correo omitido con log. |

## Resultado esperado

Comunicación oportuna sin depender exclusivamente de correo externo informal.

## Evidencia

- `tests/Feature/Notifications/NotificationSystemTest.php`, Cypress `notifications.cy.ts`.
