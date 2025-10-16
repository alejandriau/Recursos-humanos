<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expediente - {{ $persona->nombre }} {{ $persona->apellidoPat }}</title>
    <style>
        /* Estilos para el PDF */
        @page {
            margin: 50px 30px;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }

        .header .subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin: 0;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #34495e;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 12px;
            border-radius: 4px;
        }

        .subsection-title {
            color: #2c3e50;
            font-size: 13px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 4px;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            padding: 6px 8px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            padding: 6px 8px;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: top;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px;
            margin-right: 5px;
            margin-bottom: 3px;
        }

        .badge-primary { background-color: #3498db; color: white; }
        .badge-success { background-color: #27ae60; color: white; }
        .badge-warning { background-color: #f39c12; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-secondary { background-color: #95a5a6; color: white; }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .table th {
            background-color: #34495e;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }

        .table td {
            padding: 8px;
            border-bottom: 1px solid #bdc3c7;
            font-size: 11px;
            vertical-align: top;
        }

        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .photo-placeholder {
            width: 80px;
            height: 100px;
            border: 2px solid #bdc3c7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #7f8c8d;
            text-align: center;
            background-color: #ecf0f1;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #bdc3c7;
            color: #7f8c8d;
            font-size: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        .signature-area {
            margin-top: 40px;
            text-align: center;
        }

        .signature-line {
            width: 300px;
            border-top: 1px solid #333;
            margin: 40px auto 5px auto;
        }

        .alert {
            padding: 8px 12px;
            margin: 10px 0;
            border-radius: 4px;
            font-size: 11px;
        }

        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }

        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-success { color: #27ae60; }
        .text-danger { color: #e74c3c; }
        .text-warning { color: #f39c12; }
        .mb-0 { margin-bottom: 0; }
        .mt-0 { margin-top: 0; }
        .mt-3 { margin-top: 15px; }
        .mb-3 { margin-bottom: 15px; }

        .jerarquia-item {
            padding-left: 15px;
            margin: 3px 0;
            position: relative;
        }

        .jerarquia-item:before {
            content: "▸";
            position: absolute;
            left: 0;
            color: #3498db;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <h1>EXPEDIENTE LABORAL</h1>
        <p class="subtitle">Sistema de Gestión de Personal</p>
        <p class="subtitle">Generado el: {{ $fechaGeneracion }}</p>
    </div>

    <!-- Información Básica -->
    <div class="section">
        <div class="section-title">I. INFORMACIÓN PERSONAL BÁSICA</div>

        <div style="display: flex; gap: 20px; margin-bottom: 15px;">
            <div class="photo-placeholder">
                FOTO<br>DEL<br>COLABORADOR
            </div>

            <div style="flex: 1;">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nombre Completo:</div>
                        <div class="info-value">
                            <strong>{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Documento de Identidad:</div>
                        <div class="info-value">{{ $persona->ci }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Fecha de Nacimiento:</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y') }}
                            ({{ $edad }} años)
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Género:</div>
                        <div class="info-value">{{ $persona->sexo ?? 'No especificado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Estado Civil:</div>
                        <div class="info-value">{{ $persona->estadoCivil ?? 'No especificado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nacionalidad:</div>
                        <div class="info-value">{{ $persona->nacionalidad ?? 'Boliviana' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Teléfono:</div>
                <div class="info-value">{{ $persona->telefono ?? 'No registrado' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Correo Electrónico:</div>
                <div class="info-value">{{ $persona->email ?? 'No registrado' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Dirección:</div>
                <div class="info-value">{{ $persona->direccion ?? 'No registrada' }}</div>
            </div>
        </div>
    </div>

    <!-- Información Profesional -->
    <div class="section">
        <div class="section-title">II. INFORMACIÓN PROFESIONAL</div>

        @if($persona->profesion)
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Título Profesional:</div>
                <div class="info-value">{{ $persona->profesion->provisionN ?? 'No registrado' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Titulación:</div>
                <div class="info-value">
                    @if($persona->profesion->fechaProvision)
                        {{ \Carbon\Carbon::parse($persona->profesion->fechaProvision)->format('d/m/Y') }}
                    @else
                        No registrada
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Universidad:</div>
                <div class="info-value">{{ $persona->profesion->universidad ?? 'No registrada' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Registro Profesional:</div>
                <div class="info-value">{{ $persona->profesion->registro ?? 'No registrado' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Número de Diploma:</div>
                <div class="info-value">{{ $persona->profesion->diploma ?? 'No registrado' }}</div>
            </div>
        </div>
        @else
        <div class="alert alert-warning">
            No se registró información profesional para este colaborador.
        </div>
        @endif
    </div>

    <!-- Situación Laboral Actual -->
    <div class="section">
        <div class="section-title">III. SITUACIÓN LABORAL ACTUAL</div>

        @if($historialActual)
        <div class="subsection-title">Datos del Puesto Actual</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Denominación del Puesto:</div>
                <div class="info-value">
                    <strong>{{ $historialActual->puesto->denominacion }}</strong>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Item:</div>
                <div class="info-value">
                    <span class="badge badge-primary">{{ $historialActual->puesto->item ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Nivel Jerárquico:</div>
                <div class="info-value">
                    <span class="badge badge-info">{{ $historialActual->puesto->nivelJerarquico ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Salario Base:</div>
                <div class="info-value">
                    <strong class="text-success">{{ number_format($historialActual->puesto->haber ?? 0, 2) }} Bs.</strong>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Tipo de Movimiento:</div>
                <div class="info-value">
                    <span class="badge badge-warning">
                        {{ ucfirst(str_replace('_', ' ', $historialActual->tipo_movimiento)) }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha Inicio en Puesto:</div>
                <div class="info-value">
                    {{ \Carbon\Carbon::parse($historialActual->fecha_inicio)->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <div class="subsection-title">Ubicación Organizacional</div>
        @if($historialActual->puesto->unidadOrganizacional)
            @php
                $jerarquia = $historialActual->puesto->unidadOrganizacional->obtenerJerarquia();
            @endphp
            <div style="background-color: #f8f9fa; padding: 12px; border-radius: 4px;">
                @foreach($jerarquia as $unidad)
                    <div class="jerarquia-item">
                        <strong>{{ $unidad->tipo }}</strong> de {{ $unidad->denominacion }}
                        @if($unidad->sigla)
                            <em>({{ $unidad->sigla }})</em>
                        @endif
                        @if($unidad->codigo)
                            - Código: {{ $unidad->codigo }}
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-warning">
                No se pudo determinar la ubicación organizacional.
            </div>
        @endif
        @else
        <div class="alert alert-danger">
            <strong>ESTADO: SIN ASIGNACIÓN ACTUAL</strong><br>
            El colaborador no tiene un puesto asignado actualmente.
        </div>
        @endif
    </div>

    <!-- Antigüedad y Trayectoria -->
    <div class="section">
        <div class="section-title">IV. ANTIGÜEDAD Y TRAYECTORIA</div>

        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Fecha de Ingreso:</div>
                <div class="info-value">
                    {{ \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Antigüedad en la Institución:</div>
                <div class="info-value">
                    <strong>{{ $antiguedad['anos'] }} años, {{ $antiguedad['meses'] }} meses y {{ $antiguedad['dias'] }} días</strong>
                    <br><small>({{ $antiguedad['total_meses'] }} meses totales)</small>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Tipo de Personal:</div>
                <div class="info-value">
                    <span class="badge badge-secondary">{{ ucfirst($persona->tipo) }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Estado Laboral:</div>
                <div class="info-value">
                    <span class="badge badge-success">ACTIVO</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Puestos -->
    <div class="section page-break">
        <div class="section-title">V. HISTORIAL DE PUESTOS</div>

        @if($persona->historial->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Puesto</th>
                    <th>Unidad</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Duración</th>
                    <th>Tipo Movimiento</th>
                </tr>
            </thead>
            <tbody>
                @foreach($persona->historial as $historial)
                <tr>
                    <td>
                        <strong>{{ $historial->puesto->denominacion ?? 'N/A' }}</strong>
                        @if($historial->puesto->item)
                            <br><small>Item: {{ $historial->puesto->item }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $historial->puesto->unidadOrganizacional->denominacion ?? 'N/A' }}
                        @if($historial->puesto->unidadOrganizacional->sigla ?? false)
                            <br><small>({{ $historial->puesto->unidadOrganizacional->sigla }})</small>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($historial->fecha_inicio)->format('d/m/Y') }}</td>
                    <td>
                        @if($historial->fecha_fin)
                            {{ \Carbon\Carbon::parse($historial->fecha_fin)->format('d/m/Y') }}
                        @else
                            <span class="badge badge-success">ACTUAL</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $fin = $historial->fecha_fin ? \Carbon\Carbon::parse($historial->fecha_fin) : \Carbon\Carbon::now();
                            $duracion = $fin->diff(\Carbon\Carbon::parse($historial->fecha_inicio));
                        @endphp
                        {{ $duracion->y > 0 ? $duracion->y . 'a ' : '' }}
                        {{ $duracion->m > 0 ? $duracion->m . 'm ' : '' }}
                        {{ $duracion->y == 0 && $duracion->m == 0 ? $duracion->d . 'd' : '' }}
                    </td>
                    <td>
                        <span class="badge badge-warning">
                            {{ ucfirst(str_replace('_', ' ', $historial->tipo_movimiento)) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="alert alert-info">
            No se registran movimientos de puesto para este colaborador.
        </div>
        @endif
    </div>

    <!-- Resumen para Toma de Decisiones -->
    <div class="section">
        <div class="section-title">VI. RESUMEN EJECUTIVO</div>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 4px; border-left: 4px solid #3498db;">
            <h4 style="margin-top: 0; color: #2c3e50;">Perfil del Colaborador</h4>

            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Experiencia en la Institución:</div>
                    <div class="info-value">
                        {{ $antiguedad['anos'] }} años y {{ $antiguedad['meses'] }} meses
                        @if($antiguedad['anos'] >= 5)
                            <span class="badge badge-success">ALTA EXPERIENCIA</span>
                        @elseif($antiguedad['anos'] >= 2)
                            <span class="badge badge-warning">EXPERIENCIA MEDIA</span>
                        @else
                            <span class="badge badge-info">NUEVO INGRESO</span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nivel de Formación:</div>
                    <div class="info-value">
                        @if($persona->profesion)
                            <strong>PROFESIONAL TITULADO</strong><br>
                            <small>{{ $persona->profesion->provisionN }}</small>
                        @else
                            <span class="badge badge-warning">NO ESPECIFICADO</span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Movilidad Interna:</div>
                    <div class="info-value">
                        {{ $persona->historial->count() - 1 }} movimientos internos
                        @if($persona->historial->count() > 2)
                            <span class="badge badge-info">ALTA MOVILIDAD</span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Estabilidad Laboral:</div>
                    <div class="info-value">
                        @if($antiguedad['anos'] >= 3)
                            <span class="badge badge-success">ALTA ESTABILIDAD</span>
                        @else
                            <span class="badge badge-warning">EN PERIODO DE ADAPTACIÓN</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Firmas y Validaciones -->
    <div class="section">
        <div class="section-title">VII. VALIDACIÓN DEL EXPEDIENTE</div>

        <div class="signature-area">
            <p>El presente expediente fue generado automáticamente por el Sistema de Gestión de Personal</p>
            <div class="signature-line"></div>
            <p><strong>Responsable de Recursos Humanos</strong></p>
            <p>Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        </div>

        <div class="alert alert-info mt-3">
            <strong>Nota:</strong> Este documento es de carácter confidencial y debe ser manejado según
            las políticas de protección de datos personales de la institución.
        </div>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        Expediente Laboral - {{ $persona->nombre }} {{ $persona->apellidoPat }} -
        CI: {{ $persona->ci }} - Página <span class="pageNumber"></span> de <span class="totalPages"></span>
    </div>

    <script type="text/javascript">
        // Script para numeración de páginas (compatible con DomPDF)
        var vars = {};
        var x = document.location.search.substring(1).split('&');
        for (var i in x) {
            var z = x[i].split('=',2);
            vars[z[0]] = unescape(z[1]);
        }
        var x = ['frompage','topage','page','webpage','section','subsection','subsubsection'];
        for (var i in x) {
            var y = document.getElementsByClassName(x[i]);
            for (var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];
        }
    </script>
</body>
</html>
