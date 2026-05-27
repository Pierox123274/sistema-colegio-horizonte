# Arquitectura de base de datos

## Propósito

Modelo relacional **MySQL** (SQLite en tests) con migraciones versionadas en Laravel.

## Principios

- Claves foráneas y índices en tablas transaccionales.  
- JSON para metadatos flexibles (`audit_logs.metadata`, `provider_metadata`).  
- Soft deletes donde el dominio lo requiere.  
- Factories para pruebas.

## Dominios de tablas

| Dominio | Prefijo / grupo |
|---------|------------------|
| Core | users, students, guardians, enrollments |
| Académico | subjects, evaluations, grade_records, attendances |
| Finanzas | payments, pensions, cash_registers, sales |
| LMS | virtual_classrooms, assignments, online_exams |
| Adaptive | question_banks, diagnostic_exams, attempts |
| CMS | cms_* |
| Seguridad | audit_logs, login_attempts, user_sessions |
| Notificaciones | user_notifications* |
| Gamificación | gamification_* |
| Meetings | virtual_meetings* |
| Integraciones | integration_webhook_logs, integration_email_logs |

## Relación

Diagrama conceptual en `diagrams/DATABASE_RELATIONSHIP_DIAGRAM.md` y detalle en `database/`.
