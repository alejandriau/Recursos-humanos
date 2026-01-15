@extends('dashboard')

@section('contenido')
<div class="container">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <h2 class="mb-4">Editar Certificado</h2>

    <form action="{{ route('certificados.update', $certificado->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Persona (solo lectura porque ya está asociada) -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Persona</label>
                <div class="form-control bg-light">
                    {{ $certificado->persona->nombre }}
                    {{ $certificado->persona->apellidoPat }}
                    {{ $certificado->persona->apellidoMat }}
                </div>
                <input type="hidden" name="idPersona" value="{{ $certificado->idPersona }}">
                <small class="text-muted">No se puede cambiar la persona asociada</small>
            </div>

            <!-- Categoría -->
            <div class="col-md-6 mb-3">
                <label for="categoria" class="form-label">Categoría *</label>
                <select name="categoria" id="categoria" class="form-select @error('categoria') is-invalid @enderror" required>
                    <option value="">Seleccionar categoría</option>
                    <option value="quechua" {{ old('categoria', $certificado->categoria) == 'quechua' ? 'selected' : '' }}>
                        Quechua (vencen cada 3 años)
                    </option>
                    <option value="ley_1178" {{ old('categoria', $certificado->categoria) == 'ley_1178' ? 'selected' : '' }}>
                        Ley 1178 (sin vencimiento)
                    </option>
                    <option value="politicas_publicas" {{ old('categoria', $certificado->categoria) == 'politicas_publicas' ? 'selected' : '' }}>
                        Políticas Públicas (sin vencimiento)
                    </option>
                    <option value="responsabilidad_funcion_publica" {{ old('categoria', $certificado->categoria) == 'responsabilidad_funcion_publica' ? 'selected' : '' }}>
                        Responsabilidad por la Función Pública (sin vencimiento)
                    </option>
                    <option value="otros" {{ old('categoria', $certificado->categoria) == 'otros' ? 'selected' : '' }}>
                        Otros (sin vencimiento)
                    </option>
                </select>
                @error('categoria')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <!-- Nombre del Certificado -->
            <div class="col-md-12 mb-3">
                <label for="nombre" class="form-label">Nombre del Certificado *</label>
                <input type="text" name="nombre" id="nombre"
                       class="form-control @error('nombre') is-invalid @enderror"
                       value="{{ old('nombre', $certificado->nombre) }}" required>
                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <!-- Tipo -->
            <div class="col-md-4 mb-3">
                <label for="tipo" class="form-label">Tipo</label>
                <input type="text" name="tipo" id="tipo"
                       class="form-control @error('tipo') is-invalid @enderror"
                       value="{{ old('tipo', $certificado->tipo) }}">
                @error('tipo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Fecha de Emisión -->
            <div class="col-md-4 mb-3">
                <label for="fecha" class="form-label">Fecha de Emisión *</label>
                <input type="date" name="fecha" id="fecha"
                       class="form-control @error('fecha') is-invalid @enderror"
                       value="{{ old('fecha', $certificado->fecha ? $certificado->fecha->format('Y-m-d') : '') }}" required>
                @error('fecha')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Instituto -->
            <div class="col-md-4 mb-3">
                <label for="instituto" class="form-label">Institución</label>
                <input type="text" name="instituto" id="instituto"
                       class="form-control @error('instituto') is-invalid @enderror"
                       value="{{ old('instituto', $certificado->instituto) }}">
                @error('instituto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- PDF Actual -->
        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Archivo PDF Actual</label>
                @if($certificado->pdfcerts)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-info">
                            <i class="fas fa-file-pdf me-1"></i> PDF cargado
                        </span>
                        @php
                            // Extraer solo el nombre del archivo para mostrar
                            $nombreArchivo = basename($certificado->pdfcerts);
                        @endphp
                        <a href="{{ Storage::url($certificado->pdfcerts) }}"
                           target="_blank"
                           class="text-decoration-none">
                            {{ $nombreArchivo }}
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                                onclick="document.getElementById('eliminar_pdf').value = '1';
                                         document.getElementById('nombre_pdf_actual').style.display = 'none';
                                         this.style.display = 'none';">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                    <input type="hidden" name="eliminar_pdf" id="eliminar_pdf" value="0">
                    <span id="nombre_pdf_actual">{{ $nombreArchivo }}</span>
                @else
                    <span class="text-muted">No hay archivo PDF cargado</span>
                @endif
            </div>
        </div>

        <!-- Nuevo PDF -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="pdfcerts" class="form-label">
                    {{ $certificado->pdfcerts ? 'Reemplazar archivo PDF' : 'Subir archivo PDF' }} (opcional)
                </label>
                <input type="file" name="pdfcerts" id="pdfcerts"
                       class="form-control @error('pdfcerts') is-invalid @enderror"
                       accept=".pdf">
                @error('pdfcerts')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">
                    Tamaño máximo: 10MB. Formato permitido: PDF
                    @if($certificado->pdfcerts)
                        <br>Dejar en blanco para mantener el archivo actual.
                    @endif
                </small>
            </div>
        </div>

        <!-- Información de vencimiento -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Información de Vencimiento</h6>
                    </div>
                    <div class="card-body">
                        <div id="vencimiento-info">
                            @if($certificado->categoria === 'quechua' && $certificado->fecha_vencimiento)
                                @php
                                    $hoy = now();
                                    $fechaVencimiento = \Carbon\Carbon::parse($certificado->fecha_vencimiento);
                                    $diasRestantes = $hoy->diffInDays($fechaVencimiento, false);
                                @endphp

                                <p>
                                    <strong>Categoría:</strong>
                                    <span class="badge bg-info">Quechua</span>
                                </p>
                                <p>
                                    <strong>Fecha de vencimiento actual:</strong>
                                    <span class="{{ $fechaVencimiento->isPast() ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                        {{ $fechaVencimiento->format('d/m/Y') }}
                                    </span>
                                </p>

                                @if($fechaVencimiento->isPast())
                                    <div class="alert alert-danger py-2">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Este certificado está VENCIDO desde hace {{ abs($diasRestantes) }} días
                                    </div>
                                @elseif($diasRestantes <= 30)
                                    <div class="alert alert-warning py-2">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        Este certificado VENCE en {{ $diasRestantes }} días
                                    </div>
                                @else
                                    <div class="alert alert-success py-2">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Este certificado es VIGENTE (vence en {{ $diasRestantes }} días)
                                    </div>
                                @endif

                                <div id="nuevo-vencimiento-container" class="mt-3" style="display: none;">
                                    <hr>
                                    <p class="mb-1"><strong>Nueva fecha de vencimiento calculada:</strong></p>
                                    <span id="nueva-fecha-vencimiento" class="text-primary fw-bold"></span>
                                    <small class="text-muted d-block">
                                        * La fecha se recalculará al guardar si cambia la categoría o fecha de emisión
                                    </small>
                                </div>
                            @elseif($certificado->categoria === 'quechua')
                                <p class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Certificado de Quechua sin fecha de emisión registrada
                                </p>
                            @else
                                <p class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Este tipo de certificado ({{ ucfirst(str_replace('_', ' ', $certificado->categoria)) }}) no tiene vencimiento
                                </p>
                                <div id="nuevo-vencimiento-container" class="mt-3" style="display: none;">
                                    <hr>
                                    <p class="mb-1"><strong>Nueva fecha de vencimiento calculada:</strong></p>
                                    <span id="nueva-fecha-vencimiento" class="text-primary fw-bold"></span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Actualizar Certificado
            </button>
            <a href="{{ route('certificados.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>

            <!-- Botón para ver el certificado actual -->
            @if($certificado->pdfcerts)
                <a href="{{ Storage::url($certificado->pdfcerts) }}"
                   target="_blank"
                   class="btn btn-info">
                    <i class="fas fa-eye me-1"></i> Ver Certificado Actual
                </a>
            @endif
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoriaSelect = document.getElementById('categoria');
    const fechaInput = document.getElementById('fecha');
    const nuevoVencimientoContainer = document.getElementById('nuevo-vencimiento-container');
    const nuevaFechaVencimientoSpan = document.getElementById('nueva-fecha-vencimiento');

    // Valores originales para comparar cambios
    const categoriaOriginal = '{{ $certificado->categoria }}';
    const fechaOriginal = '{{ $certificado->fecha ? $certificado->fecha->format("Y-m-d") : "" }}';

    function calcularNuevoVencimiento() {
        const categoria = categoriaSelect.value;
        const fecha = fechaInput.value;

        // Solo mostrar si hay cambios en categoría o fecha
        const hayCambios = (categoria !== categoriaOriginal) || (fecha !== fechaOriginal);

        if (hayCambios && categoria === 'quechua' && fecha) {
            const fechaObj = new Date(fecha);
            fechaObj.setFullYear(fechaObj.getFullYear() + 3);

            const dia = fechaObj.getDate().toString().padStart(2, '0');
            const mes = (fechaObj.getMonth() + 1).toString().padStart(2, '0');
            const anio = fechaObj.getFullYear();

            nuevaFechaVencimientoSpan.textContent = `${dia}/${mes}/${anio}`;
            nuevoVencimientoContainer.style.display = 'block';
        } else if (hayCambios && categoria && categoria !== 'quechua') {
            nuevaFechaVencimientoSpan.textContent = 'Sin vencimiento';
            nuevoVencimientoContainer.style.display = 'block';
        } else {
            nuevoVencimientoContainer.style.display = 'none';
        }
    }

    categoriaSelect.addEventListener('change', calcularNuevoVencimiento);
    fechaInput.addEventListener('change', calcularNuevoVencimiento);

    // Calcular al cargar si hay valores diferentes
    calcularNuevoVencimiento();

    // Manejo del botón eliminar PDF
    const btnEliminar = document.querySelector('button[onclick*="eliminar_pdf"]');
    if (btnEliminar) {
        btnEliminar.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('eliminar_pdf').value = '1';
            document.getElementById('nombre_pdf_actual').style.display = 'none';
            this.style.display = 'none';

            // Mostrar mensaje de confirmación
            const mensaje = document.createElement('div');
            mensaje.className = 'alert alert-warning alert-dismissible fade show mt-2';
            mensaje.innerHTML = `
                <i class="fas fa-info-circle me-1"></i>
                El archivo PDF será eliminado al guardar los cambios.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            this.parentNode.parentNode.appendChild(mensaje);
        });
    }
});
</script>

<style>
.form-label {
    font-weight: 500;
    margin-bottom: 0.3rem;
}

.card {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.card-header {
    padding: 0.75rem 1rem;
}

.card-body {
    padding: 1rem;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.alert {
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
}
</style>
@endsection
