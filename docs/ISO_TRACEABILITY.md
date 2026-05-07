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
