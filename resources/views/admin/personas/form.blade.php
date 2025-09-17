@extends('dashboard')

@section('contenido')
    <div class="container mt-4">
        <h2>{{ $persona->exists ? 'Editar Persona' : 'Registrar Persona' }}</h2>

<form method="POST"
    action="{{ $persona->exists ? route('personas.update', $persona->id) : route('personas.store') }}"
    enctype="multipart/form-data">
    @csrf
    @if($persona->exists)
        @method('PUT')
    @endif

    {{-- Mostrar errores generales arriba (opcional) --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-2">
            <div class="mb-3">
                <label for="ci" class="form-label">Carnet de Identidad</label>
                <input type="text" class="form-control @error('ci') is-invalid @enderror" id="ci" name="ci"
                    value="{{ old('ci', $persona->ci) }}" required>
                @error('ci')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre"
                    value="{{ old('nombre', $persona->nombre) }}" required>
                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="apellidoPat" class="form-label">Apellido Paterno</label>
                <input type="text" class="form-control @error('apellidoPat') is-invalid @enderror" id="apellidoPat"
                    name="apellidoPat" value="{{ old('apellidoPat', $persona->apellidoPat) }}">
                @error('apellidoPat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="apellidoMat" class="form-label">Apellido Materno</label>
                <input type="text" class="form-control @error('apellidoMat') is-invalid @enderror" id="apellidoMat"
                    name="apellidoMat" value="{{ old('apellidoMat', $persona->apellidoMat) }}">
                @error('apellidoMat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="mb-3">
                <label for="fechaIngreso" class="form-label">Fecha de Ingreso</label>
                <input type="date" class="form-control @error('fechaIngreso') is-invalid @enderror" id="fechaIngreso"
                    name="fechaIngreso" value="{{ old('fechaIngreso', optional($persona->fechaIngreso)->format('Y-m-d')) }}">
                @error('fechaIngreso')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control @error('fechaNacimiento') is-invalid @enderror"
                    id="fechaNacimiento" name="fechaNacimiento"
                    value="{{ old('fechaNacimiento', optional($persona->fechaNacimiento)->format('Y-m-d')) }}">
                @error('fechaNacimiento')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="sexo" class="form-label">Sexo</label>
                <select class="form-select @error('sexo') is-invalid @enderror" id="sexo" name="sexo" required>
                    <option value="">Seleccione</option>
                    <option value="M" {{ old('sexo', $persona->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('sexo', $persona->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
                @error('sexo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono"
                    name="telefono" value="{{ old('telefono', $persona->telefono) }}">
                @error('telefono')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label for="observaciones" class="form-label">Observaciones</label>
        <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones"
            rows="3">{{ old('observaciones', $persona->observaciones) }}</textarea>
        @error('observaciones')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo</label>
                <input type="text" class="form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo"
                    value="{{ old('tipo', $persona->tipo) }}">
                @error('tipo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <small class="text-muted">JPG, PNG, máximo 2MB</small>
            <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
            @error('foto')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-4">
            <div class="mb-3 text-center">

                @if($persona->foto)
                    <div class="mb-2">
                        <img id="preview-foto" src="{{ route('persona.foto', $persona->id) }}"
                            alt="Foto de {{ $persona->nombre }}" class="rounded-circle shadow-sm cursor-pointer"
                            data-bs-toggle="modal" data-bs-target="#modalFoto{{ $persona->id }}"
                            style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                @else
                    <div class="mb-2">
                        <img id="preview-foto" src="{{ asset('images/avatar-default.png') }}" alt="Sin foto"
                            class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                @endif
                <label for="foto" class="form-label">Foto de perfil</label>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">{{ $persona->exists ? 'Actualizar' : 'Registrar' }}</button>
    <a href="{{ route('reportes.index') }}" class="btn btn-secondary">Cancelar</a>
</form>

    </div>

    <script>
        document.getElementById('foto').addEventListener('change', function (event) {
            const input = event.target;
            const preview = document.getElementById('preview-foto');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        });

    </script>

@endsection