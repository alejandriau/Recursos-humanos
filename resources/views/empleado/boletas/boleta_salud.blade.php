<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0.4cm 0.8cm; }
        * { box-sizing: border-box; }
        body {
            font-family: "DejaVu Sans", "Helvetica", "Arial", sans-serif;
            color: #263238;
            font-size: 10px;
            margin: 0;
        }

        .doc {
            border: 1px solid #333435;
            border-radius: 6px;
            padding: 10px 16px 12px;
        }

        /* ===== Encabezado ===== */
        table.header { width: 100%; border-collapse: collapse; }
        table.header td { vertical-align: middle; padding: 0; }
        .logo-cell { width: 46px; }
        .logo-cell img { max-width: 42px; max-height: 42px; }
        .logo-cell.right { text-align: right; }
        .org-name {
            text-align: center;
            font-size: 10.5px;
            font-weight: bold;
            color: #103a5c;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .org-sub {
            text-align: center;
            font-size: 8px;
            color: #7a8794;
            margin-top: 1px;
            letter-spacing: 0.2px;
        }

        .accent-line {
            height: 3px;
            border-radius: 2px;
            margin: 7px 0 8px;
            background: linear-gradient(90deg, #103a5c, #2874A6 55%, #cfe0ea 100%);
        }

        /* ===== Título del documento ===== */
        table.doctitle-row { text-align: right;  width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .doctitle {
            font-size: 15px;
            font-weight: bold;
            color: #103a5c;
        }
        .titulo{
            padding-right: 10%;
        }
        .doctitle .doctitle-sub {
            display: block;
            font-size: 8px;
            font-weight: normal;
            color: #8a95a1;
            margin-top: 1px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doccode {
            text-align: right;
            font-size: 8.5px;
            color: #8a95a1;
            vertical-align: bottom;
        }
        .doccode strong {
            display: block;
            font-size: 11px;
            color: #103a5c;
            letter-spacing: 0.5px;
        }

        /* ===== Cuerpo: datos + periodo ===== */
        table.body-grid { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.body-grid > tr > td { vertical-align: top; padding: 0; }
        .col-info { width: 62%; padding-right: 12px; }
        .col-period { width: 38%; }

        .field { margin-bottom: 6px; }
        .field .label {
            display: block;
            font-size: 7.5px;
            font-weight: bold;
            color: #8a95a1;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .field .value {
            display: block;
            font-size: 10.5px;
            color: #263238;
            padding: 2px 0 3px;
            border-bottom: 0.75px solid #3e3e3f;
        }
        .field.small .value { font-size: 9.5px; }

        .field .value-long {
            display: block;
            font-size: 9.5px;
            color: #263238;
            padding: 2px 0 3px;
            border-bottom: 0.75px solid #3e3e3f;
            line-height: 1.4;
        }

        .period-card {
            background: #f4f8fb;
            border: 0.75px solid #434444;
            border-radius: 6px;
            padding: 9px 10px;
            text-align: center;
        }
        .period-label {
            font-size: 7.5px;
            font-weight: bold;
            color: #2874A6;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 4px;
        }
        .period-dates {
            font-size: 10px;
            font-weight: bold;
            color: #103a5c;
            margin-bottom: 4px;
        }
        .period-dates .arrow { color: #2874A6; padding: 0 3px; }
        .period-times {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }
        .period-times .label-time {
            font-size: 7.5px;
            color: #8a95a1;
            text-transform: uppercase;
        }

        .badge-tipo {
            background: #103a5c;
            border-radius: 20px;
            padding: 5px 4px;
            display: block;
            margin-top: 6px;
        }
        .badge-tipo-text {
            font-size: 9px;
            color: #cfe0ea;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===== Aprobaciones ===== */
        table.approvals { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin: 0 -8px 8px; }
        table.approvals td {
            width: 50%;
            border: 0.75px solid #39393a;
            border-left: 3px solid #535455;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 9px;
            vertical-align: top;
        }
        table.approvals td.aprobado { border-left-color: #2e9e5b; }
        table.approvals td.rechazado { border-left-color: #c0392b; }
        table.approvals td.pendiente_jefe,
        table.approvals td.pendiente_rrhh { border-left-color: #d4a017; }

        .approval-title {
            font-weight: bold;
            color: #103a5c;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 3px;
        }
        .approval-name { font-size: 9.5px; color: #263238; font-weight: bold; }
        .approval-meta { font-size: 8px; color: #8a95a1; margin-top: 1px; }

        .badge-estado {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
        }
        .bg-aprobado { background: #e3f6ea; color: #1e7e42; }
        .bg-rechazado { background: #fbe9e7; color: #a5291a; }
        .bg-pendiente_jefe, .bg-pendiente_rrhh { background: #fdf3d9; color: #8a6300; }

        /* ===== Pie: código + QR ===== */
        table.footer { width: 100%; border-collapse: collapse; margin-top: 2px; }
        table.footer td { vertical-align: middle; padding: 0; }
        .verif-text {
            font-size: 7.5px;
            color: #8a95a1;
            line-height: 1.4;
            width: 65%;
            padding-right: 10px;
        }
        .verif-text strong { color: #103a5c; }
        .qr-cell {
            text-align: right;
            width: 35%;
        }
        .qr-box {
            display: inline-block;
            padding: 4px;
            border: 0.75px solid #58595a;
            border-radius: 6px;
            background: #ffffff;
        }
        .qr-box img { width: 92px; height: 92px; display: block; }
        .qr-label {
            font-size: 6.5px;
            color: #a3adb6;
            text-align: right;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
    </style>
</head>
<body>
    <div class="doc">

        <table class="header">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('images/logo-gober-i.png') }}" alt="Logo">
                </td>
                <td>
                    <div class="org-name">Gobierno Autónomo Departamental de Cochabamba</div>
                    <div class="org-sub">Órgano Ejecutivo &middot; Unidad de Gestión de Recursos Humanos (UGRH)</div>
                </td>
                <td class="logo-cell right">
                    <img src="{{ public_path('images/logo-cbba.png') }}" alt="Logo UGRH">
                </td>
            </tr>
        </table>

        <div class="accent-line"></div>

        <table class="doctitle-row">
            <tr>
                <td class="titulo">
                    <span class="doctitle">BOLETA DE SALIDA POR SALUD
                        <span class="doctitle-sub">Autorización electrónica de salida</span>
                    </span>
                </td>
                <td class="doccode">
                    Código de control
                    <strong>{{ $codigoControl }}</strong>
                </td>
            </tr>
        </table>

        <table class="body-grid">
            <tr>
                <td class="col-info">
                    <div class="field">
                        <span class="label">Nombre completo</span>
                        <span class="value">{{ $salida->persona->nombre }} {{ $salida->persona->apellidoPat }} {{ $salida->persona->apellidoMat }}</span>
                    </div>
                    <div class="field">
                        <span class="label">Cargo</span>
                        <span class="value">{{ $cargo }}</span>
                    </div>
                    <div class="field">
                        <span class="label">Unidad / Dependencia</span>
                        <span class="value">{{ $unidad }}</span>
                    </div>
                    <div class="field small">
                        <span class="label">Motivo de la salida</span>
                        <span class="value">{{ $salida->motivo }}</span>
                    </div>
                    <!-- Si tienes sustento legal, descomenta y pasa la variable -->
                    <!--
                    <div class="field small">
                        <span class="label">Sustento legal</span>
                        <span class="value-long">{{ $sustentoLegal ?? 'No especificado' }}</span>
                    </div>
                    -->
                    <div class="field small" style="margin-bottom:0;">
                        <span class="label">Fecha de solicitud</span>
                        <span class="value" style="border-bottom:none;">{{ \Carbon\Carbon::parse($salida->fechasol)->format('d-m-Y') }}</span>
                    </div>
                    <div class="field small" style="margin-bottom:0; margin-top:4px;">
                        <span class="label">Cantidad (días/horas)</span>
                        <span class="value" style="border-bottom:none;">{{ $salida->cantidad ?? '--' }}</span>
                    </div>
                </td>
                <td class="col-period">
                    <div class="period-card">
                        <div class="period-label">Periodo de salida</div>
                        <div class="period-dates">
                            {{ \Carbon\Carbon::parse($salida->fechasal)->format('d-m-Y') }}
                            <span class="arrow">&rarr;</span>
                            {{ \Carbon\Carbon::parse($salida->fecharet)->format('d-m-Y') }}
                        </div>
                        <div class="period-times">
                            <span class="label-time">Salida:</span> {{ $salida->horasal ?? '--:--' }} &nbsp;|&nbsp;
                            <span class="label-time">Retorno estimado:</span> {{ $salida->horaret ?? '--:--' }}
                        </div>
                        <div class="badge-tipo">
                            <span class="badge-tipo-text">&#9679; Salida por salud</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="approvals">
            <tr>
                <td class="{{ $salida->estado_jefe }}">
                    <div class="approval-title">&#10003; Jefe Inmediato</div>
                    @if($salida->estado_jefe === 'aprobado' && $salida->jefe)
                        <div class="approval-name">{{ $salida->jefe->nombre }} {{ $salida->jefe->apellidoPat }}</div>
                        <div class="approval-meta">Aprobado electrónicamente &middot; {{ \Carbon\Carbon::parse($salida->fecha_aprobacion_jefe)->format('d-m-Y H:i') }}</div>
                    @else
                        <span class="badge-estado bg-{{ $salida->estado_jefe }}">{{ ucfirst(str_replace('_', ' ', $salida->estado_jefe)) }}</span>
                    @endif
                </td>
                <td class="{{ $salida->estado_rrhh }}">
                    <div class="approval-title">&#10003; Recursos Humanos</div>
                    @if($salida->estado_rrhh === 'aprobado' && $salida->rrhh)
                        <div class="approval-name">{{ $salida->rrhh->nombre }} {{ $salida->rrhh->apellidoPat }}</div>
                        <div class="approval-meta">Autorizado electrónicamente &middot; {{ \Carbon\Carbon::parse($salida->fecha_aprobacion_rrhh)->format('d-m-Y H:i') }}</div>
                    @else
                        <span class="badge-estado bg-{{ $salida->estado_rrhh }}">{{ ucfirst(str_replace('_', ' ', $salida->estado_rrhh)) }}</span>
                    @endif
                </td>
            </tr>
        </table>

        <table class="footer">
            <tr>
                <td class="verif-text">
                    Documento generado electrónicamente por el sistema de la <strong>UGRH &ndash; GADC</strong>.
                    La autenticidad de las aprobaciones es verificable escaneando el código QR.
                </td>
                <td class="qr-cell">
                    <div class="qr-box">
                        <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR verificación">
                    </div>
                    <div class="qr-label">Escanear para verificar</div>
                </td>
            </tr>
        </table>

    </div>
</body>
</html>