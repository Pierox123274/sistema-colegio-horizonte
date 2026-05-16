# Feature: Tutor IA institucional
# Fase 21 — criterios BDD (ISO/IEC 29119 documentación)

Feature: Acceso al tutor y analítica IA por rol
  Como estudiante con ficha vinculada
  Quiero abrir el tutor IA y las recomendaciones
  Para obtener orientación académica basada en mis datos agregados

  Scenario: Estudiante accede al tutor IA
    Given un usuario autenticado con rol "Estudiante" y ficha de estudiante
    When solicita la ruta del tutor IA del portal estudiante
    Then la respuesta es exitosa y muestra el componente de tutor

  Scenario: Administrador accede al panel de IA institucional
    Given un usuario autenticado con rol "Administrador"
    When solicita la ruta de analítica IA intranet
    Then la respuesta es exitosa

  Scenario: Secretaría no accede al panel de IA institucional
    Given un usuario autenticado con rol "Secretaria"
    When solicita la ruta de analítica IA intranet
    Then la respuesta indica prohibición de acceso
