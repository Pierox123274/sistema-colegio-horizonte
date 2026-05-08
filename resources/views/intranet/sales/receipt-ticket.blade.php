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
        .small { font-size: 10px; }
        .actions { width: var(--ticket-width); margin: 12px auto; display: flex; gap: 8px; }
        button, a { font-family: Arial, sans-serif; border: none; padding: 8px 10px; border-radius: 6px; background: #111827; color: #fff; text-decoration: none; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { padding: 2px 0; text-align: left; vertical-align: top; }
        th:last-child, td:last-child { text-align: right; }
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
    <div class="row"><span class="label">Venta</span><span>{{ $receipt['sale']->sale_code }}</span></div>
    <div class="row"><span class="label">Fecha</span><span>{{ $receipt['sold_at_human'] }}</span></div>
    <div class="line"></div>

    <div><span class="label">Cliente:</span><br>{{ $receipt['student_name'] ?: 'Venta al publico' }}</div>
    <div style="margin-top:6px;"><span class="label">Apoderado:</span><br>{{ $receipt['guardian_name'] ?: 'No registrado' }}</div>
    <div class="line"></div>

    <table>
        <thead>
        <tr><th>Producto</th><th>Cant</th><th>P.U.</th><th>Sub</th></tr>
        </thead>
        <tbody>
        @foreach($receipt['sale']->items as $item)
            <tr>
                <td>{{ $item->product?->code }} {{ $item->product?->name }}</td>
                <td>{{ number_format((float)$item->quantity, 2) }}</td>
                <td>{{ number_format((float)$item->unit_price, 2) }}</td>
                <td>{{ number_format((float)$item->subtotal, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="line"></div>

    <div class="row"><span class="label">Metodo</span><span>{{ $receipt['sale']->payment_method }}</span></div>
    <div class="row"><span class="label">Cajero</span><span>{{ $receipt['registered_by'] }}</span></div>
    <div class="line"></div>
    <div class="row"><strong>TOTAL</strong><strong>S/ {{ $receipt['total'] }}</strong></div>

    <div class="line"></div>
    <div class="center small">{{ $receipt['institution']['message'] }}</div>
</div>

<div class="actions">
    <button onclick="window.print()">Imprimir ticket</button>
    <a href="{{ route('intranet.sales.sales.receipt.pdf', $receipt['sale']) }}">Descargar PDF</a>
</div>
</body>
</html>

