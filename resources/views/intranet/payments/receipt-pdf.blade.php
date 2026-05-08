<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante {{ $receipt['receipt_number'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .page { border: 1px solid #d1d5db; border-radius: 8px; padding: 18px; }
        .title { font-size: 18px; font-weight: bold; margin: 0 0 4px; }
        .muted { color: #4b5563; line-height: 1.4; margin: 0 0 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { padding: 6px 4px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .label { width: 34%; color: #374151; font-weight: bold; text-transform: uppercase; font-size: 11px; }
        .amount { margin-top: 16px; font-size: 22px; font-weight: bold; color: #0369a1; }
        .foot { margin-top: 16px; border-top: 1px solid #e5e7eb; padding-top: 10px; color: #374151; }
    </style>
</head>
<body>
<div class="page">
    <p class="title">Comprobante de Pago</p>
    <p class="muted">
        {{ $receipt['institution']['name'] }}<br>
        {{ $receipt['institution']['identifier'] }}<br>
        {{ $receipt['institution']['address'] }}
    </p>

    <table>
        <tr><td class="label">N. comprobante</td><td>{{ $receipt['receipt_number'] }}</td></tr>
        <tr><td class="label">Codigo de pago</td><td>{{ $receipt['payment']->payment_code }}</td></tr>
        <tr><td class="label">Fecha y hora</td><td>{{ $receipt['paid_at_human'] }}</td></tr>
        <tr><td class="label">Estudiante</td><td>{{ $receipt['student_name'] }}</td></tr>
        <tr><td class="label">Apoderado</td><td>{{ $receipt['guardian_name'] ?: 'No registrado' }}</td></tr>
        <tr><td class="label">Concepto</td><td>{{ $receipt['concept'] }}</td></tr>
        <tr><td class="label">Metodo de pago</td><td>{{ $receipt['method'] }}</td></tr>
        <tr><td class="label">Monto</td><td>S/ {{ $receipt['amount'] }}</td></tr>
        <tr><td class="label">Registrado por</td><td>{{ $receipt['registered_by'] }}</td></tr>
    </table>

    <p class="amount">TOTAL: S/ {{ $receipt['amount'] }}</p>

    <p class="foot">{{ $receipt['institution']['message'] }}</p>
</div>
</body>
</html>

