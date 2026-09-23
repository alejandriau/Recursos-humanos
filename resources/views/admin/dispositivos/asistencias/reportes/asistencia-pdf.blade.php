<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Asistencia</title>
    <style>

@page {
    size: A4 portrait;
    margin: 8mm 7mm 12mm 7mm;
}

body {
    font-family: 'DejaVu Sans', 'Arial', sans-serif;
    font-size: 9pt;
    color: #333;
    position: relative;
}

/* Logos superiores */
.logo-superior-izquierdo,
.logo-superior-derecho {
    position: absolute;
    top: 0;
    width: 60px;
    height: auto;
}

.logo-superior-izquierdo {
    left: 0;
}

.logo-superior-derecho {
    right: 0;
}

/* Contenido */
.contenido {
    margin-top: 5px;
    margin-bottom: 10px;
}

/* Título */
h2 {
    font-size: 15pt;
    text-align: center;
    margin: 0 0 3px 0;
}

/* Período */
.subtitulo {
    text-align: center;
    font-size: 9pt;
    color: #555;
    margin-bottom: 8px;
}

/* Datos empleado */
.info-empleado {
    margin-bottom: 8px;
    font-size: 9pt;
}

.info-empleado strong {
    display: inline-block;
    width: 75px;
}

/* Tabla */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 7.5pt;
}

th,
td {
    border: 1px solid #999;
    padding: 3px 3px;
    text-align: center;
}

th {
    background-color: #35b335;
    font-weight: bold;
}

/* Totales */
.totales {
    background-color: #f2f2f2;
    font-weight: bold;
}

/* Texto */
.text-end {
    text-align: right;
}

.text-muted {
    color: #777;
}

/* Badges */
.badge {
    display: inline-block;
    padding: 1px 4px;
    border-radius: 3px;
    font-size: 6pt;
    font-weight: bold;
}

/* Estados */
.bg-success {
    background-color: #d4edda;
    color: #155724;
}

.bg-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.bg-warning {
    background-color: #fff3cd;
    color: #856404;
}

.bg-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.bg-secondary {
    background-color: #e2e3e5;
    color: #383d41;
}

.bg-primary {
    background-color: #cce5ff;
    color: #004085;
}

.bg-light {
    background-color: #f8f9fa;
    color: #212529;
}

.fst-italic {
    font-style: italic;
}

.fw-bold {
    font-weight: bold;
}

.text-danger {
    color: #dc3545;
}

.small {
    font-size: 7pt;
}

.mt-1 {
    margin-top: 2px;
}

.mb-0 {
    margin-bottom: 0;
}

.table-warning {
    background-color: #fff3cd;
}

.text-center {
    text-align: center;
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
    <!-- Logos -->
    <img src="{{ public_path('images/logo-cbba.png') }}" class="logo-superior-izquierdo" alt="Logo">
    <img src="{{ public_path('images/logo-gober-i.png') }}" class="logo-superior-derecho" alt="Logo">

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
                    <th style="min-width:90px;">Fecha / Estado</th>
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
                    function minToHm($minutos) {
                        if (!is_numeric($minutos)) return '00:00';
                        $minutos = (int) $minutos;
                        if ($minutos === 0) return '00:00';
                        $abs = abs($minutos);
                        $h = floor($abs / 60);
                        $m = $abs % 60;
                        return str_pad($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
                    }

                    function badgeEstado($estado, $esHoy) {
                        // Usamos abreviaturas para ahorrar espacio
                        if ($estado === 'pendiente') {
                            return '<span class="badge bg-warning" style="font-size:6pt; padding:1px 4px; margin-left:4px;">⏳ Pend</span>';
                        }
                        if ($estado === 'completo') return '<span class="badge bg-success" style="font-size:6pt; padding:1px 4px; margin-left:4px;">✓ Comp</span>';
                        if ($estado === 'tardanza') return '<span class="badge bg-info" style="font-size:6pt; padding:1px 4px; margin-left:4px;">⚠ Tard</span>';
                        if ($estado === 'falta_injustificada') return '<span class="badge bg-danger" style="font-size:6pt; padding:1px 4px; margin-left:4px;">✗ Falta</span>';
                        if ($estado === 'falta_justificada') return '<span class="badge bg-primary" style="font-size:6pt; padding:1px 4px; margin-left:4px;">✓ Justif</span>';
                        if ($estado === 'no_laborable') return '<span class="badge bg-secondary" style="font-size:6pt; padding:1px 4px; margin-left:4px;">No Lab</span>';
                        return '<span class="badge bg-light" style="font-size:6pt; padding:1px 4px; margin-left:4px;">' . $estado . '</span>';
                    }
                @endphp

                @forelse($reporte['dias'] as $dia)
                    <tr @if($dia['es_hoy']) class="table-warning" @endif>
                        <td>
                            <span style="display: inline-block; white-space: nowrap;">
                                {{ $dia['fecha'] }}
                                {!! badgeEstado($dia['estado'], $dia['es_hoy']) !!}
                            </span>
                        </td>
                        <td class="small">{{ $dia['turno'] }}</td>
                        <td>
                            @if($dia['entrada'])
                                {{ $dia['entrada'] }}
                            @elseif($dia['es_hoy'] && $dia['estado'] === 'pendiente')
                                <span class="text-muted fst-italic">Pendiente</span>
                            @elseif($dia['estado'] === 'feriado')
                                <span class="text-muted fst-italic">Feriado</span>
                            @else
                                <span class="text-danger fw-bold">No marcó</span>
                            @endif
                        </td>
                        <td>
                            @if($dia['salida'])
                                {{ $dia['salida'] }}
                            @elseif($dia['es_hoy'] && $dia['estado'] === 'pendiente')
                                <span class="text-muted fst-italic">Pendiente</span>
                            @elseif($dia['estado'] === 'feriado')
                                <span class="text-muted fst-italic">Feriado</span>
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
        <!-- ELIMINADO el div con {PAGE_NUM} porque ya lo pondremos desde el controlador -->
    </div>
</body>
</html>