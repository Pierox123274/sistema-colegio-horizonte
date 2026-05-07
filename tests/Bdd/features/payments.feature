# language: es
# Escenarios BDD (ISO/IEC 29119) — módulo financiero (conceptos, pensiones, pagos)
# Cobertura automatizada: ver tests/Feature/Intranet/FinanceManagementTest.php

Característica: Finanzas — conceptos, pensiones y pagos
  Como administración o secretaría
  Quiero gestionar conceptos de pago, pensiones por matrícula y el registro de cobros
  Para mantener la deuda y los pagos trazables sin exceder saldos

  Escenario: Administrador define un concepto de pago
    Dado que no existe el código de concepto
    Cuando registra un nuevo concepto con datos válidos
    Entonces el concepto queda almacenado y disponible para pensiones y pagos

  Escenario: Secretaría genera una pensión y registra cobros
    Dado una matrícula y un concepto activos
    Cuando crea la pensión para un mes y año sin duplicar periodo
    Entonces la obligación aparece con estado coherente

    Cuando registra un pago ligado a esa pensión dentro del saldo pendiente
    Entonces el estado de la pensión se actualiza según los montos pagados

  Escenario: Roles sin acceso financiero
    Dado un usuario docente, estudiante o apoderado
    Cuando solicita rutas del menú Finanzas
    Entonces el acceso es denegado

  Escenario: Validaciones de integridad
    Dado un código de concepto ya existente
    Cuando se intenta crear otro con el mismo código
    Entonces la operación se rechaza

    Dado una pensión ya creada para un mes y año
    Cuando se intenta crear otra para la misma matrícula y periodo
    Entonces la operación se rechaza

    Dado un saldo pendiente en una pensión
    Cuando se intenta registrar un monto mayor al pendiente
    Entonces la operación se rechaza
