# Feature: Diagnóstico adaptativo y nivelación inteligente
# Fase 22 — criterios BDD (ISO/IEC 29119 documentación)

Feature: Aprendizaje adaptativo sin dependencia de IA externa obligatoria
  Como estudiante con ficha vinculada
  Quiero rendir diagnósticos, ver mi ruta de aprendizaje y recibir recomendaciones
  Para nivelar mi desempeño con reglas y datos institucionales

  Scenario: Estudiante accede al listado de diagnósticos
    Given un usuario autenticado con rol "Estudiante" y ficha de estudiante
    When solicita la ruta del diagnóstico del portal estudiante
    Then la respuesta es exitosa y muestra el listado de exámenes

  Scenario: Estudiante accede a la ruta de aprendizaje
    Given un usuario autenticado con rol "Estudiante" y ficha de estudiante
    When solicita la ruta de aprendizaje adaptativo
    Then la respuesta es exitosa y muestra el progreso y metas

  Scenario: Docente accede al aula adaptativa
    Given un usuario autenticado con rol "Docente"
    When solicita la ruta de aprendizaje adaptativo del portal docente
    Then la respuesta es exitosa

  Scenario: Docente accede a resultados de diagnóstico
    Given un usuario autenticado con rol "Docente"
    When solicita la ruta de resultados de diagnóstico
    Then la respuesta es exitosa

  Scenario: Administrador accede a analítica adaptativa institucional
    Given un usuario autenticado con rol "Administrador"
    When solicita la ruta de analítica adaptativa en intranet
    Then la respuesta es exitosa

  Scenario: Secretaría no accede a analítica adaptativa institucional
    Given un usuario autenticado con rol "Secretaria"
    When solicita la ruta de analítica adaptativa en intranet
    Then la respuesta indica prohibición de acceso
