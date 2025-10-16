@extends('dashboard')
@section('contenido')
<!-- Sale & Revenue Start -->
<div class="container-fluid pt-4 px-4">

    <h5 class="text-center">File de personal pasivo dos, en las gabetas de madera</h5><br>

    <div>
        <div>
            <h4 class="">Filtra por letra</h4>
        </div>
        @can('filtrar_letra_pasivos_dos')
        <div class="letras-bus" id="letterForm">
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                @foreach (range('A', 'Z') as $letra)
                    @continue($letra == 'I')
                    <form action="{{ route('pasivodos.letra') }}" method="GET">
                        <input type="hidden" name="letra" value="{{ $letra }}">
                        <button class="btn btn-{{ $letra == ($letter ?? 'A') ? 'success' : 'primary' }}" type="submit">
                            {{ $letra }}
                        </button>
                    </form>
                @endforeach
                <!-- Botón para limpiar filtros -->
                @if(isset($letter) && $letter != 'A')
                <a href="{{ route('pasivodos.index') }}" class="btn btn-outline-secondary">
                    Limpiar
                </a>
                @endif
            </div>
        </div>
        @endcan
    </div>

    <div class="row">
        @can('buscar_pasivos_dos')
        <div class="col-md-8 g-4">
            <form class="form" method="GET" action="{{ route('pasivodos.buscar') }}">
                <div class="input-group">
                    <input class="form-control" type="search" name="query" placeholder="Buscar por nombre completo"
                        id="search" value="{{ request('query', $search ?? '') }}">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                    @if(request('query') || isset($search))
                    <a href="{{ route('pasivodos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                    @endif
                </div>
                <!-- Mantener el parámetro de letra en la búsqueda -->
                @if(isset($letter) && $letter != 'A')
                <input type="hidden" name="letra" value="{{ $letter }}">
                @endif
            </form>
        </div>
        @endcan

        @can('crear_pasivos_dos')
        <div class="col-md-4 registro-p">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary flotanteg" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                Registrar o agregar nuevo persona
            </button>
            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header forcolo">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Registro de nueva persona</h1>
                        <button type="button" class="btn-close cerrarf" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                        <form method="POST" action="{{ route('pasivodos.guardar')}}">
                            @csrf
                            <div class="modal-body">
                                <div class="container mt-5">
                                        <!-- Campo para el Número -->
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label for="numero" class="form-label">Número:</label>
                                                <input type="number" class="form-control" id="numero" name="codigo" required>
                                            </div>
                                            <!-- Campo para la Letra (opciones) -->
                                            <div class="mb-3 col-md-6">
                                                <label for="letra" class="form-label">Letra:</label>
                                                <select class="form-select" id="letra" name="letra" required>
                                                    <option value="">Seleccione una letra</option>
                                                    @foreach (['A','B','C','D','E','G','H','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'] as $letra)
                                                        <option value="{{ $letra }}">{{ $letra }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Campo para el Nombre Completo -->
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre Completo:</label>
                                            <input type="text" class="form-control" id="nombre" name="nombrecompleto" required>
                                        </div>

                                        <!-- Campo para Observaciones -->
                                        <div class="mb-3">
                                            <label for="observaciong" class="form-label">Observación:</label>
                                            <textarea class="form-control" id="observaciong" name="observacion" rows="4" placeholder="Escribe aquí..."></textarea>
                                        </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary" name="guardarpasivo">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <div class="">
        <div class="row">
            <div class="col-md-8">
                <h4 class="mb-4">Lista de Personas</h4>
            </div>
            @can('ver_ultimo_registro_pasivos_dos')
            <div class="col-md-4 d-flex justify-content-end mt-3">
                <a href="{{ route('pasivodos.ultimo') }}" class="btn btn-outline-primary rounded-pill shadow-sm px-1">
                    Ver último registro &raquo;
                </a>
            </div>
            @endcan
        </div>

        @can('generar_pdf_pasivos_dos')
        <div class="" id="tabla-imprimir">
            <form action="{{ url('pasivodos/pdf') }}" method="GET">
                @csrf
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>CODIGO</th>
                            <th>NOMBRE COMPLETO</th>
                            <th>OBSERVACIONES</th>
                            @can('eliminar_seleccion_pasivos_dos')
                            <th>Acciones</th>
                            @endcan
                        </tr>
                    </thead >
                    <tbody id="tablaBody">
                        @if ($selecciones->count() > 0)
                            @foreach ($selecciones as $seleccion)
                                <tr>
                                    <td><input type="hidden" name="idreporte[]" value="{{$seleccion->pasivodos->id}}">{{ $seleccion->pasivodos->letra ?? '' }} {{ $seleccion->pasivodos->codigo ?? '' }}</td>
                                    <td>{{ $seleccion->pasivodos->nombrecompleto ?? ''}}</td>
                                    <td>{{ $seleccion->pasivodos->observacion ?? ''}}</td>
                                    @can('eliminar_seleccion_pasivos_dos')
                                    <td>
                                        <button type="button" class="btn btn-danger btn-eliminar" data-id="{{ $seleccion->id }}">X</button>
                                    </td>
                                    @endcan
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

                @if ($selecciones->count() > 0)
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-pdf me-2"></i>Generar PDF
                    </button>

                    @can('eliminar_seleccion_pasivos_dos')
                    <button type="button" class="btn btn-danger" id="btnEliminarTodo">
                        <i class="fas fa-trash me-2"></i>Eliminar Todo
                    </button>
                    @endcan
                </div>
                @endif
            </form>
        </div>
        @endcan

        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>CODIGO</th>
                        <th style="min-width: 400px;">NOMBRE COMPLETO</th>
                        <th class="obser-tabla">OBSERVACIONES</th>
                        @can('editar_pasivos_dos')
                        <th class="obser-tabla">EDITAR</th>
                        @endcan
                        @can('eliminar_pasivos_dos')
                        <th class="obser-tabla">ELIMINAR</th>
                        @endcan
                        @can('seleccionar_pasivos_dos')
                        <th class="obser-tabla">SOLICITAR</th>
                        @endcan
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach ($resultados as $row)
                        <tr>
                            @can('editar_pasivos_dos')
                            <form method="POST" action="{{ route('pasivodos.actualizar', $row->id) }}">
                                @csrf
                                @method('PUT')
                            @endcan
                                <td class="pasivocod">{{ $row->letra }} {{ $row->codigo }}</td>
                                <td style="background-color: {{ empty($row->nombrecompleto) ? '#e11d36' : 'transparent' }}">
                                    @can('editar_pasivos_dos')
                                    <input type="text"
                                        class="inpu inpu-pasivomod w-100"
                                        style="all: unset;"
                                        name="nombrecompleto"
                                        value="{{ $row->nombrecompleto }}">
                                    @else
                                    {{ $row->nombrecompleto }}
                                    @endcan
                                </td>

                                <td>
                                    @can('editar_pasivos_dos')
                                    <input type="text" class="inpu inpu-pasivomod" style="all: unset;" name="observacion" value="{{ $row->observacion }}">
                                    @else
                                    {{ $row->observacion }}
                                    @endcan
                                </td>

                                @can('editar_pasivos_dos')
                                <td><button type="submit" name="editarp" class="btn btn-warning">Actualizar</button></td>
                                </form>
                                @endcan

                                @can('eliminar_pasivos_dos')
                                <td>
                                    <form action="{{ route('pasivodos.eliminar', $row->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="eliminarcof()" class="btn btn-danger" name="elminarp">Eliminar</button>
                                    </form>
                                </td>
                                @endcan

                                @can('seleccionar_pasivos_dos')
                                <td>
                                    <form class="seleccionar-pasivod">
                                        @csrf
                                        <input type="hidden" name="idselecc" value="{{ $row->id }}">
                                        <button type="submit" class="btn btn-primary" name="selccionarp">Seleccionar</button>
                                    </form>
                                </td>
                                @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-4">
                {{ $resultados->withQueryString()->links() }}
            </div>
        </div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function sendLetter(letter) {
            $.ajax({
                url: "{{ route('pasivodos.letra') }}",  // Archivo PHP que procesa el dato
                type: 'GET',
                data: { letter: letter },  // Enviar la letra seleccionada
                success: function(response) {
                    $("#table-body").html(response);
                },
                error: function() {
                    $("#table-body").html("<strong>Error:</strong> No se pudo enviar la letra.");
                }
            });
        }


    $(document).on("submit", ".seleccionar-pasivod", function (e) {
        e.preventDefault();

        var id = $(this).find('input[name="idselecc"]').val();
        if (!id) {
            Swal.fire({
                icon: 'warning',
                title: 'ID no encontrado.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                background: '#ffc107',
                color: '#000',
                customClass: { popup: 'custom-toast' }
            });
            return;
        }

        $.ajax({
            url: "{{ route('pasivodos.traer') }}",
            type: "GET",
            data: {
                idselecc: id
            },
            success: function (response) {
                if (response.success) {
                    response.data.forEach(function(row) {
                        var rowHTML = '<tr>';
                        rowHTML += '<td><input type="hidden" name="idreporte[]" value="'+row.id+'">' + row.codigo + '</td>';
                        rowHTML += '<td>' + row.nombrecompleto + '</td>';
                        rowHTML += '<td>' + row.observacion + '</td>';
                        rowHTML += '<td><button type="button" class="btn btn-danger btn-eliminar" data-id="'+row.idSeleccion+'">X</button></td>';
                        rowHTML += '</tr>';

                        $("#tablaBody").append(rowHTML);
                    });

                    Swal.fire({
                        icon: 'success',
                        title: '¡Registro agregado!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        background: '#007BFF',
                        color: '#fff',
                        customClass: { popup: 'custom-toast' }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message || 'Error al obtener los datos.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        background: '#dc3545',
                        color: '#fff',
                        customClass: { popup: 'custom-toast' }
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error del servidor',
                    text: 'Ocurrió un problema al conectar.',
                    background: '#dc3545',
                    color: '#fff',
                });
            }
        });
    });


$(document).on('submit', '.form-actualizar', function(e) {
    e.preventDefault();

    const form = $(this);
    const actionUrl = form.attr('action');
    const formData = form.serialize();

    $.ajax({
        url: actionUrl,
        type: 'POST',
        data: formData,
        success: function(response) {
            alert(response.mensaje || "Actualizado.");
        },
        error: function(xhr) {
            alert("Ocurrió un error.");
            console.error(xhr.responseText);
        }
    });
});

    $(document).ready(function () {
        $('#tablaBody').on('click', '.btn-eliminar', function () {
            const boton = $(this);
            const id = boton.data('id');

            // Confirmación con SweetAlert2
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Esto eliminará el registro!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('seleccion.eliminar') }}",
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function (response) {
                            if (response.success) {
                                boton.closest('tr').remove();

                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Registro eliminado correctamente',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    background: '#28a745',
                                    color: '#fff',
                                    customClass: { popup: 'custom-toast' }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'No se pudo eliminar',
                                    text: response.message || 'Intenta de nuevo.',
                                    background: '#dc3545',
                                    color: '#fff',
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error en el servidor',
                                text: 'No se pudo procesar la eliminación.',
                                background: '#dc3545',
                                color: '#fff',
                            });
                        }
                    });
                }
            });
        });
    });

// Eliminar todas las selecciones
$(document).ready(function () {
    $('#btnEliminarTodo').on('click', function () {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esto eliminará TODOS tus registros seleccionados!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar todo',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('seleccion.eliminar.todo') }}",
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            // Limpiar la tabla
                            $("#tablaBody").html('<tr><td colspan="4" class="text-center">No tienes registros seleccionados</td></tr>');

                            // Ocultar botones
                            $('.d-flex.gap-2.mt-3').hide();

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Todos tus registros fueron eliminados',
                                showConfirmButton: false,
                                timer: 3000,
                                background: '#28a745',
                                color: '#fff',
                                customClass: { popup: 'custom-toast' }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'No se pudieron eliminar los registros',
                                text: response.message || 'Intenta de nuevo.',
                                background: '#dc3545',
                                color: '#fff',
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error en el servidor',
                            text: 'No se pudo procesar la eliminación.',
                            background: '#dc3545',
                            color: '#fff',
                        });
                    }
                });
            }
        });
    });
});




    </script>
<!-- Sale & Revenue End -->
@endsection
