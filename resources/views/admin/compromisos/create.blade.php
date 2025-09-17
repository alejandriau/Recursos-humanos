@extends('dashboard')

@section('title', 'Crear Compromiso')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Crear Nuevo Compromiso</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('compromisos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="idPersona" class="form-label">Persona *</label>
                        <select class="form-select @error('idPersona') is-invalid @enderror"
                                id="idPersona" name="idPersona" required>
                            <option value="">Seleccionar Persona</option>
                            @foreach($personas as $persona)
                                <option value="{{ $persona->id }}" {{ old('idPersona') == $persona->id ? 'selected' : '' }}>
                                    {{ $persona->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('idPersona')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Compromiso 1 -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Compromiso 1</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="compromiso1" class="form-label">Descripción</label>
                        <input type="text" class="form-control @error('compromiso1') is-invalid @enderror"
                               id="compromiso1" name="compromiso1" value="{{ old('compromiso1') }}" maxlength="45"
                               placeholder="Descripción del compromiso 1">
                        @error('compromiso1')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="pdfcomp1" class="form-label">Archivo PDF</label>
                        <input type="file" class="form-control @error('pdfcomp1') is-invalid @enderror"
                               id="pdfcomp1" name="pdfcomp1" accept=".pdf">
                        @error('pdfcomp1')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Compromiso 2 -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Compromiso 2</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="compromiso2" class="form-label">Descripción</label>
                        <input type="text" class="form-control @error('compromiso2') is-invalid @enderror"
                               id="compromiso2" name="compromiso2" value="{{ old('compromiso2') }}" maxlength="45"
                               placeholder="Descripción del compromiso 2">
                        @error('compromiso2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="pdfcomp2" class="form-label">Archivo PDF</label>
                        <input type="file" class="form-control @error('pdfcomp2') is-invalid @enderror"
                               id="pdfcomp2" name="pdfcomp2" accept=".pdf">
                        @error('pdfcomp2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Compromiso 3 -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Compromiso 3</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="compromiso3" class="form-label">Descripción</label>
                        <input type="text" class="form-control @error('compromiso3') is-invalid @enderror"
                               id="compromiso3" name="compromiso3" value="{{ old('compromiso3') }}" maxlength="45"
                               placeholder="Descripción del compromiso 3">
                        @error('compromiso3')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="pdfcomp3" class="form-label">Archivo PDF</label>
                        <input type="file" class="form-control @error('pdfcomp3') is-invalid @enderror"
                               id="pdfcomp3" name="pdfcomp3" accept=".pdf">
                        @error('pdfcomp3')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-text mb-3">* Campos opcionales. Puede completar desde 1 hasta 3 compromisos.</div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('compromisos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
