<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0.5cm 0.9cm; }
        * { box-sizing: border-box; }
        body {
            font-family: "DejaVu Sans", "Helvetica", "Arial", sans-serif;
            color: #222;
            font-size: 10px;
            margin: 0;
        }
        /* Resto de estilos igual que en comisión (mantén las clases) */
        .contenedor { border: 1.5px solid #2874A6; border-radius: 4px; padding: 8px 12px; }
        table.encabezado { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.encabezado td { vertical-align: middle; padding: 0; }
        table.encabezado .logo-izq { width: 60px; text-align: left; }
        table.encabezado .logo-der { width: 60px; text-align: right; }
        table.encabezado img { max-width: 55px; max-height: 55px; }
        table.encabezado .titulo { text-align: center; font-size: 12px; font-weight: bold; color: #1a5276; text-transform: uppercase; line-height: 1.3; }
        table.encabezado .subtitulo { text-align: center; font-size: 9px; color: #555; margin-top: 2px; }
        .linea-doble { border-top: 1.5px solid #2874A6; border-bottom: 0.5px solid #2874A6; margin: 4px 0 8px; height: 3px; }
        .doc-titulo { text-align: center; font-size: 10.5px; font-weight: bold; letter-spacing: 0.5px; background: #eaf2f8; padding: 4px; margin-bottom: 8px; border-radius: 3px; }
        table.datos { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.datos td { padding: 3px 4px; font-size: 10px; vertical-align: top; }
        table.datos .etiqueta { font-weight: bold; color: #1a5276; width: 32%; }
        table.datos .valor { width: 68%; border-bottom: 0.5px dotted #aaa; }
        table.aprobaciones { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.aprobaciones td { width: 50%; border: 0.75px solid #ccc; padding: 6px; font-size: 9px; vertical-align: top; }
        table.aprobaciones .titulo-aprob { font-weight: bold; color: #1a5276; font-size: 9px; text-transform: uppercase; margin-bottom: 3px; }
        .badge-estado { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 8.5px; font-weight: bold; }
        .bg-aprobado { background: #d4edda; color: #155724; }
        .bg-rechazado { background: #f8d7da; color: #721c24; }
        .bg-pendiente { background: #fff3cd; color: #856404; }
        table.pie { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.pie td { vertical-align: middle; padding: 0; }
        .codigo-control { font-size: 9px; color: #555; }
        .codigo-control strong { font-size: 11px; color: #1a5276; letter-spacing: 0.5px; }
        .qr-cell { text-align: right; width: 120px; }
        .qr-cell img { width: 70px; height: 70px; }
        .qr-cell .qr-label { font-size: 7px; color: #888; text-align: right; }
    </style>
</head>
<body>
    <div class="contenedor">
        <!-- Encabezado (igual) -->
        <table class="encabezado">
            <tr>
                <td class="logo-izq">
                    <img src="{{ public_path('images/logo-gober-i.png') }}" alt="Logo">
                </td>
                <td class="titulo">
                    GOBIERNO AUTÓNOMO DEPARTAMENTAL DE COCHABAMBA
                    <div class="subtitulo">Unidad de Gestión de Recursos Humanos (UGRH)</div>
                </td>
                <td class="logo-der">
                    <img src="{{ public_path('images/logo-cbba.png') }}" alt="Logo UGRH">
                </td>
            </tr>
        </table>

        <div class="linea-doble"></div>

        <!-- Título cambiado -->
        <div class="doc-titulo">BOLETA DE VACACIÓN</div>

        <table class="datos">
            <tr>
                <td class="etiqueta">Nombre completo:</td>
                <td class="valor">{{ $salida->persona->nombre }} {{ $salida->persona->apellidoPat }} {{ $salida->persona->apellidoMat }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Cargo:</td>
                <td class="valor">{{ $cargo }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Unidad / Dependencia:</td>
                <td class="valor">{{ $unidad }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Fecha de inicio:</td>
                <td class="valor">{{ \Carbon\Carbon::parse($salida->fechasal)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Fecha de retorno:</td>
                <td class="valor">{{ \Carbon\Carbon::parse($salida->fecharet)->format('d-m-Y') }}</td>
            </tr>
            <!-- Nueva fila: días solicitados -->
            <tr>
                <td class="etiqueta">Días solicitados:</td>
                <td class="valor"><strong>{{ $diasSolicitados }}</strong> días</td>
            </tr>
            <tr>
                <td class="etiqueta">Fecha de solicitud:</td>
                <td class="valor">{{ \Carbon\Carbon::parse($salida->fechasol)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Motivo:</td>
                <td class="valor">{{ $salida->motivo }}</td>
            </tr>
        </table>

        <!-- Aprobaciones (igual) -->
        <table class="aprobaciones">
            <tr>
                <td>
                    <div class="titulo-aprob">&#10003; Aprobación Jefe Inmediato</div>
                    @if($salida->estado_jefe === 'aprobado' && $salida->jefe)
                        <strong>{{ $salida->jefe->nombre }} {{ $salida->jefe->apellidoPat }}</strong><br>
                        Aprobado electrónicamente<br>
                        {{ \Carbon\Carbon::parse($salida->fecha_aprobacion_jefe)->format('d-m-Y H:i') }}
                    @else
                        <span class="badge-estado bg-{{ $salida->estado_jefe }}">
                            {{ ucfirst($salida->estado_jefe) }}
                        </span>
                    @endif
                </td>
                <td>
                    <div class="titulo-aprob">&#10003; Visto Bueno Recursos Humanos</div>
                    @if($salida->estado_rrhh === 'aprobado' && $salida->rrhh)
                        <strong>{{ $salida->rrhh->nombre }} {{ $salida->rrhh->apellidoPat }}</strong><br>
                        Autorizado electrónicamente<br>
                        {{ \Carbon\Carbon::parse($salida->fecha_aprobacion_rrhh)->format('d-m-Y H:i') }}
                    @else
                        <span class="badge-estado bg-{{ $salida->estado_rrhh }}">
                            {{ ucfirst($salida->estado_rrhh) }}
                        </span>
                    @endif
                </td>
            </tr>
        </table>

        <div style="text-align:center; font-size:7.5px; color:#888; margin-top:4px;">
            Documento generado electrónicamente. La autenticidad de las aprobaciones
            es verificable mediante el código QR.
        </div>

        <!-- Pie (código + QR) -->
        <table class="pie">
            <tr>
                <td class="codigo-control">
                    Código de control:<br>
                    <strong>{{ $codigoControl }}</strong>
                </td>
                <td class="qr-cell">
                    <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR verificación">
                    <div class="qr-label">Escanear para verificar</div>
                </td>
            </tr>
        </table>

    </div>
</body>
</html>
