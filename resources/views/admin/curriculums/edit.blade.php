@extends('dashboard')

@section('title', 'Editar Curriculum')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Editar Curriculum #{{ $curriculum->id }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('curriculums.update', $curriculum) }}" method="POST" enctype="multipart/form-data">
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
                                    {{ old('idPersona', $curriculum->idPersona) == $persona->id ? 'selected' : '' }}>
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
                            <option value="1" {{ old('estado', $curriculum->estado) == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('estado', $curriculum->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                          id="descripcion" name="descripcion" rows="3" maxlength="200"
                          placeholder="Descripción principal del curriculum">{{ old('descripcion', $curriculum->descripcion) }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mas" class="form-label">Información Adicional ("Mas")</label>
                        <input type="text" class="form-control @error('mas') is-invalid @enderror"
                               id="mas" name="mas" value="{{ old('mas', $curriculum->mas) }}" maxlength="200"
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
                               id="otros" name="otros" value="{{ old('otros', $curriculum->otros) }}" maxlength="200"
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
                <div class="form-text">
                    @if($curriculum->pdfcorri)
                        Archivo actual: {{ basename($curriculum->pdfcorri) }}
                    @else
                        No hay archivo actual
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('curriculums.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
