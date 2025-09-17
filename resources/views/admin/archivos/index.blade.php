@extends('dashboard')
@section('contenido')
<!-- Sale & Revenue Start -->
<div class="container-fluid pt-4 px-4">

    <div class="container">
        <div class="row g-4">
            <div class="col-sm-12">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('successdelete'))
                    <div class="alert alert-success">
                        {{ session('successdelete') }}
                    </div>
                @endif
            </div>
        </div>
        <div>
            <h2>Busque y seleccione una persona para subir el archivo</h2>
        </div>
        <div class="row">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control mb-3" placeholder="Buscar...">
            </div>
            <div class="col-md-4">
                <select name="" class="form-select" id="lista">
                    <option value="">Seleccione una opción</option>
                </select>
            </div>
        </div>
        <hr>
        <h2>Subir nuevo archivo</h2>
        <div class="" id="formulario">
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="idPersona" class="form-label">CI</label>
                            <input type="text" name="ci" class="form-control" value="" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="idPersona" class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="" disabled>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="idPersona" class="form-label">Primer apellido</label>
                            <input type="text" name="primerapellido" class="form-control" value="" disabled>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="idPersona" class="form-label">Segundo apellido</label>
                            <input type="text" name="segundoapellido" class="form-control" value="" disabled>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="">
                            <select name="subcarpeta" class="form-select">
                                <option value="">Seleccione una opción</option>
                                <option value="documentos_incorporacion">documentos_incorporacion</option>
                                <option value="documentos_formacion_academica_experiencia_laboral">documentos_formacion_academica_experiencia_laboral</option>
                                <option value="documentos_varios">documentos_varios</option>
                                <option value="documentos_desvinculacion">documentos_desvinculacion</option>

                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tipoDocumento" class="form-label">Tipo de Documento</label>
                            <input type="text" name="tipoDocumento" class="form-control" required>
                            <label for="archivo" class="form-label">Archivo</label>
                            <input type="file" name="archivo" class="form-control" accept="application/pdf" required onchange="previewPDF(event)">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea name="observaciones" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Subir</button>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <!-- Vista previa del PDF -->
                        <div class="mb-3" id="pdf-preview" style="display: none;">
                            <label class="form-label">Vista previa:</label>
                            <iframe id="pdf-viewer" width="100%" height="500px"></iframe>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                    </div>
                </div>



            </form>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function previewPDF(event) {
        const file = event.target.files[0];
        const previewDiv = document.getElementById('pdf-preview');
        const viewer = document.getElementById('pdf-viewer');

        if (file && file.type === "application/pdf") {
            const fileURL = URL.createObjectURL(file);
            viewer.src = fileURL;
            previewDiv.style.display = 'block';
        } else {
            viewer.src = "";
            previewDiv.style.display = 'none';
        }
    }


    $(document).ready(function () {
        $('#search').on('input', function () {
            let search = $(this).val();
            $.ajax({
                url: "{{ route('archivos.buscar') }}",
                type: "GET",
                data: { search: search
                },
                success: function (response) {
                    $('#lista').html(response);
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

    $('#lista').on('change', function () {
        var valorSeleccionado = $(this).val();

        if (valorSeleccionado !== '') {
            $.ajax({
                url: "{{ route('archivos.formulario') }}",
                method: 'GET',
                data: { opcion: valorSeleccionado },
                success: function (respuesta) {
                    $('#formulario').html(respuesta);
                },
                error: function () {
                    $('#formulario').html('<p>Error al cargar la formulario.</p>');
                }
            });
        } else {
            $('#formulario').html('');
        }
    });

</script>



<!-- Sale & Revenue End -->
@endsection
