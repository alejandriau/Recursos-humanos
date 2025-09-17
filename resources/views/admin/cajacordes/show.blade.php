@extends('dashboard')

@section('title', 'Detalles Caja de Cordes')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles de la Caja de Cordes #{{ $cajacorde->id }}</h5>
        <div class="btn-group">
            <a href="{{ route('cajacordes.edit', $cajacorde) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('cajacordes.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $cajacorde->id }}
                </div>

                <div class="mb-3">
                    <strong>Fecha:</strong> {{ $cajacorde->fecha->format('d/m/Y') }}
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $cajacorde->persona->nombre ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Código:</strong> {{ $cajacorde->codigo ?? 'N/A' }}
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Otros:</strong> {{ $cajacorde->otros ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $cajacorde->estado ? 'success' : 'danger' }}">
                        {{ $cajacorde->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $cajacorde->fechaRegistro->format('d/m/Y H:i') }}
                </div>

                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $cajacorde->fechaActualizacion ? $cajacorde->fechaActualizacion->format('d/m/Y H:i') : 'Nunca' }}
                </div>

                <div class="mb-3">
                    <strong>Archivo PDF:</strong>
                    @if($cajacorde->pdfcaja)
                        <a href="{{ route('cajacordes.download', $cajacorde) }}" class="btn btn-success btn-sm ms-2">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                    @else
                        <span class="text-muted">No hay archivo</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
