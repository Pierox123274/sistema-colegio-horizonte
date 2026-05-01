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
- **Cypress** y **Gherkin** (según fases del roadmap).
- **Pest** opcional si el equipo lo adopta de mutuo acuerdo (el proyecto arranca con PHPUnit).

## Fase actual

Fase 1 no añade casos de negocio nuevos; las pruebas existentes de Breeze se mantienen. Las carpetas `tests/Feature` y `tests/Unit` son el lugar estándar para crecer.
