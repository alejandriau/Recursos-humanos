@extends('dashboard')

@section('title', 'Registrar Nuevo CAS')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-contract"></i> Registrar Nuevo Certificado de Antigüedad de Servicio
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('cas.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <form action="{{ route('cas.store') }}" method="POST" id="casForm">
                    @csrf
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
                $selected = false;

                // Lógica de selección mejorada
                if ($personaSeleccionada && $personaSeleccionada->id == $p->id) {
                    $selected = true;
                } elseif (!$personaSeleccionada && $personas->count() === 1) {
                    $selected = true;
                } else {
                    $selected = old('id_persona') == $p->id;
                }
            @endphp
            <option value="{{ $p->id }}"
                data-fecha-ingreso="{{ $fechaIngreso }}"
                @selected($selected)>
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
                                           value="{{ old('fecha_ingreso_institucion') }}" required>
                                    @error('fecha_ingreso_institucion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Resto del formulario igual... -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="anios_servicio" class="form-label fw-semibold">Años de Servicio *</label>
                                    <input type="number" name="anios_servicio" id="anios_servicio"
                                           class="form-control shadow-sm rounded-3 @error('anios_servicio') is-invalid @enderror"
                                           value="{{ old('anios_servicio', 0) }}" min="0" max="50" required>
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
                                           value="{{ old('meses_servicio', 0) }}" min="0" max="11" required>
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
                                           value="{{ old('dias_servicio', 0) }}" min="0" max="30" required>
                                    @error('dias_servicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_emision_cas" class="form-label fw-semibold">Fecha de Emisión CAS *</label>
                                    <input type="date" name="fecha_emision_cas" id="fecha_emision_cas"
                                           class="form-control shadow-sm rounded-3 @error('fecha_emision_cas') is-invalid @enderror"
                                           value="{{ old('fecha_emision_cas') }}" required>
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
                                           value="{{ old('fecha_presentacion_rrhh') }}" required>
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
                                           value="{{ old('fecha_calculo_antiguedad') }}" required>
                                    @error('fecha_calculo_antiguedad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="periodo_calificacion" class="form-label fw-semibold">Periodo de Calificación</label>
                                    <input type="text" name="periodo_calificacion" id="periodo_calificacion"
                                           class="form-control shadow-sm rounded-3 @error('periodo_calificacion') is-invalid @enderror"
                                           value="{{ old('periodo_calificacion') }}" placeholder="Ej: Enero - Diciembre 2024" maxlength="100">
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
                                           value="{{ old('meses_calificacion') }}" placeholder="Ej: Hasta diciembre 2024" maxlength="100">
                                    @error('meses_calificacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="archivo_cas" class="form-label fw-semibold">Archivo CAS (PDF)</label>
                                    <input type="text" name="archivo_cas" id="archivo_cas"
                                           class="form-control shadow-sm rounded-3 @error('archivo_cas') is-invalid @enderror"
                                           value="{{ old('archivo_cas') }}" placeholder="Nombre del archivo PDF" maxlength="250">
                                    @error('archivo_cas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observaciones" class="form-label fw-semibold">Observaciones</label>
                                    <textarea name="observaciones" id="observaciones"
                                              class="form-control shadow-sm rounded-3 @error('observaciones') is-invalid @enderror"
                                              rows="3" placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-info-circle"></i> Información de Cálculo Automático</h5>
                                    <p class="mb-0">
                                        Al guardar el CAS, el sistema calculará automáticamente: <br>- Porcentaje de bono según escala legal (mínimo 2 años de servicio) <br>- Monto basado en el salario mínimo vigente <br>- Nivel de alerta según fechas
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar CAS
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

    console.log('Script CAS cargado'); // Para debug

    // Función para actualizar la fecha de ingreso
    function actualizarFechaIngreso() {
        if (!selectPersona || !inputFechaIngreso) {
            console.error('Elementos no encontrados');
            return;
        }

        const selectedOption = selectPersona.options[selectPersona.selectedIndex];

        if (selectedOption) {
            const fechaIngreso = selectedOption.getAttribute('data-fecha-ingreso');
            console.log('Fecha obtenida:', fechaIngreso, 'para persona:', selectPersona.value);

            if (fechaIngreso && selectPersona.value !== '') {
                inputFechaIngreso.value = fechaIngreso;
            } else {
                inputFechaIngreso.value = '';
            }
        }
    }

    // Inicializar al cargar
    if (selectPersona && selectPersona.value && selectPersona.value !== '') {
        console.log('Inicializando fecha para persona seleccionada:', selectPersona.value);
        actualizarFechaIngreso();
    }

    // Evento cuando cambia la selección
    if (selectPersona) {
        selectPersona.addEventListener('change', actualizarFechaIngreso);
    }

    // Validaciones del formulario
    const form = document.getElementById('casForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validar fecha de ingreso
            if (!inputFechaIngreso.value) {
                e.preventDefault();
                alert('Error: La fecha de ingreso a la institución es obligatoria.');
                inputFechaIngreso.focus();
                return false;
            }

            // Resto de validaciones...
            return true;
        });
    }
});
</script>
@endsection
