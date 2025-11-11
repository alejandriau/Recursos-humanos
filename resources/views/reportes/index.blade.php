@extends('dashboard')

@section('contenidouno')
    <meta content="Lista de personal" name="description">
    <title>Lista de personal activo</title>
@endsection

@section('contenido')
<div class="container-fluid pt-4 px-4">

    <div class="bg-light rounded p-4">

        <div class="row align-items-center g-4">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-0 text-dark">Lista de Personas</h1>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('personas.create') }}" class="text-decoration-none">
                    <button type="button" class="btn btn-success btn-lg shadow-sm rounded-3">
                        <i class="fas fa-user-plus me-2"></i> Registrar persona
                    </button>
                </a>
            </div>
        </div>
        <!-- Filtros Avanzados -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 text-white">Filtros de Búsqueda</h5>
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

                    <!--<div class="col-md-3">
                        <label class="form-label">Unidad Organizacional:</label>
                        <select name="unidad_id" id="unidad-select" class="form-select">
                            <option value="">Todas las unidades</option>
                            @foreach($unidades as $unidad)
                                <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>-->

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
                    <button type="button" id="btn-exportar-pdf" class="btn btn-outline-danger">
                        <i class="fa fa-file-pdf"></i> Exportar PDF
                    </button>
                    <button type="button" id="btn-exportar-excel" class="btn btn-outline-success">
                        <i class="fa fa-file-excel"></i> Exportar Excel
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
    .pagination {
        margin-bottom: 0;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    let searchTimeout;

    // Función para aplicar filtros
    function aplicarFiltros(page = 1) {
        const filtros = {
            search: $('#search').val(),
            tipo: $('#tipo-select').val(),
            fecha_inicio: $('#fecha-inicio').val(),
            fecha_fin: $('#fecha-fin').val(),
            unidad_id: $('#unidad-select').val(),
            nivel_jerarquico: $('#nivel-jerarquico').val(),
            estado: $('#estado-select').val(),
            page: page
        };

        // Mostrar loading
        mostrarLoading();

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
                    scrollToTable();
                } else {
                    mostrarError('Error al aplicar los filtros');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                mostrarError('Ocurrió un error al aplicar los filtros.');
            }
        });
    }

    // Función para mostrar loading
    function mostrarLoading() {
        $('#tabla-container').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="text-muted">Buscando registros...</p>
            </div>
        `);
    }

    // Función para mostrar errores
    function mostrarError(mensaje) {
        $('#tabla-container').html(`
            <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                <i class="fa fa-exclamation-triangle me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
    }

    // Scroll a la tabla
    function scrollToTable() {
        $('html, body').animate({
            scrollTop: $('#tabla-container').offset().top - 100
        }, 400);
    }

    // Eventos para filtros (siempre reinician a página 1)
    $('#btn-aplicar-filtros').on('click', function() {
        aplicarFiltros(1);
    });

    // Búsqueda con debounce
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            aplicarFiltros(1);
        }, 600);
    });

    // Enter en búsqueda
    $('#search').on('keypress', function(e) {
        if (e.which === 13) {
            clearTimeout(searchTimeout);
            aplicarFiltros(1);
        }
    });

    // Filtros que reinician a página 1
    $('#tipo-select, #unidad-select, #estado-select').on('change', function() {
        aplicarFiltros(1);
    });

    $('#fecha-inicio, #fecha-fin, #nivel-jerarquico').on('change', function() {
        aplicarFiltros(1);
    });

    // Limpiar filtros
    $('#btn-limpiar-filtros').on('click', function() {
        $('#search').val('');
        $('#tipo-select').val('TODOS');
        $('#unidad-select').val('');
        $('#fecha-inicio').val('');
        $('#fecha-fin').val('');
        $('#nivel-jerarquico').val('');
        $('#estado-select').val('1');
        aplicarFiltros(1);
    });

    // Manejar clic en paginación (funciona para ambas situaciones)
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        const page = getPageFromHref(href);
        aplicarFiltros(page);
    });

    // Función para extraer página del href
    function getPageFromHref(href) {
        if (!href) return 1;

        const url = new URL(href, window.location.origin);
        return url.searchParams.get('page') || 1;
    }

    // Exportar datos (opcional)
    $('#btn-exportar-pdf, #btn-exportar-excel').on('click', function() {
        const tipo = $(this).attr('id') === 'btn-exportar-pdf' ? 'pdf' : 'excel';
        exportarDatos(tipo);
    });

    function exportarDatos(tipo) {
        const filtros = {
            search: $('#search').val(),
            tipo: $('#tipo-select').val(),
            fecha_inicio: $('#fecha-inicio').val(),
            fecha_fin: $('#fecha-fin').val(),
            unidad_id: $('#unidad-select').val(),
            nivel_jerarquico: $('#nivel-jerarquico').val(),
            estado: $('#estado-select').val(),
            export_type: tipo
        };

        // Limpiar filtros vacíos
        Object.keys(filtros).forEach(key => {
            if (!filtros[key]) delete filtros[key];
        });

        const queryString = new URLSearchParams(filtros).toString();
        const url = "{{ route('reportes.personal') }}?" + queryString;
        window.open(url, '_blank');
    }
});
</script>

@endsection
