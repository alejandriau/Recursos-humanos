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
    <h2 class="mb-4">Nuevo Certificado</h2>

    <form action="{{ route('certificados.store') }}" method="POST">
        
        @csrf

        @include('admin.certificados.form')

        <button type="submit" class="btn btn-success mt-3">Guardar</button>
        <a href="{{ route('certificados.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
@endsection
