<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Aportes - {{ $persona->ci }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 1px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        h4 {
            background-color: #ddd;
            padding: 5px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
        }
        .aportes{
            font-size: 10px
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Certificado de Aportes</h2>
        <p>Unidad de Gestión de Recursos Humanos</p>
    </div>

    <div class="info">
        <p><strong>CI:</strong> {{ $persona->ci }}</p>
        <p><strong>Nombre:</strong> {{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</p>
        <p><strong>Fecha de nacimiento:</strong> {{ optional($persona->fechaNacimiento)->format('d/m/Y') }}</p>
    </div>

    @foreach($planillasPorAnio as $anio => $planillas)
        <h4>Gestión {{ $anio }}</h4>
        <table>
            <thead>
                <tr>
                    <th>Mes</th><th>Días</th><th>HABER BASICO</th><th>TOTAL GANADO</th><th class="aportes">APORTE A LA SEGURIDAD SOCIAL DE LARGO PLAZO</th><th>Descuentos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($planillas as $p)
                <tr>
                    <td>
                        <strong>
                            {{ strtoupper(\Carbon\Carbon::create()->month($p->mes)->locale('es')->monthName) }}
                        </strong>
                    </td>
                    <td>{{ $p->dia_trab ?? '-' }}</td>
                    <td>{{ number_format($p->h_basico ?? 0, 2) }}</td>
                    <td>{{ number_format($p->neto ?? 0, 2) }}</td>
                    <td>{{ number_format($p->t_afp ?? 0, 2) }}</td>
                    <td>{{ number_format($p->tot_des ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="footer">
        Certificación generada el {{ now()->format('d/m/Y') }}<br>
        Documento válido para fines que convenga al interesado.
    </div>
</body>
</html>