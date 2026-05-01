# Progreso actual del proyecto

Última actualización coherente con el trabajo de **Fase 1** (arquitectura y configuración base).

## Completado — Fase 1

- [x] Estructura de carpetas backend: `Services`, `Actions`, `DTOs`, `Enums`, `Traits`, `Support`.
- [x] Organización de Form Requests: subcarpetas `Http/Requests/Intranet` y `Http/Requests/Public` (además de `Auth` existente).
- [x] Organización frontend: `Pages/Public`, `Pages/Intranet` (páginas Breeze actuales no movidas para no romper rutas).
- [x] Documentación base: `ARCHITECTURE.md`, `SECURITY_POLICY.md`, `TESTING_STRATEGY.md`, `ISO_TRACEABILITY.md`, `CURRENT_PROGRESS.md`.

## Completado — Fase 2

- [x] `spatie/laravel-permission` y migraciones de permisos.
- [x] Roles iniciales y `RoleSeeder`; registro público asigna rol **Estudiante**; usuario de demo en `DatabaseSeeder` como **Administrador**.
- [x] Middleware de roles, rutas intranet (`/intranet/dashboard`, perfil bajo el mismo grupo), `UserPolicy` y `AuthorizesRequests` en el `Controller` base.
- [x] `IntranetLayout`, sidebar y dashboard temporal `Intranet/Dashboard`.
- [x] Pruebas Feature (auth + intranet), escenarios Gherkin en `tests/Bdd/features`, base Cypress (`cypress/`, scripts npm).
- [x] `docs/AUTHORIZATION.md`.

## No iniciado (según ROADMAP)

- Fase 2 (base): roles Spatie, intranet protegida, pruebas y Cypress — **completada en su alcance inicial**; permisos granulares y flujos avanzados quedan para fases posteriores.
- Fase 3 en adelante: layouts intranet premium, web pública, módulos de negocio, ML, despliegue.

## Notas

- La ruta `/` sigue renderizando `Welcome` en `Pages/Welcome.tsx` hasta la fase de web institucional.
- El **login Breeze** (pantallas y controladores) se mantiene; el destino tras login sigue siendo la ruta nombrada `dashboard` (`/intranet/dashboard`).
- En producción HTTPS: definir `SESSION_SECURE_COOKIE=true` (y revisar `SESSION_SAME_SITE`) según `docs/SECURITY_POLICY.md`.
