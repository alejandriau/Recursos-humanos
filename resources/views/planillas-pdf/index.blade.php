@extends('dashboard')

@section('title', 'documentos')

@section('contenido')
<div class="container">
    <!-- Header con Buscador -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3">
                <i class="fas fa-folder-open text-primary mr-2"></i>Mis documentos
            </h1>
            <p class="text-muted">Gestiona y busca tus documentos fácilmente</p>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('documentos.create') }}" class="btn btn-success">
                <i class="fas fa-plus mr-2"></i>Subir Archivo
            </a>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('documentos.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="input-group">
                            <input type="text" 
                                   name="busqueda" 
                                   class="form-control" 
                                   placeholder="Buscar por título, descripción o nombre de archivo..."
                                   value="{{ request('busqueda') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Puedes buscar por nombre de archivo como "reporte.docx"
                        </small>
                    </div>
                    
                    <div class="col-md-3 mb-2">
                        <select name="tema" class="form-control">
                            <option value="">Todos los temas</option>
                            @foreach($temas as $tema)
                                <option value="{{ $tema }}" {{ request('tema') == $tema ? 'selected' : '' }}>
                                    {{ $tema }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-2">
                        <select name="tipo" class="form-control">
                            <option value="">Todos los tipos</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo }}" {{ request('tipo') == $tipo ? 'selected' : '' }}>
                                    {{ ucfirst($tipo) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de documentos -->
    @if($archivos->count() > 0)
        <div class="row">
            @foreach($archivos as $archivo)
            <div class="col-md-3 mb-4">
                <div class="card file-card h-100"
                     onclick="abrirModal('{{ $archivo->id }}')"
                     style="cursor: pointer;">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @php
                                $icono = App\Http\Controllers\ArchivoController::getIcono($archivo->tipo);
                            @endphp
                            <i class="fas {{ $icono }} file-icon"></i>
                        </div>
                        
                        <h6 class="card-title mb-2" title="{{ $archivo->titulo }}">
                            {{ Str::limit($archivo->titulo, 25) }}
                        </h6>
                        
                        <div class="mb-2">
                            <small class="text-muted file-name" title="{{ $archivo->nombre_archivo }}">
                                {{ Str::limit($archivo->nombre_archivo, 20) }}
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <span class="badge badge-tema">{{ $archivo->tema }}</span>
                            <span class="badge badge-tipo">{{ $archivo->tipo }}</span>
                        </div>
                        
                        <div class="small text-muted">
                            <div><i class="fas fa-file"></i> {{ strtoupper($archivo->extension) }}</div>
                            <div><i class="fas fa-hdd"></i> {{ number_format($archivo->tamano / 1024, 1) }} KB</div>
                            <div><i class="fas fa-calendar"></i> {{ $archivo->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-top-0 pt-0">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('documentos.download', $archivo->id) }}" 
                               class="btn btn-sm btn-outline-success"
                               onclick="event.stopPropagation();">
                                <i class="fas fa-download"></i>
                            </a>
                            
                            <button class="btn btn-sm btn-outline-primary"
                                    onclick="abrirModal('{{ $archivo->id }}'); event.stopPropagation();">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                            
                            <form action="{{ route('documentos.destroy', $archivo->id) }}" 
                                  method="POST"
                                  onsubmit="return confirm('¿Eliminar este archivo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="event.stopPropagation();">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $archivos->appends(request()->query())->links() }}
        </div>
    @else
        <!-- Sin documentos -->
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
            <h3>No hay documentos</h3>
            <p class="text-muted mb-4">
                @if(request()->has('busqueda'))
                    No se encontraron documentos para "{{ request('busqueda') }}"
                @else
                    Sube tu primer archivo para comenzar
                @endif
            </p>
            <a href="{{ route('documentos.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-upload mr-2"></i>Subir Archivo
            </a>
        </div>
    @endif
</div>

<!-- Modal para ver archivo -->
<div class="modal fade" id="archivoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file mr-2"></i>
                    <span id="modalTitulo"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <!-- Información del archivo -->
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        <i id="modalIcono" class="fas fa-file fa-3x mb-3"></i>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Nombre del archivo:</strong></td>
                                <td><code id="modalNombreArchivo"></code></td>
                            </tr>
                            <tr>
                                <td><strong>Tamaño:</strong></td>
                                <td id="modalTamano"></td>
                            </tr>
                            <tr>
                                <td><strong>Tipo:</strong></td>
                                <td><span id="modalTipo" class="badge"></span></td>
                            </tr>
                            <tr>
                                <td><strong>Tema:</strong></td>
                                <td><span id="modalTema" class="badge"></span></td>
                            </tr>
                            <tr>
                                <td><strong>Subido:</strong></td>
                                <td id="modalFecha"></td>
                            </tr>
                        </table>
                        
                        <div id="modalDescripcion" class="mt-3"></div>
                    </div>
                </div>
                
                <!-- Vista previa del contenido -->
                <div class="mt-4">
                    <h6><i class="fas fa-eye mr-2"></i>Vista Previa</h6>
                    <div id="modalPreview" class="preview-content">
                        <div class="text-center py-5">
                            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                            <p class="mt-2">Cargando contenido...</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Cerrar
                </button>
                <a href="#" id="modalDownload" class="btn btn-primary">
                    <i class="fas fa-download mr-2"></i>Descargar
                </a>
                <a href="#" id="modalPreviewFull" class="btn btn-info" target="_blank">
                    <i class="fas fa-external-link-alt mr-2"></i>Ver Completo
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function abrirModal(archivoId) {
        // Mostrar modal con spinner
        $('#archivoModal').modal('show');
        
        // Cargar datos del archivo
        $.ajax({
            url: '/documentos/' + archivoId,
            method: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    var archivo = response.archivo;
                    
                    // Actualizar información básica
                    $('#modalTitulo').text(archivo.titulo);
                    $('#modalNombreArchivo').text(archivo.nombre_archivo);
                    $('#modalIcono').attr('class', 'fas ' + archivo.icono + ' fa-3x mb-3');
                    $('#modalTamano').text(archivo.tamano);
                    $('#modalTipo').text(archivo.tipo).addClass('badge-tipo');
                    $('#modalTema').text(archivo.tema).addClass('badge-tema');
                    $('#modalFecha').text(archivo.fecha);
                    $('#modalDownload').attr('href', archivo.download_url);
                    $('#modalPreviewFull').attr('href', archivo.preview_url);
                    
                    // Actualizar descripción
                    if (archivo.descripcion) {
                        $('#modalDescripcion').html('<strong>Descripción:</strong> ' + archivo.descripcion);
                    } else {
                        $('#modalDescripcion').html('<em class="text-muted">Sin descripción</em>');
                    }
                    
                    // Cargar vista previa del contenido
                    cargarPreviewContenido(archivoId);
                }
            },
            error: function() {
                $('#modalPreview').html(
                    '<div class="alert alert-danger">Error al cargar el archivo</div>'
                );
            }
        });
    }
    
    function cargarPreviewContenido(archivoId) {
        $('#modalPreview').html(
            '<div class="text-center py-3">' +
            '<i class="fas fa-spinner fa-spin fa-2x text-muted"></i>' +
            '<p class="mt-2">Cargando vista previa...</p>' +
            '</div>'
        );
        
        $.ajax({
            url: '/documentos/' + archivoId + '/contenido-preview',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.tipo === 'texto') {
                    $('#modalPreview').html(
                        '<div class="preview-content">' + 
                        response.contenido + 
                        '</div>'
                    );
                } else if (response.tipo === 'imagen') {
                    $('#modalPreview').html(
                        '<div class="text-center">' +
                        '<img src="' + response.url + '" class="preview-image img-fluid">' +
                        '</div>'
                    );
                } else {
                    $('#modalPreview').html(
                        '<div class="alert alert-info text-center">' +
                        '<i class="fas fa-info-circle fa-2x mb-3"></i>' +
                        '<p>' + (response.mensaje || 'No hay vista previa disponible') + '</p>' +
                        '<p class="small">Descarga el archivo para ver su contenido completo</p>' +
                        '</div>'
                    );
                }
            },
            error: function() {
                $('#modalPreview').html(
                    '<div class="alert alert-warning text-center">' +
                    '<i class="fas fa-exclamation-triangle"></i>' +
                    '<p class="mt-2">No se pudo cargar la vista previa</p>' +
                    '</div>'
                );
            }
        });
    }
    
    // Cerrar modal al presionar ESC
    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $('#archivoModal').modal('hide');
        }
    });
    
    // Buscar documentos en tiempo real
    $('#searchInput').on('input', function() {
        var query = $(this).val();
        if (query.length > 2) {
            // Puedes implementar búsqueda en tiempo real aquí
        }
    });
</script>
@endpush