@extends('dashboard')

@section('title', 'Detalles Curriculum')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles del Curriculum #{{ $curriculum->id }}</h5>
        <div class="btn-group">
            <a href="{{ route('curriculums.edit', $curriculum) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('curriculums.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $curriculum->id }}
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $curriculum->persona->nombre ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $curriculum->estado ? 'success' : 'danger' }}">
                        {{ $curriculum->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $curriculum->fechaRegistro->format('d/m/Y H:i') }}
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $curriculum->fechaActualizacion ? $curriculum->fechaActualizacion->format('d/m/Y H:i') : 'Nunca' }}
                </div>

                <div class="mb-3">
                    <strong>Archivo PDF:</strong>
                    @if($curriculum->pdfcorri)
                        <a href="{{ route('curriculums.download', $curriculum) }}" class="btn btn-success btn-sm ms-2">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                    @else
                        <span class="text-muted">No hay archivo</span>
                    @endif
                </div>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <strong>Descripción:</strong>
            <p class="text-muted">{{ $curriculum->descripcion ?? 'Sin descripción' }}</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Información Adicional ("Mas"):</strong>
                    <p class="text-muted">{{ $curriculum->mas ?? 'Sin información adicional' }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Otros Datos:</strong>
                    <p class="text-muted">{{ $curriculum->otros ?? 'Sin otros datos' }}</p>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <strong>Información Resumida:</strong>
            <p class="text-muted">{{ $curriculum->informacion_resumida }}</p>
        </div>
    </div>
</div>
@endsection
