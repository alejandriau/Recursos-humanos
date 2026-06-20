@extends('layouts.baseusr')

@section('comunicado')
    @if ($gestion->first())
        @php $anio = $gestion->first()->anio; @endphp
        <div class="alert alert-success" role="alert">
            <div class="row justify-content-start">
                <div>
                    <b><i class="fa-solid fa-calendar-days me-2 fs-5"></i> FERIADOS GESTIÓN: {{ $anio }}</b>
                    <span class="badge bg-info text-white">{{ count($feriado) }} feriado(s)</span>
                </div>
            </div>
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
    <div class="alert alert-primary" role="alert">
        <h5 class="text-center">Formulario de Solicitud de Comisión</h5>
    </div>
    <div id="app">
        <div class="row">
            <div class="col-md-6">
                <!-- Usamos el id de la persona -->
                <input type="text" id="idpersona" value="{{ $persona->id }}" hidden>
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="nomb" 
                           value="{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}" disabled>
                    <label>Servidor Público:</label>
                </div>
                <div class="form-floating mb-2">
                    <select class="form-select" id="salida" disabled>
                        @foreach ($tipoSal as $sal)
                            @if ($sal->descripcion == 'COMISION')
                                <option value="{{ $sal->id }}">{{ $sal->descripcion }}</option>
                            @endif
                        @endforeach
                    </select>
                    <label for="salida">Tipo de salida:</label>
                </div>
                <div class="form-floating mb-2">
                    <input type="date" id="fechasol" class="form-control" readonly 
                           value="{{ now()->toDateString() }}">
                    <label for="fechasol">Fecha de solicitud:</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-2">
                            <input type="text" id="fsalida" class="form-control">
                            <label for="fsalida">Fecha de salida:</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="time" id="horasal" class="form-control">
                            <label for="horasal">Hora de salida:</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-2">
                            <input type="text" id="fretorno" class="form-control">
                            <label for="fretorno">Fecha de retorno:</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="time" id="horaret" class="form-control">
                            <label for="horaret">Hora de retorno:</label>
                        </div>
                    </div>
                </div>
                <div class="form-floating mb-2">
                    <textarea class="form-control" id="motivo" rows="3" placeholder="Describa el motivo de la comisión"></textarea>
                    <label for="motivo">Motivo de la comisión:</label>
                </div>
            </div>
        </div>
        <hr>
        {{-- BLOQUE DE BÚSQUEDA DEL INMEDIATO SUPERIOR --}}
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="row mb-3">
                    <div class="col-md-10">
                        <div class="form-floating">
                            <input type="text" id="dato" class="form-control" placeholder="Nombre o apellido" required>
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
                    <input type="text" id="idSup" hidden>
                    <div class="form-floating mb-2">
                        <input type="text" id="nombreSup" class="form-control" readonly>
                        <label for="nombreSup">Inmediato Superior Seleccionado:</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <table class="table" id="tablaSuperiores">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tbodySuperiores">
                            <!-- Se llena dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-start mt-3">
            <button class="btn btn-primary me-2" id="solicitarCom"><i class="fa fa-save"></i> Registrar Comisión</button>
            <a href="/homeusr" class="btn btn-secondary"><i class="fa fa-times"></i> Cancelar</a>
        </div>
    </div>
</div>

{{-- Flatpickr --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ----- Fechas inhábiles (feriados) -----
        const feriados = @json($feriado->pluck('fechaf')->map(fn($f) => \Carbon\Carbon::parse($f)->format('Y-m-d')));

        const fechasFeriado = feriados.map(f => {
            const [y, m, d] = f.split("-").map(Number);
            return new Date(y, m - 1, d);
        });

        const deshabilitarFechas = (date) => {
            const esFinDeSemana = date.getDay() === 0 || date.getDay() === 6;
            const esFeriado = fechasFeriado.some(f => f.toDateString() === date.toDateString());
            return esFinDeSemana || esFeriado;
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

        // ----- Búsqueda del superior -----
        const datoInput = document.getElementById("dato");
        const btnBuscar = document.getElementById("btnBuscarSup");
        const tbody = document.getElementById("tbodySuperiores");
        const idSupHidden = document.getElementById("idSup");
        const nombreSupInput = document.getElementById("nombreSup");

        function buscarSuperiores() {
            const buscar = datoInput.value.trim();
            if (!buscar) {
                Swal.fire("Advertencia", "Debe ingresar un nombre o apellido antes de buscar", "warning");
                return;
            }

            tbody.innerHTML = '<tr><td colspan="2" class="text-center">Buscando...</td></tr>';

            axios.get('/vacacion/buscar-superior', { params: { buscar: buscar } })
                .then(res => {
                    const data = res.data;
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="2" class="text-center">No se encontraron resultados</td></tr>';
                        return;
                    }
                    let html = '';
                    data.forEach(p => {
                        const nombreCompleto = `${p.nombre} ${p.apellidoPat} ${p.apellidoMat}`;
                        html += `
                            <tr>
                                <td>${nombreCompleto}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm asignar-sup" data-id="${p.id}" data-nombre="${nombreCompleto}">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;

                    document.querySelectorAll(".asignar-sup").forEach(btn => {
                        btn.addEventListener("click", function() {
                            idSupHidden.value = this.dataset.id;
                            nombreSupInput.value = this.dataset.nombre;
                            tbody.innerHTML = '';
                            datoInput.value = '';
                        });
                    });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire("Error", "No se pudo completar la búsqueda", "error");
                });
        }

        btnBuscar.addEventListener("click", buscarSuperiores);
        datoInput.addEventListener("keyup", function(e) {
            if (e.key === "Enter") buscarSuperiores();
        });

        // ----- Registrar comisión -----
    document.getElementById("solicitarCom").addEventListener("click", async function() {
        const idpersona = document.getElementById("idpersona").value;
        const tipoSal = document.getElementById("salida").value;
        const fechasol = document.getElementById("fechasol").value;
        const fsalida = document.getElementById("fsalida").value;
        const fretorno = document.getElementById("fretorno").value;
        const horasal = document.getElementById("horasal").value;
        const horaret = document.getElementById("horaret").value;
        const motivo = document.getElementById("motivo").value;
        const idSup = document.getElementById("idSup").value;

        // Validación básica en frontend
        if (!idpersona || !tipoSal || !fechasol || !fsalida || !fretorno || !horasal || !horaret || !motivo || !idSup) {
            Swal.fire("Faltan datos", "Complete todos los campos obligatorios", "warning");
            return;
        }

        const data = {
            idpersona: idpersona,
            tipoSal: tipoSal,
            fechasol: fechasol,
            fsalida: fsalida,
            fretorno: fretorno,
            horasal: horasal,
            horaret: horaret,
            motivo: motivo,
            idSup: idSup
        };

        try {
            const res = await axios.post('/comision/registrar', data);
            Swal.fire("Éxito", res.data.mensaje || "Solicitud registrada correctamente", "success");
            setTimeout(() => window.location.href = "/homeusr", 1000);
        } catch (err) {
            console.error("Error completo:", err);
            console.error("Datos de respuesta:", err.response?.data);

            let mensaje = "No se pudo registrar. Intente nuevamente.";

            if (err.response) {
                const data = err.response.data;
                if (data.error) {
                    mensaje = data.error;
                } else if (data.message && data.errors) {
                    const firstError = Object.values(data.errors)[0]?.[0];
                    mensaje = firstError || data.message;
                } else if (data.message) {
                    mensaje = data.message;
                }
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire("Error", mensaje, "error");
            } else {
                alert("Error: " + mensaje);
            }
        }
    });
    });
</script>
@endsection