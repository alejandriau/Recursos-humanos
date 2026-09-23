<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Permiso por Salud - {{ $codigoControl }}</title>
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

        /* Contenedor fluido de media página */
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
        .logo-left img { max-width: 55px; max-height: 40px; }
        .logo-right { width: 14%; text-align: right; }
        .logo-right img { max-width: 60px; max-height: 40px; }
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
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            color: #103a5c;
            letter-spacing: 0.2px;
        }

        /* Barra de Control Electrónico */
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

        /* Tablas de Datos del Formulario */
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

        /* Fechas Estilo Moderno */
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
            padding: 1px 3px;
            font-size: 8.5px;
            font-weight: bold;
            color: #103a5c;
            letter-spacing: 1px;
            text-align: center;
        }
        .date-separator {
            padding: 0 3px !important;
            font-weight: bold;
            color: #666;
            font-size: 9px;
        }

        /* Tablas de Aprobación y QR */
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
    $fret = $salida->fecharet ? \Carbon\Carbon::parse($salida->fecharet) : null;
    $fsol = \Carbon\Carbon::parse($salida->fechasol);
    $cantidadVal = (int) ($salida->cantidad ?? 1);

    // Detección lógica de la modalidad (Horas vs Días)
    $esHoras = ($salida->tipo_unidad ?? '') === 'horas' 
            || ($fret && $fsal->isSameDay($fret) && !empty($salida->horasal));
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
                    <h1>Formulario de Permiso por Salud</h1>
                </td>
                <td class="logo-right">
                    <img src="{{ public_path('images/logo-cbba.png') }}" alt="Logo">
                </td>
            </tr>
        </table>

        <!-- Control y Correlativo -->
        <table class="control-bar">
            <tr>
                <td style="width: 38%;">
                    <strong>CÓDIGO CONTROL:</strong> <span style="font-size: 9px; font-weight: bold; color: #103a5c;">{{ $codigoControl }}</span>
                </td>
                <td style="width: 34%;">
                    <strong>N° CORRELATIVO:</strong> {{ $salida->codigo ?? 'S/C' }}
                </td>
                <td style="width: 28%; text-align: right;">
                    <strong>ESTADO:</strong> <span style="text-transform: uppercase; font-weight: bold;">{{ str_replace('_', ' ', $salida->estado ?? 'PENDIENTE') }}</span>
                </td>
            </tr>
        </table>

        <!-- Datos del Solicitante -->
        <table class="form-table">
            <tr>
                <td class="lbl" style="width: 22%;">Nombres y Apellidos:</td>
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

        <!-- Fechas, Horas y Tiempo Concedido de Forma Dinámica -->
        <table class="form-table">
            @if($esHoras)
                <!-- Modalidad POR HORAS -->
                <tr>
                    <td class="lbl" style="width: 22%;">Fecha del Permiso:</td>
                    <td style="width: 43%;">
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
                    <td class="lbl" style="width: 12%; text-align: right; padding-right: 4px;">Tiempo:</td>
                    <td style="width: 23%;">
                        <div class="box-val" style="text-align: center; font-weight: bold; background-color: #f4f8fb; color: #103a5c;">
                            {{ $cantidadVal }} {{ $cantidadVal == 1 ? 'HORA' : 'HORAS' }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="lbl">Hora Salida:</td>
                    <td>
                        <div class="box-val" style="text-align: center; font-weight: bold; width: 80px;">
                            {{ $salida->horasal ? \Carbon\Carbon::parse($salida->horasal)->format('H:i') : '--:--' }}
                        </div>
                    </td>
                    <td class="lbl" style="text-align: right; padding-right: 4px;">Hora Retorno:</td>
                    <td>
                        <div class="box-val" style="text-align: center; font-weight: bold;">
                            {{ $salida->horaret ? \Carbon\Carbon::parse($salida->horaret)->format('H:i') : '--:--' }}
                        </div>
                    </td>
                </tr>
            @else
                <!-- Modalidad POR DÍAS -->
                <tr>
                    <td class="lbl" style="width: 22%;">Fecha Desde:</td>
                    <td style="width: 43%;">
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
                    <td class="lbl" style="width: 12%; text-align: right; padding-right: 4px;">Tiempo:</td>
                    <td style="width: 23%;">
                        <div class="box-val" style="text-align: center; font-weight: bold; background-color: #f4f8fb; color: #103a5c;">
                            {{ $cantidadVal }} {{ $cantidadVal == 1 ? 'DÍA' : 'DÍAS' }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="lbl">Fecha Hasta:</td>
                    <td colspan="3">
                        <table class="modern-date-table">
                            <tr>
                                <td><div class="date-badge">{{ $fret ? $fret->format('d') : $fsal->format('d') }}</div></td>
                                <td class="date-separator">/</td>
                                <td><div class="date-badge">{{ $fret ? $fret->format('m') : $fsal->format('m') }}</div></td>
                                <td class="date-separator">/</td>
                                <td><div class="date-badge">{{ $fret ? $fret->format('Y') : $fsal->format('Y') }}</div></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            @endif
        </table>

        <!-- Motivo -->
        <table class="form-table">
            <tr>
                <td class="lbl" style="width: 22%;">Motivo Salud:</td>
                <td style="width: 78%;">
                    <div class="box-val" style="min-height: 18px;">
                        {{ $salida->motivo }}
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

        <!-- Firmas y QR Ampliado -->
        <table class="signatures-table">
            <tr>
                <td style="width: 32%;">
                    <div class="sig-header">Firma Servidor/a</div>
                    <div class="stamp-box">
                        <strong>REGISTRO ELECTRÓNICO</strong><br>
                        <span>ID: {{ $salida->persona_id }}</span><br>
                        <span>Fecha: {{ $fsol->format('d/m/Y H:i') }}</span>
                    </div>
                </td>
                <td style="width: 36%;">
                    <div class="sig-header">Firma Inmediato Superior</div>
                    <div class="stamp-box">
                        @if($salida->estado_jefe === 'aprobado')
                            <strong style="color: #0056b3;">✓ APROBADO DIGITALMENTE</strong><br>
                            <span>{{ $salida->jefe->nombre ?? $salida->jefe->nombres ?? '' }} {{ $salida->jefe->apellidoPat ?? $salida->jefe->paterno ?? '' }}</span><br>
                            <span>Fecha: {{ \Carbon\Carbon::parse($salida->fecha_aprobacion_jefe)->format('d/m/Y H:i') }}</span>
                        @else
                            <span style="color: #888;">[ Pendiente ]</span>
                        @endif
                    </div>
                </td>
                <td style="width: 32%; text-align: center;">
                    <div class="sig-header">Verificación QR</div>
                    <div style="padding-top: 2px;">
                        <img src="data:image/png;base64,{{ $qrBase64 }}" style="width: 95px; height: 95px; display: block; margin: 0 auto;" alt="QR Verificación">
                    </div>
                </td>
            </tr>
        </table>

        <div class="legal-footer">
            Documento oficial generado por la UGRH - GADC. Validez comprobable mediante escaneo del código QR.
        </div>

    </div>
</div>

</body>
</html>