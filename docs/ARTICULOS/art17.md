# ARTÍCULO 17 — FICHA TÉCNICA PARA CURSOR

## 1. Referencia

Nye, B. D.; Pavlik, P. I.; Windsor, A.; Olney, A. M.; Hajeer, M.; Hu, X. (2018).  
“SKOPE-IT: superposición de tutoría en lenguaje natural sobre un sistema de aprendizaje adaptativo para matemáticas”.  
Revista: International Journal of STEM Education.  
DOI: https://doi.org/10.1186/s40594-018-0109-4.

## 2. Idea clave del artículo

El artículo presenta SKOPE-IT, un sistema híbrido que integra tutoría en lenguaje natural con un sistema de aprendizaje adaptativo para matemáticas.

La idea principal es que los sistemas educativos inteligentes no deben funcionar como herramientas aisladas. Por el contrario, pueden integrarse para combinar diferentes fortalezas: por un lado, la adaptación de problemas matemáticos según el nivel del estudiante; por otro, la tutoría conversacional que guía al estudiante durante la resolución.

## 3. Aporte específico al sistema

Este artículo aporta la idea de integrar la práctica adaptativa con una guía explicativa paso a paso.

Su valor principal para el sistema VRAEM es que permite justificar que la plataforma no solo debe recomendar ejercicios, sino también orientar al estudiante mientras aprende.

Este artículo fortalece:

- Tutoría inteligente.
- Retroalimentación explicativa.
- Guía paso a paso.
- Autoexplicación del estudiante.
- Integración entre módulos del sistema.
- Aprendizaje matemático adaptativo.
- Interoperabilidad entre componentes.

## 4. Decisión técnica derivada

A partir de este artículo, el sistema debe incorporar una lógica de acompañamiento durante la resolución de actividades.

Esto significa que, cuando un estudiante resuelva un ejercicio, el sistema no debe limitarse a decir “correcto” o “incorrecto”. Debe ofrecer pistas, explicación del procedimiento, preguntas guía y retroalimentación según el error cometido.

La decisión técnica principal es que la adaptación debe combinar dos niveles:

- Adaptación macro: selección de actividades según el nivel del estudiante.
- Adaptación micro: apoyo paso a paso durante la resolución de cada actividad.

## 5. Datos que sugiere considerar

El artículo permite justificar el uso de datos relacionados con interacción, resolución de problemas y uso de tutoría.

Para el sistema, se pueden considerar:

- Respuestas correctas.
- Respuestas incorrectas.
- Tipo de error cometido.
- Paso del ejercicio donde ocurre el error.
- Número de intentos.
- Tiempo de resolución.
- Pistas solicitadas.
- Explicaciones consultadas.
- Diálogos o preguntas respondidas.
- Progreso en problemas matemáticos.
- Actividades completadas.
- Interacción con la retroalimentación.
- Nivel de dominio por tema.
- Transferencia a ejercicios similares.

## 6. Técnicas que respalda

El artículo respalda técnicas y enfoques relacionados con sistemas tutores inteligentes y tutoría en lenguaje natural.

Para el sistema, respalda:

- Intelligent Tutoring Systems.
- Tutoría en lenguaje natural.
- Procesamiento de lenguaje natural.
- Análisis semántico latente, LSA.
- Normalización de respuestas.
- Ejemplos resueltos interactivos.
- Diálogos de autoexplicación.
- Teoría de espacios de conocimiento.
- Arquitectura orientada a servicios.
- Integración mediante mensajes semánticos.
- Aprendizaje adaptativo en matemáticas.

Para el MVP, no es necesario implementar NLP avanzado desde el inicio. Se puede comenzar con pistas predefinidas, retroalimentación por tipo de error y explicaciones paso a paso.

## 7. Regla o lógica aplicable al sistema

Este artículo permite derivar las siguientes reglas funcionales:

Si el estudiante falla una pregunta matemática, el sistema debe mostrar una pista antes de mostrar la respuesta final.

Si el estudiante falla en un paso específico, el sistema debe explicar ese paso con un ejemplo sencillo.

Si el estudiante solicita varias pistas, el sistema debe registrar que necesita refuerzo en ese tema.

Si el estudiante resuelve correctamente después de recibir una pista, el sistema debe registrar mejora con apoyo.

Si el estudiante falla varias veces aunque reciba explicación, el sistema debe recomendar una actividad previa más básica.

Si el estudiante domina un ejercicio, el sistema puede presentar un problema similar con datos diferentes para evaluar transferencia.

## 8. Métricas útiles

El artículo permite orientar métricas relacionadas con aprendizaje, interacción y uso de tutoría.

Para el sistema, se pueden considerar:

- Mejora entre pre-test y post-test.
- Puntaje en ejercicios matemáticos.
- Cantidad de pistas solicitadas.
- Cantidad de intentos por actividad.
- Tiempo de resolución.
- Nivel de interacción con explicaciones.
- Porcentaje de ejercicios completados.
- Mejora después de recibir retroalimentación.
- Dominio por componente de conocimiento.
- Transferencia a ejercicios similares.
- Frecuencia de uso de la tutoría.
- Relación entre interacción con el sistema y desempeño.

## 9. Resultado o evidencia relevante

El artículo muestra que la integración entre tutoría basada en lenguaje natural y aprendizaje adaptativo tiene potencial para mejorar la comprensión matemática.

Los resultados no fueron completamente concluyentes en todos los casos, pero se observó que los estudiantes que interactuaron más con el sistema lograron mejores desempeños. Esto sugiere que la tutoría activa y la autoexplicación pueden favorecer el aprendizaje, especialmente cuando se combinan con práctica adaptativa.

El aporte más importante no está solo en los resultados, sino en el modelo de integración: unir sistemas especializados para crear una experiencia más completa.

## 10. Limitación del artículo

Aunque el artículo es útil para orientar el diseño del sistema, presenta algunas limitaciones:

- Está enfocado principalmente en matemáticas.
- No está contextualizado en la zona VRAEM.
- Se basa en integración con sistemas existentes como AutoTutor y ALEKS.
- Implementar lenguaje natural avanzado puede ser técnicamente complejo.
- No ofrece una solución lista para nivelación académica escolar.
- Los resultados no son totalmente concluyentes en todos los casos.
- Requiere diseño cuidadoso de diálogos, pistas y ejemplos.
- Puede ser costoso desarrollar tutoría conversacional completa desde cero.

Por ello, el sistema debe tomar la idea de tutoría guiada, pero implementarla inicialmente de forma simple y viable.

## 11. Cómo se usará en el sistema

Este artículo se usará para justificar que el sistema debe brindar apoyo durante la resolución de actividades, no solo después de calificarlas.

Su aporte principal se aplicará en:

- Retroalimentación paso a paso.
- Pistas automáticas.
- Explicaciones por error.
- Ejemplos resueltos interactivos.
- Actividades matemáticas adaptativas.
- Registro de intentos y pistas.
- Recomendación de ejercicios similares.
- Diseño modular de componentes inteligentes.

## 12. Aplicación directa al sistema VRAEM

En el sistema de nivelación académica adaptativa para estudiantes del VRAEM, este artículo permite definir que los estudiantes con brechas en matemáticas necesitan acompañamiento guiado.

La plataforma debe:

- Recomendar ejercicios según el nivel del estudiante.
- Mostrar pistas cuando el estudiante se equivoca.
- Explicar el procedimiento paso a paso.
- Registrar en qué parte del ejercicio falla.
- Recomendar ejercicios previos si el estudiante no comprende.
- Usar ejemplos similares para reforzar transferencia.
- Permitir que el docente vea qué tipo de errores se repiten.

Esto es importante porque muchos estudiantes con rezago no solo necesitan más ejercicios, sino orientación durante el proceso de resolución.

## 13. Decisión técnica final derivada del Artículo 17

A partir de este artículo, el sistema debe implementar una lógica de tutoría guiada dentro de las actividades adaptativas.

Esto significa que el sistema debe combinar recomendación de ejercicios con retroalimentación paso a paso, pistas y explicaciones.

La decisión técnica central es que la adaptación debe funcionar en dos niveles:

- Seleccionar qué actividad corresponde al estudiante.
- Guiar cómo resolver la actividad cuando el estudiante presenta dificultad.

## 14. Instrucción para Cursor

Cuando desarrolles el sistema, usa este artículo para implementar apoyo inteligente dentro de las actividades, especialmente en matemáticas.

No hagas que el sistema solo califique respuestas. Cada actividad debe poder incluir:

- Pistas.
- Explicación del procedimiento.
- Tipo de error.
- Intentos del estudiante.
- Retroalimentación por paso.
- Ejemplo resuelto.
- Ejercicio similar posterior.

El sistema debe reflejar la idea central del artículo: el aprendizaje adaptativo mejora cuando la práctica personalizada se combina con tutoría guiada e interacción explicativa.