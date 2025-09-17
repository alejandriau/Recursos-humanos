@extends('dashboard')

@section('title', 'Detalles de Bachiller')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles de Bachiller #{{ $bachiller->id }}</h5>
        <div class="btn-group">
            <a href="{{ route('bachilleres.edit', $bachiller) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('bachilleres.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $bachiller->id }}
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $bachiller->persona->nombre ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Fecha de Bachiller:</strong>
                    {{ $bachiller->fecha ? $bachiller->fecha->format('d/m/Y') : 'N/A' }}
                    @if($bachiller->fecha && $bachiller->es_reciente)
                        <br><span class="badge bg-info">Reciente</span>
                    @endif
                    @if($bachiller->anios_desde_fecha)
                        <br><span class="text-muted">(Hace {{ $bachiller->anios_desde_fecha }} años)</span>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $bachiller->estado ? 'success' : 'danger' }}">
                        {{ $bachiller->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $bachiller->fechaRegistro->format('d/m/Y H:i') }}
                </div>

                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $bachiller->fechaActualizacion ? $bachiller->fechaActualizacion->format('d/m/Y H:i') : 'Nunca' }}
                </div>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <strong>Observación:</strong>
            <p class="text-muted">{{ $bachiller->observacion ?? 'Sin observación' }}</p>
        </div>

        <div class="mb-3">
            <strong>Otros Datos:</strong>
            <p class="text-muted">{{ $bachiller->otros ?? 'Sin otros datos' }}</p>
        </div>

        <div class="mb-3">
            <strong>Información Completa:</strong>
            <p class="text-muted">{{ $bachiller->informacion_completa }}</p>
        </div>
    </div>
</div>
@endsection
