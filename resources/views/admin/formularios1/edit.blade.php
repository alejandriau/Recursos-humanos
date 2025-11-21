@extends('dashboard')

@section('title', 'Editar Formulario 1')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Editar Formulario 1 #{{ $formulario1->id }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('formularios1.update', $formulario1) }}" method="POST" enctype="multipart/form-data">
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
                                    {{ old('idPersona', $formulario1->idPersona) == $persona->id ? 'selected' : '' }}>
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
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                               id="fecha" name="fecha" value="{{ old('fecha', $formulario1->fecha ? $formulario1->fecha->format('Y-m-d') : '') }}">
                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="observacion" class="form-label">Observación</label>
                <input type="text" class="form-control @error('observacion') is-invalid @enderror"
                       id="observacion" name="observacion" value="{{ old('observacion', $formulario1->observacion) }}"
                       maxlength="45" placeholder="Observaciones sobre el formulario">
                @error('observacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pdfform1" class="form-label">Archivo PDF</label>
                <input type="file" class="form-control @error('pdfform1') is-invalid @enderror"
                       id="pdfform1" name="pdfform1" accept=".pdf">
                <div class="form-text">
                    Formatos permitidos: PDF. Tamaño máximo: 2MB
                    @if($formulario1->pdfform1)
                        <br>Archivo actual:
                        <a href="{{ route('formularios1.download', $formulario1) }}" target="_blank">
                            {{ $formulario1->pdfform1 }}
                        </a>
                    @endif
                </div>
                @error('pdfform1')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado *</label>
                <select class="form-select @error('estado') is-invalid @enderror"
                        id="estado" name="estado" required>
                    <option value="1" {{ old('estado', $formulario1->estado) == 1 ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('estado', $formulario1->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('estado')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('formularios1.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
