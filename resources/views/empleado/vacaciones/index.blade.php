@extends('layouts.baseusr')

@section('comunicado')
    @if ($gestion->first())
        @php $anio = $gestion->first()->anio; @endphp
        <div class="alert alert-success" role="alert">
            <div class="row justify-content-start">
                <div>
                    <b><i class="fa-solid fa-calendar-days me-2 fs-5"></i> FERIADOS GESTION: {{ $anio }}</b>
                    <span class="badge bg-info text-white">{{ count($feriado) }} feriado(s)</span>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="col-md-7">Descripción</th>
                            <th scope="col" class="col-md-5">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($feriado as $fer)
                            <tr>
                                <td class="text-start">
                                    <i class="fa-solid fa-calendar-days me-2 text-warning"></i>
                                    {{ $fer->descripcion }}
                                </td>
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

        <div class="alert alert-success" role="alert">
            <h5 class="text-center">Formulario de Solicitud de Vacación</h5>
        </div>

        {{-- Contenedor principal sin Vue --}}
        <div id="app">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        {{-- Campo oculto con el ID de la persona autenticada --}}
                        <input type="hidden" id="idpersona" value="{{ $persona->id }}">
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control form-control-sm" id="nomb"
                                   placeholder="Nombres y apellidos" disabled
                                   value="{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}">
                            <label for="nomb">Servidor Público:</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-floating mb-2">
                            <select class="form-select" id="salida" disabled>
                                @foreach ($tipoSal as $sal)
                                    @if ($sal->descripcion == 'VACACION')
                                        <option value="{{ $sal->id }}">{{ $sal->descripcion }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <label for="salida">Tipo de salida:</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-floating mb-2">
                            <input type="date" id="fechasol" class="form-control" required readonly>
                            <label for="fechasol">Fecha de solicitud:</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-floating mb-2">
                                    <input type="text" id="fsalida" class="form-control" required>
                                    <label for="fsalida">Inicio vacación:</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-floating mb-2">
                                    <input type="text" id="fretorno" class="form-control" required>
                                    <label for="fretorno">Fin vacación:</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-floating mb-2">
                                    <input type="text" id="totaldias" class="form-control border-warning" readonly>
                                    <label for="totaldias">Cantidad de días solicitadas:</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="mdia">
                                <label class="form-check-label" for="mdia">Medio día</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 form-floating">
                        <input type="text" id="observacion" class="form-control border border-warning"
                               placeholder="Observacion" required>
                        <label for="observacion">Observaciones:</label>
                    </div>
                    <div class="bg-secondary text-white p-2">
                        <p><strong>Días disponibles de vacación:</strong> <span id="diasDisponiblesSpan"></span></p>
                    </div>
                </div>
            </div>
            <hr class="mt-1">

            {{-- Búsqueda de superior --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="row mb-3">
                        <div class="col-md-10">
                            <div class="form-floating">
                                <input type="text" id="dato" class="form-control" placeholder="Nombre o apellido"
                                       required>
                                <label for="dato">Buscar inmediato superior (nombre o apellido):</label>
                            </div>
                        </div>
                        <div class="col-md-2 text-start">
                            <button class="btn btn-success" id="btnBuscarSup"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <input type="hidden" id="idSup">
                        <div class="form-floating mb-2">
                            <input type="text" id="nombreSup" class="form-control" readonly>
                            <label for="nombreSup">Inmediato Superior Seleccionado:</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de resultados de búsqueda --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tablaSup">
                                <!-- Aquí se cargan dinámicamente los resultados -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-start">
                <button class="btn btn-primary me-2" id="solicitarVac"><i class="fa fa-save"></i> Registrar Vacación</button>
                <a href="/homeusr" class="btn btn-secondary"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    </div>

    {{-- Flatpickr para fechas --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    {{-- Script principal con JS puro --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Datos de feriados desde PHP
            const feriados = @json($feriado->pluck('fechaf')->map(fn($f) => \Carbon\Carbon::parse($f)->format('Y-m-d')));

            // Obtener ID de la persona autenticada (desde el campo oculto)
            const idPersona = document.getElementById('idpersona').value;

            // Función para parsear fecha local (YYYY-MM-DD)
            function parseFechaLocal(fechaStr) {
                const [anio, mes, dia] = fechaStr.split('-').map(Number);
                return new Date(anio, mes - 1, dia);
            }

            // Calcular días hábiles entre dos fechas, excluyendo fines de semana y feriados
            function calcularDias(finicio, ffin, feriados, medioDia) {
                const start = parseFechaLocal(finicio);
                const end = parseFechaLocal(ffin);
                if (isNaN(start) || isNaN(end) || end < start) return 0;

                let total = 0;
                const temp = new Date(start);
                while (temp <= end) {
                    const day = temp.getDay();
                    const strFecha = temp.toISOString().split('T')[0];
                    const esFeriado = feriados.includes(strFecha);
                    if (day !== 0 && day !== 6 && !esFeriado) {
                        total++;
                    }
                    temp.setDate(temp.getDate() + 1);
                }

                if (medioDia) {
                    total = Math.max(total - 0.5, 0);
                }
                return total;
            }

            // Recalcular días cada vez que cambian las fechas o el checkbox de medio día
            function calcularDiasGlobal() {
                const fsalida = document.getElementById('fsalida').value;
                const fretorno = document.getElementById('fretorno').value;
                const medioDia = document.getElementById('mdia').checked;

                if (!fsalida || !fretorno) {
                    document.getElementById('totaldias').value = '';
                    return;
                }

                const total = calcularDias(fsalida, fretorno, feriados, medioDia);
                document.getElementById('totaldias').value = total;
            }

            // Cargar días disponibles desde el servidor
            function cargarDiasDisponibles() {
                axios.get('/vacacion/dias-disponibles', {
                        params: { idpersona: idPersona }
                    })
                    .then(response => {
                        // Validar que el span exista antes de asignar
                        const spanDias = document.getElementById('diasDisponiblesSpan');
                        if (spanDias) {
                            spanDias.textContent = response.data.dias;
                        } else {
                            console.error('Elemento #diasDisponiblesSpan no encontrado');
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar días disponibles:', error);
                    });
            }

            // Inicializar fecha de solicitud con hoy
            const fechasol = document.getElementById('fechasol');
            if (fechasol) {
                fechasol.value = new Date().toISOString().split('T')[0];
            }

            // Configurar Flatpickr para inicio y fin
            const fechasFeriado = feriados.map(f => {
                const [y, m, d] = f.split('-').map(Number);
                return new Date(y, m - 1, d);
            });

            const deshabilitarFechas = (date) => {
                const esFinDeSemana = date.getDay() === 0 || date.getDay() === 6;
                const esFeriado = fechasFeriado.some(f => f.toDateString() === date.toDateString());
                return esFinDeSemana || esFeriado;
            };

            const fsalidaInput = document.getElementById('fsalida');
            const fretornoInput = document.getElementById('fretorno');

            if (fsalidaInput) {
                flatpickr('#fsalida', {
                    minDate: 'today',
                    disable: [deshabilitarFechas],
                    locale: 'es',
                    dateFormat: 'Y-m-d',
                    onChange: calcularDiasGlobal
                });
            }

            if (fretornoInput) {
                flatpickr('#fretorno', {
                    minDate: 'today',
                    disable: [deshabilitarFechas],
                    locale: 'es',
                    dateFormat: 'Y-m-d',
                    onChange: calcularDiasGlobal
                });
            }

            // Evento para medio día
            const mdiaCheckbox = document.getElementById('mdia');
            if (mdiaCheckbox) {
                mdiaCheckbox.addEventListener('change', calcularDiasGlobal);
            }

            // Cargar días disponibles al inicio
            cargarDiasDisponibles();

            // --- Búsqueda de superior (sin Vue) ---
            const btnBuscar = document.getElementById('btnBuscarSup');
            const inputBuscar = document.getElementById('dato');
            const tablaSup = document.getElementById('tablaSup');

            function buscarPersonal() {
                const buscar = inputBuscar.value.trim();
                if (!buscar) {
                    Swal.fire('Advertencia', 'Debe ingresar un nombre o apellido antes de buscar', 'warning');
                    return;
                }

                axios.get('/vacacion/buscar-superior', {
                        params: { buscar: buscar }
                    })
                    .then(response => {
                        const personas = response.data;
                        tablaSup.innerHTML = '';
                        if (personas.length === 0) {
                            tablaSup.innerHTML =
                                '<tr><td colspan="2" class="text-center">No se encontraron personas</td></tr>';
                            return;
                        }
                        personas.forEach(p => {
                            const tr = document.createElement('tr');
                            const nombreCompleto = `${p.nombre} ${p.apellidoPat} ${p.apellidoMat}`;
                            tr.innerHTML = `
                                <td>${nombreCompleto}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm asignar-sup" 
                                            data-id="${p.id}" 
                                            data-nombre="${nombreCompleto}">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </td>
                            `;
                            tablaSup.appendChild(tr);
                        });

                        // Asignar evento a cada botón "asignar"
                        document.querySelectorAll('.asignar-sup').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const id = this.dataset.id;
                                const nombre = this.dataset.nombre;
                                document.getElementById('idSup').value = id;
                                document.getElementById('nombreSup').value = nombre;
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Error en búsqueda:', error);
                        Swal.fire('Error', 'Ocurrió un problema al buscar', 'error');
                    });
            }

            if (btnBuscar) {
                btnBuscar.addEventListener('click', buscarPersonal);
            }
            if (inputBuscar) {
                inputBuscar.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        buscarPersonal();
                    }
                });
            }

            // --- Registro de solicitud ---
            const solicitarBtn = document.getElementById('solicitarVac');
            if (solicitarBtn) {
                solicitarBtn.addEventListener('click', function() {

                    // Validar que el meta CSRF exista
                    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    let token = '';
                    if (tokenMeta) {
                        token = tokenMeta.content;
                    } else {
                        console.error('No se encontró el meta tag CSRF');
                        Swal.fire('Error', 'Falta el token CSRF. Por favor, recargue la página.', 'error');
                        return;
                    }

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/vacacion/registrar-vacacion';

                    const datos = {
                        _token: token,
                        persona_id: idPersona,
                        tipoSal: document.getElementById('salida').value,
                        fechasol: document.getElementById('fechasol').value,
                        fsalida: document.getElementById('fsalida').value,
                        fretorno: document.getElementById('fretorno').value,
                        totaldias: document.getElementById('totaldias').value,
                        idSup: document.getElementById('idSup').value,
                        observacion: document.getElementById('observacion').value
                    };

                    for (const key in datos) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = datos[key];
                        form.appendChild(input);
                    }

                    document.body.appendChild(form);
                    form.submit();
                });
            }


                    console.log(idPersona);
                             console.log(document.getElementById('salida').value),
                         console.log(document.getElementById('fechasol').value),
                     console.log(document.getElementById('fsalida').value),
                         console.log(document.getElementById('fretorno').value),
                        console.log(document.getElementById('totaldias').value),
             console.log(document.getElementById('idSup').value),
                        console.log(document.getElementById('observacion').value)
        });

    </script>
@endsection