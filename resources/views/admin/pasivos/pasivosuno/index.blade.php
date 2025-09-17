@extends('dashboard')
@section('contenido')
<!-- Sale & Revenue Start -->
<div class="container-fluid pt-4 px-4">


    <div class="row g-4">
        <div class="col-sm-12">
            <a href="<?php echo asset(''); ?>ventas/crearproducto">
                <button type="submit" class="btn btn-success">Registrar nuevo pasivo uno</button>
            </a>
        </div>
    </div>

    <div class="container">
        <h1 class="mb-4">Lista de Pasivos uno</h1>
        <div class="table-responsive">
            <div class="container">
                <form method="GET" action="{{ url('pasivouno/buscar') }}">
                    @csrf
                    <input type="search" name="pasivosu" placeholder="Buscar por nombre completo" required class="form-control">
                    <button type="submit" class="btn btn-primary mt-2">Buscar</button>
                </form>
            </div>
            @if(isset($resultados) && !$resultados->isEmpty())
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>CODIGO</th>
                            <th>NOMBRE COMPLETO</th>
                            <th>OBSERVACIONES</th>
                            <th>SOLICITAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resultados as $item)
                        <tr>
                            <td>{{ substr($item->nombrecompleto, 0, 1) . ' ' . $item->codigo }}</td>
                            <td>{{ $item->nombrecompleto }}</td>
                            <td>{{ $item->observacion }}</td>
                            <td>
                                <form action="{{ url('/pasivos/reservar') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <button type="submit" class="btn btn-primary">Reservar</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif(isset($resultados))
                <p>No se encontraron resultados.</p>
            @endif


        </div>
    </div>

</div>
<!-- Sale & Revenue End -->

@if (session('success'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 1000,
        timerProgressBar: true,
        background: '#007BFF', // azul
        color: '#fff', // texto blanco
        customClass: {
            popup: 'custom-toast'
        },
    });
</script>
@endif

<!-- Estilos personalizados -->
<style>
    .swal2-popup.custom-toast {
        width: 300px !important;
        height: 80px !important;
        border-radius: 12px;
        font-size: 16px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
</style>
@endsection
