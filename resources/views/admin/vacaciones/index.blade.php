@extends('dashboard')

@section('title', 'Gestión de Vacaciones')

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Gestión de Solicitudes de Vacaciones</h5>
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-clock me-1"></i> {{ $totalPendientes }} Pendientes
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros avanzados -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('admin.vacaciones.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <input type="text" name="buscar" class="form-control" placeholder="Buscar empleado..." value="{{ $buscar }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="estado" class="form-select">
                                        <option value="">Todos los estados</option>
                                        @foreach($estados as $key => $value)
                                            <option value="{{ $key }}" {{ $estado == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="fecha_inicio" class="form-control" placeholder="Desde" value="{{ $fecha_inicio }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="fecha_fin" class="form-control" placeholder="Hasta" value="{{ $fecha_fin }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-2"></i>Filtrar
                                    </button>
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
                                <th>Empleado</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Días</th>
                                <th>Estado</th>
                                <th>Solicitado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vacaciones as $vacacion)
                                <tr>
                                    <td>
                                        <strong>{{ $vacacion->persona->nombre }} {{ $vacacion->persona->apellidoPat }}</strong>
                                    </td>
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
                                        <a href="{{ route('admin.vacaciones.show', $vacacion) }}" class="btn btn-sm btn-info" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($vacacion->estado == 'pendiente')
                                            <button class="btn btn-sm btn-success" title="Aprobar"
                                                    onclick="aprobarVacacion({{ $vacacion->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" title="Rechazar"
                                                    onclick="rechazarVacacion({{ $vacacion->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay solicitudes de vacaciones</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center">
                    {{ $vacaciones->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para aprobar -->
<div class="modal fade" id="modalAprobar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formAprobar" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Aprobar Solicitud de Vacaciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comentario" class="form-label">Comentario (Opcional)</label>
                        <textarea class="form-control" id="comentario" name="comentario" rows="3"
                                  placeholder="Agregar un comentario..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Aprobar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para rechazar -->
<div class="modal fade" id="modalRechazar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRechazar" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Rechazar Solicitud de Vacaciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="motivo_rechazo" class="form-label">Motivo del Rechazo *</label>
                        <textarea class="form-control" id="motivo_rechazo" name="motivo_rechazo" rows="3"
                                  placeholder="Explica el motivo del rechazo..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function aprobarVacacion(id) {
    const form = document.getElementById('formAprobar');
    form.action = `/admin/vacaciones/${id}/aprobar`;
    new bootstrap.Modal(document.getElementById('modalAprobar')).show();
}

function rechazarVacacion(id) {
    const form = document.getElementById('formRechazar');
    form.action = `/admin/vacaciones/${id}/rechazar`;
    new bootstrap.Modal(document.getElementById('modalRechazar')).show();
}
</script>
@endsection

