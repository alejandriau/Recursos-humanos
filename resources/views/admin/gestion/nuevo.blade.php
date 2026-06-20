
@extends('dashboard')

@section('title', 'vacaciones')

@section('content_header')
     <div class="alert alert-secondary" role="alert">
        <div class="row justify-content-start">
            <div class="col-9">
                <b>CREAR NUEVA GESTION</b>
            </div>
            <div class="col-3 text-primary d-flex justify-content-end">
                <i class="fa-solid fa-user fs-4"></i>&nbsp;{{ auth()->user()->name }}
            </div>
        </div>
    </div>
@stop


@section('contenido')
<div class="container-fluid mt-2 rounded shadow bg-white">
    <form action="/apertura-gestion/gestion" method="post">
        @csrf
        <div class="row pt-4 pb-2">
            <div class="col-md-4" style="text-align: left">
                <div class="form-floating mb-2 w-75">
                    <input type="number" class="form-control form-control-sm " name="anio" placeholder="Año"
                        required>
                    <label for="año">Ingrese año</label>
                </div>
            </div>
            <div class="col-md-4" style="text-align: left">
                <div class="form-floating mb-2 w-75">
                    <input type="date" class="form-control form-control-sm " name="fecha" placeholder="Fecha"
                        required>
                    <label for="Fecha">Fecha de creacion</label>
                </div>
            </div>
            <div class="col-md-4" style="text-align: left">
                <div class="form-floating mb-2 w-75">
                    <select class="form-select" id="estado" aria-label="Estado" name="estado">
                        <option value="Habilitado">Habilitado</option>
                    </select>
                    <label for="Estado">Estado de gestion</label>
                </div>
            </div>
            <div class="col-md-12" style="text-align: left">
                <button type="submit" class="btn btn-warning mb-3 ms-3 w-25"> <i class="fa-solid fa-floppy-disk"></i>
                    Guardar</button><a href="/apertura-gestion/gestion" class="btn btn-info mb-3 ms-3"><i
                        class="fa-solid fa-xmark"></i>
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


