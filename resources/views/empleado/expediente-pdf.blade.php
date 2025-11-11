<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expediente - {{ $persona->ci }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .section { margin-bottom: 15px; }
        .section-title { background-color: #f8f9fa; padding: 5px; font-weight: bold; border-left: 4px solid #007bff; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .foto { text-align: center; margin-bottom: 15px; }
        .foto img { max-width: 150px; max-height: 150px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="header">
        <h2>EXPEDIENTE PERSONAL</h2>
        <p>Generado el: {{ $fechaGeneracion }}</p>
    </div>

    <!-- Foto -->
    @if($fotoBase64)
    <div class="foto">
        <img src="data:image/jpeg;base64,{{ $fotoBase64 }}" alt="Foto">
    </div>
    @endif

    <!-- Información Personal -->
    <div class="section">
        <div class="section-title">INFORMACIÓN PERSONAL</div>
        <table>
            <tr>
                <th width="25%">Nombre completo:</th>
                <td>{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</td>
            </tr>
            <tr>
                <th>Cédula de Identidad:</th>
                <td>{{ $persona->ci }}</td>
            </tr>
            <tr>
                <th>Fecha de Nacimiento:</th>
                <td>{{ $persona->fechaNacimiento ? \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y') : 'No especificada' }}</td>
            </tr>
            <tr>
                <th>Edad:</th>
                <td>{{ $edad }} años</td>
            </tr>
            <tr>
                <th>Sexo:</th>
                <td>{{ $persona->sexo }}</td>
            </tr>
            <tr>
                <th>Teléfono:</th>
                <td>{{ $persona->telefono ?? 'No especificado' }}</td>
            </tr>
        </table>
    </div>

    <!-- Información Laboral -->
    <div class="section">
        <div class="section-title">INFORMACIÓN LABORAL</div>
        <table>
            <tr>
                <th width="25%">Fecha de Ingreso:</th>
                <td>{{ $persona->fechaIngreso ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') : 'No especificada' }}</td>
            </tr>
            <tr>
                <th>Antigüedad:</th>
                <td>{{ $antiguedad['anos'] }} años, {{ $antiguedad['meses'] }} meses, {{ $antiguedad['dias'] }} días</td>
            </tr>
            <tr>
                <th>Profesión:</th>
                <td>{{ $persona->profesion->nombre ?? 'No especificada' }}</td>
            </tr>
            @if($historialActual)
            <tr>
                <th>Puesto Actual:</th>
                <td>{{ $historialActual->puesto->nombre ?? 'No asignado' }}</td>
            </tr>
            <tr>
                <th>Unidad Organizacional:</th>
                <td>
                    @php
                        $unidad = $historialActual->puesto->unidadOrganizacional ?? null;
                        $ruta = [];
                        while ($unidad) {
                            $ruta[] = $unidad->nombre;
                            $unidad = $unidad->padre;
                        }
                        echo implode(' → ', array_reverse($ruta));
                    @endphp
                </td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Historial Laboral -->
    @if($persona->historial && $persona->historial->count() > 0)
    <div class="section">
        <div class="section-title">HISTORIAL LABORAL</div>
        <table>
            <thead>
                <tr>
                    <th>Puesto</th>
                    <th>Unidad</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($persona->historial as $historial)
                <tr>
                    <td>{{ $historial->puesto->nombre ?? 'N/A' }}</td>
                    <td>
                        @php
                            $unidad = $historial->puesto->unidadOrganizacional ?? null;
                            $ruta = [];
                            while ($unidad) {
                                $ruta[] = $unidad->nombre;
                                $unidad = $unidad->padre;
                            }
                            echo implode(' → ', array_reverse($ruta));
                        @endphp
                    </td>
                    <td>{{ \Carbon\Carbon::parse($historial->fecha_inicio)->format('d/m/Y') }}</td>
                    <td>
                        @if($historial->fecha_fin)
                            {{ \Carbon\Carbon::parse($historial->fecha_fin)->format('d/m/Y') }}
                        @else
                            Actual
                        @endif
                    </td>
                    <td>
                        @if($historial->fecha_fin)
                            Finalizado
                        @else
                            Activo
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($persona->observaciones)
    <div class="section">
        <div class="section-title">OBSERVACIONES</div>
        <p>{{ $persona->observaciones }}</p>
    </div>
    @endif
</body>
</html>
