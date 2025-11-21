@extends('dashboard')

@section('title', 'Crear Curriculum')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Crear Nuevo Curriculum</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('curriculums.store') }}" method="POST" enctype="multipart/form-data">
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
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                          id="descripcion" name="descripcion" rows="3" maxlength="200"
                          placeholder="Descripción principal del curriculum">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mas" class="form-label">Información Adicional ("Mas")</label>
                        <input type="text" class="form-control @error('mas') is-invalid @enderror"
                               id="mas" name="mas" value="{{ old('mas') }}" maxlength="200"
                               placeholder="Información adicional importante">
                        @error('mas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="otros" class="form-label">Otros Datos</label>
                        <input type="text" class="form-control @error('otros') is-invalid @enderror"
                               id="otros" name="otros" value="{{ old('otros') }}" maxlength="200"
                               placeholder="Otros datos relevantes">
                        @error('otros')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="pdfcorri" class="form-label">Archivo PDF del Curriculum</label>
                <input type="file" class="form-control @error('pdfcorri') is-invalid @enderror"
                       id="pdfcorri" name="pdfcorri" accept=".pdf">
                @error('pdfcorri')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Máximo 2MB, solo archivos PDF</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('curriculums.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
