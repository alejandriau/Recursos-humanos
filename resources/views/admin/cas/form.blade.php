@csrf

<div class="row g-4">
    <div class="col-md-4">
        <label for="anios" class="form-label fw-semibold">Años</label>
        <input type="number" id="anios" name="anios" class="form-control shadow-sm rounded-3 @error('anios') is-invalid @enderror" value="{{ old('anios', $cas->anios ?? '') }}">
        @error('anios')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="meses" class="form-label fw-semibold">Meses</label>
        <input type="number" id="meses" name="meses" class="form-control shadow-sm rounded-3 @error('meses') is-invalid @enderror" value="{{ old('meses', $cas->meses ?? '') }}">
        @error('meses')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="dias" class="form-label fw-semibold">Días</label>
        <input type="number" id="dias" name="dias" class="form-control shadow-sm rounded-3 @error('dias') is-invalid @enderror" value="{{ old('dias', $cas->dias ?? '') }}">
        @error('dias')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="fechaEmision" class="form-label fw-semibold">Fecha de Emisión</label>
        <input type="date" id="fechaEmision" name="fechaEmision" class="form-control shadow-sm rounded-3 @error('fechaEmision') is-invalid @enderror" value="{{ old('fechaEmision', $cas->fechaEmision ?? '') }}">
        @error('fechaEmision')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="fechaTiempo" class="form-label fw-semibold">Fecha de Tiempo</label>
        <input type="date" id="fechaTiempo" name="fechaTiempo" class="form-control shadow-sm rounded-3 @error('fechaTiempo') is-invalid @enderror" value="{{ old('fechaTiempo', $cas->fechaTiempo ?? '') }}">
        @error('fechaTiempo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="idPersona" class="form-label fw-semibold">Persona</label>
        <select id="idPersona" name="idPersona" class="form-select shadow-sm rounded-3 @error('idPersona') is-invalid @enderror">
            <option value="" disabled @if(old('idPersona', $cas->idPersona ?? '') == '') selected @endif>-- Seleccione --</option>
            @foreach($personas as $p)
                <option value="{{ $p->id }}" @selected(old('idPersona', $cas->idPersona ?? '') == $p->id)>
                    {{ $p->apellidoPat.' '. $p->apellidoMat.' '. $p->nombre }}
                </option>
            @endforeach
        </select>
        @error('idPersona')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="pdfcas" class="form-label fw-semibold">Archivo PDF</label>
        <input type="file" id="pdfcas" name="pdfcas" class="form-control shadow-sm rounded-3 @error('pdfcas') is-invalid @enderror" accept="application/pdf">
        @error('pdfcas')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if(!empty($cas->pdfcas))
            <a href="{{ asset('storage/' . $cas->pdfcas) }}" target="_blank" class="d-block mt-2 small text-decoration-underline text-primary">📎 Ver archivo actual</a>
        @endif
    </div>
</div>

<div class="mt-4 d-flex justify-content-start align-items-center gap-3">
    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">💾 Guardar</button>
    <a href="{{ route('cas.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">Cancelar</a>
</div>
