# ARTÍCULO 03 — FICHA TÉCNICA PARA CURSOR

## 1. Referencia

Bayounes, W.; Bayoudh Saâdi, I.; Kinshuk. (2022).  
“Aprendizaje adaptativo: hacia un modelo intencional para la orientación del proceso de aprendizaje basado en la motivación del alumno”.  
Revista: Entornos de aprendizaje inteligentes.  
Volumen/Número: 9(1).  
Páginas: 33–33.

## 2. Idea clave del artículo

El artículo propone un modelo intencional para guiar el proceso de aprendizaje dentro de sistemas tutores inteligentes, incorporando la motivación del estudiante como un factor central de adaptación.

La idea principal es que un sistema adaptativo no debe considerar únicamente el rendimiento académico o el contenido, sino también la motivación del estudiante. Para ello, el artículo se apoya en el modelo ARCS, que organiza la motivación en cuatro dimensiones:

- Atención.
- Relevancia.
- Confianza.
- Satisfacción.

## 3. Aporte específico al sistema

Este artículo aporta la necesidad de considerar factores motivacionales dentro del sistema de nivelación académica adaptativa.

Su valor principal es que permite diseñar una adaptación más humana, donde el sistema no solo recomiende actividades por bajo rendimiento, sino también según señales de desmotivación, baja confianza o poca participación.

Este artículo ayuda a fortalecer:

- Retroalimentación motivacional.
- Selección de estrategias de aprendizaje.
- Acompañamiento del estudiante.
- Adaptación según confianza y participación.
- Mensajes de refuerzo positivo.
- Prevención del abandono de actividades.

## 4. Decisión técnica derivada

A partir de este artículo, el sistema debe incluir una dimensión motivacional dentro del perfil del estudiante.

Esto significa que el sistema no debe registrar solo puntajes y errores, sino también señales asociadas a motivación, como participación, continuidad, confianza percibida o abandono de actividades.

La decisión técnica principal es que las recomendaciones deben considerar tanto el desempeño académico como el estado motivacional del estudiante.

## 5. Datos que sugiere considerar

El artículo permite justificar el uso de datos motivacionales y de comportamiento.

Para el sistema, se pueden considerar:

- Nivel de participación.
- Actividades completadas.
- Actividades abandonadas.
- Frecuencia de ingreso.
- Tiempo de permanencia.
- Intentos fallidos.
- Progreso por tema.
- Nivel de confianza declarado por el estudiante.
- Satisfacción con la actividad.
- Dificultad percibida.
- Respuestas correctas e incorrectas.
- Necesidad de apoyo.
- Historial de retroalimentación.
- Reacción ante actividades difíciles.

Para el MVP, la motivación puede medirse con indicadores simples, como frecuencia de uso, abandono de actividades y una pregunta breve de autopercepción.

## 6. Técnicas o enfoques que respalda

El artículo respalda enfoques relacionados con sistemas tutores inteligentes y adaptación motivacional.

Para el sistema, respalda:

- Modelo ARCS de motivación.
- Sistemas tutores inteligentes.
- Modelado del estudiante.
- Adaptación basada en motivación.
- Selección de estrategias pedagógicas.
- Guía intencional del aprendizaje.
- Formalismo Map.
- Modelo Felder-Silverman de estilos de aprendizaje.
- Estrategias cognitivas, metacognitivas, sociales y afectivas.
- Reglas de selección de intención y estrategia.

Este artículo es más útil para diseñar la lógica motivacional y pedagógica del sistema que para seleccionar un algoritmo predictivo específico.

## 7. Regla o lógica aplicable al sistema

Este artículo permite derivar las siguientes reglas funcionales:

Si el estudiante abandona varias actividades, el sistema debe generar una recomendación motivacional y reducir temporalmente la dificultad.

Si el estudiante muestra baja confianza en un tema, el sistema debe recomendar actividades básicas con retroalimentación positiva.

Si el estudiante completa actividades de manera constante, el sistema debe reforzar su progreso con mensajes de satisfacción y avance.

Si el estudiante falla repetidamente, el sistema debe evitar mostrar solo error; debe orientar qué hacer para mejorar.

Si el estudiante presenta baja participación, el sistema debe alertar al docente para brindar acompañamiento.

Si el estudiante mejora después de una actividad, el sistema debe mostrar retroalimentación que fortalezca su confianza.

## 8. Métricas útiles

El artículo evalúa principalmente la utilidad percibida del modelo propuesto mediante una escala de Likert.

Para el sistema, se pueden considerar métricas como:

- Utilidad percibida por el estudiante.
- Nivel de motivación.
- Nivel de confianza.
- Actividades completadas.
- Actividades abandonadas.
- Participación en la plataforma.
- Satisfacción del estudiante.
- Mejora del rendimiento después de retroalimentación motivacional.
- Permanencia en la ruta de nivelación.
- Progreso por tema.
- Reducción de intentos fallidos.
- Cantidad de estudiantes que continúan después de recibir apoyo.

## 9. Resultado o evidencia relevante

El artículo reporta una validación experimental preliminar con estudiantes universitarios. Los resultados muestran que la mayoría de los procesos de aprendizaje fueron valorados como útiles con frecuencia.

Según el artículo, el 50% de los procesos fueron evaluados como “often”, el 20% como “always” y el 30% como “sometimes”. La media general de utilidad fue 2.33 en una escala de 0 a 4, lo que indica una aceptación moderadamente positiva del modelo.

Estos resultados sugieren que considerar la motivación del estudiante puede mejorar la percepción de utilidad del aprendizaje adaptativo.

## 10. Limitación del artículo

Aunque el artículo aporta una visión valiosa sobre motivación y adaptación, presenta algunas limitaciones:

- La validación experimental es preliminar.
- La muestra utilizada es reducida.
- Se aplicó en un solo contexto universitario.
- No está contextualizado en educación rural ni en la zona VRAEM.
- No propone una arquitectura de software completa.
- No entrega un modelo machine learning entrenado.
- Depende de cuestionarios explícitos para identificar motivación y estilos.
- Puede ser difícil medir motivación con precisión si el estudiante no responde sinceramente.
- No se enfoca directamente en nivelación académica escolar.

Por ello, el sistema debe tomar la dimensión motivacional como complemento de la adaptación académica, pero implementarla inicialmente de forma simple y viable.

## 11. Cómo se usará en el sistema

Este artículo se usará para justificar que el sistema debe considerar la motivación del estudiante dentro de la personalización.

No se usará principalmente para definir algoritmos de machine learning, sino para diseñar:

- Mensajes motivacionales.
- Retroalimentación positiva.
- Indicadores de confianza.
- Detección de abandono.
- Alertas por baja participación.
- Ajuste de dificultad cuando existe frustración.
- Estrategias de acompañamiento docente.

## 12. Aplicación directa al sistema VRAEM

En el sistema de nivelación académica adaptativa para estudiantes del VRAEM, este artículo permite definir que los estudiantes con brechas de aprendizaje pueden necesitar no solo refuerzo académico, sino también apoyo motivacional.

La plataforma debe:

- Evitar que el estudiante se frustre con actividades demasiado difíciles.
- Reforzar su confianza cuando mejora.
- Mostrar mensajes claros y positivos.
- Recomendar actividades alcanzables.
- Detectar abandono o baja participación.
- Alertar al docente cuando el estudiante necesita acompañamiento.
- Usar la retroalimentación para fortalecer la continuidad del aprendizaje.

Esto es importante porque en contextos vulnerables la desmotivación puede aumentar el riesgo de abandono o bajo compromiso con la nivelación.

## 13. Decisión técnica final derivada del Artículo 03

A partir de este artículo, el sistema debe incorporar una lógica de adaptación motivacional.

Esto significa que la recomendación de actividades no debe depender solo del puntaje, sino también de señales como participación, confianza, satisfacción, intentos fallidos y abandono de actividades.

La decisión técnica central es complementar la adaptación académica con retroalimentación motivacional, usando el modelo ARCS como referencia conceptual.

## 14. Instrucción para Cursor

Cuando desarrolles el sistema, usa este artículo para implementar una capa motivacional simple dentro de la experiencia del estudiante.

No hagas que el sistema solo muestre resultados fríos como “correcto” o “incorrecto”. Debe orientar y motivar.

El sistema debe permitir:

- Mostrar mensajes de progreso.
- Reforzar la confianza del estudiante.
- Recomendar actividades alcanzables.
- Reducir dificultad cuando exista frustración.
- Alertar al docente por abandono o baja participación.
- Registrar confianza o satisfacción mediante preguntas simples.
- Usar retroalimentación positiva después de cada actividad.

El sistema debe reflejar la idea central del artículo: un aprendizaje adaptativo efectivo no solo adapta contenidos, también debe considerar la motivación del estudiante.