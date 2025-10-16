@extends('dashboard')

@section('title', 'Detalles de Formulario 2')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles de Formulario 2 #{{ $formulario2->id }}</h5>
        <div class="btn-group">
            @can('editar formularios2')
            <a href="{{ route('formularios2.edit', $formulario2) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            @endcan
            <a href="{{ route('formularios2.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $formulario2->id }}
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $formulario2->persona->nombre ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Fecha:</strong>
                    {{ $formulario2->fecha ? $formulario2->fecha->format('d/m/Y') : 'N/A' }}
                    @if($formulario2->fecha && $formulario2->es_reciente)
                        <br><span class="badge bg-info">Reciente</span>
                    @endif
                    @if($formulario2->anios_desde_fecha)
                        <br><span class="text-muted">(Hace {{ $formulario2->anios_desde_fecha }} años)</span>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $formulario2->estado ? 'success' : 'danger' }}">
                        {{ $formulario2->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $formulario2->fechaRegistro->format('d/m/Y H:i') }}
                </div>

                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $formulario2->fechaActualizacion ? $formulario2->fechaActualizacion->format('d/m/Y H:i') : 'Nunca' }}
                </div>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <strong>Observación:</strong>
            <p class="text-muted">{{ $formulario2->observacion ?? 'Sin observación' }}</p>
        </div>

        <div class="mb-3">
            <strong>Archivo PDF:</strong>
            @if($formulario2->pdfform2)
                <div class="mt-2">
                    <a href="{{ route('formularios2.download', $formulario2) }}" class="btn btn-success" target="_blank">
                        <i class="fas fa-download"></i> Descargar PDF
                    </a>
                    <span class="ms-2 text-muted">{{ $formulario2->pdfform2 }}</span>
                </div>
            @else
                <p class="text-muted">No hay archivo PDF</p>
            @endif
        </div>

        <div class="mb-3">
            <strong>Información Completa:</strong>
            <p class="text-muted">{{ $formulario2->informacion_completa }}</p>
        </div>
    </div>
</div>
@endsection
