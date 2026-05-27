# Limitaciones conocidas — Sistema Horizonte

Documento de referencia tras **Fase 29** (QA integral). No bloquean demo ni piloto controlado; deben revisarse antes de producción masiva.

## Panel y métricas

- El **dashboard intranet** (`/intranet/dashboard`) muestra tarjetas estadísticas y actividad con **datos de demostración** (`resources/js/data/intranetDashboardDemo.ts`). Los accesos rápidos sí enlazan a módulos reales según rol.
- Algunos reportes analíticos dependen de volumen de datos en BD; en entornos vacíos las vistas pueden mostrar estados vacíos.

## Notificaciones y comunicación

- Canal **push** y **WhatsApp**: arquitectura preparada; no implementados en esta fase.
- Los jobs programados (`SendAcademicRemindersJob`, `SendFinancialRemindersJob`, `SendSystemNotificationsJob`) requieren **scheduler** (`php artisan schedule:work` o cron) en el servidor.
- El dropdown de notificaciones hace **polling ligero** (90 s, solo con panel abierto y pestaña visible); no hay WebSockets.

## IA y adaptativo

- **Tutor IA** y insights docentes requieren configuración OpenAI (`OPENAI_API_KEY`). Sin cuota o clave, se muestran mensajes de fallback.
- El diagnóstico adaptativo no sustituye evaluación formal institucional; es herramienta de apoyo pedagógico.

## LMS y gamificación

- Aulas virtuales y exámenes online están integrados con flujos básicos; no incluyen proctoring avanzado ni integraciones externas (Meet, Teams).
- Gamificación: ranking y retos son **institucionales y saludables**; no hay economía virtual ni recompensas monetarias.

## CMS

- SEO avanzado (sitemap automático, schema.org por página) es parcial; homepage y noticias son administrables.
- Medios grandes deben optimizarse antes de subir; no hay CDN integrado por defecto.

## Seguridad y roles

- Rol **Apoderado** definido en permisos pero sin portal dedicado completo (evolución futura).
- Docente sin rol Administrador es redirigido al portal docente; aún puede tener rutas intranet si conoce la URL — protegidas por middleware por módulo.

## Infraestructura

- Despliegue documentado en Fase 27; backups y health checks requieren configuración del entorno (`.env`, disco, colas).
- Assets en `public/build` deben generarse con `npm run build` en cada release.

## Pruebas

- Cypress E2E de esta fase validan **acceso guest y rutas públicas**; flujos autenticados completos requieren usuario de prueba en `DatabaseSeeder` o comandos de demo.

---

Última revisión: **Fase 29** — Mayo 2026.
