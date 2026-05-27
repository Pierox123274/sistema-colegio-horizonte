# language: es
@advanced-ai
Característica: IA generativa avanzada institucional
  Como docente o estudiante del colegio
  Quiero usar copilotos y generadores IA seguros
  Para apoyar la planificación y el aprendizaje sin romper políticas institucionales

  Antecedentes:
    Dado que la plataforma Horizonte está disponible

  @teacher @copilot
  Escenario: Docente accede al copiloto IA
    Dado que inicio sesión como docente
    Cuando visito la ruta del copiloto IA docente
    Entonces veo el hub del copiloto pedagógico

  @teacher @exam
  Escenario: Generación de examen con fallback local
    Dado que inicio sesión como docente
    Y la IA generativa está deshabilitada
    Cuando solicito generar preguntas de examen sobre un tema
    Entonces recibo preguntas de plantilla local segura

  @student @coach
  Escenario: Estudiante usa coach de resumen
    Dado que inicio sesión como estudiante
    Y la IA generativa está deshabilitada
    Cuando solicito un resumen de un tema
    Entonces recibo puntos de resumen orientativos

  @admin @analytics
  Escenario: Administrador ve analítica de uso IA
    Dado que inicio sesión como administrador
    Cuando visito la analítica IA institucional
    Entonces veo métricas de uso y módulos IA
