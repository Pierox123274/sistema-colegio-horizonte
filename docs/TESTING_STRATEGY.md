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
