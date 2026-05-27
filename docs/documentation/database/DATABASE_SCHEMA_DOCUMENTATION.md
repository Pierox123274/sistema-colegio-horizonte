# Esquema de base de datos — Visión general

## Motor

- **Producción:** MySQL 8+.  
- **Tests:** SQLite en memoria o archivo.

## Organización

43+ migraciones en `database/migrations/` ordenadas cronológicamente.

## Dominios

| Dominio | Tablas representativas |
|---------|------------------------|
| Identidad | users, roles (Spatie), permissions |
| Académico | students, guardians, enrollments, sections, grades |
| Evaluación | subjects, evaluations, grade_records, attendances |
| Finanzas | payment_concepts, pensions, payments |
| Comercial | products, sales, cash_registers |
| LMS | virtual_classrooms, assignments, online_exams |
| Adaptive | question_banks, diagnostic_exams, diagnostic_attempts |
| CMS | cms_pages, cms_news, cms_media, … |
| Seguridad | audit_logs, login_attempts, user_sessions |
| Ops | jobs, failed_jobs, cache |
| Notificaciones | user_notifications, preferences |
| Gamificación | student_gamification_profiles, achievements, … |
| Meetings | virtual_meetings, meeting_participants, … |
| Integraciones | integration_webhook_logs, integration_email_logs |

## Convenciones

- `id` BIGINT PK.  
- `created_at` / timestamps según tabla.  
- JSON para metadatos flexibles.
