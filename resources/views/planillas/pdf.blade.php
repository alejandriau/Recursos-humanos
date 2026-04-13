<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificado de Aportes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .gd-number {
            text-align: right;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 18pt;
            margin: 20px 0;
        }
        .subtitle {
            font-weight: bold;
            font-size: 11pt;
            margin: 10px 0;
            text-align: left;
        }
        .content {
            margin: 20px 0;
        }
        .periodo {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }
        th {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .footer {
            margin-top: 50px;
        }
        .signature {
            text-align: center;
            margin-top: 40px;
        }
        .right {
            text-align: right;
        }
        .italic {
            font-style: italic;
        }
        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="title">CERTIFICACION DE APORTES</div>
    <div class="gd-number">GD-UGRH/{{ str_pad(rand(1,999),3,'0',STR_PAD_LEFT) }}/{{ date('Y') }}</div>
    
    <div class="header">
        <div class="subtitle">LA UNIDAD DE GESTIÓN DE RECURSOS HUMANOS DEL GOBIERNO AUTONOMO DEPARTAMENTAL DE COCHABAMBA.</div>
        <div class="subtitle">CERTIFICA:</div>
    </div>
    
    <div class="content">
        <p>Que, la Sra. <strong>{{ strtoupper($persona->apellidoPat . ' ' . $persona->apellidoMat . ' ' . $persona->nombre) }}</strong> con C.I. {{ $persona->ci }}, presta servicios en el Gobierno Autónomo Departamental de Cochabamba bajo el Régimen de la Ley Nº 2027 Estatuto del funcionario Público, como personal de contrato y planta, de acuerdo al siguiente detalle:</p>
        
        @foreach($periodos as $indice => $periodo)
            <div class="periodo">
                @php
                    $inicioStr = \Carbon\Carbon::parse($periodo['inicio'])->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
                    $esUltimo = ($indice === count($periodos) - 1);
                @endphp
                @if($esUltimo)
                    Del {{ $inicioStr }} a la fecha viene desempeñando funciones como {{ $periodo['cargo'] }}
                @else
                    @php $finStr = \Carbon\Carbon::parse($periodo['fin'])->locale('es')->isoFormat('D [de] MMMM [de] YYYY'); @endphp
                    Del {{ $inicioStr }} al {{ $finStr }} desempeño funciones como {{ $periodo['cargo'] }}
                @endif
            </div>
        @endforeach
        
        <p class="italic">siendo sus haberes y aportes los siguientes:</p>
        
        @foreach($planillasPorAnio as $anio => $planillasAnio)
            <h3>GESTION {{ $anio }}</h3>
            <table>
                <thead>
                    <tr>
                        <th>MES</th>
                        <th>DÍAS TRAB.</th>
                        <th>HABER BASICO</th>
                        <th>TOTAL GANADO</th>
                        <th>APORTE A LA SEGURIDAD SOCIAL DE LARGO PLAZO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($planillasAnio as $p)
                        <tr>
                            <td>{{ strtoupper(\Carbon\Carbon::create()->month($p->mes)->locale('es')->monthName) }}</td>
                            <td>{{ $p->dia_trab ?? '-' }}</td>
                            <td>{{ number_format($p->h_basico ?? 0, 2, ',', '.') }}</td>
                            <td>{{ number_format($p->neto ?? 0, 2, ',', '.') }}</td>
                            <td>{{ number_format($p->t_afp ?? 0, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
    
    <div class="footer">
        <p class="italic">Es cuanto certifico, para fines que convenga a la interesada.</p>
        <br><br>
        <div class="right">Cochabamba, {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</div>
        <br><br><br>
        <div class="signature">
            <div>___________________________</div>
            <div class="bold">Unidad de Gestión de Recursos Humanos</div>
        </div>
    </div>
</body>
</html>