@extends('dashboard')

@section('title', 'Editar CENVI')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Editar CENVI #{{ $cenvi->id }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('cenvis.update', $cenvi) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha *</label>
                        <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                               id="fecha" name="fecha" value="{{ old('fecha', $cenvi->fecha->format('Y-m-d')) }}" required>
                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="idPersona" class="form-label">Persona *</label>
                        <select class="form-select @error('idPersona') is-invalid @enderror"
                                id="idPersona" name="idPersona" required>
                            <option value="">Seleccionar Persona</option>
                            @foreach($personas as $persona)
                                <option value="{{ $persona->id }}"
                                    {{ old('idPersona', $cenvi->idPersona) == $persona->id ? 'selected' : '' }}>
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
                <label for="observacion" class="form-label">Observación</label>
                <textarea class="form-control @error('observacion') is-invalid @enderror"
                          id="observacion" name="observacion" rows="3" maxlength="100">{{ old('observacion', $cenvi->observacion) }}</textarea>
                @error('observacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pdfcenvi" class="form-label">Archivo PDF</label>
                <input type="file" class="form-control @error('pdfcenvi') is-invalid @enderror"
                       id="pdfcenvi" name="pdfcenvi" accept=".pdf">
                @error('pdfcenvi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    @if($cenvi->pdfcenvi)
                        Archivo actual: {{ basename($cenvi->pdfcenvi) }}
                    @else
                        No hay archivo actual
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado *</label>
                <select class="form-select @error('estado') is-invalid @enderror"
                        id="estado" name="estado" required>
                    <option value="1" {{ old('estado', $cenvi->estado) == 1 ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('estado', $cenvi->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('estado')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('cenvis.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
