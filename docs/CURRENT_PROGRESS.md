# Progreso actual del proyecto

Última actualización: **Fase 27** (Preparación producción, deploy real y hardening final).

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

## Completado — Fase 10 (comprobantes de pago)

- [x] Dependencia PDF instalada: `barryvdh/laravel-dompdf`.
- [x] `PaymentReceiptController` + `PaymentReceiptService` para comprobante HTML, PDF y ticket térmico.
- [x] Rutas creadas:
  - `/intranet/payments/{payment}/receipt`
  - `/intranet/payments/{payment}/receipt/pdf`
  - `/intranet/payments/{payment}/receipt/ticket`
- [x] Vistas Blade de comprobante en `resources/views/intranet/payments/`:
  - `receipt.blade.php`
  - `receipt-pdf.blade.php`
  - `receipt-ticket.blade.php` (58mm/80mm, `@media print`).
- [x] Datos institucionales configurables por `config/institution.php` (nombre, identificador demo, dirección demo, mensaje institucional y QR demo opcional).
- [x] Trazabilidad de usuario registrador: `payments.created_by_user_id`.
- [x] Botones en detalle de pago (`Pages/Intranet/Payments/Show.tsx`): Ver comprobante, Descargar PDF, Imprimir ticket.
- [x] Pruebas fase 10: `tests/Feature/Intranet/PaymentReceiptTest.php`, `tests/Bdd/features/payment_receipts.feature`, `cypress/e2e/payment-receipts.cy.ts`.

## Completado — Fase 11 (inventario)

- [x] Modelos y tablas: `product_categories`, `products`, `inventory_movements`.
- [x] Reglas de negocio: códigos únicos en categorías/productos; precios y stocks >= 0; cantidad de movimiento > 0; bloqueo de stock negativo.
- [x] Recalculo automático de stock por movimiento (`entrada`, `salida`, `ajuste`) con transacción y bloqueo pesimista (`lockForUpdate`).
- [x] Trazabilidad en movimiento: `previous_stock`, `new_stock`, `reason`, `observations`, `created_by_user_id`.
- [x] Backend: controladores `ProductCategoryController`, `ProductController`, `InventoryMovementController`; servicios `ProductCategoryService`, `ProductService`, `InventoryMovementService`; Form Requests y policies por recurso.
- [x] Sidebar desplegable **Inventario** con hijos **Categorías**, **Productos**, **Movimientos**.
- [x] Frontend Inertia:
  - `Pages/Intranet/Inventory/Categories/*`
  - `Pages/Intranet/Inventory/Products/*`
  - `Pages/Intranet/Inventory/Movements/*`
- [x] UI con filtros, badges, cards de estadísticas y alertas de stock bajo.
- [x] Seed demo: `InventoryDemoSeeder` integrado en `DatabaseSeeder`.
- [x] Pruebas fase 11: `tests/Feature/Intranet/InventoryManagementTest.php`, `tests/Bdd/features/inventory.feature`, `cypress/e2e/inventory.cy.ts`.

## Completado — Fase 12 (caja y ventas)

- [x] Tablas y modelos: `cash_registers`, `sales`, `sale_items`, `cash_movements` con trazabilidad de usuario, fecha y estado.
- [x] Reglas de negocio:
  - caja abierta obligatoria para vender;
  - no permitir dos cajas abiertas por usuario en el mismo día;
  - no vender productos inactivos ni sin stock;
  - anulación de venta devuelve stock y genera contramovimiento de caja.
- [x] Backend: `CashRegisterController`, `SaleController`, `CashMovementController`, `SaleReceiptController`; servicios `CashRegisterService`, `SaleService`, `CashMovementService`, `SaleReceiptService`; requests de apertura/cierre y registro de venta.
- [x] Frontend Inertia:
  - `Pages/Intranet/Sales/CashRegisters/Index.tsx`
  - `Pages/Intranet/Sales/Sales/{Index,Create,Show}.tsx`
  - `Pages/Intranet/Sales/CashMovements/Index.tsx`
- [x] Comprobante de venta HTML y PDF: `resources/views/intranet/sales/receipt*.blade.php`.
- [x] Sidebar desplegable **Caja y ventas** con hijos **Caja diaria**, **Ventas**, **Nueva venta**, **Movimientos**.
- [x] Pruebas fase 12: `tests/Feature/Intranet/CashSalesManagementTest.php`.
- [x] Ajustes Fase 12:
  - buscador profesional de estudiante en nueva venta (código/nombres/apellidos/documento) con vista previa y apoderados vinculados;
  - registro de venta corregido para `student_id`/`guardian_id` nullable y validación de pertenencia del apoderado;
  - exportación de ventas por filtros a PDF y CSV compatible con Excel desde listado;
  - corrección de hora local en formulario `datetime-local` y timezone de app por `APP_TIMEZONE`.

## Completado — Fase 13 (asistencia académica)

- [x] Modelo y tabla `attendances` con trazabilidad completa (`recorded_by_user_id`) y unicidad por `student_id + attendance_date + section_id`.
- [x] Enum `AttendanceStatus`: `presente`, `tarde`, `falta`, `justificado`.
- [x] Backend: `AttendanceController`, `AttendanceService`, `StoreAttendanceBatchRequest`, `AttendancePolicy`.
- [x] Registro masivo por sección y fecha con validación de estudiantes matriculados (integrado con matrículas/año académico).
- [x] Consulta histórica por estudiante y listado filtrado por fecha, rango, sección, estudiante y estado.
- [x] Métricas: porcentaje de asistencia, tardanzas, faltas y justificados.
- [x] Exportación de asistencia en PDF y CSV compatible con Excel.
- [x] Sidebar desplegable **Asistencia** con accesos a registrar, historial y reportes.
- [x] Pruebas fase 13: `tests/Feature/Intranet/AttendanceManagementTest.php`, `tests/Bdd/features/attendance.feature`, `cypress/e2e/attendance.cy.ts`.

## Completado — Fase 15 (portal docente)

- [x] Rutas `/teacher/dashboard`, `/teacher/attendance`, `/teacher/grades`, `/teacher/students`, `/teacher/reports` con middleware `role:Docente|Administrador` (Secretaria, Estudiante y Apoderado excluidos).
- [x] Controladores `Teacher*Controller` delgados; reutilización de `StudentService`, políticas de `Attendance` y `GradeRecord`, y enlaces al ERP para registro masivo y exportaciones.
- [x] `TeacherNavigation` + prop compartida `teacherNav`; ítem **Portal docente** en `IntranetNavigation` para docente y administrador.
- [x] Frontend: `Layouts/TeacherLayout.tsx`, páginas `Pages/Teacher/**` (dashboard con tarjetas, listados y accesos rápidos).
- [x] Pruebas: `tests/Feature/Teacher/TeacherPortalTest.php`, `tests/Bdd/features/teacher_portal.feature`, `cypress/e2e/teacher-portal.cy.ts`.

## Completado — Fase 15.1 (usuarios y asignaciones docentes)

- [x] Campo `users.is_active`; login rechaza cuentas inactivas (`LoginRequest`).
- [x] Modelo `TeacherAssignment` y migración; política `TeacherAssignmentPolicy`; rutas `intranet.admin.*` solo **Administrador**.
- [x] `AdminUserController` (listado con filtros, alta, edición), `TeacherAssignmentController`; Form Requests `StoreIntranetUserRequest`, `UpdateIntranetUserRequest`, `StoreTeacherAssignmentRequest`, `UpdateTeacherAssignmentRequest`.
- [x] `UserPolicy` extendida (gestión de usuarios solo administrador; perfil sigue permitiendo auto-actualización); `StudentPolicy` y `StudentService` limitan al **docente sin admin/secretaría** a estudiantes matriculados en sus secciones del año activo según asignaciones.
- [x] `TeacherContextService` para secciones activas, resumen de dashboard docente y autorización de ficha estudiante.
- [x] Portal docente: dashboard, estudiantes, asistencia y notas filtrados por secciones asignadas; avisos si no hay asignaciones.
- [x] Sidebar intranet: grupo **Administración** (Usuarios, Asignaciones docentes); iconos `shield` y `user-cog` en `navIcons`.
- [x] UI Inertia: `Pages/Intranet/Admin/Users/*`, `Pages/Intranet/Admin/TeacherAssignments/*`.
- [x] Pruebas: `tests/Feature/Intranet/AdminUserAndTeacherAssignmentTest.php`; ajuste en `StudentManagementTest` (detalle con administrador).

## Completado — Fase 14 (calificaciones y evaluaciones académicas)

- [x] Modelos y tablas: `subjects`, `evaluations`, `grade_records` con relaciones a estudiantes, estructura académica y año académico.
- [x] Backend: `SubjectController`, `EvaluationController`, `AcademicGradeController`; servicios `SubjectService`, `EvaluationService`, `AcademicGradeService`; Form Requests y Policies.
- [x] Registro masivo de notas por evaluación cargando únicamente estudiantes con matrícula activa en sección/año de la evaluación.
- [x] Reglas: nota entre 0 y 20; unicidad por `evaluation_id + student_id`; actualización de nota existente sin duplicar.
- [x] Promedios y analítica: promedio por curso, promedio general, ranking y lista de riesgo académico.
- [x] Historial académico por estudiante y exportación de reportes PDF/CSV compatible con Excel.
- [x] Frontend Inertia:
  - `Pages/Intranet/Academic/Subjects/*`
  - `Pages/Intranet/Academic/Evaluations/*`
  - `Pages/Intranet/Academic/Grades/{RecordsIndex,StudentHistory}.tsx`
- [x] Sidebar en gestión académica: Cursos, Evaluaciones, Registro de notas, Historial académico, Reportes académicos.
- [x] Pruebas fase 14: `tests/Feature/Intranet/AcademicGradesManagementTest.php`, `tests/Bdd/features/academic_grades.feature`, `cypress/e2e/academic-grades.cy.ts`.

## Completado — Fase 16 (portal estudiante)

- [x] Vinculación `students.user_id` (nullable, único) y relaciones `User` ↔ `Student`.
- [x] Rutas `/student/dashboard`, `/student/grades`, `/student/attendance`, `/student/payments`, `/student/profile` con middleware `role:Estudiante|Administrador`.
- [x] `StudentContextService`, `StudentNavigation`, controladores `Student*Controller`, `StudentLayout` y páginas `Pages/Student/**` (solo lectura).
- [x] Redirección post-login y desde `/intranet/dashboard` para estudiante sin administración → portal estudiante (`AuthRedirect`).
- [x] Seeder demo `StudentPortalDemoSeeder` (`estudiante@demo.com` / `password`).
- [x] Pruebas: `tests/Feature/Student/StudentPortalTest.php`; ajustes en `AuthenticationTest` e `IntranetAuthorizationTest`.

## Completado — Fase 17 (comunicados y notificaciones)

- [x] Tablas `announcements`, `announcement_recipients`, `announcement_reads`; enums `AnnouncementPriority`, `AnnouncementAudienceType`.
- [x] `AnnouncementService` (CRUD admin, visibilidad por rol/destinatarios, ventana `starts_at`/`ends_at`, lecturas, adjuntos en `storage/app/public/announcements`, campana en cabecera).
- [x] `AnnouncementPolicy` (gestión solo Administrador; lectura según audiencia).
- [x] Rutas: admin `intranet/announcements/*`; lectura docente `teacher/announcements/*`; estudiante `student/announcements/*`; bandeja secretaría/apoderado `intranet/announcements/inbox/*`.
- [x] UI: componentes `Components/Announcements/*`, páginas admin/inbox/portales, `NotificationBell` en cabecera, panel `RecentAnnouncementsPanel` en dashboards.
- [x] Pruebas: `tests/Feature/Announcements/AnnouncementManagementTest.php` (8 casos).

## Completado — Fase 18 (reportes y dashboard analítico)

- [x] Servicios `AnalyticsService`, `AcademicAnalyticsService`, `FinancialAnalyticsService`, `InventoryAnalyticsService` (reutilizan datos de módulos existentes).
- [x] `AnalyticsPolicy` y marcador `AnalyticsDashboard`; rutas admin `intranet/analytics`, `intranet/reports/analytics/*`; docente `teacher/analytics`.
- [x] Dashboard ejecutivo (académico, financiero, inventario/ventas, usuarios, comunicados) con permisos por rol.
- [x] Portal docente analítico (secciones asignadas).
- [x] Gráficos Recharts (barras, líneas, donut) + KPI cards + filtros año/fechas.
- [x] Exportación PDF/CSV por subreporte.
- [x] Pruebas `tests/Feature/Intranet/AnalyticsDashboardTest.php`, `tests/Bdd/features/analytics_dashboard.feature`, `cypress/e2e/analytics-dashboard.cy.ts`.

## Completado — Fase 19 (seguridad avanzada, auditoría e ISO)

- [x] Tablas `audit_logs`, `login_attempts`, `user_sessions`; enums `AuditAction`, `AuditModule`, `AuditResult`, `AuditSeverity`; `config/security.php`.
- [x] Servicios `AuditService`, `SecurityService`, `SessionSecurityService`.
- [x] Middleware `LogUserActivity`, `VerifyActiveSession`, `PreventSuspiciousAccess` (registro global en `bootstrap/app.php`).
- [x] Integración auth: intentos de login, bloqueo temporal, auditoría login/logout, registro de sesión, invalidación al cambiar contraseña.
- [x] `SecurityPolicy` + marcador `SecurityDashboard`; rutas `intranet/security/*` (auditoría, sesiones, intentos, monitoreo).
- [x] UI Inertia `Pages/Intranet/Security/*`, componentes `Components/Security/*`, menú **Seguridad y auditoría** en `IntranetNavigation`.
- [x] Auditoría explícita en usuarios admin y exportaciones analíticas; trazabilidad ISO (quién, cuándo, desde dónde, qué cambió).
- [x] Pruebas `tests/Feature/Security/AuditSecurityTest.php`, `tests/Bdd/features/security_audit.feature`, `cypress/e2e/security-audit.cy.ts`.

## Completado — Fase 20 (DevOps institucional, automatización y producción)

- [x] Programación en `routes/console.php` (limpieza auditoría/sesiones, respaldos, alertas, métricas snapshot).
- [x] Comandos `institution:*` en `app/Console/Commands/`; servicios `InstitutionBackupService`, `SystemHealthService`; `config/devops.php`.
- [x] Jobs en cola: respaldo, resumen diario, alertas académicas/financieras, escaneo seguridad, snapshot métricas (IA futura), notificación de comunicado, bienvenida usuario (opcional).
- [x] Mailables y vistas `resources/views/emails/*`; notificación `InstitutionOperationalAlertNotification`.
- [x] Panel admin `intranet/system/health`, `jobs`, `backups` + `SystemOperationsPolicy` / `SystemOperationsDashboard`; entradas en `IntranetNavigation`.
- [x] Docker (`Dockerfile`, `docker-compose.yml`, `docker/nginx.conf`); CI `.github/workflows/ci.yml` (Pint, PHPUnit, npm build).
- [x] Documentación `DEPLOYMENT.md`, `DEVOPS.md`; variables `DEVOPS_*` en `.env.example`.
- [x] Pruebas `tests/Feature/System/DevOpsInfrastructureTest.php`, BDD `devops_infrastructure.feature`, Cypress `devops-infrastructure.cy.ts`.

## Completado — Fase 21 (Tutor IA institucional)

- [x] Arquitectura desacoplada `app/AI/` (contrato, `OpenAIProvider`, stubs Ollama/Gemini/Claude, `NullAIProvider`); `config/ai.php` y variables `AI_*` / `OPENAI_*` en `.env.example`.
- [x] Servicios `AITutorService`, `AcademicRiskAnalysisService`, `StudentRecommendationService`; sanitización `AIPromptSanitizer`; auditoría módulo `ai` / acción `ai_query` (hash, sin prompts completos).
- [x] Rutas con `throttle:ai`: portal estudiante (`/student/ai-tutor`, `/student/recommendations`), docente (`/teacher/ai-insights`, `/teacher/students-risk`), administrador (`/intranet/ai-analytics`); `AIPolicy` + `AIDashboard`.
- [x] Jobs `GenerateStudentInsightsJob`, `GenerateTeacherInsightsJob`, `GenerateInstitutionInsightsJob`; programación diaria de insights institucionales en `routes/console.php`.
- [x] UI Inertia React/TS: `Student/AITutor`, `Student/Recommendations`, `Teacher/AIInsights`, `Teacher/StudentsRisk`, `Intranet/AIAnalytics/Index`; navegación en `StudentNavigation`, `TeacherNavigation`, `IntranetNavigation`.
- [x] Documentación `docs/AI_ARCHITECTURE.md`; actualización de arquitectura y trazabilidad.
- [x] Pruebas `tests/Feature/AI/AITutorTest.php`, BDD `ai_tutor.feature`, Cypress `ai-tutor.cy.ts`.

## Completado — Fase 22 (Diagnóstico adaptativo y nivelación inteligente)

- [x] Dominio: `QuestionBank`, `QuestionOption`, `DiagnosticExam` (alcance año/sección/grado/nivel, umbrales, `created_by_user_id`), `DiagnosticAttempt`, `LearningRecommendation`, `StudentAdaptiveProfile`; migración de alcance `add_scope_and_thresholds_to_diagnostic_exams`.
- [x] Servicios: `AdaptiveDiagnosticService`, `AdaptiveAnalyticsService`, `DiagnosticExamAccessService` (visibilidad estudiante por matrícula; docente por `TeacherAssignment`); clasificación por umbrales del examen.
- [x] Políticas: `DiagnosticExamPolicy`, `QuestionBankPolicy` (registro explícito en `AppServiceProvider`); `DiagnosticAttemptPolicy` para intentos; `AdaptiveLearningPolicy` + dashboard legacy si aplica.
- [x] Rutas estudiante: `student/diagnostic*` (autorización `take`), `student/learning-path`.
- [x] Rutas docente: `teacher/pedagogical-panel`, `teacher/diagnostics` (+ `create`, `{exam}`, `{exam}/results`), `teacher/academic-risk`; redirecciones `teacher/adaptive-learning` → panel pedagógico, `teacher/diagnostic-results` → listado de diagnósticos; menú lateral agrupado (Académico, Inteligencia académica, Comunicación).
- [x] Rutas intranet: `intranet/adaptive/diagnostic-exams*`, `intranet/adaptive/questions`, `intranet/adaptive/results` (rol Administrador|Secretaría; escritura vía políticas); submenú **Aprendizaje adaptativo** en administración.
- [x] UI Inertia: `Student/Diagnostic/*`, `Student/LearningPath`, `Teacher/PedagogicalPanel`, `Teacher/Diagnostics/*`, `Teacher/AcademicRisk`, `Intranet/Adaptive/DiagnosticExams/*`, `Intranet/Adaptive/Questions/Index`, `Intranet/Adaptive/Results/Index`, `Intranet/AdaptiveAnalytics/Index`.
- [x] Pruebas ampliadas: `tests/Feature/AdaptiveLearning/AdaptiveLearningTest.php` (redirecciones docente, secretaría índice intranet, docente no crea fuera de sección, estudiante no ve examen ajeno a matrícula); BDD/Cypress existentes de la fase.

## Completado — Fase 23 (Aula virtual, tareas y evaluaciones online)

- [x] Migración `2026_05_20_100000_create_lms_tables` (aulas, anuncios, recursos, tareas, entregas, exámenes, preguntas, intentos, calendario académico).
- [x] Modelos y enums LMS; factories `VirtualClassroomFactory`, `AssignmentFactory`, `OnlineExamFactory`.
- [x] Servicios: `LMSService`, `AssignmentService`, `OnlineExamService`, `LMSCalendarService`, `LMSDashboardService`, `VirtualClassroomAccessService`, `LMSAdaptiveIntegrationService` (recomendaciones/debilidades tras tareas y exámenes).
- [x] Políticas LMS + `AuditModule::Lms`; controladores docente/estudiante/admin e intranet `intranet/lms`.
- [x] Rutas: `teacher/classrooms*`, `teacher/calendar`, `student/classrooms*`, `student/calendar`, examen en línea con intentos.
- [x] UI Inertia: `Teacher/Classrooms/*`, `Teacher/Calendar/Index`, `Student/Classrooms/*`, `Student/Calendar/Index`, `Intranet/LMS/Overview`; KPI `lms` en dashboards; menú intranet **Aula virtual (LMS)**.
- [x] Pruebas: `tests/Feature/LMS/VirtualClassroomTest.php`, BDD `virtual_classroom.feature`, Cypress `virtual-classroom.cy.ts`.

## Completado — Fase UX/UI (Landing pública institucional)

- [x] Landing `Public/Home` con enfoque **colegio real** (referencia conceptual tipo Andino): identidad, comunidad, admisión, vida escolar, niveles, infraestructura, noticias.
- [x] Hero institucional (mensaje de valores, imagen campus, CTAs admisión / conocer más) — sin dashboards ni KPIs tecnológicos al inicio.
- [x] Secciones: estadísticas institucionales, historia/misión/visión/valores, propuesta educativa, niveles, vida escolar, logros, infraestructura, testimonios, noticias.
- [x] **Innovación educativa** al final (aula virtual, tutor IA, adaptativo, analítica) como complemento pedagógico; enlace al portal separado del menú principal.
- [x] `PublicNavbar`: Inicio, Nosotros, Niveles, Admisión, Vida escolar, Noticias, Contacto + botón **Portal** (login) visualmente distinto.
- [x] Dark/light (`PublicThemeProvider`), animaciones suaves (Framer Motion), responsive, glassmorphism sutil.
- [x] Componentes `Components/Public/Institutional/*` + reutilización de secciones Fase 4; sin cambios en backend.

## Completado — Fase 24 (CMS institucional premium)

- [x] Migración `cms_*` (páginas, secciones, hero slides, noticias, categorías, galerías, testimonios, settings, menús, media).
- [x] Modelos `App\Models\Cms\*`, enums `CmsPublicationStatus`, `CmsMenuLocation`, políticas y auditoría (`AuditModule::Cms`).
- [x] Servicios `CmsSettingService`, `CmsPublicService` (caché), `CmsContentService`, `CmsMediaService`; presentador `CmsPublicPresenter`.
- [x] Panel intranet `/intranet/cms` (dashboard, páginas, noticias, galerías, testimonios, hero, homepage, settings, menús, media).
- [x] `CmsContentSeeder` + contenido demo alineado al sitio público; `PublicSiteController` consume CMS dinámico.
- [x] Frontend público: Home, noticias, galería y layout con SEO/meta desde CMS (fallback a contenido estático si vacío).
- [x] UI intranet CMS completa (páginas, noticias, categorías, galerías, testimonios, hero, homepage, settings, menús, media); editor HTML (`CmsRichTextEditor`, base para Tiptap).
- [ ] Pendiente menor: menús/navbar/footer públicos 100 % desde CMS; Tiptap; `body` CMS en todas las páginas internas estáticas.
- [x] Pruebas `tests/Feature/CMS/CmsManagementTest.php`, BDD `cms_management.feature`, Cypress `cms-management.cy.ts`.

## Completado — Fase 25 (UX/UI global premium institucional)

- [x] Refuerzo de design system global con componentes `Components/App/*` para cards, tablas, headers, badges, tabs, modal, drawer, skeleton y empty states.
- [x] Hardening visual de primitives (`Card`, `TableContainer`, `StatsCard`, `EmptyState`, `SectionTitle`) con modo dark coherente y sombras institucionales.
- [x] Layouts `IntranetLayout`, `TeacherLayout`, `StudentLayout` alineados a una superficie visual uniforme premium.
- [x] Dashboards refinados: `Pages/Intranet/Dashboard`, `Pages/Teacher/Dashboard`, `Pages/Student/Dashboard` con nueva jerarquía visual y bloques reutilizables.
- [x] Tablas/formularios refinados en módulos críticos (`Intranet/Students/Index`, `Intranet/Payments/Index`) usando `AppFilterBar`, `AppTable`, `AppBadge`, `AppEmptyState`.
- [x] Tokens utilitarios agregados en `resources/css/app.css` (`app-surface`, `app-focus-ring`, `app-transition`).

## Completado — Fase 25-B (Refinamiento UX/UI premium masivo)

- [x] Adopción masiva de `Components/App/*` en módulos críticos: CMS, Seguridad/Auditoría, Analítica, LMS, Adaptive, Comunicados, Reportes, Gestión de usuarios.
- [x] CMS premium refinado: `Intranet/Cms/Dashboard`, `Intranet/Cms/News/Index`, `Intranet/Cms/Media/Index` con `AppPageHeader`, `AppStatCard`, `AppFilterBar`, `AppTable`, `AppBadge`, `AppEmptyState`.
- [x] Seguridad y auditoría alineadas a header global (`Intranet/Security/AuditLogs`, `Sessions`, `LoginAttempts`) manteniendo timeline, filtros y tablas especializadas.
- [x] Analítica institucional y docente refinada (`Intranet/Analytics/Index`, `Teacher/Analytics/Index`) con superficies y tablas App\* consistentes.
- [x] LMS/adaptive/comunicación/reportes/administración refinados (`Intranet/LMS/Overview`, `Intranet/Adaptive/DiagnosticExams/Index`, `Intranet/Announcements/Index`, `Intranet/Reports/Analytics/Index`, `Intranet/Admin/Users/Index`, `Teacher/Classrooms/Index`, `Student/Classrooms/Index`).
- [x] Validación completada: `php artisan test`, `npm run build`, `vendor/bin/pint --dirty`.

## Completado — Fase 26 (Gamificación y logros institucional)

- [x] Nuevas entidades de gamificación: `GamificationProfile`, `Achievement`, `StudentAchievement`, `ExperienceTransaction`, `StudentStreak`, `Challenge`, `StudentChallenge`, `LeaderboardSnapshot`.
- [x] Nuevos enums de negocio: `AchievementType`, `ChallengeType`, `ExperienceSource`, `StreakType`.
- [x] Motor de gamificación `GamificationService` con XP, niveles, retos, streaks, logros y ranking saludable.
- [x] Integración automática con módulos clave: LMS (`AssignmentService`, `OnlineExamService`), adaptive (`AdaptiveDiagnosticService`) e IA (`StudentAIController`).
- [x] Nueva vista de estudiante `/student/gamification` + componentes UI premium `Components/Gamification/*`.
- [x] Dashboard estudiante con mini bloque XP/nivel/racha; dashboard docente con alumnos destacados por gamificación.
- [x] Analítica institucional de gamificación `/intranet/gamification` (admin) con KPIs y top estudiantes.
- [x] Rutas, navegación y políticas agregadas (`StudentNavigation`, `IntranetNavigation`, `GamificationPolicy`, `GamificationDashboard`).
- [x] Cobertura inicial de pruebas: `tests/Feature/Gamification/GamificationTest.php`, `tests/Bdd/features/gamification.feature`, `cypress/e2e/gamification.cy.ts`.

## Completado — Fase 27 (Preparación producción, deploy real y hardening final)

- [x] Hardening de seguridad HTTP con `SecurityHeadersMiddleware` (X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, CSP y HSTS condicional en producción HTTPS).
- [x] Health check de producción ampliado (`SystemHealthService` + UI `Intranet/System/Health`) con checks `ok/warning/critical` para DB, queue, cache, storage writable, storage link, scheduler heartbeat, disco, APP_DEBUG, HTTPS, mail y backups.
- [x] Scripts de despliegue y verificación: `scripts/deploy.sh`, `scripts/production-check.sh`.
- [x] Artefactos operativos para scheduler/colas: `deploy/supervisor/laravel-worker.conf`, `deploy/cron/scheduler.txt`.
- [x] Docker producción separado: `docker-compose.prod.yml` + `docker/nginx.prod.conf`.
- [x] SEO/metadata de producción: `robots.txt` con sitemap, `sitemap.xml`, `manifest.webmanifest`, canonical + OpenGraph base en layout público.
- [x] Documentación ampliada: `DEPLOYMENT.md`, `DEVOPS.md`, `PRODUCTION_CHECKLIST.md`.
- [x] Pruebas de readiness: `tests/Feature/System/ProductionReadinessTest.php`.

## Pendiente / siguientes fases (ROADMAP)

- Boleta térmica y PDF de comprobante ampliados; permisos granulares por módulo si aplica.
- Portal apoderado (rol Apoderado) si aplica en roadmap.
- LMS: UI ampliada (anuncios/recursos CRUD, rúbricas, banco de preguntas múltiples en formulario, sincronización explícita con `GradeRecord` si se requiere).

## Notas

- El **login Breeze** (pantallas y controladores) se mantiene; el destino tras login sigue siendo la ruta nombrada `dashboard` (`/intranet/dashboard`).
- En producción HTTPS: definir `SESSION_SECURE_COOKIE=true` (y revisar `SESSION_SAME_SITE`) según `docs/SECURITY_POLICY.md`.
- Datos demo de estudiantes: `php artisan db:seed --class=StudentDemoSeeder` (opcional).
- Datos demo de apoderados (requiere estudiantes): `php artisan db:seed --class=GuardianDemoSeeder` (opcional).
- Estructura académica demo: `AcademicStructureSeeder` se ejecuta con `php artisan db:seed` (además de roles y usuario de prueba).
