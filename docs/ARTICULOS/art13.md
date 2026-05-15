# ARTÍCULO 13 — FICHA TÉCNICA PARA CURSOR

## 1. Referencia

du Plooy, E.; Casteleijn, D.; Franzsen, D. (2024).  
“Aprendizaje adaptativo personalizado en la educación superior: una revisión exploratoria de las características clave y su impacto en el rendimiento académico y la participación”.  
Revista: Heliyon.  
Volumen: 10.  
Artículo: e39630.

## 2. Idea clave del artículo

El artículo analiza el Personalized Adaptive Learning —PAL— en educación superior. Su idea principal es que el aprendizaje adaptativo personalizado permite ajustar las trayectorias de aprendizaje según las necesidades, características, desempeño y comportamiento de cada estudiante.

El artículo destaca que el PAL no solo busca mejorar calificaciones, sino también fortalecer el progreso académico, la retención estudiantil, el engagement, la autorregulación y la participación activa del estudiante.

## 3. Aporte específico al sistema

Este artículo aporta una visión práctica sobre qué características debe tener un sistema de aprendizaje adaptativo personalizado.

Su valor principal para el sistema es que permite identificar qué datos o indicadores pueden activar la adaptación. Por ejemplo:

- Evaluaciones de conocimiento previo.
- Registros de actividad.
- Niveles de confianza del estudiante.
- Analítica de aprendizaje.
- Historial de progreso.
- Participación en la plataforma.

Este artículo ayuda a que el sistema no adapte actividades de manera arbitraria, sino usando indicadores concretos del estudiante.

## 4. Decisión técnica derivada

A partir de este artículo, el sistema debe incorporar indicadores de activación adaptativa.

Esto significa que el sistema debe decidir cuándo recomendar, reforzar, avanzar o cambiar la ruta del estudiante según señales observables.

La decisión técnica principal es que la adaptación debe activarse a partir de datos como evaluación inicial, actividad registrada, progreso, confianza o desempeño, y no únicamente por una nota final.

## 5. Datos que sugiere considerar

El artículo permite justificar el uso de datos académicos, conductuales y de participación.

Para el sistema, se pueden considerar:

- Resultado de evaluación diagnóstica.
- Pre-test o prueba de conocimiento previo.
- Puntaje por tema.
- Actividades realizadas.
- Actividades no completadas.
- Registros de acceso.
- Historial de progreso.
- Nivel de participación.
- Tiempo de dedicación.
- Nivel de confianza del estudiante.
- Nivel de autorregulación.
- Temas dominados.
- Temas con dificultad.
- Engagement del estudiante.
- Retención o permanencia en el proceso de nivelación.

## 6. Técnicas que respalda

El artículo no se centra en un solo algoritmo específico, sino en características y prácticas comunes del aprendizaje adaptativo personalizado.

Para el sistema, respalda enfoques como:

- Personalized Adaptive Learning.
- Learning Analytics.
- Evaluación diagnóstica adaptativa.
- Modelado del estudiante.
- Seguimiento del engagement.
- Rutas personalizadas.
- Sistemas de recomendación.
- Adaptación basada en registros de actividad.
- Adaptación basada en conocimiento previo.
- Adaptación basada en confianza o autorregulación.

Este artículo es útil para definir qué señales debe mirar el sistema antes de tomar una decisión adaptativa.

## 7. Regla o lógica aplicable al sistema

Este artículo permite derivar las siguientes reglas funcionales:

Si el estudiante obtiene bajo resultado en la prueba de conocimiento previo, el sistema debe iniciar con actividades básicas de nivelación.

Si el estudiante muestra buen desempeño en un tema, el sistema puede permitir avanzar hacia actividades de mayor dificultad.

Si el estudiante tiene baja participación o no completa actividades, el sistema debe generar una alerta de bajo engagement.

Si el estudiante registra baja confianza en un tema, el sistema debe recomendar refuerzo antes de avanzar.

Si el estudiante mantiene progreso constante, el sistema debe actualizar su ruta y mostrar avance positivo.

Si el estudiante presenta dificultades repetidas, el sistema debe reforzar el tema y notificar al docente.

## 8. Métricas útiles

El artículo trabaja con métricas relacionadas con impacto académico y participación. Para el sistema, se pueden considerar:

- Rendimiento académico.
- Mejora entre pre-test y post-test.
- Engagement del estudiante.
- Retención o permanencia.
- Progreso académico.
- Actividades completadas.
- Nivel de autorregulación.
- Pensamiento crítico, si se mide con instrumentos adicionales.
- Desarrollo de competencias.
- Nivel de confianza del estudiante.
- Participación en la plataforma.
- Mejora por tema.
- Reducción de brechas de aprendizaje.

## 9. Resultado o evidencia relevante

El artículo revisa 69 estudios sobre Personalized Adaptive Learning en educación superior.

Entre los resultados más relevantes:

- 41 estudios reportaron mejoras en el rendimiento académico.
- 25 estudios evidenciaron incremento del engagement estudiantil.
- También se identificaron beneficios en autorregulación, pensamiento crítico y personalización del aprendizaje.
- El pre-knowledge quiz o prueba de conocimiento previo aparece como uno de los indicadores más utilizados para activar la adaptación.
- Plataformas como Moodle y McGraw-Hill’s Connect LearnSmart aparecen como herramientas usadas en implementaciones de PAL.

Estos resultados permiten afirmar que el aprendizaje adaptativo personalizado suele obtener mejores resultados que modelos tradicionales, especialmente cuando se busca mejorar rendimiento, participación y progreso académico.

## 10. Limitación del artículo

Aunque el artículo es útil como síntesis del PAL, presenta algunas limitaciones:

- Se enfoca en educación superior, no directamente en educación escolar rural.
- No propone una arquitectura de software lista para implementar.
- No entrega un modelo de machine learning entrenado.
- No profundiza suficientemente en contextos como el VRAEM.
- Presenta resultados de varios estudios, pero no todos son comparables entre sí.
- No siempre analiza con detalle la calidad metodológica de cada estudio.
- No desarrolla profundamente problemas éticos, privacidad de datos o dependencia tecnológica.
- No aborda de forma específica la nivelación académica en estudiantes con rezago severo.

Por ello, debe usarse como referencia para diseñar indicadores de adaptación y no como guía completa de implementación.

## 11. Cómo se usará en el sistema

Este artículo se usará para justificar que el sistema debe adaptar la ruta del estudiante usando indicadores concretos, no solo notas generales.

Su aporte principal se aplicará en:

- Evaluación diagnóstica.
- Indicadores de activación adaptativa.
- Seguimiento del engagement.
- Registro de progreso.
- Detección de estudiantes con baja participación.
- Ajuste de rutas según conocimiento previo.
- Apoyo a la autorregulación del estudiante.

## 12. Aplicación directa al sistema VRAEM

En el sistema de nivelación académica adaptativa para estudiantes del VRAEM, este artículo permite definir que cada estudiante debe iniciar con una evaluación de conocimiento previo.

A partir de esa evaluación, el sistema debe:

- Identificar su nivel inicial.
- Detectar temas débiles.
- Asignar actividades de refuerzo.
- Medir su progreso.
- Registrar su participación.
- Detectar bajo engagement.
- Alertar al docente cuando el estudiante no avance.
- Ajustar la ruta según desempeño y actividad registrada.

Esto es importante porque los estudiantes con brechas de aprendizaje no solo necesitan contenido personalizado, sino también seguimiento de su participación y permanencia en el proceso.

## 13. Decisión técnica final derivada del Artículo 13

A partir de este artículo, el sistema debe implementar una lógica de adaptación basada en indicadores.

Esto significa que la ruta de nivelación debe activarse y modificarse según señales como conocimiento previo, progreso, participación, registros de actividad y nivel de confianza del estudiante.

La decisión técnica central es que el sistema debe medir tanto rendimiento como engagement, porque un estudiante puede tener dificultades no solo por bajo conocimiento, sino también por baja participación, poca confianza o falta de continuidad.

## 14. Instrucción para Cursor

Cuando desarrolles el sistema, usa este artículo para implementar indicadores que activen la personalización.

No hagas que la adaptación dependa solo del puntaje final. Considera también:

- Evaluación inicial.
- Actividades completadas.
- Actividades pendientes.
- Progreso por tema.
- Participación.
- Tiempo de uso.
- Nivel de confianza, si se registra.
- Continuidad del estudiante en la ruta.

El sistema debe reflejar la idea central del artículo: el aprendizaje adaptativo personalizado mejora cuando la plataforma ajusta la experiencia educativa usando indicadores reales del estudiante, especialmente rendimiento, progreso y engagement.