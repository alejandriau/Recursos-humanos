@extends('layouts.baseadm')

@section('title', 'Catálogo de Carreras')
@section('header', 'Gestión de Carreras')

@section('contenido')
<div class="container-fluid fade-in">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2 text-primary"></i>
                        Lista de Carreras
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearCarrera">
                        <i class="fas fa-plus me-1"></i> Nueva Carrera
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaCarreras">
                            <thead>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Área de Conocimiento</th>
                                    <th>Nivel Académico</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carreras as $carrera)
                                <tr>
                                    <td>{{ $carrera->id }}</td>
                                    <td><strong>{{ $carrera->nombre }}</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $carrera->areaConocimiento->nombre ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $carrera->nivelAcademico->nombre ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($carrera->estado)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" 
                                                    class="btn btn-warning btn-editar" 
                                                    data-id="{{ $carrera->id }}"
                                                    data-nombre="{{ $carrera->nombre }}"
                                                    data-area="{{ $carrera->idAreaConocimiento }}"
                                                    data-nivel="{{ $carrera->idNivelAcademico }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" 
                                                    onclick="eliminarCarrera({{ $carrera->id }})" 
                                                    class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Carrera -->
<div class="modal fade" id="modalCrearCarrera" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Nueva Carrera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearCarrera">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Carrera *</label>
                        <input type="text" name="nombre" class="form-control" required>
                        <small class="text-muted">Máximo 200 caracteres</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Área de Conocimiento *</label>
                        <select name="idAreaConocimiento" class="form-select" required>
                            <option value="">Seleccione...</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nivel Académico *</label>
                        <select name="idNivelAcademico" class="form-select" required>
                            <option value="">Seleccione...</option>
                            @foreach($niveles as $nivel)
                                <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Carrera -->
<div class="modal fade" id="modalEditarCarrera" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Carrera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarCarrera">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Carrera *</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Área de Conocimiento *</label>
                        <select name="idAreaConocimiento" id="edit_idAreaConocimiento" class="form-select" required>
                            <option value="">Seleccione...</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nivel Académico *</label>
                        <select name="idNivelAcademico" id="edit_idNivelAcademico" class="form-select" required>
                            <option value="">Seleccione...</option>
                            @foreach($niveles as $nivel)
                                <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// USANDO EXACTAMENTE LA MISMA ESTRUCTURA QUE FUNCIONA EN ÁREAS
$(document).ready(function() {
    console.log('✅ DOM Listo - Carreras');
    
    // Inicializar DataTable si existe
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#tablaCarreras').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [[0, 'desc']]
        });
    }
    
    // FORMULARIO DE CREACIÓN - MISMA ESTRUCTURA QUE ÁREAS
    $('#formCrearCarrera').on('submit', function(e) {
        e.preventDefault();
        
        console.log('📝 Formulario de carrera enviado');
        
        var nombre = $('input[name="nombre"]', this).val().trim();
        var idAreaConocimiento = $('select[name="idAreaConocimiento"]', this).val();
        var idNivelAcademico = $('select[name="idNivelAcademico"]', this).val();
        
        // Validaciones
        if (!nombre) {
            alert('El nombre de la carrera es requerido');
            return false;
        }
        
        if (!idAreaConocimiento) {
            alert('Debe seleccionar un área de conocimiento');
            return false;
        }
        
        if (!idNivelAcademico) {
            alert('Debe seleccionar un nivel académico');
            return false;
        }
        
        // Datos a enviar - MISMA ESTRUCTURA QUE ÁREAS
        var datos = {
            nombre: nombre,
            idAreaConocimiento: idAreaConocimiento,
            idNivelAcademico: idNivelAcademico,
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        console.log('Enviando datos:', datos);
        
        // Enviar petición - MISMA ESTRUCTURA QUE ÁREAS
        $.ajax({
            url: '/api/carreras',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta:', response);
                if (response.success) {
                    alert('Carrera creada exitosamente');
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Error desconocido'));
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = 'Errores de validación:\n';
                    $.each(errors, function(key, value) {
                        errorMsg += '- ' + value[0] + '\n';
                    });
                    alert(errorMsg);
                } else {
                    alert('Error en el servidor: ' + (xhr.responseJSON?.message || xhr.statusText));
                }
            }
        });
    });
    
    // EDITAR CARRERA - MISMA ESTRUCTURA QUE ÁREAS
    $('.btn-editar').on('click', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        var area = $(this).data('area');
        var nivel = $(this).data('nivel');
        
        console.log('Editar carrera:', {id, nombre, area, nivel});
        
        $('#edit_id').val(id);
        $('#edit_nombre').val(nombre);
        $('#edit_idAreaConocimiento').val(area);
        $('#edit_idNivelAcademico').val(nivel);
        
        $('#modalEditarCarrera').modal('show');
    });
    
    // FORMULARIO DE EDICIÓN - MISMA ESTRUCTURA QUE ÁREAS
    $('#formEditarCarrera').on('submit', function(e) {
        e.preventDefault();
        
        var id = $('#edit_id').val();
        var nombre = $('#edit_nombre').val().trim();
        var idAreaConocimiento = $('#edit_idAreaConocimiento').val();
        var idNivelAcademico = $('#edit_idNivelAcademico').val();
        
        // Validaciones
        if (!nombre) {
            alert('El nombre de la carrera es requerido');
            return false;
        }
        
        if (!idAreaConocimiento) {
            alert('Debe seleccionar un área de conocimiento');
            return false;
        }
        
        if (!idNivelAcademico) {
            alert('Debe seleccionar un nivel académico');
            return false;
        }
        
        // Datos a enviar - MISMA ESTRUCTURA QUE ÁREAS
        var datos = {
            nombre: nombre,
            idAreaConocimiento: idAreaConocimiento,
            idNivelAcademico: idNivelAcademico,
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT'
        };
        
        console.log('Actualizando carrera:', datos);
        
        $.ajax({
            url: '/api/carreras/' + id,
            type: 'POST',
            data: datos,
            success: function(response) {
                console.log('Respuesta:', response);
                if (response.success) {
                    alert('Carrera actualizada correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = 'Errores de validación:\n';
                    $.each(errors, function(key, value) {
                        errorMsg += '- ' + value[0] + '\n';
                    });
                    alert(errorMsg);
                } else {
                    alert('Error al actualizar: ' + (xhr.responseJSON?.message || xhr.statusText));
                }
            }
        });
    });
});

// FUNCIÓN ELIMINAR - MISMA ESTRUCTURA QUE ÁREAS
function eliminarCarrera(id) {
    if (!confirm('¿Estás seguro de eliminar esta carrera?')) return;
    
    $.ajax({
        url: '/api/carreras/' + id,
        type: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                alert('Carrera eliminada correctamente');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            alert('Error al eliminar: ' + (xhr.responseJSON?.message || xhr.statusText));
        }
    });
}
</script>

@endsection