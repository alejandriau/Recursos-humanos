@extends('dashboard')

@section('title', 'Crear Formulario 1')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Crear Nuevo Formulario 1</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('formularios1.store') }}" method="POST" enctype="multipart/form-data">
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

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                               id="fecha" name="fecha" value="{{ old('fecha') }}">
                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="observacion" class="form-label">Observación</label>
                <input type="text" class="form-control @error('observacion') is-invalid @enderror"
                       id="observacion" name="observacion" value="{{ old('observacion') }}"
                       maxlength="45" placeholder="Observaciones sobre el formulario">
                @error('observacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pdfform1" class="form-label">Archivo PDF</label>
                <input type="file" class="form-control @error('pdfform1') is-invalid @enderror"
                       id="pdfform1" name="pdfform1" accept=".pdf">
                <div class="form-text">Formatos permitidos: PDF. Tamaño máximo: 2MB</div>
                @error('pdfform1')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('formularios1.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
