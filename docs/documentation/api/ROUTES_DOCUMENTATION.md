# Documentación de rutas

## Archivos

- `routes/web.php` — aplicación principal (público, intranet, teacher, student).  
- `routes/webhooks.php` — callbacks externos.  
- `routes/console.php` — scheduler.

## Prefijos principales

| Prefijo | Audiencia |
|---------|-----------|
| `/` | Sitio público |
| `/login`, `/register` | Auth Breeze |
| `/intranet` | Admin / secretaría |
| `/teacher` | Docente |
| `/student` | Estudiante |
| `/webhooks` | Sistemas externos |

## Middleware común

`auth`, `verified`, `role:…`, `throttle:ai`, políticas por recurso.

## Seguridad

Rutas sensibles solo con rol explícito; webhooks con firma opcional.
