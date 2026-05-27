# language: es
@external-integrations
Característica: Integraciones externas institucionales
  Como administrador del colegio
  Quiero ver el estado de integraciones externas
  Para operar en producción con proveedores reales de forma segura

  Escenario: Panel de integraciones visible para administrador
    Dado que inicio sesión como administrador
    Cuando visito el panel de integraciones
    Entonces veo el estado de los proveedores configurados

  Escenario: Webhook de pagos registrado sin firma obligatoria
    Cuando se envía un webhook de prueba a pagos
    Entonces el sistema registra el evento
