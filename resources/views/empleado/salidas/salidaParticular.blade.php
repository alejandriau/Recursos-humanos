@extends('layouts.baseusr')

@section('comunicado')
    @php
        $ges = $gestion->first();
    @endphp
    @if ($ges)
        LISTA DE FERIADOS GESTIÓN : {{ $ges->anio }}
        <table class="table table-hover table-sm">
            <thead>
                <tr><th class="col-md-7">Descripción</th><th class="col-md-5">Fecha</th></tr>
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
            <b>GESTIÓN DE SALIDAS PARTICULARES</b>
        </div>
    </div>
    <hr>

    <div class="container mt-2 rounded shadow bg-white p-3">
        <!-- Botón nuevo -->
        <div class="row mb-3">
            <div class="col-md-12 text-start">
                <button type="button" class="btn btn-primary" id="btnNuevoParticular">
                    <i class="fa-solid fa-plus"></i> Nueva Salida Particular
                </button>
            </div>
        </div>

        <!-- Tabla de salidas -->
        <div class="table-responsive">
            <table class="table table-hover table-sm" id="tablaParticulares">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha solicitud</th>
                        <th>Fecha salida</th>
                        <th>Fecha retorno</th>
                        <th>Cantidad</th>
                        <th>Motivo</th>
                        <th>Jefe</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodySalidas">
                    @foreach ($salidas as $salida)
                        <tr id="fila-{{ $salida->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($salida->fechasol)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($salida->fechasal)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($salida->fecharet)->format('d-m-Y') }}</td>
                            <td>{{ $salida->cantidad }}</td>
                            <td>{{ Str::limit($salida->motivo, 30) }}</td>
                            <td>{{ $salida->jefe ? $salida->jefe->nombre . ' ' . $salida->jefe->apellidoPat : 'N/A' }}</td>
                            <td>
                                @php
                                    $badge = 'secondary';
                                    if ($salida->estado == 'aprobado') $badge = 'success';
                                    elseif ($salida->estado == 'rechazado') $badge = 'danger';
                                    elseif (in_array($salida->estado, ['pendiente_jefe','pendiente_rrhh'])) $badge = 'warning';
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ str_replace('_', ' ', $salida->estado) }}</span>
                            </td>
                            <td>
                                @if(in_array($salida->estado, ['aprobado', 'pendiente_jefe', 'pendiente_rrhh']))
                                    {{-- Botón para descargar PDF --}}
                                    <a href="{{ route('salidas.particular.pdf', $salida->id) }}" class="btn btn-sm btn-success" target="_blank">
                                        <i class="fa-solid fa-file-pdf"></i> PDF
                                    </a>
                                @endif
                                @if (in_array($salida->estado, ['pendiente_jefe', 'pendiente_rrhh']))
                                    <button class="btn btn-sm btn-warning btnEditar" data-id="{{ $salida->id }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btnEliminar" data-id="{{ $salida->id }}">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                @else
                                    <span class="text-muted">Sin acciones</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if ($salidas->isEmpty())
                        <tr><td colspan="9" class="text-center">No hay salidas particulares registradas.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('modales')
    <!-- ===== MODAL CREAR / EDITAR ===== -->
    <div class="modal fade" id="modalParticular" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formParticular">
                    @csrf
                    <input type="hidden" name="_method" id="methodField" value="POST">
                    <input type="hidden" name="id_editar" id="idEditar" value="">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalTitle">Registrar Salida Particular</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            {{-- Columna izquierda: datos del servidor --}}
                            <div class="col-md-6">
                                <h5><span class="text-secondary">Servidor público</span></h5>
                                <input type="hidden" id="idserv" value="{{ $persona->id }}">
                                <div class="form-floating mb-2">
                                    <input type="text" class="form-control" id="nomb"
                                           value="{{ $persona->nombre ?? '' }} {{ $persona->apellidoPat ?? '' }} {{ $persona->apellidoMat ?? '' }}"
                                           disabled>
                                    <label>Nombres y apellidos</label>
                                </div>
                                <div class="form-floating mb-2">
                                    <select class="form-select" id="tipoSal" name="tipoSal">
                                        @foreach ($tiposHijos as $hijo)
                                            <option value="{{ $hijo->id }}">{{ $hijo->descripcion }}</option>
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

                            {{-- Columna derecha: fechas, horas y cantidad --}}
                            <div class="col-md-6">
                                <div class="form-floating mb-2">
                                    <input type="date" class="form-control" id="fsalida" name="fsalida" required>
                                    <label for="fsalida">Fecha salida (*)</label>
                                </div>
                                <div class="form-floating mb-2">
                                    <input type="time" class="form-control" id="horasal" name="horasal" required>
                                    <label for="horasal">Hora salida (*)</label>
                                </div>
                                <div class="form-floating mb-2">
                                    <input type="date" class="form-control" id="fretorno" name="fretorno" required>
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

                            {{-- Búsqueda de inmediato superior --}}
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
                                <div class="row mx-1 mt-2" id="resultadosSuperior" style="text-align: left">
                                    {{-- Resultados dinámicos --}}
                                </div>
                            </div>

                            {{-- Datos para aprobar (superior asignado) --}}
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
                        <button type="submit" class="btn btn-primary" id="btnGuardar">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- Librerías JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============================================================
        // 1. CONFIGURACIÓN INICIAL
        // ============================================================
        const hoyStr = new Date().toISOString().split('T')[0];
        document.getElementById('fechasol').value = hoyStr;

        // Obtener feriados desde el backend (para deshabilitar)
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

        // Inicializar flatpickr para fechas de salida y retorno (con restricciones)
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
        // 2. OBTENER SUSTENTO LEGAL AL CAMBIAR TIPO DE SALIDA
        // ============================================================
        const tipoSalSelect = document.getElementById('tipoSal');
        const motivoTextarea = document.getElementById('motivo');

        tipoSalSelect.addEventListener('change', function() {
            const tipoId = this.value;
            if (!tipoId) return;
            axios.get(`/tiposalida/${tipoId}`)
                .then(response => {
                    const data = response.data;
                    // Rellenar el motivo con el sustento legal
                    if (data.sustLegal) {
                        motivoTextarea.value = data.sustLegal;
                    } else {
                        motivoTextarea.value = '';
                    }
                    // Si el tipo tiene una unidad de medida (días u horas), lo guardamos
                    // para el cálculo automático
                    if (data.tipo_medida) {
                        // Guardar en un data attribute del select
                        tipoSalSelect.dataset.medida = data.tipo_medida;
                    }
                    // Recalcular cantidad automáticamente
                    calcularCantidad();
                })
                .catch(error => {
                    console.error('Error al obtener tipo de salida:', error);
                    Swal.fire('Error', 'No se pudo cargar el sustento legal', 'error');
                });
        });

        // Disparar cambio inicial para cargar el sustento del primer tipo
        if (tipoSalSelect.value) {
            tipoSalSelect.dispatchEvent(new Event('change'));
        }

        // ============================================================
        // 3. CALCULAR CANTIDAD (días u horas) AUTOMÁTICAMENTE
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
            const medida = tipoSalSelect.dataset.medida || 'dias'; // por defecto días

            let cantidad;
            if (medida === 'horas') {
                cantidad = diffHoras.toFixed(1);
            } else { // días
                cantidad = (diffHoras / 24).toFixed(1);
            }
            cantidadInput.value = cantidad;
        }

        // Eventos que disparan el cálculo
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

                    // Asignar evento a los botones
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
        const form = document.getElementById('formParticular');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validar campos obligatorios
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

            // Validar que la cantidad sea un número válido
            if (isNaN(parseFloat(cantidad)) || parseFloat(cantidad) <= 0) {
                Swal.fire('Error', 'La cantidad calculada no es válida', 'error');
                return;
            }

            // Construir el objeto de datos
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
                ? `/salidas/particulares/${idEditar}`
                : '/particular/registrar';

            // Mostrar loading
            Swal.fire({
                title: 'Procesando...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Enviar con Axios
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
                        location.reload();
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
        document.getElementById('btnNuevoParticular').addEventListener('click', function() {
            // Resetear formulario
            form.reset();
            document.getElementById('methodField').value = 'POST';
            document.getElementById('idEditar').value = '';
            document.getElementById('modalTitle').textContent = 'Registrar Salida Particular';
            document.getElementById('btnGuardar').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar';
            document.getElementById('nombreSup').value = '';
            document.getElementById('idSup').value = '';
            document.getElementById('resultadosSuperior').innerHTML = '';
            document.getElementById('cantidad').value = '';
            document.getElementById('fechasol').value = hoyStr;

            // Limpiar fechas
            pickerSalida.setDate(null);
            pickerRetorno.setDate(null);

            // Forzar cambio de tipo para cargar sustento
            if (tipoSalSelect.value) {
                tipoSalSelect.dispatchEvent(new Event('change'));
            }

            const modal = new bootstrap.Modal(document.getElementById('modalParticular'));
            modal.show();
        });

        // ============================================================
        // 7. BOTONES "EDITAR"
        // ============================================================
        document.querySelectorAll('.btnEditar').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                axios.get(`/particular/${id}/edit`)
                    .then(response => {
                        const data = response.data;
                        // Llenar campos del formulario
                        document.getElementById('tipoSal').value = data.tiposalida_id;
                        document.getElementById('fechasol').value = data.fechasol;
                        document.getElementById('motivo').value = data.motivo; // ya trae el sustento
                        document.getElementById('fsalida').value = data.fechasal;
                        document.getElementById('horasal').value = data.horasal;
                        document.getElementById('fretorno').value = data.fecharet;
                        document.getElementById('horaret').value = data.horaret;
                        document.getElementById('cantidad').value = data.cantidad;
                        if (data.jefe) {
                            document.getElementById('idSup').value = data.jefe_id;
                            document.getElementById('nombreSup').value = data.jefe.nombre + ' ' + data.jefe.apellidoPat;
                        }
                        // Configurar para edición
                        document.getElementById('methodField').value = 'PUT';
                        document.getElementById('idEditar').value = data.id;
                        document.getElementById('modalTitle').textContent = 'Editar Salida Particular';
                        document.getElementById('btnGuardar').innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Actualizar';

                        // Forzar cambio de tipo para actualizar sustento si cambia
                        tipoSalSelect.dispatchEvent(new Event('change'));

                        const modal = new bootstrap.Modal(document.getElementById('modalParticular'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error al cargar datos:', error);
                        Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
                    });
            });
        });

        // ============================================================
        // 8. BOTONES "ELIMINAR"
        // ============================================================
        document.querySelectorAll('.btnEliminar').forEach(btn => {
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
                        axios.delete(`/salidas/particulares/${id}`, {
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
                            }).then(() => location.reload());
                        })
                        .catch(error => {
                            let msg = error.response?.data?.error || 'Error al eliminar';
                            Swal.fire('Error', msg, 'error');
                        });
                    }
                });
            });
        });
    });
</script>
