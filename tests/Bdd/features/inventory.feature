Feature: Gestión de inventario
  Como administrador de intranet
  Quiero gestionar categorías, productos y movimientos
  Para mantener el stock controlado y trazable

  Scenario: Administrador crea categoría
    Given el usuario tiene rol "Administrador"
    When registra una nueva categoría de inventario
    Then el sistema guarda la categoría correctamente

  Scenario: Administrador crea producto
    Given existe una categoría de inventario
    And el usuario tiene rol "Administrador"
    When registra un producto con stock inicial
    Then el sistema guarda el producto con sus precios y stock

  Scenario: Entrada de inventario
    Given existe un producto con stock actual
    And el usuario tiene rol "Administrador"
    When registra un movimiento de tipo "entrada"
    Then el sistema incrementa el stock y registra la trazabilidad

  Scenario: Salida con control de stock
    Given existe un producto con stock limitado
    And el usuario tiene rol "Administrador"
    When intenta registrar una salida mayor al stock disponible
    Then el sistema rechaza la operación por stock negativo

  Scenario: Secretaria solo visualiza
    Given el usuario tiene rol "Secretaria"
    When abre los listados de inventario
    Then el sistema permite listar y ver sin crear ni editar

