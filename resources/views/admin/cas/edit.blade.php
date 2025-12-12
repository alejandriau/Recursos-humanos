@extends('dashboard')

@section('title', 'Editar CAS')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Editar Certificado de Antigüedad de Servicio
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('cas.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <!-- AÑADE enctype="multipart/form-data" al form -->
                <form action="{{ route('cas.update', $cas->id) }}" method="POST" id="casForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                        @endif

                        <div class="row">
                            <!-- Datos de la Persona -->
                            <div class="col-md-6">
                                <label for="id_persona" class="form-label fw-semibold">Persona *</label>
                                <select id="id_persona" name="id_persona"
                                    class="form-select shadow-sm rounded-3 @error('id_persona') is-invalid @enderror"
                                    required>
                                    <option value="" disabled>-- Seleccione --</option>
                                    @foreach($personas as $p)
                                        @php
                                            $fechaIngreso = $p->fechaIngreso ? date('Y-m-d', strtotime($p->fechaIngreso)) : '';
                                        @endphp
                                        <option value="{{ $p->id }}"
                                            data-fecha-ingreso="{{ $fechaIngreso }}"
                                            @selected(old('id_persona', $cas->id_persona) == $p->id)>
                                            {{ $p->apellidoPat.' '. $p->apellidoMat.' '. $p->nombre }} - CI: {{ $p->ci }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_persona')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha de Ingreso a la Institución -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_ingreso_institucion" class="form-label fw-semibold">Fecha de Ingreso a la Institución *</label>
                                    <input type="date" name="fecha_ingreso_institucion" id="fecha_ingreso_institucion"
                                           class="form-control shadow-sm rounded-3 @error('fecha_ingreso_institucion') is-invalid @enderror"
                                           value="{{ old('fecha_ingreso_institucion', $cas->fecha_ingreso_institucion ? date('Y-m-d', strtotime($cas->fecha_ingreso_institucion)) : '') }}" required>
                                    @error('fecha_ingreso_institucion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tiempo de Servicio -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="anios_servicio" class="form-label fw-semibold">Años de Servicio *</label>
                                    <input type="number" name="anios_servicio" id="anios_servicio"
                                           class="form-control shadow-sm rounded-3 @error('anios_servicio') is-invalid @enderror"
                                           value="{{ old('anios_servicio', $cas->anios_servicio) }}" min="0" max="50" required>
                                    @error('anios_servicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="meses_servicio" class="form-label fw-semibold">Meses *</label>
                                    <input type="number" name="meses_servicio" id="meses_servicio"
                                           class="form-control shadow-sm rounded-3 @error('meses_servicio') is-invalid @enderror"
                                           value="{{ old('meses_servicio', $cas->meses_servicio) }}" min="0" max="11" required>
                                    @error('meses_servicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dias_servicio" class="form-label fw-semibold">Días *</label>
                                    <input type="number" name="dias_servicio" id="dias_servicio"
                                           class="form-control shadow-sm rounded-3 @error('dias_servicio') is-invalid @enderror"
                                           value="{{ old('dias_servicio', $cas->dias_servicio) }}" min="0" max="30" required>
                                    @error('dias_servicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Fechas Importantes -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_emision_cas" class="form-label fw-semibold">Fecha de Emisión CAS *</label>
                                    <input type="date" name="fecha_emision_cas" id="fecha_emision_cas"
                                           class="form-control shadow-sm rounded-3 @error('fecha_emision_cas') is-invalid @enderror"
                                           value="{{ old('fecha_emision_cas', $cas->fecha_emision_cas ? date('Y-m-d', strtotime($cas->fecha_emision_cas)) : '') }}" required>
                                    @error('fecha_emision_cas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_presentacion_rrhh" class="form-label fw-semibold">Fecha de Presentación RRHH *</label>
                                    <input type="date" name="fecha_presentacion_rrhh" id="fecha_presentacion_rrhh"
                                           class="form-control shadow-sm rounded-3 @error('fecha_presentacion_rrhh') is-invalid @enderror"
                                           value="{{ old('fecha_presentacion_rrhh', $cas->fecha_presentacion_rrhh ? date('Y-m-d', strtotime($cas->fecha_presentacion_rrhh)) : '') }}" required>
                                    @error('fecha_presentacion_rrhh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_calculo_antiguedad" class="form-label fw-semibold">Fecha de Cálculo de Antigüedad *</label>
                                    <input type="date" name="fecha_calculo_antiguedad" id="fecha_calculo_antiguedad"
                                           class="form-control shadow-sm rounded-3 @error('fecha_calculo_antiguedad') is-invalid @enderror"
                                           value="{{ old('fecha_calculo_antiguedad', $cas->fecha_calculo_antiguedad ? date('Y-m-d', strtotime($cas->fecha_calculo_antiguedad)) : '') }}" required>
                                    @error('fecha_calculo_antiguedad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información de Calificación -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="periodo_calificacion" class="form-label fw-semibold">Periodo de Calificación</label>
                                    <input type="text" name="periodo_calificacion" id="periodo_calificacion"
                                           class="form-control shadow-sm rounded-3 @error('periodo_calificacion') is-invalid @enderror"
                                           value="{{ old('periodo_calificacion', $cas->periodo_calificacion) }}" placeholder="Ej: Enero - Diciembre 2024" maxlength="100">
                                    @error('periodo_calificacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="meses_calificacion" class="form-label fw-semibold">Meses de Calificación</label>
                                    <input type="text" name="meses_calificacion" id="meses_calificacion"
                                           class="form-control shadow-sm rounded-3 @error('meses_calificacion') is-invalid @enderror"
                                           value="{{ old('meses_calificacion', $cas->meses_calificacion) }}" placeholder="Ej: Hasta diciembre 2024" maxlength="100">
                                    @error('meses_calificacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Archivo y Observaciones -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="archivo_cas" class="form-label fw-semibold">Archivo CAS (PDF)</label>
                                    <!-- CAMBIA input type="text" por type="file" -->
                                    <input type="file" name="archivo_cas" id="archivo_cas"
                                           class="form-control shadow-sm rounded-3 @error('archivo_cas') is-invalid @enderror"
                                           accept=".pdf">
                                    @error('archivo_cas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <!-- Muestra información del archivo actual -->
                                    @if($cas->archivo_cas)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Archivo actual:
                                            <a href="{{ route('cas.ver-archivo', $cas->id) }}" target="_blank" class="text-primary">
                                                <i class="fas fa-file-pdf"></i> Ver archivo actual
                                            </a>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i> Si no seleccionas un nuevo archivo, se mantendrá el actual.
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observaciones" class="form-label fw-semibold">Observaciones</label>
                                    <textarea name="observaciones" id="observaciones"
                                              class="form-control shadow-sm rounded-3 @error('observaciones') is-invalid @enderror"
                                              rows="3" placeholder="Observaciones adicionales...">{{ old('observaciones', $cas->observaciones) }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información de Cálculo (Solo lectura) -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-calculator"></i> Información de Cálculo (Solo lectura)
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">Porcentaje de Bono</label>
                                                <input type="text" class="form-control" value="{{ $cas->porcentaje_bono }}%" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">Monto Calculado</label>
                                                <input type="text" class="form-control" value="Bs. {{ number_format($cas->monto_bono, 2) }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">Nivel de Alerta</label>
                                                <input type="text" class="form-control" value="{{ $cas->nivel_alerta }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">Estado CAS</label>
                                                <input type="text" class="form-control" value="{{ ucfirst($cas->estado_cas) }}" readonly>
                                            </div>
                                        </div>
                                        @if($cas->escalaBono)
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <small class="text-muted">
                                                    Escala aplicada: {{ $cas->escalaBono->nombre }}
                                                    ({{ $cas->escalaBono->anios_minimos }} años mínimo)
                                                </small>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alertas sobre recálculo automático -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-warning" id="recalculoAlert" style="display: none;">
                                    <h5><i class="fas fa-exclamation-triangle"></i> ¡Atención!</h5>
                                    <p class="mb-0">
                                        <strong>Se recalculará automáticamente:</strong><br>
                                        - Porcentaje de bono según escala legal<br>
                                        - Monto basado en el salario mínimo vigente<br>
                                        - Nivel de alerta según fechas
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar CAS
                        </button>
                        <a href="{{ route('cas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectPersona = document.getElementById('id_persona');
    const inputFechaIngreso = document.getElementById('fecha_ingreso_institucion');
    const recalculoAlert = document.getElementById('recalculoAlert');

    // Campos que disparan recálculo
    const camposRecalculo = [
        'anios_servicio',
        'meses_servicio',
        'dias_servicio',
        'fecha_calculo_antiguedad',
        'fecha_ingreso_institucion'
    ];

    // Valores iniciales para comparación
    const valoresIniciales = {};
    camposRecalculo.forEach(campo => {
        const input = document.getElementById(campo);
        if (input) {
            valoresIniciales[campo] = input.value;
        }
    });

    // Función para verificar si hay cambios que requieren recálculo
    function verificarCambiosRecalculo() {
        let hayCambios = false;

        camposRecalculo.forEach(campo => {
            const input = document.getElementById(campo);
            if (input && input.value !== valoresIniciales[campo]) {
                hayCambios = true;
            }
        });

        return hayCambios;
    }

    // Monitorear cambios en los campos
    camposRecalculo.forEach(campo => {
        const input = document.getElementById(campo);
        if (input) {
            input.addEventListener('change', function() {
                if (verificarCambiosRecalculo()) {
                    recalculoAlert.style.display = 'block';
                } else {
                    recalculoAlert.style.display = 'none';
                }
            });
        }
    });

    // Función para actualizar la fecha de ingreso
    function actualizarFechaIngreso(personaId = null) {
        const selectedId = personaId || selectPersona.value;
        const selectedOption = selectPersona.querySelector(`option[value="${selectedId}"]`);

        if (selectedOption) {
            const fechaIngreso = selectedOption.getAttribute('data-fecha-ingreso');

            if (fechaIngreso && selectedId !== '') {
                inputFechaIngreso.value = fechaIngreso;
                // Actualizar el valor inicial para comparación
                valoresIniciales['fecha_ingreso_institucion'] = fechaIngreso;
            } else {
                inputFechaIngreso.value = '';
                valoresIniciales['fecha_ingreso_institucion'] = '';
            }

            // Verificar si hay cambios
            if (verificarCambiosRecalculo()) {
                recalculoAlert.style.display = 'block';
            }
        }
    }

    // Inicialización con TomSelect (si está disponible)
    if (typeof TomSelect !== 'undefined') {
        const tomselect = new TomSelect('#id_persona', {
            plugins: ['dropdown_input'],
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            searchField: ['text'],
            onChange: function(value) {
                if (value && value !== '') {
                    actualizarFechaIngreso(value);
                } else {
                    inputFechaIngreso.value = '';
                    valoresIniciales['fecha_ingreso_institucion'] = '';
                }
            }
        });
    } else {
        // Lógica sin TomSelect
        selectPersona.addEventListener('change', function() {
            actualizarFechaIngreso();
        });
    }

    // Validación de archivo PDF
    const archivoInput = document.getElementById('archivo_cas');
    if (archivoInput) {
        archivoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024; // MB
                const fileType = file.type;

                if (fileType !== 'application/pdf') {
                    alert('Error: Solo se permiten archivos PDF.');
                    e.target.value = '';
                } else if (fileSize > 4) { // 4MB máximo
                    alert('Error: El archivo excede el tamaño máximo de 4MB.');
                    e.target.value = '';
                }
            }
        });
    }

    // Validaciones del formulario
    document.getElementById('casForm').addEventListener('submit', function(e) {
        const fechaEmision = new Date(document.getElementById('fecha_emision_cas').value);
        const fechaPresentacion = new Date(document.getElementById('fecha_presentacion_rrhh').value);
        const fechaCalculo = new Date(document.getElementById('fecha_calculo_antiguedad').value);

        if (fechaPresentacion < fechaEmision) {
            e.preventDefault();
            alert('Error: La fecha de presentación no puede ser anterior a la fecha de emisión.');
            return false;
        }

        if (fechaCalculo > fechaEmision) {
            e.preventDefault();
            alert('Error: La fecha de cálculo no puede ser anterior a la fecha de emisión.');
            return false;
        }

        const anios = parseInt(document.getElementById('anios_servicio').value);
        const meses = parseInt(document.getElementById('meses_servicio').value);
        const dias = parseInt(document.getElementById('dias_servicio').value);

        if (anios < 0 || meses < 0 || dias < 0) {
            e.preventDefault();
            alert('Error: Los valores de antigüedad no pueden ser negativos.');
            return false;
        }

        if (meses > 11) {
            e.preventDefault();
            alert('Error: Los meses no pueden ser mayores a 11.');
            return false;
        }

        if (dias > 30) {
            e.preventDefault();
            alert('Error: Los días no pueden ser mayores a 30.');
            return false;
        }

        // Validar que la persona esté seleccionada
        const personaId = document.getElementById('id_persona').value;
        if (!personaId || personaId === '') {
            e.preventDefault();
            alert('Error: Debe seleccionar una persona.');
            return false;
        }
    });
});
</script>
@endsection
