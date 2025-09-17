@extends('dashboard')

@section('title', 'Editar Licencia de Conducir')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Editar Licencia de Conducir #{{ $licencia->id }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('licencias-conducir.update', $licencia) }}" method="POST" enctype="multipart/form-data">
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
                        <label for="categoria" class="form-label">Categoría *</label>
                        <select class="form-select @error('categoria') is-invalid @enderror"
                                id="categoria" name="categoria" required>
                            <option value="">Seleccionar Categoría</option>
                            <option value="A" {{ old('categoria', $licencia->categoria) == 'A' ? 'selected' : '' }}>A - Motocicletas</option>
                            <option value="B" {{ old('categoria', $licencia->categoria) == 'B' ? 'selected' : '' }}>B - Vehículos particulares</option>
                            <option value="C" {{ old('categoria', $licencia->categoria) == 'C' ? 'selected' : '' }}>C - Vehículos de carga</option>
                            <option value="D" {{ old('categoria', $licencia->categoria) == 'D' ? 'selected' : '' }}>D - Transporte público</option>
                            <option value="E" {{ old('categoria', $licencia->categoria) == 'E' ? 'selected' : '' }}>E - Maquinaria pesada</option>
                        </select>
                        @error('categoria')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fechavencimiento" class="form-label">Fecha de Vencimiento *</label>
                        <input type="date" class="form-control @error('fechavencimiento') is-invalid @enderror"
                               id="fechavencimiento" name="fechavencimiento"
                               value="{{ old('fechavencimiento', $licencia->fechavencimiento->format('Y-m-d')) }}" required>
                        @error('fechavencimiento')
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

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                          id="descripcion" name="descripcion" rows="3" maxlength="500"
                          placeholder="Observaciones o restricciones de la licencia">{{ old('descripcion', $licencia->descripcion) }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pdflicc" class="form-label">Archivo PDF de la Licencia</label>
                <input type="file" class="form-control @error('pdflicc') is-invalid @enderror"
                       id="pdflicc" name="pdflicc" accept=".pdf">
                @error('pdflicc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    @if($licencia->pdflicc)
                        Archivo actual: {{ basename($licencia->pdflicc) }}
                    @else
                        No hay archivo actual
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('licencias-conducir.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
