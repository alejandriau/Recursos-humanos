@extends('layouts.baseusr')

@section('title', 'Mi Historial')

@section('cuerpo')
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
                                    @foreach($persona->historial as $historial)
                                    <tr>
                                        <td>{{ $historial->puesto->denominacion ?? 'N/A' }}</td>

                                        <td>
                                            @php
                                                $jerarquia = $historial->puesto->unidadOrganizacional?->obtenerJerarquia();
                                            @endphp

                                            {{ $jerarquia ? $jerarquia->pluck('denominacion')->implode(' → ') : 'Sin unidad' }}
                                        </td>

                                        <td>{{ $historial->fecha_inicio?->format('d/m/Y') }}</td>

                                        <td>
                                            {{ $historial->fecha_fin?->format('d/m/Y') ?? 'Actual' }}
                                        </td>

                                        <td>
                                            @if($historial->estado == 'activo')
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($historial->estado) }}</span>
                                            @endif
                                        </td>

                                        <td>{{ $historial->observaciones ?? '-' }}</td>
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
