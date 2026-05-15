# ARTÍCULO 34 — FICHA TÉCNICA PARA CURSOR

## 1. Referencia

Dai, W.; Lin, J.; Jin, F. J.-Y.; Tsai, Y.-S.; Srivastava, N.; Le Bodic, P.; Gašević, D.; Chen, G. (2025).  
“Learning Analytics for Early Identification of At-Risk Students and Feedback Intervention”.  
Revista: Journal of Learning Analytics.  
Volumen/Número: 12(3).  
Páginas: 102–125.

## 2. Idea clave del artículo

El artículo plantea que la analítica del aprendizaje no debe limitarse a predecir qué estudiantes están en riesgo académico. Su idea principal es que la predicción temprana debe estar acompañada de una intervención pedagógica concreta, clara y motivadora.

El estudio combina modelos predictivos de machine learning con correos de retroalimentación relacional enviados a estudiantes identificados como en riesgo. Esta retroalimentación busca aclarar el desempeño del estudiante, indicar acciones concretas, promover comunicación con docentes y compañeros, y generar una sensación de apoyo.

## 3. Aporte específico al sistema

Este artículo aporta una lógica importante para el sistema: detectar estudiantes en riesgo no es suficiente; el sistema también debe intervenir.

Su aporte principal es fortalecer la parte de:

- Identificación temprana de estudiantes en riesgo.
- Alertas académicas accionables.
- Retroalimentación personalizada.
- Mensajes de apoyo al estudiante.
- Seguimiento del comportamiento después de la intervención.
- Intervención docente basada en datos.

Este artículo complementa los artículos sobre predicción de riesgo, porque agrega el componente de acción pedagógica después de la predicción.

## 4. Decisión técnica derivada

A partir de este artículo, el sistema debe implementar una lógica de intervención después de detectar riesgo académico.

Esto significa que, cuando el sistema clasifique a un estudiante como riesgo medio o alto, no debe limitarse a mostrar una alerta. También debe generar una acción concreta, como:

- Mensaje personalizado al estudiante.
- Recomendación de actividades pendientes.
- Recordatorio de temas no revisados.
- Invitación a solicitar apoyo docente.
- Sugerencia de reforzar temas específicos.
- Registro de seguimiento posterior a la intervención.

La decisión técnica principal es que toda alerta de riesgo debe estar asociada a una intervención.

## 5. Datos que sugiere considerar

El artículo permite justificar el uso de datos de comportamiento en plataformas educativas y datos académicos previos.

Para el sistema, se pueden considerar:

- Frecuencia de ingreso.
- Número de sesiones.
- Duración total de sesiones.
- Días activos.
- Semanas activas.
- Actividades visitadas.
- Actividades no visitadas.
- Actividades pendientes.
- Participación en foros o espacios de consulta.
- Revisión de calificaciones.
- Uso de materiales.
- Entrega de tareas.
- Regularidad de estudio.
- Puntaje académico.
- Historial académico previo.
- Riesgo académico estimado.
- Cambios de participación después de recibir retroalimentación.

Para el contexto VRAEM, estos datos deben interpretarse junto con posibles problemas de conectividad o acceso.

## 6. Técnicas que respalda

El artículo respalda el uso de machine learning y learning analytics para identificación temprana de riesgo.

Técnicas o enfoques útiles para el sistema:

- Learning Analytics.
- Modelos predictivos.
- Regresión logística.
- Random Forest.
- Gradient Boosting Tree.
- Clasificación de estudiantes en riesgo.
- Análisis de importancia de características.
- Analítica orientada a la acción.
- Retroalimentación relacional.
- Evaluación de generalización entre cohortes.
- Seguimiento del comportamiento posterior a la intervención.

Para el MVP, se puede iniciar con reglas de riesgo y mensajes automáticos. Luego, cuando exista suficiente información, se puede incorporar un modelo como Random Forest o Gradient Boosting.

## 7. Regla o lógica aplicable al sistema

Este artículo permite derivar las siguientes reglas funcionales:

Si el estudiante presenta baja participación y bajo rendimiento, el sistema debe clasificarlo como estudiante en riesgo.

Si el estudiante es clasificado como riesgo alto, el sistema debe generar una intervención inmediata, no solo una alerta.

Si el estudiante tiene actividades pendientes, el mensaje debe indicar cuáles son y recomendar completarlas.

Si el estudiante no revisó ciertos recursos, el sistema debe sugerirlos como parte del refuerzo.

Si el estudiante mantiene baja participación después de la intervención, el sistema debe notificar al docente.

Si el estudiante mejora su participación después de la retroalimentación, el sistema debe registrar la intervención como efectiva.

Si el sistema se aplica en una nueva cohorte o grupo, el modelo debe ser revisado o actualizado para evitar pérdida de precisión.

## 8. Métricas útiles

El artículo usa métricas de predicción y métricas de intervención. Para el sistema, se pueden considerar:

- Accuracy.
- Macro F1-score.
- AUC.
- NPV.
- TNR.
- Cantidad de estudiantes identificados en riesgo.
- Cantidad de intervenciones generadas.
- Actividades completadas después de la intervención.
- Actividades visitadas después de la intervención.
- Cambio en la frecuencia de ingreso.
- Cambio en el tiempo de uso.
- Mejora del rendimiento después de la retroalimentación.
- Porcentaje de estudiantes que responden a la intervención.
- Satisfacción del estudiante con la retroalimentación.
- Intervenciones que requieren seguimiento docente.

## 9. Resultado o evidencia relevante

El artículo muestra que los modelos entrenados y evaluados dentro del mismo semestre obtuvieron AUC de 0.98 usando datos de semanas 0 a 4 y también 0.98 usando datos de semanas 0 a 7.

Cuando el modelo fue entrenado con datos del semestre 2022 y aplicado al semestre 2023, el rendimiento bajó, pero se mantuvo aceptable, con AUC de 0.72 para semanas 0 a 4 y AUC de 0.78 para semanas 0 a 7.

Respecto a la intervención, en la semana 5 se identificaron 239 estudiantes en riesgo. Antes del correo, estos estudiantes habían visitado en promedio 2.69 actividades de 19 requeridas. Una semana después, el promedio subió a 3.66 y dos semanas después a 3.92.

Además, 76 estudiantes, equivalentes al 31.80%, visitaron al menos una actividad no revisada dentro de las dos semanas posteriores a la intervención.

Estos resultados muestran que la retroalimentación puede generar cambios positivos en la participación, aunque no todos los estudiantes responden de la misma manera.

## 10. Limitación del artículo

Aunque el artículo es útil para orientar el sistema, presenta algunas limitaciones:

- Está aplicado en educación superior, no directamente en educación escolar rural.
- El contexto es un curso universitario de algoritmos y programación.
- No está contextualizado en la zona VRAEM.
- El modelo pierde precisión cuando se aplica a una cohorte distinta.
- La intervención no tuvo el mismo impacto en todos los estudiantes.
- Solo alrededor de un tercio de estudiantes en riesgo visitó actividades no revisadas después del correo.
- La encuesta tuvo una tasa de respuesta baja, 9.27%.
- No propone una arquitectura completa de software.
- No diseña actividades de nivelación académica para estudiantes con rezago escolar.
- Depende de datos de plataformas como Ed y Moodle, que pueden no existir inicialmente en el sistema VRAEM.

Por ello, el sistema debe tomar la lógica de predicción + intervención, pero adaptarla a una plataforma simple, contextualizada y viable.

## 11. Cómo se usará en el sistema

Este artículo se usará para justificar que el sistema debe convertir las alertas de riesgo en acciones concretas.

No se usará solo para repetir la idea de predicción académica, sino para fortalecer el componente de intervención posterior.

Su aporte principal se aplicará en:

- Alertas tempranas.
- Retroalimentación personalizada.
- Mensajes automáticos al estudiante.
- Registro de actividades pendientes.
- Seguimiento después de una intervención.
- Notificación al docente.
- Evaluación de efectividad de la retroalimentación.

## 12. Aplicación directa al sistema VRAEM

En el sistema de nivelación académica adaptativa para estudiantes del VRAEM, este artículo permite definir que un estudiante en riesgo no debe recibir solo una etiqueta como “riesgo alto”.

La plataforma debe:

- Detectar bajo rendimiento o baja participación.
- Identificar actividades no completadas.
- Generar un mensaje claro y motivador.
- Recomendar acciones específicas.
- Invitar al estudiante a pedir apoyo docente.
- Notificar al docente si el riesgo continúa.
- Medir si el estudiante mejora después del mensaje.
- Diferenciar baja participación por desinterés de baja participación por problemas de conectividad.

Esto es importante porque en contextos vulnerables el sistema debe actuar de manera orientadora y no sancionadora.

## 13. Decisión técnica final derivada del Artículo 34

A partir de este artículo, el sistema debe implementar una lógica de alerta temprana con intervención relacional.

Esto significa que cada vez que el sistema detecte riesgo académico, debe generar una intervención personalizada, positiva y accionable.

La decisión técnica central es que el sistema debe seguir este ciclo:

detección de riesgo → mensaje de retroalimentación → recomendación de acción → seguimiento posterior → intervención docente si no hay mejora.

## 14. Instrucción para Cursor

Cuando desarrolles el sistema, usa este artículo para implementar alertas que generen acciones reales.

No hagas que el sistema solo muestre “estudiante en riesgo”. Cada alerta debe incluir:

- Motivo del riesgo.
- Actividades pendientes.
- Tema o competencia con dificultad.
- Recomendación concreta.
- Mensaje motivador.
- Opción de solicitar apoyo docente.
- Seguimiento después de la intervención.

El sistema debe reflejar la idea central del artículo: la analítica del aprendizaje es más útil cuando no solo predice el riesgo, sino que activa una retroalimentación oportuna, humana y pedagógicamente diseñada.