@extends('dashboard')

@section('title', 'Mi Perfil')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mi Perfil</h3>
                    <div class="card-tools">
                        <a href="{{ route('empleado.historial') }}" class="btn btn-info">
                            <i class="fas fa-history"></i> Ver Mi Historial
                        </a>
                        <a href="{{ route('empleado.expediente') }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Descargar Expediente
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Foto de perfil -->
                        <div class="col-md-3 text-center">
                            @if($persona->foto)
                                <img src="{{ route('personas.foto', $persona->id) }}"
                                     alt="Foto" class="img-fluid rounded-circle mb-3" style="max-height: 200px;">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                     style="width: 200px; height: 200px;">
                                    <i class="fas fa-user fa-3x text-secondary"></i>
                                </div>
                            @endif

                            <h4>{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</h4>
                            <p class="text-muted">CI: {{ $persona->ci }}</p>
                        </div>

                        <!-- Información personal -->
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Información Personal</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <th>Nombre completo:</th>
                                            <td>{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</td>
                                        </tr>
                                        <tr>
                                            <th>CI:</th>
                                            <td>{{ $persona->ci }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fecha de Nacimiento:</th>
                                            <td>{{ $persona->fechaNacimiento ? \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y') : 'No especificada' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Edad:</th>
                                            <td>{{ $edad ?? 'No especificada' }} años</td>
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

                                <div class="col-md-6">
                                    <h5>Información Laboral</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <th>Fecha de Ingreso:</th>
                                            <td>{{ $persona->fechaIngreso ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') : 'No especificada' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Antigüedad:</th>
                                            <td>
                                                @if($antiguedad)
                                                    {{ $antiguedad['anos'] }} años, {{ $antiguedad['meses'] }} meses, {{ $antiguedad['dias'] }} días
                                                @else
                                                    No especificada
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Profesión:</th>
                                            <td>{{ $persona->profesion->nombre ?? 'No especificada' }}</td>
                                        </tr>
                                        @if($historial)
                                        <tr>
                                            <th>Puesto Actual:</th>
                                            <td>{{ $historial->puesto->nombre ?? 'No asignado' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Unidad Organizacional:</th>
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
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            @if($persona->observaciones)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>Observaciones</h5>
                                    <p class="text-muted">{{ $persona->observaciones }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
