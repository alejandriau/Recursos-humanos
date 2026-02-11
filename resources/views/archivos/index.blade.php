@extends('dashboard')

@section('title', 'documentos')

@section('contenido')
<style>
    /* Estilos ultra compactos para grid */
    .container-fluid { 
        padding: 0 15px; 
    }
    
    /* Cards ultra compactas */
    .file-card {
        transition: all 0.2s;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 0 !important;
        height: 100%;
    }
    
    .file-card:hover {
        border-color: #adb5bd;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .file-card .card-body {
        padding: 0.75rem 0.5rem !important;
    }
    
    .file-card .card-footer {
        padding: 0.5rem !important;
        background: transparent;
        border-top: 1px solid #f1f3f4;
    }
    
    /* Iconos más pequeños */
    .file-card i.fa-2x {
        font-size: 1.5rem !important;
    }
    
    /* Tipografía compacta */
    .file-card .card-title {
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.2rem !important;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .file-name {
        font-size: 0.65rem !important;
        padding: 1px 4px;
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        border-radius: 3px;
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Badges más pequeños */
    .badge {
        font-size: 0.6rem;
        padding: 0.2rem 0.4rem;
        font-weight: 500;
    }
    
    /* Texto de metadatos más pequeño */
    .file-card .small.text-muted div {
        font-size: 0.6rem;
        line-height: 1.2;
        display: inline-block;
    }
    
    .file-card .small.text-muted i {
        width: 12px;
        font-size: 0.6rem;
    }
    
    /* Botones más pequeños */
    .file-card .btn-sm {
        padding: 0.15rem 0.3rem;
        font-size: 0.65rem;
    }
    
    .file-card .btn-sm i {
        font-size: 0.65rem;
    }
    
    /* Grilla más densa */
    .row {
        margin-right: -5px;
        margin-left: -5px;
    }
    
    .col-md-3, .col-sm-4, .col-6 {
        padding-right: 5px;
        padding-left: 5px;
    }
    
    /* MODAL REDISEÑADO - VISTA PREVIA GRANDE */
    .modal-lg {
        max-width: 95% !important;
    }
    
    @media (min-width: 768px) {
        .modal-lg {
            max-width: 90% !important;
        }
    }
    
    @media (min-width: 1200px) {
        .modal-lg {
            max-width: 85% !important;
        }
    }
    
    .modal-content {
        border: none;
        border-radius: 12px;
    }
    
    .modal-header {
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
        border-radius: 12px 12px 0 0;
    }
    
    .modal-header .close {
        padding: 0.75rem;
        margin: -0.75rem -0.75rem -0.75rem auto;
        font-size: 1.5rem;
        font-weight: 300;
        color: #495057;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    
    .modal-header .close:hover {
        opacity: 1;
        color: #000;
    }
    
    .modal-body {
        padding: 1.5rem;
        background: #fff;
    }
    
    .modal-footer {
        padding: 0.75rem 1.25rem;
        border-top: 1px solid #e9ecef;
        background: #f8f9fa;
        border-radius: 0 0 12px 12px;
    }
    
    /* ÁREA DE VISTA PREVIA - AHORA GRANDE Y CLARA */
    .preview-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        min-height: 600px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e9ecef;
    }
    
    #modalPreview {
        width: 100%;
        height: 100%;
    }
    
    /* Preview para TEXTO */
    #modalPreview .preview-content {
        max-height: 550px;
        overflow-y: auto;
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        line-height: 1.6;
        border: 1px solid #dee2e6;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
        white-space: pre-wrap;
        word-break: break-word;
    }
    
    /* Preview para IMAGEN */
    #modalPreview img {
        max-height: 550px;
        max-width: 100%;
        object-fit: contain;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    /* Preview para PDF */
    #modalPreview iframe {
        width: 100%;
        height: 550px;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    /* Panel de información compacto pero legible */
    .info-panel {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.25rem;
        height: fit-content;
        border: 1px solid #e9ecef;
    }
    
    .info-panel table {
        margin-bottom: 0;
    }
    
    .info-panel td {
        padding: 0.5rem 0.25rem !important;
        font-size: 0.85rem;
        border: none;
        border-bottom: 1px solid #e9ecef;
    }
    
    .info-panel tr:last-child td {
        border-bottom: none;
    }
    
    .info-panel strong {
        font-weight: 600;
        color: #495057;
    }
    
    #modalNombreArchivo {
        font-size: 0.75rem;
        word-break: break-all;
        background: #fff;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        display: inline-block;
    }
    
    /* Descripción con scroll si es larga */
    .descripcion-box {
        max-height: 120px;
        overflow-y: auto;
        font-size: 0.8rem;
        padding: 0.5rem;
        background: white;
        border-radius: 6px;
        border: 1px solid #e9ecef;
    }
    
    /* Responsive para móviles */
    @media (max-width: 767px) {
        .modal-body .row {
            flex-direction: column;
        }
        
        .col-md-3, .col-md-9 {
            width: 100%;
            max-width: 100%;
            flex: 0 0 100%;
        }
        
        .col-md-3 {
            margin-bottom: 1rem;
        }
        
        .preview-section {
            min-height: 400px;
        }
        
        #modalPreview iframe,
        #modalPreview img,
        #modalPreview .preview-content {
            max-height: 400px;
        }
    }
</style>

<div class="container-fluid">
    <!-- Header compacto -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">
                <i class="fas fa-folder-open text-primary mr-2"></i>Mis documentos
            </h1>
            <p class="text-muted small mt-1 mb-0">Gestiona y busca tus documentos fácilmente</p>
        </div>
        <a href="{{ route('documentos.create') }}" class="btn btn-success btn-sm">
            <i class="fas fa-plus mr-1"></i>Subir
        </a>
    </div>

    <!-- Filtros compactos -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body p-2">
            <form method="GET" action="{{ route('documentos.index') }}">
                <div class="row no-gutters align-items-center">
                    <div class="col-md-5 pr-2">
                        <div class="input-group input-group-sm">
                            <input type="text" 
                                   name="busqueda" 
                                   class="form-control form-control-sm border-right-0" 
                                   placeholder="Buscar por título, descripción o archivo..."
                                   value="{{ request('busqueda') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary btn-sm" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2 pr-2">
                        <select name="tema" class="form-control form-control-sm">
                            <option value="">Todos los temas</option>
                            @foreach($temas as $tema)
                                <option value="{{ $tema }}" {{ request('tema') == $tema ? 'selected' : '' }}>
                                    {{ $tema }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2 pr-2">
                        <select name="tipo" class="form-control form-control-sm">
                            <option value="">Todos los tipos</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo }}" {{ request('tipo') == $tipo ? 'selected' : '' }}>
                                    {{ ucfirst($tipo) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-outline-primary btn-sm btn-block">
                            <i class="fas fa-filter mr-1"></i>Filtrar
                        </button>
                    </div>
                </div>
                <small class="form-text text-muted mt-1 pl-1">
                    <i class="fas fa-info-circle"></i> Busca por nombre exacto: "reporte.docx"
                </small>
            </form>
        </div>
    </div>

    <!-- Lista ultra compacta de documentos -->
    @if($archivos->count() > 0)
        <div class="row">
            @foreach($archivos as $archivo)
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                <div class="card file-card h-100"
                     onclick="abrirModal('{{ $archivo->id }}')"
                     style="cursor: pointer;">
                    <div class="card-body text-center p-2">
                        <!-- Icono -->
                        <div class="mb-2">
                            @if($archivo->tipo == 'word')
                                <i class="fas fa-file-word fa-2x text-primary"></i>
                            @elseif($archivo->tipo == 'excel')
                                <i class="fas fa-file-excel fa-2x text-success"></i>
                            @elseif($archivo->tipo == 'pdf')
                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                            @elseif($archivo->tipo == 'imagen')
                                <i class="fas fa-file-image fa-2x text-info"></i>
                            @elseif($archivo->tipo == 'powerpoint')
                                <i class="fas fa-file-powerpoint fa-2x text-warning"></i>
                            @elseif($archivo->tipo == 'texto')
                                <i class="fas fa-file-alt fa-2x text-secondary"></i>
                            @elseif($archivo->tipo == 'comprimido')
                                <i class="fas fa-file-archive fa-2x text-dark"></i>
                            @else
                                <i class="fas fa-file fa-2x text-muted"></i>
                            @endif
                        </div>
                        
                        <!-- Título -->
                        <!-- Nombre archivo -->
                        <div class="mb-1">
                            <small class="file-name " title="{{ $archivo->nombre_archivo }}">
                                <h5 class="card-title fs-4">
                                    {{ Str::limit($archivo->nombre_archivo) }}
                                </h5>
                            </small>
                        </div>
                        <h6 class="card-title" title="{{ $archivo->titulo }}">
                            {{ Str::limit($archivo->titulo, 30) }}
                        </h6>
                        
                        
                        <!-- Badges -->
                        <div class="mb-2">
                            <span class="badge badge-tema">{{ Str::limit($archivo->tema, 8) }}</span>
                            <span class="badge badge-tipo">{{ Str::limit($archivo->tipo, 6) }}</span>
                        </div>
                        
                        <!-- Metadatos -->
                        <div class="small text-muted">
                            <span class="mr-2">
                                <i class="fas fa-file"></i> {{ strtoupper($archivo->extension) }}
                            </span>
                            <span>
                                <i class="fas fa-hdd"></i> {{ round($archivo->tamano / 1024) }}KB
                            </span>
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="card-footer border-top-0 pt-0">
                        <div class="d-flex justify-content-around">
                            <a href="{{ route('documentos.download', $archivo->id) }}" 
                               class="btn btn-sm btn-outline-success px-2"
                               onclick="event.stopPropagation();">
                                <i class="fas fa-download"></i>
                            </a>
                            
                            <button class="btn btn-sm btn-outline-primary px-2"
                                    onclick="abrirModal('{{ $archivo->id }}'); event.stopPropagation();">
                                <i class="fas fa-eye"></i>
                            </button>
                            
                            <form action="{{ route('documentos.destroy', $archivo->id) }}" 
                                  method="POST"
                                  onsubmit="return confirm('¿Eliminar este archivo?')"
                                  style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm btn-outline-danger px-2"
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
        
        <!-- Paginación compacta -->
        <div class="d-flex justify-content-center mt-4">
            {{ $archivos->appends(request()->query())->links() }}
        </div>
    @else
        <!-- Mensaje sin archivos -->
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
            <h5 class="mb-2">No hay documentos</h5>
            <p class="text-muted small mb-4">
                @if(request()->has('busqueda'))
                    No se encontraron documentos para "{{ request('busqueda') }}"
                @else
                    Sube tu primer archivo para comenzar
                @endif
            </p>
            <a href="{{ route('documentos.create') }}" class="btn btn-primary">
                <i class="fas fa-upload mr-2"></i>Subir Archivo
            </a>
        </div>
    @endif
</div>

<!-- MODAL CON VISTA PREVIA GRANDE - CORREGIDO BOTÓN CERRAR -->
<div class="modal fade" id="archivoModal" tabindex="-1" role="dialog" aria-labelledby="archivoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archivoModalLabel">
                    <span id="modalTitulo"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <!-- Columna izquierda: Información (25%) -->
                    <div class="col-md-3">
                        <div class="info-panel">
                            <div class="text-center mb-3">
                                <i id="modalIcono" class="fas fa-file fa-3x mb-2"></i>
                                <div class="mt-2">
                                    <span id="modalTipo" class="badge badge-tipo mr-1"></span>
                                    <span id="modalTema" class="badge badge-tema"></span>
                                </div>
                            </div>
                            
                            <table class="table table-sm">
                                <tr>
                                    <td colspan="2"><strong>Nombre del archivo:</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <code id="modalNombreArchivo" style="font-size: 0.75rem;"></code>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tamaño:</strong></td>
                                    <td id="modalTamano"></td>
                                </tr>
                                <tr>
                                    <td><strong>Subido:</strong></td>
                                    <td id="modalFecha"></td>
                                </tr>
                                <tr>
                                    <td><strong>Extensión:</strong></td>
                                    <td><span id="modalExtension" class="badge badge-secondary"></span></td>
                                </tr>
                            </table>
                            
                            <div class="mt-3">
                                <strong>Descripción:</strong>
                                <div id="modalDescripcion" class="descripcion-box mt-1">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna derecha: VISTA PREVIA GRANDE (75%) -->
                    <div class="col-md-9">
                        <div class="preview-section">
                            <div id="modalPreview" class="w-100">
                                <!-- El contenido se carga vía AJAX -->
                                <div class="text-center py-5">
                                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                                    <p class="mt-3">Cargando vista previa del archivo...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <a href="#" id="modalDownload" class="btn btn-primary">
                        <i class="fas fa-download mr-1"></i> Descargar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // URLs
    const modalDataUrlTemplate = "{{ route('documentos.modal-data', ':id') }}";
    const previewUrlTemplate   = "{{ route('documentos.contenido-preview', ':id') }}";

    // Función para abrir modal - CORREGIDA
    function abrirModal(archivoId) {
        console.log('Abriendo modal para archivo:', archivoId);
        
        // Mostrar modal
        $('#archivoModal').modal('show');
        
        // Resetear preview con loader
        $('#modalPreview').html(
            '<div class="text-center py-5">' +
            '<i class="fas fa-spinner fa-spin fa-3x text-primary"></i>' +
            '<p class="mt-3">Cargando vista previa del archivo...</p>' +
            '</div>'
        );

        // Resetear datos
        $('#modalTitulo').text('Cargando...');
        $('#modalNombreArchivo').text('Cargando...');
        $('#modalTamano').text('Cargando...');
        $('#modalTipo').text('Cargando...').removeClass().addClass('badge');
        $('#modalTema').text('Cargando...').removeClass().addClass('badge');
        $('#modalExtension').text('Cargando...');
        $('#modalFecha').text('Cargando...');
        $('#modalDescripcion').html('<em class="text-muted">Cargando descripción...</em>');

        // Cargar datos del archivo
        $.ajax({
            url: modalDataUrlTemplate.replace(':id', archivoId),
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Datos recibidos:', response);
                
                if (response.success) {
                    var archivo = response.archivo;

                    // Título del modal
                    $('#modalTitulo').text(archivo.titulo || 'Sin título');
                    
                    // Información del archivo
                    $('#modalNombreArchivo').text(archivo.nombre_archivo || 'Desconocido');
                    $('#modalTamano').text(archivo.tamano || '0 KB');
                    $('#modalFecha').text(archivo.fecha || 'Desconocida');
                    $('#modalExtension').text(archivo.extension ? archivo.extension.toUpperCase() : 'N/A');
                    
                    // Badges
                    $('#modalTipo').text(archivo.tipo || 'desconocido').addClass('badge-tipo');
                    $('#modalTema').text(archivo.tema || 'sin tema').addClass('badge-tema');
                    
                    // URLs
                    $('#modalDownload').attr('href', archivo.download_url || '#');
                    $('#modalPreviewFull').attr('href', archivo.preview_url || '#');

                    // Icono según tipo
                    var iconoClass = 'fas fa-file fa-3x mb-2 text-muted';
                    if (archivo.tipo === 'word') iconoClass = 'fas fa-file-word fa-3x mb-2 text-primary';
                    else if (archivo.tipo === 'excel') iconoClass = 'fas fa-file-excel fa-3x mb-2 text-success';
                    else if (archivo.tipo === 'pdf') iconoClass = 'fas fa-file-pdf fa-3x mb-2 text-danger';
                    else if (archivo.tipo === 'imagen') iconoClass = 'fas fa-file-image fa-3x mb-2 text-info';
                    else if (archivo.tipo === 'powerpoint') iconoClass = 'fas fa-file-powerpoint fa-3x mb-2 text-warning';
                    else if (archivo.tipo === 'texto') iconoClass = 'fas fa-file-alt fa-3x mb-2 text-secondary';
                    
                    $('#modalIcono').attr('class', iconoClass);

                    // Descripción
                    if (archivo.descripcion) {
                        $('#modalDescripcion').html('<div class="descripcion-box">' + archivo.descripcion + '</div>');
                    } else {
                        $('#modalDescripcion').html('<em class="text-muted">Sin descripción</em>');
                    }

                    // Cargar preview del contenido
                    cargarPreviewContenido(archivoId);
                }
            },
            error: function(xhr) {
                console.error('Error cargando datos:', xhr);
                $('#modalPreview').html(
                    '<div class="alert alert-danger text-center p-4">' +
                    '<i class="fas fa-exclamation-triangle fa-3x mb-3"></i>' +
                    '<h5>Error al cargar</h5>' +
                    '<p class="mb-0">No se pudo cargar la información del archivo</p>' +
                    '</div>'
                );
            }
        });
    }

    // Función para cargar preview del contenido
    function cargarPreviewContenido(archivoId) {
        console.log('Cargando preview para:', archivoId);
        
        $.ajax({
            url: previewUrlTemplate.replace(':id', archivoId),
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Preview recibido:', response.tipo);
                
                let html = '';

                // TEXTO - Mostrar contenido completo
                if (response.tipo === 'texto') {
                    html = '<div class="preview-content">' + 
                           (response.contenido || 'Archivo vacío') + 
                           '</div>';
                }
                
                // IMAGEN - Mostrar grande
                else if (response.tipo === 'imagen') {
                    html = '<div class="text-center">' +
                           '<img src="' + response.url + '" alt="Vista previa" style="max-height: 550px; max-width: 100%;">' +
                           '</div>';
                }
                
                // PDF - Mostrar en iframe
                else if (response.tipo === 'pdf') {
                    html = '<iframe src="' + response.url + '" style="width:100%; height:550px;" frameborder="0"></iframe>';
                }
                
                // EXCEL, WORD, POWERPOINT - Mensaje con botón grande
                else if (response.tipo === 'excel') {
                    html = '<div class="text-center p-5">' +
                           '<i class="fas fa-file-excel fa-4x text-success mb-3"></i>' +
                           '<h5 class="mb-3">Archivo de Excel</h5>' +
                           '<p class="text-muted mb-4">' + (response.mensaje || 'No se puede mostrar vista previa') + '</p>' +
                           '<a href="' + response.url_descarga + '" class="btn btn-success btn-lg" download>' +
                           '<i class="fas fa-download mr-2"></i> Descargar Excel' +
                           '</a>' +
                           '</div>';
                }
                
                else if (response.tipo === 'word') {
                    html = '<div class="text-center p-5">' +
                           '<i class="fas fa-file-word fa-4x text-primary mb-3"></i>' +
                           '<h5 class="mb-3">Archivo de Word</h5>' +
                           '<p class="text-muted mb-4">' + (response.mensaje || 'No se puede mostrar vista previa') + '</p>' +
                           '<a href="' + response.url_descarga + '" class="btn btn-primary btn-lg" download>' +
                           '<i class="fas fa-download mr-2"></i> Descargar Word' +
                           '</a>' +
                           '</div>';
                }
                
                else if (response.tipo === 'powerpoint') {
                    html = '<div class="text-center p-5">' +
                           '<i class="fas fa-file-powerpoint fa-4x text-warning mb-3"></i>' +
                           '<h5 class="mb-3">Archivo de PowerPoint</h5>' +
                           '<p class="text-muted mb-4">' + (response.mensaje || 'No se puede mostrar vista previa') + '</p>' +
                           '<a href="' + response.url_descarga + '" class="btn btn-warning btn-lg" download>' +
                           '<i class="fas fa-download mr-2"></i> Descargar PowerPoint' +
                           '</a>' +
                           '</div>';
                }
                
                // OTROS FORMATOS
                else {
                    html = '<div class="text-center p-5">' +
                           '<i class="fas fa-file fa-4x text-muted mb-3"></i>' +
                           '<h5 class="mb-3">Sin vista previa disponible</h5>' +
                           '<p class="text-muted mb-4">' + (response.mensaje || 'Este tipo de archivo no se puede visualizar') + '</p>' +
                           '<a href="' + (response.url_descarga || '#') + '" class="btn btn-secondary btn-lg" download>' +
                           '<i class="fas fa-download mr-2"></i> Descargar archivo' +
                           '</a>' +
                           '</div>';
                }

                $('#modalPreview').html(html);
            },
            error: function(xhr) {
                console.error('Error cargando preview:', xhr);
                $('#modalPreview').html(
                    '<div class="alert alert-danger text-center p-5">' +
                    '<i class="fas fa-exclamation-triangle fa-3x mb-3"></i>' +
                    '<h5>Error al cargar la vista previa</h5>' +
                    '<p class="mb-0">Intenta descargar el archivo directamente</p>' +
                    '</div>'
                );
            }
        });
    }

    // Asegurar que el modal se cierre correctamente
    $(document).ready(function() {
        // Forzar cierre del modal con el botón X
        $('.modal .close').on('click', function() {
            $('#archivoModal').modal('hide');
        });
        
        // Cerrar con el botón Cerrar del footer
        $('[data-dismiss="modal"]').on('click', function() {
            $('#archivoModal').modal('hide');
        });
    });
</script>

@push('styles')
<style>
    /* Badges personalizados */
    .badge-tema {
        background-color: #e3f2fd;
        color: #1565c0;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }
    
    .badge-tipo {
        background-color: #e8f5e9;
        color: #2e7d32;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }
    
    /* Hover en cards */
    .file-card {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .file-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-color: #80bdff;
    }
    
    /* Scrollbar personalizado */
    .preview-content::-webkit-scrollbar {
        width: 8px;
    }
    
    .preview-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .preview-content::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }
    
    .preview-content::-webkit-scrollbar-thumb:hover {
        background: #999;
    }
    
    /* Paginación compacta */
    .pagination {
        margin-bottom: 0;
    }
    
    .page-link {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
    }
    
    /* Asegurar que el modal se cierre */
    .modal-backdrop {
        z-index: 1040 !important;
    }
    
    .modal {
        z-index: 1050 !important;
    }
</style>
@endpush

@endsection