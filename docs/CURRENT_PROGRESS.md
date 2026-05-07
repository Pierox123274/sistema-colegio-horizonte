# Progreso actual del proyecto

Última actualización: **Fase 6** (apoderados y vínculos con estudiantes) sobre las fases 1–5.

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

## Completado — Fase 3 (UI intranet)

- [x] Paleta institucional en Tailwind: `navy`, `brand.red`, `brand.yellow`, `plomo`.
- [x] Componentes reutilizables en `resources/js/Components/Intranet/`: `Card`, `StatsCard`, `PageContainer`, `SectionTitle`, `EmptyState`, `TableContainer`, `Sidebar`, `Header`, `navIcons`.
- [x] `IntranetLayout` con sidebar colapsable (persistencia `localStorage`), overlay móvil, cabecera institucional.
- [x] `IntranetNavigation` (PHP): ítems de menú ERP visuales; `Dashboard`, **Estudiantes**, **Apoderados** (roles con acceso) y `Mi perfil` con enlace real; resto deshabilitado hasta módulos correspondientes.
- [x] `Intranet/Dashboard` con tarjetas estadísticas demo, actividad reciente demo, accesos rápidos y vista previa de tabla (`resources/js/data/intranetDashboardDemo.ts`).
- [x] `Profile/Edit` alineado visualmente con `Card` + `PageContainer`.
- [x] Dependencia `lucide-react` para iconografía.
- [x] Prueba Feature `IntranetDashboardUiTest` (componente Inertia en respuesta).

## Completado — Fase 4 (web pública)

- [x] Rutas públicas limpias: `/`, `/nosotros`, `/niveles`, `/admision`, `/noticias`, `/contacto` (intranet sin cambios base: `/login`, `/intranet/dashboard`, perfil).
- [x] `PublicSiteController` + `PublicLayout` + `resources/js/Components/Public/*` + `Pages/Public/*`.
- [x] Landing con hero, nosotros (resumen), niveles (Inicial / Primaria / Secundaria), propuesta educativa, admisión, noticias demo, CTA y footer.
- [x] Contenido demo en `resources/js/data/publicSiteDemo.ts` (sin BD).
- [x] Pruebas `tests/Feature/Public/PublicSiteTest.php`.
- [x] La página de inicio pública usa `Pages/Public/Home` (no la plantilla Breeze `Welcome`).

## Completado — Fase 5 (estudiantes)

- [x] Modelo `Student`, migración `students`, `StudentFactory`, seeder opcional `StudentDemoSeeder`.
- [x] Enums y catálogo de grados por nivel (`StudentGradeCatalog`).
- [x] `StoreStudentRequest`, `UpdateStudentRequest`, `StudentController`, `StudentService`, `StudentPolicy`.
- [x] Rutas intranet: `/intranet/students`, `/intranet/students/create`, `/intranet/students/{student}`, `/intranet/students/{student}/edit` (store/update con POST/PUT/PATCH).
- [x] UI Inertia: `Pages/Intranet/Students/*`, filtros (búsqueda, nivel, estado), badges, formulario compartido `StudentFormFields`; detalle y listado muestran vínculos con apoderados (pivote).
- [x] Pruebas Feature `tests/Feature/Intranet/StudentManagementTest.php`, escenario `tests/Bdd/features/students.feature`, Cypress base `cypress/e2e/students.cy.ts`.
- [x] Mensajes flash compartidos en `HandleInertiaRequests` para feedback post alta/edición.

## Completado — Fase 6 (apoderados)

- [x] Modelo `Guardian`, tablas `guardians` y pivote `guardian_student` (parentesco por vínculo, responsable económico, contacto principal, prioridad de emergencia, observaciones).
- [x] Enum `GuardianRelationshipType`; relaciones Eloquent `Student::guardians()` y `Guardian::students()`.
- [x] `GuardianController`, `GuardianService` (sync de vínculos, reglas de exclusividad de responsable económico y contacto principal por estudiante), `StoreGuardianRequest`, `UpdateGuardianRequest`, `GuardianPolicy`.
- [x] `GuardianFactory`, seeder opcional `GuardianDemoSeeder`.
- [x] Rutas intranet `intranet.guardians.*` alineadas a roles (misma convención que estudiantes).
- [x] UI Inertia `Pages/Intranet/Guardians/*`, componentes `GuardianFormFields`, `GuardianStudentLinksEditor`, filtros en listado.
- [x] Pruebas `tests/Feature/Intranet/GuardianManagementTest.php`, `tests/Bdd/features/guardians.feature`, `cypress/e2e/guardians.cy.ts`.

## Pendiente / siguientes fases (ROADMAP)

- Matrículas, pagos, inventario, permisos granulares por módulo si aplica.

## Notas

- El **login Breeze** (pantallas y controladores) se mantiene; el destino tras login sigue siendo la ruta nombrada `dashboard` (`/intranet/dashboard`).
- En producción HTTPS: definir `SESSION_SECURE_COOKIE=true` (y revisar `SESSION_SAME_SITE`) según `docs/SECURITY_POLICY.md`.
- Datos demo de estudiantes: `php artisan db:seed --class=StudentDemoSeeder` (opcional).
- Datos demo de apoderados (requiere estudiantes): `php artisan db:seed --class=GuardianDemoSeeder` (opcional).
