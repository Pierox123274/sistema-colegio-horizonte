# ARTÍCULO 39 — FICHA TÉCNICA PARA CURSOR

## 1. Referencia

Foung, D.; Chen, J.; Cheung, K. (2023).  
“Exploring language needs of college transfer students with learning analytics: towards a more equitable experience”.  
Revista: International Journal of Educational Technology in Higher Education.  
Volumen/Número: 20(60).  
Páginas: 1–15.

## 2. Idea clave del artículo

El artículo analiza las necesidades lingüísticas de estudiantes transferidos en educación superior mediante Learning Analytics.

La idea principal es que la equidad educativa no se logra únicamente permitiendo el acceso a una institución o plataforma. También es necesario identificar las necesidades específicas de los estudiantes y ofrecer apoyos diferenciados según sus condiciones académicas, lingüísticas y de trayectoria educativa.

El estudio demuestra que los datos institucionales y de plataformas educativas pueden ayudar a detectar estudiantes con riesgo académico, especialmente cuando presentan dificultades en lenguaje, vocabulario, presentación oral, organización y desarrollo de contenido.

## 3. Aporte específico al sistema

Este artículo aporta una mirada de equidad basada en necesidades específicas del estudiante.

Su valor principal para el sistema VRAEM es que permite justificar que no todos los estudiantes con bajo rendimiento tienen el mismo tipo de dificultad. Algunos pueden tener problemas matemáticos, otros problemas de comprensión lectora, otros dificultades de expresión, vocabulario o interpretación de instrucciones.

Este artículo ayuda a fortalecer:

- Diagnóstico por dimensiones específicas.
- Detección de necesidades académicas concretas.
- Learning Analytics para identificar estudiantes en riesgo.
- Apoyo diferenciado según tipo de brecha.
- Equidad educativa basada en datos.
- Recomendaciones más precisas según necesidad real.

## 4. Decisión técnica derivada

A partir de este artículo, el sistema debe evitar clasificar al estudiante únicamente con una nota global.

La decisión técnica principal es que el sistema debe descomponer el rendimiento del estudiante en dimensiones o competencias específicas.

Por ejemplo, en comprensión lectora no basta con registrar “aprobó” o “desaprobó”. El sistema debe identificar si la dificultad está en:

- Vocabulario.
- Comprensión literal.
- Comprensión inferencial.
- Organización de ideas.
- Interpretación de consignas.
- Producción de respuestas.
- Expresión oral o escrita, si aplica.

Esto permitirá recomendar actividades más precisas y justas.

## 5. Datos que sugiere considerar

El artículo permite justificar el uso de datos académicos, institucionales y de desempeño específico.

Para el sistema, se pueden considerar:

- Puntaje general.
- Puntaje por competencia.
- Puntaje por tema.
- Nivel de comprensión lectora.
- Nivel de vocabulario.
- Dificultad en interpretación de preguntas.
- Dificultad en organización de respuestas.
- Dificultad en expresión escrita.
- Historial académico.
- Carga de actividades.
- Actividades completadas.
- Actividades pendientes.
- Progreso por dimensión.
- Comparación entre evaluación inicial y posterior.
- Estudiantes con riesgo por área específica.
- Necesidad de apoyo docente.

En el contexto VRAEM, estos datos pueden adaptarse especialmente a comprensión lectora y razonamiento matemático.

## 6. Técnicas o enfoques que respalda

El artículo respalda enfoques de Learning Analytics y predicción de riesgo académico.

Para el sistema, respalda:

- Learning Analytics.
- Árboles de clasificación.
- Análisis predictivo.
- Comparación de grupos.
- Pruebas estadísticas.
- Identificación de factores de riesgo.
- Análisis por dimensiones de desempeño.
- Dashboards de apoyo académico.
- Detección temprana de necesidades.
- Analítica orientada a la equidad.

Este artículo no se usa principalmente para justificar aprendizaje adaptativo general, sino para definir cómo detectar necesidades específicas del estudiante mediante datos.

## 7. Regla o lógica aplicable al sistema

Este artículo permite derivar las siguientes reglas funcionales:

Si el estudiante obtiene bajo puntaje global, el sistema debe identificar en qué dimensión específica se concentra la dificultad.

Si el estudiante presenta bajo desempeño en vocabulario, el sistema debe recomendar actividades de vocabulario antes de actividades de comprensión avanzada.

Si el estudiante tiene problemas en organización de respuestas, el sistema debe recomendar ejercicios guiados de estructura y orden de ideas.

Si el estudiante falla preguntas por mala interpretación de consignas, el sistema debe recomendar actividades de comprensión de instrucciones.

Si varios estudiantes presentan bajo desempeño en la misma dimensión, el sistema debe mostrar al docente una brecha grupal.

Si un estudiante tiene bajo rendimiento en una competencia específica, el sistema debe evitar asignarle una ruta genérica y debe recomendar apoyo focalizado.

## 8. Métricas útiles

El artículo trabaja con métricas de predicción y comparación de desempeño. Para el sistema, se pueden considerar:

- Precisión del modelo de clasificación.
- Puntaje por competencia.
- Puntaje por dimensión.
- Mejora en vocabulario.
- Mejora en comprensión lectora.
- Mejora en organización de respuestas.
- Mejora en presentación o expresión, si aplica.
- Comparación entre evaluación diagnóstica y evaluación posterior.
- Cantidad de estudiantes con brechas por dimensión.
- Cantidad de estudiantes que superan una brecha específica.
- Diferencias de desempeño entre grupos.
- Efectividad de actividades de refuerzo por dimensión.

## 9. Resultado o evidencia relevante

El artículo utilizó datos de 706 estudiantes transferidos y aplicó un árbol de clasificación para predecir si tendrían desempeño en riesgo o satisfactorio en cursos de lengua.

El modelo alcanzó una precisión de 70.94%, considerada aceptable para identificar estudiantes con posible riesgo académico.

Los factores más importantes para predecir el éxito fueron:

- Resultado previo en examen público de inglés.
- GPA de graduación del programa subuniversitario.
- GPA actual en la universidad.
- Créditos requeridos para graduarse.
- Carga académica del semestre.

Además, el estudio comparó el rendimiento de estudiantes transferidos con estudiantes de ingreso directo y encontró diferencias significativas en cinco áreas:

- Desarrollo de contenido.
- Organización.
- Lenguaje y vocabulario.
- Habilidades de presentación.
- Referencias.

Las mayores brechas aparecieron en lenguaje, vocabulario y habilidades de presentación.

## 10. Limitación del artículo

Aunque el artículo es útil para orientar el sistema, presenta algunas limitaciones:

- Está aplicado en educación superior, no en educación escolar.
- Se centra en estudiantes transferidos universitarios.
- Está contextualizado en Hong Kong, no en el VRAEM.
- Se enfoca en cursos de lengua, no en todas las áreas académicas.
- No propone una arquitectura de software lista para implementar.
- No desarrolla un sistema de nivelación adaptativa completo.
- No incluye suficientemente factores cualitativos como motivación, ansiedad, apoyo familiar o percepción del estudiante.
- No aborda directamente conectividad rural o brechas tecnológicas.
- No entrega un modelo de machine learning listo para aplicar en el contexto VRAEM.

Por ello, el sistema debe tomar su enfoque de analítica por necesidades específicas, pero adaptarlo a estudiantes con brechas de aprendizaje en contexto rural.

## 11. Cómo se usará en el sistema

Este artículo se usará para justificar que el sistema debe identificar brechas específicas y no solo niveles generales.

Su aporte principal se aplicará en:

- Diagnóstico por competencia.
- Análisis de necesidades específicas.
- Detección de riesgo por dimensión.
- Recomendación focalizada.
- Reportes de brechas académicas.
- Apoyo diferenciado para estudiantes.
- Analítica con enfoque de equidad.

No se usará principalmente para repetir la idea de personalización general, sino para fortalecer la precisión del diagnóstico y de las recomendaciones.

## 12. Aplicación directa al sistema VRAEM

En el sistema de nivelación académica adaptativa para estudiantes del VRAEM, este artículo permite definir que el diagnóstico debe ser detallado.

La plataforma debe:

- Evaluar competencias específicas.
- Detectar en qué dimensión falla el estudiante.
- Recomendar actividades focalizadas.
- Evitar rutas genéricas para todos los estudiantes de bajo rendimiento.
- Mostrar al docente las brechas individuales y grupales.
- Usar datos para diseñar apoyos más justos.
- Diferenciar entre bajo rendimiento general y dificultad específica.

Esto es importante porque reducir desigualdad educativa requiere reconocer qué necesita cada estudiante, no solo identificar quién está desaprobando.

## 13. Decisión técnica final derivada del Artículo 39

A partir de este artículo, el sistema debe implementar una lógica de diagnóstico granular por competencias.

Esto significa que el sistema debe analizar el desempeño del estudiante en dimensiones específicas y generar recomendaciones focalizadas según la brecha detectada.

La decisión técnica central es que el sistema debe pasar de una clasificación general como “nivel bajo” a una clasificación más útil, por ejemplo:

- Nivel bajo en vocabulario.
- Nivel medio en comprensión literal.
- Nivel bajo en comprensión inferencial.
- Nivel alto en resolución básica.
- Riesgo específico en interpretación de consignas.

Esta clasificación permitirá una nivelación más precisa y equitativa.

## 14. Instrucción para Cursor

Cuando desarrolles el sistema, usa este artículo para implementar diagnósticos por dimensión o competencia.

No hagas que el sistema solo calcule una nota final. Cada evaluación debe permitir identificar:

- Tema evaluado.
- Competencia evaluada.
- Tipo de dificultad.
- Dimensión afectada.
- Nivel de riesgo por dimensión.
- Actividad recomendada para esa brecha.

El sistema debe reflejar la idea central del artículo: la analítica del aprendizaje permite una experiencia más equitativa cuando identifica necesidades específicas y orienta apoyos diferenciados para cada estudiante.