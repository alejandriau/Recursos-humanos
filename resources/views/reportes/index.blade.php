@extends('dashboard')

@section('contenidouno')
    <meta content="Lista de personal" name="description">
    <title>Lista de personal activo</title>
@endsection

@section('contenido')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12">
            <a href="{{ route('personas.create') }}">
                <button type="submit" class="btn btn-success">Registrar persona</button>
            </a>
        </div>
    </div>

    <div class="bg-light rounded p-4">
        <h1 class="mb-4">Lista de Personas</h1>

        <!-- Filtros Avanzados -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Filtros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Personal:</label>
                        <select name="tipo" id="tipo-select" class="form-select">
                            <option value="TODOS">Todos</option>
                            <option value="ITEM">Planta</option>
                            <option value="CONTRATO">Contrato</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Unidad Organizacional:</label>
                        <select name="unidad_id" id="unidad-select" class="form-select">
                            <option value="">Todas las unidades</option>
                            @foreach($unidades as $unidad)
                                <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Fecha Ingreso Desde:</label>
                        <input type="date" id="fecha-inicio" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Fecha Ingreso Hasta:</label>
                        <input type="date" id="fecha-fin" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Búsqueda General:</label>
                        <input type="text" id="search" class="form-control" placeholder="Buscar por nombre, apellido, CI, título...">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Nivel Jerárquico:</label>
                        <input type="text" id="nivel-jerarquico" class="form-control" placeholder="Ej: Jefe, Director, etc.">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Estado:</label>
                        <select id="estado-select" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <button type="button" id="btn-aplicar-filtros" class="btn btn-primary">
                            <i class="fa fa-filter"></i> Aplicar Filtros
                        </button>
                        <button type="button" id="btn-limpiar-filtros" class="btn btn-secondary">
                            <i class="fa fa-refresh"></i> Limpiar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Exportación -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <a href="{{ route('reportes.personal') }}" class="btn btn-outline-danger">
                        <i class="fa fa-file-pdf"></i> Exportar PDF
                    </a>
                    <a href="{{ route('reportes.excel') }}" class="btn btn-outline-success">
                        <i class="fa fa-file-excel"></i> Exportar Excel
                    </a>
                    <button type="button" id="btn-exportar-filtrado" class="btn btn-outline-info">
                        <i class="fa fa-download"></i> Exportar Filtrado Actual
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Resultados -->
        <div class="table-responsive" id="tabla-container">
            @include('reportes.partes.tabla-personas', ['personas' => $personas])
        </div>
    </div>

    @if (session('success'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            background: '#007BFF',
            color: '#fff',
            customClass: {
                popup: 'custom-toast'
            },
        });
    </script>
    @endif
</div>

<style>
    .swal2-popup.custom-toast {
        width: 300px !important;
        height: 80px !important;
        border-radius: 12px;
        font-size: 16px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .dropdown-menu {
        min-width: 200px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Función para aplicar filtros
    function aplicarFiltros() {
        const filtros = {
            search: $('#search').val(),
            tipo: $('#tipo-select').val(),
            fecha_inicio: $('#fecha-inicio').val(),
            fecha_fin: $('#fecha-fin').val(),
            unidad_id: $('#unidad-select').val(),
            nivel_jerarquico: $('#nivel-jerarquico').val(),
            estado: $('#estado-select').val()
        };

        $.ajax({
            url: "{{ route('reportes.filtros-avanzados') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ...filtros
            },
            success: function (response) {
                if (response.success) {
                    $('#tabla-container').html(response.html);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('Ocurrió un error al aplicar los filtros.');
            }
        });
    }

    // Eventos para filtros
    $('#btn-aplicar-filtros').on('click', aplicarFiltros);

    $('#search').on('input', function() {
        clearTimeout(this.delay);
        this.delay = setTimeout(aplicarFiltros, 500);
    });

    $('#tipo-select, #unidad-select, #estado-select').on('change', aplicarFiltros);

    $('#fecha-inicio, #fecha-fin, #nivel-jerarquico').on('change', aplicarFiltros);

    // Limpiar filtros
    $('#btn-limpiar-filtros').on('click', function() {
        $('#search').val('');
        $('#tipo-select').val('TODOS');
        $('#unidad-select').val('');
        $('#fecha-inicio').val('');
        $('#fecha-fin').val('');
        $('#nivel-jerarquico').val('');
        $('#estado-select').val('1');
        aplicarFiltros();
    });

    // Exportar datos filtrados
    $('#btn-exportar-filtrado').on('click', function() {
        const filtros = {
            tipo: $('#tipo-select').val(),
            fecha_inicio: $('#fecha-inicio').val(),
            fecha_fin: $('#fecha-fin').val(),
            unidad_id: $('#unidad-select').val()
        };

        const queryString = new URLSearchParams(filtros).toString();
        window.open("{{ route('reportes.personal') }}?" + queryString, '_blank');
    });
});
</script>
@endsection
