<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard de Estadísticas - RRHH</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        /* Reset completo para PDF */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            padding: 10px;
            color: #333;
            background: white;
            font-size: 10px;
            line-height: 1.3;
        }

        .container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            background: white;
        }

        .header {
            background: linear-gradient(135deg, #2C3E50 0%, #34495E 100%);
            color: white;
            padding: 15px 10px;
            text-align: center;
            margin-bottom: 15px;
            border-radius: 5px;
            page-break-after: avoid;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .header p {
            margin: 3px 0 0 0;
            opacity: 0.9;
            font-size: 9px;
        }

        .content {
            padding: 0;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .card {
            background: white;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
            page-break-inside: avoid;
        }

        .card h3 {
            margin: 0 0 8px 0;
            color: #2C3E50;
            font-size: 10px;
            font-weight: 600;
            border-bottom: 1px solid #3498DB;
            padding-bottom: 4px;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            border-bottom: 1px solid #f8f9fa;
            font-size: 8px;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-value {
            font-weight: 700;
            font-size: 8px;
            color: #2C3E50;
        }

        .progress-bar {
            background: #ecf0f1;
            border-radius: 3px;
            height: 4px;
            margin-top: 2px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 7px;
            page-break-inside: avoid;
        }

        th {
            background: #3498DB;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-weight: 600;
            font-size: 7px;
        }

        td {
            padding: 4px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 7px;
            line-height: 1.1;
        }

        .badge {
            padding: 2px 4px;
            border-radius: 8px;
            font-size: 6px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }

        .chart-container {
            background: white;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            page-break-inside: avoid;
        }

        .chart-title {
            font-size: 10px;
            font-weight: 600;
            color: #2C3E50;
            margin-bottom: 8px;
            text-align: center;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            margin: 10px 0;
            page-break-inside: avoid;
        }

        .summary-card {
            background: #667eea;
            color: white;
            padding: 8px 4px;
            border-radius: 4px;
            text-align: center;
        }

        .summary-number {
            font-size: 12px;
            font-weight: 700;
            margin: 3px 0;
            line-height: 1;
        }

        .summary-label {
            font-size: 6px;
            opacity: 0.9;
            line-height: 1;
        }

        .footer {
            text-align: center;
            padding: 8px;
            background: #f8f9fa;
            color: #6c757d;
            font-size: 7px;
            border-top: 1px solid #dee2e6;
            margin-top: 15px;
            page-break-before: avoid;
        }

        .color-masculino { background: #3498DB; }
        .color-femenino { background: #E74C3C; }
        .color-otros { background: #9B59B6; }

        /* Colores para texto */
        .text-success { color: #27ae60; }
        .text-danger { color: #e74c3c; }
        .text-warning { color: #f39c12; }

        /* Mini progress bars */
        .mini-progress {
            width: 30px;
            height: 3px;
            background: #ecf0f1;
            border-radius: 1px;
            display: inline-block;
            margin-left: 3px;
            vertical-align: middle;
        }

        .mini-progress-fill {
            height: 100%;
            border-radius: 1px;
        }

        /* Asegurar que todo el contenido sea visible */
        .force-show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>
</head>
<body>
    <!-- Header Fijo -->
    <div class="header force-show">
        <h1>📊 Dashboard de Estadísticas - RRHH</h1>
        <p>Reporte generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="container">
        <div class="content">
            <!-- Resumen Rápido -->
            <div class="summary-grid force-show">
                <div class="summary-card">
                    <div class="summary-number">{{ $estadisticas['total_personal'] }}</div>
                    <div class="summary-label">TOTAL PERSONAL</div>
                </div>
                <div class="summary-card" style="background: #27ae60;">
                    <div class="summary-number">{{ $estadisticas['puestos_ocupados'] }}</div>
                    <div class="summary-label">PUESTOS OCUPADOS</div>
                </div>
                <div class="summary-card" style="background: #e74c3c;">
                    <div class="summary-number">{{ $estadisticas['puestos_vacantes'] }}</div>
                    <div class="summary-label">PUESTOS VACANTES</div>
                </div>
                <div class="summary-card" style="background: #9b59b6;">
                    <div class="summary-number">{{ round($estadisticas['antiguedad_promedio']) }}</div>
                    <div class="summary-label">AÑOS PROMEDIO</div>
                </div>
            </div>

            <!-- Primera Fila -->
            <div class="grid force-show">
                <div class="card">
                    <h3>📈 Estadísticas Principales</h3>
                    <div class="stat-item">
                        <span>Total Personal:</span>
                        <span class="stat-value">{{ $estadisticas['total_personal'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span>Puestos Ocupados:</span>
                        <span class="stat-value text-success">{{ $estadisticas['puestos_ocupados'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span>Puestos Vacantes:</span>
                        <span class="stat-value text-danger">{{ $estadisticas['puestos_vacantes'] }}</span>
                    </div>
                    <div class="stat-item">
                        <span>Antigüedad Promedio:</span>
                        <span class="stat-value">{{ round($estadisticas['antiguedad_promedio']) }} años</span>
                    </div>
                </div>

                <div class="card">
                    <h3>👥 Distribución por Género</h3>
                    @php
                        $totalPersonas = $estadisticas['total_personal'];
                    @endphp
                    @foreach($estadisticas['distribucion_sexo'] as $genero => $total)
                        @php
                            $porcentaje = $totalPersonas > 0 ? round(($total / $totalPersonas) * 100, 1) : 0;
                        @endphp
                        <div class="stat-item">
                            <span>{{ $genero }}:</span>
                            <span class="stat-value">{{ $total }} ({{ $porcentaje }}%)</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill"
                                 style="background: {{ $genero == 'Masculino' ? '#3498DB' : ($genero == 'Femenino' ? '#E74C3C' : '#9B59B6') }};
                                        width: {{ $porcentaje }}%">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Tabla de Unidades -->
            <div class="chart-container force-show">
                <h3 class="chart-title">🏢 Distribución por Unidades</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Unidad</th>
                            <th>Total</th>
                            <th>Ocupados</th>
                            <th>Vacantes</th>
                            <th>% Ocupación</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($distribucionUnidades as $distribucion)
                            @php
                                $porcentaje = $distribucion['porcentaje_ocupacion'];
                                if ($porcentaje >= 80) {
                                    $badgeClass = 'badge-success';
                                    $estado = 'Óptimo';
                                } elseif ($porcentaje >= 50) {
                                    $badgeClass = 'badge-warning';
                                    $estado = 'Moderado';
                                } else {
                                    $badgeClass = 'badge-danger';
                                    $estado = 'Crítico';
                                }
                            @endphp
                            <tr>
                                <td><strong>{{ \Illuminate\Support\Str::limit($distribucion['unidad'], 20) }}</strong></td>
                                <td>{{ $distribucion['total_puestos'] }}</td>
                                <td class="text-success">{{ $distribucion['puestos_ocupados'] }}</td>
                                <td class="text-danger">{{ $distribucion['vacantes'] }}</td>
                                <td>
                                    <span style="font-weight: 600;">{{ $porcentaje }}%</span>
                                    <div class="mini-progress">
                                        <div class="mini-progress-fill" style="
                                            background: {{ $porcentaje >= 80 ? '#27ae60' : ($porcentaje >= 50 ? '#f39c12' : '#e74c3c') }};
                                            width: {{ $porcentaje }}%">
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge {{ $badgeClass }}">{{ $estado }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Evolución del Personal -->
            <div class="chart-container force-show">
                <h3 class="chart-title">📈 Evolución (Últimos 12 Meses)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Mes</th>
                            <th>Total</th>
                            <th>Tendencia</th>
                            <th>Crecimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $mesAnterior = null; @endphp
                        @foreach($evolucionPersonal as $evolucion)
                            @php
                                if ($mesAnterior !== null) {
                                    $diferencia = $evolucion['total'] - $mesAnterior;
                                    if ($diferencia > 0) {
                                        $tendencia = '↗'; $color = '#27ae60';
                                    } elseif ($diferencia < 0) {
                                        $tendencia = '↘'; $color = '#e74c3c';
                                    } else {
                                        $tendencia = '→'; $color = '#f39c12';
                                    }
                                } else {
                                    $tendencia = '•'; $color = '#95a5a6';
                                }
                                $mesAnterior = $evolucion['total'];
                            @endphp
                            <tr>
                                <td><strong>{{ $evolucion['mes'] }}</strong></td>
                                <td style="font-weight: 600;">{{ $evolucion['total'] }}</td>
                                <td style="color: {{ $color }};">{{ $tendencia }}</td>
                                <td style="color: {{ $color }}; font-weight: 600;">
                                    {{ isset($diferencia) ? ($diferencia > 0 ? "+$diferencia" : $diferencia) : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Resumen Ejecutivo -->
            <div class="card force-show">
                <h3>💼 Resumen Ejecutivo</h3>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; font-size: 7px;">
                    <div>
                        <h4 style="color: #2C3E50; margin-bottom: 5px; font-size: 8px;">✅ Fortalezas</h4>
                        <ul style="color: #27ae60; list-style: none; padding: 0; margin: 0;">
                            <li style="margin-bottom: 2px;">• Ocupación: {{ round(($estadisticas['puestos_ocupados'] / $estadisticas['total_puestos'] * 100), 1) }}%</li>
                            <li style="margin-bottom: 2px;">• Antigüedad: {{ round($estadisticas['antiguedad_promedio']) }} años</li>
                        </ul>
                    </div>
                    <div>
                        <h4 style="color: #2C3E50; margin-bottom: 5px; font-size: 8px;">⚠️ Oportunidades</h4>
                        <ul style="color: #e74c3c; list-style: none; padding: 0; margin: 0;">
                            <li style="margin-bottom: 2px;">• {{ $estadisticas['puestos_vacantes'] }} vacantes</li>
                            <li style="margin-bottom: 2px;">• Optimizar distribución</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer force-show">
            <p>© {{ date('Y') }} Sistema de Gestión de RRHH | Reporte confidencial</p>
        </div>
    </div>
</body>
</html>
