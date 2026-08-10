@extends('layouts.baseadm')

@section('title', 'vacaciones')

@section('content_header')
    <div class="alert alert-secondary" role="alert">
        <div class="row justify-content-start">
            <div class="col-9">
                <b>MODIFICAR FERIADO</b>
            </div>
            <div class="col-3 text-primary d-flex justify-content-end">
                <i class="fa-solid fa-user fs-4"></i>&nbsp;{{ auth()->user()->name }}
            </div>
        </div>

    </div>
@stop

@section('content')

    <div class="container-fluid pt-4 pb-2 rounded shadow bg-white">
        <form action="/feriado-gestion/feriado/{{ $feriado->id }}" method="post">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6" style="text-align: left">
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control form-control-sm " id="descripcion" name="descripcion"
                            value="{{ $feriado->descripcion }}" placeholder="Descripcion del feriado" required>
                        <label for="descripcio">Descripcion del feriado</label>
                    </div>
                </div>
                <div class="col-md-3" style="text-align: left">
                    <div class="form-floating mb-2">
                        <input type="date" class="form-control form-control-sm " id="fecha" name="fecha"
                            value="{{ $feriado->fechaf }}" placeholder="Fecha del feriado" required>
                        <label for="Fecha del feriado">Fecha del feriado</label>
                    </div>
                </div>
                <div class="col-md-12" style="text-align: left">
                    <button type="submit" class="btn btn-warning mb-3 ms-3 w-25"> <i class="fa-solid fa-floppy-disk"></i>
                        Guardar</button><a href="/feriado-gestion/feriado" class="btn btn-info mb-3 ms-3"><i class="fa-solid fa-xmark"></i>
                        Cancelar</a>
                </div>
            </div>
        </form>
    </div>
@stop

@section('css')
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
@stop

@section('js')
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome.js') }}"></script>
    <script src="{{ asset('js/funcionesJs.js') }}"></script>
@stop
