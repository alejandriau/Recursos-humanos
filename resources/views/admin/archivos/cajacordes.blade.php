@extends('dashboard')

@section('contenido')
    <div class="container">
        <h2>{{ isset($caja) ? 'Editar Caja Corde' : 'Registrar Caja Corde' }}</h2>

        <form action="{{ isset($caja) ? route('cajacordes.update', $caja->id) : route('cajacordes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($caja))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="{{ old('fecha', $caja->fecha ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="codigo" class="form-label">Código</label>
                <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $caja->codigo ?? '') }}">
            </div>

            <div class="mb-3">
                <label for="otros" class="form-label">Otros</label>
                <input type="text" name="otros" class="form-control" value="{{ old('otros', $caja->otros ?? '') }}">
            </div>

            <div class="mb-3">
                <label for="pdfcaja" class="form-label">Archivo PDF</label>
                <input type="file" name="pdfcaja" class="form-control">
                @if(isset($caja) && $caja->pdfcaja)
                    <small><a href="{{ asset('storage/' . $caja->pdfcaja) }}" target="_blank">Ver PDF actual</a></small>
                @endif
            </div>

            <div class="mb-3">
                <label for="idPersona" class="form-label">Persona</label>
                <select name="idPersona" class="form-control" required>
                    <option value="">Seleccione una persona</option>
                    @foreach($personas as $persona)
                        <option value="{{ $persona->id }}" {{ old('idPersona', $caja->idPersona ?? '') == $persona->id ? 'selected' : '' }}>
                            {{ $persona->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">
                {{ isset($caja) ? 'Actualizar' : 'Registrar' }}
            </button>
            <a href="{{ route('cajacordes.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection
