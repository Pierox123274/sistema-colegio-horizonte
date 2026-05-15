# ARTÍCULO 20 — FICHA TÉCNICA PARA CURSOR

## 1. Referencia

McIntyre, N. A. (2023).  
“Acceso al aprendizaje en línea: análisis del aprendizaje automático desde una perspectiva de justicia social”.  
Revista: Education and Information Technologies.  
Volumen: 28.  
Páginas: 3787–3832.

## 2. Idea clave del artículo

El artículo analiza el acceso al aprendizaje en línea desde una perspectiva de justicia social, usando machine learning explicable sobre datos masivos de una plataforma educativa.

La idea principal es que el aprendizaje en línea no garantiza automáticamente igualdad de oportunidades. Aunque las plataformas digitales pueden ampliar el acceso educativo, también pueden reproducir o intensificar desigualdades si no se consideran factores como país, género, conectividad, alfabetización digital, contexto socioeconómico y condiciones del hogar.

## 3. Aporte específico al sistema

Este artículo aporta una mirada crítica y social al sistema. Su valor principal es recordar que un sistema inteligente de nivelación académica no debe centrarse únicamente en predecir rendimiento o recomendar actividades, sino también en asegurar acceso justo y uso equitativo.

Para el sistema VRAEM, este artículo es importante porque el contexto presenta limitaciones reales de conectividad, recursos tecnológicos, brechas digitales y condiciones socioeconómicas. Por eso, el sistema debe diseñarse considerando accesibilidad, simplicidad, bajo consumo de recursos y apoyo docente.

## 4. Decisión técnica derivada

A partir de este artículo, el sistema debe incorporar criterios de equidad y accesibilidad desde el diseño.

Esto significa que el sistema debe:

- Funcionar con una interfaz simple.
- Evitar depender de conexión permanente.
- Priorizar contenidos livianos.
- Registrar dificultades de acceso.
- Detectar baja participación no solo como desinterés, sino como posible problema de conectividad o contexto.
- Evitar que el modelo de machine learning penalice injustamente a estudiantes con menor acceso digital.
- Permitir intervención docente cuando el bajo uso del sistema pueda deberse a barreras externas.

La decisión técnica principal es que el sistema debe interpretar los datos con contexto, no solo como números.

## 5. Datos que sugiere considerar

El artículo permite justificar que el sistema no debe recolectar solo datos académicos. También debe considerar variables de acceso y contexto.

Para el sistema, se pueden considerar:

- Puntaje académico.
- Tiempo de uso.
- Frecuencia de ingreso.
- Actividades completadas.
- Actividades abandonadas.
- Tiempo empleado por sesión.
- Nivel de dificultad de las actividades.
- Edad o grado del estudiante.
- Género, si el estudio lo permite éticamente.
- Zona o institución educativa.
- Disponibilidad de conectividad.
- Tipo de dispositivo utilizado.
- Problemas reportados de acceso.
- Participación antes y después de intervenciones.
- Periodos de inactividad.
- Necesidad de apoyo docente.

Importante:
Las variables sensibles, como género o contexto socioeconómico, no deben usarse para discriminar ni reducir oportunidades. Deben usarse solo para detectar brechas y mejorar la equidad del sistema.

## 6. Técnicas que respalda

El artículo respalda el uso de machine learning con enfoque explicable y socialmente responsable.

Técnicas o enfoques útiles para el sistema:

- Machine learning explicable.
- XGBoost.
- Shapley values o SHAP para interpretar variables.
- Modelos predictivos.
- Análisis de importancia de variables.
- Minería de datos educativos.
- Human-in-the-loop.
- Análisis de acceso y participación.
- Modelos guiados por teoría y datos.
- Analítica educativa con enfoque de equidad.

Para el MVP, no es necesario implementar SHAP desde el inicio. Pero sí se debe preparar el sistema para explicar por qué se clasifica a un estudiante como en riesgo o por qué se recomienda determinada acción.

## 7. Regla o lógica aplicable al sistema

Este artículo permite derivar las siguientes reglas funcionales:

Si un estudiante tiene baja participación, el sistema no debe asumir automáticamente bajo interés o bajo rendimiento. Debe registrar posible problema de acceso.

Si un estudiante no completa actividades por varios días, el sistema debe generar una alerta para que el docente verifique si existe dificultad de conectividad, dispositivo o apoyo familiar.

Si el estudiante usa poco la plataforma pero obtiene buen rendimiento cuando participa, el sistema debe diferenciar entre bajo acceso y bajo desempeño académico.

Si varios estudiantes de una misma zona o institución presentan baja participación, el sistema debe generar un reporte de posible brecha de acceso.

Si el modelo usa variables sensibles, el sistema debe tratarlas solo como indicadores de equidad y no como factores para limitar recomendaciones.

Si el sistema detecta estudiantes con baja conectividad, debe priorizar actividades livianas, descargables o de menor dependencia tecnológica.

## 8. Métricas útiles

El artículo usa métricas de machine learning y análisis de importancia de variables. Para el sistema, se pueden considerar:

- Frecuencia de acceso a la plataforma.
- Tiempo promedio de uso.
- Actividades iniciadas.
- Actividades completadas.
- Actividades abandonadas.
- Periodos de inactividad.
- Mejora del rendimiento según nivel de acceso.
- Participación por zona o institución.
- Participación por género, si se recolecta éticamente.
- Reportes de problemas de conectividad.
- Estudiantes con riesgo por bajo acceso.
- Estudiantes con riesgo por bajo rendimiento.
- Exactitud del modelo predictivo.
- Importancia de variables en la predicción.
- Explicabilidad de recomendaciones.
- Reducción de brechas de acceso.

## 9. Resultado o evidencia relevante

El artículo muestra que el acceso al aprendizaje en línea está condicionado por múltiples factores. No depende solo de tener una plataforma disponible.

La autora identifica que variables como país, género, contexto COVID-19, habilidad matemática, edad, nivel de dificultad y tiempo empleado pueden influir en el acceso al aprendizaje en línea.

También demuestra que los modelos guiados únicamente por teoría pueden ser insuficientes, por lo que es útil combinar teoría, datos y machine learning explicable para comprender fenómenos complejos de desigualdad educativa.

## 10. Limitación del artículo

Aunque el artículo es muy útil para una tesis sobre desigualdad educativa, presenta algunas limitaciones:

- Trabaja con datos de una sola plataforma: Maths-Whizz Tutor.
- Usa una submuestra de 5000 registros tomada de una base mucho mayor.
- No propone una arquitectura de software lista para implementar.
- No desarrolla directamente un sistema de nivelación académica.
- No está contextualizado en el VRAEM.
- Se centra más en el acceso al aprendizaje en línea que en la recomendación adaptativa de actividades.
- No incorpora completamente factores cualitativos como apoyo familiar, emociones, motivación o condiciones culturales.
- Es más fuerte en diagnóstico de desigualdad que en propuestas técnicas concretas de intervención.

Por ello, el sistema debe usar este artículo como fundamento para diseñar una plataforma más justa, accesible y sensible al contexto, no como una guía completa de implementación.

## 11. Cómo se usará en el sistema

Este artículo se usará para justificar que el sistema debe considerar la equidad y el acceso como parte de la lógica inteligente.

No se usará principalmente para repetir la idea de aprendizaje adaptativo, sino para reforzar que la personalización debe tener en cuenta las condiciones reales del estudiante.

Su aporte principal se aplicará en:

- Diseño accesible.
- Registro de problemas de conectividad.
- Detección de baja participación.
- Alertas de posible brecha de acceso.
- Explicabilidad del modelo.
- Interpretación responsable de datos.
- Seguimiento docente contextualizado.

## 12. Aplicación directa al sistema VRAEM

En el sistema de nivelación académica para estudiantes del VRAEM, este artículo permite definir que el bajo uso de la plataforma puede deberse a desigualdad de acceso, no necesariamente a falta de interés.

La plataforma debe:

- Registrar cuándo el estudiante no ingresa.
- Diferenciar bajo rendimiento de bajo acceso.
- Alertar al docente ante inactividad prolongada.
- Permitir registrar problemas de conectividad o dispositivo.
- Usar contenidos livianos.
- Evitar dashboards que comparen negativamente a estudiantes con menos acceso.
- Priorizar apoyo docente para estudiantes con barreras tecnológicas.
- Usar datos sensibles solo para detectar brechas y mejorar la equidad.

Esto es importante porque en el VRAEM pueden existir limitaciones tecnológicas, económicas y sociales que afectan el uso del sistema.

## 13. Decisión técnica final derivada del Artículo 20

A partir de este artículo, el sistema debe implementar una lógica de aprendizaje adaptativo con enfoque de justicia social.

Esto significa que el sistema no debe analizar únicamente el rendimiento académico, sino también las condiciones de acceso y participación del estudiante.

La decisión técnica central es diferenciar entre:

- Estudiante con bajo rendimiento académico.
- Estudiante con bajo acceso tecnológico.
- Estudiante con ambas dificultades.

Esa diferenciación permitirá que las recomendaciones sean más justas y contextualizadas.

## 14. Instrucción para Cursor

Cuando desarrolles el sistema, usa este artículo para evitar que el modelo de machine learning sea injusto o descontextualizado.

No clasifiques automáticamente a un estudiante como “bajo desempeño” solo porque usa poco la plataforma. Antes, considera señales de acceso:

- Pocas sesiones iniciadas.
- Actividades abandonadas.
- Tiempo de conexión bajo.
- Reportes de problemas de internet.
- Uso irregular por zona o institución.
- Inactividad prolongada.

El sistema debe reflejar la idea central del artículo: la tecnología educativa solo reduce desigualdades si se diseña considerando las condiciones reales de acceso, contexto y justicia social.