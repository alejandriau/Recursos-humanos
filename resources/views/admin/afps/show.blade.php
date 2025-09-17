@extends('dashboard')

@section('title', 'Detalles AFP')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles del AFP #{{ $afp->id }}</h5>
        <div class="btn-group">
            <a href="{{ route('afps.edit', $afp) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('afps.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $afp->id }}
                </div>

                <div class="mb-3">
                    <strong>CUA:</strong> {{ $afp->cua }}
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $afp->persona->nombre ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $afp->estado ? 'success' : 'danger' }}">
                        {{ $afp->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $afp->fechaRegistro->format('d/m/Y H:i') }}
                </div>

                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $afp->FechaActualizacion ? $afp->FechaActualizacion->format('d/m/Y H:i') : 'Nunca' }}
                </div>

                <div class="mb-3">
                    <strong>Archivo PDF:</strong>
                    @if($afp->pdfafps)
                        <a href="{{ route('afps.download', $afp) }}" class="btn btn-success btn-sm ms-2">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                    @else
                        <span class="text-muted">No hay archivo</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="mb-3">
            <strong>Observación:</strong>
            <p class="text-muted">{{ $afp->observacion ?? 'Sin observación' }}</p>
        </div>
    </div>
</div>
@endsection
