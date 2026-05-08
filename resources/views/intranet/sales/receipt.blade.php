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
        .grid { display: grid; grid-template-columns: 180px 1fr; gap: 8px 16px; margin-bottom: 20px; }
        .label { color: #3f3f46; font-size: 13px; font-weight: 700; text-transform: uppercase; }
        .value { font-size: 14px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 8px; }
        th, td { border-bottom: 1px solid #e4e4e7; padding: 8px; text-align: left; }
        .amount { font-size: 24px; font-weight: 800; color: #0369a1; margin-top: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1 style="margin:0; font-size: 22px;">Comprobante de venta</h1>
        <p style="margin:8px 0 0; font-size:12px; color:#52525b;">
            {{ $receipt['institution']['name'] }}<br>
            {{ $receipt['institution']['identifier'] }}<br>
            {{ $receipt['institution']['address'] }}
        </p>
    </div>
    <div class="grid">
        <div class="label">N. comprobante</div><div class="value">{{ $receipt['receipt_number'] }}</div>
        <div class="label">Código venta</div><div class="value">{{ $receipt['sale']->sale_code }}</div>
        <div class="label">Fecha</div><div class="value">{{ $receipt['sold_at_human'] }}</div>
        <div class="label">Estudiante</div><div class="value">{{ $receipt['student_name'] ?: '—' }}</div>
        <div class="label">Apoderado</div><div class="value">{{ $receipt['guardian_name'] ?: '—' }}</div>
        <div class="label">Método</div><div class="value">{{ $receipt['sale']->payment_method }}</div>
        <div class="label">Registrado por</div><div class="value">{{ $receipt['registered_by'] }}</div>
    </div>
    <table>
        <thead>
        <tr><th>Producto</th><th>Cant.</th><th>P. Unit</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
        @foreach($receipt['sale']->items as $item)
            <tr>
                <td>{{ $item->product?->code }} - {{ $item->product?->name }} ({{ $item->product?->size }})</td>
                <td>{{ number_format((float)$item->quantity, 2) }}</td>
                <td>S/ {{ number_format((float)$item->unit_price, 2) }}</td>
                <td>S/ {{ number_format((float)$item->subtotal, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p class="amount">Total: S/ {{ $receipt['total'] }}</p>
</div>
</body>
</html>

