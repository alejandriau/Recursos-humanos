@extends('dashboard')

@section('title', 'Mis Vacaciones')

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-umbrella-beach me-2"></i>Mis Solicitudes de Vacaciones</h5>
            </div>
            <div class="card-body">
                <!-- Resumen de días -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h4 class="card-title">{{ $diasDisponibles }}</h4>
                                <p class="card-text">Días Disponibles</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h4 class="card-title">{{ $vacaciones->where('estado', 'aprobado')->sum('dias_tomados') }}</h4>
                                <p class="card-text">Días Aprobados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h4 class="card-title">{{ $vacaciones->where('estado', 'pendiente')->count() }}</h4>
                                <p class="card-text">Solicitudes Pendientes</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('empleado.vacaciones.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" name="buscar" class="form-control" placeholder="Buscar..." value="{{ $buscar }}">
                                </div>
                                <div class="col-md-4">
                                    <select name="estado" class="form-select">
                                        <option value="">Todos los estados</option>
                                        <option value="pendiente" {{ $estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="aprobado" {{ $estado == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                                        <option value="rechazado" {{ $estado == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de vacaciones -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Días Solicitados</th>
                                <th>Estado</th>
                                <th>Fecha Solicitud</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vacaciones as $vacacion)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($vacacion->fecha_inicio)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($vacacion->fecha_fin)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $vacacion->dias_tomados }} días</span>
                                    </td>
                                    <td>

    @if($vacacion->estado == 'pendiente')
        <span class="badge bg-warning text-dark">Pendiente</span>
    @elseif($vacacion->estado == 'aprobado')
        <span class="badge bg-success">Aprobado</span>
    @else
        <span class="badge bg-danger">Rechazado</span>
    @endif

                                    </td>
                                    <td>{{ $vacacion->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('empleado.vacaciones.show', $vacacion) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay solicitudes de vacaciones</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('empleado.vacaciones.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Nueva Solicitud
                    </a>
                    {{ $vacaciones->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
