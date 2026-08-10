@extends('layouts.baseadm')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            @if($profesion)
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap text-primary me-2"></i>
                        Detalle de la Profesión
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Badge de Profesión Principal -->
                            @if($profesion->esPrincipal)
                                <div class="alert alert-primary">
                                    <i class="fas fa-star me-2"></i>
                                    <strong>Profesión Principal</strong> - Esta es la profesión que se usa para las validaciones de perfil.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Persona</label>
                                <p class="fw-bold mb-0">
                                    @if($profesion->persona)
                                        {{ $profesion->persona->nombre_completo ?? ($profesion->persona->nombre . ' ' . $profesion->persona->apellidoPat . ' ' . $profesion->persona->apellidoMat) }}
                                    @else
                                        <span class="text-danger">Persona no encontrada o eliminada</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Carrera</label>
                                <p class="fw-bold mb-0">
                                    @if($profesion->carrera)
                                        {{ $profesion->carrera->nombre }}
                                    @else
                                        <span class="text-warning">Carrera no especificada</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Área de Conocimiento</label>
                                <p>
                                    @if($profesion->carrera && $profesion->carrera->areaConocimiento)
                                        <span class="badge bg-secondary">{{ $profesion->carrera->areaConocimiento->nombre }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Nivel Académico</label>
                                <p>
                                    @if($profesion->carrera && $profesion->carrera->nivelAcademico)
                                        <span class="badge bg-info">{{ $profesion->carrera->nivelAcademico->nombre }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Título / Diploma</label>
                                <p>{{ $profesion->diploma ?: '—' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Universidad</label>
                                <p>{{ $profesion->universidad ?: '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Fecha de Titulación</label>
                                <p>{{ $profesion->fechaTitulo ? \Carbon\Carbon::parse($profesion->fechaTitulo)->format('d/m/Y') : '—' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Experiencia desde Titulación</label>
                                <p>
                                    @if($profesion->fechaTitulo)
                                        <span class="badge {{ $profesion->aniosExperienciaDesdeTitulacion >= 3 ? 'bg-success' : 'bg-warning' }}">
                                            {{ number_format($profesion->aniosExperienciaDesdeTitulacion, 1) }} años
                                            ({{ $profesion->mesesExperienciaDesdeTitulacion }} meses)
                                        </span>
                                    @else
                                        <span class="text-muted">No hay fecha de titulación</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">N° Registro</label>
                                <p>{{ $profesion->registro ?: '—' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Cédula Profesional</label>
                                <p>{{ $profesion->cedulaProfesion ?: '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">N° Provisión Nacional</label>
                                <p>{{ $profesion->provisionN ?: '—' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Fecha de Provisión</label>
                                <p>{{ $profesion->fechaProvision ? \Carbon\Carbon::parse($profesion->fechaProvision)->format('d/m/Y') : '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Documentos</label>
                                <div class="mt-2">
                                    @if($profesion->pdfDiploma)
                                        <a href="{{ Storage::url($profesion->pdfDiploma) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fas fa-file-pdf"></i> Ver Diploma
                                        </a>
                                    @endif
                                    @if($profesion->pdfProvision)
                                        <a href="{{ Storage::url($profesion->pdfProvision) }}" target="_blank" class="btn btn-sm btn-outline-success me-2">
                                            <i class="fas fa-file-pdf"></i> Ver Provisión
                                        </a>
                                    @endif
                                    @if($profesion->pdfcedulap)
                                        <a href="{{ Storage::url($profesion->pdfcedulap) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-file-pdf"></i> Ver Cédula
                                        </a>
                                    @endif
                                    @if(!$profesion->pdfDiploma && !$profesion->pdfProvision && !$profesion->pdfcedulap)
                                        <span class="text-muted">No hay documentos adjuntos</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($profesion->observacion)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="info-group mb-3">
                                    <label class="text-muted small">Observaciones</label>
                                    <p class="text-muted">{{ $profesion->observacion }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('profesion.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                        <div>
                            @if(!empty($profesion) && $profesion->id)
                                <a href="{{ route('profesion.edit', $profesion->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Editar
                                </a>
                            @else
                                <a href="{{ route('profesion.create',$profesion->persona->id) }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Registrar Profesión
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @else
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    No hay profesión registrada para esta persona.
                </div>

                <div class="text-center">
                    <a href="{{ route('profesion.create',$profesion->persona->id) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Registrar Profesión
                    </a>
                </div>
            @endif


        </div>
    </div>
</div>
@endsection