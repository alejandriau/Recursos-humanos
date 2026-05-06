<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Croquis - Sistema RRHH GADC</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            background: white;
            font-size: 13px;
            line-height: 1.4;
            color: #212529;
            margin: 0;
            padding: 15px;
        }

        /* Contenedor principal: tamaño carta (8.5x11 pulgadas) */
        .documento {
            max-width: 100%;
            background: white;
            border: none;
        }

        /* Encabezado institucional compacto */
        .institutional-header {
            background: #0a2b4e;
            color: white;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .institution-info h1 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .institution-info p {
            font-size: 11px;
            opacity: 0.9;
        }

        .document-code {
            text-align: right;
            background: rgba(0,0,0,0.2);
            padding: 5px 12px;
            border-radius: 20px;
        }

        .document-code span {
            font-size: 10px;
            display: block;
        }

        .document-code strong {
            font-size: 16px;
            letter-spacing: 1px;
        }

        /* Título */
        .title-section {
            border-left: 3px solid #0a2b4e;
            padding-left: 10px;
            margin-bottom: 15px;
        }

        .title-section h2 {
            color: #0a2b4e;
            font-size: 18px;
            margin-bottom: 2px;
        }

        .title-section p {
            color: #6c757d;
            font-size: 11px;
        }

        /* Tabla de datos más compacta */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .data-table td {
            padding: 6px 5px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: top;
        }

        .data-table td:first-child {
            width: 30%;
            font-weight: 600;
            color: #0a2b4e;
            background-color: transparent;
        }

        /* Mapa más pequeño */
        .map-section {
            margin: 15px 0;
            border: 1px solid #ced4da;
            border-radius: 6px;
            overflow: hidden;
            background: #f8f9fa;
        }

        .map-title {
            background: #e9ecef;
            padding: 4px 10px;
            font-weight: bold;
            font-size: 11px;
            border-bottom: 1px solid #ced4da;
            color: #0a2b4e;
        }

        .map-frame {
            width: 100%;
            height: 620px;
            border: none;
        }

        /* Botón de impresión */
        .print-button-container {
            text-align: center;
            margin-bottom: 15px;
        }

        .btn-print {
            background: #0a2b4e;
            color: white;
            border: none;
            padding: 6px 18px;
            border-radius: 30px;
            font-size: 13px;
            cursor: pointer;
        }

        .btn-print:hover {
            background: #0e3a5f;
        }

        /* Pie de página compacto */
        .footer {
            border-top: 1px solid #dee2e6;
            padding: 8px 0;
            font-size: 10px;
            color: #6c757d;
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        /* Estilos para impresión (tamaño carta) */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }

            .print-button-container {
                display: none;
            }

            .institutional-header {
                background: #0a2b4e;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .map-frame {
                height: 520px;
                break-inside: avoid;
            }

            .documento {
                margin: 0;
                padding: 0;
            }

            /* Asegurar que no se rompa en dos páginas */
            .map-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<div class="documento">

    <!-- Encabezado institucional compacto sin logo -->
    <div class="institutional-header">
        <div class="institution-info">
            <h1>GOBIERNO AUTÓNOMO DEPARTAMENTAL DE COCHABAMBA</h1>
            <p>Unidad de Gestión de Recursos Humanos - Sistema Integral RR.HH.</p>
        </div>
        <div class="document-code">
            <span>CROQUIS N°</span>
            <strong>{{ str_pad($croqui->id, 6, '0', STR_PAD_LEFT) }}</strong>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="title-section">
        <h2>REGISTRO DE UBICACIÓN GEOGRÁFICA</h2>
        <p>Croquis de domicilio particular / referencia laboral</p>
    </div>

    <!-- Tabla de datos -->
    <table class="data-table">
        <tr><td>N° Expediente / Croquis</td><td><strong>{{ $croqui->id }}</strong> (Registro interno)</td></tr>
        <tr><td>Servidor Público / Persona</td><td>{{ $croqui->persona->nombre ?? 'N/A' }} {{ $croqui->persona->apellidoPat ?? '' }} {{ $croqui->persona->apellidoMat ?? '' }}</td></tr>
        <tr><td>Dirección</td><td>{{ $croqui->direccion }}</td></tr>
        <tr><td>Descripción / Referencias</td><td>{{ $croqui->descripcion ?? 'Sin descripción' }}</td></tr>
        <tr><td>Coordenadas</td><td><strong>Latitud:</strong> {{ $croqui->latitud }}<br><strong>Longitud:</strong> {{ $croqui->longetud }}</td></tr>
        <tr><td>Fecha registro</td><td>{{ $croqui->fechaRegistro->format('d/m/Y H:i') }}</td></tr>
    </table>

    <!-- Mapa -->
    <div class="map-section">
        <div class="map-title">📍 MAPA DE UBICACIÓN</div>
        <iframe class="map-frame" src="{{ $croqui->google_maps_iframe }}" frameborder="0" scrolling="no" loading="lazy"></iframe>
        <div style="padding: 4px 10px; font-size: 10px; text-align: center;">
            <a href="{{ $croqui->google_maps_link }}" target="_blank" style="color:#0a2b4e;">Abrir en Google Maps</a>
        </div>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <div>Sistema RR.HH – GADC</div>
        <div>Impreso: {{ now()->format('d/m/Y H:i:s') }} | Usuario: {{ Auth::user()->name ?? 'Sistema' }}</div>
        <div>Documento referencial</div>
    </div>

</div>

<!-- Botón de impresión -->
<div class="print-button-container">
    <button class="btn-print" onclick="window.print();">🖨️ Imprimir / Guardar PDF</button>
</div>

</body>
</html>