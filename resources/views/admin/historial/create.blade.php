@extends('dashboard')

@section('contenidouno')
    <title>Registrar Designación</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            z-index: 9999 !important;
        }
        .card-header {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }
        .info-box {
            border-left: 4px solid #007bff;
            padding-left: 15px;
            margin-bottom: 20px;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
            display: none;
        }
        .is-invalid {
            border-color: #dc3545;
        }
        .alert-validation {
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
@endsection

@section('contenido')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>Registrar Nueva Designación
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Botón Volver -->
                    <div class="mb-4">
                        <a href="{{ route('historial') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver a la lista
                        </a>
                    </div>
                    {{-- Después de las alertas generales --}}
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error:</strong> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Añade también debug si estás en desarrollo --}}
@if(config('app.debug') && session()->has('errors'))
<div class="alert alert-warning mt-3">
    <h5>Debug Info:</h5>
    <pre>{{ print_r($errors->all(), true) }}</pre>
</div>
@endif

                    <!-- Alertas de error generales -->
                    @if ($errors->any())
                    <div class="alert alert-danger alert-validation">
                        <strong>Por favor, corrige los siguientes errores:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('historial.store') }}" method="POST" enctype="multipart/form-data" id="designacionForm">
                        @csrf

                        <div class="row">
                            <!-- Información del Puesto -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="card-title mb-0">Información del Puesto</h6>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" name="puesto_id" value="{{ $puesto->id }}">

                                        <div class="info-box">
                                            <h6>{{ $puesto->denominacion }}</h6>
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Item: <strong>{{ $puesto->item }}</strong></small>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Nivel: <strong>{{ $puesto->nivelgerarquico }}</strong></small>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-muted">Haber básico:
                                                    <strong>${{ number_format($puesto->haber, 2) }}</strong>
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Dependencia -->
<!-- Dependencia -->
<div class="mt-3">
    <label class="form-label">Dependencia:</label>
    <div class="alert alert-light">
        @php
            $unidad = $puesto->unidadOrganizacional;
            $jerarquia = [];

            // Obtener la jerarquía completa
            while ($unidad) {
                $jerarquia[] = $unidad->denominacion . ' (' . $unidad->tipo . ')';
                $unidad = $unidad->padre;
            }

            // Mostrar en orden inverso (de mayor a menor jerarquía)
            echo implode(' → ', array_reverse($jerarquia));
        @endphp
    </div>
</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de la Designación -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="card-title mb-0">Datos de la Designación</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Persona -->
                                        <div class="mb-3">
                                            <label for="persona_id" class="form-label">Persona *</label>
                                            <select class="form-select @error('persona_id') is-invalid @enderror"
                                                    id="persona_id" name="persona_id" required>
                                                <option value="">Seleccionar Persona</option>
                                                @foreach($personas as $persona)
                                                    <option value="{{ $persona->id }}" {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                                                        {{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('persona_id')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            <div class="error-message" id="persona_id_error"></div>
                                        </div>

                                        <!-- Tipo de Movimiento -->
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Movimiento *</label>
                                            <select name="tipo_movimiento" class="form-select @error('tipo_movimiento') is-invalid @enderror" required>
                                                <option value="">-- Seleccione --</option>
                                                @foreach([
                                                    'designacion_inicial' => 'Designación Inicial',
                                                    'movilidad' => 'Movilidad',
                                                    'ascenso' => 'Ascenso',
                                                    'reasignacion' => 'Reasignación',
                                                    'comision' => 'Comisión',
                                                    'interinato' => 'Interinato',
                                                    'encargo_funciones' => 'Encargo de Funciones',
                                                    'recontratacion' => 'Recontratación'
                                                ] as $value => $label)
                                                    <option value="{{ $value }}" {{ old('tipo_movimiento') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('tipo_movimiento')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            <div class="error-message" id="tipo_movimiento_error"></div>
                                        </div>

                                        <!-- Tipo de Contrato -->
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Contrato *</label>
                                            <select name="tipo_contrato" class="form-select @error('tipo_contrato') is-invalid @enderror" required>
                                                <option value="">-- Seleccione --</option>
                                                @foreach([
                                                    'permanente' => 'Permanente',
                                                    'contrato_administrativo' => 'Contrato Administrativo',
                                                    'contrato_plazo_fijo' => 'Contrato Plazo Fijo',
                                                    'contrato_obra' => 'Contrato por Obra',
                                                    'honorarios' => 'Honorarios'
                                                ] as $value => $label)
                                                    <option value="{{ $value }}" {{ old('tipo_contrato') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('tipo_contrato')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            <div class="error-message" id="tipo_contrato_error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Fechas y Documento -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="card-title mb-0">Fechas y Documentación</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Fecha de Inicio *</label>
                                                <input type="date" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror"
                                                       value="{{ old('fecha_inicio') }}" required>
                                                @error('fecha_inicio')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                                <div class="error-message" id="fecha_inicio_error"></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Fecha de Fin</label>
                                                <input type="date" name="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror"
                                                       value="{{ old('fecha_fin') }}">
                                                @error('fecha_fin')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                                <div class="error-message" id="fecha_fin_error"></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Fecha de Vencimiento</label>
                                                <input type="date" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                                                       value="{{ old('fecha_vencimiento') }}">
                                                @error('fecha_vencimiento')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                                <div class="error-message" id="fecha_vencimiento_error"></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">% Dedicación</label>
                                                <input type="number" name="porcentaje_dedicacion" class="form-control @error('porcentaje_dedicacion') is-invalid @enderror"
                                                       min="1" max="100" value="{{ old('porcentaje_dedicacion', 100) }}">
                                                @error('porcentaje_dedicacion')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                                <div class="error-message" id="porcentaje_dedicacion_error"></div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Número de Memo</label>
                                            <input type="text" name="numero_memo" class="form-control @error('numero_memo') is-invalid @enderror"
                                                   placeholder="MEMO-2024-001" value="{{ old('numero_memo') }}">
                                            @error('numero_memo')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            <div class="error-message" id="numero_memo_error"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Fecha de Memo</label>
                                            <input type="date" name="fecha_memo" class="form-control @error('fecha_memo') is-invalid @enderror"
                                                   value="{{ old('fecha_memo') }}">
                                            @error('fecha_memo')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            <div class="error-message" id="fecha_memo_error"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Archivo del Memo (PDF)</label>
                                            <input type="file" name="archivo_memo" class="form-control @error('archivo_memo') is-invalid @enderror"
                                                   accept=".pdf">
                                            @error('archivo_memo')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            <div class="error-message" id="archivo_memo_error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información Adicional -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="card-title mb-0">Información Adicional</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Salario</label>
                                            <input type="number" name="salario" class="form-control @error('salario') is-invalid @enderror"
                                                   step="0.01" value="{{ old('salario', $puesto->haber) }}">
                                            @error('salario')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            <div class="error-message" id="salario_error"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Jornada Laboral</label>
                                            <select name="jornada_laboral" class="form-select @error('jornada_laboral') is-invalid @enderror">
                                                <option value="completa" {{ old('jornada_laboral') == 'completa' ? 'selected' : '' }}>Completa</option>
                                                <option value="media_jornada" {{ old('jornada_laboral') == 'media_jornada' ? 'selected' : '' }}>Media Jornada</option>
                                                <option value="parcial" {{ old('jornada_laboral') == 'parcial' ? 'selected' : '' }}>Parcial</option>
                                            </select>
                                            @error('jornada_laboral')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            <div class="error-message" id="jornada_laboral_error"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Motivo</label>
                                            <textarea name="motivo" class="form-control @error('motivo') is-invalid @enderror" rows="3"
                                                      placeholder="Motivo de la designación...">{{ old('motivo') }}</textarea>
                                            @error('motivo')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            <div class="error-message" id="motivo_error"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Observaciones</label>
                                            <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="3"
                                                      placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                                            @error('observaciones')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            <div class="error-message" id="observaciones_error"></div>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input type="checkbox" name="renovacion_automatica"
                                                   class="form-check-input @error('renovacion_automatica') is-invalid @enderror"
                                                   id="renovacion_automatica" {{ old('renovacion_automatica') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="renovacion_automatica">
                                                Renovación Automática
                                            </label>
                                            @error('renovacion_automatica')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            <div class="error-message" id="renovacion_automatica_error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="reset" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-undo me-2"></i>Limpiar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Registrar Designación
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializar Select2 para búsqueda de personas
    $('#persona_id').select2({
        placeholder: "Buscar persona...",
        allowClear: true,
        width: '100%'
    });

    // Validación en tiempo real
    $('#designacionForm').on('input change', 'input, select, textarea', function() {
        validateField($(this));
    });

    // Validación antes de enviar el formulario
    $('#designacionForm').on('submit', function(e) {
        let isValid = true;

        // Validar todos los campos requeridos
        $('#designacionForm').find('input, select, textarea').each(function() {
            if (!validateField($(this))) {
                isValid = false;
            }
        });

        // Validación adicional de fechas
        const fechaInicio = new Date($('input[name="fecha_inicio"]').val());
        const fechaFin = new Date($('input[name="fecha_fin"]').val());
        const fechaVencimiento = new Date($('input[name="fecha_vencimiento"]').val());

        if ($('input[name="fecha_fin"]').val() && fechaFin < fechaInicio) {
            showError('fecha_fin_error', 'La fecha de fin no puede ser anterior a la fecha de inicio');
            isValid = false;
        }

        if ($('input[name="fecha_vencimiento"]').val() && fechaVencimiento <= fechaInicio) {
            showError('fecha_vencimiento_error', 'La fecha de vencimiento debe ser posterior a la fecha de inicio');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            // Mostrar mensaje general de error
            alert('Por favor, corrige los errores en el formulario antes de enviar.');
        }
    });

    function validateField(field) {
        const fieldName = field.attr('name');
        const errorElement = $(`#${fieldName}_error`);

        // Limpiar error previo
        errorElement.hide().text('');
        field.removeClass('is-invalid');

        // Validar campo requerido
        if (field.prop('required') && !field.val()) {
            showError(errorElement.attr('id'), 'Este campo es obligatorio');
            return false;
        }

        // Validaciones específicas por tipo de campo
        if (fieldName === 'porcentaje_dedicacion' && field.val()) {
            const value = parseInt(field.val());
            if (value < 1 || value > 100) {
                showError(errorElement.attr('id'), 'El porcentaje debe estar entre 1 y 100');
                return false;
            }
        }

        if (fieldName === 'salario' && field.val()) {
            const value = parseFloat(field.val());
            if (value < 0) {
                showError(errorElement.attr('id'), 'El salario no puede ser negativo');
                return false;
            }
        }

        if (fieldName === 'archivo_memo' && field.val()) {
            const file = field[0].files[0];
            if (file) {
                if (file.type !== 'application/pdf') {
                    showError(errorElement.attr('id'), 'El archivo debe ser un PDF');
                    return false;
                }
                if (file.size > 2048 * 1024) { // 2MB en bytes
                    showError(errorElement.attr('id'), 'El archivo no puede ser mayor a 2MB');
                    return false;
                }
            }
        }

        return true;
    }

    function showError(elementId, message) {
        $(`#${elementId}`).text(message).show();
        $(`[name="${elementId.replace('_error', '')}"]`).addClass('is-invalid');
    }
});
</script>
@endsection
