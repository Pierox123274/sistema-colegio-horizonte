Feature: Gamificación institucional
  Como institución educativa
  Quiero motivar el progreso del estudiante con XP y logros
  Para reforzar hábitos académicos y participación saludable

  Scenario: Estudiante visualiza su perfil gamificado
    Given un estudiante autenticado con ficha vinculada
    When ingresa a "/student/gamification"
    Then debe ver su nivel, XP y retos activos

  Scenario: Acciones académicas otorgan XP
    Given un estudiante con perfil gamificado
    When completa una tarea y aprueba un examen
    Then su XP acumulado debe incrementarse

  Scenario: Permisos de analítica gamificada
    Given un usuario docente autenticado
    When intenta abrir "/intranet/gamification"
    Then debe recibir acceso denegado

