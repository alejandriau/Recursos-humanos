@extends('dashboard')

@section('title', 'Detalles de Declaración de Consanguinidad')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles de Declaración de Consanguinidad #{{ $consanguinidad->id }}</h5>
        <div class="btn-group">
            @can('editar consanguinidades')
            <a href="{{ route('consanguinidades.edit', $consanguinidad) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            @endcan
            <a href="{{ route('consanguinidades.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $consanguinidad->id }}
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $consanguinidad->persona->nombre ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Fecha:</strong>
                    {{ $consanguinidad->fecha ? $consanguinidad->fecha->format('d/m/Y') : 'N/A' }}
                    @if($consanguinidad->fecha && $consanguinidad->es_reciente)
                        <br><span class="badge bg-info">Reciente</span>
                    @endif
                    @if($consanguinidad->anios_desde_fecha)
                        <br><span class="text-muted">(Hace {{ $consanguinidad->anios_desde_fecha }} años)</span>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $consanguinidad->estado ? 'success' : 'danger' }}">
                        {{ $consanguinidad->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $consanguinidad->fechaRegistro->format('d/m/Y H:i') }}
                </div>

                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $consanguinidad->fechaActualizacion ? $consanguinidad->fechaActualizacion->format('d/m/Y H:i') : 'Nunca' }}
                </div>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <strong>Observación:</strong>
            <p class="text-muted">{{ $consanguinidad->observacion ?? 'Sin observación' }}</p>
        </div>

        <div class="mb-3">
            <strong>Archivo PDF:</strong>
            @if($consanguinidad->pdfconsag)
                <div class="mt-2">
                    <a href="{{ route('consanguinidades.download', $consanguinidad) }}" class="btn btn-success" target="_blank">
                        <i class="fas fa-download"></i> Descargar PDF
                    </a>
                    <span class="ms-2 text-muted">{{ $consanguinidad->pdfconsag }}</span>
                </div>
            @else
                <p class="text-muted">No hay archivo PDF</p>
            @endif
        </div>

        <div class="mb-3">
            <strong>Información Completa:</strong>
            <p class="text-muted">{{ $consanguinidad->informacion_completa }}</p>
        </div>
    </div>
</div>
@endsection
