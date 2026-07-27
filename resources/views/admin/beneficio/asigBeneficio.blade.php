{{-- resources/views/admin/beneficio/asigBeneficio.blade.php --}}
@extends('layouts.baseadm')

@section('title', 'Asignación de Beneficios')

@section('content_header')
<div class="alert alert-secondary" role="alert">
    <div class="row align-items-center">
        <div class="col-md-6">
            <b><i class="fas fa-gift me-2"></i>ASIGNACIÓN DE BENEFICIOS AL PERSONAL</b>
        </div>
        <div class="col-md-6 text-md-end">
            <i class="fas fa-user me-1"></i>{{ auth()->user()->name }}
            <span class="badge bg-primary ms-2">Admin</span>
        </div>
    </div>
</div>
@stop

@section('contenido')
<div class="container-fluid">
    <!-- Panel Principal -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-user-plus text-primary me-2"></i>
                Asignar Beneficios al Personal
            </h5>
        </div>
        <div class="card-body">
            <!-- Formulario de búsqueda y selección -->
            <div class="row g-3 mb-4">
                <div class="col-md-5">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="busquedaPersonal"
                               placeholder="Buscar por nombre, apellido o CI">
                        <label for="busquedaPersonal">
                            <i class="fas fa-search me-1"></i> Buscar personal
                        </label>
                    </div>
                    <div id="resultadoBusqueda" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;"></div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="personalSeleccionado"
                               placeholder="Personal seleccionado" readonly>
                        <label for="personalSeleccionado">
                            <i class="fas fa-user-check me-1"></i> Personal seleccionado
                        </label>
                        <input type="hidden" id="persona_id">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-select" id="gestion_id">
                            @foreach ($gestiones as $gestion)
                            <option value="{{ $gestion->id }}">{{ $gestion->anio }}</option>
                            @endforeach
                        </select>
                        <label for="gestion_id">
                            <i class="fas fa-calendar me-1"></i> Gestión
                        </label>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Beneficios Disponibles -->
            <div class="mb-3">
                <h6 class="mb-3">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Tipos de Beneficios Asignables
                    <span class="badge bg-secondary ms-2">{{ $tiposalidas->count() }}</span>
                    <span class="badge bg-info ms-2" id="totalAsignados">0 asignados</span>
                </h6>

                <div class="row g-3" id="beneficiosContainer">
                    @foreach ($tiposalidas as $tipo)
                    <div class="col-xl-3 col-lg-4 col-md-6" id="card_{{ $tipo->id }}">
                        <div class="card h-100 tipo-card border-0 shadow-sm" data-tipoid="{{ $tipo->id }}">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input tipo-check" type="checkbox"
                                           value="{{ $tipo->id }}" id="tipo_{{ $tipo->id }}"
                                           data-unidad="{{ $tipo->unidad }}"
                                           data-periodicidad="{{ $tipo->periodicidad }}"
                                           data-default="{{ $tipo->cantidad_default }}">
                                    <label class="form-check-label" for="tipo_{{ $tipo->id }}">
                                        <strong>{{ $tipo->descripcion }}</strong>
                                        <span class="badge asignado-badge" style="display:none; background-color: #28a745;">
                                            <i class="fas fa-check me-1"></i>Asignado
                                        </span>
                                        @if ($tipo->usa_tabla_antiguedad)
                                            <span class="badge bg-warning text-dark ms-1">
                                                <i class="fas fa-clock me-1"></i>Antigüedad
                                            </span>
                                        @endif
                                        @if ($tipo->requiere_aprobacion_jefe)
                                            <span class="badge bg-info text-white ms-1">
                                                <i class="fas fa-user-tie me-1"></i>Jefe
                                            </span>
                                        @endif
                                    </label>
                                </div>

                                <div class="mt-2">
                                    <div class="row small text-muted">
                                        <div class="col-6">
                                            <i class="fas fa-ruler me-1"></i>
                                            Unidad: {{ ucfirst($tipo->unidad ?? 'N/A') }}
                                        </div>
                                        <div class="col-6">
                                            <i class="fas fa-sync me-1"></i>
                                            {{ ucfirst($tipo->periodicidad ?? 'N/A') }}
                                        </div>
                                    </div>
                                    @if ($tipo->cantidad_default !== null)
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-clock me-1"></i>
                                        Default: {{ $tipo->cantidad_default }}
                                    </div>
                                    @endif
                                </div>

                                <div class="mt-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <input type="number" min="0" step="0.1"
                                               class="form-control cantidad-input"
                                               placeholder="Cantidad"
                                               id="cantidad_{{ $tipo->id }}" disabled>
                                        <span class="input-group-text">{{ $tipo->unidad ?? 'días' }}</span>
                                    </div>
                                    <!-- Mostrar cantidad asignada actual -->
                                    <div class="small text-success mt-1 cantidad-asignada" style="display:none;">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Asignado: <span class="cantidad-valor">0</span>
                                        <span class="unidad-texto">{{ $tipo->unidad ?? 'días' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-primary" onclick="guardarBeneficios()">
                            <i class="fas fa-save me-2"></i>Asignar Beneficios
                        </button>
                        <button class="btn btn-success" onclick="asignarVacaciones()">
                            <i class="fas fa-umbrella-beach me-2"></i>Asignar Vacaciones
                        </button>
                        <button class="btn btn-info" onclick="cargarBeneficiosAsignados()">
                            <i class="fas fa-sync me-2"></i>Actualizar Tabla
                        </button>
                        <button class="btn btn-secondary" onclick="limpiarFormulario()">
                            <i class="fas fa-undo me-2"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Beneficios Asignados -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-table text-primary me-2"></i>
                Beneficios Asignados
            </h5>
            <span class="badge bg-primary" id="totalBeneficios">0</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaBeneficios">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Tipo</th>
                            <th class="text-center">Asignado</th>
                            <th class="text-center">Usado</th>
                            <th class="text-center">Disponible</th>
                            <th>Gestión</th>
                            <th>Período</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Origen</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaBeneficiosBody">
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fs-3 mb-2 d-block"></i>
                                Seleccione un empleado para ver sus beneficios
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabla de Vacaciones Asignadas -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-umbrella-beach text-primary me-2"></i>
                Vacaciones Asignadas
            </h5>
            <span class="badge bg-success" id="totalVacaciones">0</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaVacaciones">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Período</th>
                            <th class="text-center">Años Antigüedad</th>
                            <th class="text-center">Asignado</th>
                            <th class="text-center">Usado</th>
                            <th class="text-center">Vencido</th>
                            <th class="text-center">Arrastre</th>
                            <th class="text-center">Disponible</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaVacacionesBody">
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fs-3 mb-2 d-block"></i>
                                Seleccione un empleado para ver sus vacaciones
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edición de Beneficio -->
<div class="modal fade" id="modalEditarCantidad" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Editar Cantidad de Beneficio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarCantidad">
                    <input type="hidden" id="beneficioEditarId">
                    <div class="form-floating mb-3">
                        <input type="number" min="0" step="0.1" class="form-control"
                               id="nuevaCantidad" placeholder="Nueva cantidad">
                        <label for="nuevaCantidad">Nueva Cantidad</label>
                    </div>
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-1"></i>
                        La cantidad no puede ser menor a lo ya usado por el empleado.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarEdicionCantidad()">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vacaciones -->
<div class="modal fade" id="modalVacaciones" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-umbrella-beach text-primary me-2"></i>
                    Asignar Vacaciones
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formVacaciones">
                    <input type="hidden" id="vacacion_persona_id">
                    <input type="hidden" id="vacacion_gestion_id">

                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="vacacion_dias"
                               placeholder="Días" min="1" step="1">
                        <label for="vacacion_dias">Días de Vacación</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="vacacion_anios"
                               placeholder="Años" min="0" step="1">
                        <label for="vacacion_anios">Años de Antigüedad</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="vacacion_periodo"
                               placeholder="Año" min="2000" step="1" value="{{ date('Y') }}">
                        <label for="vacacion_periodo">Año del Período</label>
                    </div>

                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-1"></i>
                        La asignación de vacaciones creará un período y un movimiento de crédito inicial.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="guardarVacaciones()">
                    <i class="fas fa-save me-1"></i>Asignar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.tipo-card {
    transition: all 0.2s ease;
    position: relative;
}
.tipo-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important;
}
.tipo-card .card-body {
    padding: 1rem;
}
.tipo-check:checked + label {
    color: #0d6efd;
}
.cantidad-input:disabled {
    background-color: #f8f9fa;
}
.cantidad-input:enabled {
    background-color: #fff;
    border-color: #0d6efd;
}
#resultadoBusqueda .list-group-item {
    cursor: pointer;
    border-left: 3px solid transparent;
}
#resultadoBusqueda .list-group-item:hover {
    background-color: #e7f1ff;
    border-left-color: #0d6efd;
}
.table th {
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.badge {
    font-weight: 500;
}
/* Estilo para tarjeta con beneficio asignado */
.tipo-card.asignado {
    border-left: 4px solid #28a745;
    background-color: #f8fff8;
}
.tipo-card .asignado-badge {
    font-size: 0.65rem;
    padding: 0.2rem 0.5rem;
}
.cantidad-asignada {
    font-size: 0.8rem;
}
</style>

<script>
let timeoutBusqueda = null;
let beneficiosAsignados = {};
let vacacionesAsignadas = [];

document.addEventListener('DOMContentLoaded', function() {
    // Autocompletado de búsqueda
    document.getElementById('busquedaPersonal').addEventListener('input', function() {
        clearTimeout(timeoutBusqueda);
        const valor = this.value.trim();

        if (valor.length < 2) {
            document.getElementById('resultadoBusqueda').innerHTML = '';
            return;
        }

        timeoutBusqueda = setTimeout(async () => {
            try {
                const res = await axios.get(`{{ route('buscar.personal') }}?q=${valor}`);
                const resultados = res.data;
                const resultadoDiv = document.getElementById('resultadoBusqueda');
                resultadoDiv.innerHTML = '';

                if (resultados.length === 0) {
                    resultadoDiv.innerHTML = `
                        <div class="list-group-item text-muted text-center">
                            <i class="fas fa-search me-1"></i> No se encontraron resultados
                        </div>
                    `;
                    return;
                }

                resultados.forEach(p => {
                    const item = document.createElement('a');
                    item.classList.add('list-group-item', 'list-group-item-action');
                    item.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${p.nombre} ${p.apellidoPat} ${p.apellidoMat || ''}</strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-id-card me-1"></i>${p.ci}
                                </small>
                            </div>
                            <span class="badge bg-primary">
                                <i class="fas fa-check me-1"></i>Seleccionar
                            </span>
                        </div>
                    `;
                    item.onclick = () => {
                        seleccionarPersonal(p.id, p.nombre, p.apellidoPat, p.apellidoMat, p.ci);
                    };
                    resultadoDiv.appendChild(item);
                });
            } catch (error) {
                console.error('Error en búsqueda:', error);
            }
        }, 300);
    });

    // Manejo de checkboxes
    document.querySelectorAll('.tipo-check').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const cantidadInput = document.getElementById('cantidad_' + this.value);
            cantidadInput.disabled = !this.checked;
            if (this.checked && this.dataset.default) {
                cantidadInput.value = this.dataset.default;
            } else if (!this.checked) {
                cantidadInput.value = '';
            }
        });
    });
});

// Seleccionar personal
async function seleccionarPersonal(id, nombre, apellidoPat, apellidoMat, ci) {
    const nombreCompleto = `${nombre} ${apellidoPat} ${apellidoMat || ''}`.trim();
    document.getElementById('personalSeleccionado').value = `${nombreCompleto} (${ci})`;
    document.getElementById('persona_id').value = id;
    document.getElementById('resultadoBusqueda').innerHTML = '';

    // Cargar todos los datos del empleado
    await cargarDatosEmpleado(id);
}

// Cargar todos los datos del empleado (beneficios + vacaciones)
async function cargarDatosEmpleado(personaId) {
    const gestionId = document.getElementById('gestion_id').value;

    try {
        // Cargar beneficios
        const resBeneficios = await axios.get(`{{ route('beneficios.obtener') }}`, {
            params: { persona_id: personaId, gestion_id: gestionId }
        });

        // Cargar vacaciones
        const resVacaciones = await axios.get(`{{ route('vacaciones.obtener') }}`, {
            params: { persona_id: personaId, gestion_id: gestionId }
        });

        beneficiosAsignados = resBeneficios.data;
        vacacionesAsignadas = resVacaciones.data;

        // Mostrar en tablas
        mostrarBeneficiosAsignados(beneficiosAsignados);
        mostrarVacacionesAsignadas(vacacionesAsignadas);

        // Resaltar en tarjetas
        resaltarBeneficiosAsignados(beneficiosAsignados);

    } catch (error) {
        console.error('Error al cargar datos del empleado:', error);
        Swal.fire('Error', 'No se pudieron cargar los datos del empleado.', 'error');
    }
}

// Resaltar beneficios ya asignados en las tarjetas
function resaltarBeneficiosAsignados(beneficios) {
    // Limpiar resaltados anteriores
    document.querySelectorAll('.tipo-card').forEach(card => {
        card.classList.remove('asignado');
        card.querySelector('.asignado-badge').style.display = 'none';
        card.querySelector('.cantidad-asignada').style.display = 'none';
    });

    if (!beneficios || beneficios.length === 0) {
        document.getElementById('totalAsignados').textContent = '0 asignados';
        return;
    }

    let totalAsignados = 0;

    beneficios.forEach(b => {
        const tipoId = b.tiposalida_id;
        const card = document.querySelector(`#card_${tipoId}`);
        if (card) {
            const tipoCard = card.querySelector('.tipo-card');
            tipoCard.classList.add('asignado');

            const badge = tipoCard.querySelector('.asignado-badge');
            badge.style.display = 'inline-block';

            const cantidadDiv = tipoCard.querySelector('.cantidad-asignada');
            cantidadDiv.style.display = 'block';
            cantidadDiv.querySelector('.cantidad-valor').textContent = b.cantidad_asignada;

            totalAsignados++;

            // Marcar checkbox si tiene cantidad
            const checkbox = document.getElementById(`tipo_${tipoId}`);
            if (b.cantidad_asignada > 0) {
                checkbox.checked = true;
                const cantidadInput = document.getElementById(`cantidad_${tipoId}`);
                cantidadInput.disabled = false;
                cantidadInput.value = b.cantidad_asignada;
            }
        }
    });

    document.getElementById('totalAsignados').textContent = `${totalAsignados} asignados`;
}

// Mostrar beneficios en tabla
function mostrarBeneficiosAsignados(data) {
    const tbody = document.getElementById('tablaBeneficiosBody');
    tbody.innerHTML = '';

    if (!data || data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fs-3 mb-2 d-block"></i>
                    No hay beneficios asignados para este empleado
                </td>
            </tr>
        `;
        document.getElementById('totalBeneficios').textContent = '0';
        return;
    }

    data.forEach(beneficio => {
        const tr = document.createElement('tr');
        tr.id = `beneficio-${beneficio.id}`;

        const estadoColors = {
            'activo': 'success',
            'pendiente': 'warning',
            'vencido': 'danger',
            'agotado': 'secondary'
        };

        tr.innerHTML = `
            <td class="ps-3">
                <span class="fw-bold">${beneficio.tiposalida?.descripcion || 'N/A'}</span>
            </td>
            <td class="text-center fw-bold">${beneficio.cantidad_asignada ?? 'N/A'}</td>
            <td class="text-center text-info">${beneficio.cantidad_usada ?? 0}</td>
            <td class="text-center">
                <span class="badge bg-${beneficio.saldo_disponible > 0 ? 'success' : 'secondary'}">
                    ${beneficio.saldo_disponible ?? 0}
                </span>
            </td>
            <td>${beneficio.gestion?.anio || 'N/A'}</td>
            <td>${beneficio.mes ? 'Mes ' + beneficio.mes : 'Anual'}</td>
            <td class="text-center">
                <span class="badge bg-${estadoColors[beneficio.estado] || 'secondary'}">
                    ${beneficio.estado || 'activo'}
                </span>
            </td>
            <td class="text-center">
                <span class="badge bg-primary">Beneficio</span>
            </td>
            <td class="text-center">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-warning" onclick="editarBeneficio(${beneficio.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="eliminarBeneficio(${beneficio.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('totalBeneficios').textContent = data.length;
}

// Mostrar vacaciones en tabla
function mostrarVacacionesAsignadas(data) {
    const tbody = document.getElementById('tablaVacacionesBody');
    tbody.innerHTML = '';

    if (!data || data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fs-3 mb-2 d-block"></i>
                    No hay vacaciones asignadas para este empleado
                </td>
            </tr>
        `;
        document.getElementById('totalVacaciones').textContent = '0';
        return;
    }

    data.forEach(vacacion => {
        const tr = document.createElement('tr');
        tr.id = `vacacion-${vacacion.id}`;

        const estadoColors = {
            'activo': 'success',
            'pendiente': 'warning',
            'vencido': 'danger',
            'agotado': 'secondary'
        };

        tr.innerHTML = `
            <td class="ps-3">
                <span class="fw-bold">Período ${vacacion.numero_periodo}</span>
            </td>
            <td class="text-center">${vacacion.anios_antiguedad}</td>
            <td class="text-center fw-bold">${vacacion.dias_asignados}</td>
            <td class="text-center text-info">${vacacion.dias_usados}</td>
            <td class="text-center text-warning">${vacacion.dias_vencidos}</td>
            <td class="text-center">${vacacion.dias_arrastre}</td>
            <td class="text-center">
                <span class="badge bg-${vacacion.saldo_disponible > 0 ? 'success' : 'secondary'}">
                    ${vacacion.saldo_disponible}
                </span>
            </td>
            <td class="text-center">
                <span class="badge bg-${estadoColors[vacacion.estado] || 'secondary'}">
                    ${vacacion.estado || 'activo'}
                </span>
            </td>
            <td class="text-center">
                <span class="badge bg-success">Vacación</span>
            </td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('totalVacaciones').textContent = data.length;
}

// Guardar beneficios
async function guardarBeneficios() {
    const personaId = document.getElementById('persona_id').value;
    const gestionId = document.getElementById('gestion_id').value;
    const beneficios = [];

    document.querySelectorAll('.tipo-check:checked').forEach(chk => {
        const cantidad = document.getElementById('cantidad_' + chk.value).value;
        if (cantidad && parseFloat(cantidad) > 0) {
            beneficios.push({
                tiposalida_id: chk.value,
                cantidad: parseFloat(cantidad)
            });
        }
    });

    if (!personaId) {
        return Swal.fire('Atención', 'Debe seleccionar un empleado.', 'warning');
    }

    if (beneficios.length === 0) {
        return Swal.fire('Atención', 'Debe seleccionar al menos un beneficio con cantidad válida.', 'warning');
    }

    try {
        const res = await axios.post('{{ route("guardar.beneficios") }}', {
            persona_id: personaId,
            gestion_id: gestionId,
            beneficios: beneficios
        });

        Swal.fire('¡Éxito!', 'Beneficios asignados correctamente.', 'success');

        // Recargar datos del empleado
        await cargarDatosEmpleado(personaId);

        // Limpiar selección de checkboxes
        document.querySelectorAll('.tipo-check:checked').forEach(chk => {
            chk.checked = false;
            document.getElementById('cantidad_' + chk.value).value = '';
            document.getElementById('cantidad_' + chk.value).disabled = true;
        });

    } catch (error) {
        let mensaje = 'Error al asignar beneficios';
        if (error.response?.data?.errors) {
            mensaje = Object.values(error.response.data.errors).flat().join('<br>');
        } else if (error.response?.data?.message) {
            mensaje = error.response.data.message;
        }
        Swal.fire('Error', mensaje, 'error');
    }
}

// Asignar vacaciones
function asignarVacaciones() {
    const personaId = document.getElementById('persona_id').value;
    const gestionId = document.getElementById('gestion_id').value;

    if (!personaId) {
        return Swal.fire('Atención', 'Debe seleccionar un empleado.', 'warning');
    }

    document.getElementById('vacacion_persona_id').value = personaId;
    document.getElementById('vacacion_gestion_id').value = gestionId;
    document.getElementById('vacacion_dias').value = '';
    document.getElementById('vacacion_anios').value = '';
    document.getElementById('vacacion_periodo').value = new Date().getFullYear();

    const modal = new bootstrap.Modal(document.getElementById('modalVacaciones'));
    modal.show();
}

async function guardarVacaciones() {
    const personaId = document.getElementById('vacacion_persona_id').value;
    const gestionId = document.getElementById('vacacion_gestion_id').value;
    const dias = document.getElementById('vacacion_dias').value;
    const anios = document.getElementById('vacacion_anios').value;
    const periodo = document.getElementById('vacacion_periodo').value;

    if (!dias || parseFloat(dias) < 1) {
        return Swal.fire('Atención', 'Ingrese una cantidad válida de días.', 'warning');
    }

    try {
        const res = await axios.post('{{ route("beneficios.asignar.vacaciones") }}', {
            persona_id: personaId,
            gestion_id: gestionId,
            dias: parseFloat(dias),
            anios_antiguedad: parseInt(anios) || 0,
            numero_periodo: parseInt(periodo) || new Date().getFullYear()
        });

        Swal.fire('¡Éxito!', 'Vacaciones asignadas correctamente.', 'success');

        // Cerrar modal
        bootstrap.Modal.getInstance(document.getElementById('modalVacaciones')).hide();

        // Recargar datos del empleado
        await cargarDatosEmpleado(personaId);

    } catch (error) {
        let mensaje = 'Error al asignar vacaciones';
        if (error.response?.data?.errors) {
            mensaje = Object.values(error.response.data.errors).flat().join('<br>');
        } else if (error.response?.data?.message) {
            mensaje = error.response.data.message;
        }
        Swal.fire('Error', mensaje, 'error');
    }
}

// Cargar beneficios asignados (para el botón Actualizar)
async function cargarBeneficiosAsignados() {
    const personaId = document.getElementById('persona_id').value;
    if (!personaId) return;
    await cargarDatosEmpleado(personaId);
}

// Eliminar beneficio
async function eliminarBeneficio(id) {
    const confirmacion = await Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmacion.isConfirmed) return;

    try {
        await axios.delete(`{{ url('admin/beneficios') }}/${id}`);
        Swal.fire('Eliminado', 'Beneficio eliminado correctamente.', 'success');
        await cargarDatosEmpleado(document.getElementById('persona_id').value);
    } catch (error) {
        Swal.fire('Error', error.response?.data?.message || 'No se pudo eliminar el beneficio.', 'error');
    }
}

// Editar beneficio
function editarBeneficio(id) {
    const fila = document.querySelector(`#beneficio-${id}`);
    if (!fila) return;

    const cantidad = fila.children[1]?.textContent?.trim() || '';
    document.getElementById('beneficioEditarId').value = id;
    document.getElementById('nuevaCantidad').value = cantidad;

    const modal = new bootstrap.Modal(document.getElementById('modalEditarCantidad'));
    modal.show();
}

async function guardarEdicionCantidad() {
    const id = document.getElementById('beneficioEditarId').value;
    const nuevaCantidad = document.getElementById('nuevaCantidad').value;

    if (!nuevaCantidad || parseFloat(nuevaCantidad) < 0) {
        return Swal.fire('Atención', 'Ingrese una cantidad válida.', 'warning');
    }

    try {
        await axios.put(`{{ url('admin/beneficios') }}/${id}`, {
            cantidad: parseFloat(nuevaCantidad)
        });

        Swal.fire('Actualizado', 'Cantidad actualizada correctamente.', 'success');

        bootstrap.Modal.getInstance(document.getElementById('modalEditarCantidad')).hide();
        await cargarDatosEmpleado(document.getElementById('persona_id').value);

    } catch (error) {
        let mensaje = 'Error al actualizar';
        if (error.response?.data?.message) {
            mensaje = error.response.data.message;
        }
        Swal.fire('Error', mensaje, 'error');
    }
}

// Limpiar formulario
function limpiarFormulario() {
    document.getElementById('busquedaPersonal').value = '';
    document.getElementById('resultadoBusqueda').innerHTML = '';
    document.getElementById('personalSeleccionado').value = '';
    document.getElementById('persona_id').value = '';
    document.getElementById('gestion_id').selectedIndex = 0;

    // Limpiar tablas
    document.getElementById('tablaBeneficiosBody').innerHTML = `
        <tr>
            <td colspan="9" class="text-center py-4 text-muted">
                <i class="fas fa-inbox fs-3 mb-2 d-block"></i>
                Seleccione un empleado para ver sus beneficios
            </td>
        </tr>
    `;
    document.getElementById('tablaVacacionesBody').innerHTML = `
        <tr>
            <td colspan="9" class="text-center py-4 text-muted">
                <i class="fas fa-inbox fs-3 mb-2 d-block"></i>
                Seleccione un empleado para ver sus vacaciones
            </td>
        </tr>
    `;

    document.getElementById('totalBeneficios').textContent = '0';
    document.getElementById('totalVacaciones').textContent = '0';
    document.getElementById('totalAsignados').textContent = '0 asignados';

    // Limpiar resaltados
    document.querySelectorAll('.tipo-card').forEach(card => {
        card.classList.remove('asignado');
        card.querySelector('.asignado-badge').style.display = 'none';
        card.querySelector('.cantidad-asignada').style.display = 'none';
    });

    document.querySelectorAll('.tipo-check:checked').forEach(chk => {
        chk.checked = false;
        document.getElementById('cantidad_' + chk.value).value = '';
        document.getElementById('cantidad_' + chk.value).disabled = true;
    });

    beneficiosAsignados = {};
    vacacionesAsignadas = [];
}

// Cargar al cambiar gestión
document.getElementById('gestion_id').addEventListener('change', function() {
    const personaId = document.getElementById('persona_id').value;
    if (personaId) {
        cargarDatosEmpleado(personaId);
    }
});
</script>
@stop
