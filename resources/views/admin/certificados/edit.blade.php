@extends('dashboard')

@section('contenido')
<div class="container">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <h2 class="mb-4">Editar Certificado</h2>

    <form action="{{ route('certificados.update', $certificado->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('admin.certificados.form')

        <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
        <a href="{{ route('certificados.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
