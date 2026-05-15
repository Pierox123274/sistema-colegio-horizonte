# ARTÍCULO 14 — FICHA TÉCNICA PARA CURSOR

## 1. Referencia

Tan, L. Y.; Hu, S.; Yeo, D. J.; Cheong, K. H. (2025).  
“Artificial intelligence-enabled adaptive learning platforms: A review”.  
Revista: Computers and Education: Artificial Intelligence.  
Volumen: 9.  
Artículo: 100429.

## 2. Idea clave del artículo

El artículo analiza las plataformas de aprendizaje adaptativo potenciadas por inteligencia artificial, conocidas como Adaptive Learning Platforms —ALPs—. Estas plataformas permiten ajustar dinámicamente el contenido, la evaluación, la secuencia de aprendizaje y la retroalimentación según las características y necesidades individuales del estudiante.

La idea principal del artículo es que una plataforma adaptativa no debe limitarse a mostrar contenido, sino que debe funcionar mediante una estructura inteligente compuesta por tres elementos: learner model, domain model y adaptation model.

## 3. Aporte específico al sistema

Este artículo aporta una base técnica y conceptual para organizar la lógica adaptativa del sistema. Su principal contribución es explicar que una plataforma inteligente debe separar claramente:

- El modelo del estudiante.
- El modelo del dominio o contenido.
- El modelo de adaptación.

Esto sirve para que el sistema de nivelación académica no funcione como una plataforma simple de actividades, sino como una solución capaz de analizar al estudiante, organizar los contenidos y decidir qué actividad, evaluación o ruta debe recibir.

## 4. Decisión técnica derivada

A partir de este artículo, el sistema debe estructurar su lógica adaptativa en tres capas:

1. Learner model:
   Representa el perfil del estudiante.

2. Domain model:
   Representa los cursos, temas, competencias, preguntas y actividades.

3. Adaptation model:
   Decide qué contenido, evaluación, dificultad o secuencia recomendar al estudiante.

La decisión técnica principal es que la personalización debe aplicarse en tres niveles: contenido adaptativo, evaluación adaptativa y secuencia adaptativa.

## 5. Datos que sugiere considerar

El artículo permite justificar el uso de datos más amplios para construir el perfil del estudiante. Para el sistema, se pueden considerar:

- Datos demográficos básicos.
- Nivel académico inicial.
- Conocimiento previo.
- Respuestas en evaluaciones.
- Puntaje obtenido.
- Tiempo de interacción.
- Actividades realizadas.
- Progreso por tema.
- Preferencias de aprendizaje.
- Motivación.
- Autoeficacia.
- Comportamiento dentro de la plataforma.
- Temas dominados.
- Temas con dificultad.
- Historial de retroalimentación.
- Riesgo académico.

Para el MVP, no es necesario usar sensores ni datos fisiológicos. Se recomienda iniciar con datos académicos y de interacción simple.

## 6. Técnicas que respalda

El artículo respalda el uso de técnicas de inteligencia artificial y machine learning como:

- Random Forest.
- Fuzzy C-Means.
- Bayesian Networks.
- Deep Knowledge Tracing.
- Item Response Theory.
- Matrix Factorization.
- Reinforcement Learning.
- Collaborative Filtering.
- LSTM.
- Genetic Algorithms.
- Ant Colony Optimization.
- Large Language Models.
- Sistemas de recomendación.
- Learning Analytics.

Para el sistema de tesis, se recomienda iniciar con modelos interpretables y luego dejar preparada la arquitectura para incorporar técnicas más avanzadas.

## 7. Regla o lógica aplicable al sistema

Este artículo permite derivar la siguiente lógica funcional:

Si el estudiante no domina un tema, el sistema no debe permitir que avance directamente a contenidos más complejos sin antes recomendar actividades de refuerzo.

Si el estudiante responde correctamente varias actividades de un mismo tema, el sistema puede aumentar la dificultad o avanzar al siguiente contenido.

Si el estudiante falla repetidamente en una competencia, el sistema debe modificar la secuencia de aprendizaje y priorizar contenidos previos.

Si el estudiante muestra bajo progreso o baja participación, el sistema debe generar una alerta para el docente.

Si el estudiante mejora después de actividades adaptativas, el sistema debe actualizar su perfil y ajustar su ruta de aprendizaje.

## 8. Tipos de adaptación que debe considerar el sistema

El artículo identifica tres formas principales de adaptación que pueden aplicarse al sistema:

### Adaptive content

El sistema recomienda contenidos o actividades según el nivel y las brechas del estudiante.

Ejemplo:
Si el estudiante falla en comprensión lectora inferencial, el sistema recomienda ejercicios básicos de inferencia antes de avanzar a textos más complejos.

### Adaptive assessment

El sistema adapta la dificultad de las preguntas según el desempeño del estudiante.

Ejemplo:
Si el estudiante responde correctamente preguntas básicas, se le presentan preguntas de dificultad media. Si falla, se mantiene o reduce la dificultad.

### Adaptive sequencing

El sistema modifica el orden de los contenidos según las necesidades del estudiante.

Ejemplo:
Si el estudiante tiene problemas en operaciones básicas, el sistema debe reforzar ese tema antes de recomendar problemas matemáticos más avanzados.

## 9. Métricas útiles

El artículo menciona métricas usadas para evaluar plataformas adaptativas. Para el sistema, se pueden considerar:

- Learning gains o mejora del aprendizaje.
- Comparación entre pre-test y post-test.
- Satisfacción del estudiante.
- Engagement o participación.
- Motivación del estudiante.
- Progreso académico.
- Reducción de errores.
- Finalización de actividades.
- Mejora en evaluaciones posteriores.
- Tiempo de avance en la ruta.
- Cantidad de estudiantes que superan una brecha.
- Nivel de uso de la plataforma.
- Alertas de riesgo académico atendidas por el docente.

Estas métricas sirven para evaluar si la nivelación adaptativa realmente mejora el aprendizaje.

## 10. Resultados o evidencia relevante

El artículo reporta que las plataformas adaptativas potenciadas por IA suelen mostrar impactos positivos en el rendimiento académico, la motivación, el engagement y la satisfacción de los estudiantes.

También menciona ejemplos donde los estudiantes que usaron plataformas adaptativas obtuvieron mejores resultados que grupos con enseñanza tradicional o no adaptativa.

Sin embargo, el artículo reconoce que no todos los estudios presentan mejoras iguales. Los resultados dependen de factores como la duración de la intervención, la calidad de los datos, el diseño pedagógico, la infraestructura tecnológica y el contexto educativo.

## 11. Limitación del artículo

Aunque el artículo es muy útil para orientar el diseño del sistema, presenta algunas limitaciones:

- Es una revisión general, no una implementación específica para el VRAEM.
- No propone una arquitectura de software lista para copiar.
- No entrega un modelo entrenado para nivelación académica.
- Analiza muchas plataformas, pero no siempre compara con profundidad cuál funciona mejor.
- Algunas técnicas mencionadas requieren muchos datos e infraestructura.
- No profundiza lo suficiente en contextos rurales o con conectividad limitada.
- No aborda directamente estudiantes con rezago académico severo.
- Algunas tecnologías futuras, como sensores, AR, VR o LLMs, pueden ser costosas para un MVP.

Por ello, el sistema debe tomar las ideas principales del artículo, pero adaptarlas a una solución simple, viable y contextualizada para estudiantes del VRAEM.

## 12. Cómo se usará en el sistema

Este artículo se usará para justificar que el sistema debe tener una arquitectura adaptativa basada en tres modelos:

- Modelo del estudiante.
- Modelo del dominio.
- Modelo de adaptación.

Su aporte principal se aplicará en la organización interna de la lógica inteligente del sistema. Además, permitirá justificar que la personalización no debe limitarse a recomendar actividades, sino también adaptar la dificultad, la evaluación, la secuencia de contenidos y la retroalimentación.

## 13. Aplicación directa al sistema VRAEM

En el sistema de nivelación académica para estudiantes del VRAEM, este artículo permite definir que cada estudiante debe tener una ruta personalizada según su desempeño real.

La plataforma debe:

- Diagnosticar el nivel del estudiante.
- Construir un perfil académico.
- Organizar contenidos por tema, competencia y dificultad.
- Recomendar actividades según brechas detectadas.
- Adaptar la dificultad de las evaluaciones.
- Cambiar la secuencia de aprendizaje según el progreso.
- Mostrar retroalimentación.
- Permitir al docente monitorear estudiantes en riesgo.

## 14. Decisión técnica final derivada del Artículo 14

A partir de este artículo, el sistema debe implementar una lógica de plataforma adaptativa basada en learner model, domain model y adaptation model.

Esto significa que el sistema debe separar claramente los datos del estudiante, la estructura de los contenidos y las reglas o modelos que deciden la adaptación.

La adaptación debe aplicarse en tres niveles: contenido, evaluación y secuencia. Esta decisión permitirá que el sistema no sea una plataforma estática, sino una herramienta inteligente de nivelación académica adaptativa.

## 15. Instrucción para Cursor

Cuando desarrolles el sistema, usa este artículo como fundamento para organizar la arquitectura adaptativa.

No mezcles toda la lógica en una sola función. Separa claramente:

- La información del estudiante.
- La información de los temas, competencias y actividades.
- La lógica que decide qué recomendar.
- La lógica que adapta la dificultad.
- La lógica que actualiza el progreso.

El sistema debe reflejar la idea central del artículo: una plataforma adaptativa basada en inteligencia artificial debe analizar al estudiante, organizar el conocimiento y adaptar la experiencia de aprendizaje de manera continua.