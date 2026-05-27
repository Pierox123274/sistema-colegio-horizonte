# Documentación de pruebas — Visión general

## Estrategia

Pirámide de pruebas alineada a ISO/IEC 29119 y buenas prácticas Laravel:

1. **Unitarias** — lógica aislada (donde aplica).  
2. **Feature (PHPUnit)** — HTTP, políticas, servicios con base de datos refrescada.  
3. **BDD (Gherkin)** — escenarios legibles para negocio.  
4. **E2E (Cypress)** — smoke de flujos críticos en navegador.

## Herramientas

| Herramienta | Uso |
|-------------|-----|
| PHPUnit | `php artisan test` |
| Laravel Pint | Estilo PHP `vendor/bin/pint --dirty` |
| Gherkin | `tests/Bdd/features/*.feature` |
| Cypress | `npm run e2e` (si está configurado en `package.json`) |
| GitHub Actions | CI en cada push/PR |

## Comandos principales

```bash
php artisan test
php artisan test --filter=NombreTest
vendor/bin/pint --dirty
npm run build
npm run e2e
```

## Resultado esperado

- Suite Feature: cientos de tests en verde (entorno SQLite en memoria o archivo).  
- Build frontend sin errores TypeScript.  
- Pint sin cambios pendientes o con formato aplicado.

## Importancia

Garantiza regresiones controladas en un sistema multi-módulo y multi-rol, esencial para evaluación académica y despliegue institucional.

## Documentos relacionados

- [FEATURE_TESTS_DOCUMENTATION.md](./FEATURE_TESTS_DOCUMENTATION.md)  
- [BDD_TESTS_DOCUMENTATION.md](./BDD_TESTS_DOCUMENTATION.md)  
- [CYPRESS_TESTS_DOCUMENTATION.md](./CYPRESS_TESTS_DOCUMENTATION.md)  
- Documento vivo: `docs/TESTING_STRATEGY.md`
