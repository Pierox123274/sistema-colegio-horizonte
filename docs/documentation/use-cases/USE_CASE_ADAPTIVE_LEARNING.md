# Caso de uso — Diagnóstico y aprendizaje adaptativo

## Identificación

| Campo | Valor |
|-------|--------|
| **ID** | UC-ADP-007 |
| **Nombre** | Rendir diagnóstico y recibir ruta de aprendizaje |
| **Módulo** | Aprendizaje adaptativo |

## Actores

| Tipo | Actor |
|------|--------|
| **Principal** | Estudiante |
| **Secundarios** | Docente/Admin (diseño de exámenes), `AdaptiveDiagnosticService`, `StudentRecommendationService` |

## Objetivo

Identificar brechas de aprendizaje mediante exámenes diagnósticos y proponer recomendaciones personalizadas.

## Precondiciones

1. Examen diagnóstico publicado y dentro de alcance (nivel/sección).
2. Estudiante con acceso según `DiagnosticExamAccessService`.

## Flujo principal

1. El estudiante accede a **Diagnósticos** (`/student/diagnostic`).
2. Inicia intento (`DiagnosticAttempt`).
3. Responde banco de preguntas con opciones ponderadas.
4. Al finalizar, el sistema calcula perfil (`student_adaptive_profiles`).
5. Se generan recomendaciones (`learning_recommendations`) y ruta en **Learning Path** (`/student/learning-path`).
6. Integración opcional con LMS vía `LMSAdaptiveIntegrationService`.

## Flujos alternativos

| ID | Condición | Comportamiento |
|----|-----------|----------------|
| FA-1 | Reintento permitido | Nuevo intento si política lo permite. |
| FA-2 | Docente revisa resultados | Panel intranet adaptive analytics. |

## Excepciones

| ID | Excepción | Respuesta |
|----|-----------|-----------|
| EX-1 | Examen cerrado | No inicio de intento. |
| EX-2 | Tiempo agotado | Cierre automático del intento. |

## Resultado esperado

Personalización pedagógica basada en evidencia de desempeño diagnóstico.

## Evidencia

- `tests/Feature/AdaptiveLearning/AdaptiveLearningTest.php`, Cypress `adaptive-learning.cy.ts`.
