Feature: Comprobantes de pagos
  Como usuario de finanzas autorizado
  Quiero visualizar e imprimir comprobantes de pago
  Para entregar evidencia formal al estudiante o apoderado

  Scenario: Administrador visualiza comprobante
    Given existe un pago registrado
    And el usuario tiene rol "Administrador"
    When ingresa a la ruta de comprobante del pago
    Then el sistema muestra el comprobante con datos institucionales

  Scenario: Secretaria visualiza comprobante
    Given existe un pago registrado
    And el usuario tiene rol "Secretaria"
    When ingresa a la ruta de comprobante del pago
    Then el sistema permite ver y descargar el comprobante

  Scenario: Docente no tiene acceso
    Given existe un pago registrado
    And el usuario tiene rol "Docente"
    When intenta abrir el comprobante de pago
    Then el sistema responde acceso prohibido

  Scenario: Descarga PDF de comprobante
    Given existe un pago registrado
    And el usuario tiene rol "Administrador"
    When solicita la version PDF del comprobante
    Then el sistema responde un archivo PDF valido

  Scenario: Vista de ticket termico
    Given existe un pago registrado
    And el usuario tiene rol "Administrador"
    When solicita la vista de ticket del comprobante
    Then el sistema responde una vista imprimible para 58mm y 80mm

