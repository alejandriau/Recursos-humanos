@extends('dashboard')

@section('contenido')
<div class="container">
    <h4>Registrar nueva profesión para {{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</h4>
    <form action="{{ route('profesion.store', $persona->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.profesion.form')
    </form>
</div>
@endsection
