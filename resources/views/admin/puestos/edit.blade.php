@extends('dashboard')

@section('contenido')
<div class="container">
    <h2>Editar Puesto</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('puesto.update', $puesto->id) }}" method="POST">
        @method('PUT')
        @include('admin.puestos.form')
    </form>
</div>

@endsection
