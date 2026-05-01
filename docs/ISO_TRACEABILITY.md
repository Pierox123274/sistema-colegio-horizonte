# Trazabilidad ISO y requerimientos

Matriz liviana entre **objetivos del sistema** (`SYSTEM_REQUIREMENTS.md`), estándares ISO citados en el proyecto y **artefactos concretos** del repositorio. Se actualizará por fase.

## Estándares y significado breve

| Norma | Enfoque en este proyecto |
|-------|---------------------------|
| **ISO 9001** | Procesos claros, trazabilidad requisito → implementación → prueba, mejora continua documentada. |
| **ISO/IEC 27001** | Seguridad de la información, controles de acceso, protección de datos, gestión de riesgos. |
| **ISO/IEC 25010** | Calidad del producto: funcionalidad, rendimiento, seguridad, mantenibilidad, usabilidad, compatibilidad. |
| **ISO/IEC 29119** | Pruebas: planificación, diseño, ejecución, evidencia. |

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
