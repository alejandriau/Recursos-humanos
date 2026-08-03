<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Asistencia</title>
    <style>
        @page {
            margin: 20mm 15mm;
            size: landscape;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            position: relative;
        }
        /* Logos en esquinas */
        .logo-superior-izquierdo {
            position: absolute;
            top: 0;
            left: 0;
            width: 80px;
            height: auto;
        }
        .logo-superior-derecho {
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: auto;
        }
        .logo-inferior-izquierdo {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: auto;
        }
        .logo-inferior-derecho {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 80px;
            height: auto;
        }
        .contenido {
            margin-top: 25px;
            margin-bottom: 25px;
        }
        h2 {
            font-size: 16pt;
            text-align: center;
            margin-bottom: 5px;
        }
        .subtitulo {
            text-align: center;
            font-size: 11pt;
            color: #555;
            margin-bottom: 15px;
        }
        .info-empleado {
            margin-bottom: 15px;
            font-size: 11pt;
        }
        .info-empleado strong {
            display: inline-block;
            width: 120px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        th, td {
            border: 1px solid #999;
            padding: 4px 6px;
            text-align: center;
        }
        th {
            background-color: #e9f5e9;
            font-weight: bold;
        }
        .totales {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .text-muted {
            color: #777;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
        }
        .bg-success { background-color: #d4edda; color: #155724; }
        .bg-danger { background-color: #f8d7da; color: #721c24; }
        .bg-warning { background-color: #fff3cd; color: #856404; }
        .bg-info { background-color: #d1ecf1; color: #0c5460; }
        .bg-secondary { background-color: #e2e3e5; color: #383d41; }
        .bg-primary { background-color: #cce5ff; color: #004085; }
        .bg-light { background-color: #f8f9fa; color: #212529; }
        .fst-italic { font-style: italic; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        .small { font-size: 8pt; }
        .mt-1 { margin-top: 2px; }
        .mb-0 { margin-bottom: 0; }
        .table-warning { background-color: #fff3cd; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <!-- Logos en las cuatro esquinas (ajusta las rutas) -->
    <img src="{{ public_path('images/logo-cbba.png') }}" class="logo-superior-izquierdo" alt="Logo">
    <img src="{{ public_path('images/logo-gober-i.png') }}" class="logo-superior-derecho" alt="Logo">
    <img src="{{ public_path('images/logo-izq-inf.png') }}" class="logo-inferior-izquierdo" alt="Logo">
    <img src="{{ public_path('images/logo-der-inf.png') }}" class="logo-inferior-derecho" alt="Logo">

    <div class="contenido">
        <h2>Reporte de Asistencia</h2>
        <div class="subtitulo">Período: {{ $reporte['fecha_inicio'] }} al {{ $reporte['fecha_fin'] }}</div>

        <div class="info-empleado">
            <strong>Empleado:</strong> {{ $reporte['persona']->nombre }} {{ $reporte['persona']->apellidoPat ?? '' }} {{ $reporte['persona']->apellidoMat ?? '' }}<br>
            <strong>CI:</strong> {{ $reporte['persona']->ci }}
        </div>

        <table>
            <thead>
                <tr>
                    <th style="min-width:120px;">Fecha / Estado</th>
                    <th>Turnos</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Atraso</th>
                    <th>Sal Ant</th>
                    <th>Ausen</th>
                    <th>Justificaciones</th>
                    <th>Hrs. Ext</th>
                    <th>Jor</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Función para formatear minutos a HH:MM
                    function minToHm($minutos) {
                        if ($minutos === null || $minutos === 0) return '00:00';
                        $abs = round(abs($minutos));
                        $h = floor($abs / 60);
                        $m = $abs % 60;
                        return str_pad($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
                    }

                    // Función para badge según estado
                    function badgeEstado($estado, $esHoy) {
                        if ($estado === 'pendiente') {
                            return '<span class="badge bg-warning" title="Aún no cierra el día">⏳ Pendiente</span>';
                        }
                        if ($estado === 'completo') return '<span class="badge bg-success">✓ Completo</span>';
                        if ($estado === 'tardanza') return '<span class="badge bg-info">⚠ Tardanza</span>';
                        if ($estado === 'falta_injustificada') return '<span class="badge bg-danger">✗ Falta</span>';
                        if ($estado === 'falta_justificada') return '<span class="badge bg-primary">✓ Justificado</span>';
                        if ($estado === 'no_laborable') return '<span class="badge bg-secondary">Sin laborar</span>';
                        return '<span class="badge bg-light">' . $estado . '</span>';
                    }
                @endphp

                @forelse($reporte['dias'] as $dia)
                    <tr @if($dia['es_hoy']) class="table-warning" @endif>
                        <td>
                            {{ $dia['fecha'] }}
                            <div class="mt-1">{!! badgeEstado($dia['estado'], $dia['es_hoy']) !!}</div>
                        </td>
                        <td class="small">{{ $dia['turno'] }}</td>
                        <td>
                            @if($dia['entrada'])
                                {{ $dia['entrada'] }}
                            @elseif($dia['es_hoy'] && $dia['estado'] === 'pendiente')
                                <span class="text-muted fst-italic">Pendiente</span>
                            @else
                                <span class="text-danger fw-bold">No marcó</span>
                            @endif
                        </td>
                        <td>
                            @if($dia['salida'])
                                {{ $dia['salida'] }}
                            @elseif($dia['es_hoy'] && $dia['estado'] === 'pendiente')
                                <span class="text-muted fst-italic">Pendiente</span>
                            @else
                                <span class="text-danger fw-bold">No marcó</span>
                            @endif
                        </td>
                        <td>{{ minToHm($dia['atraso_min']) }}</td>
                        <td>{{ minToHm($dia['sal_ant_min']) }}</td>
                        <td>{{ $dia['ausen'] > 0 ? $dia['ausen'] : '00:00' }}</td>
                        <td class="small text-muted">{{ $dia['justificacion'] }}</td>
                        <td>{{ minToHm($dia['ext_min']) }}</td>
                        <td class="fw-bold">{{ minToHm($dia['jor_min']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-muted py-3">No hay días evaluados en este rango</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="totales">
                    <td colspan="4" class="text-end">TOTALES</td>
                    <td>{{ minToHm($reporte['totales']['atraso']) }}</td>
                    <td>{{ minToHm($reporte['totales']['sal_ant']) }}</td>
                    <td>{{ $reporte['totales']['ausen'] }}</td>
                    <td></td>
                    <td>{{ minToHm($reporte['totales']['ext']) }}</td>
                    <td>{{ minToHm($reporte['totales']['jor']) }}</td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 20px; font-size: 8pt; text-align: center; color: #888;">
            Reporte generado el {{ date('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
