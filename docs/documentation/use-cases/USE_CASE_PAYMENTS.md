# Caso de uso — Gestión de pagos escolares

## Identificación

| Campo | Valor |
|-------|--------|
| **ID** | UC-PAY-008 |
| **Nombre** | Registrar pago y emitir comprobante |
| **Módulo** | Finanzas / ERP |

## Actores

| Tipo | Actor |
|------|--------|
| **Principal** | Secretaría (registro), Administrador (conceptos) |
| **Secundarios** | Apoderado (consulta futura), Pasarela de pago (**preparación**) |

## Objetivo

Registrar pagos contra pensiones/conceptos, mantener historial financiero y generar comprobantes PDF.

## Precondiciones

1. Conceptos de pago y pensiones configurados.
2. Estudiante matriculado con obligaciones pendientes o parciales.

## Flujo principal

1. Secretaría accede a **Pagos** (`/intranet/payments`).
2. Busca estudiante y selecciona concepto/pensión.
3. Registra monto, método y fecha vía `PaymentService`.
4. El sistema actualiza saldo y permite descarga de comprobante (`PaymentReceiptService`).
5. Auditoría registra la operación financiera.

## Flujos alternativos

| ID | Condición | Comportamiento |
|----|-----------|----------------|
| FA-1 | Pago en línea | **Preparado** — `MercadoPagoProvider` / `CulqiProvider` con `NullPaymentGateway` por defecto. |
| FA-2 | Webhook de confirmación | Ruta en `routes/webhooks.php` cuando integración esté activa. |

## Excepciones

| ID | Excepción | Respuesta |
|----|-----------|-----------|
| EX-1 | Monto inválido | Validación de formulario; no persistencia. |
| EX-2 | Usuario sin rol financiero | 403. |

## Resultado esperado

Trazabilidad financiera institucional y comprobantes oficiales para apoderados.

## Evidencia

- `tests/Feature/Intranet/FinanceManagementTest.php`, `PaymentReceiptTest.php`, Cypress `payments.cy.ts`.
