@extends('dashboard')

@section('contenidouno')
    <title>Editar Designación</title>
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
    </style>
@endsection

@section('contenido')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Designación
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Botón Volver -->
                    <div class="mb-4">
                        <a href="{{ route('historial') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver a la lista
                        </a>
                    </div>

                    <form action="{{ route('historial.update', $historial->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Información del Puesto -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="card-title mb-0">Información del Puesto</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-box">
                                            <h6>{{ $historial->puesto->denominacion }}</h6>
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Item: <strong>{{ $historial->puesto->item }}</strong></small>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Nivel: <strong>{{ $historial->puesto->nivelgerarquico }}</strong></small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dependencia -->
                                        <div class="mt-3">
                                            <label class="form-label">Dependencia:</label>
                                            <div class="alert alert-light">
                                                @php
                                                    $niveles = [];
                                                    if ($historial->puesto->area?->denominacion) $niveles[] = $historial->puesto->area->denominacion;
                                                    if ($historial->puesto->unidad?->denominacion) $niveles[] = $historial->puesto->unidad->denominacion;
                                                    if ($historial->puesto->direccion?->denominacion) $niveles[] = $historial->puesto->direccion->denominacion;
                                                    if ($historial->puesto->secretaria?->denominacion) $niveles[] = $historial->puesto->secretaria->denominacion;
                                                    echo implode(' → ', $niveles);
                                                @endphp
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de la Persona -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="card-title mb-0">Información del Personal</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-box">
                                            <h6>{{ $historial->persona->nombre }} {{ $historial->persona->apellidoPat }} {{ $historial->persona->apellidoMat }}</h6>
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">CI: <strong>{{ $historial->persona->ci }}</strong></small>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Estado:
                                                        <span class="badge bg-{{ $historial->persona->estado ? 'success' : 'secondary' }}">
                                                            {{ $historial->persona->estado ? 'Activo' : 'Inactivo' }}
                                                        </span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Datos de la Designación -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="card-title mb-0">Datos de la Designación</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Tipo de Movimiento -->
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Movimiento *</label>
                                            <select name="tipo_movimiento" class="form-select" required>
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
                                                    <option value="{{ $value }}" {{ $historial->tipo_movimiento == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Tipo de Contrato -->
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Contrato *</label>
                                            <select name="tipo_contrato" class="form-select" required>
                                                @foreach([
                                                    'permanente' => 'Permanente',
                                                    'contrato_administrativo' => 'Contrato Administrativo',
                                                    'contrato_plazo_fijo' => 'Contrato Plazo Fijo',
                                                    'contrato_obra' => 'Contrato por Obra',
                                                    'honorarios' => 'Honorarios'
                                                ] as $value => $label)
                                                    <option value="{{ $value }}" {{ $historial->tipo_contrato == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Estado -->
                                        <div class="mb-3">
                                            <label class="form-label">Estado *</label>
                                            <select name="estado" class="form-select" required>
                                                <option value="activo" {{ $historial->estado == 'activo' ? 'selected' : '' }}>Activo</option>
                                                <option value="concluido" {{ $historial->estado == 'concluido' ? 'selected' : '' }}>Concluido</option>
                                                <option value="suspendido" {{ $historial->estado == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fechas y Documento -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="card-title mb-0">Fechas y Documentación</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Fecha de Inicio *</label>
                                                <input type="date" name="fecha_inicio" class="form-control"
                                                       value="{{ $historial->fecha_inicio->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Fecha de Fin</label>
                                                <input type="date" name="fecha_fin" class="form-control"
                                                       value="{{ $historial->fecha_fin ? $historial->fecha_fin->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Fecha de Vencimiento</label>
                                                <input type="date" name="fecha_vencimiento" class="form-control"
                                                       value="{{ $historial->fecha_vencimiento ? $historial->fecha_vencimiento->format('Y-m-d') : '' }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">% Dedicación</label>
                                                <input type="number" name="porcentaje_dedicacion" class="form-control"
                                                       min="1" max="100" value="{{ $historial->porcentaje_dedicacion }}">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Número de Memo</label>
                                            <input type="text" name="numero_memo" class="form-control"
                                                   value="{{ $historial->numero_memo }}" placeholder="MEMO-2024-001">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Fecha de Memo</label>
                                            <input type="date" name="fecha_memo" class="form-control"
                                                   value="{{ $historial->fecha_memo ? $historial->fecha_memo->format('Y-m-d') : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Información Adicional -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="card-title mb-0">Información Adicional</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Salario</label>
                                            <input type="number" name="salario" class="form-control"
                                                   step="0.01" value="{{ $historial->salario }}">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Jornada Laboral</label>
                                            <select name="jornada_laboral" class="form-select">
                                                <option value="completa" {{ $historial->jornada_laboral == 'completa' ? 'selected' : '' }}>Completa</option>
                                                <option value="media_jornada" {{ $historial->jornada_laboral == 'media_jornada' ? 'selected' : '' }}>Media Jornada</option>
                                                <option value="parcial" {{ $historial->jornada_laboral == 'parcial' ? 'selected' : '' }}>Parcial</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Motivo</label>
                                            <textarea name="motivo" class="form-control" rows="3">{{ $historial->motivo }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Observaciones</label>
                                            <textarea name="observaciones" class="form-control" rows="3">{{ $historial->observaciones }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Archivo y Opciones -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="card-title mb-0">Archivo y Opciones</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($historial->archivo_memo)
                                        <div class="mb-3">
                                            <label class="form-label">Archivo Actual</label>
                                            <div class="alert alert-info">
                                                <i class="fas fa-file-pdf me-2"></i>
                                                <a href="{{ route('historial.descargar.memo', $historial->id) }}" target="_blank">
                                                    Ver archivo actual
                                                </a>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="form-label">Nuevo Archivo del Memo (PDF)</label>
                                            <input type="file" name="archivo_memo" class="form-control" accept=".pdf">
                                            <small class="text-muted">Dejar vacío para mantener el archivo actual</small>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input type="checkbox" name="renovacion_automatica"
                                                   class="form-check-input" id="renovacion_automatica"
                                                   {{ $historial->renovacion_automatica ? 'checked' : '' }}>
                                            <label class="form-check-label" for="renovacion_automatica">
                                                Renovación Automática
                                            </label>
                                        </div>

                                        @if($historial->conserva_puesto_original && $historial->puesto_original_id)
                                        <div class="alert alert-warning">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Esta designación conserva el puesto original:
                                            {{ $historial->puestoOriginal->denominacion ?? 'N/A' }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="reset" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-undo me-2"></i>Restablecer
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Actualizar Designación
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
    // Inicializar Select2
    $('select').select2({
        placeholder: "Seleccione...",
        allowClear: true,
        width: '100%'
    });

    // Validación de fechas
    $('form').on('submit', function(e) {
        const fechaInicio = new Date($('input[name="fecha_inicio"]').val());
        const fechaFin = new Date($('input[name="fecha_fin"]').val());
        const fechaVencimiento = new Date($('input[name="fecha_vencimiento"]').val());

        if (fechaFin && fechaFin < fechaInicio) {
            e.preventDefault();
            alert('La fecha de fin no puede ser anterior a la fecha de inicio');
            return false;
        }

        if (fechaVencimiento && fechaVencimiento <= fechaInicio) {
            e.preventDefault();
            alert('La fecha de vencimiento debe ser posterior a la fecha de inicio');
            return false;
        }
    });
});
</script>
@endsection
