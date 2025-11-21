@extends('dashboard')

@section('title', 'Editar Caja de Cordes')

@section('contenido')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Editar Caja de Cordes #{{ $cajacorde->id }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('cajacordes.update', $cajacorde) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha *</label>
                        <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                               id="fecha" name="fecha" value="{{ old('fecha', $cajacorde->fecha->format('Y-m-d')) }}" required>
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
                                    {{ old('idPersona', $cajacorde->idPersona) == $persona->id ? 'selected' : '' }}>
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

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código</label>
                        <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                               id="codigo" name="codigo" value="{{ old('codigo', $cajacorde->codigo) }}" maxlength="45"
                               placeholder="Código de referencia">
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="otros" class="form-label">Otros</label>
                        <input type="text" class="form-control @error('otros') is-invalid @enderror"
                               id="otros" name="otros" value="{{ old('otros', $cajacorde->otros) }}" maxlength="45"
                               placeholder="Información adicional">
                        @error('otros')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="pdfcaja" class="form-label">Archivo PDF</label>
                <input type="file" class="form-control @error('pdfcaja') is-invalid @enderror"
                       id="pdfcaja" name="pdfcaja" accept=".pdf">
                @error('pdfcaja')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    @if($cajacorde->pdfcaja)
                        Archivo actual: {{ basename($cajacorde->pdfcaja) }}
                    @else
                        No hay archivo actual
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado *</label>
                <select class="form-select @error('estado') is-invalid @enderror"
                        id="estado" name="estado" required>
                    <option value="1" {{ old('estado', $cajacorde->estado) == 1 ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('estado', $cajacorde->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('estado')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('cajacordes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
