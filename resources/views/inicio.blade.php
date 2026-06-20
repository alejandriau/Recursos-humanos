<!DOCTYPE html>
<html lang="es">

<head>
    <script>
        localStorage.clear();
        //localStorage.removeItem('responseData');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Salidas</title>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/fontawesome.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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
    <div class="row justify-content-center mt-5 pt-5 mx-3">
        <div class="col-md-4"></div>
        <div class="col-md-4">

            <div class="border border-primary shadow p-3 mb-5 bg-body-tertiary rounded">

                <div id="app">
                    <div class="col-md-12 mb-5 mt-4">
                        <h4> <span class="text-secondary"> <i class="fa-solid fa-lock text-primary fs-4"></i>
                                Autenticación </span>
                        </h4>
                        <hr>
                    </div>
                    <div class="input-group flex-nowrap text-center mb-4 px-5">
                        <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-user"></i></span>
                        <input type="text" id="usuario" class="form-control" placeholder="Usuario"
                            aria-describedby="addon-wrapping" required>
                    </div>
                    <div class="input-group flex-nowrap text-center mb-5 px-5">
                        <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-key"></i></span>
                        <input type="password" id="password" class="form-control" placeholder="Password"
                            aria-describedby="addon-wrapping" required>

                    </div>
                    <div class="row" style="text-align: right">

                        <div class="col-md-6"><a href="">¿Olvido su contraseña?</a></div>
                        <div class="col-md-6"><button type="submit" id="conectar" class="btn btn-primary">
                                Iniciar session <i class="fa-solid fa-person-walking-arrow-right"></i></button></div>

                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-4"></div>
    </div>
    <nav class="navbar fixed-bottom navbar-expand-lg navbar-dark bg-dark ">
        <div class="container text-white d-flex justify-content-center ">Desarrollado por UGE © 2024 - GADC</div>
    </nav>
</body>

</html>
<script>
    document.getElementById('conectar').addEventListener('click', async () => {
        const usuario = document.getElementById('usuario').value;
        const password = document.getElementById('password').value;
        const token = "servidoresgadc12345";

        try {
            const response = await axios.post('/login-api', {
                    usuario: usuario,
                    password: password
                });
            console.log("RESPUESTA:", response.data);

            console.log('Respuesta del servidor:', response.data);

            if (response.data.code==200) {
                localStorage.setItem('responseData', JSON.stringify(response.data));
                location.href = '/validate';
            } else {
                Swal.fire({
                icon: "error",
                title:"DATOS INCORRECTOS",
                text: "Verifique tus credenciales e intente de nuevo"
            });
            document.getElementById('usuario').value="";
            document.getElementById('password').value="";
            }
            //location.href = '/verify';
            //alert('Conexión exitosa');
        } catch (error) {
            console.error('Error en la conexión:', error);
            alert('Error al conectar');
        }
    });
    console.log("hora");
    console.log(Vue);
</script>
