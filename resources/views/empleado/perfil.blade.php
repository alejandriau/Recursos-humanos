{{-- resources/views/empleado/perfil.blade.php --}}
@extends('layouts.baseusr')

@section('title', 'Mi Perfil')

@section('cuerpo')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-user-circle text-primary me-2"></i>Mi Perfil
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                            <li class="breadcrumb-item active">Mi Perfil</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('empleado.historial') }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-history me-1"></i>Historial
                    </a>
                    <a href="{{ route('empleado.expediente') }}" class="btn btn-outline-success">
                        <i class="fas fa-file-pdf me-1"></i>Expediente
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta Principal -->
    <div class="row">
        <div class="col-lg-4">
            <!-- Foto y datos básicos -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    <!-- Foto -->
                    <div class="position-relative d-inline-block">
                        @if($persona->foto)
                            <img src="{{ route('persona.foto', $persona->id) }}"
                                 alt="Foto" class="rounded-circle img-fluid"
                                 style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto"
                                 style="width: 150px; height: 150px; border: 4px solid #fff; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
                                <i class="fas fa-user fa-5x text-secondary"></i>
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-2 border border-white" style="width: 20px; height: 20px;"></span>
                    </div>

                    <h4 class="mt-3 mb-1">{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</h4>
                    <p class="text-muted mb-1">
                        <i class="fas fa-id-card me-1"></i>{{ $persona->ci }}
                    </p>
                    <p class="text-muted small">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Miembro desde {{ $persona->fechaIngreso ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') : 'N/A' }}
                    </p>

                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <div>
                            <span class="badge bg-primary d-block fs-6">{{ $edad ?? 'N/A' }}</span>
                            <small class="text-muted">Años</small>
                        </div>
                        <div>
                            <span class="badge bg-success d-block fs-6">
                                @if($antiguedad)
                                    {{ $antiguedad['anos'] }}a {{ $antiguedad['meses'] }}m
                                @else
                                    N/A
                                @endif
                            </span>
                            <small class="text-muted">Antigüedad</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contacto -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0"><i class="fas fa-address-card me-2 text-primary"></i>Contacto</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-phone text-primary me-2" style="width: 20px;"></i>
                            <span>{{ $persona->telefono ?? 'No registrado' }}</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope text-primary me-2" style="width: 20px;"></i>
                            <span>{{ $persona->email ?? 'No registrado' }}</span>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt text-primary me-2" style="width: 20px;"></i>
                            <span>{{ $persona->direccion ?? 'No registrada' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Información Personal -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-user me-2 text-primary"></i>Información Personal</h6>
                    <span class="badge bg-secondary">{{ $persona->sexo ?? 'N/A' }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small text-uppercase d-block">Nombre Completo</label>
                                <p class="fw-bold mb-0">{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small text-uppercase d-block">Cédula de Identidad</label>
                                <p class="fw-bold mb-0">{{ $persona->ci }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small text-uppercase d-block">Fecha de Nacimiento</label>
                                <p class="fw-bold mb-0">{{ $persona->fechaNacimiento ? \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small text-uppercase d-block">Profesión</label>
                                <p class="fw-bold mb-0">{{ $persona->profesion->provisionN ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small text-uppercase d-block">Fecha de Ingreso</label>
                                <p class="fw-bold mb-0">{{ $persona->fechaIngreso ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small text-uppercase d-block">Antigüedad</label>
                                <p class="fw-bold mb-0">
                                    @if($antiguedad)
                                        {{ $antiguedad['anos'] }} años, {{ $antiguedad['meses'] }} meses, {{ $antiguedad['dias'] }} días
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Laboral -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0"><i class="fas fa-briefcase me-2 text-primary"></i>Información Laboral</h6>
                </div>
                <div class="card-body">
                    @if($historialActual && $historialActual->puesto)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase d-block">Puesto Actual</label>
                                    <p class="fw-bold mb-0">
                                        <i class="fas fa-user-tie text-success me-1"></i>
                                        {{ $historialActual->puesto->denominacion ?? 'N/A' }}
                                        @if($historialActual->puesto->nivelJerarquico ?? false)
                                            <span class="badge bg-info ms-2">{{ $historialActual->puesto->nivelJerarquico }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase d-block">Fecha de Inicio en el Puesto</label>
                                    <p class="fw-bold mb-0">{{ $historialActual->fecha_inicio ? \Carbon\Carbon::parse($historialActual->fecha_inicio)->format('d/m/Y') : 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item">
                                    <label class="text-muted small text-uppercase d-block">Unidad Organizacional</label>
                                    <p class="fw-bold mb-0">
                                        <!-- Para depuración - eliminar después -->
                                        @if(isset($jerarquiaUnidad) && !empty($jerarquiaUnidad))
                                            <div class="alert alert-info">
                                                <br>{{ $historialActual->puesto->unidadOrganizacional->tipo ?? '-' }}: {{ $historialActual->puesto->unidadOrganizacional->denominacion ?? 'Sin unidad' }}
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                No se encontró jerarquía para esta unidad.
                                            </div>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No hay información laboral registrada.</p>
                    @endif
                </div>
            </div>

            <!-- Observaciones -->
            @if($persona->observaciones)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0"><i class="fas fa-comment me-2 text-primary"></i>Observaciones</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">{{ $persona->observaciones }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.info-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}
.info-item:last-child {
    border-bottom: none;
}
.info-item label {
    font-size: 0.7rem;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.card {
    border-radius: 12px;
}
.card-header {
    border-radius: 12px 12px 0 0 !important;
}
.badge.bg-primary {
    font-size: 1.1rem;
    padding: 0.5rem 1rem;
}
.badge.bg-success {
    font-size: 1.1rem;
    padding: 0.5rem 1rem;
}
</style>
@endsection
