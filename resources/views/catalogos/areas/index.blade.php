@extends('dashboard')

@section('title', 'Catálogo de Áreas de Conocimiento')
@section('header', 'Gestión de Áreas de Conocimiento')

@section('contenido')
<div class="container-fluid fade-in">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-tags me-2 text-primary"></i>
                        Lista de Áreas de Conocimiento
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearArea">
                        <i class="fas fa-plus me-1"></i> Nueva Área
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaAreas">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Carreras Asociadas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($areas as $area)
                                <tr>
                                    <td>{{ $area->id }}</td>
                                    <td><strong>{{ $area->nombre }}</strong></td>
                                    <td>{{ $area->descripcion ?? 'Sin descripción' }}</td>
                                    <td>
                                        @if($area->estado)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $area->carreras_count ?? 0 }} carreras</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" 
                                                    class="btn btn-warning btn-editar" 
                                                    data-id="{{ $area->id }}"
                                                    data-nombre="{{ $area->nombre }}"
                                                    data-descripcion="{{ $area->descripcion }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" 
                                                    onclick="eliminarArea({{ $area->id }})" 
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

<!-- Modal Crear Área -->
<!-- Modal Crear Área -->
<div class="modal fade" id="modalCrearArea" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Nueva Área de Conocimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearArea">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Área *</label>
                        <input type="text" name="nombre" class="form-control" required>
                        <small class="text-muted">Máximo 300 caracteres</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
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

<!-- Modal Editar Área -->
<div class="modal fade" id="modalEditarArea" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Área de Conocimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarArea">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Área *</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3"></textarea>
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
// Esperar a que el DOM esté listo
// Reemplaza completamente tu script con esta versión simplificada para pruebas
$(document).ready(function() {
    console.log('✅ DOM Listo');
    
    // FORMULARIO DE CREACIÓN SIMPLIFICADO
    $('#formCrearArea').on('submit', function(e) {
        e.preventDefault();
        
        console.log('📝 Formulario enviado');
        
        var nombre = $('input[name="nombre"]', this).val().trim();
        var descripcion = $('textarea[name="descripcion"]', this).val();
        
        if (!nombre) {
            alert('El nombre es requerido');
            return false;
        }
        
        // Datos a enviar
        var datos = {
            nombre: nombre,
            descripcion: descripcion,
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        console.log('Enviando datos:', datos);
        
        // Enviar petición
        $.ajax({
            url: '/api/areas',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta:', response);
                if (response.success) {
                    alert('Área creada exitosamente');
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
    
    // Editar área (tu código existente)
    $('.btn-editar').on('click', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        var descripcion = $(this).data('descripcion') || '';
        
        $('#edit_id').val(id);
        $('#edit_nombre').val(nombre);
        $('#edit_descripcion').val(descripcion);
        
        $('#modalEditarArea').modal('show');
    });
    
    // Formulario de edición
    $('#formEditarArea').on('submit', function(e) {
        e.preventDefault();
        
        var id = $('#edit_id').val();
        var nombre = $('#edit_nombre').val().trim();
        var descripcion = $('#edit_descripcion').val();
        
        if (!nombre) {
            alert('El nombre es requerido');
            return false;
        }
        
        var datos = {
            nombre: nombre,
            descripcion: descripcion,
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT'
        };
        
        $.ajax({
            url: '/api/areas/' + id,
            type: 'POST',
            data: datos,
            success: function(response) {
                if (response.success) {
                    alert('Área actualizada correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('Error al actualizar: ' + (xhr.responseJSON?.message || xhr.statusText));
            }
        });
    });
});

function eliminarArea(id) {
    if (!confirm('¿Estás seguro de eliminar esta área?')) return;
    
    $.ajax({
        url: '/api/areas/' + id,
        type: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                alert('Área eliminada correctamente');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Error al eliminar: ' + (xhr.responseJSON?.message || xhr.statusText));
        }
    });
}
</script>

@endsection