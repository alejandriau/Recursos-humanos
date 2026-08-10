@extends('layouts.baseadm')

@section('title', 'Catálogo de Niveles Académicos')
@section('header', 'Gestión de Niveles Académicos')

@section('contenido')
<div class="container-fluid fade-in">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2 text-primary"></i>
                        Lista de Niveles Académicos
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearNivel">
                        <i class="fas fa-plus me-1"></i> Nuevo Nivel
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaNiveles">
                            <thead>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Orden</th>
                                    <th>Título Universitario</th>
                                    <th>Estado</th>
                                    <th>Carreras Asociadas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($niveles as $nivel)
                                <tr>
                                    <td>{{ $nivel->id }}</td>
                                    <td><strong>{{ $nivel->nombre }}</strong></td>
                                    <td>{{ $nivel->orden }}</td>
                                    <td>
                                        @if($nivel->esTituloUniversitario)
                                            <span class="badge bg-success">Sí</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($nivel->estado)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $nivel->carreras_count ?? 0 }} carreras</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" 
                                                    class="btn btn-warning btn-editar" 
                                                    data-id="{{ $nivel->id }}"
                                                    data-nombre="{{ $nivel->nombre }}"
                                                    data-orden="{{ $nivel->orden }}"
                                                    data-estitulo="{{ $nivel->esTituloUniversitario }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" 
                                                    onclick="eliminarNivel({{ $nivel->id }})" 
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

<!-- Modal Crear Nivel -->
<div class="modal fade" id="modalCrearNivel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Nuevo Nivel Académico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearNivel">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Nivel *</label>
                        <input type="text" name="nombre" class="form-control" required>
                        <small class="text-muted">Máximo 100 caracteres</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Orden *</label>
                        <input type="number" name="orden" class="form-control" required min="0">
                        <div class="form-text">Número menor = nivel más bajo</div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="esTituloUniversitario" class="form-check-input" id="esTitulo" value="1">
                        <label class="form-check-label" for="esTitulo">
                            Es Título Universitario
                        </label>
                        <div class="form-text">Los títulos universitarios cuentan para experiencia profesional.</div>
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

<!-- Modal Editar Nivel -->
<div class="modal fade" id="modalEditarNivel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Nivel Académico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarNivel">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Nivel *</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Orden *</label>
                        <input type="number" name="orden" id="edit_orden" class="form-control" required min="0">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="esTituloUniversitario" class="form-check-input" id="edit_esTitulo" value="1">
                        <label class="form-check-label" for="edit_esTitulo">
                            Es Título Universitario
                        </label>
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
    console.log('✅ DOM Listo - Niveles Académicos');
    
    // Inicializar DataTable si existe
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#tablaNiveles').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [[2, 'asc']] // Ordenar por la columna "Orden"
        });
    }
    
    // FORMULARIO DE CREACIÓN - MISMA ESTRUCTURA QUE ÁREAS
    $('#formCrearNivel').on('submit', function(e) {
        e.preventDefault();
        
        console.log('📝 Formulario de nivel enviado');
        
        var nombre = $('input[name="nombre"]', this).val().trim();
        var orden = $('input[name="orden"]', this).val();
        var esTituloUniversitario = $('input[name="esTituloUniversitario"]', this).is(':checked') ? 1 : 0;
        
        // Validaciones
        if (!nombre) {
            alert('El nombre del nivel es requerido');
            return false;
        }
        
        if (orden === '') {
            alert('El orden es requerido');
            return false;
        }
        
        // Datos a enviar - MISMA ESTRUCTURA QUE ÁREAS
        var datos = {
            nombre: nombre,
            orden: orden,
            esTituloUniversitario: esTituloUniversitario,
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        console.log('Enviando datos:', datos);
        
        // Enviar petición - MISMA ESTRUCTURA QUE ÁREAS
        $.ajax({
            url: '/api/niveles',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta:', response);
                if (response.success) {
                    alert('Nivel académico creado exitosamente');
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
    
    // EDITAR NIVEL - MISMA ESTRUCTURA QUE ÁREAS
    $('.btn-editar').on('click', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        var orden = $(this).data('orden');
        var esTitulo = $(this).data('estitulo');
        
        console.log('Editar nivel:', {id, nombre, orden, esTitulo});
        
        $('#edit_id').val(id);
        $('#edit_nombre').val(nombre);
        $('#edit_orden').val(orden);
        $('#edit_esTitulo').prop('checked', esTitulo == 1);
        
        $('#modalEditarNivel').modal('show');
    });
    
    // FORMULARIO DE EDICIÓN - MISMA ESTRUCTURA QUE ÁREAS
    $('#formEditarNivel').on('submit', function(e) {
        e.preventDefault();
        
        var id = $('#edit_id').val();
        var nombre = $('#edit_nombre').val().trim();
        var orden = $('#edit_orden').val();
        var esTituloUniversitario = $('#edit_esTitulo').is(':checked') ? 1 : 0;
        
        // Validaciones
        if (!nombre) {
            alert('El nombre del nivel es requerido');
            return false;
        }
        
        if (orden === '') {
            alert('El orden es requerido');
            return false;
        }
        
        // Datos a enviar - MISMA ESTRUCTURA QUE ÁREAS
        var datos = {
            nombre: nombre,
            orden: orden,
            esTituloUniversitario: esTituloUniversitario,
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT'
        };
        
        console.log('Actualizando nivel:', datos);
        
        $.ajax({
            url: '/api/niveles/' + id,
            type: 'POST',
            data: datos,
            success: function(response) {
                console.log('Respuesta:', response);
                if (response.success) {
                    alert('Nivel académico actualizado correctamente');
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
function eliminarNivel(id) {
    if (!confirm('¿Estás seguro de eliminar este nivel académico?')) return;
    
    $.ajax({
        url: '/api/niveles/' + id,
        type: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                alert('Nivel académico eliminado correctamente');
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