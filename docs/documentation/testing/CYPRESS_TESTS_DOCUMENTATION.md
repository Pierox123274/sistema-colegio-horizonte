# Pruebas E2E (Cypress)

## Ubicación

`cypress/e2e/*.cy.ts` — 24 archivos.

## Cobertura smoke

| Archivo | Flujo |
|---------|--------|
| `auth.cy.ts` | Login |
| `students.cy.ts` | Estudiantes |
| `teacher-portal.cy.ts` | Docente |
| `virtual-classroom.cy.ts` | LMS |
| `adaptive-learning.cy.ts` | Diagnósticos |
| `ai-tutor.cy.ts`, `advanced-ai.cy.ts` | IA |
| `cms-management.cy.ts` | CMS |
| `notifications.cy.ts` | Notificaciones |
| `virtual-meetings.cy.ts` | Reuniones |
| `external-integrations.cy.ts` | Panel integraciones |
| `platform-quality-assurance.cy.ts` | QA |

## Comandos

```bash
npm run e2e
# o según package.json: npx cypress run
```

Variables: `CYPRESS_BASE_URL` (ver `.env.example`).

## Importancia

Valida integración real navegador + assets compilados (`npm run build`).
