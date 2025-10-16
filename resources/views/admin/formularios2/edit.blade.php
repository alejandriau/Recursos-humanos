@extends('dashboard')

@section('title', 'Editar Formulario 2')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Editar Formulario 2 #{{ $formulario2->id }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('formularios2.update', $formulario2) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="idPersona" class="form-label">Persona *</label>
                        <select class="form-select @error('idPersona') is-invalid @enderror"
                                id="idPersona" name="idPersona" required>
                            <option value="">Seleccionar Persona</option>
                            @foreach($personas as $persona)
                                <option value="{{ $persona->id }}"
                                    {{ old('idPersona', $formulario2->idPersona) == $persona->id ? 'selected' : '' }}>
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
                               id="fecha" name='fecha' value="{{ old('fecha', $formulario2->fecha ? $formulario2->fecha->format('Y-m-d') : '') }}">
                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="observacion" class="form-label">Observación</label>
                <textarea class="form-control @error('observacion') is-invalid @enderror"
                          id="observacion" name="observacion" rows="3" maxlength="300"
                          placeholder="Observaciones sobre el formulario">{{ old('observacion', $formulario2->observacion) }}</textarea>
                @error('observacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pdfform2" class="form-label">Archivo PDF</label>
                <input type="file" class="form-control @error('pdfform2') is-invalid @enderror"
                       id="pdfform2" name="pdfform2" accept=".pdf">
                <div class="form-text">
                    Formatos permitidos: PDF. Tamaño máximo: 2MB
                    @if($formulario2->pdfform2)
                        <br>Archivo actual:
                        <a href="{{ route('formularios2.download', $formulario2) }}" target="_blank">
                            {{ $formulario2->pdfform2 }}
                        </a>
                    @endif
                </div>
                @error('pdfform2')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado *</label>
                <select class="form-select @error('estado') is-invalid @enderror"
                        id="estado" name="estado" required>
                    <option value="1" {{ old('estado', $formulario2->estado) == 1 ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('estado', $formulario2->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('estado')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('formularios2.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
