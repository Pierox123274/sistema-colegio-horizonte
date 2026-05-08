<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color:#0f172a; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d4d4d8; padding: 6px; text-align: left; }
    </style>
</head>
<body>
<h1>Comprobante de venta {{ $receipt['receipt_number'] }}</h1>
<p>{{ $receipt['institution']['name'] }} - {{ $receipt['institution']['identifier'] }}</p>
<p>Venta: {{ $receipt['sale']->sale_code }} | Fecha: {{ $receipt['sold_at_human'] }} | Método: {{ $receipt['sale']->payment_method }}</p>
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
<p><strong>Total: S/ {{ $receipt['total'] }}</strong></p>
</body>
</html>

