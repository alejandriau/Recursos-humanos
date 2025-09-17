@extends('dashboard')

@section('title', 'Detalles Compromiso')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles del Compromiso #{{ $compromiso->id }}</h5>
        <div class="btn-group">
            <a href="{{ route('compromisos.edit', $compromiso) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('compromisos.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $compromiso->id }}
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $compromiso->persona->nombre ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Total de Compromisos:</strong>
                    <span class="badge bg-primary">{{ $compromiso->total_compromisos }}</span>
                </div>

                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $compromiso->estado ? 'success' : 'danger' }}">
                        {{ $compromiso->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $compromiso->fechaRegistro->format('d/m/Y H:i') }}
                </div>

                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $compromiso->fechaActualizacion ? $compromiso->fechaActualizacion->format('d/m/Y H:i') : 'Nunca' }}
                </div>
            </div>
        </div>

        <hr>

        <h6>Compromisos Registrados:</h6>
        <div class="row">
            @foreach($compromiso->compromisos as $comp)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header bg-light">
                        <strong>Compromiso {{ $comp['numero'] }}</strong>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Descripción:</strong> {{ $comp['descripcion'] }}</p>
                        <p class="mb-2">
                            <strong>Archivo:</strong>
                            @if($comp['archivo'])
                                <a href="{{ route('compromisos.download', ['compromiso' => $compromiso, 'numero' => $comp['numero']]) }}"
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-download"></i> Descargar
                                </a>
                            @else
                                <span class="text-muted">No hay archivo</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($compromiso->total_compromisos === 0)
        <div class="alert alert-info">
            No hay compromisos registrados para este registro.
        </div>
        @endif
    </div>
</div>
@endsection
