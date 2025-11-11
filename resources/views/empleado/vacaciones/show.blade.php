@extends('dashboard')

@section('title', 'Detalle de Vacación')

@section('contenido')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Detalle de Solicitud</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información de la Solicitud</h6>
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
                            <h6 class="mt-3">Comentario del Administrador</h6>
                            <div class="card border-danger">
                                <div class="card-body">
                                    <p class="mb-0 text-danger">{{ $vacacion->motivo_rechazo }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('empleado.vacaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
