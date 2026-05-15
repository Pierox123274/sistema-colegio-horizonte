# ARTÍCULO 19 — FICHA TÉCNICA PARA CURSOR

## 1. Referencia

Vaarma, M.; Li, H. (2024).  
“Predicción del abandono escolar mediante aprendizaje automático: un estudio empírico en la educación superior finlandesa”.  
Revista: Technology in Society.  
Volumen: 76.  
Artículo: 102474.

## 2. Idea clave del artículo

El artículo analiza la predicción de la deserción estudiantil mediante machine learning, integrando datos demográficos, académicos y de actividad en plataformas LMS como Moodle.

La idea principal es que el riesgo académico no debe detectarse al final del periodo, cuando ya es tarde para intervenir, sino de forma temprana y continua. Para ello, el artículo propone analizar la evolución del estudiante a lo largo del tiempo, usando datos mensuales y modelos predictivos.

## 3. Aporte específico al sistema

Este artículo aporta la lógica de alerta temprana para detectar estudiantes en riesgo.

Su valor principal para el sistema es que permite justificar que la plataforma no solo debe mostrar el rendimiento actual del estudiante, sino también identificar señales de posible abandono, bajo compromiso o riesgo académico.

Este artículo ayuda a fortalecer:

- Predicción de riesgo académico.
- Seguimiento longitudinal del estudiante.
- Análisis de datos de uso de la plataforma.
- Alertas tempranas para el docente.
- Integración de datos académicos y comportamiento digital.
- Intervención antes de que el estudiante abandone la ruta de nivelación.

## 4. Decisión técnica derivada

A partir de este artículo, el sistema debe incorporar una lógica de detección temprana de riesgo.

Esto significa que el sistema debe analizar periódicamente datos como puntajes, actividades completadas, errores, inactividad y participación para identificar estudiantes que podrían abandonar o no avanzar adecuadamente en la nivelación.

La decisión técnica principal es que el riesgo académico debe calcularse de manera continua, no solo después de una evaluación final.

## 5. Datos que sugiere considerar

El artículo permite justificar el uso combinado de datos académicos, demográficos y de interacción digital.

Para el sistema, se pueden considerar:

- Puntaje académico.
- Evaluaciones desaprobadas.
- Temas con bajo rendimiento.
- Actividades completadas.
- Actividades no completadas.
- Actividades abandonadas.
- Frecuencia de ingreso.
- Tiempo de uso.
- Interacciones con la plataforma.
- Progreso mensual o semanal.
- Historial de errores.
- Nivel académico inicial.
- Edad o grado del estudiante.
- Institución educativa.
- Zona o contexto.
- Días de inactividad.
- Riesgo académico calculado.

Para el contexto VRAEM, los datos de actividad deben interpretarse con cuidado, porque baja participación también puede deberse a conectividad limitada.

## 6. Técnicas que respalda

El artículo respalda el uso de modelos de machine learning para clasificación y predicción de riesgo, entre ellos:

- Regresión logística.
- Random Forest.
- Support Vector Machine.
- Redes neuronales.
- Gradient Boosting.
- CatBoost.
- Análisis longitudinal.
- Permutation importance.
- Learning Analytics.
- Modelos predictivos con datos LMS.

Para el MVP del sistema, se recomienda iniciar con reglas simples o regresión logística/árboles de decisión. Luego, cuando exista más data, se puede evaluar Random Forest, Gradient Boosting o CatBoost.

## 7. Regla o lógica aplicable al sistema

Este artículo permite derivar las siguientes reglas funcionales:

Si el estudiante acumula varios días de inactividad, el sistema debe generar una alerta temprana.

Si el estudiante falla repetidamente actividades de un mismo tema, el sistema debe marcar riesgo académico en ese tema.

Si el estudiante reduce su frecuencia de uso durante varias semanas, el sistema debe registrar posible desvinculación.

Si el estudiante tiene bajo puntaje y baja participación, el sistema debe clasificarlo como riesgo alto.

Si el estudiante tiene bajo uso pero buen rendimiento cuando participa, el sistema debe marcar posible problema de acceso, no necesariamente bajo desempeño.

Si el riesgo se mantiene durante varias semanas, el sistema debe notificar al docente para intervención.

## 8. Métricas útiles

El artículo usa métricas de clasificación para evaluar modelos predictivos. Para el sistema, se pueden considerar:

- AUC.
- Average Precision.
- Precisión.
- Recall.
- F1-score.
- Exactitud de clasificación.
- Cantidad de estudiantes detectados en riesgo.
- Cantidad de alertas tempranas generadas.
- Cantidad de intervenciones docentes realizadas.
- Reducción de estudiantes en riesgo.
- Mejora después de una alerta.
- Progreso semanal o mensual.
- Días de inactividad.
- Actividades completadas después de la intervención.

En el sistema, el recall será especialmente importante porque interesa detectar la mayor cantidad posible de estudiantes en riesgo para que el docente pueda intervenir.

## 9. Resultado o evidencia relevante

El artículo compara diez algoritmos de machine learning y encuentra que los modelos con mejor rendimiento promedio fueron CatBoost, redes neuronales y regresión logística.

El mejor resultado general lo obtuvo CatBoost, con un AUC promedio de 0.853, Average Precision de 0.721, F1-score de 59%, precisión de 81% y recall de 47%.

También identifica variables relevantes para predecir riesgo o abandono, como:

- Créditos acumulados.
- Cursos desaprobados.
- Actividad en Moodle.
- Datos académicos previos.
- Datos demográficos.
- Evolución temporal del estudiante.

El aporte importante no es solo qué modelo predice mejor, sino que el riesgo cambia con el tiempo y debe monitorearse de forma continua.

## 10. Limitación del artículo

Aunque el artículo es útil para diseñar alertas tempranas, presenta algunas limitaciones:

- Está aplicado en educación superior finlandesa.
- No está contextualizado en educación escolar ni en el VRAEM.
- Se enfoca en deserción universitaria, no directamente en nivelación académica.
- Requiere datos históricos suficientes para entrenar buenos modelos.
- Los modelos pueden perder precisión si se aplican en contextos distintos.
- Los datos LMS pueden reflejar baja conectividad y no necesariamente falta de interés.
- No propone una arquitectura de software lista para implementar.
- No diseña actividades de refuerzo académico.

Por ello, el sistema debe usar este artículo como fundamento para alertas tempranas, pero adaptándolo al contexto escolar, rural y vulnerable.

## 11. Cómo se usará en el sistema

Este artículo se usará para justificar que el sistema debe detectar riesgo académico antes de que el estudiante abandone la ruta de nivelación.

Su aporte principal se aplicará en:

- Cálculo de riesgo académico.
- Alertas tempranas.
- Seguimiento longitudinal.
- Análisis de actividad del estudiante.
- Priorización de intervención docente.
- Identificación de estudiantes con bajo avance.
- Evaluación de modelos predictivos.

No se usará para repetir la idea general de aprendizaje adaptativo, sino para fortalecer la predicción temprana de estudiantes en riesgo.

## 12. Aplicación directa al sistema VRAEM

En el sistema de nivelación académica adaptativa para estudiantes del VRAEM, este artículo permite definir que la plataforma debe vigilar señales tempranas de abandono o bajo avance.

La plataforma debe:

- Registrar actividad semanal del estudiante.
- Detectar inactividad prolongada.
- Identificar bajo rendimiento persistente.
- Calcular nivel de riesgo académico.
- Generar alertas para el docente.
- Diferenciar riesgo académico de problema de conectividad.
- Recomendar intervención docente cuando el riesgo sea alto.
- Revisar si el estudiante mejora después de la intervención.

Esto es importante porque los estudiantes con brechas de aprendizaje pueden abandonar el proceso de nivelación si no reciben apoyo a tiempo.

## 13. Decisión técnica final derivada del Artículo 19

A partir de este artículo, el sistema debe implementar una lógica de alerta temprana basada en machine learning o reglas predictivas.

Esto significa que el sistema debe analizar de forma continua el rendimiento y la participación del estudiante para detectar riesgo académico antes de que el problema sea grave.

La decisión técnica central es que el sistema debe clasificar el riesgo del estudiante en niveles como:

- Riesgo bajo.
- Riesgo medio.
- Riesgo alto.

Esta clasificación debe ayudar al docente a intervenir oportunamente.

## 14. Instrucción para Cursor

Cuando desarrolles el sistema, usa este artículo para implementar el componente de predicción o detección temprana de riesgo académico.

No esperes hasta la evaluación final para identificar problemas. El sistema debe monitorear señales como:

- Bajo puntaje.
- Errores repetidos.
- Actividades incompletas.
- Inactividad.
- Disminución de participación.
- Bajo progreso.
- Abandono de actividades.

El sistema debe reflejar la idea central del artículo: los datos académicos y de interacción pueden usarse para detectar tempranamente estudiantes en riesgo y activar apoyo docente antes de que abandonen o fracasen en el proceso de aprendizaje.