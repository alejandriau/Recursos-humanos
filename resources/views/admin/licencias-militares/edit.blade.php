@extends('dashboard')

@section('title', 'Editar Licencia Militar')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Editar Licencia Militar #{{ $licencia->id }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('licencias-militares.update', $licencia) }}" method="POST" enctype="multipart/form-data">
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
                                    {{ old('idPersona', $licencia->idPersona) == $persona->id ? 'selected' : '' }}>
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
                        <label for="estado" class="form-label">Estado *</label>
                        <select class="form-select @error('estado') is-invalid @enderror"
                                id="estado" name="estado" required>
                            <option value="1" {{ old('estado', $licencia->estado) == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('estado', $licencia->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código</label>
                        <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                               id="codigo" name="codigo" value="{{ old('codigo', $licencia->codigo) }}" maxlength="45"
                               placeholder="Código de la licencia">
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="serie" class="form-label">Serie</label>
                        <input type="text" class="form-control @error('serie') is-invalid @enderror"
                               id="serie" name="serie" value="{{ old('serie', $licencia->serie) }}" maxlength="45"
                               placeholder="Serie de la licencia">
                        @error('serie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                               id="fecha" name="fecha" value="{{ old('fecha', $licencia->fecha ? $licencia->fecha->format('Y-m-d') : '') }}">
                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                          id="descripcion" name="descripcion" rows="3" maxlength="500"
                          placeholder="Descripción de la licencia militar">{{ old('descripcion', $licencia->descripcion) }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pdflic" class="form-label">Archivo PDF de la Licencia</label>
                <input type="file" class="form-control @error('pdflic') is-invalid @enderror"
                       id="pdflic" name="pdflic" accept=".pdf">
                @error('pdflic')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    @if($licencia->pdflic)
                        Archivo actual: {{ basename($licencia->pdflic) }}
                    @else
                        No hay archivo actual
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('licencias-militares.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
