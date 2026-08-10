@extends('layouts.baseadm')

@section('title', 'vacaciones')
@section('plugins.Sweetalert2', true)
@section('content_header')
    <div class="alert alert-secondary" role="alert">
        <div class="row justify-content-start">
            <div class="col-9">
                <b>GESTION</b>
            </div>
            <div class="col-3 text-primary d-flex justify-content-end">
                <i class="fa-solid fa-user fs-4"></i>&nbsp;{{ auth()->user()->name }}
            </div>
        </div>
    </div>
@stop
@section('contenido')
    <?php $var = 0; ?>
    @foreach ($gestion1 as $ges1)
        @if ($ges1->estado == 'Habilitado')
            <?php $var = 1; ?>
        @endif
    @endforeach
    <?php if ($var == 0) {
        echo "<a href='/apertura-gestion/gestion/create' class='btn btn-primary w-25'><i class='fa-solid fa-plus'></i>
                                Crear Gestion</a>";
    } else {
        echo "<a href='/apertura-gestion/gestion/create' class='btn btn-primary w-25 disabled'><i class='fa-solid fa-plus'></i>
                            Crear Gestion</a>";
    }
    ?>

    <div class="container-fluid pb-1 mt-2 pt-2 rounded shadow bg-white ">
        <h5 style="text-align: center"><span class=" text-secondary"> Lista de registros - Gestion </span></h5>
        <div class="row  justify-content-center">

            <table class="table table-hover w-50">
                <thead>
                    <tr>
                        <th scope="col" class="col-md-0">ID</th>
                        <th scope="col" class="col-md-6">Gestion</th>
                        <th scope="col" class="col-md-2">Fecha</th>
                        <th scope="col" class="col-md-2">Estado</th>
                        <th scope="col" class="col-md-1">Modificar</th>
                        <th scope="col" class="col-md-1">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gestion as $ges)
                        @if ($ges->estado == 'Habilitado')
                            <tr>
                                <th scope="row">{{ $ges->id }}</th>
                                <td>{{ $ges->anio }}</td>
                                <td>{{ $ges->fecha }}</td>
                                <td>{{ $ges->estado }}</td>
                                <td>
                                    <a href="/apertura-gestion/gestion/{{ $ges->id }}/edit" class="btn btn-warning mb-3 ms-3 "
                                        title="Modificar gestion"><i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </td>
                                <td>
                                    <form action="/apertura-gestion/gestion/{{ $ges->id }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button  onclick="return confirm('¿Esta seguro de eliminar la Gestion?')" type="submit" class="btn btn-danger mb-3 ms-3" title="Eliminar gestion">
                                            <i class='fa-solid fa-trash'></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <th scope="row">{{ $ges->id }}</th>
                                <td>{{ $ges->anio }}</td>
                                <td>{{ $ges->fecha }}</td>
                                <td>{{ $ges->estado }}</td>
                                <td>

                                </td>
                                <td>

                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

        </div>
        {{ $gestion->links() }}
    </div>

@stop

@section('css')
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
@stop

@section('js')
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome.js') }}"></script>
    <script src="{{ asset('js/funcionesJs.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    @if (session('status'))
        <script>
            Swal.fire({
                text: "Nueva Gestion registrado",
                icon: "success",
                confirmButtonText: "Aceptar",
                color: "#0e47ec",

            });
        </script>
    @endif
    @if (session('upstatus'))
        <script>
            Swal.fire({
                text: "Gestion modificado",
                icon: "success",
                color: "#FFB300",
                confirmButtonText: "Aceptar",
                confirmButtonColor: "#FFB300"
            });
        </script>
    @endif
@stop
