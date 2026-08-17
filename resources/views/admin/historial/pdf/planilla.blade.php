<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planilla Presupuestaria</title>
    <style>
        @page { margin: 22px 18px 28px 18px; size: legal landscape; }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 8pt; color: #000; }

        /* Logos */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .header-table td { border: none; vertical-align: middle; padding: 0; }
        .logo-left  { width: 12%; text-align: left; }
        .logo-right { width: 12%; text-align: right; }
        .logo-img   { max-height: 50px; max-width: 110px; }
        .title-center { width: 76%; text-align: center; }
        .main-title { font-size: 10.5pt; font-weight: bold; text-transform: uppercase; margin: 0; line-height: 1.3; }
        .sub-title  { font-size: 9pt; font-weight: bold; margin: 3px 0 0 0; }

        /* Tabla */
        table.data { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.data th {
            background-color: #D9E1F2;
            border: 1px solid #4F81BD;
            padding: 4px 3px;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        table.data td {
            border: 1px solid #4F81BD;
            padding: 3px 4px;
            vertical-align: middle;
            font-size: 7.5pt;
        }
        .dependencia-row td {
            background-color: #B4C7DC;
            font-weight: bold;
            font-size: 8.5pt;
            text-transform: uppercase;
            padding: 5px 4px;
        }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .nowrap { white-space: nowrap; }

        .footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            text-align: center; font-size: 7pt; color: #444;
        }
    </style>
</head>
<body>

    <!-- ENCABEZADO CON LOGOS EN AMBAS ESQUINAS -->
    <table class="header-table">
        <tr>
            <td class="logo-left">
                <img src="{{ public_path('img/logo_gobernacion.png') }}" class="logo-img" alt="Logo Gobernación">
            </td>
            <td class="title-center">
                <p class="main-title">Planilla Presupuestaria de Personal de Planta</p>
                <p class="sub-title">
                    Órgano Ejecutivo del Gobierno Autónomo Departamental de Cochabamba - 2026
                </p>
            </td>
            <td class="logo-right">
                <img src="{{ public_path('img/logo_escudo.png') }}" class="logo-img" alt="Escudo">
            </td>
        </tr>
    </table>

    <!-- DATOS -->
    <table class="data">
        <thead>
            <tr>
                <th style="width:3%">N°</th>
                <th style="width:14%">Dependencia / Denominación Jerárquica</th>
                <th style="width:18%">Nombre de Cargo</th>
                <th style="width:7%">Categoría</th>
                <th style="width:5%">Nivel<br>(Clase)</th>
                <th style="width:6%">Nivel<br>Salarial</th>
                <th style="width:8%">Clasificación<br>del Puesto</th>
                <th style="width:7%">Sueldo o<br>Haber Mensual</th>
                <th style="width:14%">Nombre Completo</th>
                <th style="width:7%">Fecha de<br>Nacimiento</th>
                <th style="width:6%">Nº Carnet</th>
                <th style="width:7%">Fecha de<br>Ingreso</th>
                <th style="width:14%">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($puestos as $fila)
                @if($fila['tipo'] === 'dependencia')
                    <tr class="dependencia-row">
                        <td colspan="13">{{ $fila['nombre'] }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="text-center">{{ $fila['item'] }}</td>
                        <td>{{ $fila['dependencia_jerarquica'] }}</td>
                        <td>{{ $fila['nombre_cargo'] }}</td>
                        <td class="text-center">{{ $fila['categoria'] }}</td>
                        <td class="text-center">{{ $fila['nivel_clase'] }}</td>
                        <td class="text-center">{{ $fila['nivel_salarial'] }}</td>
                        <td class="text-center">{{ $fila['clasificacion'] }}</td>
                        <td class="text-right nowrap">Bs {{ number_format($fila['haber'], 0, ',', '.') }}</td>
                        <td>{{ $fila['nombre_completo'] }}</td>
                        <td class="text-center">{{ $fila['fecha_nacimiento'] }}</td>
                        <td class="text-center">{{ $fila['ci'] }}</td>
                        <td class="text-center">{{ $fila['fecha_ingreso'] }}</td>
                        <td>{{ $fila['observaciones'] }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} | Sistema de Gestión de Recursos Humanos
    </div>

</body>
</html>
