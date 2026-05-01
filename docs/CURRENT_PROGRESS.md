# Progreso actual del proyecto

Última actualización coherente con el trabajo de **Fase 1** (arquitectura y configuración base).

## Completado — Fase 1

- [x] Estructura de carpetas backend: `Services`, `Actions`, `DTOs`, `Enums`, `Traits`, `Support`.
- [x] Organización de Form Requests: subcarpetas `Http/Requests/Intranet` y `Http/Requests/Public` (además de `Auth` existente).
- [x] Organización frontend: `Pages/Public`, `Pages/Intranet` (páginas Breeze actuales no movidas para no romper rutas).
- [x] Documentación base: `ARCHITECTURE.md`, `SECURITY_POLICY.md`, `TESTING_STRATEGY.md`, `ISO_TRACEABILITY.md`, `CURRENT_PROGRESS.md`.

## No iniciado (según ROADMAP)

- Fase 2: Autenticación avanzada, roles y permisos (Spatie, etc.).
- Fase 3 en adelante: layouts intranet premium, web pública, módulos de negocio, ML, despliegue.

## Notas

- La ruta `/` sigue renderizando `Welcome` en `Pages/Welcome.tsx` hasta la fase de web institucional.
- El login y flujos Breeze **no** se modificaron en Fase 1.
