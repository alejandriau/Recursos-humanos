@extends('dashboard')

@section('title', 'vacaciones')

@section('content_header')
    <div class="alert alert-secondary" role="alert">
        <div class="row justify-content-start">
            <div class="col-9">
                <b>MODIFICAR TIPO DE SALIDA</b>
            </div>
            <div class="col-3 text-primary d-flex justify-content-end">
                <i class="fa-solid fa-user fs-4"></i>&nbsp;{{ auth()->user()->name }}
            </div>
        </div>
    </div>
@stop

@section('contenido')
    <div class="container-fluid pt-4 pb-2 rounded shadow bg-white">
        <div class="row">
            <div class="col-md-12" style="text-align: left">
                <form action="/tsalida/{{ $tiposalida->id }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control form-control-sm " name="descripcion"
                                    value="{{ $tiposalida->descripcion }}" required>
                                <label for="Descripcion tipo de salida">Descripcion tipo de salida</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                             <div class="form-floating pb-3 mb-2">
                                <textarea rows="5" class="form-control" name="sustento">{{ $tiposalida->sustLegal }}</textarea>
                                <label for="Sustento legal">Sustento legal</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating mb-2">
                                <select class="form-select" id="estado" aria-label="expresa" name="expresa" required>
                                    @if ($tiposalida->expresa === 'Dias')
                                        <option value="{{ $tiposalida->expresa }}">{{ $tiposalida->expresa }}</option>
                                        <option value=""></option>
                                        <option value="Horas">Horas</option>
                                    @else
                                        @if ($tiposalida->expresa === 'Horas')
                                            <option value="{{ $tiposalida->expresa }}">{{ $tiposalida->expresa }}</option>
                                            <option value=""></option>
                                            <option value="Dias">Dias</option>
                                        @else
                                            <option value=""></option>
                                            <option value="Dias">Dias</option>
                                            <option value="Horas">Horas</option>
                                        @endif

                                    @endif
                                </select>
                                <label for="Expresa">Expresa en:</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating mb-2">
                                <select class="form-select" id="padre" aria-label="Nivel" name="padre">
                                    <option value=""></option>
                                    @foreach ($salida as $salida)
                                        @if ($salida->id_padre === null)
                                         <option value="{{$salida->id}}">{{$salida->descripcion}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <label for="Dependencia">Dependencia</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning mb-3 ms-3 w-25"> <i
                            class="fa-regular fa-pen-to-square"></i>
                        Editar tipo salida</button> <a href="/tsalida" class="btn btn-info mb-3 ms-3"><i
                            class="fa-solid fa-xmark"></i>
                        Cancelar</a>
                </form>
            </div>
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
@stop
