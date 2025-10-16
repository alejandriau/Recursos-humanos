@extends('dashboard')

@section('title', 'Detalles de Formulario 1')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles de Formulario 1 #{{ $formulario1->id }}</h5>
        <div class="btn-group">
            @can('editar formularios1')
            <a href="{{ route('formularios1.edit', $formulario1) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            @endcan
            <a href="{{ route('formularios1.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $formulario1->id }}
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $formulario1->persona->nombre ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Fecha:</strong>
                    {{ $formulario1->fecha ? $formulario1->fecha->format('d/m/Y') : 'N/A' }}
                    @if($formulario1->fecha && $formulario1->es_reciente)
                        <br><span class="badge bg-info">Reciente</span>
                    @endif
                    @if($formulario1->anios_desde_fecha)
                        <br><span class="text-muted">(Hace {{ $formulario1->anios_desde_fecha }} años)</span>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $formulario1->estado ? 'success' : 'danger' }}">
                        {{ $formulario1->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $formulario1->fechaRegistro->format('d/m/Y H:i') }}
                </div>

                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $formulario1->fechaActualizacion ? $formulario1->fechaActualizacion->format('d/m/Y H:i') : 'Nunca' }}
                </div>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <strong>Observación:</strong>
            <p class="text-muted">{{ $formulario1->observacion ?? 'Sin observación' }}</p>
        </div>

        <div class="mb-3">
            <strong>Archivo PDF:</strong>
            @if($formulario1->pdfform1)
                <div class="mt-2">
                    <a href="{{ route('formularios1.download', $formulario1) }}" class="btn btn-success" target="_blank">
                        <i class="fas fa-download"></i> Descargar PDF
                    </a>
                    <span class="ms-2 text-muted">{{ $formulario1->pdfform1 }}</span>
                </div>
            @else
                <p class="text-muted">No hay archivo PDF</p>
            @endif
        </div>

        <div class="mb-3">
            <strong>Información Completa:</strong>
            <p class="text-muted">{{ $formulario1->informacion_completa }}</p>
        </div>
    </div>
</div>
@endsection
