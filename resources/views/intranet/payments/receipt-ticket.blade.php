<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket {{ $receipt['receipt_number'] }}</title>
    <style>
        :root {
            --ticket-width: 80mm;
        }
        body { margin: 0; font-family: "Courier New", monospace; background: #fff; color: #000; }
        .ticket { width: var(--ticket-width); max-width: 100%; margin: 0 auto; padding: 10px 8px; font-size: 12px; }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; gap: 8px; }
        .label { font-weight: 700; }
        .small { font-size: 11px; }
        .actions { width: var(--ticket-width); margin: 12px auto; display: flex; gap: 8px; }
        button, a { font-family: Arial, sans-serif; border: none; padding: 8px 10px; border-radius: 6px; background: #111827; color: #fff; text-decoration: none; cursor: pointer; }
        @media print {
            body { margin: 0; }
            .actions { display: none; }
        }
        @media print and (max-width: 58mm) {
            :root { --ticket-width: 58mm; }
        }
    </style>
</head>
<body>
<div class="ticket">
    <div class="center">
        <strong>{{ $receipt['institution']['name'] }}</strong><br>
        <span class="small">{{ $receipt['institution']['identifier'] }}</span><br>
        <span class="small">{{ $receipt['institution']['address'] }}</span>
    </div>
    <div class="line"></div>

    <div class="row"><span class="label">Comp.</span><span>{{ $receipt['receipt_number'] }}</span></div>
    <div class="row"><span class="label">Pago</span><span>{{ $receipt['payment']->payment_code }}</span></div>
    <div class="row"><span class="label">Fecha</span><span>{{ $receipt['paid_at_human'] }}</span></div>
    <div class="line"></div>

    <div><span class="label">Estudiante:</span><br>{{ $receipt['student_name'] }}</div>
    <div style="margin-top:6px;"><span class="label">Apoderado:</span><br>{{ $receipt['guardian_name'] ?: 'No registrado' }}</div>
    <div style="margin-top:6px;"><span class="label">Concepto:</span><br>{{ $receipt['concept'] }}</div>
    <div class="row" style="margin-top:6px;"><span class="label">Metodo</span><span>{{ $receipt['method'] }}</span></div>
    <div class="row"><span class="label">Registrado por</span><span>{{ $receipt['registered_by'] }}</span></div>
    <div class="line"></div>

    <div class="row"><strong>TOTAL</strong><strong>S/ {{ $receipt['amount'] }}</strong></div>

    @if($receipt['institution']['show_qr_demo'])
        <div class="center small" style="margin-top:8px;">
            [QR DEMO]<br>{{ $receipt['qr_demo_payload'] }}
        </div>
    @endif

    <div class="line"></div>
    <div class="center small">{{ $receipt['institution']['message'] }}</div>
</div>

<div class="actions">
    <button onclick="window.print()">Imprimir ticket</button>
    <a href="{{ route('intranet.payments.receipt.pdf', $receipt['payment']) }}">Descargar PDF</a>
</div>
</body>
</html>

