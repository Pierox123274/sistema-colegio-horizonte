<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte académico</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        .muted { color: #6b7280; font-size: 11px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .metrics { margin-top: 12px; }
        .metrics li { margin-bottom: 4px; }
    </style>
</head>
<body>
    <h1>Reporte académico de calificaciones</h1>
    <p class="muted">Generado: {{ now()->format('Y-m-d H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Estudiante</th>
                <th>Código</th>
                <th>Curso</th>
                <th>Evaluación</th>
                <th>Periodo</th>
                <th>Sección</th>
                <th>Nota</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ optional($row->evaluation?->evaluated_at)->format('Y-m-d') }}</td>
                    <td>{{ trim(($row->student?->first_name ?? '').' '.($row->student?->last_name ?? '')) }}</td>
                    <td>{{ $row->student?->code }}</td>
                    <td>{{ $row->evaluation?->subject?->name }}</td>
                    <td>{{ $row->evaluation?->title }}</td>
                    <td>{{ $row->evaluation?->period }}</td>
                    <td>{{ $row->evaluation?->section?->name }}</td>
                    <td>{{ $row->score }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <ul class="metrics">
        <li>Total registros: {{ $metrics['total_records'] }}</li>
        <li>Promedio curso: {{ $metrics['course_average'] }}</li>
        <li>Promedio general: {{ $metrics['general_average'] }}</li>
    </ul>
</body>
</html>

