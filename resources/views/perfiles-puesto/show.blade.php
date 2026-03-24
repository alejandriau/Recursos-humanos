@extends('dashboard')

@section('title', 'Perfil del Puesto')
@section('header', 'Perfil del Puesto: ' . $puesto->denominacion)

@section('contenido')
<div class="container-fluid fade-in">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-list me-2 text-primary"></i>
                        Requisitos del Puesto
                        <div>{{ $puesto->item }} {{ $puesto->denominacion }}{{ $puesto->nivelJerarquico }}</div>
                    </h5>
                    <a href="{{ route('perfil-puesto.edit', $puesto->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i> {{ $tienePerfil ? 'Editar Perfil' : 'Definir Perfil' }}
                    </a>
                </div>
                <div class="card-body">
                    @if($tienePerfil && $perfilActual)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card p-3 border rounded mb-3">
                                    <h6 class="fw-bold text-primary">
                                        <i class="fas fa-graduation-cap me-2"></i>Formación Académica
                                    </h6>
                                    <hr>
                                    <p><strong>Nivel Académico Requerido:</strong> 
                                        <span class="badge bg-info">{{ $perfilActual->nivelAcademicoRequerido ?? 'No especificado' }}</span>
                                    </p>
                                    <p><strong>Áreas de Conocimiento:</strong></p>
                                    @if($areasPermitidas->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($areasPermitidas as $area)
                                                <span class="badge bg-secondary">{{ $area->nombre }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">Todas las áreas permitidas</span>
                                    @endif
                                    
                                    @if($carrerasEspecificas->count() > 0)
                                        <p class="mt-3"><strong>Carreras Específicas:</strong></p>
                                        <div class="list-group">
                                            @foreach($carrerasEspecificas as $carrera)
                                                <div class="list-group-item list-group-item-action">
                                                    <div class="d-flex justify-content-between">
                                                        <span>{{ $carrera->nombre }}</span>
                                                        <span class="badge bg-info">{{ $carrera->areaConocimiento->nombre ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-card p-3 border rounded mb-3">
                                    <h6 class="fw-bold text-primary">
                                        <i class="fas fa-briefcase me-2"></i>Experiencia
                                    </h6>
                                    <hr>
                                    <p>
                                        <strong>Años de Experiencia Mínimos:</strong> 
                                        <span class="badge {{ ($perfilActual->aniosExperienciaMinimos ?? 0) > 0 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $perfilActual->aniosExperienciaMinimos ?? 0 }} años
                                        </span>
                                    </p>
                                    <p class="text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        La experiencia se calcula desde la fecha de titulación del candidato.
                                    </p>
                                </div>
                                
                                <div class="info-card p-3 border rounded">
                                    <h6 class="fw-bold text-primary">
                                        <i class="fas fa-file-certificate me-2"></i>Requisitos Especiales
                                    </h6>
                                    <hr>
                                    <p>
                                        <strong>Título en Provisión Nacional:</strong>
                                        @if($perfilActual->requiereTituloEnProvisionNacional)
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i> Requerido</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-times me-1"></i> No requerido</span>
                                        @endif
                                    </p>
                                    
                                    @if($perfilActual->observacion)
                                        <p><strong>Observaciones:</strong></p>
                                        <p class="text-muted">{{ $perfilActual->observacion }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                            <h5 class="text-muted">Este puesto no tiene perfil definido</h5>
                            <p>Defina los requisitos del puesto para poder validar candidatos.</p>
                            <a href="{{ route('perfil-puesto.edit', $puesto->id) }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i> Definir Perfil
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('puestos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver a Puestos
                        </a>
                        @if($tienePerfil)
                            <a href="{{ route('validacion.index') }}?idPuesto={{ $puesto->id }}" class="btn btn-primary">
                                <i class="fas fa-check-circle me-1"></i> Validar Candidatos
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .info-card {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    .info-card:hover {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transform: translateY(-2px);
    }
    .gap-1 {
        gap: 0.25rem;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.35rem 0.65rem;
    }
    .list-group-item {
        padding: 0.5rem 1rem;
    }
</style>
@endpush
@endsection