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

    <h2 class="mb-4">Nuevo Certificado</h2>

    <form action="{{ route('certificados.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Persona -->
            <div class="col-md-6 mb-3">
                <label for="idPersona" class="form-label">Persona *</label>
                <select name="idPersona" id="idPersona" class="form-select @error('idPersona') is-invalid @enderror" required>
                    <option value="">Seleccionar persona</option>
                    @foreach($personas as $persona)
                        <option value="{{ $persona->id }}"
                            {{ old('idPersona', $idPersona ?? '') == $persona->id ? 'selected' : '' }}>
                            {{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}
                        </option>
                    @endforeach
                </select>
                @error('idPersona')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Categoría -->
            <div class="col-md-6 mb-3">
                <label for="categoria" class="form-label">Categoría *</label>
                <select name="categoria" id="categoria" class="form-select @error('categoria') is-invalid @enderror" required>
                    <option value="">Seleccionar categoría</option>
                    <option value="quechua" {{ old('categoria') == 'quechua' ? 'selected' : '' }}>
                        Quechua (vencen cada 3 años)
                    </option>
                    <option value="ley_1178" {{ old('categoria') == 'ley_1178' ? 'selected' : '' }}>
                        Ley 1178 (sin vencimiento)
                    </option>
                    <option value="politicas_publicas" {{ old('categoria') == 'politicas_publicas' ? 'selected' : '' }}>
                        Políticas Públicas (sin vencimiento)
                    </option>
                    <option value="responsabilidad_funcion_publica" {{ old('categoria') == 'responsabilidad_funcion_publica' ? 'selected' : '' }}>
                        Responsabilidad por la Función Pública (sin vencimiento)
                    </option>
                    <option value="otros" {{ old('categoria') == 'otros' ? 'selected' : '' }}>
                        Otros (sin vencimiento)
                    </option>
                </select>
                @error('categoria')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">
                    Nota: Solo los certificados de Quechua tienen vencimiento (3 años)
                </small>
            </div>
        </div>

        <div class="row">
            <!-- Nombre del Certificado -->
            <div class="col-md-12 mb-3">
                <label for="nombre" class="form-label">Nombre del Certificado *</label>
                <input type="text" name="nombre" id="nombre"
                       class="form-control @error('nombre') is-invalid @enderror"
                       value="{{ old('nombre') }}"
                       placeholder="Ej: Certificado de Quechua Básico" required>
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
                       value="{{ old('tipo') }}"
                       placeholder="Ej: Básico, Intermedio, Avanzado">
                @error('tipo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Fecha de Emisión -->
            <div class="col-md-4 mb-3">
                <label for="fecha" class="form-label">Fecha de Emisión *</label>
                <input type="date" name="fecha" id="fecha"
                       class="form-control @error('fecha') is-invalid @enderror"
                       value="{{ old('fecha') }}" required>
                @error('fecha')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Instituto -->
            <div class="col-md-4 mb-3">
                <label for="instituto" class="form-label">Institución</label>
                <input type="text" name="instituto" id="instituto"
                       class="form-control @error('instituto') is-invalid @enderror"
                       value="{{ old('instituto') }}"
                       placeholder="Ej: Instituto de Idiomas">
                @error('instituto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- PDF -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="pdfcerts" class="form-label">Archivo PDF (opcional)</label>
                <input type="file" name="pdfcerts" id="pdfcerts"
                       class="form-control @error('pdfcerts') is-invalid @enderror"
                       accept=".pdf">
                @error('pdfcerts')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">
                    Tamaño máximo: 2MB. Formato permitido: PDF
                </small>
            </div>
        </div>

        <!-- Información de vencimiento (solo lectura) -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Información de Vencimiento</h6>
                    </div>
                    <div class="card-body">
                        <div id="vencimiento-info">
                            <p class="mb-0" id="texto-vencimiento">
                                Seleccione una categoría para ver información de vencimiento
                            </p>
                            <div id="fecha-vencimiento-container" class="mt-2" style="display: none;">
                                <strong>Fecha de vencimiento calculada:</strong>
                                <span id="fecha-vencimiento" class="text-success fw-bold"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Guardar Certificado
            </button>
            <a href="{{ route('certificados.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoriaSelect = document.getElementById('categoria');
    const fechaInput = document.getElementById('fecha');
    const vencimientoInfo = document.getElementById('texto-vencimiento');
    const fechaVencimientoContainer = document.getElementById('fecha-vencimiento-container');
    const fechaVencimientoSpan = document.getElementById('fecha-vencimiento');

    function calcularFechaVencimiento() {
        const categoria = categoriaSelect.value;
        const fecha = fechaInput.value;

        if (categoria === 'quechua' && fecha) {
            const fechaObj = new Date(fecha);
            fechaObj.setFullYear(fechaObj.getFullYear() + 3);

            const dia = fechaObj.getDate().toString().padStart(2, '0');
            const mes = (fechaObj.getMonth() + 1).toString().padStart(2, '0');
            const anio = fechaObj.getFullYear();

            vencimientoInfo.textContent = 'Este certificado de Quechua vencerá en 3 años.';
            fechaVencimientoSpan.textContent = `${dia}/${mes}/${anio}`;
            fechaVencimientoContainer.style.display = 'block';
        } else if (categoria === 'quechua' && !fecha) {
            vencimientoInfo.textContent = 'Seleccione una fecha de emisión para calcular el vencimiento.';
            fechaVencimientoContainer.style.display = 'none';
        } else if (categoria && categoria !== 'quechua') {
            vencimientoInfo.textContent = 'Este tipo de certificado no tiene vencimiento.';
            fechaVencimientoContainer.style.display = 'none';
        } else {
            vencimientoInfo.textContent = 'Seleccione una categoría para ver información de vencimiento.';
            fechaVencimientoContainer.style.display = 'none';
        }
    }

    categoriaSelect.addEventListener('change', calcularFechaVencimiento);
    fechaInput.addEventListener('change', calcularFechaVencimiento);

    // Calcular al cargar la página si hay valores predefinidos
    if (categoriaSelect.value || fechaInput.value) {
        calcularFechaVencimiento();
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
</style>
@endsection
