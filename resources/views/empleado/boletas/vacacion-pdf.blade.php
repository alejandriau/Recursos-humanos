<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0.5cm 0.9cm; }
        * { box-sizing: border-box; }
        body {
            font-family: "DejaVu Sans", "Helvetica", "Arial", sans-serif;
            color: #1a1a1a;
            font-size: 10px;
            margin: 0;
        }

        /* ===== Encabezado ===== */
        table.header { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.header td { vertical-align: middle; padding: 0; }
        .logo-cell { width: 55px; }
        .logo-cell img { max-width: 50px; max-height: 50px; }
        .logo-cell.right { text-align: right; }
        .org-name {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .org-sub {
            text-align: center;
            font-size: 9.5px;
            color: #1a1a1a;
            margin-top: 1px;
        }
        .doctitle {
            text-align: center;
            font-size: 12.5px;
            font-weight: bold;
            color: #1a5276;
            text-transform: uppercase;
            margin: 6px 0 10px;
        }

        /* ===== Filas de datos (estilo caja simple) ===== */
        table.datos { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.datos td {
            border: 0.75px solid #333;
            padding: 4px 6px;
            font-size: 10px;
        }
        table.datos .etiqueta { font-weight: bold; width: 30%; }
        table.datos .valor { width: 70%; }

        table.dos-col { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.dos-col td {
            border: 0.75px solid #333;
            padding: 4px 6px;
            font-size: 10px;
            width: 50%;
        }
        table.dos-col .etiqueta { font-weight: bold; }

        table.motivo { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.motivo td {
            border: 0.75px solid #333;
            padding: 4px 6px;
            font-size: 10px;
            vertical-align: top;
            height: 26px;
        }
        table.motivo .etiqueta { font-weight: bold; margin-bottom: 2px; display: block; }

        /* Días solicitados: destacado pero simple */
        .dias-destacado { font-size: 12px; font-weight: bold; color: #1a5276; }

        /* ===== Aprobaciones (mismo espíritu que "firma" del PDF de comisión) ===== */
        table.aprobaciones { width: 100%; border-collapse: collapse; margin-top: 6px; margin-bottom: 6px; }
        table.aprobaciones td {
            border: 0.75px solid #333;
            width: 50%;
            padding: 6px 7px;
            font-size: 9px;
            vertical-align: top;
            height: 40px;
        }
        .titulo-aprob {
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .badge-estado {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 8px;
            font-size: 8.5px;
            font-weight: bold;
        }
        .bg-aprobado { background: #d4edda; color: #155724; }
        .bg-rechazado { background: #f8d7da; color: #721c24; }
        .bg-pendiente_jefe, .bg-pendiente_rrhh { background: #fff3cd; color: #856404; }

        /* ===== Pie: código + QR ===== */
        table.pie { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.pie td { vertical-align: middle; padding: 0; }
        .verif-text {
            font-size: 7.5px;
            color: #555;
            line-height: 1.4;
            width: 62%;
            padding-right: 10px;
        }
        .codigo-control { font-size: 8.5px; color: #555; margin-top: 4px; }
        .codigo-control strong { display: block; font-size: 11px; color: #1a5276; letter-spacing: 0.5px; }
        .qr-cell { text-align: right; width: 38%; }
        .qr-cell img { width: 92px; height: 92px; }
        .qr-label { font-size: 7px; color: #888; text-align: right; margin-top: 2px; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('images/logo-gober-i.png') }}" alt="Logo">
            </td>
            <td>
                <div class="org-name">Gobierno Autónomo Departamental de Cochabamba</div>
                <div class="org-sub">Órgano Ejecutivo &ndash; Unidad de Gestión de Recursos Humanos (UGRH)</div>
            </td>
            <td class="logo-cell right">
                <img src="{{ public_path('images/logo-cbba.png') }}" alt="Logo UGRH">
            </td>
        </tr>
    </table>

    <div class="doctitle">Boleta de Vacación</div>

    <table class="datos">
        <tr>
            <td class="etiqueta">Nombre y apellidos</td>
            <td class="valor">{{ $salida->persona->nombre }} {{ $salida->persona->apellidoPat }} {{ $salida->persona->apellidoMat }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Cargo</td>
            <td class="valor">{{ $cargo }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Unidad / Dependencia</td>
            <td class="valor">{{ $unidad }}</td>
        </tr>
    </table>

    <table class="dos-col">
        <tr>
            <td class="etiqueta">Fecha de inicio</td>
            <td>{{ \Carbon\Carbon::parse($salida->fechasal)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Fecha de retorno</td>
            <td>{{ \Carbon\Carbon::parse($salida->fecharet)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Total días solicitados</td>
            <td class="dias-destacado">{{ $diasSolicitados }} días</td>
        </tr>
        <tr>
            <td class="etiqueta">Fecha de solicitud</td>
            <td>{{ \Carbon\Carbon::parse($salida->fechasol)->format('d-m-Y') }}</td>
        </tr>
    </table>

    <table class="motivo">
        <tr>
            <td>
                <span class="etiqueta">Motivo</span>
                {{ $salida->motivo }}
            </td>
        </tr>
    </table>

    <table class="aprobaciones">
        <tr>
            <td>
                <div class="titulo-aprob">&#10003; Firma Jefe Inmediato</div>
                @if($salida->estado_jefe === 'aprobado' && $salida->jefe)
                    <strong>{{ $salida->jefe->nombre }} {{ $salida->jefe->apellidoPat }}</strong><br>
                    Aprobado electrónicamente<br>
                    {{ \Carbon\Carbon::parse($salida->fecha_aprobacion_jefe)->format('d-m-Y H:i') }}
                @else
                    <span class="badge-estado bg-{{ $salida->estado_jefe }}">{{ ucfirst(str_replace('_', ' ', $salida->estado_jefe)) }}</span>
                @endif
            </td>
            <td>
                <div class="titulo-aprob">&#10003; Vo.Bo. Recursos Humanos</div>
                @if($salida->estado_rrhh === 'aprobado' && $salida->rrhh)
                    <strong>{{ $salida->rrhh->nombre }} {{ $salida->rrhh->apellidoPat }}</strong><br>
                    Autorizado electrónicamente<br>
                    {{ \Carbon\Carbon::parse($salida->fecha_aprobacion_rrhh)->format('d-m-Y H:i') }}
                @else
                    <span class="badge-estado bg-{{ $salida->estado_rrhh }}">{{ ucfirst(str_replace('_', ' ', $salida->estado_rrhh)) }}</span>
                @endif
            </td>
        </tr>
    </table>

    <table class="pie">
        <tr>
            <td class="verif-text">
                Documento generado electrónicamente. La autenticidad de las aprobaciones es verificable mediante el código QR.
                <div class="codigo-control">
                    Código de control
                    <strong>{{ $codigoControl }}</strong>
                </div>
            </td>
            <td class="qr-cell">
                <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR verificación">
                <div class="qr-label">Escanear para verificar</div>
            </td>
        </tr>
    </table>

</body>
</html>
