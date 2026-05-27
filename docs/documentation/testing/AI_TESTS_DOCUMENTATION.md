# Pruebas de IA

## Archivos

- `tests/Feature/AI/AITutorTest.php` — tutor, permisos, OpenAI fake, auditoría.  
- `tests/Feature/AI/AdvancedAIFeaturesTest.php` — copiloto, generadores, caché, exports.  
- `tests/Bdd/features/ai_tutor.feature`, `advanced_ai.feature`  
- `cypress/e2e/ai-tutor.cy.ts`, `advanced-ai.cy.ts`

## Escenarios clave

- IA deshabilitada → fallback / mensaje institucional.  
- `Http::fake` para OpenAI sin llamadas reales.  
- Metadatos de auditoría en `audit_logs.metadata` (no columna `context`).  
- Secretaría no accede a analítica IA admin.

## Comandos

```bash
php artisan test --filter=AITutorTest
php artisan test --filter=AdvancedAIFeaturesTest
```
