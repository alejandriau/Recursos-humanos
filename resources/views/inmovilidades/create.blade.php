@extends('dashboard')

@section('title', 'Registrar Nueva Inmovilidad')

@section('contenido')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-plus-circle"></i> Registrar Nueva Inmovilidad Laboral
            </h3>
        </div>
        <div class="card-body">
            <form id="formInmovilidad" action="{{ route('rrhh.inmovilidades.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Búsqueda de persona -->
                <div class="form-group">
                    <label class="font-weight-bold">Buscar Persona *</label>
                    <div class="input-group">
                        <input type="text" id="buscar_persona" class="form-control" placeholder="Ingrese nombre, apellido o documento de identidad">
                        <div class="input-group-append">
                            <button type="button" id="btn_buscar" class="btn btn-primary">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                    <div id="resultados_personas" class="mt-2"></div>
                    <input type="hidden" name="persona_id" id="persona_id" required>
                </div>

                <!-- Datos de la persona seleccionada -->
                <div id="datos_persona" style="display: none;" class="alert alert-info">
                    <i class="fas fa-user-check"></i> <strong>Persona seleccionada:</strong>
                    <span id="nombre_persona"></span>
                    <button type="button" id="cambiar_persona" class="close" aria-label="Cambiar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Tipo de inmovilidad -->
                <div class="form-group">
                    <label class="font-weight-bold">Tipo de Inmovilidad *</label>
                    <select name="tipo_inmovilidad" id="tipo_inmovilidad" class="form-control" required>
                        <option value="">Seleccionar tipo de inmovilidad...</option>
                        <option value="por_discapacidad">Por Discapacidad</option>
                        <option value="por_tutor_discapacitado">Por Tutor de Discapacitado</option>
                        <option value="por_dependiente_discapacitado">Por Dependiente Discapacitado</option>
                        <option value="por_embarazo">Por Embarazo</option>
                        <option value="por_lactancia">Por Lactancia</option>
                        <option value="por_paternidad">Por Paternidad</option>
                    </select>
                </div>

                <!-- Campos comunes -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Inicio *</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Fin *</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Número Resolución RRHH *</label>
                            <input type="text" name="numero_resolucion_rrhh" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha Resolución *</label>
                            <input type="date" name="fecha_resolucion_rrhh" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Norma Legal *</label>
                            <input type="text" name="norma_legal" class="form-control" placeholder="Ej: Ley N° 29973" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Artículo *</label>
                            <input type="text" name="articulo" class="form-control" placeholder="Ej: Art. 45" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Documento Resolución (PDF) *</label>
                    <input type="file" name="resolucion_path" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
                    <small class="form-text text-muted">Formatos permitidos: PDF, JPG, JPEG, PNG (Máx. 5MB)</small>
                </div>

                <div class="form-group">
                    <label>Documento Solicitud (PDF)</label>
                    <input type="file" name="solicitud_path" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">Opcional - Formatos permitidos: PDF, JPG, JPEG, PNG (Máx. 5MB)</small>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="renovable" class="form-check-input" value="1" id="renovable">
                    <label class="form-check-label" for="renovable">Inmovilidad Renovable</label>
                </div>

                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3" placeholder="Observaciones adicionales..."></textarea>
                </div>

                <!-- Campos específicos por tipo -->
                <div id="campos_especificos"></div>

                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Registrar Inmovilidad
                    </button>
                    <a href="{{ route('rrhh.inmovilidades.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let personaSeleccionada = false;

$(document).ready(function() {
    // Búsqueda de personas
    let timeoutId;
    $('#buscar_persona').on('keyup', function() {
        clearTimeout(timeoutId);
        let termino = $(this).val();
        if (termino.length >= 2) {
            timeoutId = setTimeout(() => buscarPersonas(termino), 500);
        } else if (termino.length === 0) {
            $('#resultados_personas').html('');
        }
    });

    $('#btn_buscar').click(function() {
        let termino = $('#buscar_persona').val();
        if (termino.length >= 2) {
            buscarPersonas(termino);
        } else {
            Swal.fire('Error', 'Ingrese al menos 2 caracteres para buscar', 'warning');
        }
    });

    // Cambiar persona
    $('#cambiar_persona').click(function() {
        $('#persona_id').val('');
        $('#datos_persona').hide();
        $('#buscar_persona').val('').focus();
        personaSeleccionada = false;
    });

    // Validación de fechas
    $('#fecha_inicio, #fecha_fin').change(function() {
        let inicio = $('#fecha_inicio').val();
        let fin = $('#fecha_fin').val();
        if (inicio && fin && fin <= inicio) {
            Swal.fire('Error', 'La fecha de fin debe ser posterior a la fecha de inicio', 'error');
            $('#fecha_fin').val('');
        }
    });

    // Cargar campos específicos según tipo
    $('#tipo_inmovilidad').change(function() {
        let tipo = $(this).val();
        if (tipo && personaSeleccionada) {
            cargarCamposEspecificos(tipo);
        } else if (!personaSeleccionada) {
            Swal.fire('Atención', 'Primero debe seleccionar una persona', 'warning');
            $(this).val('');
        }
    });
});

function buscarPersonas(termino) {
    $.ajax({
        url: '{{ route("rrhh.inmovilidades.buscar-persona") }}',
        type: 'GET',
        data: { termino: termino },
        success: function(data) {
            if (data.length > 0) {
                let html = '<div class="list-group">';
                data.forEach(persona => {
                    html += `<a href="#" class="list-group-item list-group-item-action"
                                onclick="seleccionarPersona(${persona.id}, '${persona.nombres} ${persona.apellidos}', '${persona.documento_identidad}')">
                                <strong>${persona.nombres} ${persona.apellidos}</strong><br>
                                <small>DNI: ${persona.documento_identidad}</small>
                            </a>`;
                });
                html += '</div>';
                $('#resultados_personas').html(html);
            } else {
                $('#resultados_personas').html('<div class="alert alert-warning">No se encontraron personas</div>');
            }
        },
        error: function(xhr) {
            console.error(xhr);
            Swal.fire('Error', 'Error al buscar personas', 'error');
        }
    });
}

function seleccionarPersona(id, nombre, documento) {
    $('#persona_id').val(id);
    $('#nombre_persona').text(`${nombre} - DNI: ${documento}`);
    $('#datos_persona').show();
    $('#resultados_personas').html('');
    $('#buscar_persona').val('');
    personaSeleccionada = true;

    // Cargar campos específicos si ya hay tipo seleccionado
    let tipo = $('#tipo_inmovilidad').val();
    if (tipo) {
        cargarCamposEspecificos(tipo);
    }
}

function cargarCamposEspecificos(tipo) {
    let personaId = $('#persona_id').val();
    if (!personaId) {
        Swal.fire('Error', 'Primero seleccione una persona', 'error');
        return;
    }

    $.ajax({
        url: `/rrhh/inmovilidades/campos/${tipo}`,
        type: 'GET',
        data: { persona_id: personaId },
        success: function(data) {
            $('#campos_especificos').html(data);
        },
        error: function(xhr) {
            console.error(xhr);
            Swal.fire('Error', 'Error al cargar campos específicos', 'error');
        }
    });
}
</script>
@endpush
@endsection
