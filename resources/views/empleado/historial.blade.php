@extends('dashboard')

@section('title', 'Mi Historial')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mi Historial Laboral</h3>
                    <div class="card-tools">
                        <a href="{{ route('empleado.perfil') }}" class="btn btn-primary">
                            <i class="fas fa-user"></i> Volver a Mi Perfil
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($persona->historialPuestos && $persona->historialPuestos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Puesto</th>
                                        <th>Unidad Organizacional</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($persona->historialPuestos as $historial)
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
                                                <span class="badge badge-success">Actual</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($historial->fecha_fin)
                                                <span class="badge badge-secondary">Finalizado</span>
                                            @else
                                                <span class="badge badge-success">Activo</span>
                                            @endif
                                        </td>
                                        <td>{{ $historial->observaciones ?? 'Sin observaciones' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Información</h5>
                            No se encontraron registros en tu historial laboral.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
