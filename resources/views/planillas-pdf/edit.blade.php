@extends('dashboard')

@section('contenido')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-edit me-3 fs-4"></i>
                        <div>
                            <h4 class="mb-0 fw-bold">Editar Planilla PDF</h4>
                            <p class="mb-0 opacity-75">Actualizar información de la planilla</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error al procesar el formulario:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('planillas-pdf.update', $planilla->id) }}" method="POST" enctype="multipart/form-data" id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-3">
                                <i class="fas fa-file-pdf me-2"></i>Archivo actual
                            </label>

                            <div class="current-file-info border rounded-3 p-3 bg-light">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-primary me-3 fs-2"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $planilla->nombre_original }}</h6>
                                        <p class="text-muted small mb-1">Subido el: {{ $planilla->created_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-muted small mb-0">
                                            Tamaño: {{ number_format(filesize(storage_path('app/public/' . $planilla->ruta)) / 1024, 2) }} KB
                                        </p>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('planillas-pdf.show', $planilla->id) }}"
                                           class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </a>
                                        <a href="{{ route('planillas-pdf.download', $planilla->id) }}"
                                           class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-download me-1"></i>Descargar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="nuevo_archivo_pdf" class="form-label fw-semibold mb-3">
                                <i class="fas fa-sync-alt me-2"></i>Reemplazar archivo PDF (Opcional)
                            </label>

                            <div class="file-upload-area border rounded-3 p-4 text-center position-relative"
                                 id="dropZone">
                                <input type="file" class="form-control d-none" id="nuevo_archivo_pdf"
                                       name="nuevo_archivo_pdf" accept=".pdf">

                                <div class="file-upload-content" id="fileUploadContent">
                                    <i class="fas fa-file-pdf text-warning mb-3" style="font-size: 3rem;"></i>
                                    <h5 class="mb-2">Arrastre el nuevo archivo PDF aquí</h5>
                                    <p class="text-muted mb-3">o haga clic para seleccionar (opcional)</p>
                                    <button type="button" class="btn btn-outline-warning" id="browseBtn">
                                        <i class="fas fa-folder-open me-2"></i>Seleccionar nuevo archivo
                                    </button>
                                </div>

                                <div class="file-preview d-none" id="filePreview">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-warning me-3 fs-2"></i>
                                        <div class="flex-grow-1 text-start">
                                            <h6 class="mb-1" id="fileName">Nombre del archivo</h6>
                                            <p class="text-muted small mb-0" id="fileSize">Tamaño del archivo</p>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeFile">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-text mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Si no selecciona un nuevo archivo, se mantendrá el actual
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="periodo_pago" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-alt me-2"></i>Período de pago
                                </label>
                                <input type="text" class="form-control" id="periodo_pago"
                                       name="periodo_pago"
                                       value="{{ old('periodo_pago', $planilla->periodo_pago) }}"
                                       placeholder="Ej: Octubre 2023">
                                <div class="form-text">
                                    Ej: Octubre 2023, Noviembre 2023, etc.
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="anio" class="form-label fw-semibold">
                                    <i class="fas fa-calendar me-2"></i>Año
                                </label>
                                <select class="form-select" id="anio" name="anio" required>
                                    <option value="">Seleccionar año</option>
                                    @php
                                        $currentYear = date('Y');
                                        $startYear = 2020;
                                        $endYear = $currentYear + 5;
                                    @endphp
                                    @for($year = $endYear; $year >= $startYear; $year--)
                                        <option value="{{ $year }}"
                                            {{ old('anio', $planilla->anio) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                <div class="form-text">
                                    Año al que corresponde la planilla
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="fecha_elaboracion" class="form-label fw-semibold">
                                <i class="fas fa-calendar-day me-2"></i>Fecha de elaboración
                            </label>
                            <input type="date" class="form-control" id="fecha_elaboracion"
                                   name="fecha_elaboracion"
                                   value="{{ old('fecha_elaboracion', $planilla->fecha_elaboracion ? $planilla->fecha_elaboracion->format('Y-m-d') : date('Y-m-d')) }}">
                            <div class="form-text">
                                Fecha en que se elaboró la planilla
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="total_empleados" class="form-label fw-semibold">
                                <i class="fas fa-users me-2"></i>Total de empleados
                            </label>
                            <input type="number" class="form-control" id="total_empleados"
                                   name="total_empleados"
                                   value="{{ old('total_empleados', $planilla->total_empleados) }}"
                                   min="0" placeholder="Ej: 150">
                            <div class="form-text">
                                Número total de empleados en esta planilla
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notas" class="form-label fw-semibold">
                                <i class="fas fa-sticky-note me-2"></i>Notas adicionales
                            </label>
                            <textarea class="form-control" id="notas" name="notas"
                                      rows="3" placeholder="Agregue cualquier nota o comentario sobre esta planilla...">{{ old('notas', $planilla->notas ?? '') }}</textarea>
                        </div>

                        <div class="alert alert-warning" role="alert">
                            <div class="d-flex">
                                <i class="fas fa-exclamation-triangle me-3 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Precaución</h6>
                                    <p class="mb-0">Al reemplazar el archivo, el anterior será eliminado permanentemente del sistema.</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-top pt-4 mt-3">
                            <div class="mb-3 mb-md-0">
                                <a href="{{ route('planillas-pdf.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al listado
                                </a>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-2"></i>Eliminar
                                </button>
                                <button type="submit" class="btn btn-warning px-4" id="submitBtn">
                                    <i class="fas fa-save me-2"></i>Actualizar Planilla
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar permanentemente esta planilla?</p>
                <div class="alert alert-danger mb-0">
                    <strong>Archivo:</strong> {{ $planilla->nombre_original }}<br>
                    <strong>Período:</strong> {{ $planilla->periodo_pago }}<br>
                    <strong>Año:</strong> {{ $planilla->anio }}
                </div>
                <p class="mt-3 mb-0"><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('planillas-pdf.destroy', $planilla->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Eliminar Permanentemente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.file-upload-area {
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6 !important;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-area:hover {
    border-color: #ffc107 !important;
    background-color: #fffbf0;
}

.file-upload-area.dragover {
    border-color: #ffc107 !important;
    background-color: #fff8e1;
}

.file-preview {
    animation: fadeIn 0.5s ease;
}

.current-file-info {
    background-color: #f8f9fa !important;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #e0a800;
    color: #000;
}

.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
}

.form-label {
    color: #495057;
    font-weight: 600;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('nuevo_archivo_pdf');
    const browseBtn = document.getElementById('browseBtn');
    const fileUploadContent = document.getElementById('fileUploadContent');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeFileBtn = document.getElementById('removeFile');
    const editForm = document.getElementById('editForm');
    const submitBtn = document.getElementById('submitBtn');
    const anioSelect = document.getElementById('anio');
    const periodoPagoInput = document.getElementById('periodo_pago');

    // Abrir selector de archivos al hacer clic en el área o botón
    dropZone.addEventListener('click', function(e) {
        if (e.target !== browseBtn && e.target !== removeFileBtn) {
            fileInput.click();
        }
    });

    browseBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.click();
    });

    // Manejar la selección de archivos
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            handleFileSelection(this.files[0]);
        }
    });

    // Manejar arrastrar y soltar
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropZone.classList.add('dragover');
    }

    function unhighlight() {
        dropZone.classList.remove('dragover');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            handleFileSelection(files[0]);
        }
    }

    function handleFileSelection(file) {
        // Validar tipo de archivo
        if (file.type !== 'application/pdf') {
            alert('Error: Solo se permiten archivos PDF');
            return;
        }

        // Validar tamaño (10MB)
        const maxSize = 10 * 1024 * 1024; // 10MB en bytes
        if (file.size > maxSize) {
            alert('Error: El archivo es demasiado grande. El tamaño máximo permitido es 10MB');
            return;
        }

        // Mostrar vista previa
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);

        fileUploadContent.classList.add('d-none');
        filePreview.classList.remove('d-none');

        // Intentar extraer información del nombre del archivo si los campos están vacíos
        extractInfoFromFileName(file.name);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function extractInfoFromFileName(fileName) {
        // Solo extraer información si los campos están vacíos o no han sido modificados
        if (!periodoPagoInput.value || periodoPagoInput.value === '{{ $planilla->periodo_pago }}') {
            const periodMatch = extractPeriodFromFileName(fileName);
            if (periodMatch) {
                periodoPagoInput.value = periodMatch;
            }
        }

        // Extraer año del nombre del archivo si no está seleccionado
        if (!anioSelect.value || anioSelect.value === '{{ $planilla->anio }}') {
            const yearRegex = /(?:^|\D)(\d{4})(?:\D|$)/;
            const yearMatch = fileName.match(yearRegex);

            if (yearMatch) {
                const year = parseInt(yearMatch[1]);
                const currentYear = new Date().getFullYear();

                // Validar que el año sea razonable (entre 2020 y 5 años en el futuro)
                if (year >= 2020 && year <= currentYear + 5) {
                    anioSelect.value = year;
                }
            }
        }
    }

    function extractPeriodFromFileName(fileName) {
        // Buscar meses en español
        const months = [
            'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
        ];

        const monthRegex = new RegExp(months.join('|'), 'gi');
        const monthMatch = fileName.match(monthRegex);

        if (monthMatch) {
            const month = monthMatch[0].charAt(0).toUpperCase() + monthMatch[0].slice(1);

            // Buscar año en el nombre del archivo para el período
            const yearRegex = /\b(20\d{2})\b/;
            const yearMatch = fileName.match(yearRegex);
            const year = yearMatch ? yearMatch[0] : anioSelect.value;

            return `${month} ${year}`;
        }

        // Buscar patrones como "01-2023", "01/2023"
        const monthYearRegex = /\b(\d{1,2})[\/\-](\d{4})\b/;
        const monthYearMatch = fileName.match(monthYearRegex);

        if (monthYearMatch) {
            const month = parseInt(monthYearMatch[1]);
            const year = monthYearMatch[2];
            const monthNames = [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ];

            if (month >= 1 && month <= 12) {
                return `${monthNames[month - 1]} ${year}`;
            }
        }

        return null;
    }

    // Eliminar archivo seleccionado
    removeFileBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.value = '';
        filePreview.classList.add('d-none');
        fileUploadContent.classList.remove('d-none');
    });

    // Validación antes de enviar el formulario
    editForm.addEventListener('submit', function(e) {
        if (!anioSelect.value) {
            e.preventDefault();
            alert('Por favor, seleccione el año de la planilla');
            return;
        }

        // Cambiar el texto del botón durante el envío
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Actualizando...';
        submitBtn.disabled = true;
    });
});
</script>
@endsection
