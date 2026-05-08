<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111827; }
        h1 { margin: 0 0 8px; font-size: 18px; }
        .meta { margin-bottom: 12px; font-size: 11px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        .totals { margin-top: 12px; }
    </style>
</head>
<body>
<h1>Reporte de ventas</h1>
<div class="meta">
    Generado: {{ now()->format('d/m/Y H:i:s') }}<br>
    Filtros:
    {{ $filters['day'] ?: ($filters['date_from'] ?: '---').' a '.($filters['date_to'] ?: '---') }},
    método={{ $filters['payment_method'] ?: 'todos' }},
    estado={{ $filters['status'] ?: 'todos' }}.
</div>
<table>
    <thead>
    <tr>
        <th>Fecha</th><th>Código</th><th>Cliente</th><th>Apoderado</th><th>Método</th><th>Estado</th><th>Total</th><th>Cajero</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sales as $sale)
        <tr>
            <td>{{ $sale->sold_at?->format('d/m/Y H:i') }}</td>
            <td>{{ $sale->sale_code }}</td>
            <td>{{ $sale->student ? trim($sale->student->first_name.' '.$sale->student->last_name.' ('.$sale->student->code.')') : 'Venta al público' }}</td>
            <td>{{ $sale->guardian ? trim($sale->guardian->first_name.' '.$sale->guardian->last_name) : '—' }}</td>
            <td>{{ $sale->payment_method }}</td>
            <td>{{ $sale->status }}</td>
            <td>S/ {{ number_format((float)$sale->total, 2) }}</td>
            <td>{{ $sale->createdByUser?->name ?? '—' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<div class="totals">
    <p>Cantidad de ventas: <strong>{{ $summary['count'] }}</strong></p>
    <p>Total vendido: <strong>S/ {{ $summary['total_sold'] }}</strong></p>
    <p>Total anulado: <strong>S/ {{ $summary['total_canceled'] }}</strong></p>
    <p>Total neto: <strong>S/ {{ $summary['net_total'] }}</strong></p>
    <p>Métodos de pago:</p>
    <ul>
        @foreach($summary['by_method'] as $method => $data)
            <li>{{ $method }}: {{ $data['count'] }} venta(s), S/ {{ number_format((float)$data['amount'], 2) }}</li>
        @endforeach
    </ul>
</div>
</body>
</html>

