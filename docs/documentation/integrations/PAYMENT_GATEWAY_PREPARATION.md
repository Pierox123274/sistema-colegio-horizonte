# Pasarela de pagos (preparación)

## Providers

- `MercadoPagoProvider`  
- `CulqiProvider`  
- `NullPaymentGateway` (por defecto sin credenciales)

## Variables

```
INTEGRATION_PAYMENTS_ENABLED=false
MERCADOPAGO_ENABLED=false
MERCADOPAGO_ACCESS_TOKEN=
CULQI_ENABLED=false
```

## Flujo futuro

Checkout → webhook → actualizar `payments` en ERP.

**No hay cobro en línea obligatorio** hasta configurar cuenta merchant institucional.
