<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.3;
            font-size: 10px;
        }
        .header {
            background-color: #2c5aa0;
            color: white;
            padding: 8px 0;
            width: 100%;
        }
        .institutional-info {
            background-color: #f8f9fa;
            padding: 4px 20px;
            border-bottom: 1px solid #dee2e6;
            font-size: 8px;
            color: #495057;
        }
        .report-info {
            background-color: #e9ecef;
            padding: 4px 20px;
            font-size: 8px;
            border-bottom: 1px solid #ced4da;
        }
        .letra-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #2c5aa0;
        }
        .letra-display {
            background: #2c5aa0;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .content {
            padding: 0 20px 15px 20px;
        }
        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 9px;
        }
        .tabla th {
            background-color: #2c5aa0;
            color: white;
            padding: 5px 4px;
            text-align: left;
            border: 1px solid #1e3d72;
            font-weight: bold;
            font-size: 9px;
        }
        .tabla td {
            padding: 4px;
            border: 1px solid #dee2e6;
        }
        .tabla tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .numero {
            text-align: center;
            font-weight: bold;
            color: #2c5aa0;
            width: 30px;
        }
        .total-registros {
            background: #2c5aa0;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #343a40;
            color: white;
            padding: 8px 20px;
            font-size: 7px;
        }
        .page-number {
            text-align: center;
            font-size: 8px;
            color: #666;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px dashed #ccc;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-cell {
            width: 40px;
            text-align: center;
            padding: 0 10px;
        }
        .logo {
            width: 35px;
            height: 35px;
            background-image: url('data:image/png;base64,{{ base64_encode(file_get_contents(public_path('dashmin/img/logo-gob.png'))) }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 50%;
        }
        .title-cell {
            text-align: center;
            padding: 0 5px;
        }
        .institution-name {
            font-size: 11px;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
        }
        .institution-department {
            font-size: 8px;
            margin: 0;
            opacity: 0.9;
        }
        .border-bottom {
            border-bottom: 3px solid #f8c300;
        }
        .section-title {
            color: #2c5aa0;
            font-size: 11px;
            font-weight: bold;
            margin: 0;
        }
        .subtitle {
            color: #666;
            font-size: 9px;
            margin: 0;
        }
        .info-adicional {
            margin-top: 10px;
            padding: 6px;
            background: #e8f4f8;
            border-radius: 4px;
            border-left: 3px solid #2c5aa0;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <!-- Encabezado institucional con tabla -->
    <table class="header-table header">
        <tr>
            <td class="logo-cell">
                <div class="logo"></div>
            </td>
            <td class="title-cell">
                <p class="institution-name">GOBIERNO AUTÓNOMO DEPARTAMENTAL DE COCHABAMBA</p>
                <p class="institution-department">Unidad de Gestión de Recursos Humanos - UGRH</p>
            </td>
        </tr>
    </table>
    <div class="border-bottom"></div>

    <!-- Información institucional compacta -->
    <div class="institutional-info">
        <strong>SIGRH</strong> | Pasivo Uno EX CORDECO | {{ date('d/m/Y H:i') }}
    </div>

    <!-- Información del reporte compacta -->
    <div class="report-info">
        <table width="100%">
            <tr>
                <td><strong>Generado por:</strong> Sistema UGRH</td>
                <td><strong>Módulo:</strong> Reportes</td>
                <td><strong>Versión:</strong> 2.0</td>
                <td style="text-align: right;"><strong>Letra:</strong> {{ $letra }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <!-- Sección de la letra compacta -->
        <div class="letra-section">
            <div class="letra-display">LETRA {{ $letra }}</div>
            <div class="section-title">REPORTE EX CORDECO</div>
            <div class="subtitle">Personal letra {{ $letra }}</div>
        </div>

        <!-- Tabla de datos -->
        <table class="tabla">
            <thead>
                <tr>
                    <th width="5%">N°</th>
                    <th width="15%">CÓDIGO</th>
                    <th width="55%">NOMBRE COMPLETO</th>
                    <th width="25%">OBSERVACIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $index => $registro)
                <tr>
                    <td class="numero">{{ $index + 1 }}</td>
                    <td><strong style="color: #2c5aa0;">{{ $registro->codigo }}</strong></td>
                    <td>{{ $registro->nombrecompleto }}</td>
                    <td>{{ $registro->observacion ?: '---' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total de registros -->
        @if(count($datos) > 0)
        <div class="total-registros">
            📊 TOTAL: {{ count($datos) }} REGISTROS
        </div>
        @else
        <div style="text-align: center; color: #e74c3c; font-weight: bold; margin-top: 10px; padding: 8px; background: #ffeaa7; border-radius: 4px; font-size: 10px;">
            ⚠️ NO SE ENCONTRARON REGISTROS PARA LA LETRA {{ $letra }}
        </div>
        @endif

        <!-- Información adicional compacta -->
        <div class="info-adicional">
            <p style="margin: 0; text-align: center;">
                <strong>GADC</strong> · UGRH · Confidencial: Uso Interno · Código: GADC-{{ $letra }}-{{ date('Ymd') }}
            </p>
        </div>

        <!-- Número de página -->
        <div class="page-number">
            Página 1 de 1
        </div>
    </div>

    <!-- Pie de página compacto -->
    <div class="footer">
        <table width="100%">
            <tr>
                <td>
                    <strong>GADC - UGRH</strong> | Av. Heroínas E-0356 · Tel: (591) 4-4259000
                </td>
                <td style="text-align: right;">
                    {{ date('d/m/Y H:i') }} | SIGRH v2.0
                </td>
            </tr>
        </table>
    </div>
</body>
</html>