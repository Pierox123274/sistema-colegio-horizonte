<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $report['title'] ?? 'Reporte analítico' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a2744; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
        h2 { font-size: 13px; margin-top: 18px; }
    </style>
</head>
<body>
    <h1>{{ $report['title'] ?? 'Reporte analítico' }}</h1>
    <p class="meta">Generado: {{ $generated_at }}</p>

    @if(!empty($report['summary']))
        <h2>Resumen</h2>
        <table>
            @foreach($report['summary'] as $key => $value)
                <tr>
                    <th>{{ str_replace('_', ' ', ucfirst($key)) }}</th>
                    <td>{{ is_scalar($value) ? $value : json_encode($value) }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @foreach(['top_students' => 'Top estudiantes', 'risk_students' => 'Estudiantes en riesgo', 'most_absences' => 'Mayor número de faltas', 'recent_payments' => 'Pagos recientes', 'low_stock' => 'Stock bajo'] as $section => $title)
        @if(!empty($report[$section]))
            <h2>{{ $title }}</h2>
            <table>
                <thead>
                    <tr>
                        @foreach(array_keys($report[$section][0] ?? []) as $col)
                            <th>{{ str_replace('_', ' ', ucfirst($col)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($report[$section] as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach
</body>
</html>
