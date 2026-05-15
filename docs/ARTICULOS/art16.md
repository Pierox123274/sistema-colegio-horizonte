# ARTÍCULO 16 — FICHA TÉCNICA PARA CURSOR

## 1. Referencia

Song, C.; Shin, S.-Y.; Shin, K.-S. (2024).  
“Implementación del marco de optimización del aprendizaje basado en retroalimentación dinámica: un enfoque de aprendizaje automático para personalizar las trayectorias educativas”.  
Revista: Applied Sciences.  
Volumen: 14.  
Artículo: 916.

## 2. Idea clave del artículo

El artículo propone el Dynamic Feedback-Driven Learning Optimization Framework (DFDLOF), un marco de optimización del aprendizaje basado en retroalimentación dinámica y machine learning.

La idea principal es que el aprendizaje personalizado no debe depender solo de una evaluación inicial, sino de un ciclo continuo donde el sistema recolecta datos del estudiante, analiza su desempeño, ofrece retroalimentación y ajusta la trayectoria educativa de forma progresiva.

## 3. Aporte específico al sistema

Este artículo aporta una lógica de adaptación continua para el sistema. Su valor principal es que permite diseñar el sistema como un proceso cíclico:

datos del estudiante → análisis con machine learning → retroalimentación → ajuste de actividades → nueva evaluación → actualización del perfil.

Este enfoque es útil para que el sistema no recomiende actividades solo una vez, sino que adapte constantemente la ruta de nivelación según el avance real del estudiante.

## 4. Decisión técnica derivada

A partir de este artículo, el sistema debe implementar una lógica de retroalimentación dinámica.

Esto significa que después de cada evaluación o actividad, el sistema debe:

- Registrar el resultado del estudiante.
- Analizar si mejoró, se mantuvo o empeoró.
- Actualizar su perfil académico.
- Ajustar la dificultad de las siguientes actividades.
- Recomendar nuevos contenidos de refuerzo.
- Generar alertas si el estudiante presenta bajo avance.
- Permitir que el docente supervise la evolución.

La decisión técnica principal es que la ruta de aprendizaje debe ser dinámica, no fija.

## 5. Datos que sugiere considerar

El artículo permite justificar el uso de datos relacionados con desempeño, participación y comportamiento dentro del sistema.

Para el sistema se deben considerar:

- Puntaje en evaluaciones.
- Respuestas correctas.
- Respuestas incorrectas.
- Temas fallados.
- Tiempo de resolución.
- Actividades completadas.
- Intentos por actividad.
- Nivel de dificultad alcanzado.
- Frecuencia de uso.
- Participación del estudiante.
- Progreso acumulado.
- Historial de retroalimentación.
- Cambios en el rendimiento.
- Riesgo académico.
- Necesidad de intervención docente.

## 6. Técnicas que respalda

El artículo respalda el uso de técnicas de machine learning aplicadas a la personalización del aprendizaje, como:

- Supervised learning para predicción del desempeño.
- Unsupervised learning para agrupar estudiantes según patrones.
- Reinforcement learning para ajustar rutas de aprendizaje después de cada interacción.
- Deep learning para analizar datos complejos de comportamiento.
- Predictive analytics para anticipar dificultades.
- Learning analytics para seguimiento del progreso.
- Performance tracking para monitoreo continuo.

Para el MVP del sistema, se recomienda implementar primero reglas de adaptación y modelos simples. Luego, cuando exista más información acumulada, se puede evolucionar hacia modelos predictivos o aprendizaje por refuerzo.

## 7. Regla o lógica aplicable al sistema

Este artículo permite derivar las siguientes reglas funcionales:

Si el estudiante mejora después de una actividad, el sistema debe aumentar progresivamente la dificultad.

Si el estudiante falla varias veces en el mismo tema, el sistema debe reducir la dificultad y recomendar refuerzo básico.

Si el estudiante muestra bajo progreso en varias sesiones, el sistema debe marcarlo como estudiante en riesgo.

Si el estudiante completa correctamente actividades consecutivas, el sistema debe permitir avanzar al siguiente tema.

Si el estudiante abandona actividades o participa poco, el sistema debe generar una alerta para el docente.

Si el sistema detecta que una recomendación no mejora el desempeño, debe ajustar la siguiente recomendación.

## 8. Métricas útiles

El artículo menciona variables relacionadas con engagement, efectividad del aprendizaje, seguimiento del rendimiento y capacidad de adaptación.

Para el sistema, se pueden considerar estas métricas:

- Mejora del puntaje entre evaluación inicial y evaluación posterior.
- Nivel de participación del estudiante.
- Cantidad de actividades completadas.
- Tiempo promedio de resolución.
- Progreso por tema.
- Reducción de errores.
- Reducción de brechas de aprendizaje.
- Frecuencia de uso del sistema.
- Cantidad de retroalimentaciones generadas.
- Cantidad de recomendaciones aceptadas o completadas.
- Estudiantes detectados en riesgo académico.
- Mejora después de recibir retroalimentación.
- Efectividad de las actividades recomendadas.

## 9. Resultado o evidencia relevante

El artículo sostiene que el DFDLOF mejora el compromiso del estudiante y la efectividad del aprendizaje porque combina contenido personalizado, retroalimentación en tiempo real y ajuste dinámico de trayectorias.

También indica que este enfoque puede superar modelos más rígidos, ya que no se limita a entregar contenidos predeterminados, sino que adapta continuamente la experiencia educativa según el desempeño y comportamiento del estudiante.

Sin embargo, los resultados se presentan principalmente de forma conceptual y descriptiva, no como una validación experimental amplia con muchas métricas numéricas comparativas.

## 10. Limitación del artículo

Aunque el artículo es útil para orientar el diseño del sistema, presenta algunas limitaciones:

- Es más fuerte en el plano conceptual que en la validación empírica.
- No presenta una implementación específica para el contexto VRAEM.
- No entrega una arquitectura de software lista para copiar.
- No define una base de datos concreta.
- No proporciona un modelo entrenado aplicable directamente.
- No profundiza suficiente en conectividad limitada o ruralidad.
- Menciona privacidad y ética de datos, pero no desarrolla profundamente esos riesgos.
- Sus ejemplos principales se relacionan con plataformas como Khan Academy y Coursera, que tienen contextos distintos al VRAEM.

Por ello, el sistema debe tomar la lógica de retroalimentación dinámica, pero adaptarla a un entorno más simple, viable y contextualizado.

## 11. Cómo se usará en el sistema

Este artículo se usará para justificar que el sistema debe tener un ciclo de mejora continua.

Su aporte principal se aplicará en la lógica de actualización del perfil del estudiante, ajuste de dificultad, retroalimentación automática y modificación de la ruta de aprendizaje según el progreso.

No se usará para repetir la idea general de aprendizaje adaptativo, sino específicamente para reforzar que la adaptación debe ser continua y basada en feedback.

## 12. Aplicación directa al sistema VRAEM

En el sistema de nivelación académica para estudiantes del VRAEM, este artículo permite definir que la plataforma debe reaccionar al avance real del estudiante.

La plataforma debe:

- Evaluar al estudiante.
- Recomendar una actividad.
- Medir el resultado.
- Dar retroalimentación.
- Ajustar la siguiente actividad.
- Actualizar el perfil.
- Alertar al docente si el estudiante no mejora.

Esto es importante porque los estudiantes con brechas de aprendizaje no pueden seguir una ruta fija; necesitan una trayectoria flexible que cambie según su avance.

## 13. Decisión técnica final derivada del Artículo 16

A partir de este artículo, el sistema debe implementar un ciclo de retroalimentación dinámica.

Esto significa que cada interacción del estudiante debe generar datos que permitan ajustar la ruta de nivelación. El sistema debe aprender del progreso del estudiante y modificar sus recomendaciones de manera continua.

La decisión técnica central es que la nivelación académica debe funcionar como un proceso iterativo:

diagnóstico → recomendación → actividad → retroalimentación → actualización → nueva recomendación.

## 14. Instrucción para Cursor

Cuando desarrolles el sistema, usa este artículo para implementar la lógica de adaptación continua.

No hagas que la ruta de aprendizaje sea estática. Después de cada actividad o evaluación, el sistema debe revisar el desempeño del estudiante y decidir si corresponde:

- Mantener el nivel.
- Subir la dificultad.
- Bajar la dificultad.
- Recomendar refuerzo.
- Cambiar de tema.
- Alertar al docente.
- Actualizar el perfil del estudiante.

El sistema debe reflejar la idea central del artículo: el aprendizaje personalizado mejora cuando existe retroalimentación dinámica y ajuste continuo basado en datos.