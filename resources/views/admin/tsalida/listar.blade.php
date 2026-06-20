@extends('dashboard')

@section('title', 'vacaciones')

@section('content_header')
    <div class="alert alert-secondary" role="alert">
        <div class="row justify-content-start">
            <div class="col-9">
                <b>REGISTRAR TIPO DE SALIDAS</b>
            </div>
            <div class="col-3 text-primary d-flex justify-content-end">
                <i class="fa-solid fa-user fs-4"></i>&nbsp;{{ auth()->user()->name }}
            </div>
        </div>

    </div>
@stop
@section('contenido')
    <form action="/tipo-salida/salida" method="post">
        <button type="submit" class="btn btn-primary w-25"> <i class="fa-solid fa-plus"></i>
            Añadir tipo de salida</button>
        <div class="container-fluid mt-2 pt-3 pb-2 rounded shadow bg-white">
            <div class="row">
                <div class="col-md-12">

                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating pb-3 mb-2">
                                <input type="text" class="form-control form-control-sm " id="descripcion"
                                    name="descripcion" placeholder="Motivo de salida" required>
                                <label for="motivo">Descripcion tipo de salida (*)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating pb-3 mb-2">
                                <textarea class="form-control form-control-sm " id="sustento" name="sustento" required> </textarea>
                                <label for="Sustento legal">Sustento legal</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating mb-2">
                                <select class="form-select" id="expresa" aria-label="expresa" name="expresa" required>
                                    <option value=" "></option>
                                    <option value="Dias">Dias</option>
                                    <option value="Horas">Horas</option>
                                </select>
                                <label for="Expresa">Expresa en:</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class=" form-floating mb-2">
                                <select class="form-select" aria-label="expresa" name="padre">
                                    <option value=""> </option>
                                    @foreach ($tipoSal as $salida)
                                        @if ($salida->id_padre === null)
                                            <option value="{{ $salida->id }}">{{ $salida->descripcion }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <label for="Nivel">Dependencia</label>
                            </div>
                        </div>
                    </div>
    </form>
    </div>
    <h5 style="text-align: center"><span class=" text-secondary"> Lista de registros - Tipo de salidas </span></h5>
    </div>
    <div class="row justify-content-center">

        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" class="col-md-0">ID</th>
                    <th scope="col" class="col-md-3">Descripcion</th>
                    <th scope="col" class="col-md-5">Sustento legal</th>
                    <th scope="col" class="col-md-1">Expresa</th>
                    <th scope="col" class="col-md-1">Dependencia</th>
                    <th scope="col" class="col-md-1">Modificar</th>
                    <th scope="col" class="col-md-1">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tipoSal as $sal)
                    <tr>

                        <th>{{ $sal->id }}</th>
                        <td>{{ $sal->descripcion }}</td>
                        <td>{{ $sal->sustLegal }}</td>
                        <td>{{ $sal->expresa }}</td>
                        <td>{{ $sal->id_padre }}</td>
                        <td>
                            <a href="/tipo-salida/salida/{{ $sal->id }}" class="btn btn-warning"><i
                                    class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </td>
                        <td>
                            <form action="/tipo-salida/salida/{{ $sal->id }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('¿Esta seguro de eliminar tipo de salida?')" type="submit"
                                    class="btn btn-danger ">
                                    <i class='fa-solid fa-trash'></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                @endforeach
            </tbody>
        </table>
        {{ $tipoSal->links() }}
    </div>

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
                text: "Tipo de salida Registrado",
                icon: "success",
                confirmButtonText: "Aceptar",
                color: "#0e47ec",

            });
        </script>
    @endif
    @if (session('upstatus'))
        <script>
            Swal.fire({
                text: "Tipo de salida Modificado",
                icon: "success",
                color: "#FFB300",
                confirmButtonText: "Aceptar",
                confirmButtonColor: "#FFB300"
            });
        </script>
    @endif
@stop
