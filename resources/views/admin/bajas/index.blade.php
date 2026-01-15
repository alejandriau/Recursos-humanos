@extends('dashboard')

@section('contenido')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-uppercase fw-bold">Listado de Bajas de Personal</h2>
        <a href="{{ route('altasbajas') }}" class="btn btn-success">
            <i class="fas fa-arrow-left me-2"></i>Volver a Altas/Bajas
        </a>
    </div>

    <!-- Filtros Mejorados -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('bajasaltas.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar por nombre</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre, apellidos..."
                        value="{{ request('nombre') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Motivo</label>
                    <select name="motivo" class="form-select">
                        <option value="">Todos los motivos</option>
                        <option value="renuncia" {{ request('motivo') == 'renuncia' ? 'selected' : '' }}>Renuncia</option>
                        <option value="despido" {{ request('motivo') == 'despido' ? 'selected' : '' }}>Despido</option>
                        <option value="jubilacion" {{ request('motivo') == 'jubilacion' ? 'selected' : '' }}>Jubilación</option>
                        <option value="termino_contrato" {{ request('motivo') == 'termino_contrato' ? 'selected' : '' }}>Término de contrato</option>
                        <option value="otros" {{ request('motivo') == 'otros' ? 'selected' : '' }}>Otros</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark me-2">
                        <i class="fas fa-filter me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('bajasaltas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh me-1"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas -->
    @if(isset($estadisticas))
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $estadisticas['total'] }}</h4>
                            <p class="mb-0">Total Bajas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $estadisticas['este_mes'] }}</h4>
                            <p class="mb-0">Este Mes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $estadisticas['con_pdf'] }}</h4>
                            <p class="mb-0">Con PDF</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-pdf fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $estadisticas['sin_pdf'] }}</h4>
                            <p class="mb-0">Sin PDF</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-excel fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabla Mejorada -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-sm" id="tablaBajas">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">N°</th>
                            <th>Nombre Completo</th>
                            <th>CI</th>
                            <th>Fecha Ingreso</th>
                            <th>Fecha Baja</th>
                            <th>Motivo</th>
                            <th>Observación</th>
                            <th>Tiempo en Institución</th>
                            <th>PDF</th>
                            <th width="150" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bajas as $key => $baja)
                            <tr>
                                <td class="fw-bold">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($baja['foto'] && file_exists(public_path('storage/' . $baja['foto'])))
                                            <img src="{{ asset('storage/' . $baja['foto']) }}"
                                                 class="rounded-circle me-2"
                                                 width="32" height="32"
                                                 alt="{{ $baja['nombre'] }}">
                                        @else
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                 style="width: 32px; height: 32px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                        <span>{{ $baja['nombre'] }}</span>
                                    </div>
                                </td>
                                <td>{{ $baja['ci'] ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($baja['fecha_ingreso'])->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-danger">
                                        {{ \Carbon\Carbon::parse($baja['fecha_baja'])->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $baja['motivo'] }}</span>
                                </td>
                                <td>
                                    @if($baja['observacion'])
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;"
                                              title="{{ $baja['observacion'] }}">
                                            {{ $baja['observacion'] }}
                                        </span>
                                    @else
                                        <span class="text-muted">Sin observación</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $baja['tiempo_en_institucion'] }}</span>
                                </td>
                                <td>
                                    @if($baja['pdfbaja'])
                                        <div class="btn-group" role="group">
                                            <!-- Botón Ver PDF en Modal -->
                                            <button type="button" class="btn btn-sm btn-outline-danger ver-pdf-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalPdf"
                                                    data-pdf-url="{{ route('bajasaltas.ver-pdf', $baja['id']) }}"
                                                    data-download-url="{{ route('bajasaltas.descargar-pdf', $baja['id']) }}"
                                                    data-nombre="{{ $baja['nombre'] }}"
                                                    title="Ver PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>

                                            <!-- Botón Descargar -->
                                            <a href="{{ route('bajasaltas.descargar-pdf', $baja['id']) }}"
                                               class="btn btn-sm btn-outline-success"
                                               title="Descargar PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted small">Sin PDF</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Botón Ver Detalles -->
                                        <button type="button" class="btn btn-sm btn-outline-info ver-detalles-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalVer"
                                                data-baja-data='@json($baja)'
                                                title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Botón Editar -->
                                        <button type="button" class="btn btn-sm btn-outline-primary editar-baja-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditar"
                                                data-baja-id="{{ $baja['id'] }}"
                                                data-baja-data='@json($baja)'
                                                title="Editar Baja">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Botón Eliminar -->
                                        <button type="button" class="btn btn-sm btn-outline-danger eliminar-baja-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEliminar"
                                                data-baja-id="{{ $baja['id'] }}"
                                                data-baja-nombre="{{ $baja['nombre'] }}"
                                                title="Eliminar Baja">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn text-dark fw-bold fs-4" type="button" id="dropdownMenu{{ $baja['id'] }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            ⋮
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $baja['id'] }}">
                                            <li><a class="dropdown-item" href="{{ route('personas.show', $baja['persona_id']) }}">🔍 Ver</a></li>
                                            <li><a class="dropdown-item" href="{{ route('persona.dashboard', $baja['persona_id'])}}">📊 Documentación</a></li>
                                            <li><a class="dropdown-item" href="{{ route('personas.historial', $baja['persona_id'])}}">
                                                📋 Historiall
                                            </a></li>
                                            <li>
                                                <a href="#" class="dropdown-item btn-reactivar"
                                                data-persona-id="{{ $baja['persona_id'] }}"
                                                data-persona-nombre="{{ $baja['nombre'] }}">
                                                    <i class="fas fa-user-check me-1"></i>Dar de Alta
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay registros de bajas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <!-- Paginación Mejorada -->
            @if($bajas instanceof \Illuminate\Pagination\LengthAwarePaginator && $bajas->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Mostrando {{ $bajas->firstItem() }} - {{ $bajas->lastItem() }} de {{ $bajas->total() }}
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Botón Anterior --}}
                            <li class="page-item {{ $bajas->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $bajas->previousPageUrl() }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            {{-- Números de página --}}
                            @foreach ($bajas->getUrlRange(max(1, $bajas->currentPage() - 2), min($bajas->lastPage(), $bajas->currentPage() + 2)) as $page => $url)
                                <li class="page-item {{ $page == $bajas->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach

                            {{-- Botón Siguiente --}}
                            <li class="page-item {{ !$bajas->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $bajas->nextPageUrl() }}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Único para Ver PDF -->
<div class="modal fade" id="modalPdf" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf me-2"></i>
                    PDF de Baja - <span id="pdfNombre"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <a href="#" id="pdfDownloadBtn" class="btn btn-success me-2">
                        <i class="fas fa-download me-1"></i>Descargar PDF
                    </a>
                    <button type="button" class="btn btn-primary" id="pdfPrintBtn">
                        <i class="fas fa-print me-1"></i>Imprimir
                    </button>
                </div>

                <div class="embed-responsive embed-responsive-16by9">
                <iframe src="" class="w-100"
                        style="height: 80vh; border: none;"
                        id="pdfIframe">
                </iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Único para Ver Detalles -->
<div class="modal fade" id="modalVer" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Detalles de Baja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalVerBody">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Único para Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data" id="formEditar">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Editar Baja - <span id="editarNombre"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalEditarBody">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Único para Eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                </div>
                <p>¿Estás seguro de que deseas eliminar la baja de <strong id="eliminarNombre"></strong>?</p>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta acción reactivará al empleado en el sistema y no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <form action="" method="POST" id="formEliminar">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts adicionales -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Modal PDF
        const modalPdf = document.getElementById('modalPdf');
        if (modalPdf) {
            modalPdf.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const pdfUrl = button.getAttribute('data-pdf-url');
                const downloadUrl = button.getAttribute('data-download-url');
                const nombre = button.getAttribute('data-nombre');

                document.getElementById('pdfNombre').textContent = nombre;
                document.getElementById('pdfDownloadBtn').href = downloadUrl;

                const iframe = document.getElementById('pdfIframe');
                iframe.src = pdfUrl;
                        const pdfViewUrl = pdfUrl + '#view=FitH&zoom=100';
                iframe.src = pdfViewUrl;
            });

            modalPdf.addEventListener('hidden.bs.modal', function () {
                const iframe = document.getElementById('pdfIframe');
                iframe.src = '';
            });

            document.getElementById('pdfPrintBtn').addEventListener('click', function() {
                const iframe = document.getElementById('pdfIframe');
                if (iframe.contentWindow) {
                    iframe.contentWindow.print();
                }
            });
        }

        // Modal Ver Detalles
        const modalVer = document.getElementById('modalVer');
        if (modalVer) {
            modalVer.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const bajaData = JSON.parse(button.getAttribute('data-baja-data'));

                const contenido = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Información Personal</h6>
                            <p><strong>Nombre:</strong> ${bajaData.nombre}</p>
                            <p><strong>CI:</strong> ${bajaData.ci || 'N/A'}</p>
                            <p><strong>Fecha Nacimiento:</strong> ${bajaData.fecha_nacimiento ? new Date(bajaData.fecha_nacimiento).toLocaleDateString('es-ES') : 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Información de Baja</h6>
                            <p><strong>Fecha Ingreso:</strong> ${new Date(bajaData.fecha_ingreso).toLocaleDateString('es-ES')}</p>
                            <p><strong>Fecha Baja:</strong> ${new Date(bajaData.fecha_baja).toLocaleDateString('es-ES')}</p>
                            <p><strong>Tiempo:</strong> ${bajaData.tiempo_en_institucion}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Motivo:</strong> <span class="badge bg-warning text-dark">${bajaData.motivo}</span></p>
                            <p><strong>Observación:</strong> ${bajaData.observacion || 'Sin observación'}</p>
                            ${bajaData.pdfbaja ? `
                                <p><strong>Documento:</strong></p>
                                <div class="btn-group">
                                    <a href="${bajaData.pdfbaja}" target="_blank" class="btn btn-danger">
                                        <i class="fas fa-file-pdf me-1"></i>Ver PDF
                                    </a>
                                    <a href="${bajaData.pdfbaja}" download class="btn btn-success">
                                        <i class="fas fa-download me-1"></i>Descargar
                                    </a>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;

                document.getElementById('modalVerBody').innerHTML = contenido;
            });
        }

        // Modal Editar
        const modalEditar = document.getElementById('modalEditar');
        if (modalEditar) {
            modalEditar.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const bajaId = button.getAttribute('data-baja-id');
                const bajaData = JSON.parse(button.getAttribute('data-baja-data'));

                document.getElementById('editarNombre').textContent = bajaData.nombre;
                document.getElementById('formEditar').action = `/bajasaltas/${bajaId}`;

                const contenido = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha de Baja *</label>
                                <input type="date" name="fecha" class="form-control" value="${new Date(bajaData.fecha_baja).toISOString().split('T')[0]}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Motivo *</label>
                                <input type="text" name="motivo" class="form-control" value="${bajaData.motivo || ''}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observacion" class="form-control" rows="3" placeholder="Ingrese observaciones adicionales...">${bajaData.observacion || ''}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">PDF de Baja</label>
                        <input type="file" name="pdffile" class="form-control" accept=".pdf">
                        <div class="form-text">
                            Formatos aceptados: PDF (Máximo 2MB)
                            ${bajaData.pdfbaja ? `
                                <br>
                                <a href="${bajaData.pdfbaja}" target="_blank" class="text-danger">
                                    <i class="fas fa-file-pdf me-1"></i>Ver PDF actual
                                </a>
                            ` : '<span class="text-muted">No hay PDF cargado actualmente</span>'}
                        </div>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Información del Empleado</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Nombre:</strong> ${bajaData.nombre}</p>
                                    <p class="mb-1"><strong>CI:</strong> ${bajaData.ci || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Fecha Ingreso:</strong> ${new Date(bajaData.fecha_ingreso).toLocaleDateString('es-ES')}</p>
                                    <p class="mb-1"><strong>Tiempo en Institución:</strong> ${bajaData.tiempo_en_institucion}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('modalEditarBody').innerHTML = contenido;
            });
        }

        // Modal Eliminar
        const modalEliminar = document.getElementById('modalEliminar');
        if (modalEliminar) {
            modalEliminar.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const bajaId = button.getAttribute('data-baja-id');
                const bajaNombre = button.getAttribute('data-baja-nombre');

                document.getElementById('eliminarNombre').textContent = bajaNombre;
                document.getElementById('formEliminar').action = `/bajasaltas/${bajaId}`;
            });
        }

        // Validación de archivos PDF
        document.addEventListener('submit', function(e) {
            if (e.target.matches('#formEditar')) {
                const fileInput = e.target.querySelector('input[type="file"]');
                if (fileInput && fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const fileType = file.type;
                    const fileSize = file.size;

                    if (fileType !== 'application/pdf') {
                        e.preventDefault();
                        alert('Solo se permiten archivos PDF.');
                        return;
                    }

                    if (fileSize > 2 * 1024 * 1024) {
                        e.preventDefault();
                        alert('El archivo PDF no debe exceder los 2MB.');
                        return;
                    }
                }
            }

            if (e.target.matches('#formEliminar')) {
                if (!confirm('¿Estás seguro de que deseas eliminar este registro? Esta acción reactivará al empleado.')) {
                    e.preventDefault();
                }
            }
        });
    });
    // SweetAlert2 para reactivar
    document.querySelectorAll('.btn-reactivar').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            const personaId = this.dataset.personaId;
            const personaNombre = this.dataset.personaNombre;

            Swal.fire({
                title: `¿Activar a ${personaNombre}?`,
                text: 'La persona volverá a estar activa en el sistema',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, activar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/personas/${personaId}/activar`;

                }
            });
        });
    });
</script>
@if(session('success'))
<script>
    Swal.fire({
        position: "top-end",
        icon: "success",
        title: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 1500
    });
</script>
@endif


@endsection
