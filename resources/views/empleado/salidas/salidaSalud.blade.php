@extends('layouts.baseusr')

@section('comunicado')
@if ($gestion->first())
    @php $anio = $gestion->first()->anio; @endphp
    <div class="alert alert-success" role="alert">
        <b><i class="fa-solid fa-calendar-days me-2 fs-5"></i> FERIADOS GESTIÓN: {{ $anio }}</b>
        <span class="badge bg-info text-white">{{ count($feriado) }} feriado(s)</span>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Descripción</th>
                        <th scope="col">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($feriado as $fer)
                        <tr>
                            <td><i class="fa-solid fa-calendar-days me-2 text-warning"></i>{{ $fer->descripcion }}</td>
                            <td>{{ \Carbon\Carbon::parse($fer->fechaf)->format('d-m-Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <p>FERIADOS GESTIÓN: Ninguno disponible</p>
@endif
@endsection

@section('cuerpo')
<div class="container mt-4 p-4 bg-white shadow rounded">
    <div class="alert alert-danger text-center">
        <h5>Formulario de Solicitud de Salida Médica</h5>
    </div>

    <div class="row">
        <div class="col-md-6">
            {{-- ID de la persona (servidor) --}}
            <input type="text" id="idserv" value="{{ $persona->id ?? '' }}" hidden>
            <div class="form-floating mb-2">
                <input type="text" class="form-control" id="nomb"
                       value="{{ $persona->nombre ?? '' }} {{ $persona->apellidoPat ?? '' }} {{ $persona->apellidoMat ?? '' }}"
                       disabled>
                <label>Servidor Público:</label>
            </div>
            <div class="form-floating mb-2">
                <select class="form-select" id="salida" disabled>
                    @foreach ($tipoSal as $sal)
                        @if ($sal->descripcion == 'SALUD')
                            <option value="{{ $sal->id }}">{{ $sal->descripcion }}</option>
                        @endif
                    @endforeach
                </select>
                <label for="salida">Tipo de salida:</label>
            </div>
            <div class="form-floating mb-2">
                <input type="date" id="fechasol" class="form-control" readonly>
                <label for="fechasol">Fecha de solicitud:</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-2">
                        <input type="text" id="fsalida" class="form-control" placeholder="Seleccione fecha">
                        <label for="fsalida">Fecha de salida:</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="time" id="horasal" class="form-control">
                        <label for="horasal">Hora de salida:</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-2">
                        <input type="text" id="fretorno" class="form-control" placeholder="Seleccione fecha">
                        <label for="fretorno">Fecha de retorno:</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="time" id="horaret" class="form-control">
                        <label for="horaret">Hora de retorno:</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    {{-- Búsqueda del inmediato superior (sin Vue) --}}
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="row mb-3">
                <div class="col-md-10">
                    <div class="form-floating">
                        <input type="text" id="dato" class="form-control" placeholder="Nombre o apellido">
                        <label for="dato">Buscar inmediato superior (nombre o apellido):</label>
                    </div>
                </div>
                <div class="col-md-2 text-start">
                    <button class="btn btn-success" id="btnBuscarSuperior"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <input type="text" id="idSup" hidden>
            <div class="form-floating mb-2">
                <input type="text" id="nombreSup" class="form-control" readonly>
                <label for="nombreSup">Inmediato Superior Seleccionado:</label>
            </div>
        </div>
    </div>

    {{-- Tabla de resultados de búsqueda --}}
    <div class="row">
        <div class="col-md-6">
            <table class="table" id="tablaResultados">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="cuerpoResultados">
                    {{-- Se llenará dinámicamente --}}
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-start mt-3">
        <button class="btn btn-primary me-2" id="solicitarSalud"><i class="fa fa-save"></i> Registrar Salida Médica</button>
        <a href="/homeusr" class="btn btn-secondary"><i class="fa fa-times"></i> Cancelar</a>
    </div>
</div>

{{-- Incluir Flatpickr para fechas --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Datos desde el backend (persona) ya está en los inputs ---
        // Fecha actual para solicitud
        const hoy = new Date().toISOString().split('T')[0];
        document.getElementById('fechasol').value = hoy;

        // --- Configurar Flatpickr con restricción de feriados y fines de semana ---
        const feriados = @json($feriado->pluck('fechaf')->map(fn($f) => \Carbon\Carbon::parse($f)->format('Y-m-d')));

        // Función para deshabilitar fines de semana y feriados
        const deshabilitarFechas = function(date) {
            // Domingo (0) o Sábado (6)
            if (date.getDay() === 0 || date.getDay() === 6) return true;
            // Comparar con feriados (fechas en formato Y-m-d)
            const dateStr = date.getFullYear() + '-' +
                            String(date.getMonth() + 1).padStart(2, '0') + '-' +
                            String(date.getDate()).padStart(2, '0');
            return feriados.includes(dateStr);
        };

        flatpickr("#fsalida", {
            minDate: "today",
            disable: [deshabilitarFechas],
            locale: "es",
            dateFormat: "Y-m-d"
        });

        flatpickr("#fretorno", {
            minDate: "today",
            disable: [deshabilitarFechas],
            locale: "es",
            dateFormat: "Y-m-d"
        });

        // --- Búsqueda de superiores (AJAX) ---
        const btnBuscar = document.getElementById('btnBuscarSuperior');
        const inputDato = document.getElementById('dato');
        const tbody = document.getElementById('cuerpoResultados');

        function buscarSuperior() {
            const dato = inputDato.value.trim();
            if (!dato) {
                Swal.fire('Advertencia', 'Debe ingresar un nombre o apellido para buscar', 'warning');
                return;
            }

            fetch(`/vacacion/buscar-superior?buscar=${encodeURIComponent(dato)}`)
                .then(res => res.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="2" class="text-muted">No se encontraron resultados</td></tr>';
                        return;
                    }
                    data.forEach(persona => {
                        const nombre = `${persona.nombre} ${persona.apellidoPat} ${persona.apellidoMat}`;
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${nombre}</td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="asignarSuperior(${persona.id}, '${nombre}')">
                                    <i class="fa fa-check"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'No se pudo realizar la búsqueda', 'error');
                });
        }

        btnBuscar.addEventListener('click', buscarSuperior);
        inputDato.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') buscarSuperior();
        });

        // Función global para asignar superior
        window.asignarSuperior = function(id, nombre) {
            document.getElementById('idSup').value = id;
            document.getElementById('nombreSup').value = nombre;
            // Limpiar tabla de resultados
            tbody.innerHTML = '';
            inputDato.value = '';
        };

        // --- Envío del formulario ---
        document.getElementById('solicitarSalud').addEventListener('click', async function() {
            const idserv = document.getElementById('idserv').value;
            const tipoSal = document.getElementById('salida').value;
            const fechasol = document.getElementById('fechasol').value;
            const fsalida = document.getElementById('fsalida').value;
            const horasal = document.getElementById('horasal').value;
            const fretorno = document.getElementById('fretorno').value;
            const horaret = document.getElementById('horaret').value;
            const idSup = document.getElementById('idSup').value;

            if (!idserv || !tipoSal || !fechasol || !fsalida || !horasal || !fretorno || !horaret || !idSup) {
                Swal.fire('Faltan datos', 'Complete todos los campos obligatorios', 'warning');
                return;
            }

            try {
                const response = await axios.post('/vacacion/registrar-salida-salud', {
                    idserv,
                    tipoSal,
                    fechasol,
                    fsalida,
                    horasal,
                    fretorno,
                    horaret,
                    idSup
                });
                Swal.fire('Éxito', response.data.mensaje || 'Salida médica registrada', 'success');
                setTimeout(() => window.location.href = '/homeusr', 1000);
            } catch (error) {
                let mensaje = 'No se pudo registrar';
                if (error.response && error.response.data && error.response.data.error) {
                    mensaje = error.response.data.error;
                }
                Swal.fire('Error', mensaje, 'error');
            }
        });
    });
</script>
@endsection