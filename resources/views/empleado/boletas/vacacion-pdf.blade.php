<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Vacación - {{ $codigoControl }}</title>
    <style>
        @page {
            size: letter portrait;
            margin: 0.5cm 0.6cm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 8.5px;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }

        .half-page {
            width: 100%;
            padding: 0;
            box-sizing: border-box;
        }

        .border-container {
            border: 1px solid #103a5c;
            border-radius: 4px;
            padding: 6px 8px;
            width: 100%;
            background-color: #ffffff;
        }

        /* Encabezado */
        table.header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .logo-left { width: 14%; text-align: left; }
        .logo-left img { max-width: 92px; max-height: 40px; }
        .logo-right { width: 14%; text-align: right; }
        .logo-right img { max-width: 82px; max-height: 40px; }
        .header-title {
            width: 72%;
            text-align: center;
        }
        .header-title h3 {
            margin: 0;
            font-size: 8px;
            text-transform: uppercase;
            font-weight: bold;
            color: #555;
        }
        .header-title h2 {
            margin: 1px 0;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            color: #103a5c;
        }
        .header-title h1 {
            margin: 1px 0 0 0;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
            color: #103a5c;
            letter-spacing: 0.3px;
        }

        /* Control / Correlativo */
        table.control-bar {
            width: 100%;
            border-collapse: collapse;
            border: 0.5px solid #103a5c;
            margin-bottom: 4px;
            background-color: #f4f8fb;
            border-radius: 3px;
        }
        table.control-bar td {
            padding: 2px 5px;
            font-size: 8px;
        }

        /* Estructura Formulario */
        table.form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
            table-layout: fixed;
        }
        table.form-table td {
            padding: 1.5px;
            vertical-align: middle;
        }
        .lbl {
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            color: #333;
        }
        .box-val {
            border: 0.5px solid #888;
            border-radius: 2px;
            padding: 2px 5px;
            font-size: 9px;
            background: #fff;
            color: #000;
        }

        /* Badges de Fecha Modernos */
        table.modern-date-table {
            border-collapse: collapse;
            margin: 0;
        }
        table.modern-date-table td {
            padding: 0 !important;
            border: none !important;
        }
        .date-badge {
            border: 0.5px solid #103a5c;
            background-color: #f8fafc;
            border-radius: 3px;
            padding: 1px 4px;
            font-size: 8.5px;
            font-weight: bold;
            color: #103a5c;
            letter-spacing: 0.5px;
            text-align: center;
        }
        .date-separator {
            padding: 0 3px !important;
            font-weight: bold;
            color: #666;
            font-size: 9px;
        }

        /* Tarjeta de Días Solicitados */
        .days-box {
            border: 0.5px solid #103a5c;
            background-color: #103a5c;
            color: #ffffff;
            border-radius: 3px;
            text-align: center;
            padding: 2px 6px;
            font-weight: bold;
            font-size: 11px;
            display: inline-block;
        }

        /* Tabla de Aprobaciones y Firmas */
        table.signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            table-layout: fixed;
        }
        table.signatures-table td {
            border: 0.5px solid #666;
            vertical-align: top;
            padding: 3px;
            height: 105px;
        }
        .sig-header {
            font-size: 7.5px;
            font-weight: bold;
            text-align: center;
            border-bottom: 0.5px solid #666;
            padding-bottom: 2px;
            margin-bottom: 3px;
            text-transform: uppercase;
            background-color: #f4f8fb;
            color: #103a5c;
        }
        .stamp-box {
            font-size: 7.5px;
            line-height: 1.25;
        }

        .badge-estado {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .bg-aprobado { background: #e3f6ea; color: #1e7e42; }
        .bg-rechazado { background: #fbe9e7; color: #a5291a; }
        .bg-pendiente { background: #fdf3d9; color: #8a6300; }

        .legal-footer {
            font-size: 6px;
            text-align: center;
            margin-top: 3px;
            color: #555;
            border-top: 0.5px dashed #aaa;
            padding-top: 2px;
        }
    </style>
</head>
<body>

@php
    $fsal = \Carbon\Carbon::parse($salida->fechasal);
    $fret = \Carbon\Carbon::parse($salida->fecharet);
    $fsol = \Carbon\Carbon::parse($salida->fechasol);
@endphp

<div class="half-page">
    <div class="border-container">

        <!-- Encabezado Institucional -->
        <table class="header-table">
            <tr>
                <td class="logo-left">
                    <img src="{{ public_path('images/logo-gober-i.png') }}" alt="Escudo">
                </td>
                <td class="header-title">
                    <h3>Órgano Ejecutivo</h3>
                    <h2>Gobierno Autónomo Departamental de Cochabamba</h2>
                    <h1>Formulario de solicitud de vacación</h1>
                </td>
                <td class="logo-right">
                    <img src="{{ public_path('images/logo-cbba.png') }}" alt="Logo UGRH">
                </td>
            </tr>
        </table>

        <!-- Control Electrónico -->
        <table class="control-bar">
            <tr>
                <td style="width: 40%;">
                    <strong>CÓDIGO CONTROL:</strong> <span style="font-size: 9px; font-weight: bold; color: #103a5c;">{{ $codigoControl }}</span>
                </td>
                <td style="width: 35%;">
                    <strong>N° CORRELATIVO:</strong> {{ $salida->codigo ?? 'S/C' }}
                </td>
                <td style="width: 25%; text-align: right;">
                    <strong>ESTADO:</strong> <span style="text-transform: uppercase; font-weight: bold;">{{ str_replace('_', ' ', $salida->estado) }}</span>
                </td>
            </tr>
        </table>

        <!-- Datos del Solicitante -->
        <table class="form-table">
            <tr>
                <td class="lbl" style="width: 22%;">Servidor Publico:</td>
                <td colspan="3" style="width: 78%;">
                    <div class="box-val">
                        <strong>{{ $salida->persona->nombre ?? $salida->persona->nombres }} 
                        {{ $salida->persona->apellidoPat ?? $salida->persona->paterno }} 
                        {{ $salida->persona->apellidoMat ?? $salida->persona->materno }}</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="lbl">Dependencia:</td>
                <td colspan="3">
                    <div class="box-val">{{ $unidad }}</div>
                </td>
            </tr>
            <tr>
                <td class="lbl">Cargo:</td>
                <td colspan="3">
                    <div class="box-val">{{ $cargo }}</div>
                </td>
            </tr>
        </table>

        <!-- Periodo de Vacación (Sin horas) -->
        <table class="form-table">
            <tr>
                <td class="lbl" style="width: 22%;">Fecha Inicio:</td>
                <td style="width: 38%;">
                    <table class="modern-date-table">
                        <tr>
                            <td><div class="date-badge">{{ $fsal->format('d') }}</div></td>
                            <td class="date-separator">/</td>
                            <td><div class="date-badge">{{ $fsal->format('m') }}</div></td>
                            <td class="date-separator">/</td>
                            <td><div class="date-badge">{{ $fsal->format('Y') }}</div></td>
                        </tr>
                    </table>
                </td>
                <td class="lbl" style="width: 18%; text-align: right; padding-right: 4px;">Días Solicitados:</td>
                <td style="width: 22%;">
                    <div class="days-box">
                        {{ $diasSolicitados }} {{ $diasSolicitados == 1 ? 'DÍA' : 'DÍAS' }}
                    </div>
                </td>
            </tr>

            <tr>
                <td class="lbl">Fecha Conclusión:</td>
                <td colspan="3">
                    <table class="modern-date-table">
                        <tr>
                            <td><div class="date-badge">{{ $fret->format('d') }}</div></td>
                            <td class="date-separator">/</td>
                            <td><div class="date-badge">{{ $fret->format('m') }}</div></td>
                            <td class="date-separator">/</td>
                            <td><div class="date-badge">{{ $fret->format('Y') }}</div></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Motivo / Observaciones -->
        <table class="form-table">
            <tr>
                <td class="lbl" style="width: 22%;">Motivo / Detalle:</td>
                <td style="width: 78%;">
                    <div class="box-val" style="min-height: 18px;">
                        {{ $salida->motivo ?? 'Uso de vacación anual reglamentaria.' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Fecha de Solicitud -->
        <table class="form-table">
            <tr>
                <td class="lbl" style="width: 22%;">Fecha Solicitud:</td>
                <td style="width: 78%;">
                    <table class="modern-date-table">
                        <tr>
                            <td style="padding-right: 5px !important; font-size: 8.5px;">Cochabamba,</td>
                            <td><div class="date-badge">{{ $fsol->format('d') }}</div></td>
                            <td style="padding: 0 4px !important; font-size: 8px; font-weight: bold;">DE</td>
                            <td>
                                <div class="date-badge" style="letter-spacing: 0;">
                                    {{ mb_strtoupper($fsol->locale('es')->monthName) }}
                                </div>
                            </td>
                            <td style="padding: 0 4px !important; font-size: 8px; font-weight: bold;">DE</td>
                            <td><div class="date-badge">{{ $fsol->format('Y') }}</div></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Firmas, Flujo de Aprobación y QR -->
        <table class="signatures-table">
            <tr>
                <!-- Solicitante -->
                <td style="width: 28%;">
                    <div class="sig-header">Servidor Solicitante</div>
                    <div class="stamp-box">
                        <strong>REGISTRO ELECTRÓNICO</strong><br>
                        <span>ID: {{ $salida->persona_id }}</span><br>
                        <span>Fecha: {{ $fsol->format('d/m/Y H:i') }}</span>
                    </div>
                </td>

                <!-- Jefe Inmediato -->
                <td style="width: 28%;">
                    <div class="sig-header">Jefe Inmediato</div>
                    <div class="stamp-box">
                        @if($salida->estado_jefe === 'aprobado')
                            <strong style="color: #0056b3;">✓ APROBADO DIGITALMENTE</strong><br>
                            <span>{{ $salida->jefe->nombre ?? $salida->jefe->nombres ?? '' }} {{ $salida->jefe->apellidoPat ?? $salida->jefe->paterno ?? '' }}</span><br>
                            <span>Fecha: {{ \Carbon\Carbon::parse($salida->fecha_aprobacion_jefe)->format('d/m/Y H:i') }}</span>
                        @else
                            <span class="badge-estado bg-pendiente">{{ str_replace('_', ' ', $salida->estado_jefe) }}</span>
                        @endif
                    </div>
                </td>

                <!-- RRHH / Autorización -->
                <td style="width: 28%;">
                    <div class="sig-header">Recursos Humanos</div>
                    <div class="stamp-box">
                        @if($salida->estado_rrhh === 'aprobado')
                            <strong style="color: #1e7e42;">✓ AUTORIZADO UGRH</strong><br>
                            <span>{{ $salida->rrhh->nombre ?? $salida->rrhh->nombres ?? '' }} {{ $salida->rrhh->apellidoPat ?? $salida->rrhh->paterno ?? '' }}</span><br>
                            <span>Fecha: {{ \Carbon\Carbon::parse($salida->fecha_aprobacion_rrhh)->format('d/m/Y H:i') }}</span>
                        @else
                            <span class="badge-estado bg-pendiente">{{ str_replace('_', ' ', $salida->estado_rrhh) }}</span>
                        @endif
                    </div>
                </td>

                <!-- QR Verificación -->
                <td style="width: 16%; text-align: center;">
                    <div class="sig-header">QR</div>
                    <div style="padding-top: 2px;">
                        <img src="data:image/png;base64,{{ $qrBase64 }}" style="width: 80px; height: 80px; display: block; margin: 0 auto;" alt="QR Verificación">
                    </div>
                </td>
            </tr>
        </table>

        <div class="legal-footer">
            Documento oficial generado electrónicamente por la UGRH - GADC. Validez comprobable mediante escaneo del código QR.
        </div>

    </div>
</div>

</body>
</html>