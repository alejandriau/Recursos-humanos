@extends('dashboard')

@section('title', 'Perfil del Puesto')
@section('header', 'Perfil del Puesto: ' . $puesto->denominacion)

@section('contenido')
<div class="container-fluid fade-in">
    <div class="row">
        <div class="col-md-12">

            <div class="card shadow-sm">

                <!-- HEADER -->
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2 text-primary"></i>
                            {{ $puesto->denominacion }}
                        </h5>

                        <!-- INFO DEL PUESTO -->
                        <div class="small text-muted mt-1">
                            <div><strong>Item:</strong> {{ $puesto->item }}</div>
                            <div><strong>Nivel:</strong> {{ $puesto->nivelJerarquico }}</div>
                        </div>
                    </div>

                    <a href="{{ route('perfil-puesto.edit', $puesto->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i> 
                        {{ $perfilActual ? 'Editar Perfil' : 'Definir Perfil' }}
                    </a>
                </div>

                <div class="card-body">

                    <!-- 🔥 UBICACIÓN ORGANIZACIONAL -->
                    <div class="info-card p-3 border rounded mb-3">
                        <h6 class="fw-bold text-primary">
                            <i class="fas fa-sitemap me-2"></i>Ubicación Organizacional
                        </h6>
                        <hr>

                        <p>
                            <strong>Unidad:</strong>
                            <span class="badge bg-dark">
                                {{ $puesto->unidad->denominacion ?? 'No asignada' }}
                            </span>
                        </p>

                        @if($puesto->unidad && $puesto->unidad->padre)
                            <p>
                                <strong>Depende de:</strong>
                                <span class="badge bg-secondary">
                                    {{ $puesto->unidad->padre->denominacion }}
                                </span>
                            </p>
                        @endif
                    </div>

                    @if($perfilActual)

                        <div class="row">

                            <!-- FORMACIÓN -->
                            <div class="col-md-6">
                                <div class="info-card p-3 border rounded mb-3">
                                    <h6 class="fw-bold text-primary">
                                        <i class="fas fa-graduation-cap me-2"></i>Formación Académica
                                    </h6>
                                    <hr>

                                    <p>
                                        <strong>Nivel Académico:</strong> 
                                        <span class="badge bg-info">
                                            {{ $perfilActual->nivelAcademico->nombre ?? 'No especificado' }}
                                        </span>
                                    </p>

                                    <p>
                                        <strong>Área de Conocimiento:</strong><br>
                                        {{ $perfilActual->areaConocimiento->nombre ?? 'No especificado' }}
                                    </p>

                                    <p>
                                        <strong>Carrera / Especialidad:</strong><br>
                                        {{ $perfilActual->carrera->nombre ?? 'No especificada' }}
                                    </p>
                                </div>

                                <!-- CONOCIMIENTOS ADICIONALES Y OBJETIVO -->
                                @if($perfilActual->conocimientoTexto || $perfilActual->objetivo)
                                    <div class="info-card p-3 border rounded mb-3">
                                        <h6 class="fw-bold text-primary">
                                            <i class="fas fa-brain me-2"></i>Conocimientos y Objetivo
                                        </h6>
                                        <hr>

                                        @if($perfilActual->conocimientoTexto)
                                            <p><strong>Conocimientos específicos:</strong></p>
                                            <p class="text-muted">{{ $perfilActual->conocimientoTexto }}</p>
                                        @endif

                                        @if($perfilActual->objetivo)
                                            <p><strong>Objetivo del puesto:</strong></p>
                                            <p class="text-muted">{{ $perfilActual->objetivo }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- EXPERIENCIA + REQUISITOS -->
                            <div class="col-md-6">

                                <!-- EXPERIENCIA -->
                                <div class="info-card p-3 border rounded mb-3">
                                    <h6 class="fw-bold text-primary">
                                        <i class="fas fa-briefcase me-2"></i>Experiencia
                                    </h6>
                                    <hr>

                                    <p>
                                        <strong>Años mínimos:</strong> 
                                        <span class="badge {{ ($perfilActual->aniosExperienciaMinimos ?? 0) > 0 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $perfilActual->aniosExperienciaMinimos ?? 0 }} años
                                        </span>
                                    </p>

                                    <p class="text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Se calcula desde la titulación del candidato.
                                    </p>
                                </div>

                                <!-- REQUISITOS ESPECIALES -->
                                <div class="info-card p-3 border rounded">
                                    <h6 class="fw-bold text-primary">
                                        <i class="fas fa-file-certificate me-2"></i>Requisitos Especiales
                                    </h6>
                                    <hr>

                                    <p>
                                        <strong>Título en Provisión Nacional:</strong>
                                        @if($perfilActual->requiereTituloEnProvisionNacional)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i> Requerido
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times me-1"></i> No requerido
                                            </span>
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
                        <!-- SIN PERFIL -->
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

                <!-- FOOTER -->
                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ route('puestos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>

                    @if($perfilActual)
                        <a href="{{ route('validacion.index') }}?idPuesto={{ $puesto->id }}" class="btn btn-primary">
                            <i class="fas fa-check-circle me-1"></i> Validar Candidatos
                        </a>
                    @endif
                </div>

            </div>

        </div>
    </div>
</div>
@endsection