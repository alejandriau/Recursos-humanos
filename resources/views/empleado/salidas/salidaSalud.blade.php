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
                        <tr><th>Descripción</th><th>Fecha</th></tr>
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
<div class="row">
    <div class="col-md-10">
        <div class="container mt-4 p-4 bg-white shadow rounded">
            <div class="alert alert-danger text-center">
                <h5>Formulario de Solicitud de Salida Médica</h5>
            </div>

            {{-- Botón nuevo --}}
            <div class="text-start mb-3">
                <button class="btn btn-success" id="btnNuevaSalud">
                    <i class="fa fa-plus"></i> Nueva Solicitud Médica
                </button>
                <a href="/homeusr" class="btn btn-secondary"><i class="fa fa-times"></i> Cancelar</a>
            </div>

            {{-- Listado de solicitudes --}}
            <div class="mt-4">
                <h5>Mis solicitudes médicas</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaSalud">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Fecha salida</th>
                                <th>Hora salida</th>
                                <th>Fecha retorno</th>
                                <th>Hora retorno</th>
                                <th>Cantidad</th>
                                <th>Motivo</th>
                                <th>🧑‍💼 Jefe Inmediato</th>
                                <th>🏢 RRHH</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodySalud">
                            @foreach($solicitudes as $sol)
                                @php
                                    $estadoJefe = strtolower($sol->estado_jefe ?? '');
                                    $estadoRRHH = strtolower($sol->estado_rrhh ?? '');
                                @endphp
                                <tr id="fila-{{ $sol->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sol->fechasal)->format('d-m-Y') }}</td>
                                    <td>{{ $sol->horasal }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sol->fecharet)->format('d-m-Y') }}</td>
                                    <td>{{ $sol->horaret }}</td>
                                    <td>{{ $sol->cantidad }}</td>
                                    <td style="max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $sol->motivo }}">
                                        {{ Str::limit($sol->motivo, 30) }}
                                    </td>

                                    {{-- ===== JEFE INMEDIATO ===== --}}
                                    <td>
                                        @php
                                            $badgeJefe = 'bg-secondary';
                                            $textoJefe = 'Sin estado';
                                            $iconoJefe = 'fa-question-circle';

                                            if ($estadoJefe === 'pendiente') {
                                                $badgeJefe = 'bg-warning text-dark'; $textoJefe = 'Pendiente'; $iconoJefe = 'fa-clock';
                                            } elseif ($estadoJefe === 'aprobado') {
                                                $badgeJefe = 'bg-success'; $textoJefe = 'Aprobado'; $iconoJefe = 'fa-check-circle';
                                            } elseif ($estadoJefe === 'rechazado') {
                                                $badgeJefe = 'bg-danger'; $textoJefe = 'Rechazado'; $iconoJefe = 'fa-times-circle';
                                            } elseif (empty($estadoJefe)) {
                                                $badgeJefe = 'bg-light text-dark border'; $textoJefe = 'Sin asignar'; $iconoJefe = 'fa-user-slash';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeJefe }} py-2 px-3 mb-1 d-inline-block">
                                            <i class="fas {{ $iconoJefe }} me-1"></i>{{ $textoJefe }}
                                        </span>
                                        <div class="small mt-1">
                                            @if($sol->jefe)
                                                <i class="fas fa-user-tie me-1 text-muted"></i>
                                                {{ $sol->jefe->nombre }} {{ $sol->jefe->apellidoPat }}
                                                @if($sol->fecha_aprobacion_jefe)
                                                    <br>
                                                    <span class="text-muted" style="font-size: 0.75rem;">
                                                        <i class="far fa-calendar-alt me-1"></i>
                                                        {{ \Carbon\Carbon::parse($sol->fecha_aprobacion_jefe)->format('d/m/Y H:i') }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted fst-italic" style="font-size: 0.8rem;">
                                                    <i class="fas fa-user-slash me-1"></i>Sin jefe asignado
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- ===== RRHH ===== --}}
                                    <td>
                                        @php
                                            $rrhhPuedeActuar = $estadoJefe === 'aprobado';
                                            $rrhhNoAplica = $estadoJefe === 'rechazado' || empty($estadoJefe);

                                            $badgeRRHH = 'bg-secondary';
                                            $textoRRHH = 'Desconocido';
                                            $iconoRRHH = 'fa-question-circle';

                                            if ($rrhhNoAplica) {
                                                $badgeRRHH = 'bg-light text-muted border'; $textoRRHH = 'N/A'; $iconoRRHH = 'fa-minus-circle';
                                            } elseif (!$rrhhPuedeActuar) {
                                                $badgeRRHH = 'bg-light text-dark border'; $textoRRHH = 'En espera'; $iconoRRHH = 'fa-hourglass-half';
                                            } elseif ($estadoRRHH === 'pendiente') {
                                                $badgeRRHH = 'bg-warning text-dark'; $textoRRHH = 'Pendiente'; $iconoRRHH = 'fa-clock';
                                            } elseif ($estadoRRHH === 'aprobado') {
                                                $badgeRRHH = 'bg-success'; $textoRRHH = 'Aprobado'; $iconoRRHH = 'fa-check-circle';
                                            } elseif ($estadoRRHH === 'rechazado') {
                                                $badgeRRHH = 'bg-danger'; $textoRRHH = 'Rechazado'; $iconoRRHH = 'fa-times-circle';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeRRHH }} py-2 px-3 mb-1 d-inline-block">
                                            <i class="fas {{ $iconoRRHH }} me-1"></i>{{ $textoRRHH }}
                                        </span>
                                        <div class="small mt-1">
                                            @if($rrhhPuedeActuar && !$rrhhNoAplica)
                                                <i class="fas fa-user-shield me-1 text-muted"></i>
                                                {{ $sol->rrhh->nombre ?? ''}} {{ $sol->rrhh->apellidoPat ?? '' }}
                                                @if($sol->fecha_aprobacion_rrhh)
                                                    <br>
                                                    <span class="text-muted" style="font-size: 0.75rem;">
                                                        <i class="far fa-calendar-alt me-1"></i>
                                                        {{ \Carbon\Carbon::parse($sol->fecha_aprobacion_rrhh)->format('d/m/Y H:i') }}
                                                    </span>
                                                @endif
                                            @elseif($rrhhNoAplica)
                                                <span class="text-muted fst-italic" style="font-size: 0.8rem;">
                                                    No aplica por estado del jefe
                                                </span>
                                            @else
                                                <span class="text-muted fst-italic" style="font-size: 0.8rem;">
                                                    Esperando aprobación del jefe
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- ===== ACCIONES ===== --}}
                                    <td>
                                        @php
                                            $editable = empty($estadoJefe) || $estadoJefe === 'pendiente';
                                            $pdfDisponible = $estadoJefe === 'aprobado';
                                        @endphp

                                        @if($editable)
                                            <button class="btn btn-sm btn-info btn-editar" data-id="{{ $sol->id }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-eliminar" data-id="{{ $sol->id }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @else
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="text-muted me-1" style="font-size: 0.8rem;">No editable</span>
                                                @if($pdfDisponible)
                                                    <a href="{{ route('salud.boleta', $sol->id) }}" class="btn btn-sm btn-success" target="_blank">
                                                        <i class="fa fa-file-pdf"></i> PDF
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($solicitudes->isEmpty())
                                <tr><td colspan="10" class="text-center text-muted py-4">No tienes solicitudes médicas registradas.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 container py-2 my-4 px-1 bg-white shadow rounded">
        @include('components.feriados')
    </div>
</div>
@endsection

@section('modales')
{{-- ===== MODAL CREAR / EDITAR ===== --}}
<div class="modal fade" id="modalSalud" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formSalud">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">
                <input type="hidden" name="id_editar" id="idEditar" value="">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalTitle">Registrar Salida Médica</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        {{-- Columna izquierda --}}
                        <div class="col-md-6">
                            <input type="hidden" id="idserv" name="idserv" value="{{ $persona->id }}">
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="nomb"
                                       value="{{ $persona->nombre ?? '' }} {{ $persona->apellidoPat ?? '' }} {{ $persona->apellidoMat ?? '' }}"
                                       disabled>
                                <label>Servidor Público</label>
                            </div>
                            <div class="form-floating mb-2">
                                <select class="form-select" id="tipoSal" name="tipoSal">
                                    @foreach ($tipoSal as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->descripcion }}</option>
                                    @endforeach
                                </select>
                                <label for="tipoSal">Tipo de salida (*)</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input type="date" class="form-control" id="fechasol" name="fechasol" readonly>
                                <label for="fechasol">Fecha de solicitud</label>
                            </div>
                            <div class="form-floating mb-2">
                                <textarea class="form-control" id="motivo" name="motivo" rows="3"></textarea>
                                <label for="motivo">Motivo / Sustento legal (*)</label>
                            </div>
                        </div>

                        {{-- Columna derecha --}}
                        <div class="col-md-6">
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="fsalida" name="fsalida" required>
                                <label for="fsalida">Fecha salida (*)</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input type="time" class="form-control" id="horasal" name="horasal" required>
                                <label for="horasal">Hora salida (*)</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="fretorno" name="fretorno" required>
                                <label for="fretorno">Fecha retorno (*)</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input type="time" class="form-control" id="horaret" name="horaret" required>
                                <label for="horaret">Hora retorno (*)</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="cantidad" name="cantidad" readonly>
                                <label for="cantidad">Cantidad (días/horas) *</label>
                                <small class="text-muted">Se calcula automáticamente según el tipo</small>
                            </div>
                        </div>

                        <hr>

                        {{-- Búsqueda de superior --}}
                        <div class="col-md-6">
                            <h5><span class="text-secondary">Buscar inmediato superior</span></h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="dato" placeholder="Nombre o apellido">
                                        <label for="dato">Nombre o apellido</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mt-2">
                                    <button type="button" class="btn btn-success" id="btnBuscarSuperior">
                                        <i class="fa-solid fa-magnifying-glass"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            <div class="row mx-1 mt-2" id="resultadosSuperior"></div>
                        </div>

                        {{-- Superior seleccionado --}}
                        <div class="col-md-6">
                            <h5><span class="text-secondary">Datos para aprobar</span></h5>
                            <input type="hidden" id="idSup" name="idSup">
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="nombreSup" readonly required>
                                <label for="nombreSup">Inmediato superior (*)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" id="btnGuardar">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    

{{-- Librerías --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================================
    // 1. CONFIGURACIÓN INICIAL
    // ============================================================
    const hoyStr = new Date().toISOString().split('T')[0];
    document.getElementById('fechasol').value = hoyStr;

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

    const pickerSalida = flatpickr("#fsalida", {
        minDate: "today",
        disable: [deshabilitarFechas],
        locale: "es",
        dateFormat: "Y-m-d"
    });

    const pickerRetorno = flatpickr("#fretorno", {
        minDate: "today",
        disable: [deshabilitarFechas],
        locale: "es",
        dateFormat: "Y-m-d"
    });

    // ============================================================
    // 2. SUSTENTO LEGAL AL CAMBIAR TIPO DE SALIDA
    // ============================================================
    const tipoSalSelect = document.getElementById('tipoSal');
    const motivoTextarea = document.getElementById('motivo');

    tipoSalSelect.addEventListener('change', function() {
        const tipoId = this.value;
        if (!tipoId) return;
        axios.get(`/tiposalida/${tipoId}`)
            .then(response => {
                const data = response.data;
                motivoTextarea.value = data.sustLegal || '';
                tipoSalSelect.dataset.medida = data.tipo_medida || 'dias';
                calcularCantidad();
            })
            .catch(error => {
                console.error('Error al obtener tipo de salida:', error);
                Swal.fire('Error', 'No se pudo cargar el sustento legal', 'error');
            });
    });

    if (tipoSalSelect.value) {
        tipoSalSelect.dispatchEvent(new Event('change'));
    }

    // ============================================================
    // 3. CÁLCULO DE CANTIDAD (días u horas)
    // ============================================================
    const fsalidaInput = document.getElementById('fsalida');
    const fretornoInput = document.getElementById('fretorno');
    const horasalInput = document.getElementById('horasal');
    const horaretInput = document.getElementById('horaret');
    const cantidadInput = document.getElementById('cantidad');

    function calcularCantidad() {
        const fsalida = fsalidaInput.value;
        const fretorno = fretornoInput.value;
        const horasal = horasalInput.value;
        const horaret = horaretInput.value;
        if (!fsalida || !fretorno || !horasal || !horaret) {
            cantidadInput.value = '';
            return;
        }

        const salida = new Date(`${fsalida}T${horasal}`);
        const retorno = new Date(`${fretorno}T${horaret}`);
        if (retorno < salida) {
            cantidadInput.value = 'Error: retorno < salida';
            return;
        }

        const diffMs = retorno - salida;
        const diffHoras = diffMs / (1000 * 60 * 60);
        const medida = tipoSalSelect.dataset.medida || 'dias';

        let cantidad;
        if (medida === 'horas') {
            cantidad = diffHoras.toFixed(1);
        } else {
            cantidad = (diffHoras / 24).toFixed(1);
        }
        cantidadInput.value = cantidad;
    }

    [fsalidaInput, fretornoInput, horasalInput, horaretInput].forEach(input => {
        input.addEventListener('change', calcularCantidad);
        input.addEventListener('keyup', calcularCantidad);
    });

    // ============================================================
    // 4. BUSCAR SUPERIOR (Axios)
    // ============================================================
    function buscarSuperior() {
        const dato = document.getElementById('dato').value.trim();
        if (!dato) {
            Swal.fire('Advertencia', 'Debe ingresar un nombre o apellido', 'warning');
            return;
        }

        const contenedor = document.getElementById('resultadosSuperior');
        contenedor.innerHTML = '<p class="text-muted">Buscando...</p>';

        axios.get('/vacacion/buscar-superior', { params: { buscar: dato } })
            .then(response => {
                const data = response.data;
                if (data.length === 0) {
                    contenedor.innerHTML = '<p class="text-muted">No se encontraron resultados</p>';
                    return;
                }
                let html = `<table class="table table-sm">
                            <thead><tr><th>Nombre</th><th>Acción</th></tr></thead>
                            <tbody>`;
                data.forEach(p => {
                    const nombre = `${p.nombre} ${p.apellidoPat} ${p.apellidoMat}`;
                    html += `<tr>
                                <td>${nombre}</td>
                                <td>
                                    <button class="btn btn-success btn-sm asignar-sup" data-id="${p.id}" data-nombre="${nombre}">
                                        <i class="fa-solid fa-angles-right"></i>
                                    </button>
                                </td>
                            </tr>`;
                });
                html += '</tbody></table>';
                contenedor.innerHTML = html;

                document.querySelectorAll('.asignar-sup').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.getElementById('idSup').value = this.dataset.id;
                        document.getElementById('nombreSup').value = this.dataset.nombre;
                        contenedor.innerHTML = '';
                        document.getElementById('dato').value = '';
                    });
                });
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
                contenedor.innerHTML = '<p class="text-danger">Error en la búsqueda</p>';
            });
    }

    document.getElementById('btnBuscarSuperior').addEventListener('click', buscarSuperior);
    document.getElementById('dato').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') buscarSuperior();
    });

    // ============================================================
    // 5. ENVÍO DEL FORMULARIO (CREAR / ACTUALIZAR)
    // ============================================================
    const form = document.getElementById('formSalud');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const idserv = document.getElementById('idserv').value;
        const tipoSal = document.getElementById('tipoSal').value;
        const fechasol = document.getElementById('fechasol').value;
        const motivo = document.getElementById('motivo').value;
        const fsalida = document.getElementById('fsalida').value;
        const horasal = document.getElementById('horasal').value;
        const fretorno = document.getElementById('fretorno').value;
        const horaret = document.getElementById('horaret').value;
        const idSup = document.getElementById('idSup').value;
        const cantidad = document.getElementById('cantidad').value;

        if (!idserv || !tipoSal || !fechasol || !motivo || !fsalida || !horasal || !fretorno || !horaret || !idSup || !cantidad) {
            Swal.fire('Faltan datos', 'Complete todos los campos obligatorios (*)', 'warning');
            return;
        }

        if (isNaN(parseFloat(cantidad)) || parseFloat(cantidad) <= 0) {
            Swal.fire('Error', 'La cantidad calculada no es válida', 'error');
            return;
        }

        const data = {
            persona_id: idserv,
            tipoSal: tipoSal,
            fechasol: fechasol,
            fsalida: fsalida,
            horasal: horasal,
            fretorno: fretorno,
            horaret: horaret,
            motivo: motivo,
            cantidad: cantidad,
            idSup: idSup
        };

        const method = document.getElementById('methodField').value;
        const idEditar = document.getElementById('idEditar').value;
        const url = method === 'PUT'
            ? `/salud/actualizar/${idEditar}`
            : '/vacacion/registrar-salida-salud';

        Swal.fire({
            title: 'Procesando...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        const config = {
            method: method === 'PUT' ? 'PUT' : 'POST',
            url: url,
            data: data,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        };

        axios(config)
            .then(response => {
                Swal.close();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: response.data.message || 'Operación exitosa',
                    timer: 1500,
                    showConfirmButton: false,
                    timerProgressBar: true
                }).then(() => {
                    cargarSalud();
                    bootstrap.Modal.getInstance(document.getElementById('modalSalud')).hide();
                });
            })
            .catch(error => {
                Swal.close();
                let mensaje = 'Ocurrió un error al procesar la solicitud.';
                if (error.response) {
                    const data = error.response.data;
                    if (data.errors) {
                        mensaje = Object.values(data.errors).flat().join('<br>');
                    } else if (data.message) {
                        mensaje = data.message;
                    } else if (data.error) {
                        mensaje = data.error;
                    }
                }
                Swal.fire('Error', mensaje, 'error');
            });
    });

    // ============================================================
    // 6. BOTÓN "NUEVO"
    // ============================================================
    document.getElementById('btnNuevaSalud').addEventListener('click', function() {
        form.reset();
        document.getElementById('methodField').value = 'POST';
        document.getElementById('idEditar').value = '';
        document.getElementById('modalTitle').textContent = 'Registrar Salida Médica';
        document.getElementById('btnGuardar').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar';
        document.getElementById('nombreSup').value = '';
        document.getElementById('idSup').value = '';
        document.getElementById('resultadosSuperior').innerHTML = '';
        document.getElementById('cantidad').value = '';
        document.getElementById('fechasol').value = hoyStr;

        pickerSalida.setDate(null);
        pickerRetorno.setDate(null);

        if (tipoSalSelect.value) {
            tipoSalSelect.dispatchEvent(new Event('change'));
        }

        const modal = new bootstrap.Modal(document.getElementById('modalSalud'));
        modal.show();
    });

    // ============================================================
    // 7. BIND EDITAR (delegación para recarga AJAX)
    // ============================================================
    function bindEditar() {
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                axios.get(`/salud/${id}/edit`)
                    .then(response => {
                        const data = response.data;
                        document.getElementById('tipoSal').value = data.tiposalida_id;
                        document.getElementById('fechasol').value = data.fechasol;
                        document.getElementById('motivo').value = data.motivo;
                        document.getElementById('fsalida').value = data.fechasal;
                        document.getElementById('horasal').value = data.horasal;
                        document.getElementById('fretorno').value = data.fecharet;
                        document.getElementById('horaret').value = data.horaret;
                        document.getElementById('cantidad').value = data.cantidad;
                        if (data.jefe) {
                            document.getElementById('idSup').value = data.jefe_id;
                            document.getElementById('nombreSup').value = data.jefe.nombre + ' ' + data.jefe.apellidoPat;
                        }
                        document.getElementById('methodField').value = 'PUT';
                        document.getElementById('idEditar').value = data.id;
                        document.getElementById('modalTitle').textContent = 'Editar Salida Médica';
                        document.getElementById('btnGuardar').innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Actualizar';

                        tipoSalSelect.dispatchEvent(new Event('change'));

                        const modal = new bootstrap.Modal(document.getElementById('modalSalud'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error al cargar datos:', error);
                        Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
                    });
            });
        });
    }

    // ============================================================
    // 8. BIND ELIMINAR (delegación para recarga AJAX)
    // ============================================================
    function bindEliminar() {
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                Swal.fire({
                    title: '¿Está seguro?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(`/salud/eliminar/${id}`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: response.data.message || 'Eliminado correctamente',
                                timer: 1500,
                                showConfirmButton: false,
                                timerProgressBar: true
                            });
                            cargarSalud();
                        })
                        .catch(error => {
                            let msg = error.response?.data?.error || 'Error al eliminar';
                            Swal.fire('Error', msg, 'error');
                        });
                    }
                });
            });
        });
    }

    // ============================================================
    // 9. RECARGAR TABLA DINÁMICAMENTE
    // ============================================================
    function cargarSalud() {
        axios.get('/salud/mis-solicitudes')
            .then(res => {
                if (!res.data.success) {
                    Swal.fire('Error', res.data.message, 'error');
                    return;
                }

                const data = res.data.data;
                const tbody = document.getElementById('tbodySalud');
                if (!tbody) return;

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">No tienes solicitudes médicas registradas.</td></tr>';
                    return;
                }

                let html = '';
                data.forEach((sol, index) => {
                    const estadoJefe = (sol.estado_jefe || '').toLowerCase();
                    const estadoRRHH = (sol.estado_rrhh || '').toLowerCase();

                    // === JEFE INMEDIATO ===
                    let badgeJefe = 'bg-secondary';
                    let textoJefe = 'Sin estado';
                    let iconoJefe = 'fa-question-circle';

                    if (estadoJefe === 'pendiente') {
                        badgeJefe = 'bg-warning text-dark'; textoJefe = 'Pendiente'; iconoJefe = 'fa-clock';
                    } else if (estadoJefe === 'aprobado') {
                        badgeJefe = 'bg-success'; textoJefe = 'Aprobado'; iconoJefe = 'fa-check-circle';
                    } else if (estadoJefe === 'rechazado') {
                        badgeJefe = 'bg-danger'; textoJefe = 'Rechazado'; iconoJefe = 'fa-times-circle';
                    } else if (!estadoJefe) {
                        badgeJefe = 'bg-light text-dark border'; textoJefe = 'Sin asignar'; iconoJefe = 'fa-user-slash';
                    }

                    const nombreJefe = sol.jefe_nombre && sol.jefe_apellido_pat
                        ? `${sol.jefe_nombre} ${sol.jefe_apellido_pat}`
                        : null;

                    const htmlJefe = nombreJefe
                        ? `<i class="fas fa-user-tie me-1 text-muted"></i>${nombreJefe}
                           ${sol.fecha_aprobacion_jefe ? `<br><span class="text-muted" style="font-size:0.75rem;"><i class="far fa-calendar-alt me-1"></i>${sol.fecha_aprobacion_jefe}</span>` : ''}`
                        : `<span class="text-muted fst-italic" style="font-size:0.8rem;"><i class="fas fa-user-slash me-1"></i>Sin jefe asignado</span>`;

                    // === RRHH ===
                    const rrhhPuedeActuar = estadoJefe === 'aprobado';
                    const rrhhNoAplica = estadoJefe === 'rechazado' || !estadoJefe;

                    let badgeRRHH = 'bg-secondary';
                    let textoRRHH = 'Desconocido';
                    let iconoRRHH = 'fa-question-circle';

                    if (rrhhNoAplica) {
                        badgeRRHH = 'bg-light text-muted border'; textoRRHH = 'N/A'; iconoRRHH = 'fa-minus-circle';
                    } else if (!rrhhPuedeActuar) {
                        badgeRRHH = 'bg-light text-dark border'; textoRRHH = 'En espera'; iconoRRHH = 'fa-hourglass-half';
                    } else if (estadoRRHH === 'pendiente') {
                        badgeRRHH = 'bg-warning text-dark'; textoRRHH = 'Pendiente'; iconoRRHH = 'fa-clock';
                    } else if (estadoRRHH === 'aprobado') {
                        badgeRRHH = 'bg-success'; textoRRHH = 'Aprobado'; iconoRRHH = 'fa-check-circle';
                    } else if (estadoRRHH === 'rechazado') {
                        badgeRRHH = 'bg-danger'; textoRRHH = 'Rechazado'; iconoRRHH = 'fa-times-circle';
                    }

                    const nombreRRHH = sol.rrhh_nombre && sol.rrhh_apellido_pat
                        ? `${sol.rrhh_nombre} ${sol.rrhh_apellido_pat}`
                        : null;

                    let htmlRRHH = '';
                    if (rrhhPuedeActuar && !rrhhNoAplica) {
                        htmlRRHH = `<i class="fas fa-user-shield me-1 text-muted"></i>${nombreRRHH}
                            ${sol.fecha_aprobacion_rrhh ? `<br><span class="text-muted" style="font-size:0.75rem;"><i class="far fa-calendar-alt me-1"></i>${sol.fecha_aprobacion_rrhh}</span>` : ''}`;
                    } else if (rrhhNoAplica) {
                        htmlRRHH = `<span class="text-muted fst-italic" style="font-size:0.8rem;">No aplica por estado del jefe</span>`;
                    } else {
                        htmlRRHH = `<span class="text-muted fst-italic" style="font-size:0.8rem;">Esperando aprobación del jefe</span>`;
                    }

                    // === ACCIONES ===
                    const editable = !estadoJefe || estadoJefe === 'pendiente';
                    const pdfDisponible = estadoJefe === 'aprobado';

                    let acciones = '';
                    if (editable) {
                        acciones = `
                            <button class="btn btn-sm btn-info btn-editar" data-id="${sol.id}"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger btn-eliminar" data-id="${sol.id}"><i class="fa fa-trash"></i></button>
                        `;
                    } else {
                        const pdfBtn = pdfDisponible
                            ? `<a href="/salud/boleta/${sol.id}" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-file-pdf"></i> PDF</a>`
                            : '';
                        acciones = `
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-muted me-1" style="font-size:0.8rem;">No editable</span>
                                ${pdfBtn}
                            </div>
                        `;
                    }

                    html += `
                        <tr id="fila-${sol.id}">
                            <td>${index + 1}</td>
                            <td>${sol.fechasal || ''}</td>
                            <td>${sol.horasal || ''}</td>
                            <td>${sol.fecharet || ''}</td>
                            <td>${sol.horaret || ''}</td>
                            <td>${sol.cantidad || ''}</td>
                            <td style="max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${sol.motivo || ''}">${sol.motivo || ''}</td>
                            <td>
                                <span class="badge ${badgeJefe} py-2 px-3 mb-1 d-inline-block"><i class="fas ${iconoJefe} me-1"></i>${textoJefe}</span>
                                <div class="small mt-1">${htmlJefe}</div>
                            </td>
                            <td>
                                <span class="badge ${badgeRRHH} py-2 px-3 mb-1 d-inline-block"><i class="fas ${iconoRRHH} me-1"></i>${textoRRHH}</span>
                                <div class="small mt-1">${htmlRRHH}</div>
                            </td>
                            <td>${acciones}</td>
                        </tr>
                    `;
                });

                tbody.innerHTML = html;
                bindEditar();
                bindEliminar();
            })
            .catch(err => {
                console.error(err.response?.data);
                Swal.fire('Error', err.response?.data?.message || 'No se pudo cargar la lista de solicitudes', 'error');
            });
    }

    // Bind inicial
    bindEditar();
    bindEliminar();
});
</script>
@endpush