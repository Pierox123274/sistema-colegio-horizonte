# Pruebas Feature (PHPUnit)

## Ubicación

`tests/Feature/` — ~39 archivos agrupados por dominio.

## Módulos cubiertos (ejemplos)

| Carpeta / archivo | Módulo |
|-------------------|--------|
| `Auth/*` | Login, registro, contraseñas |
| `Intranet/*` | Estudiantes, matrículas, finanzas, inventario |
| `Student/StudentPortalTest.php` | Portal estudiante |
| `Teacher/TeacherPortalTest.php` | Portal docente |
| `LMS/VirtualClassroomTest.php` | Aula virtual |
| `AdaptiveLearning/` | Diagnósticos |
| `AI/` | Tutor y IA avanzada |
| `CMS/` | Gestión sitio web |
| `Security/AuditSecurityTest.php` | Auditoría |
| `Notifications/` | Notificaciones |
| `Meetings/` | Videoclases |
| `Integrations/` | Integraciones externas |
| `Gamification/` | Gamificación |
| `System/` | DevOps, QA, producción |

## Ejemplo de ejecución

```bash
php artisan test --filter=VirtualClassroomTest
php artisan test tests/Feature/AI/
```

## Qué validan

- Códigos HTTP y redirecciones Inertia.  
- Políticas (403 para roles incorrectos).  
- Persistencia en base de datos.  
- Fakes HTTP para OpenAI en tests de IA.

## Resultado esperado

`PASS` con aserciones sobre componentes Inertia (`assertInertia`).
