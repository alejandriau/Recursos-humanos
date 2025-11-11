@extends('dashboard')

@section('title', 'Gestión de Asistencias')

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Gestión de Asistencias</h5>
                    <div>
                        <span class="badge bg-success me-2">
                            <i class="fas fa-users me-1"></i> {{ $presentesHoy }} Presentes Hoy
                        </span>
                        <a href="{{ route('admin.asistencias.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-1"></i>Nuevo Registro
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros Avanzados -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('admin.asistencias.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Empleado</label>
                                    <select name="persona_id" class="form-select">
                                        <option value="">Todos los empleados</option>
                                        @foreach($personas as $persona)
                                            <option value="{{ $persona->id }}" {{ request('persona_id') == $persona->id ? 'selected' : '' }}>
                                                {{ $persona->nombre }} {{ $persona->apellidoPat }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-select">
                                        <option value="">Todos</option>
                                        @foreach($estados as $key => $value)
                                            <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fecha Específica</label>
                                    <input type="date" name="fecha" class="form-control" value="{{ request('fecha', date('Y-m-d')) }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Desde</label>
                                    <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Hasta</label>
                                    <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Estadísticas Rápidas -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="d-flex flex-wrap gap-3">
                            <div class="stat-card bg-primary text-white p-3 rounded">
                                <h6 class="mb-0">Total Registros</h6>
                                <h4 class="mb-0">{{ $totalRegistros }}</h4>
                            </div>
                            <div class="stat-card bg-success text-white p-3 rounded">
                                <h6 class="mb-0">Presentes Hoy</h6>
                                <h4 class="mb-0">{{ $presentesHoy }}</h4>
                            </div>
                            <div class="stat-card bg-info text-white p-3 rounded">
                                <h6 class="mb-0">Filtrados</h6>
                                <h4 class="mb-0">{{ $asistencias->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Asistencias -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Empleado</th>
                                <th>Fecha</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Retraso</th>
                                <th>Horas Extras</th>
                                <th>Estado</th>
                                <th>Tipo</th>
                                <th>Observaciones</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asistencias as $asistencia)
                                <tr>
                                    <td>
                                        @if($asistencia->persona)
                                            <strong>{{ $asistencia->persona->nombre }} {{ $asistencia->persona->apellidoPat }}</strong>
                                            <br>
                                            <small class="text-muted">ID: {{ $asistencia->idPersona }}</small>
                                        @else
                                            <span class="text-danger">Persona no encontrada</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($asistencia->fecha)
                                            {{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($asistencia->hora_entrada)
                                            <span class="badge bg-success">{{ $asistencia->hora_entrada }}</span>
                                        @else
                                            <span class="badge bg-secondary">--:--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($asistencia->hora_salida)
                                            <span class="badge bg-info">{{ $asistencia->hora_salida }}</span>
                                        @else
                                            <span class="badge bg-secondary">--:--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($asistencia->minutos_retraso > 0)
                                            <span class="badge bg-warning">{{ $asistencia->minutos_retraso }} min</span>
                                        @else
                                            <span class="badge bg-success">0 min</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($asistencia->horas_extras > 0)
                                            <span class="badge bg-info">{{ $asistencia->horas_extras }}h</span>
                                        @else
                                            <span class="badge bg-secondary">0h</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($asistencia->estado == 'presente') bg-success
                                            @elseif($asistencia->estado == 'tardanza') bg-warning
                                            @elseif($asistencia->estado == 'ausente') bg-danger
                                            @elseif($asistencia->estado == 'permiso') bg-info
                                            @elseif($asistencia->estado == 'vacaciones') bg-primary
                                            @else bg-secondary @endif">
                                            {{ $asistencia->estado }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark">{{ $asistencia->tipo_registro }}</span>
                                    </td>
                                    <td>
                                        @if($asistencia->observaciones)
                                            <small title="{{ $asistencia->observaciones }}">
                                                {{ Str::limit($asistencia->observaciones, 30) }}
                                            </small>
                                        @else
                                            <span class="text-muted">Sin observaciones</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.asistencias.edit', $asistencia->id) }}"
                                               class="btn btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.asistencias.destroy', $asistencia->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de eliminar este registro?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No hay registros de asistencia</h5>
                                        <p class="text-muted">No se encontraron registros con los filtros aplicados.</p>
                                        <a href="{{ route('admin.asistencias.index') }}" class="btn btn-primary">
                                            <i class="fas fa-refresh me-1"></i>Ver todos los registros
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($asistencias->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando {{ $asistencias->firstItem() ?? 0 }} - {{ $asistencias->lastItem() ?? 0 }} de {{ $asistencias->total() }} registros
                    </div>
                    {{ $asistencias->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    min-width: 150px;
    text-align: center;
}
.stat-card h6 {
    font-size: 0.875rem;
}
.stat-card h4 {
    font-weight: bold;
}
</style>
@endsection
