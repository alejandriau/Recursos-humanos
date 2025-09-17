@extends('dashboard')

@section('contenido')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Editar CAS</h2>
    <form action="{{ route('cas.update', $cas) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.cas.form')
    </form>
</div>
@endsection
