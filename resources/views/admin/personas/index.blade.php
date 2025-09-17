@extends('dashboard')
@section('contenido')
<!-- Sale & Revenue Start -->
<div class="container-fluid pt-4 px-4">

    <div class="row g-4">
        <div class="col-sm-12">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('successdelete'))
                <div class="alert alert-success">
                    {{ session('successdelete') }}
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-sm-12">
            <a href="<?php echo asset(''); ?>ventas/crearproducto">
                <button type="submit" class="btn btn-success">Registrar perosna</button>
            </a>
        </div>
    </div>

    <div class="">
        <h1 class="mb-4">Lista de Personas</h1>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">CI</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Apellido Paterno</th>
                        <th scope="col">Apellido Materno</th>
                        <th scope="col">Sexo</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Fecha Nacimiento</th>
                        <th scope="col">Fecha Ingreso</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($personas as $persona)
                        <tr>
                            <td>{{ $persona->id }}</td>
                            <td>{{ $persona->ci }}</td>
                            <td>{{ $persona->nombre }}</td>
                            <td>{{ $persona->apellidoPat }}</td>
                            <td>{{ $persona->apellidoMat }}</td>
                            <td>{{ $persona->sexo }}</td>
                            <td>{{ $persona->telefono }}</td>
                            <td>{{ $persona->fechaNacimiento }}</td>
                            <td>{{ $persona->fechaIngreso }}</td>
                            <td>{{ $persona->estado ? 'Activo' : 'Inactivo' }}</td>
                            <td>
                                <!-- Botón Eliminar -->
                                <form action="" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?')">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
<!-- Sale & Revenue End -->
@endsection
