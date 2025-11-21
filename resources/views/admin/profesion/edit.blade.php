@extends('dashboard')

@section('contenido')
<div class="container">
    <h4>Editar profesión de {{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</h4>
    <form action="{{ route('profesion.update', [$persona->id, $profesion->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.profesion.form')
    </form>
</div>
@endsection
