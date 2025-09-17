@extends('dashboard')

@section('contenido')
<div class="container">
    <h2>Crear Puesto</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('puesto.store') }}" method="POST">
        @include('admin.puestos.form')
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    function toggleSelectVisibility() {
        $("select").each(function () {
            if ($(this).find("option").length > 1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Ocultar todos los selects al inicio
    $("select").hide();

    // Cargar las secretarías al cargar la página
    $.get("{{ route('secretarias')}}", function (data) {
        $("#secretaria").append(data);
        toggleSelectVisibility();
    });

    // Cargar direcciones según la secretaría seleccionada
    $("#secretaria").change(function () {
        let idSecretaria = $(this).val();
        $("#direccion, #unidad, #area, #unidadsecre, #areasecre, #areadire, #areaunidad, #items").html('<option value="">--Seleccione--</option>').hide();

        if (idSecretaria) {
            $.get("{{route('direcciones')}}", { idSecretaria: idSecretaria }, function (data) {
                $("#direccion").append(data);
                toggleSelectVisibility();
            });
            $.get("{{route('unidadessecre')}}", { idSecretaria: idSecretaria }, function (data) {
                $("#unidadsecre").append(data);
                toggleSelectVisibility();
            });
            $.get("{{route('areassecretaria')}}", { idSecretaria: idSecretaria }, function (data) {
                $("#areasecre").append(data);
                toggleSelectVisibility();
            });
            $.get("consul/puesto.php", { idSecretaria: idSecretaria }, function (data) {
                $("#items").append(data);
                toggleSelectVisibility();
            });
            $.get("consul/personapord.php", { idSecretaria: idSecretaria }, function (data) {
                $("#tablaBody").html(data);
                toggleSelectVisibility();
            });
        }
    });

    // Cargar unidades según la dirección seleccionada
    $("#direccion").change(function () {
        let idDireccion = $(this).val();
        $("#unidad, #area, #unidadsecre, #areasecre, #areadire, #areaunidad, #items").html('<option value="">--Seleccione--</option>').hide();

        if (idDireccion) {
            $.get("{{ route('unidades')}}", { idDireccion: idDireccion }, function (data) {
                $("#unidad").append(data);
                toggleSelectVisibility();
            });
            $.get("{{route('areasdireccion')}}", { idDireccion: idDireccion }, function (data) {
                $("#areadire").append(data);
                toggleSelectVisibility();
            });
            $.get("consul/puesto.php", { idDireccion: idDireccion }, function (data) {
                $("#items").append(data);
                toggleSelectVisibility();
            });
            $.get("consul/personapord.php", { idDireccion: idDireccion }, function (data) {
                $("#tablaBody").html(data);
                toggleSelectVisibility();
            });
        }
    });

    // Cargar áreas según la unidad de secretaría seleccionada
    $("#unidadsecre").change(function () {
        let idUnidad = $(this).val();
        $("#direccion, #unidad, #area, #areasecre, #areadire, #areaunidad, #items").html('<option value="">--Seleccione--</option>').hide();

        if (idUnidad) {
            $.get("{{ route('areas')}}", { idUnidad: idUnidad }, function (data) {
                $("#area").append(data);
                toggleSelectVisibility();
            });
            $.get("consul/puesto.php", { idUnidad: idUnidad }, function (data) {
                $("#items").append(data);
                toggleSelectVisibility();
            });
            $.get("consul/personapord.php", { idUnidad: idUnidad }, function (data) {
                $("#tablaBody").html(data);
                toggleSelectVisibility();
            });
        }
    });

    // Cargar áreas según la unidad seleccionada
    $("#unidad").change(function () {
        let idUnidad = $(this).val();
        $("#area, #areaunidad,  #areadire").html('<option value="">--Seleccione--</option>').hide();

        if (idUnidad) {
            $.get("{{ route('areas')}}", { idUnidad: idUnidad }, function (data) {
                $("#area").append(data);
                toggleSelectVisibility();
            });
            $.get("consul/puesto.php", { idUnidad: idUnidad }, function (data) {
                $("#items").append(data);
                toggleSelectVisibility();
            });
            $.get("consul/personapord.php", { idUnidad: idUnidad }, function (data) {
                $("#tablaBody").html(data);
                toggleSelectVisibility();
            });
        }
    });
    $("#area").change(function () {
        let idArea = $(this).val();
        $("#items").html('<option value="">--Seleccione--</option>').hide();

        if (idArea) {
            $.get("consul/puesto.php", { idArea: idArea }, function (data) {
                $("#items").append(data);
                toggleSelectVisibility();
            });
            $.get("consul/personapord.php", { idArea: idArea }, function (data) {
                $("#tablaBody").html(data);
                toggleSelectVisibility();
            });
        }
    });
    $("#areadire").change(function () {
        let idArea = $(this).val();

        $("#items").html('<option value="">--Seleccione--</option>').hide();

        if (idArea) {
            $.get("consul/puesto.php", { idArea: idArea }, function (data) {
                $("#items").append(data);
                toggleSelectVisibility();
            });
            $.get("consul/personapord.php", { idArea: idArea }, function (data) {
                $("#tablaBody").html(data);
                toggleSelectVisibility();
            });
        }
    });
    $("#areasecre").change(function () {
        let idArea = $(this).val();
        $("#items, #direccion, #unidadsecre").html('<option value="">--Seleccione--</option>').hide();

        if (idArea) {
            $.get("consul/puesto.php", { idArea: idArea }, function (data) {
                $("#items").append(data);
                toggleSelectVisibility();
            });
            $.get("consul/personapord.php", { idArea: idArea }, function (data) {
                $("#tablaBody").html(data);
                toggleSelectVisibility();
            });
        }
    });
});

</script>
@endsection
