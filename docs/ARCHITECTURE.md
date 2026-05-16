# Arquitectura del sistema — I.E.P. Horizonte

Este documento fija la arquitectura base del proyecto **Laravel 12 + Inertia + React (TypeScript)**. Objetivo: mantenibilidad y escalabilidad sin **Clean Architecture pesada**: capas claras, pocas abstracciones, crecimiento por dominio cuando aparezca lógica real.

## Principios

1. **Laravel como capa de entrega**: HTTP, autenticación, colas, vistas Inertia y políticas siguen siendo el marco principal.
2. **Orquestación fina**: los controladores delegan en **Actions** (un caso de uso por clase o por método explícito) y en **Services** cuando el flujo crece o se reutiliza entre puntos de entrada.
3. **Modelos delgados**: Eloquent para persistencia y relaciones; reglas complejas o transacciones multi-modelo preferentemente fuera del modelo (Services/Actions).
4. **DTOs en los bordes**: objetos de transferencia para agrupar datos entre capas (respuestas a Inertia, integraciones externas) cuando un array suelto dificulta el mantenimiento.
5. **Frontend por contexto**: páginas públicas bajo `Pages/Public`, intranet bajo `Pages/Intranet`, portal docente bajo `Pages/Teacher`, portal estudiante bajo `Pages/Student`; componentes y layouts compartidos en `Components` y `Layouts`.

## Estructura backend (`app/`)

| Ruta | Uso |
|------|-----|
| `Http/Controllers` | Entrada HTTP; validación delegada a Form Requests; sin lógica de negocio voluminosa. |
| `Http/Requests` | Validación y autorización por formulario. Subcarpetas: `Auth` (Breeze), `Intranet`, `Public`. |
| `Actions` | Casos de uso puntuales: “crear X”, “importar Y”. Idealmente invocables desde controladores, jobs o comandos. |
| `Services` | Coordinación, reutilización entre acciones, integraciones (APIs, archivos). Evitar “god services”. |
| `DTOs` | Estructuras de datos inmutables o tipadas entre capas (no Eloquent). |
| `Enums` | Estados, tipos y catálogos cerrados (PHP 8.1+). |
| `Models` | Entidades Eloquent. |
| `Policies` / `Middleware` | Autorización y controles transversales (según se implemente). |
| `Support` | Utilidades puras sin estado de dominio (formateo, cálculos genéricos, helpers de librería). |
| `Traits` | Comportamiento reutilizable entre clases del dominio o infraestructura (con moderación). |

## Flujo recomendado (resumen)

```
Request → Form Request → Controller → Action y/o Service → Model / DB
                ↓
         Inertia::render(..., DTO/array estable)
```

Los **Jobs** y **Commands** pueden llamar a las mismas Actions/Services que los controladores.

## Portal docente (Fase 15)

- **Rutas HTTP**: prefijo `/teacher` con middleware `auth`, `verified` y `role:Docente|Administrador` (Secretaria, Estudiante y Apoderado no entran).
- **Controladores delgados**: `TeacherDashboardController`, `TeacherAttendanceController`, `TeacherGradesController`, `TeacherStudentsController`, `TeacherReportsController` renderizan páginas Inertia dedicadas y delegan datos en **servicios y políticas ya existentes** (`StudentService`, `AttendancePolicy`, `GradeRecordPolicy`, etc.). El registro masivo de asistencia y notas sigue en las rutas `intranet.*` para no duplicar validación ni almacenamiento.
- **Navegación**: `App\Support\TeacherNavigation` alimenta `teacherNav` en `HandleInertiaRequests`; el menú ERP (`IntranetNavigation`) incluye enlace **Portal docente** para los mismos roles.

## DevOps y operaciones (Fase 20)

- **Programación**: tareas institucionales en `routes/console.php` (Laravel 12); comandos `institution:*` para retención, respaldos y validación de entorno.
- **Colas**: jobs en `app/Jobs/` (`ShouldQueue`) para respaldos, correos automáticos, alertas y snapshot de métricas (`InstitutionMetricsSnapshotJob`) preparatorio para IA.
- **Respaldos**: `storage/app/backups`, servicio `InstitutionBackupService` (ZIP, SQLite / MySQL vía `mysqldump` si existe).
- **Observabilidad**: `SystemHealthService` + UI Inertia `Intranet/System/*` (salud, jobs fallidos, listado de respaldos); solo **Administrador** (`SystemOperationsPolicy`).
- **CI/CD**: GitHub Actions en `.github/workflows/ci.yml` (Pint, tests, build Vite).
- **Contenedores**: `Dockerfile`, `docker-compose.yml` (app, nginx, mysql, redis, queue, scheduler), `docker/nginx.conf`.
- **Seguridad de configuración**: `EnvSecurityValidator` y comando `institution:validate-environment`.

## Tutor IA institucional (Fase 21)

- **Contrato**: `App\AI\Contracts\AIProviderInterface`; implementación **OpenAI** en `App\AI\Providers\OpenAIProvider` (sin claves en código; `config/ai.php` + `.env`).
- **Proveedores reserva**: `OllamaProvider`, `GeminiProvider`, `ClaudeProvider` (stubs para expansión); `NullAIProvider` cuando la IA está deshabilitada.
- **Servicios**: `AITutorService` (chat, caché, auditoría con hash de prompt), `AcademicRiskAnalysisService` (riesgo heurístico), `StudentRecommendationService` (reglas pedagógicas).
- **Jobs**: `GenerateStudentInsightsJob`, `GenerateTeacherInsightsJob`, `GenerateInstitutionInsightsJob` (`ShouldQueue`); scheduler: `GenerateInstitutionInsightsJob` diario en `routes/console.php`.
- **Autorización**: `AIPolicy` sobre `App\Support\AIDashboard` (tutor estudiante + admin; insights docente + admin; **panel institucional solo Administrador**).
- **HTTP**: rutas `student/ai-tutor`, `student/recommendations`, `teacher/ai-insights`, `teacher/students-risk`, `intranet/ai-analytics` con `throttle:ai`.
- **Frontend**: `Pages/Student/AITutor`, `Student/Recommendations`, `Teacher/AIInsights`, `Teacher/StudentsRisk`, `Intranet/AIAnalytics/Index`.
- **Documentación**: `docs/AI_ARCHITECTURE.md`.

## Aprendizaje adaptativo (Fase 22)

- **Dominio**: `QuestionBank` + `QuestionOption` (ítems por curso/tema con competencias JSON); `DiagnosticExam` (modo fijo o adaptativo, alcance por año/sección/grado/nivel, umbrales de clasificación, `created_by_user_id`, pivote ordenado con puntos); `DiagnosticAttempt` (respuestas, puntaje, nivel clasificado, estado adaptativo); `LearningRecommendation`; `StudentAdaptiveProfile` (último nivel, debilidades, ruta de aprendizaje serializada).
- **Visibilidad y permisos**: `DiagnosticExamAccessService` (matrícula y exámenes «globales» sin año/sección; asignaciones docentes `TeacherAssignment`); políticas `DiagnosticExamPolicy`, `QuestionBankPolicy` registradas en `AppServiceProvider`; `DiagnosticAttemptPolicy` para interactuar con intentos.
- **Lógica**: `AdaptiveDiagnosticService` calcula puntajes, ajuste de dificultad en modo adaptativo, genera recomendaciones heurísticas; **no requiere proveedor de IA**.
- **Analítica**: `AdaptiveAnalyticsService` consolida métricas para docente (secciones, temas débiles, sin diagnóstico) e institución; `AcademicRiskAnalysisService` alimenta **Riesgo académico** en portal docente.
- **Auditoría**: `AuditModule::AdaptiveLearning` en creación/actualización de exámenes (intranet/docente) y flujo de intentos según servicio.
- **HTTP estudiante**: `student/diagnostic*`, `student/learning-path` (listado filtrado por `take` + matrícula).
- **HTTP docente**: `teacher/pedagogical-panel`, `teacher/diagnostics` (+ `create`, `{exam}`, `{exam}/results`), `teacher/academic-risk`; redirecciones desde `teacher/adaptive-learning` y `teacher/diagnostic-results` hacia el nuevo flujo; se mantienen `teacher/analytics`, `teacher/ai-insights`, `teacher/students-risk`.
- **HTTP intranet (Administrador|Secretaría)**: `intranet/adaptive/diagnostic-exams*`, `intranet/adaptive/questions`, `intranet/adaptive/results` (políticas restringen escritura a roles autorizados); `intranet/adaptive-analytics` (solo Administrador).
- **Frontend**: `Pages/Student/Diagnostic/*`, `Student/LearningPath`, `Teacher/PedagogicalPanel`, `Teacher/Diagnostics/*`, `Teacher/AcademicRisk`, `Intranet/Adaptive/*`, `Intranet/AdaptiveAnalytics/Index`.

## Seguridad, auditoría e ISO (Fase 19)

- **Persistencia**: `audit_logs` (acción, módulo, entidad, IP, user agent, old/new values, severidad), `login_attempts`, `user_sessions` (dispositivo, expiración, bandera sospechosa).
- **Servicios**: `AuditService` (registro y consulta con filtros por rol), `SecurityService` (intentos, bloqueo, IPs sospechosas), `SessionSecurityService` (registro, expiración, revocación).
- **Middleware global (web)**: `PreventSuspiciousAccess`, `VerifyActiveSession`, `LogUserActivity` (mutaciones POST/PUT/PATCH/DELETE).
- **Autorización**: `SecurityPolicy` sobre `App\Support\SecurityDashboard` (admin total; secretaría lectura acotada; docente solo su historial; estudiante/apoderado sin acceso).
- **Rutas**: `intranet/security/audit-logs`, `sessions`, `login-attempts`, `access-monitor`.
- **Configuración**: `config/security.php` (intentos, lockout, vida de sesión, umbrales IP).
- **Integración auth**: `LoginRequest`, `AuthenticatedSessionController`, `PasswordController`; auditoría en `AdminUserController` y exportes analíticos.

## Analítica y reportes (Fase 18)

- **Servicios**: `AnalyticsService` (orquestación y permisos por rol), `AcademicAnalyticsService`, `FinancialAnalyticsService`, `InventoryAnalyticsService`; reutilizan `AcademicGradeService::metrics`, consultas sobre asistencia, pagos, pensiones, ventas e inventario.
- **Autorización**: `AnalyticsPolicy` sobre `App\Support\AnalyticsDashboard` (admin total; secretaría académico/financiero; docente solo sus secciones vía `TeacherContextService`).
- **Rutas**: `intranet/analytics`, `intranet/reports/analytics/{type}` (+ export PDF/CSV); `teacher/analytics`.
- **Frontend**: `Components/Analytics/*` (KPI, filtros, gráficos Recharts), páginas `Intranet/Analytics`, `Intranet/Reports/Analytics`, `Teacher/Analytics`.

## Comunicados y notificaciones (Fase 17)

- **Dominio**: `Announcement`, `AnnouncementRecipient`, `AnnouncementRead`; prioridad y audiencia por enum; ventana de publicación `starts_at` / `ends_at`; adjuntos en disco público `storage/app/public/announcements`.
- **Servicio**: `AnnouncementService` (CRUD admin, visibilidad por rol o destinatarios personalizados, conteo de no leídos, payload de campana y tarjetas).
- **Entrega HTTP**: `AnnouncementController` (admin); `TeacherAnnouncementController`, `StudentAnnouncementController`, `IntranetAnnouncementInboxController` (solo lectura + marcar leído).
- **Autorización**: `AnnouncementPolicy` (gestión solo Administrador).
- **Frontend compartido**: `Components/Announcements/*`, prop `announcementBell` en `HandleInertiaRequests`; `NotificationBell` en cabecera intranet/portales.

## Portal estudiante (Fase 16)

- **Rutas HTTP**: prefijo `/student` con middleware `auth`, `verified` y `role:Estudiante|Administrador` (Docente, Secretaria y Apoderado no entran).
- **Vinculación cuenta ↔ ficha**: columna `students.user_id` (nullable, único); `StudentContextService::resolveStudentFor()` y `requireStudentFor()` para el rol Estudiante.
- **Controladores delgados**: `StudentDashboardController`, `StudentGradesController`, `StudentAttendanceController`, `StudentPaymentsController`, `StudentProfileController`; solo lectura; reutilizan `AcademicGradeService`, `AttendanceService` y `PaymentService`.
- **Navegación**: `App\Support\StudentNavigation` → prop `studentNav` en `HandleInertiaRequests`; redirección post-login y desde `/intranet/dashboard` vía `AuthRedirect` (estudiante sin administración).

## Administración de usuarios y carga docente (Fase 15.1)

- **Dominio**: `TeacherAssignment` (docente, año, nivel, grado, sección, curso opcional, tutor de aula, activo); `users.is_active`.
- **Servicio**: `TeacherContextService` (secciones del docente en el año activo, estadísticas del portal, comprobación de acceso a ficha de estudiante).
- **Entrega HTTP**: `AdminUserController`, `TeacherAssignmentController`; rutas bajo `/intranet/admin/*` con `role:Administrador`.
- **Autorización**: `UserPolicy` (solo administrador gestiona listado de usuarios; cualquier usuario autenticado sigue pudiendo actualizar su propio perfil vía reglas `update`); `TeacherAssignmentPolicy`; docente solo ve alumnado de sus secciones (`StudentPolicy` + filtros en `StudentService` y controladores del portal docente).
- **Frontend**: `Pages/Intranet/Admin/Users/*`, `Pages/Intranet/Admin/TeacherAssignments/*`; entradas en `IntranetNavigation`.

## Frontend (`resources/js/`)

| Ruta | Uso |
|------|-----|
| `Pages/Public` | Web institucional: `Public/Home`, `Public/Nosotros`, etc. Rutas en `PublicSiteController`; layout `PublicLayout`; componentes en `Components/Public/`. |
| `Pages/Intranet` | Área autenticada (dashboard, módulos operativos). |
| `Pages/Teacher` | Portal docente (Fase 15): dashboard y accesos académicos simplificados; layout `TeacherLayout`. |
| `Pages/Student` | Portal estudiante (Fase 16): notas, asistencia, pagos y perfil; layout `StudentLayout`. Comunicados en `Pages/Student/Announcements/*` (Fase 17). Diagnóstico adaptativo y ruta de aprendizaje (Fase 22): `Pages/Student/Diagnostic/*`, `Student/LearningPath`. |
| `Components/Announcements` | UI compartida de comunicados: campana, tarjetas, listados de portal e panel en dashboards (Fase 17). |
| `Pages/Auth`, `Pages/Profile` | Breeze; el perfil usa el layout de intranet (`IntranetLayout`) para coherencia con el área autenticada. |
| `Layouts` | `PublicLayout` (web pública: navbar + footer). `IntranetLayout` (intranet). `TeacherLayout` (portal docente). `StudentLayout` (portal estudiante). `GuestLayout` / `AuthenticatedLayout` (Breeze). |
| `Components` | `Components/Public/` (navbar, secciones landing, footer). `Components/Intranet/` (shell y widgets intranet). |
| `types` | Tipos compartidos TypeScript (`User`, `PageProps`, props por página). |

## Convenciones de nombres

- **PHP**: clases `PascalCase`, acciones `CreateFooAction`, servicios `FooService`, requests `StoreFooRequest`.
- **React**: componentes `PascalCase`, archivos de página alineados con la ruta Inertia.
- **Tests**: reflejar la ruta del código bajo prueba cuando sea posible (`tests/Feature/Intranet/...`).

## Autorización (Fase 2)

- **Roles** (`App\Enums\IntranetRole` + `spatie/laravel-permission`): Administrador, Secretaria, Docente, Estudiante, Apoderado.
- Rutas de intranet: middleware `auth`, `verified` y `role:` con la lista de roles del enum.
- **UserPolicy**: el administrador gestiona usuarios del sistema (`viewAny`, `create`, `view`, `update` sobre cualquier `User`); cualquier usuario puede actualizar su propia cuenta (perfil); eliminación de cuenta propia reservada al flujo Breeze de perfil.
- Detalle operativo: `docs/AUTHORIZATION.md`.

## Módulo de estudiantes (Fase 5)

- **Dominio**: modelo `App\Models\Student`, enums (`EducationalLevel`, `StudentStatus`, `Gender`, `DocumentType`), catálogo de grados `App\Support\StudentGradeCatalog`.
- **Entrega HTTP**: `StudentController` (delgado), validación en `StoreStudentRequest` / `UpdateStudentRequest`, reglas compartidas en `Http/Requests/Intranet/Concerns/ValidatesStudentAttributes`.
- **Servicio**: `App\Services\StudentService` (listados filtrados, alta y actualización con normalización de campos opcionales).
- **Autorización**: `StudentPolicy` + middleware `role:` por ruta (Administrador/Secretaria/Docente para consulta; Administrador/Secretaria para alta/edición); Estudiante y Apoderado excluidos del módulo. El **docente sin roles administrativos** solo consulta estudiantes matriculados en sus secciones del año activo según `TeacherAssignment` (Fase 15.1).
- **Frontend**: páginas Inertia `Pages/Intranet/Students/*`, navegación habilitada en `App\Support\IntranetNavigation` solo para roles con acceso.

## Módulo de apoderados (Fase 6)

- **Dominio**: `App\Models\Guardian`, pivote `guardian_student` (N:N con `Student`), enum `GuardianRelationshipType` (parentesco en ficha y por vínculo), bandera `is_emergency_contact` en apoderado.
- **Entrega HTTP**: `GuardianController`, `StoreGuardianRequest` / `UpdateGuardianRequest` (incl. arreglo `students` con datos de pivote; filas vacías se descartan en `prepareForValidation`).
- **Servicio**: `App\Services\GuardianService` (sync de pivote, normalización, exclusividad de **responsable económico** y **contacto principal** por estudiante respecto a otros apoderados).
- **Autorización**: `GuardianPolicy` + middleware `role:` (misma matriz que estudiantes: consulta Administrador/Secretaria/Docente; escritura Administrador/Secretaria; **Estudiante** y **Apoderado** sin acceso al módulo).
- **Frontend**: `Pages/Intranet/Guardians/*`, `GuardianFormFields`, `GuardianStudentLinksEditor` (vincular estudiantes existentes con prioridad y roles en el vínculo).

## Estructura académica institucional (Fase 7)

- **Dominio**: `EducationalLevel` → `Grade` → `Section` → `Classroom` (`section_id` opcional en aula).
- **Entrega HTTP**: controladores `App\Http\Controllers\Academic\*`, Form Requests `Store*/Update*` por recurso, servicios `EducationalLevelService`, `GradeService`, `SectionService`, `ClassroomService`.
- **Autorización**: políticas por modelo; rutas de índice/detalle bajo `role:Administrador|Secretaria|Docente`; rutas de alta/edición/baja solo `role:Administrador` (coherente con matriz pedida).
- **Frontend**: páginas `Pages/Intranet/Academic/**`; menú **Gestión académica** con hijos en `IntranetNavigation` y render anidado en `Sidebar`; migas `Components/Intranet/IntranetBreadcrumbs`.

## Matrículas y año académico (Fase 8)

- **Dominio**: `AcademicYear` (año calendario único; solo uno `is_active`); `Enrollment` vinculado a estudiante, apoderado opcional, año académico, nivel y grados/sección del modelo académico (`EducationalLevel`, `Grade`, `Section`, `Classroom`), estado (`EnrollmentStatus`), monto y observaciones.
- **Entrega HTTP**: `EnrollmentController`, `AcademicYearController`; validación en `StoreEnrollmentRequest`, `UpdateEnrollmentRequest`, `StoreAcademicYearRequest`, `UpdateAcademicYearRequest`.
- **Servicios**: `EnrollmentService` (filtros en índice, generación de código, reglas de negocio); `AcademicYearService` (desactivar otros años al activar uno).
- **Soporte**: `EnrollmentFormCatalog` centraliza datos para selects dependientes en Inertia.
- **Autorización**: `EnrollmentPolicy`, `AcademicYearPolicy` + middleware `role:` (Docente solo lectura en matrículas; escritura Administrador/Secretaria; años académicos solo Administrador/Secretaria).
- **Frontend**: `Pages/Intranet/Enrollments/*`, `Components/Intranet/EnrollmentFormFields`; años académicos en `Pages/Intranet/AcademicYears/*`; entrada **Matrículas** en `IntranetNavigation`.

## Finanzas — conceptos, pensiones y pagos (Fase 9)

- **Dominio**: `PaymentConcept` (tipo y monto referencial); `Pension` por matrícula y periodo (mes/año únicos); `Payment` con método y estado de registro/anulación.
- **Entrega HTTP**: `PaymentConceptController`, `PensionController`, `PaymentController`; Form Requests `Store*/Update*`; generación de listados y pagos mediante `PaymentConceptService`, `PensionService`, `PaymentService` (transacciones, saldo pendiente, refresco de estado de pensión).
- **Autorización**: políticas dedicadas; rutas financieras solo `role:Administrador|Secretaria` (Docente, Estudiante y Apoderado sin acceso al menú **Finanzas**).
- **Frontend**: `Pages/Intranet/PaymentConcepts/*`, `Pages/Intranet/Pensions/*`, `Pages/Intranet/Payments/*`; submenú **Finanzas** en `IntranetNavigation` (mismo patrón que Gestión académica).
- **Fuera de esta fase**: boleta térmica final, PDF de comprobante, inventario y ventas.

## Finanzas — comprobantes PDF y boleta térmica (Fase 10)

- **Entrega HTTP**: `PaymentReceiptController` expone tres endpoints para un pago existente: comprobante HTML, PDF descargable y ticket térmico (`/intranet/payments/{payment}/receipt`, `/pdf`, `/ticket`).
- **Servicio**: `PaymentReceiptService` centraliza armado de datos del comprobante (numeración `REC-YYYYMMDD-######`, datos institucionales, payload QR demo, formato de fecha/hora).
- **Persistencia**: pagos incorporan `created_by_user_id` para trazar el usuario que registró el cobro y mostrarlo en el comprobante.
- **Renderizado**: vistas Blade dedicadas en `resources/views/intranet/payments/`:
  - `receipt.blade.php` (comprobante profesional imprimible)
  - `receipt-pdf.blade.php` (plantilla para DomPDF)
  - `receipt-ticket.blade.php` (ticket térmico 58mm/80mm con `@media print`)
- **Frontend Inertia**: el detalle de pago `Pages/Intranet/Payments/Show.tsx` agrega acciones de **Ver comprobante**, **Descargar PDF** e **Imprimir ticket** sin alterar el flujo de registro/anulación de pagos.
- **Autorización**: se mantiene la matriz de finanzas actual (solo `Administrador` y `Secretaria` por middleware + `PaymentPolicy`).
- **Dependencia**: generación PDF con `barryvdh/laravel-dompdf` (estable y compatible con Laravel 12).

## Inventario — categorías, productos y movimientos (Fase 11)

- **Dominio**: `ProductCategory` (catálogo), `Product` (stock y precios), `InventoryMovement` (entrada/salida/ajuste con trazabilidad).
- **Entrega HTTP**: controladores `ProductCategoryController`, `ProductController`, `InventoryMovementController`; Form Requests dedicados para alta/edición de categorías/productos y registro de movimientos.
- **Servicios**:
  - `ProductCategoryService` (listado filtrado y mantenimiento de categorías).
  - `ProductService` (listado filtrado por categoría/estado/stock bajo, catálogos para formularios).
  - `InventoryMovementService` (transacción con `lockForUpdate`, recalculo automático de stock, bloqueo de stock negativo, estadísticas de inventario).
- **Autorización**:
  - Administrador: alta/edición/registro de movimientos.
  - Secretaria: solo listado y detalle.
  - Docente/Estudiante/Apoderado: sin acceso.
- **Frontend Inertia**: páginas en `Pages/Intranet/Inventory/{Categories,Products,Movements}/*` con tablas, badges, filtros, cards de métricas y alertas de stock bajo.
- **Navegación**: sidebar desplegable **Inventario** con hijos **Categorías**, **Productos** y **Movimientos**.

## Caja y ventas (Fase 12)

- **Dominio**:
  - `CashRegister`: apertura/cierre diario por usuario.
  - `Sale` + `SaleItem`: venta cabecera/detalle, cliente académico opcional (`student_id`/`guardian_id`), método de pago, estado.
  - `CashMovement`: libro de movimientos de caja (`apertura`, `venta`, `anulacion_venta`, `cierre`).
- **Servicios**:
  - `CashRegisterService`: control de apertura/cierre, regla de una caja abierta por usuario y fecha, resumen de caja.
  - `SaleService`: transacción de venta con bloqueo de productos, descuento de stock, registro de movimiento de caja y anulación con devolución de stock.
  - `CashMovementService`: listado filtrado de movimientos de caja.
  - `SaleReceiptService`: armado de datos del comprobante de venta.
- **Entrega HTTP**: controladores `CashRegisterController`, `SaleController`, `CashMovementController`, `SaleReceiptController`; Form Requests `OpenCashRegisterRequest`, `CloseCashRegisterRequest`, `StoreSaleRequest`.
- **Frontend**: páginas Inertia en `Pages/Intranet/Sales/**` para caja diaria, ventas, nueva venta y movimientos.
- **Comprobantes**: vistas Blade `resources/views/intranet/sales/receipt.blade.php` y `receipt-pdf.blade.php`.
- **Correcciones posteriores**:
  - búsqueda/autocomplete de estudiante en venta (`SaleController::searchStudents`, `studentPreview`);
  - validación `guardian_id` vinculado a `student_id` en request y service;
  - reportes de ventas filtrables con exportación PDF y CSV (compatible Excel) en rutas `intranet.sales.reports.export.*`.

## Asistencia académica (Fase 13)

- **Dominio**: `Attendance` asociado a estudiante, año académico, nivel, grado, sección y usuario registrador.
- **Estados**: enum `AttendanceStatus` (`presente`, `tarde`, `falta`, `justificado`).
- **Reglas clave**:
  - unicidad por estudiante + fecha + sección;
  - registro masivo solo para estudiantes matriculados en la sección/año seleccionados;
  - edición posterior mediante actualización del registro existente (upsert).
- **Entrega HTTP**: `AttendanceController`, request `StoreAttendanceBatchRequest`, servicio `AttendanceService`.
- **Reportes**: exportación PDF (`dompdf`) y CSV compatible con Excel.
- **Frontend Inertia**: `Pages/Intranet/Attendance/{Index,Create,StudentHistory}.tsx`.
- **Autorización**:
  - Administrador: total.
  - Docente: registrar y consultar.
  - Secretaria: solo consulta.
  - Estudiante/Apoderado: sin acceso administrativo.

## Calificaciones y evaluaciones (Fase 14)

- **Dominio**:
  - `Subject`: cursos/asignaturas.
  - `Evaluation`: evaluación por curso, año, nivel, grado y sección.
  - `GradeRecord`: nota por estudiante y evaluación (unicidad `evaluation_id + student_id`).
- **Servicios**:
  - `SubjectService`: filtros y paginación de cursos.
  - `EvaluationService`: filtros y paginación de evaluaciones.
  - `AcademicGradeService`: contexto de registro masivo (matrículas activas), registro/upsert de notas, historial, métricas y reportes.
- **Entrega HTTP**: `SubjectController`, `EvaluationController`, `AcademicGradeController`.
- **Validación**:
  - `Store/UpdateSubjectRequest`
  - `Store/UpdateEvaluationRequest`
  - `StoreGradeBatchRequest`
- **Autorización**:
  - Administrador: total.
  - Docente: registra notas, consulta y exporta.
  - Secretaria: consulta y exporta.
  - Estudiante/Apoderado: sin acceso administrativo.
- **Frontend Inertia**:
  - `Pages/Intranet/Academic/Subjects/*`
  - `Pages/Intranet/Academic/Evaluations/*`
  - `Pages/Intranet/Academic/Grades/RecordsIndex.tsx`
  - `Pages/Intranet/Academic/Grades/StudentHistory.tsx`
- **Reportes**:
  - PDF: `resources/views/intranet/academic/grades-report-pdf.blade.php`
  - CSV (Excel-compatible): `AcademicGradeController::exportExcel`.

## Qué queda fuera de fases tempranas

- Boleta térmica y PDF finales, inventario y ventas (roadmap); permisos más granulares por permiso Spatie si se requiere más allá de roles en rutas.

## Referencias

- Requerimientos generales: `SYSTEM_REQUIREMENTS.md`
- Plan por fases: `ROADMAP.md`
- Trazabilidad ISO: `docs/ISO_TRACEABILITY.md`
