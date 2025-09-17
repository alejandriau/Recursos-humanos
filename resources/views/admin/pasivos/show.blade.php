@extends('dashboard')

@section('contenidouno')
    <title>Detalles de Designación</title>
    <style>
        .detail-card {
            border-left: 4px solid #007bff;
        }
        .badge-detail {
            font-size: 0.8rem;
        }
    </style>
@endsection

@section('contenido')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-eye me-2"></i>Detalles de Designación
                        </h4>
                        <div>
                            <a href="{{ route('historial') }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                            <a href="{{ route('historial.edit', $historial->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Información del Puesto -->
                        <div class="col-md-6">
                            <div class="card detail-card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title mb-0">Información del Puesto</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Denominación:</strong><br>{{ $historial->puesto->denominacion }}</p>
                                            <p><strong>Item:</strong><br>{{ $historial->puesto->item }}</p>
                                            <p><strong>Nivel Gerárquico:</strong><br>{{ $historial->puesto->nivelgerarquico }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Haber Básico:</strong><br>${{ number_format($historial->puesto->haber, 2) }}</p>
                                            <p><strong>Dependencia:</strong><br>
                                                @php
                                                    $niveles = [];
                                                    if ($historial->puesto->area?->denominacion) $niveles[] = $historial->puesto->area->denominacion;
                                                    if ($historial->puesto->unidad?->denominacion) $niveles[] = $historial->puesto->unidad->denominacion;
                                                    if ($historial->puesto->direccion?->denominacion) $niveles[] = $historial->puesto->direccion->denominacion;
                                                    if ($historial->puesto->secretaria?->denominacion) $niveles[] = $historial->puesto->secretaria->denominacion;
                                                    echo implode(' → ', $niveles);
                                                @endphp
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Personal -->
                        <div class="col-md-6">
                            <div class="card detail-card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="card-title mb-0">Información del Personal</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Nombre Completo:</strong><br>{{ $historial->persona->nombre }} {{ $historial->persona->apellidoPat }} {{ $historial->persona->apellidoMat }}</p>
                                            <p><strong>CI:</strong><br>{{ $historial->persona->ci }}</p>
                                            <p><strong>Estado:</strong><br>
                                                <span class="badge bg-{{ $historial->persona->estado ? 'success' : 'secondary' }}">
                                                    {{ $historial->persona->estado ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Fecha Ingreso:</strong><br>{{ $historial->persona->fechaIngreso->format('d/m/Y') }}</p>
                                            <p><strong>Contacto:</strong><br>{{ $historial->persona->telefono ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Detalles de la Designación -->
                        <div class="col-md-8">
                            <div class="card detail-card mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="card-title mb-0">Detalles de la Designación</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Tipo de Movimiento:</strong><br>
                                                <span class="badge bg-info badge-detail">
                                                    {{ ucfirst(str_replace('_', ' ', $historial->tipo_movimiento)) }}
                                                </span>
                                            </p>
                                            <p><strong>Tipo de Contrato:</strong><br>
                                                <span class="badge bg-secondary badge-detail">
                                                    {{ ucfirst(str_replace('_', ' ', $historial->tipo_contrato)) }}
                                                </span>
                                            </p>
                                            <p><strong>Estado:</strong><br>
                                                <span class="badge bg-{{ $historial->estado == 'activo' ? 'success' : ($historial->estado == 'concluido' ? 'secondary' : 'warning') }} badge-detail">
                                                    {{ ucfirst($historial->estado) }}
                                                </span>
                                            </p>
                                            <p><strong>Salario:</strong><br>${{ number_format($historial->salario, 2) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Fecha Inicio:</strong><br>{{ $historial->fecha_inicio->format('d/m/Y') }}</p>
                                            <p><strong>Fecha Fin:</strong><br>{{ $historial->fecha_fin ? $historial->fecha_fin->format('d/m/Y') : 'Indefinido' }}</p>
                                            <p><strong>Fecha Vencimiento:</strong><br>{{ $historial->fecha_vencimiento ? $historial->fecha_vencimiento->format('d/m/Y') : 'N/A' }}</p>
                                            <p><strong>% Dedicación:</strong><br>{{ $historial->porcentaje_dedicacion }}%</p>
                                        </div>
                                    </div>

                                    @if($historial->motivo)
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <p><strong>Motivo:</strong><br>{{ $historial->motivo }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if($historial->observaciones)
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <p><strong>Observaciones:</strong><br>{{ $historial->observaciones }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Documentación -->
                        <div class="col-md-4">
                            <div class="card detail-card mb-4">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="card-title mb-0">Documentación</h6>
                                </div>
                                <div class="card-body">
                                    @if($historial->numero_memo)
                                    <p><strong>Número de Memo:</strong><br>{{ $historial->numero_memo }}</p>
                                    @endif

                                    @if($historial->fecha_memo)
                                    <p><strong>Fecha de Memo:</strong><br>{{ $historial->fecha_memo->format('d/m/Y') }}</p>
                                    @endif

                                    @if($historial->archivo_memo)
                                    <div class="mt-3">
                                        <a href="{{ route('historial.descargar.memo', $historial->id) }}"
                                           class="btn btn-outline-primary btn-sm w-100" target="_blank">
                                            <i class="fas fa-download me-2"></i>Descargar Memo PDF
                                        </a>
                                    </div>
                                    @else
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No hay archivo adjunto
                                    </div>
                                    @endif

                                    @if($historial->conserva_puesto_original && $historial->puesto_original_id)
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Conserva puesto original: {{ $historial->puestoOriginal->denominacion }}
                                    </div>
                                    @endif

                                    @if($historial->renovacion_automatica)
                                    <div class="alert alert-success mt-3">
                                        <i class="fas fa-sync-alt me-2"></i>
                                        Renovación automática activada
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Auditoría -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="card-title mb-0">Información de Auditoría</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Creado el:</strong><br>{{ $historial->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Actualizado el:</strong><br>{{ $historial->updated_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Última acción:</strong><br>
                                                @if($historial->created_at->diffInMinutes($historial->updated_at) < 2)
                                                    Creación
                                                @else
                                                    Actualización
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
