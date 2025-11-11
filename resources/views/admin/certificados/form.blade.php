

    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre del Certificado</label>
        <input type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre', $certificado->nombre ?? '') }}" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="tipo" class="form-label">Tipo</label>
        <input type="text" class="form-control @error('tipo') is-invalid @enderror" name="tipo" value="{{ old('tipo', $certificado->tipo ?? '') }}">
        @error('tipo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="fecha" class="form-label">Fecha</label>
        <input type="date" class="form-control @error('fecha') is-invalid @enderror" name="fecha" value="{{ old('fecha', isset($certificado) ? \Carbon\Carbon::parse($certificado->fecha)->format('Y-m-d') : '') }}">
        @error('fecha')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="instituto" class="form-label">Instituto</label>
        <input type="text" class="form-control @error('instituto') is-invalid @enderror" name="instituto" value="{{ old('instituto', $certificado->instituto ?? '') }}">
        @error('instituto')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="pdfcerts" class="form-label">Archivo PDF</label>
        <input type="file" class="form-control @error('pdfcerts') is-invalid @enderror" name="pdfcerts" id="pdfcerts" accept="application/pdf">
        @error('pdfcerts')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        {{-- Mostrar el icono del PDF si ya hay un archivo --}}
        @if(!empty($certificado->pdfcerts))
            <div class="mt-2">
                <a href="{{ asset('ruta/a/pdfs/' . $certificado->pdfcerts) }}" target="_blank" class="d-inline-flex align-items-center">
                    <img src="{{ asset('images/pdf-icon.png') }}" alt="PDF" width="24" height="24" class="me-2">
                    <span>Ver archivo PDF</span>
                </a>
            </div>
        @endif
    </div>


@if(isset($personas) && $personas->count() > 0)
    <div class="mb-3">
        <label for="idPersona" class="form-label fw-semibold">Persona</label>
        <select id="idPersona" name="idPersona" class="form-select shadow-sm rounded-3 @error('idPersona') is-invalid @enderror" required>
            <option value="">-- Seleccione --</option>
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
@else
    <div class="mb-3">
        <label class="form-label fw-semibold">Persona</label>
        <input type="text" class="form-control shadow-sm rounded-3 bg-light" readonly
               value="{{ $certificado->persona->nombre }} {{ $certificado->persona->apellidoPat }} {{ $certificado->persona->apellidoMat }}">
        <input type="hidden" name="idPersona" value="{{ $certificado->persona->id }}">
    </div>
@endif
