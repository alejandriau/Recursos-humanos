@extends('dashboard')

@section('title', 'Detalles Licencia de Conducir')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles de Licencia de Conducir #{{ $licencia->id }}</h5>
        <div class="btn-group">
            <a href="{{ route('licencias-conducir.edit', $licencia) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('licencias-conducir.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $licencia->id }}
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $licencia->persona->nombre ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Categoría:</strong>
                    <span class="badge bg-primary">{{ $licencia->categoria }}</span>
                    <br>
                    <span class="text-muted">{{ $licencia->categoria_completa }}</span>
                </div>

                <div class="mb-3">
                    <strong>Fecha de Vencimiento:</strong> {{ $licencia->fechavencimiento->format('d/m/Y') }}
                    <br>
                    @if($licencia->esta_vencida)
                        <span class="badge bg-danger">Vencida</span>
                    @else
                        <span class="badge bg-success">{{ $licencia->dias_restantes }} días restantes</span>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $licencia->estado ? 'success' : 'danger' }}">
                        {{ $licencia->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $licencia->fechaRegistro->format('d/m/Y H:i') }}
                </div>

                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $licencia->fechaActualizacion ? $licencia->fechaActualizacion->format('d/m/Y H:i') : 'Nunca' }}
                </div>

                <div class="mb-3">
                    <strong>Archivo PDF:</strong>
                    @if($licencia->pdflicc)
                        <a href="{{ route('licencias-conducir.download', $licencia) }}" class="btn btn-success btn-sm ms-2">
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
            <p class="text-muted">{{ $licencia->descripcion ?? 'Sin descripción' }}</p>
        </div>
    </div>
</div>
@endsection
