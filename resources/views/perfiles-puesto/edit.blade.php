@extends('layouts.baseadm')

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

                <!-- HEADER -->
                <div class="card-header bg-white">
                    @if(!$tienePerfil)
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nuevo Perfil:</strong> Estás creando el perfil para este puesto.
                        </div>
                    @endif

                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-list me-2 text-primary"></i>
                        {{ $puesto->denominacion }}
                    </h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('perfil-puesto.store-or-update', $puesto->id) }}" method="POST">
                        @csrf

                        <div class="row">

                            <!-- NIVEL -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-layer-group me-1"></i> Nivel Académico *
                                </label>

                                <select name="idNivelAcademico" class="form-select" required>
                                    <option value="">-- Seleccione --</option>
                                    @foreach($nivelesAcademicos as $nivel)
                                        <option value="{{ $nivel->id }}"
                                            {{ old('idNivelAcademico', $perfilActual->idNivelAcademico ?? '') == $nivel->id ? 'selected' : '' }}>
                                            {{ $nivel->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- AREA -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-brain me-1"></i> Área de Conocimiento
                                </label>

                                <select name="idAreaConocimiento" class="form-select">
                                    <option value="">-- Todas las áreas --</option>
                                    @foreach($areasConocimiento as $area)
                                        <option value="{{ $area->id }}"
                                            {{ old('idAreaConocimiento', $perfilActual->idAreaConocimiento ?? '') == $area->id ? 'selected' : '' }}>
                                            {{ $area->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- CARRERA -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-book me-1"></i> Carrera (Opcional)
                                </label>

                                <select name="idCarrera" class="form-select">
                                    <option value="">-- Cualquier carrera --</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->id }}"
                                            data-area="{{ $carrera->areaConocimiento->id }}"
                                            {{ old('idCarrera', $perfilActual->idCarrera ?? '') == $carrera->id ? 'selected' : '' }}>
                                            {{ $carrera->nombre }} ({{ $carrera->areaConocimiento->nombre }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- EXPERIENCIA -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar-alt me-1"></i> Años de Experiencia
                                </label>

                                <input type="number" name="aniosExperienciaMinimos" class="form-control"
                                    value="{{ old('aniosExperienciaMinimos', $perfilActual->aniosExperienciaMinimos ?? 0) }}"
                                    min="0" max="50">
                            </div>

                            <!-- CHECKBOX -->
                            <div class="col-md-4 mb-3 d-flex align-items-center">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox"
                                        name="requiereTituloEnProvisionNacional"
                                        value="1"
                                        {{ old('requiereTituloEnProvisionNacional', $perfilActual->requiereTituloEnProvisionNacional ?? true) ? 'checked' : '' }}>

                                    <label class="form-check-label fw-bold">
                                        Requiere Provisión Nacional
                                    </label>
                                </div>
                            </div>

                            <!-- OBSERVACION -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-comment me-1"></i> Perfil y Conocimientos del Cargo
                                </label>

                                <textarea name="conocimientoTexto" class="form-control" rows="3">
                                    {{ old('conocimientoTexto', $perfilActual->conocimientoTexto ?? '') }}
                                </textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-comment me-1"></i> OBJETIVO/S DEL CARGO 
                                </label>

                                <textarea name="objetivo" class="form-control" rows="3">
                                    {{ old('ojetivo', $perfilActual->objetivo ?? '') }}
                                </textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-comment me-1"></i> Observaciones
                                </label>

                                <textarea name="observacion" class="form-control" rows="3">
                                    {{ old('observacion', $perfilActual->observacion ?? '') }}
                                </textarea>
                            </div>

                        </div>

                        <!-- BOTONES -->
                        <div class="mt-3">
                            <button class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                {{ $tienePerfil ? 'Actualizar Perfil' : 'Guardar Perfil' }}
                            </button>

                            <a href="{{ route('puestos.show', $puesto->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                        </div>

                    </form>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){

    $('select[name="idAreaConocimiento"]').change(function(){
        let area = $(this).val();
        let opciones = $('select[name="idCarrera"] option');

        opciones.show();

        if(area){
            opciones.each(function(){
                let areaCarrera = $(this).data('area');

                if(areaCarrera && areaCarrera != area){
                    $(this).hide();
                }
            });
        }
    }).trigger('change');

});
</script>
@endpush