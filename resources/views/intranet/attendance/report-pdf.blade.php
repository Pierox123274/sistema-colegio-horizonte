<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { margin: 0 0 8px; font-size: 18px; }
        .meta { margin-bottom: 12px; font-size: 11px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        .totals { margin-top: 12px; }
    </style>
</head>
<body>
<h1>Reporte de asistencia</h1>
<div class="meta">
    Generado: {{ now()->format('d/m/Y H:i:s') }}<br>
    Filtros:
    fecha={{ $filters['date'] ?: 'todas' }},
    desde={{ $filters['date_from'] ?: '---' }},
    hasta={{ $filters['date_to'] ?: '---' }},
    sección={{ $filters['section_id'] ?: 'todas' }},
    estudiante={{ $filters['student_id'] ?: 'todos' }},
    estado={{ $filters['status'] ?: 'todos' }}.
</div>

<table>
    <thead>
    <tr>
        <th>Fecha</th><th>Estudiante</th><th>Código</th><th>Nivel</th><th>Grado</th><th>Sección</th><th>Estado</th><th>Obs.</th><th>Registró</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $row)
        <tr>
            <td>{{ $row->attendance_date?->format('d/m/Y') }}</td>
            <td>{{ trim($row->student?->first_name.' '.$row->student?->last_name) }}</td>
            <td>{{ $row->student?->code }}</td>
            <td>{{ $row->educationalLevel?->name }}</td>
            <td>{{ $row->grade?->name }}</td>
            <td>{{ $row->section?->name }}</td>
            <td>{{ $row->status->value }}</td>
            <td>{{ $row->observation ?? '—' }}</td>
            <td>{{ $row->recordedBy?->name ?? '—' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="totals">
    <p>Total registros: <strong>{{ $metrics['total'] }}</strong></p>
    <p>Porcentaje asistencia: <strong>{{ $metrics['attendance_percentage'] }}%</strong></p>
    <p>Tardanzas: <strong>{{ $metrics['late_count'] }}</strong></p>
    <p>Faltas: <strong>{{ $metrics['absence_count'] }}</strong></p>
    <p>Justificados: <strong>{{ $metrics['justified_count'] }}</strong></p>
</div>
</body>
</html>

