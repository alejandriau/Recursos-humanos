@extends('layouts.baseadm')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-clock-history"></i> Gestión de Horarios</h4>
        <button class="btn btn-primary" id="btnNuevoHorario">
            <i class="bi bi-plus-lg"></i> Nuevo Horario
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

        <div class="row g-4">
            @forelse ($horarios as $horario)
            <div class="col-12 col-md-6 col-xl-4" id="fila-horario-{{ $horario->id }}">
                <div class="card h-100 shadow-sm hover-shadow transition">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $horario->nombre }}</h5>
                            @if($horario->descripcion)
                                <small class="text-muted">{{ $horario->descripcion }}</small>
                            @endif
                        </div>
                        <span class="badge {{ $horario->activo ? 'bg-success' : 'bg-secondary' }}">
                            {{ $horario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div class="card-body">
                        {{-- Días laborables --}}
                        <h6 class="fw-bold"><i class="bi bi-calendar-week"></i> Días laborables</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Día</th>
                                        <th>Entrada</th>
                                        <th>Salida</th>
                                        <th>Entrada tarde</th>
                                        <th>Salida tarde</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($horario->dias as $dia)
                                    <tr>
                                        <td>
                                            @php
                                                $diasSemana = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
                                            @endphp
                                            <span class="fw-semibold">{{ $diasSemana[$dia->dia_semana] }}</span>
                                        </td>
                                        <td>{{ $dia->hora_entrada ? \Carbon\Carbon::parse($dia->hora_entrada)->format('H:i') : '-' }}</td>
                                        <td>{{ $dia->hora_salida ? \Carbon\Carbon::parse($dia->hora_salida)->format('H:i') : '-' }}</td>
                                        <td>{{ $dia->hora_entrada_tarde ? \Carbon\Carbon::parse($dia->hora_entrada_tarde)->format('H:i') : '-' }}</td>
                                        <td>{{ $dia->hora_salida_tarde ? \Carbon\Carbon::parse($dia->hora_salida_tarde)->format('H:i') : '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center text-muted">Sin días configurados</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Tolerancias --}}
                        <div class="mt-3 d-flex gap-3">
                            <span class="badge bg-info"><i class="bi bi-clock"></i> Tol. entrada: {{ $horario->tolerancia_entrada_minutos }} min</span>
                            <span class="badge bg-warning"><i class="bi bi-clock"></i> Tol. salida: {{ $horario->tolerancia_salida_minutos }} min</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-end gap-2">
                        <button class="btn btn-sm btn-outline-primary btn-editar" data-id="{{ $horario->id }}" title="Editar">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="{{ $horario->id }}" title="Eliminar">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No hay horarios registrados.
                </div>
            </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $horarios->links() }}
        </div>
</div>

<!-- ========== MODAL ========== -->
<div class="modal fade" id="modalHorario" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHorarioTitulo">Nuevo Horario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Errores -->
                <div id="alertaErrores" class="alert alert-danger d-none">
                    <ul id="listaErrores" class="mb-0"></ul>
                </div>

                <form id="formHorario">
                    <input type="hidden" id="horario_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" required placeholder="Ej. Jornada completa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="descripcion" placeholder="Opcional">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Tolerancia Entrada (min)</label>
                            <input type="number" class="form-control" id="tolerancia_entrada" required min="0" value="10">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tolerancia Salida (min)</label>
                            <input type="number" class="form-control" id="tolerancia_salida" required min="0" value="10">
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input type="checkbox" class="form-check-input" id="activo" role="switch" checked>
                                <label class="form-check-label" for="activo">Horario activo</label>
                            </div>
                        </div>
                    </div>

                    <!-- Días de la semana -->
                    <hr>
                    <h6 class="fw-bold">Días laborables</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="tablaDias">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:120px">Día</th>
                                    <th>Entrada</th>
                                    <th>Salida</th>
                                    <th>Entrada tarde</th>
                                    <th>Salida tarde</th>
                                    <th style="width:40px"></th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoDias">
                                <!-- Filas generadas por JS -->
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnAgregarDia">
                        <i class="bi bi-plus-circle"></i> Agregar día
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarHorario">Guardar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== INICIALIZAR MODAL =====
        const modalElement = document.getElementById('modalHorario');
        const modal = new bootstrap.Modal(modalElement);

        // ===== ELEMENTOS =====
        const form = document.getElementById('formHorario');
        const horarioIdInput = document.getElementById('horario_id');
        const nombreInput = document.getElementById('nombre');
        const descripcionInput = document.getElementById('descripcion');
        const toleranciaEntradaInput = document.getElementById('tolerancia_entrada');
        const toleranciaSalidaInput = document.getElementById('tolerancia_salida');
        const activoCheckbox = document.getElementById('activo');
        const modalTitulo = document.getElementById('modalHorarioTitulo');
        const alertaErrores = document.getElementById('alertaErrores');
        const listaErrores = document.getElementById('listaErrores');
        const cuerpoDias = document.getElementById('cuerpoDias');
        const btnAgregarDia = document.getElementById('btnAgregarDia');

        // ===== FUNCIONES AUXILIARES =====
        function ocultarErrores() {
            alertaErrores.classList.add('d-none');
            listaErrores.innerHTML = '';
        }

        function mostrarErrores(errors) {
            listaErrores.innerHTML = '';
            for (const campo in errors) {
                const li = document.createElement('li');
                li.textContent = errors[campo][0];
                listaErrores.appendChild(li);
            }
            alertaErrores.classList.remove('d-none');
        }

        function resetearFormulario() {
            form.reset();
            horarioIdInput.value = '';
            toleranciaEntradaInput.value = 10;
            toleranciaSalidaInput.value = 10;
            activoCheckbox.checked = true;
            cuerpoDias.innerHTML = '';
            ocultarErrores();
        }

        function cargarDatosEnFormulario(data) {
            horarioIdInput.value = data.id;
            nombreInput.value = data.nombre;
            descripcionInput.value = data.descripcion || '';
            toleranciaEntradaInput.value = data.tolerancia_entrada_minutos;
            toleranciaSalidaInput.value = data.tolerancia_salida_minutos;
            activoCheckbox.checked = data.activo == 1;

            // Cargar días
            cuerpoDias.innerHTML = '';
            if (data.dias && data.dias.length) {
                data.dias.forEach(dia => agregarFilaDia(dia));
            }
            ocultarErrores();
        }

        // ===== FUNCIONES PARA FILAS DE DÍAS =====
        function agregarFilaDia(diaData = null) {
            const row = document.createElement('tr');
            row.dataset.id = diaData?.id || '';

            // Día de la semana
            const tdDia = document.createElement('td');
            const select = document.createElement('select');
            select.className = 'form-select form-select-sm dia-select';
            const diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            diasSemana.forEach((nombre, idx) => {
                const opt = document.createElement('option');
                opt.value = idx;
                opt.textContent = nombre;
                if (diaData && diaData.dia_semana == idx) opt.selected = true;
                select.appendChild(opt);
            });
            tdDia.appendChild(select);
            row.appendChild(tdDia);

            // Entrada
            const tdEnt = document.createElement('td');
            const inputEnt = document.createElement('input');
            inputEnt.type = 'time';
            inputEnt.className = 'form-control form-control-sm hora-entrada';
            inputEnt.value = diaData?.hora_entrada || '';
            tdEnt.appendChild(inputEnt);
            row.appendChild(tdEnt);

            // Salida
            const tdSal = document.createElement('td');
            const inputSal = document.createElement('input');
            inputSal.type = 'time';
            inputSal.className = 'form-control form-control-sm hora-salida';
            inputSal.value = diaData?.hora_salida || '';
            tdSal.appendChild(inputSal);
            row.appendChild(tdSal);

            // Entrada tarde
            const tdEntT = document.createElement('td');
            const inputEntT = document.createElement('input');
            inputEntT.type = 'time';
            inputEntT.className = 'form-control form-control-sm hora-entrada-tarde';
            inputEntT.value = diaData?.hora_entrada_tarde || '';
            tdEntT.appendChild(inputEntT);
            row.appendChild(tdEntT);

            // Salida tarde
            const tdSalT = document.createElement('td');
            const inputSalT = document.createElement('input');
            inputSalT.type = 'time';
            inputSalT.className = 'form-control form-control-sm hora-salida-tarde';
            inputSalT.value = diaData?.hora_salida_tarde || '';
            tdSalT.appendChild(inputSalT);
            row.appendChild(tdSalT);

            // Botón eliminar
            const tdAccion = document.createElement('td');
            const btnDel = document.createElement('button');
            btnDel.type = 'button';
            btnDel.className = 'btn btn-sm btn-outline-danger eliminar-dia';
            btnDel.innerHTML = '<i class="bi bi-trash"></i>';
            btnDel.addEventListener('click', function() {
                row.remove();
            });
            tdAccion.appendChild(btnDel);
            row.appendChild(tdAccion);

            cuerpoDias.appendChild(row);
        }

        // Obtener datos de las filas de días
        function obtenerDias() {
            const filas = cuerpoDias.querySelectorAll('tr');
            const dias = [];
            filas.forEach(row => {
                const id = row.dataset.id || null;
                const dia_semana = parseInt(row.querySelector('.dia-select').value);
                const hora_entrada = row.querySelector('.hora-entrada').value;
                const hora_salida = row.querySelector('.hora-salida').value;
                const hora_entrada_tarde = row.querySelector('.hora-entrada-tarde').value;
                const hora_salida_tarde = row.querySelector('.hora-salida-tarde').value;

                // Solo agregar si al menos tiene hora entrada y salida (o las tardes)
                if (hora_entrada && hora_salida) {
                    dias.push({
                        id: id,
                        dia_semana: dia_semana,
                        hora_entrada: hora_entrada,
                        hora_salida: hora_salida,
                        hora_entrada_tarde: hora_entrada_tarde || null,
                        hora_salida_tarde: hora_salida_tarde || null,
                    });
                }
            });
            return dias;
        }

        // ===== ABRIR MODAL NUEVO =====
        function abrirModalNuevo() {
            resetearFormulario();
            // Agregar una fila vacía por defecto
            agregarFilaDia();
            modalTitulo.innerText = 'Nuevo Horario';
            modal.show();
        }

        // ===== ABRIR MODAL EDITAR =====
        function abrirModalEditar(id) {
            axios.get(`/horarios/${id}/get`)
                .then(response => {
                    cargarDatosEnFormulario(response.data);
                    // Si no hay días, agregar una fila vacía
                    if (cuerpoDias.children.length === 0) {
                        agregarFilaDia();
                    }
                    modalTitulo.innerText = 'Editar Horario';
                    modal.show();
                })
                .catch(error => {
                    alert('Error al cargar los datos del horario.');
                    console.error(error);
                });
        }

        // ===== ELIMINAR =====
        function eliminarHorario(id) {
            if (!confirm('¿Eliminar este horario?')) return;

            axios.delete(`/horarios/${id}`)
                .then(response => {
                    if (response.data.success) {
                        const fila = document.getElementById(`fila-horario-${id}`);
                        if (fila) fila.remove();
                        alert(response.data.message || 'Eliminado correctamente.');
                    } else {
                        alert(response.data.message || 'Error al eliminar.');
                    }
                })
                .catch(error => {
                    alert('Error de red al eliminar.');
                    console.error(error);
                });
        }

        // ===== GUARDAR =====
        function guardarHorario() {
            const id = horarioIdInput.value;
            const dias = obtenerDias();

            const payload = {
                nombre: nombreInput.value,
                descripcion: descripcionInput.value,
                tolerancia_entrada_minutos: parseInt(toleranciaEntradaInput.value) || 0,
                tolerancia_salida_minutos: parseInt(toleranciaSalidaInput.value) || 0,
                activo: activoCheckbox.checked ? 1 : 0,
                dias: dias
            };

            const url = id ? `/horarios/${id}` : '/horarios';
            const method = id ? 'put' : 'post';

            axios[method](url, payload)
                .then(response => {
                    if (response.data.success) {
                        modal.hide();
                        window.location.reload();
                    } else {
                        if (response.data.errors) {
                            mostrarErrores(response.data.errors);
                        } else {
                            alert(response.data.message || 'Error al guardar.');
                        }
                    }
                })
                .catch(error => {
                    if (error.response && error.response.status === 422) {
                        mostrarErrores(error.response.data.errors);
                    } else {
                        alert('Error de red al guardar.');
                        console.error(error);
                    }
                });
        }

        // ===== ASIGNAR EVENTOS =====

        // Botón "Nuevo Horario"
        document.getElementById('btnNuevoHorario').addEventListener('click', abrirModalNuevo);

        // Botones "Editar" (evento delegado)
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                abrirModalEditar(id);
            });
        });

        // Botones "Eliminar" (evento delegado)
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                eliminarHorario(id);
            });
        });

        // Botón "Guardar" del modal
        document.getElementById('btnGuardarHorario').addEventListener('click', guardarHorario);

        // Agregar día
        btnAgregarDia.addEventListener('click', function() {
            agregarFilaDia();
        });

        // Limpiar errores al cerrar modal
        modalElement.addEventListener('hidden.bs.modal', function() {
            ocultarErrores();
        });
    });
</script>
@endpush