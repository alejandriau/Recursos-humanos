@extends('dashboard')

@section('contenidouno')
    <meta content="Lista de personal" name="description">
    <title>Lista de personal activo</title>
@endsection
@section('contenido')
<!-- Sale & Revenue Start -->
<div class="container-fluid pt-4 px-4">



    <div class="row g-4">
        <div class="col-sm-12">
            <a href="{{ route('personas.create') }}">
                <button type="submit" class="btn btn-success">Registrar persona</button>
            </a>
        </div>
    </div>

    <div class="">
        <h1 class="mb-4">Lista de Personas ----</h1>
        <div class="row">
            <div class="col-md-4">
                <label>Por tipo:
                    <select name="tipo" id="tipo-select">
                        <option value="ITEM">Planta</option>
                        <option value="CONTRATO">Contrato</option>
                    </select>
                </label>
            </div>
            <div class="col-md-8">
                <input type="text" id="search" class="form-control mb-3" placeholder="Buscar...">
            </div>
        </div>
        <a href="{{route('reportes.personal')}}">reporte pdf</a>
        <a href="{{route('reportes.excel')}}">descargar excel</a>
        <div class="table-responsive" id="tabla-container">
            <table class="table table-striped small" style="width:100%">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th>NIVEL GERARQUICO</th>
                        <th>APELLIDO 1</th>
                        <th>APELLIDO 2</th>
                        <th>NOMBRE</th>
                        <th>CI</th>
                        <th>HABER</th>
                        <th>FECHA INGRESO</th>
                        <th>FECHA NACIMIENTO</th>
                        <th>TITULO PROVISION NACIONAL</th>
                        <th>FECHA TITULO</th>
                        <th>TELEFONO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse ($personas as $persona)
                        <tr>
                            <td>{{ $persona->puestoActual->puesto->item ?? '' }}</td>
                            <td>{{ $persona->puestoActual->puesto->nivelgerarquico ?? '' }}</td>
                            <td>{{ $persona->apellidoPat }}</td>
                            <td>{{ $persona->apellidoMat }}</td>
                            <td>{{ $persona->nombre }}</td>
                            <td>{{ $persona->ci }}</td>
                            <td>{{ number_format($persona->puestoActual->puesto->haber ?? 0, 2, ',', '.') }}</td>
                            <td>{{ !empty($persona->fechaIngreso) ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') : '' }}</td>
                            <td>{{ !empty($persona->fechaNacimiento) ? \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y') : '' }}</td>
                            <td>{{ $persona->profesion->provisionN ?? '' }}</td>
                            <td>{{ !empty($persona->profesion->fechaProvision) ? \Carbon\Carbon::parse($persona->profesion->fechaProvision)->format('d/m/Y') : '' }}</td>
                            <td>{{$persona->telefono }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn text-dark fw-bold fs-4" type="button" id="dropdownMenu{{ $persona->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        ⋮
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $persona->id }}">
                                        <li><a class="dropdown-item" href="#">🔍 Ver</a></li>
                                        <li><a class="dropdown-item" href="{{ route('personas.edit', $persona->id) }}">✏️ Editar</a></li>
                                        <li><a class="dropdown-item" href="{{ route('personas.show', $persona->id) }}">✏️ ver</a></li>
                                        <li>
                                            <form action="{{ route('personas.destroy', $persona->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de desactivar a esta persona?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item text-danger">🗑️ Desactivar</button>
                                            </form>
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="{{ route('regisrar.archivos', $persona->id)}}">arch</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12">No se encontraron resultados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#search').on('input', function () {
                let search = $(this).val();
                let tipo = $('#tipo-select').val();
                $.ajax({
                    url: "{{ route('reportes.buscar') }}",
                    type: "GET",
                    data: { search: search,
                        tipo: tipo
                    },
                    success: function (response) {
                        $('#table-body').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error('Estado:', status);
                        console.error('Código HTTP:', xhr.status);
                        console.error('Respuesta del servidor:', xhr.responseText);
                        alert('Ocurrió un error al buscar. Revisa la consola (F12).');
                    }

                });
            });
        });
        $('#tipo-select').on('change', function () {
            const tipo = $(this).val();

            $.ajax({
                url: "{{ route('reportes.tipo') }}", // Asegúrate de que esta ruta esté definida
                method: 'GET',
                data: { tipo: tipo },
                success: function (response) {
                    $('#tabla-container').html(response); // Aquí sí lleva #
                },
                error: function () {
                    $('#tabla-container').html('<p>Error al cargar la tabla.</p>');
                }
            });
        });

    </script>

</div>
<!-- Sale & Revenue End -->
@endsection
