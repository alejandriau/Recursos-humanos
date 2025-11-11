@extends('dashboard')

@section('title', 'Detalle de Solicitud')

@section('contenido ')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-info text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Detalle de Solicitud</h5>
                    <div>
                        @if($vacacion->estado == 'pendiente')
                            <button class="btn btn-success btn-sm" onclick="aprobarVacacion({{ $vacacion->id }})">
                                <i class="fas fa-check me-1"></i>Aprobar
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="rechazarVacacion({{ $vacacion->id }})">
                                <i class="fas fa-times me-1"></i>Rechazar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información del Empleado</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Nombre:</th>
                                <td>{{ $vacacion->persona->nombre }} {{ $vacacion->persona->apellidoPat }} {{ $vacacion->persona->apellidoMat }}</td>
                            </tr>
                            <tr>
                                <th>Días Disponibles:</th>
                                <td>
                                    <span class="badge bg-{{ $diasDisponibles >= $vacacion->dias_tomados ? 'success' : 'danger' }}">
                                        {{ $diasDisponibles }} días
                                    </span>
                                </td>
                            </tr>
                        </table>

                        <h6 class="mt-4">Información de la Solicitud</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Fecha Inicio:</th>
                                <td>{{ \Carbon\Carbon::parse($vacacion->fecha_inicio)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Fecha Fin:</th>
                                <td>{{ \Carbon\Carbon::parse($vacacion->fecha_fin)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Días Solicitados:</th>
                                <td><span class="badge bg-primary">{{ $vacacion->dias_tomados }} días</span></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    @if($vacacion->estado == 'pendiente')
                                        <span class="badge badge-pending">Pendiente</span>
                                    @elseif($vacacion->estado == 'aprobado')
                                        <span class="badge badge-approved">Aprobado</span>
                                    @else
                                        <span class="badge badge-rejected">Rechazado</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Fecha Solicitud:</th>
                                <td>{{ $vacacion->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        @if($vacacion->motivo)
                            <h6>Motivo de la Solicitud</h6>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-0">{{ $vacacion->motivo }}</p>
                                </div>
                            </div>
                        @endif

                        @if($vacacion->motivo_rechazo)
                            <h6 class="mt-4">Comentario del Rechazo</h6>
                            <div class="card border-danger">
                                <div class="card-body">
                                    <p class="mb-0 text-danger">{{ $vacacion->motivo_rechazo }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Información de días -->
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Resumen de Días</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Días disponibles:</strong> {{ $diasDisponibles }}</p>
                                <p><strong>Días solicitados:</strong> {{ $vacacion->dias_tomados }}</p>
                                <p><strong>Días restantes después:</strong> {{ $diasDisponibles - $vacacion->dias_tomados }}</p>

                                @if($diasDisponibles < $vacacion->dias_tomados)
                                    <div class="alert alert-warning mt-2">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        El empleado no tiene suficientes días disponibles.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.vacaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales (los mismos del index) -->
@include('admin.vacaciones.modals')
@endsection

@push('scripts')
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
@endpush
