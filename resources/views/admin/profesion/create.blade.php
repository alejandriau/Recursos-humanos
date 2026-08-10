@extends('layouts.baseadm')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        Registrar Nueva Profesión
                    </h5>
                    <small class="text-muted">
                        Para: {{ $persona->nombre_completo ?? 'Seleccione una persona' }}
                    </small>
                </div>
                <div class="card-body">
                    @if(!isset($persona) || $persona->id == 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Primero debe seleccionar una persona. 
                            <a href="{{ route('personas.index') }}" class="alert-link">Ir a lista de personas</a>
                        </div>
                    @else
                        <form action="{{ route('profesion.store', $persona->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
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
                                                    {{ old('id_carrera') == $carrera->id ? 'selected' : '' }}>
                                                    
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
                                                    {{ old('idNivelEstudiado') == $nivel->id ? 'selected' : '' }}>
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

                                            <option value="">-- Seleccione estado --</option>

                                            <option value="en_curso" {{ old('estadoEstudio') == 'en_curso' ? 'selected' : '' }}>
                                                En curso
                                            </option>
                                            <option value="incompleto" {{ old('estadoEstudio') == 'incompleto' ? 'selected' : '' }}>
                                                Incompleto
                                            </option>
                                            <option value="egresado" {{ old('estadoEstudio') == 'egresado' ? 'selected' : '' }}>
                                                Egresado
                                            </option>
                                            <option value="titulado" {{ old('estadoEstudio') == 'titulado' ? 'selected' : '' }}>
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
                                        value="{{ old('diploma') }}" placeholder="Ej: Licenciado en Economía">
                                    @error('diploma')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Universidad</label>
                                    <input type="text" name="universidad" class="form-control @error('universidad') is-invalid @enderror" 
                                        value="{{ old('universidad') }}" placeholder="Ej: Universidad Mayor de San Simón">
                                    @error('universidad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Fechas -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Fecha de Titulación <span class="text-danger">*</span></label>
                                    <input type="date" name="fechaTitulo" class="form-control @error('fechaTitulo') is-invalid @enderror" 
                                        value="{{ old('fechaTitulo') }}" required>
                                    @error('fechaTitulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Esta fecha se usará para calcular la experiencia profesional.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Número de Registro</label>
                                    <input type="text" name="registro" class="form-control @error('registro') is-invalid @enderror" 
                                        value="{{ old('registro') }}" placeholder="Número de registro universitario">
                                    @error('registro')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Profesión Principal -->
                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="esPrincipal" class="form-check-input" id="esPrincipal" value="1" 
                                            {{ old('esPrincipal') ? 'checked' : '' }}
                                            {{ $tienePrincipal && !old('esPrincipal') ? 'disabled' : '' }}>
                                        <label class="form-check-label fw-bold" for="esPrincipal">
                                            <i class="fas fa-star text-warning me-1"></i> Marcar como profesión principal
                                        </label>
                                        @if($tienePrincipal)
                                            <div class="form-text text-warning">
                                                <i class="fas fa-info-circle"></i> Esta persona ya tiene una profesión principal. Si marca esta opción, la anterior dejará de ser principal.
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
                                        value="{{ old('provisionN') }}" placeholder="Ej: 123456">
                                    @error('provisionN')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Fecha de Provisión</label>
                                    <input type="date" name="fechaProvision" class="form-control @error('fechaProvision') is-invalid @enderror" 
                                        value="{{ old('fechaProvision') }}">
                                    @error('fechaProvision')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Cédula Profesional</label>
                                    <input type="text" name="cedulaProfesion" class="form-control @error('cedulaProfesion') is-invalid @enderror" 
                                        value="{{ old('cedulaProfesion') }}" placeholder="Número de cédula profesional">
                                    @error('cedulaProfesion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-3">

                                <!-- Documentos -->
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>Documentos (PDF)
                                </h6>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">PDF Diploma</label>
                                    <input type="file" name="pdfDiploma" class="form-control @error('pdfDiploma') is-invalid @enderror" accept=".pdf">
                                    @error('pdfDiploma')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Máximo 5MB, formato PDF.</div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">PDF Provisión Nacional</label>
                                    <input type="file" name="pdfProvision" class="form-control @error('pdfProvision') is-invalid @enderror" accept=".pdf">
                                    @error('pdfProvision')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">PDF Cédula Profesional</label>
                                    <input type="file" name="pdfcedulap" class="form-control @error('pdfcedulap') is-invalid @enderror" accept=".pdf">
                                    @error('pdfcedulap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Observaciones</label>
                                    <textarea name="observacion" class="form-control @error('observacion') is-invalid @enderror" rows="3" 
                                        placeholder="Observaciones adicionales sobre la formación...">{{ old('observacion') }}</textarea>
                                    @error('observacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Profesión
                                </button>
                                <a href="{{ route('profesion.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    @endif
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
document.getElementById('selectCarrera').addEventListener('change', function () {
    let selected = this.options[this.selectedIndex];

    let area = selected.getAttribute('data-area');
    let nivel = selected.getAttribute('data-nivel');

    let info = document.getElementById('infoCarrera');

    if (area && nivel) {
        info.innerHTML = `
            <span class="badge bg-light text-dark border">
                Área: ${area}
            </span>
            <span class="badge bg-light text-dark border ms-1">
                Nivel de la carrera: ${nivel}
            </span>
        `;
    } else {
        info.innerHTML = '';
    }
});
</script>
@endsection