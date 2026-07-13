@extends('layouts.baseadm')

@section('title', 'Nuevo Tipo de Salida')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex align-items-center mb-3 gap-2">
        <a href="#" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-plus-circle me-2 text-primary"></i>Nuevo Tipo de Salida
            </h4>
            <small class="text-muted">Registrar un tipo de salida en el catálogo</small>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('tipo-salida.salida') }}" method="POST">
                        @csrf
                        @include('admin.tsalida.form')
                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="#" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection