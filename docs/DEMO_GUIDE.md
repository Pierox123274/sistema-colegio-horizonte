# Guía de demostración — I.E.P. Horizonte

Recorrido sugerido para **demo institucional** o piloto. Usuario recomendado: el creado en `DatabaseSeeder` como **Administrador** (revisar credenciales en `.env` / seeder local).

## Preparación (5 min)

1. `php artisan migrate --seed` (entorno limpio de demo).
2. `npm run build` y `php artisan serve`.
3. Opcional: `php artisan schedule:work` para ver recordatorios automáticos.
4. Verificar `APP_URL` y enlace simbólico de storage: `php artisan storage:link`.

## 1. Sitio público (CMS)

| Paso | URL | Qué mostrar |
|------|-----|-------------|
| 1 | `/` | Homepage administrable, hero, secciones |
| 2 | `/noticias` | Noticias publicadas desde CMS |
| 3 | `/galeria` | Galerías e imágenes |
| 4 | `/contacto` | Formulario / datos institucionales |

**Intranet CMS:** `/intranet/cms` — páginas, noticias, medios, homepage (admin).

## 2. Panel administrador (ERP)

| Paso | URL | Qué mostrar |
|------|-----|-------------|
| 1 | `/intranet/dashboard` | KPIs demo + comunicados + notificaciones recientes |
| 2 | `/intranet/students` | Gestión de estudiantes |
| 3 | `/intranet/enrollments` | Matrículas |
| 4 | `/intranet/payments` | Pensiones y pagos, comprobantes |
| 5 | `/intranet/security/audit-logs` | Auditoría |
| 6 | `/intranet/system/health` | Salud del sistema (Fase 27) |

**Accesos rápidos** en el dashboard enlazan a pagos, estudiantes, notificaciones y salud.

## 3. Notificaciones (Fase 28)

| Paso | URL | Qué mostrar |
|------|-----|-------------|
| 1 | Campana en cabecera | Dropdown, marcar leídas |
| 2 | `/notifications` | Centro completo, filtros |
| 3 | `/settings/notifications` | Preferencias por categoría |

Disparadores de demo: publicar comunicado, registrar pago, entrega de tarea (según datos semilla).

## 4. Portal docente

Iniciar sesión con usuario **Docente** (o admin que navegue a):

| Paso | URL | Qué mostrar |
|------|-----|-------------|
| 1 | `/teacher/dashboard` | Resumen docente |
| 2 | `/teacher/attendance` | Asistencia |
| 3 | `/teacher/classrooms` | Aulas virtuales y tareas |
| 4 | `/teacher/pedagogical-panel` | Panel pedagógico / adaptativo |
| 5 | `/teacher/diagnostics` | Exámenes diagnósticos |
| 6 | `/teacher/ai-insights` | IA (si hay API key) |

`/teacher/adaptive-learning` redirige al panel pedagógico (ruta legacy).

## 5. Portal estudiante

Usuario **Estudiante** con registro en tabla `students`:

| Paso | URL | Qué mostrar |
|------|-----|-------------|
| 1 | `/student/dashboard` | Inicio estudiante |
| 2 | `/student/classrooms` | LMS, tareas, exámenes |
| 3 | `/student/gamification` | XP, insignias, ranking |
| 4 | `/student/ai-tutor` | Tutor IA |
| 5 | `/student/diagnostic` | Diagnóstico adaptativo |

## 6. Módulos transversales (admin)

| Módulo | URL |
|--------|-----|
| LMS overview | `/intranet/lms` |
| Gamificación institucional | `/intranet/gamification` |
| Analítica adaptativa | `/intranet/adaptive-analytics` |
| Comunicados | `/intranet/announcements` (si aplica en menú) |

## 7. Secretaría (rol limitado)

Usuario **Secretaria**: estudiantes, matrículas, pagos, CMS — **sin** salud del sistema, gamificación admin ni LMS overview admin.

## Validación técnica post-demo

```bash
php artisan test
npm run build
vendor/bin/pint --dirty
```

Suite QA Fase 29: `tests/Feature/System/PlatformQualityAssuranceTest.php`.

---

Ver también: `docs/KNOWN_LIMITATIONS.md`, `docs/ARCHITECTURE.md`, `ROADMAP.md`.
