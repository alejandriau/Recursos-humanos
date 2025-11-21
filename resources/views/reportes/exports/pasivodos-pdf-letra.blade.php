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
            line-height: 1.4;
        }
        .header {
            background-color: #27ae60;
            color: white;
            padding: 15px 0;
            width: 100%;
        }
        .institutional-info {
            background-color: #f8f9fa;
            padding: 8px 30px;
            border-bottom: 2px solid #dee2e6;
            font-size: 10px;
            color: #495057;
        }
        .report-info {
            background-color: #e9ecef;
            padding: 8px 30px;
            font-size: 9px;
        }
        .letra-section {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
        }
        .letra-display {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 6px 15px;
            border-radius: 15px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .content {
            padding: 0 30px 20px 30px;
        }
        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }
        .tabla th {
            background-color: #27ae60;
            color: white;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #219653;
            font-weight: bold;
        }
        .tabla td {
            padding: 6px;
            border: 1px solid #dee2e6;
        }
        .tabla tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .numero {
            text-align: center;
            font-weight: bold;
            color: #27ae60;
            width: 40px;
        }
        .codigo-completo {
            font-weight: bold;
            color: #e74c3c;
        }
        .total-registros {
            background: #27ae60;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }
        .footer {
            background: #343a40;
            color: white;
            padding: 12px 30px;
            margin-top: 20px;
            font-size: 8px;
        }
        .section-title {
            color: #27ae60;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 5px 0;
        }
        .subtitle {
            color: #666;
            font-size: 10px;
            text-align: center;
            margin-bottom: 12px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-cell {
            width: 60px;
            text-align: center;
            padding: 0 15px;
        }
        .logo {
            width: 50px;
            height: 50px;
            background-image: url('data:image/png;base64,{{ base64_encode(file_get_contents(public_path('dashmin/img/logo-gob.png'))) }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 50%;
        }
        .title-cell {
            text-align: center;
            padding: 0 10px;
        }
        .institution-name {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
        }
        .institution-department {
            font-size: 11px;
            margin: 2px 0 0 0;
            opacity: 0.9;
        }
        .border-bottom {
            border-bottom: 5px solid #f8c300;
        }
        .highlight {
            background: #fff3cd !important;
        }
    </style>
</head>
<body>
    <!-- Encabezado institucional con tabla -->
    <table class="header-table header">
        <tr>
            <td class="logo-cell">
                <div class="logo">GADC</div>
            </td>
            <td class="title-cell">
                <p class="institution-name">GOBIERNO AUTÓNOMO DEPARTAMENTAL DE COCHABAMBA</p>
                <p class="institution-department">Unidad de Gestión de Recursos Humanos - UGRH</p>
            </td>
        </tr>
    </table>
    <div class="border-bottom"></div>

    <!-- Información institucional -->
    <div class="institutional-info">
        <strong>Sistema Integrado de Gestión de Recursos Humanos</strong> |
        Reporte Oficial - Pasivo Dos GADC |
        Fecha: {{ date('d/m/Y H:i:s') }}
    </div>

    <!-- Información del reporte -->
    <div class="report-info">
        <table width="100%">
            <tr>
                <td><strong>Generado por:</strong> Sistema UGRH</td>
                <td><strong>Módulo:</strong> Reportes de Personal</td>
                <td><strong>Versión:</strong> 2.0</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <!-- Sección de la letra -->
        <div class="letra-section">
            <div class="letra-display">LETRA {{ $letra }}</div>
            <div class="section-title">REPORTE DE PERSONAL - SISTEMA GADC</div>
            <div class="subtitle">Listado del personal con código y letra asignada</div>
        </div>

        <!-- Tabla de datos -->
        <table class="tabla">
            <thead>
                <tr>
                    <th width="5%">Nº</th>
                    <th width="20%">CÓDIGO COMPLETO</th>
                    <th width="10%">CÓDIGO</th>
                    <th width="10%">LETRA</th>
                    <th width="40%">NOMBRE COMPLETO</th>
                    <th width="15%">OBSERVACIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $index => $registro)
                <tr class="{{ $index % 5 == 0 ? 'highlight' : '' }}">
                    <td class="numero">{{ $index + 1 }}</td>
                    <td class="codigo-completo">{{ $registro->codigo }}{{ $registro->letra }}</td>
                    <td><strong>{{ $registro->codigo }}</strong></td>
                    <td><strong style="color: #27ae60;">{{ $registro->letra }}</strong></td>
                    <td>{{ $registro->nombrecompleto }}</td>
                    <td>{{ $registro->observacion ?: '---' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total de registros -->
        @if(count($datos) > 0)
        <div class="total-registros">
            📊 TOTAL DE REGISTROS: {{ count($datos) }}
        </div>
        @else
        <div style="text-align: center; color: #e74c3c; font-weight: bold; margin-top: 15px; padding: 12px; background: #ffeaa7; border-radius: 5px; font-size: 11px;">
            ⚠️ NO SE ENCONTRARON REGISTROS PARA LA LETRA {{ $letra }}
        </div>
        @endif

        <!-- Información del sistema -->
        <div style="margin-top: 15px; padding: 10px; background: #e8f5e8; border-radius: 5px; border-left: 4px solid #27ae60;">
            <p style="margin: 0; font-size: 9px; color: #555; text-align: center;">
                <strong>Sistema:</strong> Gestión de Recursos Humanos GADC ·
                <strong>Codificación:</strong> Código + Letra ·
                <strong>Vigencia:</strong> {{ date('Y') }}
            </p>
        </div>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <table width="100%">
            <tr>
                <td>
                    <strong>Gobierno Autónomo Departamental de Cochabamba</strong><br>
                    Unidad de Gestión de Recursos Humanos - UGRH<br>
                    Av. Heroínas E-0356 · Teléfono: (591) 4-4259000
                </td>
                <td style="text-align: right;">
                    Página 1 de 1<br>
                    {{ date('d/m/Y H:i:s') }}<br>
                    SIGRH-GADC v2.0
                </td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 8px; padding-top: 8px; border-top: 1px solid #495057;">
            <div style="font-size: 7px; color: #adb5bd;">
                📄 Documento oficial del GADC ·
                Código: GADC-UGRH-P2-{{ $letra }}-{{ date('YmdHis') }}
            </div>
        </div>
    </div>
</body>
</html>
