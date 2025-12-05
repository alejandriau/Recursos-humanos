@extends('dashboard')

@section('title', 'Detalles Croquis')

@section('contenido')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalles del Croquis #{{ $croqui->id }}</h5>
        <div class="btn-group">
            <a href="{{ route('croquis.edit', $croqui) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('croquis.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <!--<strong>ID:</strong> {{ $croqui->id }}-->
                </div>

                <div class="mb-3">
                    <strong>Persona:</strong> {{ $croqui->persona->nombre ?? 'N/A' }}{{ $croqui->persona->nombre ?? 'N/A' }}
                    {{ $croqui->persona->apellidoPat ?? 'N/A' }} {{ $croqui->persona->apellidoMat ?? 'N/A' }}
                </div>

                <div class="mb-3">
                    <strong>Dirección:</strong> {{ $croqui->direccion }}
                </div>

                <div class="mb-3">
                    <strong>Descripción:</strong> {{ $croqui->descripcion ?? 'Sin descripción' }}
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Coordenadas:</strong><br>
                    <strong>Latitud:</strong> {{ $croqui->latitud }}<br>
                    <strong>Longitud:</strong> {{ $croqui->longitud }}
                </div>

                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ $croqui->estado ? 'success' : 'danger' }}">
                        {{ $croqui->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="mb-3">
                    <strong>Fecha Registro:</strong> {{ $croqui->fechaRegistro->format('d/m/Y H:i') }}
                </div>

                <div class="mb-3">
                    <strong>Última Actualización:</strong>
                    {{ $croqui->fechaActualizacion ? $croqui->fechaActualizacion->format('d/m/Y H:i') : 'Nunca' }}
                </div>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <strong>Ubicación en Mapa:</strong>
            <div class="mt-2">
                <iframe
                    width="100%"
                    height="400"
                    frameborder="0"
                    scrolling="no"
                    marginheight="0"
                    marginwidth="0"
                    src="{{ $croqui->google_maps_iframe }}"
                    style="border: 1px solid #ccc; border-radius: 5px;">
                </iframe>
            </div>
            <div class="text-center mt-2">
                <a href="{{ $croqui->google_maps_link }}" target="_blank" class="btn btn-primary btn-sm">
                    <i class="fas fa-external-link-alt"></i> Abrir en Google Maps
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
