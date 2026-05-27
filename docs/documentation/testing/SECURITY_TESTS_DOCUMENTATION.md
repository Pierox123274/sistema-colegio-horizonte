# Pruebas de seguridad

## Archivos principales

- `tests/Feature/Security/AuditSecurityTest.php`  
- `tests/Bdd/features/security_audit.feature`  
- `cypress/e2e/security-audit.cy.ts`  
- `tests/Feature/System/ProductionReadinessTest.php`

## Qué validan

- Registro en `audit_logs` tras acciones sensibles.  
- Restricción de rutas de seguridad a administrador.  
- Intentos de login y monitoreo.  
- Headers y checklist de producción (Fase 27).

## Importancia

Demuestra cumplimiento orientado a ISO/IEC 27001 en trazabilidad y control de acceso.
