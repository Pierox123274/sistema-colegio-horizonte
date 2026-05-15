# ARTÍCULO 01 — FICHA TÉCNICA PARA CURSOR

## 1. Referencia
Gligorea et al. (2023). “Aprendizaje adaptativo mediante inteligencia artificial en el aprendizaje electrónico: una revisión bibliográfica”. Education Sciences, 13(12).

## 2. Idea clave del artículo
El artículo sostiene que el aprendizaje adaptativo basado en inteligencia artificial y machine learning permite personalizar el aprendizaje mediante el análisis de datos del estudiante, como desempeño, progreso, interacción con la plataforma, preferencias y ritmo de aprendizaje.

## 3. Aporte específico al sistema
Este artículo aporta la base conceptual para que el sistema no entregue el mismo contenido a todos los estudiantes, sino que adapte la nivelación académica según el desempeño y las necesidades individuales de cada estudiante.

## 4. Decisión técnica derivada
El sistema debe trabajar con perfiles dinámicos del estudiante. Esto implica registrar datos académicos, actualizar el perfil según el avance y usar esa información para recomendar actividades personalizadas.

## 5. Datos que sugiere considerar
- Puntaje de evaluación.
- Respuestas correctas e incorrectas.
- Temas dominados.
- Temas con dificultad.
- Progreso del estudiante.
- Tiempo empleado.
- Interacciones con la plataforma.
- Ritmo de aprendizaje.

## 6. Técnicas que respalda
- Árboles de decisión.
- Clustering.
- Redes neuronales.
- Aprendizaje por refuerzo.
- Sistemas de recomendación.
- Analítica de aprendizaje.

## 7. Regla o lógica aplicable al sistema
Si el sistema detecta bajo rendimiento en un tema específico, debe registrar una brecha de aprendizaje y recomendar actividades de refuerzo asociadas a ese tema.

Si el estudiante mejora progresivamente, el sistema debe aumentar la dificultad de las actividades.

Si el estudiante falla repetidamente, el sistema debe reducir la dificultad y recomendar contenido básico.

## 8. Métricas útiles
- Mejora del rendimiento académico.
- Progreso por tema.
- Nivel de participación.
- Retención del conocimiento.
- Tasa de finalización de actividades.
- Reducción de brechas de aprendizaje.

## 9. Limitación del artículo
El artículo no está contextualizado en zonas vulnerables como el VRAEM, no considera problemas de conectividad limitada y no propone una arquitectura técnica lista para implementar.

## 10. Cómo se usará en el sistema
Este artículo se usará para justificar la personalización del aprendizaje mediante IA y ML. Su aporte principal se aplicará en la lógica de perfil dinámico, detección de brechas y recomendación personalizada.