<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comprobante {{ $receipt['receipt_number'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 24px; color: #0f172a; }
        .container { max-width: 780px; margin: 0 auto; border: 1px solid #d4d4d8; border-radius: 10px; padding: 24px; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 16px; }
        .muted { color: #52525b; font-size: 12px; }
        .grid { display: grid; grid-template-columns: 180px 1fr; gap: 8px 16px; margin-bottom: 20px; }
        .label { color: #3f3f46; font-size: 13px; font-weight: 700; text-transform: uppercase; }
        .value { font-size: 14px; }
        .amount { font-size: 28px; font-weight: 800; color: #0369a1; }
        .footer { margin-top: 18px; font-size: 13px; color: #334155; border-top: 1px solid #e4e4e7; padding-top: 12px; }
        .qr { width: 120px; height: 120px; border: 2px dashed #64748b; display: flex; align-items: center; justify-content: center; font-size: 10px; text-align: center; padding: 6px; }
        .print-actions { max-width: 780px; margin: 12px auto 0; display: flex; gap: 8px; }
        .button { background: #0f172a; color: #fff; border: none; border-radius: 8px; padding: 8px 12px; cursor: pointer; }
        @media print {
            body { padding: 0; }
            .container { border: none; border-radius: 0; max-width: 100%; }
            .print-actions { display: none; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1 style="margin:0; font-size: 24px;">Comprobante de Pago</h1>
        <p class="muted" style="margin:8px 0 0;">
            {{ $receipt['institution']['name'] }}<br>
            {{ $receipt['institution']['identifier'] }}<br>
            {{ $receipt['institution']['address'] }}
        </p>
    </div>

    <div style="display:flex; justify-content: space-between; gap: 16px;">
        <div class="grid">
            <div class="label">N. comprobante</div><div class="value">{{ $receipt['receipt_number'] }}</div>
            <div class="label">Código de pago</div><div class="value">{{ $receipt['payment']->payment_code }}</div>
            <div class="label">Fecha y hora</div><div class="value">{{ $receipt['paid_at_human'] }}</div>
            <div class="label">Estudiante</div><div class="value">{{ $receipt['student_name'] }}</div>
            <div class="label">Apoderado</div><div class="value">{{ $receipt['guardian_name'] ?: 'No registrado' }}</div>
            <div class="label">Concepto</div><div class="value">{{ $receipt['concept'] }}</div>
            <div class="label">Metodo de pago</div><div class="value">{{ $receipt['method'] }}</div>
            <div class="label">Registrado por</div><div class="value">{{ $receipt['registered_by'] }}</div>
        </div>
        @if($receipt['institution']['show_qr_demo'])
            <div class="qr">QR DEMO<br>{{ $receipt['qr_demo_payload'] }}</div>
        @endif
    </div>

    <p class="amount">S/ {{ $receipt['amount'] }}</p>

    <div class="footer">
        {{ $receipt['institution']['message'] }}
    </div>
</div>

<div class="print-actions">
    <button class="button" onclick="window.print()">Imprimir</button>
</div>
</body>
</html>

