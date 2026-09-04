@extends('layouts.baseadm')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-people-fill"></i> Asignación de Horarios</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAsignacion">
            <i class="bi bi-plus-lg"></i> Nueva Asignación
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaAsignaciones">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Persona</th>
                            <th>Horario</th>
                            <th>Vigencia</th>
                            <th>Estado</th>
                            <th class="text-end" style="width: 140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($asignaciones as $asig)
                        <tr id="asignacion-{{ $asig->id }}" 
                            class="{{ $asig->activo ? 'table-row-active' : 'table-row-inactive' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        {{ substr($asig->persona->nombre ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                    <div class="fw-semibold">
                                        {{ $asig->persona->nombre ?? '' }}
                                        {{ $asig->persona->apellidoPat ?? '' }}
                                        {{ $asig->persona->apellidoMat ?? '' }}
                                    </div>
                                        <small class="text-muted">{{ $asig->persona->ci ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    {{ $asig->horario->nombre }}
                                </span>
                            </td>
                            <td>
                                <i class="bi bi-calendar-check text-success"></i>
                                {{ $asig->fecha_inicio->format('d/m/Y') }}
                                @if($asig->fecha_fin)
                                    <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                    <i class="bi bi-calendar-x text-danger"></i>
                                    {{ $asig->fecha_fin->format('d/m/Y') }}
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary ms-1">Indefinido</span>
                                @endif
                            </td>
                            <td>
                                @if($asig->activo)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                        <i class="bi bi-check-circle"></i> Activo
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">
                                        <i class="bi bi-x-circle"></i> Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($asig->activo)
                                <button class="btn btn-sm btn-outline-warning btn-editar" 
                                        data-id="{{ $asig->id }}"
                                        data-persona="{{ trim(($asig->persona->nombre ?? '').' '.($asig->persona->apellidoPat ?? '').' '.($asig->persona->apellidoMat ?? '')) }} (CI: {{ $asig->persona->ci ?? '' }})"
                                        data-horario-id="{{ $asig->horario_id }}"
                                        data-fecha-inicio="{{ $asig->fecha_inicio ? $asig->fecha_inicio->format('Y-m-d') : '' }}"
                                        data-fecha-fin="{{ $asig->fecha_fin ? $asig->fecha_fin->format('Y-m-d') : '' }}"
                                        title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-finalizar" data-id="{{ $asig->id }}" title="Finalizar">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $asignaciones->links() }}
        </div>
    </div>
</div>

<!-- ========== MODAL (con el rediseño que ya teníamos) ========== -->
<div class="modal fade" id="modalAsignacion" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nueva Asignación de Horario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Errores -->
                <div id="alertaErrores" class="alert alert-danger d-none">
                    <ul id="listaErrores" class="mb-0"></ul>
                </div>

                <form id="formAsignacion">
                    @csrf

                    {{-- Selección de personas --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="bi bi-people"></i> Seleccionar Personas</label>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <input type="text" id="buscadorPersonas" class="form-control" placeholder="Buscar por nombre o CI...">
                            </div>
                            <div class="col-md-8 text-md-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnSeleccionarTodos">Todos</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeseleccionarTodos">Ninguno</button>
                                <button type="button" class="btn btn-sm btn-outline-success" id="btnSeleccionarNoAsignados">Solo no asignados</button>
                                <span id="contadorSeleccionados" class="ms-2 badge bg-info">0 seleccionados</span>
                            </div>
                        </div>

                        {{-- Grid de personas con checkbox --}}
                        <div class="personas-grid" id="personasGrid">
                            @foreach($personas as $p)
                            <div class="persona-item {{ $p->tiene_asignacion_activa ? 'asignado' : '' }}" 
                                 data-nombre="{{ strtolower($p->nombre.' '.$p->apellidoPat.' '.$p->apellidoMat) }}" 
                                 data-ci="{{ $p->ci }}">
                                <div class="form-check">
                                    <input class="form-check-input persona-checkbox" type="checkbox" 
                                           name="persona_ids[]" value="{{ $p->id }}" 
                                           id="persona_{{ $p->id }}"
                                           {{ $p->tiene_asignacion_activa ? 'disabled' : '' }}>
                                    <label class="form-check-label w-100" for="persona_{{ $p->id }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>
                                                <strong>{{ $p->ci }}</strong> - 
                                                {{ $p->nombre }} {{ $p->apellidoPat ?? '' }} {{ $p->apellidoMat ?? '' }}
                                            </span>
                                            @if($p->tiene_asignacion_activa)
                                                <span class="badge bg-danger">Ya asignado</span>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Los usuarios con fondo gris ya tienen una asignación activa y no se pueden seleccionar.</small>
                    </div>

                    {{-- Horario y fechas --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Horario</label>
                            <select name="horario_id" class="form-select" required>
                                @foreach($horarios as $h)
                                    <option value="{{ $h->id }}">{{ $h->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Fecha Fin (opcional)</label>
                            <input type="date" name="fecha_fin" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarAsignacion">
                    <i class="bi bi-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ========== MODAL EDITAR ========== -->
<div class="modal fade" id="modalEditarAsignacion" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Asignación de Horario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="alertaErroresEditar" class="alert alert-danger d-none">
                    <ul id="listaErroresEditar" class="mb-0"></ul>
                </div>

                <form id="formEditarAsignacion">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="edit_asignacion_id">

                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="bi bi-person"></i> Persona</label>
                        <input type="text" id="edit_persona_nombre" class="form-control" readonly>
                        <small class="text-muted">La persona no se puede cambiar; solo horario y vigencia.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Horario</label>
                        <select name="horario_id" id="edit_horario_id" class="form-select" required>
                            @foreach($horarios as $h)
                                <option value="{{ $h->id }}">{{ $h->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="edit_fecha_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha Fin (opcional)</label>
                            <input type="date" name="fecha_fin" id="edit_fecha_fin" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnActualizarAsignacion">
                    <i class="bi bi-save"></i> Actualizar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Estilos para la tabla */
    .table-row-active {
        border-left: 4px solid #198754;
    }
    .table-row-inactive {
        border-left: 4px solid #6c757d;
        opacity: 0.75;
    }
    .table-row-inactive td {
        background-color: #f8f9fa;
    }
    .table tbody tr:hover {
        background-color: rgba(0,0,0,.02);
    }
    .avatar-circle {
        width: 36px;
        height: 36px;
        background-color: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.9rem;
    }
    .badge.bg-primary-subtle {
        background-color: #cfe2ff;
        color: #0a58ca;
        border-color: #b6d4fe;
    }
    .badge.bg-success.bg-opacity-10 {
        background-color: rgba(25,135,84,0.1);
        color: #198754;
        border-color: #198754;
    }
    .badge.bg-secondary.bg-opacity-10 {
        background-color: rgba(108,117,125,0.1);
        color: #6c757d;
        border-color: #6c757d;
    }
    .badge.bg-secondary-subtle {
        background-color: #e2e3e5;
        color: #41464b;
    }

    /* Estilos para el grid de personas (modal) */
    .personas-grid {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px;
    }
    .persona-item {
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 4px;
        background-color: #fff;
        transition: background-color 0.15s;
        border-left: 3px solid transparent;
    }
    .persona-item:hover {
        background-color: #f8f9fa;
    }
    .persona-item.asignado {
        background-color: #e9ecef;
        border-left-color: #dc3545;
        opacity: 0.7;
    }
    .persona-item.asignado .form-check-label {
        color: #6c757d;
    }
    .persona-item .form-check-input:disabled {
        cursor: not-allowed;
    }
    .modal-header.bg-primary {
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .badge.bg-danger {
        font-size: 0.7rem;
        padding: 0.25rem 0.6rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('modalAsignacion');
        const modal = new bootstrap.Modal(modalElement);
        const form = document.getElementById('formAsignacion');
        const btnGuardar = document.getElementById('btnGuardarAsignacion');
        const alertaErrores = document.getElementById('alertaErrores');
        const listaErrores = document.getElementById('listaErrores');

        // ===== ELEMENTOS DEL SELECTOR =====
        const checkboxes = document.querySelectorAll('.persona-checkbox');
        const contador = document.getElementById('contadorSeleccionados');
        const buscador = document.getElementById('buscadorPersonas');
        const btnTodos = document.getElementById('btnSeleccionarTodos');
        const btnNinguno = document.getElementById('btnDeseleccionarTodos');
        const btnNoAsignados = document.getElementById('btnSeleccionarNoAsignados');
        const personasGrid = document.getElementById('personasGrid');

        // ===== FUNCIONES =====
        function actualizarContador() {
            const seleccionados = document.querySelectorAll('.persona-checkbox:checked').length;
            contador.textContent = `${seleccionados} seleccionados`;
        }

        function filtrarPersonas() {
            const texto = buscador.value.toLowerCase().trim();
            const items = personasGrid.querySelectorAll('.persona-item');
            items.forEach(item => {
                const nombre = item.dataset.nombre || '';
                const ci = item.dataset.ci || '';
                const match = nombre.includes(texto) || ci.includes(texto);
                item.style.display = match ? '' : 'none';
            });
        }

        function seleccionarTodos(checked) {
            const items = personasGrid.querySelectorAll('.persona-item');
            items.forEach(item => {
                if (item.style.display !== 'none') {
                    const cb = item.querySelector('.persona-checkbox');
                    if (cb && !cb.disabled) {
                        cb.checked = checked;
                    }
                }
            });
            actualizarContador();
        }

        // ===== EVENTOS =====
        checkboxes.forEach(cb => cb.addEventListener('change', actualizarContador));

        buscador.addEventListener('input', filtrarPersonas);

        btnTodos.addEventListener('click', () => seleccionarTodos(true));
        btnNinguno.addEventListener('click', () => seleccionarTodos(false));
        btnNoAsignados.addEventListener('click', function() {
            const items = personasGrid.querySelectorAll('.persona-item:not(.asignado)');
            items.forEach(item => {
                if (item.style.display !== 'none') {
                    const cb = item.querySelector('.persona-checkbox');
                    if (cb) cb.checked = true;
                }
            });
            actualizarContador();
        });

        modalElement.addEventListener('shown.bs.modal', function() {
            actualizarContador();
            buscador.value = '';
            filtrarPersonas();
        });

        // ===== AUXILIARES =====
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
            ocultarErrores();
            document.querySelectorAll('.persona-checkbox').forEach(cb => cb.checked = false);
            actualizarContador();
        }

        // ===== GUARDAR =====
        btnGuardar.addEventListener('click', function() {
            const formData = new FormData(form);
            axios.post('{{ route("asignacion.store") }}', formData)
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
                    console.log(error.response.data);

                    if (error.response && error.response.status === 422) {
                        mostrarErrores(error.response.data.errors);
                    }
                });
        });

        // ===== FINALIZAR =====
        document.querySelectorAll('.btn-finalizar').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('¿Finalizar esta asignación?')) return;
                const id = this.dataset.id;
                axios.delete(`/asignacion/${id}`, {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                })
                .then(response => {
                    if (response.data.success) {
                        const fila = document.getElementById(`asignacion-${id}`);
                        if (fila) fila.remove();
                        alert(response.data.message);
                    } else {
                        alert(response.data.message || 'Error al finalizar.');
                    }
                })
                .catch(error => {
                    alert('Error de red al finalizar.');
                    console.error(error);
                });
            });
        });

        // ===== RESET AL CERRAR =====
        modalElement.addEventListener('hidden.bs.modal', function() {
            ocultarErrores();
            resetearFormulario();
        });

        modalElement.addEventListener('show.bs.modal', function() {
            ocultarErrores();
        });
    });

    // ===== EDITAR =====
const modalEditarElement = document.getElementById('modalEditarAsignacion');
const modalEditar = new bootstrap.Modal(modalEditarElement);
const formEditar = document.getElementById('formEditarAsignacion');
const btnActualizar = document.getElementById('btnActualizarAsignacion');
const alertaErroresEditar = document.getElementById('alertaErroresEditar');
const listaErroresEditar = document.getElementById('listaErroresEditar');

function ocultarErroresEditar() {
    alertaErroresEditar.classList.add('d-none');
    listaErroresEditar.innerHTML = '';
}

function mostrarErroresEditar(errors) {
    listaErroresEditar.innerHTML = '';
    for (const campo in errors) {
        const li = document.createElement('li');
        li.textContent = errors[campo][0];
        listaErroresEditar.appendChild(li);
    }
    alertaErroresEditar.classList.remove('d-none');
}

// Abrir modal con los datos de la fila
document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', function() {
        ocultarErroresEditar();
        document.getElementById('edit_asignacion_id').value = this.dataset.id;
        document.getElementById('edit_persona_nombre').value = this.dataset.persona;
        document.getElementById('edit_horario_id').value = this.dataset.horarioId;
        document.getElementById('edit_fecha_inicio').value = this.dataset.fechaInicio;
        document.getElementById('edit_fecha_fin').value = this.dataset.fechaFin || '';
        modalEditar.show();
    });
});

// Enviar actualización (POST + _method=PUT, igual que el resource route espera)
btnActualizar.addEventListener('click', function() {
    const id = document.getElementById('edit_asignacion_id').value;
    const formData = new FormData(formEditar);

    axios.post(`/asignacion/${id}`, formData)
        .then(response => {
            if (response.data.success) {
                sessionStorage.setItem('flash_success', response.data.message);
                modalEditar.hide();
                window.location.reload();
            } else {
                if (response.data.errors) {
                    mostrarErroresEditar(response.data.errors);
                } else {
                    alert(response.data.message || 'Error al actualizar.');
                }
            }
        })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                mostrarErroresEditar(error.response.data.errors);
            } else {
                alert('Error de red al actualizar.');
                console.error(error);
            }
        });
});

modalEditarElement.addEventListener('hidden.bs.modal', ocultarErroresEditar);

// ===== MENSAJE FLASH TRAS RECARGA (para store y update por AJAX) =====
const flash = sessionStorage.getItem('flash_success');
if (flash) {
    sessionStorage.removeItem('flash_success');
    const div = document.createElement('div');
    div.className = 'alert alert-success alert-dismissible fade show';
    div.setAttribute('role', 'alert');
    div.innerHTML = `${flash}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    document.querySelector('.container-fluid').prepend(div);
}
</script>
@endpush