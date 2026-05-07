# Progreso actual del proyecto

Última actualización: **Fase 9** (conceptos de pago, pensiones y pagos) sobre las fases 1–8.

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

## Completado — Fase 7 (estructura académica)

- [x] Migraciones `educational_levels`, `grades`, `sections`, `classrooms` (FK y unicidades según especificación; `classrooms.section_id` nullable).
- [x] Modelos `EducationalLevel`, `Grade`, `Section`, `Classroom` con relaciones en cascada declarada en migraciones.
- [x] Servicios `EducationalLevelService`, `GradeService`, `SectionService`, `ClassroomService`; Form Requests en `Http/Requests/Intranet`; políticas dedicadas; controladores delgados en `Http/Controllers/Academic`.
- [x] Rutas `intranet.academic.*` ordenadas (literales `create`/`edit` antes de `{model}`); lectura para Administrador/Secretaria/Docente; escritura solo Administrador.
- [x] UI Inertia `Pages/Intranet/Academic/{Levels,Grades,Sections,Classrooms}/` (listados con filtros, estadísticas, badges, migas de pan `IntranetBreadcrumbs`), sidebar con submenú **Gestión académica** (`IntranetNavigation` + `Sidebar` con `children`).
- [x] Seeder `AcademicStructureSeeder` (Inicial 3–5 años, Primaria 1.º–6.º, Secundaria 1.º–5.º; demo sección y aulas); invocado desde `DatabaseSeeder`.
- [x] Pruebas `tests/Feature/Intranet/AcademicStructureTest.php`, `tests/Bdd/features/academic_structure.feature`, `cypress/e2e/academic-structure.cy.ts`.

## Completado — Fase 8 (matrículas)

- [x] Modelos y tablas `AcademicYear`, `Enrollment` (año académico con `year` único; un solo año activo; matrícula con `enrollment_code` único; apoderado opcional; estado `pendiente` / `matriculado` / `anulado` / `retirado`).
- [x] `AcademicYearService` (sincronizar un solo `is_active`), `EnrollmentService` (filtros, reglas de negocio: doble matrícula activa, apoderado vinculado, coherencia nivel–grado–sección–aula, código `MAT-` autogenerado).
- [x] `EnrollmentFormCatalog` (catálogo Inertia para selects encadenados: estudiantes con apoderados, años, niveles, grados, secciones, aulas).
- [x] `EnrollmentController`, `AcademicYearController`, Form Requests, `EnrollmentPolicy`, `AcademicYearPolicy`, factorías, `AcademicYearSeeder` opcional.
- [x] Rutas `intranet.enrollments.*` y `intranet.academic-years.*` (orden: literales `create`/`edit` antes de `{id}` en matrículas). Sin pagos, pensiones ni boletas.
- [x] UI: `Pages/Intranet/Enrollments/*`, `Pages/Intranet/AcademicYears/*`, `Components/Intranet/EnrollmentFormFields`, item **Matrículas** en `IntranetNavigation`.
- [x] Pruebas `tests/Feature/Intranet/EnrollmentManagementTest.php`, `tests/Bdd/features/enrollments.feature`, `cypress/e2e/enrollments.cy.ts`.

## Completado — Fase 9 (finanzas base)

- [x] Tablas `payment_concepts`, `pensions` (unicidad matrícula + mes + año), `payments` (`payment_code` único; vínculos opcionales a estudiante, apoderado, matrícula y pensión).
- [x] Enums `PaymentConceptType`, `PensionStatus`, `PaymentMethod`, `PaymentEntryStatus`; modelos `PaymentConcept`, `Pension`, `Payment` y relaciones en `Student`, `Enrollment`, `Guardian`.
- [x] Servicios `PaymentConceptService`, `PensionService` (saldo pendiente, refresco de estado), `PaymentService` (transacciones, código `PAY-*`, validación de saldo al pagar pensión, resumen financiero por estudiante).
- [x] Controladores `PaymentConceptController`, `PensionController`, `PaymentController`; Form Requests en `Http/Requests/Intranet`; políticas de finanzas solo **Administrador** y **Secretaria** (Docente/Estudiante/Apoderado sin acceso).
- [x] Rutas `intranet.payment-concepts.*`, `intranet.pensions.*`, `intranet.payments.*` (literales `create`, búsqueda y `payments/create` antes de `{payment}`).
- [x] UI Inertia: `Pages/Intranet/PaymentConcepts/*`, `Pages/Intranet/Pensions/*`, `Pages/Intranet/Payments/*` con filtros, badges y formulario de pago con búsqueda de estudiante y resumen de deuda; menú lateral **Finanzas** desplegable (`IntranetNavigation`).
- [x] `PaymentConceptSeeder` opcional; factorías para los tres modelos.
- [x] Pruebas `tests/Feature/Intranet/FinanceManagementTest.php`, `tests/Bdd/features/payments.feature`, `cypress/e2e/payments.cy.ts`.

## Pendiente / siguientes fases (ROADMAP)

- Boleta térmica y PDF de comprobante; inventario y ventas; reportes financieros ampliados; permisos granulares por módulo si aplica.

## Notas

- El **login Breeze** (pantallas y controladores) se mantiene; el destino tras login sigue siendo la ruta nombrada `dashboard` (`/intranet/dashboard`).
- En producción HTTPS: definir `SESSION_SECURE_COOKIE=true` (y revisar `SESSION_SAME_SITE`) según `docs/SECURITY_POLICY.md`.
- Datos demo de estudiantes: `php artisan db:seed --class=StudentDemoSeeder` (opcional).
- Datos demo de apoderados (requiere estudiantes): `php artisan db:seed --class=GuardianDemoSeeder` (opcional).
- Estructura académica demo: `AcademicStructureSeeder` se ejecuta con `php artisan db:seed` (además de roles y usuario de prueba).
