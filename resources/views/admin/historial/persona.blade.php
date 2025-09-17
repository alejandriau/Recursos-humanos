@extends('dashboard')

@section('contenidouno')
    <title>Historial de {{ $persona->nombreCompleto }}</title>
    <style>
        .timeline {
            border-left: 3px solid #007bff;
            border-bottom-right-radius: 4px;
            border-top-right-radius: 4px;
            margin: 0 auto;
            position: relative;
            padding: 0 0 0 30px;
        }
        .timeline-item {
            margin-bottom: 20px;
            position: relative;
        }
        .timeline-item:before {
            content: "";
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #007bff;
            left: -36px;
            top: 5px;
        }
        .badge-timeline {
            font-size: 0.75rem;
        }
    </style>
@endsection

@section('contenido')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>
                            Historial Laboral
                        </h4>
                        <a href="{{ route('historial') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Información de la Persona -->
                    <div class="row">
                        <div class="col-md-8">
                            <h5>{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</h5>
                            <p class="text-muted mb-1">
                                <strong>CI:</strong> {{ $persona->ci }} |
                                <strong>Fecha Ingreso:</strong> {{ $persona->fechaIngreso->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-{{ $persona->estado ? 'success' : 'secondary' }}">
                                {{ $persona->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline del Historial -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Trayectoria Laboral</h5>
                </div>
                <div class="card-body">
                    @if($persona->historial->count() > 0)
                    <div class="timeline">
                        @foreach($persona->historial as $historial)
                        <div class="timeline-item">
                            <div class="card">
                                <div class="card-header bg-{{ $historial->estado == 'activo' ? 'success' : 'light' }} text-{{ $historial->estado == 'activo' ? 'white' : 'dark' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0">
                                            {{ $historial->puesto->denominacion }}
                                        </h6>
                                        <div>
                                            <span class="badge bg-info badge-timeline">
                                                {{ ucfirst(str_replace('_', ' ', $historial->tipo_movimiento)) }}
                                            </span>
                                            <span class="badge bg-secondary badge-timeline">
                                                {{ ucfirst(str_replace('_', ' ', $historial->tipo_contrato)) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1">
                                                <strong>Item:</strong> {{ $historial->puesto->item }}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Período:</strong>
                                                {{ $historial->fecha_inicio->format('d/m/Y') }} -
                                                {{ $historial->fecha_fin ? $historial->fecha_fin->format('d/m/Y') : 'Actual' }}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Salario:</strong> ${{ number_format($historial->salario, 2) }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1">
                                                <strong>Estado:</strong>
                                                <span class="badge bg-{{ $historial->estado == 'activo' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($historial->estado) }}
                                                </span>
                                            </p>
                                            @if($historial->numero_memo)
                                            <p class="mb-1">
                                                <strong>Memo:</strong> {{ $historial->numero_memo }}
                                            </p>
                                            @endif
                                            @if($historial->motivo)
                                            <p class="mb-1">
                                                <strong>Motivo:</strong> {{ $historial->motivo }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>

                                    @if($historial->archivo_memo)
                                    <div class="mt-2">
                                        <a href="{{ route('historial.descargar', $historial->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i>Descargar Memo
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p>No se encontró historial laboral</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
