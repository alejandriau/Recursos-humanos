@extends('layouts.baseadm')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit text-warning me-2"></i>
                        Editar Profesión
                    </h5>
                    <small class="text-muted">
                        Para: {{ $persona->nombre_completo }}
                    </small>
                </div>
                <div class="card-body">
                    <form action="{{ route('profesion.update', $profesion->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Carrera -->
                            <div class="row mb-4">

                                <!-- 🎓 Carrera -->
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        Carrera / Programa Académico <span class="text-danger">*</span>
                                    </label>

                                    <select name="id_carrera" 
                                            class="form-select @error('id_carrera') is-invalid @enderror" 
                                            required 
                                            id="selectCarrera">

                                        <option value="">-- Seleccione una carrera --</option>

                                        @foreach($carreras as $carrera)
                                            <option value="{{ $carrera->id }}" 
                                                data-area="{{ $carrera->areaConocimiento->nombre }}"
                                                data-nivel="{{ $carrera->nivelAcademico->nombre }}"
                                                {{ old('id_carrera', $profesion->id_carrera) == $carrera->id ? 'selected' : '' }}>
                                                
                                                {{ $carrera->nombre }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('id_carrera')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <!-- Info dinámica -->
                                    <div id="infoCarrera" class="mt-2 small text-muted fst-italic"></div>
                                </div>

                                <!-- 📘 Nivel alcanzado -->
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        Nivel alcanzado <span class="text-danger">*</span>
                                    </label>

                                    <select name="idNivelEstudiado" 
                                            class="form-select @error('idNivelEstudiado') is-invalid @enderror" 
                                            required>

                                        <option value="">-- Seleccione nivel --</option>

                                        @foreach($niveles as $nivel)
                                            <option value="{{ $nivel->id }}"
                                                {{ old('idNivelEstudiado', $profesion->idNivelEstudiado) == $nivel->id ? 'selected' : '' }}>
                                                {{ $nivel->nombre }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('idNivelEstudiado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <small class="text-muted">
                                        Ej: Técnico básico, Bachiller, etc.
                                    </small>
                                </div>

                                <!-- 📊 Estado -->
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        Estado del estudio <span class="text-danger">*</span>
                                    </label>

                                    <select name="estadoEstudio" 
                                            class="form-select @error('estadoEstudio') is-invalid @enderror" 
                                            required>

                                        <option value="en_curso" 
                                            {{ old('estadoEstudio', $profesion->estadoEstudio) == 'en_curso' ? 'selected' : '' }}>
                                            En curso
                                        </option>

                                        <option value="incompleto" 
                                            {{ old('estadoEstudio', $profesion->estadoEstudio) == 'incompleto' ? 'selected' : '' }}>
                                            Incompleto
                                        </option>

                                        <option value="egresado" 
                                            {{ old('estadoEstudio', $profesion->estadoEstudio) == 'egresado' ? 'selected' : '' }}>
                                            Egresado
                                        </option>

                                        <option value="titulado" 
                                            {{ old('estadoEstudio', $profesion->estadoEstudio) == 'titulado' ? 'selected' : '' }}>
                                            Titulado
                                        </option>
                                    </select>

                                    @error('estadoEstudio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <!-- Diploma y Universidad -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Título / Diploma</label>
                                <input type="text" name="diploma" class="form-control @error('diploma') is-invalid @enderror" 
                                    value="{{ old('diploma', $profesion->diploma) }}" placeholder="Ej: Licenciado en Economía">
                                @error('diploma')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Universidad</label>
                                <input type="text" name="universidad" class="form-control @error('universidad') is-invalid @enderror" 
                                    value="{{ old('universidad', $profesion->universidad) }}" placeholder="Ej: Universidad Mayor de San Simón">
                                @error('universidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fechas -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha de Titulación <span class="text-danger">*</span></label>
                                <input type="date" name="fechaTitulo" class="form-control @error('fechaTitulo') is-invalid @enderror" 
                                    value="{{ old('fechaTitulo', $profesion->fechaTitulo ? \Carbon\Carbon::parse($profesion->fechaTitulo)->format('Y-m-d') : '') }}" required>
                                @error('fechaTitulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Esta fecha se usará para calcular la experiencia profesional.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Número de Registro</label>
                                <input type="text" name="registro" class="form-control @error('registro') is-invalid @enderror" 
                                    value="{{ old('registro', $profesion->registro) }}" placeholder="Número de registro universitario">
                                @error('registro')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Profesión Principal -->
                            <div class="col-md-12 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="esPrincipal" class="form-check-input" id="esPrincipal" value="1" 
                                        {{ old('esPrincipal', $profesion->esPrincipal) ? 'checked' : '' }}
                                        {{ $tieneOtraPrincipal && !$profesion->esPrincipal ? 'disabled' : '' }}>
                                    <label class="form-check-label fw-bold" for="esPrincipal">
                                        <i class="fas fa-star text-warning me-1"></i> Marcar como profesión principal
                                    </label>
                                    @if($tieneOtraPrincipal && !$profesion->esPrincipal)
                                        <div class="form-text text-warning">
                                            <i class="fas fa-info-circle"></i> Ya existe otra profesión marcada como principal.
                                        </div>
                                    @else
                                        <div class="form-text">La profesión principal se usa para las validaciones de perfil.</div>
                                    @endif
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Título en Provisión Nacional -->
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-file-certificate text-success me-2"></i>Título en Provisión Nacional
                            </h6>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">N° Provisión Nacional</label>
                                <input type="text" name="provisionN" class="form-control @error('provisionN') is-invalid @enderror" 
                                    value="{{ old('provisionN', $profesion->provisionN) }}" placeholder="Ej: 123456">
                                @error('provisionN')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha de Provisión</label>
                                <input type="date" name="fechaProvision" class="form-control @error('fechaProvision') is-invalid @enderror" 
                                    value="{{ old('fechaProvision', $profesion->fechaProvision ? \Carbon\Carbon::parse($profesion->fechaProvision)->format('Y-m-d') : '') }}">
                                @error('fechaProvision')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Cédula Profesional</label>
                                <input type="text" name="cedulaProfesion" class="form-control @error('cedulaProfesion') is-invalid @enderror" 
                                    value="{{ old('cedulaProfesion', $profesion->cedulaProfesion) }}" placeholder="Número de cédula profesional">
                                @error('cedulaProfesion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-3">

                            <!-- Documentos -->
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-file-pdf text-danger me-2"></i>Documentos (PDF)
                            </h6>

                            @if($profesion->pdfDiploma)
                                <div class="col-md-12 mb-2">
                                    <div class="alert alert-info">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        <a href="{{ Storage::url($profesion->pdfDiploma) }}" target="_blank">Ver Diploma actual</a>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">PDF Diploma (Nuevo)</label>
                                <input type="file" name="pdfDiploma" class="form-control @error('pdfDiploma') is-invalid @enderror" accept=".pdf">
                                @error('pdfDiploma')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Dejar en blanco para mantener el actual.</div>
                            </div>

                            @if($profesion->pdfProvision)
                                <div class="col-md-12 mb-2">
                                    <div class="alert alert-info">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        <a href="{{ Storage::url($profesion->pdfProvision) }}" target="_blank">Ver Provisión actual</a>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">PDF Provisión Nacional (Nuevo)</label>
                                <input type="file" name="pdfProvision" class="form-control @error('pdfProvision') is-invalid @enderror" accept=".pdf">
                                @error('pdfProvision')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($profesion->pdfcedulap)
                                <div class="col-md-12 mb-2">
                                    <div class="alert alert-info">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        <a href="{{ Storage::url($profesion->pdfcedulap) }}" target="_blank">Ver Cédula actual</a>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">PDF Cédula Profesional (Nuevo)</label>
                                <input type="file" name="pdfcedulap" class="form-control @error('pdfcedulap') is-invalid @enderror" accept=".pdf">
                                @error('pdfcedulap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Observaciones</label>
                                <textarea name="observacion" class="form-control @error('observacion') is-invalid @enderror" rows="3" 
                                    placeholder="Observaciones adicionales sobre la formación...">{{ old('observacion', $profesion->observacion) }}</textarea>
                                @error('observacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Actualizar Profesión
                            </button>
                            <a href="{{ route('profesion.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Mostrar información de la carrera seleccionada
        $('select[name="id_carrera"]').change(function() {
            var selected = $(this).find('option:selected');
            var area = selected.data('area');
            var nivel = selected.data('nivel');
            
            if (area && nivel) {
                $('#infoCarrera').html('<i class="fas fa-info-circle"></i> Área: ' + area + ' | Nivel: ' + nivel);
            } else {
                $('#infoCarrera').html('');
            }
        }).trigger('change');
    });
</script>
@endsection