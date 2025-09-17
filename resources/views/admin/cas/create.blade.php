@extends('dashboard')

@section('contenido')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Registrar nuevo CAS</h2>
    <form action="{{ route('cas.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.cas.form')
    </form>
</div>
@endsection
