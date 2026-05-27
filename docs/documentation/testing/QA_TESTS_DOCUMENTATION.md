# Pruebas de QA transversal (Fase 29)

## Archivos

- `tests/Feature/System/PlatformQualityAssuranceTest.php`  
- `tests/Bdd/features/platform_quality_assurance.feature`  
- `cypress/e2e/platform-quality-assurance.cy.ts`

## Objetivo

Validar coherencia de navegación, permisos en rutas críticas, enlaces de dashboard y estabilidad transversal post-fases.

## Complementos documentales

- `docs/KNOWN_LIMITATIONS.md`  
- `docs/DEMO_GUIDE.md`

## Ejecución

```bash
php artisan test --filter=PlatformQualityAssuranceTest
```
