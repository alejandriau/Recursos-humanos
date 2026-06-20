<script>
    window.addEventListener('DOMContentLoaded', () => {
        const responseData = localStorage.getItem('responseData');
        if (!responseData) {
            location.href = '/';
        }
    });
</script>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Salidas</title>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/fontawesome.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="{{ asset('js/vue.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
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
    <div class="row  mt-1 justify-content-center fs-5 mx-3">
        FORMULARIO DE REGISTRO DE DATOS DEL PERSONAL
    </div>
    <hr>
    <div id="app" class="container">
        <div class="row mt-0">

            <div class="col-md-6">
                <div class="row justify-content-center bg-warning  text-dark mx-0 mb-3 fw-bolder">
                    DATOS PERSONALES
                </div>

                <div>
                    <div class="form-floating mb-2 w-50">
                        <input type="number" class="form-control form-control-sm" id="ci"
                            placeholder="Carnet de identidad">
                        <label for="floatingInput">Carnet de identidad (*)</label>
                    </div>
                    <div class="form-floating mb-2 w-75">
                        <input type="text" class="form-control form-control-sm" id="nomb" placeholder="Nombres"
                            disabled>
                        <label for="floatingInput">Nombres</label>

                    </div>
                    <div class="form-floating mb-2 w-75">
                        <input type="text" class="form-control form-control-sm" id="appat"
                            placeholder="Apellido paterno" disabled>
                        <label for="floatingInput">Apellido paterno</label>
                    </div>
                    <div class="form-floating mb-2 w-75">
                        <input type="text" class="form-control form-control-sm" id="apmat"
                            placeholder="Apellido materno" disabled>
                        <label for="floatingInput">Apellido materno</label>
                    </div>
                    <div class="form-floating mb-2 w-50">
                        <select class="form-select" id="genero" aria-label="Floating label select example">
                            <option selected></option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                        </select>
                        <label for="floatingSelect">Seleccione género (*)</label>
                    </div>
                    <div class="form-floating mb-2 w-25">
                        <input type="number" class="form-control form-control-sm" id="tel"
                            placeholder="Teléfono">
                        <label for="floatingInput">Teléfono</label>
                    </div>
                    <div class="form-floating mb-2 w-50">
                        <input type="date" class="form-control form-control-sm" id="fnac"
                            placeholder="Fecha de nacimiento">
                        <label for="floatingInput">Fecha de nacimiento (*)</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control form-control-sm" id="obs"
                            placeholder="observacion">
                        <label for="floatingInput">observacion</label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div
                    class="row justify-content-center justify-content-center bg-warning  text-dark  mx-0 mb-3 fw-bolder">
                    KARDEX DEL PERSONALES
                </div>
                <div>
                    <div class="form-floating mb-2 w-50">
                        <input type="number" class="form-control form-control-sm" id="idserv"
                            placeholder="Id servidor" disabled>
                        <label for="floatingInput">Id servidor</label>
                    </div>
                    <div class="form-floating mb-2 w-25">
                        <input type="number" class="form-control form-control-sm" id="item" placeholder="N° Item">
                        <label for="floatingInput">N° Item</label>
                    </div>
                    <div class="form-floating mb-2 w-75">
                        <select class="form-select" id="nivjer" aria-label="Floating label select example">
                            <option selected></option>
                            <option value="SECRETARIA(O) DEPARTAMENTAL ">SECRETARIA(O) DEPARTAMENTAL </option>
                            <option value="DIRECTORA(OR)">DIRECTORA(OR)</option>
                            <option value="JEFA(E) DE UNIDAD">JEFA(E) DE UNIDAD</option>
                            <option value="PROFESIONAL I">PROFESIONAL I</option>
                            <option value="PROFESIONAL II">PROFESIONAL II</option>
                            <option value="ADMINISTRATIVO I">ADMINISTRATIVO I</option>
                            <option value="ADMINISTRATIVO II">ADMINISTRATIVO II</option>
                            <option value="APOYO ADMINISTRATIVO I">APOYO ADMINISTRATIVO I</option>
                            <option value="APOYO ADMINISTRATIVO II">APOYO ADMINISTRATIVO II</option>
                            <option value="ASISTENTE">ASISTENTE</option>
                        </select>
                        <label for="floatingSelect">Nivel jerarquico</label>
                    </div>
                    <div class="form-floating mb-2 w-75">
                        <input type="text" class="form-control form-control-sm" id="cargo"
                            placeholder="Cargo" disabled>
                        <label for="floatingInput">Cargo</label>
                    </div>
                    <div class="form-floating mb-2 w-75">
                        <input type="text" class="form-control form-control-sm" id="dep"
                            placeholder="Dependencia" disabled>
                        <label for="floatingInput">Dependencia</label>
                    </div>
                    <div class="form-floating mb-2 w-50">
                        <input type="text" class="form-control form-control-sm" id="relLab"
                            placeholder="Relacion laboral">
                        <label for="floatingInput">Relacion laboral</label>
                    </div>
                    <div class="form-floating mb-2 w-50">
                        <input type="text" class="form-control form-control-sm" id="dirAdm"
                            placeholder="Relacion laboral">
                        <label for="floatingInput">Direccion administrativa</label>
                    </div>
                    <div class="form-floating mb-2 w-50">
                        <input type="date" class="form-control form-control-sm" id="fing"
                            placeholder="Fecha de ingreso">
                        <label for="floatingInput">Fecha de ingreso</label>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" id="guardar" class="btn btn-warning mb-3 ms-3 w-25">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Guardar
                </button>
                <a href="/" class="btn btn-info mb-3 ms-3">
                    <i class="fa-solid fa-xmark"></i>
                    Cancelar
                </a>
            </div>
        </div>


    </div>
    <hr>
    <nav class="navbar fixed-bottom navbar-expand-lg navbar-dark bg-dark ">
        <div class="container text-white d-flex justify-content-center ">Desarrollado por UGE © 2024 - GADC</div>
    </nav>
</body>

</html>
<script>
    // Recuperar los datos almacenados
    const storedData = localStorage.getItem('responseData');
    console.log(storedData);
    // Convertir de JSON a objeto
    const parsedData = storedData ? JSON.parse(storedData) : "No hay datos almacenados.";
    
    //asigna los valores al formulario de localstorage
    document.getElementById("ci").value = parsedData.data[0].ci;
    document.getElementById("nomb").value = parsedData.data[0].nombres;
    document.getElementById("appat").value = parsedData.data[0].paterno;
    document.getElementById("apmat").value = parsedData.data[0].materno;
    document.getElementById("tel").value = parsedData.data[0].celular;
    document.getElementById("idserv").value = parsedData.data[0].idServidor;
    document.getElementById("cargo").value = parsedData.data[0].cargo;
    document.getElementById("dep").value = parsedData.data[0].dependencia;

    // datos para enviar al API
    document.getElementById('guardar').addEventListener('click', async () => {
        const idserv = document.getElementById('idserv').value;
        const ci = document.getElementById('ci').value;
        const nomb = document.getElementById('nomb').value;
        const appat = document.getElementById('appat').value;
        const apmat = document.getElementById('apmat').value;
        const genero = document.getElementById('genero').value;
        const tel = document.getElementById('tel').value;
        const fnac = document.getElementById('fnac').value;
        const obs = document.getElementById('obs').value;
        // ***************** bloque kardex del formulario *************************
        const item = document.getElementById('item').value;
        const nivjer = document.getElementById('nivjer').value;
        const cargo = document.getElementById('cargo').value;
        const dep = document.getElementById('dep').value;
        const relLab = document.getElementById('relLab').value;
        const dirAdm = document.getElementById('dirAdm').value;
        const fing = document.getElementById('fing').value;
        if (genero === "" || ci === "" || fnac === "") {
            Swal.fire({
                text: "Debe llenar los datos obligatorios (*)",
                icon: "info"
            });
            return false;
        }
        try {
            const response = await axios.post(
                '/form/guardar-personal', {
                    idserv: idserv,
                    ci: ci,
                    nomb: nomb,
                    appat: appat,
                    apmat: apmat,
                    genero: genero,
                    tel: tel,
                    fnac: fnac,
                    obs: obs,
                    item:item,
                    nivjer:nivjer,
                    cargo:cargo,
                    dep:dep,
                    relLab:relLab,
                    fing:fing

                });

            console.log('Respuesta del servidor:', response.data);
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 1200,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "Datos registrados",
            });
            setTimeout(function() {
                window.location.href = '/homeusr';
            }, 1200);
        } catch (error) {
            console.error('Error al guardar:', error);

        }
        
    });
</script>

