Feature: Sistema de notificaciones institucionales
  Como usuario autenticado
  Quiero recibir alertas operativas y académicas
  Para mantenerme al día en la plataforma

  Scenario: Usuario visualiza centro de notificaciones
    Given existe un usuario autenticado
    When ingresa a "/notifications"
    Then visualiza el centro de notificaciones

  Scenario: Usuario actualiza preferencias de notificación
    Given existe un usuario autenticado
    When guarda preferencias en "/settings/notifications"
    Then el sistema confirma la actualización

  Scenario: Sistema genera recordatorio financiero automático
    Given existe una pensión vencida para un estudiante
    When se ejecuta el job de recordatorios financieros
    Then se genera una notificación para el estudiante
