<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Salidas</title>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/fontawesome.js') }}"></script>
    <script src="{{ asset('/public/js/toastr.min.js') }}"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color:#2874A6;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="{{ URL::asset('img/cbba.png') }}" height="55px"
                    alt="" /></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link fs-5 text-white" aria-current="page" href="#">
                        SISTEMA DE SALIDAS<br>Unidad de Gestion de Recursos Humanos
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12  text-center">@yield('cuerpo')</div>
        </div>
    </div>
    <nav class="navbar fixed-bottom navbar-expand-lg navbar-dark bg-dark ">
        <div class="container text-white d-flex justify-content-center ">Desarrollado por UGE © 2024 - GADC</div>
    </nav>
</body>

</html>
