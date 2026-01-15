<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Personal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 15px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #2c3e50;
        }

        .header .subtitle {
            font-size: 10px;
            color: #7f8c8d;
            margin-top: 3px;
        }

        .info-section {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #007bff;
        }

        .info-item {
            margin-bottom: 3px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .table-container {
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 8px;
        }

        table th {
            background-color: #2c3e50;
            color: white;
            border: 1px solid #dee2e6;
            padding: 4px 3px;
            text-align: left;
            font-weight: bold;
        }

        table td {
            border: 1px solid #dee2e6;
            padding: 3px 2px;
            vertical-align: top;
        }

        table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #7f8c8d;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
        }

        .page-break {
            page-break-before: always;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            background-color: #6c757d;
            color: white;
            border-radius: 2px;
            font-size: 7px;
            margin-right: 3px;
            margin-bottom: 3px;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #7f8c8d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <h1>REPORTE DE PERSONAL</h1>
        <div class="subtitle">Sistema de Gestión de Recursos Humanos</div>
        <div class="subtitle">Generado: {{ $fechaReporte ?? now()->format('d/m/Y H:i') }}</div>
    </div>

    <!-- Información del reporte -->
    <div class="info-section">
        <div class="info-item">
            <span class="info-label">Total de Registros:</span>
            {{ $total ?? count($personas ?? []) }}
        </div>

        @if(!empty($filtros))
        <div class="info-item">
            <span class="info-label">Filtros Aplicados:</span>
            @foreach($filtros as $filtro)
                <span class="badge">{{ $filtro }}</span>
            @endforeach
        </div>
        @endif

        <div class="info-item">
            <span class="info-label">Columnas Mostradas:</span>
            {{ count($columnas ?? []) }}
        </div>
    </div>

    <!-- Tabla de datos -->
    <div class="table-container">
        @if(isset($personas) && $personas->count() > 0)
            <table>
                <thead>
                    <tr>
                        @foreach($columnas as $key => $label)
                            <th>{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($personas as $persona)
                        <tr>
                            @foreach($columnas as $key => $label)
                                <td>{{ $persona[$key] ?? '' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                No hay datos para mostrar con los filtros aplicados.
            </div>
        @endif
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <div>Reporte generado automáticamente por el Sistema de Gestión de Recursos Humanos</div>
        <div>Página <span class="page-number"></span></div>
    </div>

    <script type="text/javascript">
        // Agregar número de página
        var pages = document.getElementsByClassName('page-number');
        for(var i = 0; i < pages.length; i++) {
            pages[i].innerHTML = (i + 1);
        }
    </script>
</body>
</html>
