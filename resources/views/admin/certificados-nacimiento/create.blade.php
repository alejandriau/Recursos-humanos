@extends('dashboard')

@section('title', 'Crear Certificado de Nacimiento')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Crear Nuevo Certificado de Nacimiento</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('certificados-nacimiento.store') }}" method="POST" enctype="multipart/form-data">
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
                                    {{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}
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
                        <label for="fecha" class="form-label">Fecha del Certificado *</label>
                        <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                               id="fecha" name="fecha" value="{{ old('fecha') }}" required>
                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                          id="descripcion" name="descripcion" rows="3" maxlength="250"
                          placeholder="Descripción del certificado de nacimiento">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pdfcern" class="form-label">Archivo PDF del Certificado</label>
                <input type="file" class="form-control @error('pdfcern') is-invalid @enderror"
                       id="pdfcern" name="pdfcern" accept=".pdf">
                @error('pdfcern')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Máximo 2MB, solo archivos PDF</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('certificados-nacimiento.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
