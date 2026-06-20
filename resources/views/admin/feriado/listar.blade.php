@extends('dashboard')

@section('title', 'vacaciones')

@section('content_header')
    <div class="alert alert-secondary" role="alert">
        <div class="row justify-content-start">
            <div class="col-9">
                <b>REGISTRAR FERIADOS</b>
            </div>
            <div class="col-3 text-primary d-flex justify-content-end">
                <i class="fa-solid fa-user fs-4"></i>&nbsp;{{ auth()->user()->name }}
            </div>
        </div>

    </div>
@stop

@section('contenido')
    <form action="/feriado-gestion/feriado" method="post">
        <button type="submit" class="btn btn-primary mb-2 w-25"> <i class="fa-solid fa-plus"></i>
            Añadir feriado</button>
        <div class="container-fluid pb-2 pt-3 rounded shadow bg-white">
            @csrf
            <div class="row pt-2">
                <div class="col-md-3 form-floating mb-2 w-25">
                    <select class="form-select ps-2" id="gestion" name="gestion" aria-label=" gestion" required>
                        @foreach ($gestiones as $ges)
                            @if ($ges->estado == 'Habilitado')
                                <option value="{{ $ges->id }}">{{ $ges->anio }}</option>
                            @endif
                        @endforeach
                    </select>
                    <label for="salida" class="ps-3"> Gestion</label>
                </div>
                <div class="col-md-6" style="text-align: left">
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control form-control-sm " id="descripcion" name="descripcion"
                            placeholder="Descripcion del feriado" required>
                        <label for="descripcio">Descripcion del feriado</label>
                    </div>
                </div>
                <div class="col-md-3" style="text-align: left">
                    <div class="form-floating mb-2">
                        <input type="date" class="form-control form-control-sm " id="fecha" name="fecha"
                            placeholder="Fecha del feriado" required>
                        <label for="Fecha del feriado">Fecha del feriado</label>
                    </div>
                </div>
            </div>
    </form>

    <h5 style="text-align: center"><span class=" text-secondary"> Lista de registros - Feriado </span></h5>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col" class="col-md-0">ID</th>
                <th scope="col" class="col-md-8">Descripcion</th>
                <th scope="col" class="col-md-1">Fecha</th>
                <th scope="col" class="col-md-1">Gestion</th>
                <th scope="col" class="col-md-1">Modificar</th>
                <th scope="col" class="col-md-1">Eliminar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($feriado as $fer)
                @if ($fer->gestion->estado == 'Habilitado')
                    <tr>
                        <th scope="row">{{ $fer->id }}</th>
                        <td>{{ $fer->descripcion }}</td>
                        <td>{{ $fer->fechaf }}</td>
                        <td>{{ $fer->gestion->anio }}</td>
                        <td>
                            <a href="/feriado-gestion/feriado/{{ $fer->id }}/edit" class="btn btn-warning"><i
                                    class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </td>
                        <td>
                            <form action="/feriado-gestion/feriado/{{ $fer->id }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('¿Esta seguro de eliminar el feriado?')" type="submit" class="btn btn-danger">
                                    <i class='fa-solid fa-trash'></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    {{ $feriado->links() }}
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
                text: "Feriado registrado",
                icon: "success",
                confirmButtonText: "Aceptar",
                color: "#0e47ec",

            });
        </script>
    @endif
    @if (session('upstatus'))
        <script>
            Swal.fire({
                text: "Feriado modificado",
                icon: "success",
                color: "#FFB300",
                confirmButtonText: "Aceptar",
                confirmButtonColor: "#FFB300"
            });
        </script>
    @endif
@stop
