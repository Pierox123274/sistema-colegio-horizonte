# Fase 21 — Tutor IA institucional

## Objetivo

Tutor conversacional y analítica IA con proveedor desacoplado (OpenAI) y riesgo académico heurístico.

## Módulos

`app/AI/`, `AITutorService`, `AcademicRiskAnalysisService`, páginas `Student/AITutor`, `Teacher/AIInsights`, `Intranet/AIAnalytics`.

## Rutas

`/student/ai-tutor`, `/teacher/ai-insights`, `/intranet/ai-analytics` (throttle `ai`).

## Pruebas

`AITutorTest.php`, `ai_tutor.feature`, `ai-tutor.cy.ts`.

## Valor

Apoyo pedagógico sin sustituir al docente; analítica para dirección.
