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
<div class="row">
    <div class="col-md-10 container mt-4 p-4 bg-white shadow rounded">
        <div class="alert alert-primary" role="alert">
            <h5 class="text-center">Formulario de Solicitud de Comisión</h5>
        </div>
        <div id="app">
            <!-- Botón para abrir modal de creación -->
            <div class="text-start mb-3">
                <button class="btn btn-success" id="btnNuevaComision">
                    <i class="fa fa-plus"></i> Nueva Solicitud de Comisión
                </button>
                <a href="/homeusr" class="btn btn-secondary">
                    <i class="fa fa-times"></i> Cancelar
                </a>
            </div>

            <div class="mt-5">
                <h5 class="mb-3">Mis solicitudes de comisión</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaSolicitudes">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Fecha salida</th>
                                <th>Hora salida</th>
                                <th>Fecha retorno</th>
                                <th>Hora retorno</th>
                                <th>Motivo</th>
                                <th>Estado Jefe</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodySolicitudes">
                            @foreach($solicitudes as $sol)
                            <tr id="fila-{{ $sol->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($sol->fechasal)->format('d-m-Y') }}</td>
                                <td>{{ $sol->horasal }}</td>
                                <td>{{ \Carbon\Carbon::parse($sol->fecharet)->format('d-m-Y') }}</td>
                                <td>{{ $sol->horaret }}</td>
                                <td>{{ Str::limit($sol->motivo, 30) }}</td>
                                <td>
                                    @if($sol->estado_jefe == 'pendiente')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @elseif($sol->estado_jefe == 'aprobado')
                                        <span class="badge bg-success">Aprobado</span>
                                    @else
                                        <span class="badge bg-danger">Rechazado</span>
                                    @endif
                                </td>
<td>
    @if($sol->estado_jefe == 'pendiente')
        <button class="btn btn-sm btn-info btn-editar" data-id="{{ $sol->id }}">
            <i class="fa fa-edit"></i>
        </button>
        <button class="btn btn-sm btn-danger btn-eliminar" data-id="{{ $sol->id }}">
            <i class="fa fa-trash"></i>
        </button>
    @else
        <div class="btn-group" role="group">
            <span class="text-muted me-2">No disponible</span>
            @if($sol->estado_jefe == 'aprobado')
                <a href="{{ route('comision.boleta', $sol->id) }}" class="btn btn-sm btn-success" target="_blank">
                    <i class="fa fa-file-pdf"></i> PDF
                </a>
            @endif
        </div>
    @endif
</td>
                            </tr>
                            @endforeach
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

@section('modales')
<!-- Modal Crear -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Solicitud de Comisión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Datos personales y tipo de salida (solo lectura) -->
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" id="createIdpersona" value="{{ $persona->id }}" hidden>
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control" id="createNomb"
                                   value="{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}" disabled>
                            <label>Servidor Público:</label>
                        </div>
                        <div class="form-floating mb-2">
                            <select class="form-select" id="createSalida" disabled>
                                @foreach ($tipoSal as $sal)
                                    @if ($sal->descripcion == 'COMISION')
                                        <option value="{{ $sal->id }}">{{ $sal->descripcion }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <label for="createSalida">Tipo de salida:</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="date" id="createFechasol" class="form-control" readonly
                                   value="{{ now()->toDateString() }}">
                            <label for="createFechasol">Fecha de solicitud:</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-2">
                                    <input type="text" id="createFsalida" class="form-control">
                                    <label for="createFsalida">Fecha de salida:</label>
                                </div>
                                <div class="form-floating mb-2">
                                    <input type="time" id="createHorasal" class="form-control">
                                    <label for="createHorasal">Hora de salida:</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-2">
                                    <input type="text" id="createFretorno" class="form-control">
                                    <label for="createFretorno">Fecha de retorno:</label>
                                </div>
                                <div class="form-floating mb-2">
                                    <input type="time" id="createHoraret" class="form-control">
                                    <label for="createHoraret">Hora de retorno:</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating mb-2">
                            <textarea class="form-control" id="createMotivo" rows="3" placeholder="Describa el motivo de la comisión"></textarea>
                            <label for="createMotivo">Motivo de la comisión:</label>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Búsqueda del inmediato superior -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="row mb-3">
                            <div class="col-md-10">
                                <div class="form-floating">
                                    <input type="text" id="createDato" class="form-control" placeholder="Nombre o apellido" required>
                                    <label for="createDato">Buscar inmediato superior (nombre o apellido):</label>
                                </div>
                            </div>
                            <div class="col-md-2 text-start">
                                <button class="btn btn-success" id="createBtnBuscarSup"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <input type="text" id="createIdSup" hidden>
                            <div class="form-floating mb-2">
                                <input type="text" id="createNombreSup" class="form-control" readonly>
                                <label for="createNombreSup">Inmediato Superior Seleccionado:</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <table class="table" id="createTablaSuperiores">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="createTbodySuperiores">
                                    <!-- Se llena dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="createGuardarComision">
                    <i class="fa fa-save"></i> Registrar Comisión
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar (existente) -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar solicitud de comisión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-2">
                            <input type="date" id="editFsalida" class="form-control">
                            <label for="editFsalida">Fecha de salida</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="time" id="editHorasal" class="form-control">
                            <label for="editHorasal">Hora de salida</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-2">
                            <input type="date" id="editFretorno" class="form-control">
                            <label for="editFretorno">Fecha de retorno</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="time" id="editHoraret" class="form-control">
                            <label for="editHoraret">Hora de retorno</label>
                        </div>
                    </div>
                </div>
                <div class="form-floating mb-2">
                    <textarea class="form-control" id="editMotivo" rows="3"></textarea>
                    <label for="editMotivo">Motivo de la comisión</label>
                </div>
                <div class="row mt-3">
                    <div class="col-md-8">
                        <div class="form-floating">
                            <input type="text" id="editDato" class="form-control" placeholder="Buscar superior">
                            <label for="editDato">Buscar inmediato superior</label>
                        </div>
                    </div>
                    <div class="col-md-4 text-start">
                        <button class="btn btn-success" id="editBtnBuscarSup"><i class="fa fa-search"></i></button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <input type="text" id="editIdSup" hidden>
                        <div class="form-floating mb-2">
                            <input type="text" id="editNombreSup" class="form-control" readonly>
                            <label for="editNombreSup">Inmediato Superior Seleccionado</label>
                        </div>
                        <div id="editResultadosBusqueda" style="max-height:150px; overflow-y:auto;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarEdicion"><i class="fa fa-save"></i> Actualizar</button>
            </div>
        </div>
    </div>
</div>
@endsection

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

        // Configurar flatpickr para el modal de creación
        const createFsalidaPicker = flatpickr("#createFsalida", {
            minDate: "today",
            disable: [deshabilitarFechas],
            locale: "es",
            dateFormat: "Y-m-d"
        });

        const createFretornoPicker = flatpickr("#createFretorno", {
            minDate: "today",
            disable: [deshabilitarFechas],
            locale: "es",
            dateFormat: "Y-m-d"
        });

        // Configurar flatpickr para el modal de edición
        flatpickr("#editFsalida", {
            minDate: "today",
            disable: [deshabilitarFechas],
            locale: "es",
            dateFormat: "Y-m-d"
        });

        flatpickr("#editFretorno", {
            minDate: "today",
            disable: [deshabilitarFechas],
            locale: "es",
            dateFormat: "Y-m-d"
        });

        // ----- FUNCIONALIDAD DEL MODAL DE CREACIÓN -----

        // Abrir modal de creación
        document.getElementById("btnNuevaComision").addEventListener("click", function() {
            // Limpiar campos del modal
            document.getElementById("createFsalida").value = '';
            document.getElementById("createFretorno").value = '';
            document.getElementById("createHorasal").value = '';
            document.getElementById("createHoraret").value = '';
            document.getElementById("createMotivo").value = '';
            document.getElementById("createIdSup").value = '';
            document.getElementById("createNombreSup").value = '';
            document.getElementById("createTbodySuperiores").innerHTML = '';
            document.getElementById("createDato").value = '';

            // Resetear flatpickr
            createFsalidaPicker.setDate(null);
            createFretornoPicker.setDate(null);

            const modal = new bootstrap.Modal(document.getElementById('modalCrear'));
            modal.show();
        });

        // Búsqueda del superior en el modal de creación
        const createDatoInput = document.getElementById("createDato");
        const createBtnBuscar = document.getElementById("createBtnBuscarSup");
        const createTbody = document.getElementById("createTbodySuperiores");
        const createIdSupHidden = document.getElementById("createIdSup");
        const createNombreSupInput = document.getElementById("createNombreSup");

        function buscarSuperioresCreate() {
            const buscar = createDatoInput.value.trim();
            if (!buscar) {
                Swal.fire("Advertencia", "Debe ingresar un nombre o apellido antes de buscar", "warning");
                return;
            }

            createTbody.innerHTML = '<tr><td colspan="2" class="text-center">Buscando...</td></tr>';

            axios.get('/vacacion/buscar-superior', { params: { buscar: buscar } })
                .then(res => {
                    const data = res.data;
                    if (data.length === 0) {
                        createTbody.innerHTML = '<tr><td colspan="2" class="text-center">No se encontraron resultados</td></tr>';
                        return;
                    }
                    let html = '';
                    data.forEach(p => {
                        const nombreCompleto = `${p.nombre} ${p.apellidoPat} ${p.apellidoMat}`;
                        html += `
                            <tr>
                                <td>${nombreCompleto}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm create-asignar-sup" data-id="${p.id}" data-nombre="${nombreCompleto}">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    createTbody.innerHTML = html;

                    document.querySelectorAll(".create-asignar-sup").forEach(btn => {
                        btn.addEventListener("click", function() {
                            createIdSupHidden.value = this.dataset.id;
                            createNombreSupInput.value = this.dataset.nombre;
                            createTbody.innerHTML = '';
                            createDatoInput.value = '';
                        });
                    });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire("Error", "No se pudo completar la búsqueda", "error");
                });
        }

        createBtnBuscar.addEventListener("click", buscarSuperioresCreate);
        createDatoInput.addEventListener("keyup", function(e) {
            if (e.key === "Enter") buscarSuperioresCreate();
        });

        // Registrar comisión desde el modal de creación
        document.getElementById("createGuardarComision").addEventListener("click", async function() {
            const idpersona = document.getElementById("createIdpersona").value;
            const tipoSal = document.getElementById("createSalida").value;
            const fechasol = document.getElementById("createFechasol").value;
            const fsalida = document.getElementById("createFsalida").value;
            const fretorno = document.getElementById("createFretorno").value;
            const horasal = document.getElementById("createHorasal").value;
            const horaret = document.getElementById("createHoraret").value;
            const motivo = document.getElementById("createMotivo").value;
            const idSup = document.getElementById("createIdSup").value;

            // Validación básica
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
                // Cerrar el modal
                bootstrap.Modal.getInstance(document.getElementById('modalCrear')).hide();
                // Recargar la tabla de solicitudes
                cargarSolicitudes();
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

                Swal.fire("Error", mensaje, "error");
            }
        });

        // ----- FUNCIONALIDAD DE LA TABLA DE SOLICITUDES (existente) -----

        // Función para recargar la tabla de solicitudes
function cargarSolicitudes() {
    axios.get('/comision/mis-solicitudes')
        .then(res => {
            const data = res.data.data;

            const tbody = document.getElementById('tbodySolicitudes');
            if (!tbody) return;

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">No tienes solicitudes registradas</td></tr>';
                return;
            }

            let html = '';

            data.forEach((sol, index) => {
                // Determinar si la solicitud es editable (pendiente)
                const editable = sol.editable || (sol.estado_jefe && sol.estado_jefe.toLowerCase() === 'pendiente');

                let acciones = '';
                if (editable) {
                    acciones = `
                        <button class="btn btn-sm btn-info btn-editar" data-id="${sol.id}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${sol.id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    `;
                } else {
                    // Si no es editable, mostrar "No disponible" y el botón PDF si está aprobado
                    let pdfButton = '';
                    if (sol.estado_jefe && sol.estado_jefe.toLowerCase() === 'aprobado') {
                        pdfButton = `
                            <a href="/comision/boleta/${sol.id}" class="btn btn-sm btn-success" target="_blank">
                                <i class="fa fa-file-pdf"></i> PDF
                            </a>
                        `;
                    }
                    acciones = `
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-muted me-1">No disponible</span>
                            ${pdfButton}
                        </div>
                    `;
                }

                // Determinar el color de la badge del estado
                let estadoBadge = 'bg-secondary';
                let estadoTexto = sol.estado_jefe || 'Desconocido';

                if (sol.estado_jefe) {
                    const estadoLower = sol.estado_jefe.toLowerCase();
                    if (estadoLower === 'pendiente') {
                        estadoBadge = 'bg-warning text-dark';
                        estadoTexto = 'Pendiente';
                    } else if (estadoLower === 'aprobado') {
                        estadoBadge = 'bg-success';
                        estadoTexto = 'Aprobado';
                    } else if (estadoLower === 'rechazado') {
                        estadoBadge = 'bg-danger';
                        estadoTexto = 'Rechazado';
                    }
                }

                html += `
                    <tr id="fila-${sol.id}">
                        <td>${index + 1}</td>
                        <td>${sol.fechasal || ''}</td>
                        <td>${sol.horasal || ''}</td>
                        <td>${sol.fecharet || ''}</td>
                        <td>${sol.horaret || ''}</td>
                        <td>${sol.motivo || ''}</td>
                        <td><span class="badge ${estadoBadge}">${estadoTexto}</span></td>
                        <td>${acciones}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;

            // Reasignar eventos a los botones
            document.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', function() {
                    abrirModalEditar(this.dataset.id);
                });
            });

            document.querySelectorAll('.btn-eliminar').forEach(btn => {
                btn.addEventListener('click', function() {
                    eliminarSolicitud(this.dataset.id);
                });
            });
        })
        .catch(err => {
            console.error(err.response?.data);
            Swal.fire('Error', err.response?.data?.message || 'No se pudo cargar la lista de solicitudes', 'error');
        });
}

        // Eliminar solicitud
        function eliminarSolicitud(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/comision/eliminar/${id}`)
                        .then(res => {
                            Swal.fire('Eliminado', res.data.mensaje, 'success');
                            cargarSolicitudes();
                        })
                        .catch(err => {
                            let msg = err.response?.data?.error || 'Error al eliminar';
                            Swal.fire('Error', msg, 'error');
                        });
                }
            });
        }

        // Abrir modal de edición
        function abrirModalEditar(id) {
            axios.get(`/comision/obtener/${id}`)
                .then(res => {
                    const data = res.data;
                    document.getElementById('editId').value = data.id;
                    document.getElementById('editFsalida').value = data.fechasal;
                    document.getElementById('editFretorno').value = data.fecharet;
                    document.getElementById('editHorasal').value = data.horasal;
                    document.getElementById('editHoraret').value = data.horaret;
                    document.getElementById('editMotivo').value = data.motivo;

                    if (data.jefe_id) {
                        document.getElementById('editIdSup').value = data.jefe_id;
                        axios.get(`/vacacion/buscar-superior`, { params: { buscar: '' } })
                            .then(res2 => {
                                const jefe = res2.data.find(p => p.id == data.jefe_id);
                                if (jefe) {
                                    document.getElementById('editNombreSup').value = `${jefe.nombre} ${jefe.apellidoPat} ${jefe.apellidoMat}`;
                                }
                            });
                    }

                    document.getElementById('editResultadosBusqueda').innerHTML = '';
                    const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
                    modal.show();
                })
                .catch(err => {
                    Swal.fire('Error', err.response?.data?.error || 'No se pudo cargar la solicitud', 'error');
                });
        }

        // Guardar edición
        document.getElementById('guardarEdicion').addEventListener('click', function() {
            const id = document.getElementById('editId').value;
            const data = {
                fsalida: document.getElementById('editFsalida').value,
                fretorno: document.getElementById('editFretorno').value,
                horasal: document.getElementById('editHorasal').value,
                horaret: document.getElementById('editHoraret').value,
                motivo: document.getElementById('editMotivo').value,
                idSup: document.getElementById('editIdSup').value,
            };
            if (!data.fsalida || !data.fretorno || !data.horasal || !data.horaret || !data.motivo || !data.idSup) {
                Swal.fire('Faltan datos', 'Complete todos los campos', 'warning');
                return;
            }
            axios.put(`/comision/actualizar/${id}`, data)
                .then(res => {
                    Swal.fire('Actualizado', res.data.mensaje, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
                    cargarSolicitudes();
                })
                .catch(err => {
                    let msg = err.response?.data?.error || err.response?.data?.message || 'Error al actualizar';
                    Swal.fire('Error', msg, 'error');
                });
        });

        // Búsqueda de superior en el modal de edición
        document.getElementById('editBtnBuscarSup').addEventListener('click', function() {
            const buscar = document.getElementById('editDato').value.trim();
            if (!buscar) {
                Swal.fire('Advertencia', 'Ingrese un nombre o apellido', 'warning');
                return;
            }
            const contenedor = document.getElementById('editResultadosBusqueda');
            contenedor.innerHTML = '<p class="text-muted">Buscando...</p>';
            axios.get('/vacacion/buscar-superior', { params: { buscar: buscar } })
                .then(res => {
                    const data = res.data;
                    if (data.length === 0) {
                        contenedor.innerHTML = '<p class="text-muted">No se encontraron resultados</p>';
                        return;
                    }
                    let html = '<ul class="list-group">';
                    data.forEach(p => {
                        const nombre = `${p.nombre} ${p.apellidoPat} ${p.apellidoMat}`;
                        html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                    ${nombre}
                                    <button class="btn btn-sm btn-primary asignar-sup-modal" data-id="${p.id}" data-nombre="${nombre}">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </li>`;
                    });
                    html += '</ul>';
                    contenedor.innerHTML = html;

                    document.querySelectorAll('.asignar-sup-modal').forEach(btn => {
                        btn.addEventListener('click', function() {
                            document.getElementById('editIdSup').value = this.dataset.id;
                            document.getElementById('editNombreSup').value = this.dataset.nombre;
                            contenedor.innerHTML = '';
                            document.getElementById('editDato').value = '';
                        });
                    });
                })
                .catch(err => {
                    console.error(err);
                    contenedor.innerHTML = '<p class="text-danger">Error en la búsqueda</p>';
                });
        });

        // Cargar solicitudes al iniciar
        cargarSolicitudes();
    });
</script>
@endsection
