# Estrategia de pruebas

Enfoque alineado a **ISO/IEC 29119** (planificación, diseño, ejecución y evidencia) y a la calidad del producto (**ISO/IEC 25010**): funcionalidad, fiabilidad, rendimiento, seguridad, mantenibilidad y usabilidad donde sea automatizable.

## Pirámide de pruebas

1. **Unit (`tests/Unit`)**: funciones puras, DTOs, helpers en `Support`, reglas aisladas sin I/O o con mocks.
2. **Feature (`tests/Feature`)**: HTTP, rutas, middleware, Inertia (props clave), integración con base de datos de prueba.
3. **E2E (Cypress)**: flujos críticos de usuario (login, matrículas, pagos, etc.) cuando existan; BDD/Gherkin según `ROADMAP.md`.

## Convenciones Laravel (PHPUnit)

- Usar factories y `RefreshDatabase` donde corresponda.
- Nombrar pruebas en español o inglés de forma **consistente** con el equipo; preferir descripción clara del comportamiento esperado.
- Una aserción principal por prueba cuando sea legible; agrupar escenarios relacionados con `@dataProvider` si reduce duplicación.

## Cobertura mínima recomendada (por módulo futuro)

Cada módulo de negocio debería tener al menos:

| Artefacto | Propósito |
|-----------|-----------|
| Caso de prueba | Condición inicial, acción, resultado esperado |
| Prueba automatizada | Feature y/o Unit según capa |
| Trazabilidad | Referencia al requerimiento en `docs/ISO_TRACEABILITY.md` o en comentario `@see` breve |

## Evidencia (29119)

- Los pipelines CI (cuando existan) archivan resultados de `php artisan test` y Cypress.
- Los fallos reproducibles incluyen comando, seed y versión de PHP/Node documentadas en el issue o MR.

## Herramientas previstas

- **PHPUnit** (incluido con Laravel).
- **Cypress** (instalado): E2E frente a la app en ejecución (`npm run e2e` / `npm run e2e:open`). Variable opcional `CYPRESS_BASE_URL` (por defecto `http://localhost:8000` en `cypress.config.ts`). Requiere `php artisan serve` (o stack equivalente) antes de correr E2E.
- **Gherkin**: escenarios legibles en `tests/Bdd/features/*.feature`, enlazados con pruebas PHPUnit en `tests/Feature` (sin motor Behat en este repositorio salvo que se añada después).
- **Pest** opcional si el equipo lo adopta de mutuo acuerdo (el proyecto arranca con PHPUnit).

## Fase actual

Fase 2 añade pruebas de intranet y roles (`tests/Feature/Intranet`, ampliaciones en `tests/Feature/Auth`). Las carpetas `tests/Feature` y `tests/Unit` siguen siendo el estándar; los `.feature` documentan criterios BDD (ISO/IEC 29119).

**Fase 15.1 (usuarios y asignaciones docentes)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Intranet/AdminUserAndTeacherAssignmentTest.php` (alta usuario docente/secretaría, prohibición secretaría en admin, asignación docente y filtrado de estudiantes, usuario inactivo no inicia sesión) |

**Fase 21 (Tutor IA institucional)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/AI/AITutorTest.php` (rutas tutor/recomendaciones, insights docente, analítica IA admin, audit IA, OpenAI fake / error) |
| BDD (Gherkin) | `tests/Bdd/features/ai_tutor.feature` |
| E2E (Cypress) | `cypress/e2e/ai-tutor.cy.ts` |

**Fase 22 (aprendizaje adaptativo)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/AdaptiveLearning/AdaptiveLearningTest.php` (diagnóstico fijo, perfil y recomendaciones, redirecciones docente, panel pedagógico, listado diagnósticos, índice intranet secretaría, políticas de creación y `take` del estudiante) |
| BDD (Gherkin) | `tests/Bdd/features/adaptive_learning.feature` |
| E2E (Cypress) | `cypress/e2e/adaptive-learning.cy.ts` |

**Fase 23 (aula virtual / LMS)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/LMS/VirtualClassroomTest.php` (aula, tarea, entrega, calificación, examen online, restricciones de acceso, dashboards con `lms`, resumen admin) |
| BDD (Gherkin) | `tests/Bdd/features/virtual_classroom.feature` |
| E2E (Cypress) | `cypress/e2e/virtual-classroom.cy.ts` |

**Fase 26 (gamificación y logros)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Gamification/GamificationTest.php` (perfil gamificado estudiante, XP/niveles, retos completados y permisos) |
| BDD (Gherkin) | `tests/Bdd/features/gamification.feature` |
| E2E (Cypress) | `cypress/e2e/gamification.cy.ts` |

**Fase 24 (CMS institucional)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/CMS/CmsManagementTest.php` (dashboard, permisos, CRUD noticia, home dinámica, página home) |
| BDD (Gherkin) | `tests/Bdd/features/cms_management.feature` |
| E2E (Cypress) | `cypress/e2e/cms-management.cy.ts` |

**Fase 20 (DevOps institucional)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/System/DevOpsInfrastructureTest.php` (scheduler listado, health/jobs/backups admin, prohibición secretaría, jobs y respaldo) |
| BDD (Gherkin) | `tests/Bdd/features/devops_infrastructure.feature` |
| E2E (Cypress) | `cypress/e2e/devops-infrastructure.cy.ts` |

**Fase 19 (seguridad y auditoría)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Security/AuditSecurityTest.php` (auditoría login/logout, bloqueo por intentos, sesiones, permisos por rol, export con log, docente filtrado) |
| BDD (Gherkin) | `tests/Bdd/features/security_audit.feature` |
| E2E (Cypress) | `cypress/e2e/security-audit.cy.ts` |

**Fase 18 (analítica)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Intranet/AnalyticsDashboardTest.php` (admin/secretaría/docente, prohibición estudiante, export PDF/CSV, métricas y rankings) |
| BDD (Gherkin) | `tests/Bdd/features/analytics_dashboard.feature` |
| E2E (Cypress) | `cypress/e2e/analytics-dashboard.cy.ts` |

**Fase 17 (comunicados)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Announcements/AnnouncementManagementTest.php` (alta admin, visibilidad por audiencia docente/estudiante, prohibición de creación, marcar leído, expiración, filtros prioridad y no leídos) |

**Fase 16 (portal estudiante)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Student/StudentPortalTest.php` (estudiante con ficha vinculada; solo sus notas; login y redirección; prohibición docente/apoderado/secretaría; administrador supervisión) |
| Feature | Ajustes en `tests/Feature/Auth/AuthenticationTest.php` e `Intranet/IntranetAuthorizationTest.php` (redirección estudiante) |

**Fase 15 (portal docente)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Teacher/TeacherPortalTest.php` (docente y administrador acceden; Secretaria, Estudiante y Apoderado reciben prohibición; páginas asistencia, notas y estudiantes) |
| BDD (Gherkin) | `tests/Bdd/features/teacher_portal.feature` |
| E2E (Cypress) | `cypress/e2e/teacher-portal.cy.ts` (invitado redirigido al login en rutas `/teacher/*`) |

> **Nota:** las pruebas Feature que renderizan Inertia con `@vite` requieren que exista el manifiesto de Vite (`npm run build`) cuando se añaden páginas nuevas al build de producción.

**Fase 5 (estudiantes)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Intranet/StudentManagementTest.php` (roles, validación, unicidad, CRUD lectura/escritura según rol) |
| BDD (Gherkin) | `tests/Bdd/features/students.feature` |
| E2E (Cypress) | `cypress/e2e/students.cy.ts` (base: invitado redirigido al login en `/intranet/students`) |

Los escenarios Gherkin se ejecutan como documentación y criterio de aceptación; la automatización correspondiente vive en PHPUnit/Cypress según la tabla anterior (ISO/IEC 29119).

**Fase 6 (apoderados)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Intranet/GuardianManagementTest.php` (roles, vínculos estudiante–apoderado, responsable económico, validación y unicidad de documento) |
| BDD (Gherkin) | `tests/Bdd/features/guardians.feature` |
| E2E (Cypress) | `cypress/e2e/guardians.cy.ts` (base: invitado redirigido al login en `/intranet/guardians`) |

**Fase 7 (estructura académica)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Intranet/AcademicStructureTest.php` (admin alta nivel/grado; Secretaria/Docente solo lectura; Estudiante/Apoderado prohibido; relaciones; validaciones de unicidad y capacidad) |
| BDD (Gherkin) | `tests/Bdd/features/academic_structure.feature` |
| E2E (Cypress) | `cypress/e2e/academic-structure.cy.ts` (invitado redirigido al login en rutas académicas) |

**Fase 8 (matrículas)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Intranet/EnrollmentManagementTest.php` (altas por Administrador/Secretaria; Docente solo índice/detalle; Estudiante/Apoderado prohibido; doble matrícula activa; apoderado no vinculado; sección incoherente con grado; actualización) |
| BDD (Gherkin) | `tests/Bdd/features/enrollments.feature` |
| E2E (Cypress) | `cypress/e2e/enrollments.cy.ts` (invitado redirigido al login en `/intranet/enrollments`) |

**Fase 9 (finanzas — conceptos, pensiones y pagos)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Intranet/FinanceManagementTest.php` (alta de concepto; pensión y unicidad de periodo; pago con actualización de estado de pensión; rechazo por exceso de saldo; monto positivo; Docente/Estudiante/Apoderado sin acceso; código de concepto único) |
| BDD (Gherkin) | `tests/Bdd/features/payments.feature` |
| E2E (Cypress) | `cypress/e2e/payments.cy.ts` (invitado redirigido al login en `/intranet/payments`) |

**Fase 10 (comprobantes PDF y ticket térmico)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Intranet/PaymentReceiptTest.php` (Administrador/Secretaria pueden ver comprobante; Docente prohibido; respuesta PDF; respuesta ticket) |
| BDD (Gherkin) | `tests/Bdd/features/payment_receipts.feature` |
| E2E (Cypress) | `cypress/e2e/payment-receipts.cy.ts` (invitado redirigido al login en rutas de comprobante, PDF y ticket) |

**Fase 11 (inventario)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Intranet/InventoryManagementTest.php` (alta de categoría y producto por Administrador; entradas/salidas/ajustes; bloqueo de stock negativo; recalculo de stock; Secretaria solo visualiza; Docente/Estudiante/Apoderado sin acceso) |
| BDD (Gherkin) | `tests/Bdd/features/inventory.feature` |
| E2E (Cypress) | `cypress/e2e/inventory.cy.ts` (invitado redirigido al login en rutas de categorías/productos/movimientos) |

**Fase 12 (caja y ventas)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Intranet/CashSalesManagementTest.php` (Administrador y Secretaria abren caja; venta con/sin estudiante; venta con estudiante+apoderado; bloqueo por apoderado no vinculado; descuento y devolución de stock; export PDF/CSV; rechazo sin caja abierta; Docente sin acceso) |
| BDD (Gherkin) | Pendiente de ampliar en siguiente iteración |
| E2E (Cypress) | Pendiente de ampliar en siguiente iteración |

**Fase 13 (asistencia académica)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Intranet/AttendanceManagementTest.php` (Administrador/Docente registran; Secretaria consulta; Estudiante/Apoderado sin acceso; no duplicidad por estudiante-fecha-sección; export PDF/CSV) |
| BDD (Gherkin) | `tests/Bdd/features/attendance.feature` |
| E2E (Cypress) | `cypress/e2e/attendance.cy.ts` (invitado redirigido al login en rutas de asistencia) |

**Fase 14 (calificaciones y evaluaciones académicas)** incluye:

| Capa | Artefacto |
|------|-----------|
| Feature | `tests/Feature/Intranet/AcademicGradesManagementTest.php` (crear curso/evaluación, registro de notas, no duplicidad, promedio, exportaciones, autorización por rol) |
| BDD (Gherkin) | `tests/Bdd/features/academic_grades.feature` |
| E2E (Cypress) | `cypress/e2e/academic-grades.cy.ts` (invitado redirigido al login en rutas académicas de cursos/evaluaciones/notas) |
