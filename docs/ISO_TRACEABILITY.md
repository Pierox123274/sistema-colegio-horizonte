# Trazabilidad ISO y requerimientos

Matriz liviana entre **objetivos del sistema** (`SYSTEM_REQUIREMENTS.md`), estándares ISO citados en el proyecto y **artefactos concretos** del repositorio. Se actualizará por fase.

## Estándares y significado breve

| Norma | Enfoque en este proyecto |
|-------|---------------------------|
| **ISO 9001** | Procesos claros, trazabilidad requisito → implementación → prueba, mejora continua documentada. |
| **ISO/IEC 27001** | Seguridad de la información, controles de acceso, protección de datos, gestión de riesgos. |
| **ISO/IEC 25010** | Calidad del producto: funcionalidad, rendimiento, seguridad, mantenibilidad, usabilidad, compatibilidad. |
| **ISO/IEC 29119** | Pruebas: planificación, diseño, ejecución, evidencia. |

## Mapeo (Fase 2 — roles e intranet)

| Requerimiento / objetivo | ISO principal | Artefacto |
|--------------------------|----------------|-----------|
| Roles del sistema (Sección 6 SYSTEM_REQUIREMENTS) | 27001, 9001 | `App\Enums\IntranetRole`, `RoleSeeder`, middleware `role:`, `docs/AUTHORIZATION.md` |
| Login e intranet (Sección 2) | 25010, 27001 | Breeze + rutas `/intranet/dashboard`, `IntranetLayout`, pruebas `tests/Feature` |
| Pruebas y trazabilidad BDD | 29119 | `tests/Bdd/features/authentication.feature`, Cypress `cypress/e2e/auth.cy.ts` |

## Mapeo (Fase 5 — estudiantes)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-01** Registrar estudiantes | 9001, 25010 (adecuación funcional) | `Student`, `StudentService`, `StudentController`, rutas `intranet.students.*`, páginas `Pages/Intranet/Students/*` |
| **RF-18** Seguridad | 27001, 25010 (seguridad) | Middleware `role:` por ruta del módulo, `StudentPolicy`, exclusión Estudiante/Apoderado |
| **RNF-03** Seguridad avanzada | 27001 | Políticas + validación en Form Requests, datos personales solo para roles autorizados |
| **RNF-09** Trazabilidad | 9001 | Este documento, commits/MR con referencia a RF; flash de confirmación en altas/edición |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Intranet/StudentManagementTest.php`, `tests/Bdd/features/students.feature`, `cypress/e2e/students.cy.ts` |
| ISO 9001 (proceso requisito → implementación → prueba) | 9001 | Matriz anterior + `tests/Feature` enlazado a criterios BDD |
| ISO/IEC 27001 (control de acceso) | 27001 | `StudentPolicy`, middleware de rol, auditoría futura en fases de auditoría |
| ISO/IEC 25010 (calidad del producto) | 25010 | UX intranet coherente (`IntranetLayout`, validaciones visibles), rendimiento de listados paginados |
| ISO/IEC 29119 (pruebas) | 29119 | Pirámide: Feature + escenarios Gherkin + Cypress base |

## Mapeo (Fase 6 — apoderados)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-02** Gestión de apoderados | 9001, 25010 | `Guardian`, `guardian_student`, `GuardianService`, `GuardianController`, rutas `intranet.guardians.*`, UI `Pages/Intranet/Guardians/*` |
| **RF-18** Seguridad | 27001, 25010 | Middleware `role:` + `GuardianPolicy`; sin acceso para rol intranet **Apoderado** al CRUD ni para **Estudiante** |
| **RNF-03** Seguridad avanzada | 27001 | Validación en Form Requests; datos de contacto restringidos por rol |
| **RNF-09** Trazabilidad | 9001 | Matriz en este archivo; vínculos auditables en tabla pivote |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Intranet/GuardianManagementTest.php`, `tests/Bdd/features/guardians.feature`, `cypress/e2e/guardians.cy.ts` |

## Mapeo (Fase 7 — estructura académica)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-03** Gestión académica (niveles, grados, secciones, aulas) | 9001, 25010 | Modelos y tablas académicas, `AcademicStructureSeeder`, servicios y controladores `Academic\*`, rutas `intranet.academic.*`, UI `Pages/Intranet/Academic/*` |
| **RF-18** Seguridad | 27001, 25010 | Middleware `role:` (solo Administrador escribe; Secretaria/Docente leen; Estudiante/Apoderado sin acceso al módulo), políticas `EducationalLevelPolicy`, `GradePolicy`, `SectionPolicy`, `ClassroomPolicy` |
| **RNF-03** Seguridad avanzada | 27001 | Form Requests por recurso; eliminación condicionada en controlador si hay dependientes |
| **RNF-09** Trazabilidad | 9001 | Este documento; mensajes flash en operaciones; datos semilla reproducibles |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Intranet/AcademicStructureTest.php`, `tests/Bdd/features/academic_structure.feature`, `cypress/e2e/academic-structure.cy.ts` |

## Mapeo (Fase 8 — matrículas)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-06** Matrículas | 9001, 25010 | `AcademicYear`, `Enrollment`, `EnrollmentService`, `AcademicYearService`, `EnrollmentController`, `AcademicYearController`, rutas `intranet.enrollments.*` y `intranet.academic-years.*`, UI `Pages/Intranet/Enrollments/*` y `Pages/Intranet/AcademicYears/*` |
| **RF-01** Estudiantes (vínculo en matrícula) | 9001, 25010 | `student_id` en `enrollments`; listado de estudiantes con apoderados en `EnrollmentFormCatalog` |
| **RF-02** Apoderados (responsable opcional) | 9001, 25010 | `guardian_id` nullable; regla de apoderado vinculado vía `guardian_student` en `EnrollmentService` |
| **RF-03** Estructura académica (nivel, grado, sección, aula) | 9001, 25010 | FKs y validación en Form Requests + reglas de coherencia en `EnrollmentService` |
| **RF-18** Seguridad | 27001, 25010 | Middleware `role:`; `EnrollmentPolicy` (Administrador/Secretaria/Docente listan y consultan; solo Administrador/Secretaria escriben; **Estudiante** y **Apoderado** sin acceso). `AcademicYearPolicy` solo Administrador/Secretaria |
| **RNF-03** Seguridad avanzada | 27001 | Form Requests; reglas de negocio en servicio; sin exposición de módulo a roles no operativos |
| **RNF-09** Trazabilidad | 9001 | Códigos de matrícula, fechas, estados, observaciones; matriz en este documento; `docs/CURRENT_PROGRESS.md` |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Intranet/EnrollmentManagementTest.php`, `tests/Bdd/features/enrollments.feature`, `cypress/e2e/enrollments.cy.ts` |
| **ISO 9001** (requisito → implementación → prueba) | 9001 | Matrícula trazable a estudiante, año y ubicación curricular; pruebas Feature ligadas a roles y reglas |
| **ISO/IEC 27001** (acceso a datos) | 27001 | Políticas y middleware por rol; no se alteró el flujo de autenticación Breeze |
| **ISO/IEC 25010** (calidad) | 25010 | Usabilidad en formulario encadenado; integridad de datos con validación en capas |
| **ISO/IEC 29119** (pruebas) | 29119 | TDD en Feature; Gherkin; smoke E2E Cypress en ruta de listado |

## Mapeo (Fase 9 — conceptos, pensiones y pagos)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-07** Pensiones | 9001, 25010 | Modelo `Pension`, `PensionService`, `PensionController`, rutas `intranet.pensions.*`, migración con unicidad `(enrollment_id, month, year)`, UI `Pages/Intranet/Pensions/*` |
| **RF-08** Pagos | 9001, 25010 | Modelo `Payment`, `PaymentService`, `PaymentController`, rutas `intranet.payments.*`, generación de `payment_code`, actualización de estado de pensión vía `PensionService::refreshStatus`, UI `Pages/Intranet/Payments/*` |
| **RF-14** Reportes financieros (base) | 9001, 25010 | Listados filtrados y resumen JSON `studentFinancialSummary` para registro de cobros (sin PDF ni boleta térmica en esta fase) |
| **RF-18** Seguridad | 27001, 25010 | Middleware `role:` solo Administrador/Secretaria en rutas financieras; `PaymentConceptPolicy`, `PensionPolicy`, `PaymentPolicy`; Docente/Estudiante/Apoderado sin acceso |
| **RNF-03** Seguridad avanzada | 27001 | Form Requests y validación en servicio (`assertCanPayPension`, unicidades) |
| **RNF-09** Trazabilidad | 9001 | Códigos de pago, relaciones a matrícula/pensión; matriz en este documento; `docs/CURRENT_PROGRESS.md` |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Intranet/FinanceManagementTest.php`, `tests/Bdd/features/payments.feature`, `cypress/e2e/payments.cy.ts` |
| **ISO 9001** | 9001 | Flujo requisito → implementación → prueba para pensiones y pagos |
| **ISO/IEC 27001** | 27001 | Control de acceso por rol a datos financieros |
| **ISO/IEC 25010** | 25010 | Validaciones de negocio (saldo pendiente, duplicados) y UX de tablas/filtros |
| **ISO/IEC 29119** | 29119 | Pruebas Feature + escenarios Gherkin + smoke E2E |

## Mapeo (Fase 10 — comprobantes PDF y ticket térmico)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-09** Boletas térmicas | 9001, 25010 | Ruta `/intranet/payments/{payment}/receipt/ticket`, `PaymentReceiptController::ticket`, `resources/views/intranet/payments/receipt-ticket.blade.php` |
| **RF-10** PDF | 9001, 25010 | Ruta `/intranet/payments/{payment}/receipt/pdf`, `PaymentReceiptController::pdf`, `resources/views/intranet/payments/receipt-pdf.blade.php`, `barryvdh/laravel-dompdf` |
| **RF-08** Pagos (evidencia formal de cobro) | 9001, 25010 | `PaymentReceiptService`, `receipt.blade.php`, numeración de comprobante `REC-*`, botones en `Pages/Intranet/Payments/Show.tsx` |
| **RF-18** Seguridad (control de acceso por rol) | 27001, 25010 | Middleware `role:Administrador|Secretaria` en rutas de comprobante + autorización `PaymentPolicy::view` |
| **RNF-03** Seguridad avanzada | 27001 | Sin apertura a Docente/Estudiante/Apoderado; solo visualización de pago autorizado |
| **RNF-09** Trazabilidad | 9001 | Campo `payments.created_by_user_id`, código de pago + número de comprobante + fecha/hora en comprobante |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Intranet/PaymentReceiptTest.php`, `tests/Bdd/features/payment_receipts.feature`, `cypress/e2e/payment-receipts.cy.ts` |
| **ISO 9001** | 9001 | Flujo requisito → implementación → evidencia documental (HTML/PDF/ticket) |
| **ISO/IEC 27001** | 27001 | Restricción de acceso a datos financieros y comprobantes |
| **ISO/IEC 25010** | 25010 | Usabilidad (acciones directas desde detalle), mantenibilidad (servicio dedicado), compatibilidad de impresión térmica |
| **ISO/IEC 29119** | 29119 | Cobertura Feature, BDD y E2E base de rutas críticas |

## Mapeo (Fase 11 — inventario)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-09** Inventario (control de stock y movimientos) | 9001, 25010 | Modelos `ProductCategory`, `Product`, `InventoryMovement`; controladores y páginas `Intranet/Inventory/*`; rutas `intranet.inventory.*` |
| **RF-18** Seguridad (acceso por rol) | 27001, 25010 | Policies `ProductCategoryPolicy`, `ProductPolicy`, `InventoryMovementPolicy`; middleware `role:` (Administrador total, Secretaria solo lectura, demás sin acceso) |
| **RNF-03** Seguridad avanzada | 27001 | Validaciones de stock no negativo en request y servicio; operaciones críticas con transacción + `lockForUpdate` |
| **RNF-09** Trazabilidad | 9001 | `inventory_movements` con `previous_stock`, `new_stock`, `reason`, `observations`, `created_by_user_id`, `created_at` |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Intranet/InventoryManagementTest.php`, `tests/Bdd/features/inventory.feature`, `cypress/e2e/inventory.cy.ts` |
| **ISO 9001** | 9001 | Flujo requisito → implementación → evidencia de pruebas y documentación |
| **ISO/IEC 27001** | 27001 | Restricción estricta de acceso al módulo inventario y control de operaciones inválidas |
| **ISO/IEC 25010** | 25010 | Funcionalidad de stock, usabilidad en filtros/alertas, mantenibilidad por capa Services |
| **ISO/IEC 29119** | 29119 | Cobertura Feature + BDD + E2E para escenarios críticos de inventario |

## Mapeo (Fase 12 — caja y ventas)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-09** Inventario conectado a ventas | 9001, 25010 | `SaleService` descuenta stock y valida disponibilidad/inactividad de `Product` |
| **RF-08** Pagos / operaciones de cobro | 9001, 25010 | `Sale`, `SaleItem`, `CashRegister`, `CashMovement`; rutas `intranet.sales.*`; UI `Pages/Intranet/Sales/*` |
| **RF-18** Seguridad (acceso por rol) | 27001, 25010 | Policies `CashRegisterPolicy`, `SalePolicy`, `CashMovementPolicy`; middleware `role:Administrador|Secretaria` |
| **RNF-03** Seguridad avanzada | 27001 | Reglas de caja abierta obligatoria, bloqueo de venta sin stock, validación transaccional con `lockForUpdate` |
| **RNF-09** Trazabilidad | 9001 | `cash_movements`, estado de venta (`registrada`/`anulada`), usuario creador/anulador y timestamps |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Intranet/CashSalesManagementTest.php` |
| **ISO 9001** | 9001 | Flujo requisito → implementación → prueba en caja y ventas |
| **ISO/IEC 27001** | 27001 | Restricción de módulo y reglas de consistencia transaccional |
| **ISO/IEC 25010** | 25010 | Adecuación funcional (caja, venta, anulación, comprobante) y mantenibilidad por capa Services |
| **ISO/IEC 29119** | 29119 | Evidencia de pruebas Feature para escenarios críticos |
| Exportación de ventas diaria (PDF/Excel-compatible) | 9001, 25010 | `SaleController::exportPdf`, `SaleController::exportExcel`, `resources/views/intranet/sales/report-pdf.blade.php` |

## Mapeo (Fase 13 — asistencia académica)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-03** Gestión académica (asistencia) | 9001, 25010 | `Attendance`, `AttendanceController`, `AttendanceService`, rutas `intranet.attendance.*`, páginas `Pages/Intranet/Attendance/*` |
| **RF-16** Reportes | 9001, 25010 | Exportación `AttendanceController::exportPdf` y `AttendanceController::exportExcel`, vista `resources/views/intranet/attendance/report-pdf.blade.php` |
| **RF-18** Seguridad (acceso por rol) | 27001, 25010 | `AttendancePolicy`, middleware de rutas (Administrador y Docente registran; Secretaria consulta; Estudiante/Apoderado sin acceso) |
| **RNF-03** Seguridad avanzada | 27001 | Validación de pertenencia de estudiante a matrícula activa de sección/año en `StoreAttendanceBatchRequest` |
| **RNF-09** Trazabilidad | 9001 | `recorded_by_user_id`, `attendance_date`, estado y observación por registro |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Intranet/AttendanceManagementTest.php`, `tests/Bdd/features/attendance.feature`, `cypress/e2e/attendance.cy.ts` |
| **ISO 9001** | 9001 | Requisito → implementación → evidencia de pruebas del módulo asistencia |
| **ISO/IEC 27001** | 27001 | Control de acceso y validaciones de integridad con datos de matrícula |
| **ISO/IEC 25010** | 25010 | Funcionalidad completa de registro/consulta/reportes y mantenibilidad por Services |
| **ISO/IEC 29119** | 29119 | Cobertura Feature + BDD + E2E base para módulo asistencia |

## Mapeo (Fase 14 — calificaciones y evaluaciones)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-13** Evaluaciones y notas | 9001, 25010 | Modelos `Subject`, `Evaluation`, `GradeRecord`; controladores `SubjectController`, `EvaluationController`, `AcademicGradeController`; rutas `intranet.academic.subjects.*`, `intranet.academic.evaluations.*`, `intranet.academic.grades.*` |
| **RF-16** Reportes académicos | 9001, 25010 | Exportación PDF/CSV en `AcademicGradeController`, vista `resources/views/intranet/academic/grades-report-pdf.blade.php` |
| **RF-18** Seguridad | 27001, 25010 | Policies `SubjectPolicy`, `EvaluationPolicy`, `GradeRecordPolicy`; middleware por rol en rutas |
| **RNF-03** Seguridad avanzada | 27001 | `StoreGradeBatchRequest` valida matrícula activa por sección/año y score entre 0 y 20 |
| **RNF-09** Trazabilidad | 9001 | `grade_records.recorded_by_user_id`, `evaluations.created_by_user_id`, historial por estudiante |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Intranet/AcademicGradesManagementTest.php`, `tests/Bdd/features/academic_grades.feature`, `cypress/e2e/academic-grades.cy.ts` |
| **ISO 9001** | 9001 | Requisito → implementación → prueba documentada en módulo académico |
| **ISO/IEC 27001** | 27001 | Restricción de acceso y validación de integridad con matrículas |
| **ISO/IEC 25010** | 25010 | Funcionalidad de registro/historial/reportes y mantenibilidad por capas |
| **ISO/IEC 29119** | 29119 | Evidencia automatizada en Feature + BDD + E2E |

## Mapeo (Fase 15 — portal docente)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-03** Gestión académica (experiencia docente) | 9001, 25010 | Rutas `teacher.*`, controladores `Teacher*Controller`, páginas `Pages/Teacher/*`, `TeacherLayout` |
| **RF-18** Seguridad (acceso por rol al portal) | 27001, 25010 | Middleware `role:Docente|Administrador`; exclusión Secretaria/Estudiante/Apoderado; políticas en consultas (`AttendancePolicy`, `GradeRecordPolicy`, `StudentPolicy`) |
| **RNF-09** Trazabilidad | 9001 | Misma capa de datos que ERP; sin duplicar reglas de asistencia/notas |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Teacher/TeacherPortalTest.php`, `tests/Bdd/features/teacher_portal.feature`, `cypress/e2e/teacher-portal.cy.ts` |
| **ISO 9001** | 9001 | Requisito docente → portal dedicado → pruebas Feature |
| **ISO/IEC 27001** | 27001 | Separación de superficie: portal académico frente a módulos sensibles (finanzas, caja, inventario no expuestos aquí) |
| **ISO/IEC 25010** | 25010 | Usabilidad: interfaz simplificada y accesos rápidos |
| **ISO/IEC 29119** | 29119 | Cobertura de autorización y páginas críticas del portal |

## Mapeo (Fase 16 — portal estudiante)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-03** Consulta académica (experiencia estudiante) | 9001, 25010 | Rutas `student.*`, controladores `Student*Controller`, páginas `Pages/Student/*`, `StudentLayout` |
| **RF-11** Pagos (consulta) | 9001 | `StudentPaymentsController` + `PaymentService::studentFinancialSummary` (solo lectura) |
| **RF-18** Seguridad (acceso por rol) | 27001, 25010 | Middleware `role:Estudiante|Administrador`; `students.user_id`; estudiante solo ve su ficha |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Student/StudentPortalTest.php` |
| **ISO/IEC 27001** | 27001 | Sin exposición de módulos ERP de registro/edición; datos acotados al `student_id` resuelto |

## Mapeo (Fase 15.1 — usuarios y asignaciones docentes)

| Requerimiento / objetivo (SYSTEM_REQUIREMENTS) | ISO principal | Artefacto en el repo |
|-----------------------------------------------|---------------|----------------------|
| **RF-18** Seguridad (gestión de cuentas y carga) | 27001, 25010 | `AdminUserController`, `TeacherAssignmentController`, rutas `intranet.admin.*` solo Administrador; `UserPolicy`, `TeacherAssignmentPolicy`; `users.is_active` + bloqueo en `LoginRequest` |
| **RF-03** Operación académica (docente en aula) | 9001, 25010 | `TeacherAssignment`, `TeacherContextService`, filtrado en `Teacher*Controller` y `StudentService` / `StudentPolicy` |
| **RNF-10** Testing automatizado | 29119 | `tests/Feature/Intranet/AdminUserAndTeacherAssignmentTest.php` |

## Mapeo inicial (Fase 1 — arquitectura base)

| Requerimiento / objetivo (referencia) | ISO principal | Artefacto en el repo |
|----------------------------------------|---------------|----------------------|
| Stack Laravel + Inertia + React TypeScript (Sección 4) | 25010 (mantenibilidad, compatibilidad) | `composer.json`, `package.json`, estructura `app/`, `resources/js/` |
| MVC + Services; Clean Architecture cuando sea necesario (Sección 4) | 9001, 25010 | `docs/ARCHITECTURE.md`, carpetas `app/Services`, `app/Actions`, `app/DTOs` |
| Seguridad avanzada, protección de datos (Secciones 3–5) | 27001, 25010 (seguridad) | `docs/SECURITY_POLICY.md`, políticas/middleware en fases posteriores |
| Pruebas automatizadas, TDD/BDD (Sección 4 y 5) | 29119, 25010 | `docs/TESTING_STRATEGY.md`, `tests/Feature`, `tests/Unit`, Cypress (fases posteriores) |
| Trazabilidad por módulo: requerimiento, caso de prueba, riesgo, ISO, validación (Sección 5) | 9001, 29119 | Este documento + enlaces en PRs/MRs y, más adelante, issues por módulo |

## Convención para fases siguientes

Al implementar un módulo (por ejemplo matrículas):

1. Identificar IDs o títulos de requisito en `SYSTEM_REQUIREMENTS.md`.
2. Añadir fila a la tabla anterior (o subtabla por módulo) con ISO y rutas de código/prueba.
3. Registrar riesgos relevantes en el MR o en `docs/CURRENT_PROGRESS.md` si afectan despliegue.

## Roles del sistema (trazabilidad futura)

Los roles definidos en requerimientos (Administrador, Secretaría, Docente, Estudiante, Apoderado) se enlazarán en Fase 2+ con **policies**, **permissions** y pruebas de autorización; la trazabilidad se ampliará entonces en este archivo.
