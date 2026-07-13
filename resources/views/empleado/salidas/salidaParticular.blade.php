@extends('layouts.baseusr')

@section('comunicado')
    @php
        $ges = $gestion->first();
    @endphp
    @if ($ges)
        LISTA DE FERIADOS GESTIÓN : {{ $ges->anio }}
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th scope="col" class="col-md-7">Descripción</th>
                    <th scope="col" class="col-md-5">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($feriado as $fer)
                    <tr>
                        <td style="text-align: left">{{ $fer->descripcion }}</td>
                        <td>{{ $fer->fechaf }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        LISTA DE FERIADOS GESTIÓN :
    @endif
@endsection

@section('cuerpo')
    <div class="position-relative mt-3 mb-5">
        <div class="col-md-6 rounded bg-success text-white position-absolute start-50 translate-middle">
            <b> FORMULARIO DE AUTORIZACIÓN DE SALIDA PARTICULAR</b>
        </div>
    </div>
    <hr>
    <div class="container mt-2 rounded shadow bg-white">
        <div class="row">
            {{-- Datos del servidor --}}
            <h5 style="text-align: left"><span class="text-secondary">Servidor público</span></h5>
            <div class="col-md-6">
                <div class="form-floating mb-2 w-75">
                    <input type="text" class="form-control form-control-sm w-25" id="idserv"
                           value="{{ $persona->id ?? '' }}" hidden>
                    <label for="floatingInput">ID servidor</label>
                </div>
                <div class="form-floating mb-2 w-75">
                    <input type="text" class="form-control form-control-sm" id="nomb"
                           value="{{ $persona->nombre ?? '' }} {{ $persona->apellidoPat ?? '' }} {{ $persona->apellidoMat ?? '' }}"
                           disabled>
                    <label for="floatingInput">Nombres y apellidos</label>
                </div>
                <div class="form-floating mb-2 w-50">
                    <select class="form-select" id="salida" aria-label="Tipo de salida">
                        @foreach ($tipoSal as $sal)
                            @if ($sal->descripcion != 'VACACION' && $sal->descripcion != 'COMISION' && $sal->descripcion != 'SALUD')
                                <option value="{{ $sal->id }}">{{ $sal->descripcion }}</option>
                            @endif
                        @endforeach
                    </select>
                    <label for="salida">Tipo de salida</label>
                </div>
                <div class="form-floating mb-2 w-50">
                    <input type="date" class="form-control form-control-sm" id="fechasol"
                           placeholder="Fecha de solicitud">
                    <label for="fechasol">Fecha de solicitud (*)</label>
                </div>
                <div class="form-floating mb-2">
                    <textarea class="form-control" id="motivo" placeholder="Motivo"></textarea>
                    <label for="floatingInput">Motivo (*)</label>
                </div>
            </div>

            {{-- Fechas de salida y retorno --}}
            <div class="col-md-6">
                <div class="form-floating mb-2 w-50">
                    <input type="date" class="form-control form-control-sm" id="fsalida"
                           placeholder="Fecha de salida" required>
                    <label for="fsalida">Fecha salida (*)</label>
                </div>
                <div class="form-floating mb-2 w-25">
                    <input type="time" class="form-control form-control-sm" id="horasal"
                           placeholder="Hora salida" required>
                    <label for="floatingInput">Hora salida (*)</label>
                </div>
                <div class="form-floating mb-2 w-50">
                    <input type="date" class="form-control form-control-sm" id="fretorno"
                           placeholder="Fecha de retorno" required>
                    <label for="fretorno">Fecha retorno (*)</label>
                </div>
                <div class="form-floating mb-2 w-25">
                    <input type="time" class="form-control form-control-sm" id="horaret"
                           placeholder="Hora retorno" required>
                    <label for="floatingInput">Hora retorno (*)</label>
                </div>
            </div>

            <hr>

            {{-- Buscador de inmediato superior (sin Vue) --}}
            <div class="col-md-6">
                <h5 style="text-align: left"><span class="text-secondary">Buscar inmediato superior</span></h5>
                <div class="row">
                    <div class="col-md-8" style="text-align: left">
                        <div class="form-floating">
                            <input type="text" class="form-control form-control-sm" id="dato"
                                   placeholder="Nombre o apellido" required>
                            <label for="dato">Nombre o apellido</label>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2" style="text-align: left">
                        <button type="button" class="btn btn-success" id="btnBuscarSuperior">
                            <i class="fa-solid fa-magnifying-glass"></i> Buscar
                        </button>
                    </div>
                </div>
                <div class="row mx-1 mt-2" id="resultadosSuperior" style="text-align: left">
                    {{-- Resultados dinámicos --}}
                </div>
            </div>

            {{-- Datos para aprobar (superior asignado) --}}
            <div class="col-md-6">
                <h5 style="text-align: left"><span class="text-secondary">Datos para aprobar</span></h5>
                <div class="row mb-4" style="text-align: left">
                    <div class="col-md-0" style="text-align: left">
                        <input type="text" class="form-control form-control-sm" id="idSup" hidden>
                    </div>
                    <div class="col-md-8" style="text-align: left">
                        <div class="form-floating">
                            <input type="text" class="form-control form-control-sm" id="nombreSup"
                                   placeholder="Nombre inmediato superior" readonly required>
                            <label for="nombreSup">Nombre inmediato superior (*)</label>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            {{-- Botones --}}
            <div class="col-md-12" style="text-align: left">
                <button type="button" class="btn btn-primary mb-3 ms-3 w-25" id="solicitarCom">
                    <i class="fa-solid fa-floppy-disk"></i> Registrar Salida Particular
                </button>
                <a href="/homeusr" class="btn btn-secondary mb-3 ms-3">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </a>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Obtener fecha actual en formato YYYY-MM-DD
            const hoyStr = new Date().toISOString().split('T')[0];

            // 1. Fecha de solicitud = hoy
            document.getElementById('fechasol').value = hoyStr;

            // 2. Restringir fechas mínimas en los campos de salida y retorno
            const fsalida = document.getElementById('fsalida');
            const fretorno = document.getElementById('fretorno');
            fsalida.setAttribute('min', hoyStr);
            fretorno.setAttribute('min', hoyStr);

            // 3. Cuando cambie fsalida, actualizar el min de fretorno a esa fecha
            fsalida.addEventListener('change', function() {
                if (this.value) {
                    fretorno.setAttribute('min', this.value);
                    // Si la fecha de retorno es menor, limpiarla
                    if (fretorno.value && fretorno.value < this.value) {
                        fretorno.value = '';
                    }
                }
            });

            // 4. Buscar superiores (AJAX)
            document.getElementById('btnBuscarSuperior').addEventListener('click', buscarSuperior);
            document.getElementById('dato').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') buscarSuperior();
            });

            async function buscarSuperior() {
                const dato = document.getElementById('dato').value.trim();
                if (!dato) {
                    Swal.fire({ icon: 'error', text: 'Debe ingresar datos del inmediato superior' });
                    return;
                }

                try {
                    const response = await fetch(`/vacacion/buscar-superior?buscar=${encodeURIComponent(dato)}`);
                    const data = await response.json();
                    const contenedor = document.getElementById('resultadosSuperior');
                    contenedor.innerHTML = '';

                    if (data.length === 0) {
                        contenedor.innerHTML = '<p class="text-muted">No se encontraron resultados</p>';
                        return;
                    }

                    let tabla = `<table style="text-align: left">
                                    <tr><th>Nombre dependencia:</th><th></th></tr>`;
                    data.forEach(item => {
                        const nombreCompleto = `${item.nombre} ${item.apellidoPat} ${item.apellidoMat}`;
                        tabla += `<tr>
                                    <td>${nombreCompleto}</td>
                                    <td style="text-align: left">
                                        <button type="button" class="btn btn-success btn-sm"
                                                onclick="asignarSuperior(${item.id}, '${nombreCompleto}')">
                                            <i class="fa-solid fa-angles-right"></i>
                                        </button>
                                    </td>
                                  </tr>`;
                    });
                    tabla += '</table>';
                    contenedor.innerHTML = tabla;
                } catch (error) {
                    console.error('Error al buscar superior:', error);
                    Swal.fire({ icon: 'error', text: 'Error en la búsqueda' });
                }
            }

            // Función global para asignar superior
            window.asignarSuperior = function(id, nombre) {
                document.getElementById('idSup').value = id;
                document.getElementById('nombreSup').value = nombre;
                document.getElementById('resultadosSuperior').innerHTML = '';
                document.getElementById('dato').value = '';
            };

            // 5. Validación de fechas (incluyendo que no sean anteriores a hoy)
            document.getElementById('fechasol').addEventListener('change', compararFechas);
            document.getElementById('fsalida').addEventListener('change', compararFechas);
            document.getElementById('fretorno').addEventListener('change', compararFechas);

            function compararFechas() {
                const f0 = document.getElementById('fechasol').value;
                const f1 = document.getElementById('fsalida').value;
                const f2 = document.getElementById('fretorno').value;
                const hoy = new Date().toISOString().split('T')[0];

                // Validar que fsalida y fretorno no sean anteriores a hoy
                if (f1 && new Date(f1) < new Date(hoy)) {
                    Swal.fire({ text: 'La fecha de salida no puede ser anterior a hoy', icon: 'error' });
                    document.getElementById('fsalida').value = '';
                    return;
                }
                if (f2 && new Date(f2) < new Date(hoy)) {
                    Swal.fire({ text: 'La fecha de retorno no puede ser anterior a hoy', icon: 'error' });
                    document.getElementById('fretorno').value = '';
                    return;
                }

                // Validar que fecharet >= fechasal
                if (f1 && f2) {
                    const date1 = new Date(f1);
                    const date2 = new Date(f2);
                    if (date2 < date1) {
                        Swal.fire({
                            text: 'La fecha de retorno no puede ser menor que la fecha de salida',
                            icon: 'error'
                        });
                        document.getElementById('fretorno').value = '';
                        return;
                    }
                }

                // Validar que fechasol <= fsalida (opcional, pero puede ser útil)
                if (f0 && f1 && new Date(f0) > new Date(f1)) {
                    Swal.fire({
                        text: 'La fecha de solicitud no puede ser mayor a la fecha de salida',
                        icon: 'error'
                    });
                    document.getElementById('fsalida').value = '';
                    document.getElementById('fechasol').value = '';
                    return;
                }
            }

            // 6. Envío del formulario (registrar salida)
            document.getElementById('solicitarCom').addEventListener('click', async function() {
                const idserv = document.getElementById('idserv').value;
                const tipoSal = document.getElementById('salida').value;
                const fechasol = document.getElementById('fechasol').value;
                const motivo = document.getElementById('motivo').value;
                const fsalida = document.getElementById('fsalida').value;
                const horasal = document.getElementById('horasal').value;
                const fretorno = document.getElementById('fretorno').value;
                const horaret = document.getElementById('horaret').value;
                const idSup = document.getElementById('idSup').value;

                // Validar campos obligatorios
                if (!fechasol || !motivo || !fsalida || !horasal || !fretorno || !horaret || !idSup || !idserv) {
                    Swal.fire({ text: 'Debe llenar todos los campos obligatorios (*)', icon: 'info' });
                    return;
                }

                try {
                    const response = await axios.post('/particular/registrar', {
                        idserv,
                        tipoSal,
                        fechasol,
                        motivo,
                        fsalida,
                        horasal,
                        fretorno,
                        horaret,
                        idSup
                    });

                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: "Salida particular registrada",
                        timer: 1200,
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });

                    setTimeout(() => window.location.href = '/homeusr', 1100);
                } catch (error) {
                    console.error('Error al guardar:', error);
                    let mensaje = 'Ocurrió un error al registrar';
                    if (error.response && error.response.data && error.response.data.error) {
                        mensaje = error.response.data.error;
                    }
                    Swal.fire({ icon: 'error', text: mensaje });
                }
            });
        });
    </script>
@endsection