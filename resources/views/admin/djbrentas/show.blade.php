@extends('dashboard')

@section('title', 'Detalles DJBRenta')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles del DJBRenta #{{ $djbrenta->id }}</h5>
        <div class="btn-group">
            <a href="{{ route('djbrentas.edit', $djbrenta) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('djbrentas.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $djbrenta->id }}
                </div>

                <div class="mb-3">
                    <strong>Fecha:</strong> {{ $djbrenta->fecha->format('d/m/Y') }}
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $djbrenta->persona->nombre ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Tipo:</strong> {{ $djbrenta->tipo ?? 'Sin tipo' }}
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $djbrenta->estado ? 'success' : 'danger' }}">
                        {{ $djbrenta->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $djbrenta->fechaRegistro->format('d/m/Y H:i') }}
                </div>

                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $djbrenta->fechaActualizacion ? $djbrenta->fechaActualizacion->format('d/m/Y H:i') : 'Nunca' }}
                </div>

                <div class="mb-3">
                    <strong>Archivo PDF:</strong>
                    @if($djbrenta->pdfrenta)
                        <a href="{{ route('djbrentas.download', $djbrenta) }}" class="btn btn-success btn-sm ms-2">
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
