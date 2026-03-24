@extends('dashboard')

@section('title', 'Definir Perfil del Puesto')
@section('header', 'Definición de Perfil para el Puesto: ' . $puesto->denominacion)

@section('contenido')
@php
    $tienePerfil = $perfilActual !== null;
@endphp

<div class="container-fluid fade-in">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    @if(!$tienePerfil)
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nuevo Perfil:</strong> Estás creando el primer perfil para este puesto.
                        </div>
                    @endif
                    
                    <ul class="nav nav-tabs card-header-tabs" id="perfilTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="requisitos-tab" data-bs-toggle="tab" data-bs-target="#requisitos" type="button" role="tab">
                                <i class="fas fa-clipboard-list me-1"></i> Requisitos Generales
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="formacion-tab" data-bs-toggle="tab" data-bs-target="#formacion" type="button" role="tab">
                                <i class="fas fa-graduation-cap me-1"></i> Formación Académica
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="experiencia-tab" data-bs-toggle="tab" data-bs-target="#experiencia" type="button" role="tab">
                                <i class="fas fa-briefcase me-1"></i> Experiencia
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <form action="{{ route('perfil-puesto.store-or-update', $puesto->id) }}" method="POST" id="formPerfilPuesto">
                        @csrf
                        
                        <div class="tab-content">
                            <!-- Tab Requisitos Generales -->
                            <div class="tab-pane fade show active" id="requisitos" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="requiereTituloEnProvisionNacional" id="requiereProvision" value="1" 
                                                {{ old('requiereTituloEnProvisionNacional', ($tienePerfil ? $perfilActual->requiereTituloEnProvisionNacional : true)) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="requiereProvision">
                                                <i class="fas fa-file-certificate me-1"></i> Requiere Título en Provisión Nacional
                                            </label>
                                            <div class="form-text">Marca esta opción si el puesto exige título con provisión nacional.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-comment me-1"></i> Observaciones del Perfil
                                        </label>
                                        <textarea name="observacion" class="form-control" rows="3" placeholder="Observaciones adicionales sobre el perfil del puesto...">{{ old('observacion', ($tienePerfil ? $perfilActual->observacion : '')) }}</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Formación Académica -->
                            <div class="tab-pane fade" id="formacion" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-layer-group me-1"></i> Nivel Académico Requerido
                                        </label>
                                        <select name="nivelAcademicoRequerido" class="form-select">
                                            <option value="">-- Seleccione un nivel --</option>
                                            @foreach($nivelesAcademicos as $nivel)
                                                <option value="{{ $nivel->nombre }}" 
                                                    {{ old('nivelAcademicoRequerido', ($tienePerfil ? $perfilActual->nivelAcademicoRequerido : '')) == $nivel->nombre ? 'selected' : '' }}>
                                                    {{ $nivel->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Nivel académico mínimo requerido para el puesto.</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-brain me-1"></i> Áreas de Conocimiento Permitidas
                                        </label>
                                        <select name="areasConocimientoPermitidas[]" class="form-select" multiple size="5">
                                            @foreach($areasConocimiento as $area)
                                                <option value="{{ $area->id }}" {{ in_array($area->id, $areasSeleccionadas) ? 'selected' : '' }}>
                                                    {{ $area->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Puedes seleccionar múltiples áreas (Ctrl + Click). Déjalo vacío para permitir todas.</div>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-book me-1"></i> Carreras Específicas (Opcional)
                                        </label>
                                        <select name="carrerasEspecificas[]" class="form-select" multiple size="8">
                                            @foreach($carreras as $carrera)
                                                <option value="{{ $carrera->id }}" 
                                                    data-area="{{ $carrera->areaConocimiento->nombre }}"
                                                    data-nivel="{{ $carrera->nivelAcademico->nombre }}"
                                                    {{ in_array($carrera->id, $carrerasSeleccionadas) ? 'selected' : '' }}>
                                                    {{ $carrera->nombre }} ({{ $carrera->areaConocimiento->nombre }} - {{ $carrera->nivelAcademico->nombre }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Si especificas carreras, solo se aceptarán estas. Déjalo vacío para permitir cualquier carrera afín.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Experiencia -->
                            <div class="tab-pane fade" id="experiencia" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-calendar-alt me-1"></i> Años de Experiencia Mínimos
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="aniosExperienciaMinimos" class="form-control" 
                                                value="{{ old('aniosExperienciaMinimos', ($tienePerfil ? $perfilActual->aniosExperienciaMinimos : '')) }}" 
                                                min="0" max="50" step="1">
                                            <span class="input-group-text">años</span>
                                        </div>
                                        <div class="form-text">Experiencia requerida después de la titulación.</div>
                                    </div>
                                    
                                    <div class="col-md-12 mt-3">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Nota:</strong> La experiencia se calcula automáticamente desde la fecha de titulación del candidato hasta la fecha actual.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ $tienePerfil ? 'Actualizar Perfil' : 'Guardar Perfil' }}
                            </button>
                            <a href="{{ route('puestos.show', $puesto->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Filtrar carreras por área seleccionada
        $('#formacion select[name="areasConocimientoPermitidas[]"]').change(function() {
            var areasSeleccionadas = $(this).val();
            var opcionesCarreras = $('#formacion select[name="carrerasEspecificas[]"] option');
            
            if (areasSeleccionadas && areasSeleccionadas.length > 0) {
                opcionesCarreras.each(function() {
                    var areaCarrera = $(this).data('area');
                    if (areasSeleccionadas.includes(areaCarrera) === false) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            } else {
                opcionesCarreras.show();
            }
        }).trigger('change');
    });
</script>
@endpush
@endsection