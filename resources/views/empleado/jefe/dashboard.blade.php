{{-- resources/views/jefe/dashboard.blade.php --}}
@extends('layouts.baseusr')

@section('title', 'Panel de Jefe Inmediato')

@section('styles')
<style>
    .stat-card {
        transition: transform 0.3s;
        cursor: default;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .badge-pending {
        background: #ffc107;
        color: #000;
    }
    .badge-approved {
        background: #28a745;
        color: #fff;
    }
    .badge-rejected {
        background: #dc3545;
        color: #fff;
    }
    .solicitud-item {
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }
    .solicitud-item:hover {
        background: #f8f9fa;
        transform: scale(1.01);
    }
    .solicitud-item.vacacion {
        border-left-color: #007bff;
    }
    .solicitud-item.otro {
        border-left-color: #6c757d;
    }
    .btn-action {
        min-width: 80px;
    }
    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
    }
    .fade-in {
        animation: fadeIn 0.5s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('cuerpo')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-user-tie text-primary"></i>
                        Panel de Jefe Inmediato
                    </h2>
                    <small class="text-muted">{{ $jefe->nombre }} {{ $jefe->apellido }}</small>
                </div>
                <div>
                    <span class="badge bg-primary p-2">
                        <i class="fas fa-clock"></i>
                        {{ now()->format('d/m/Y H:i') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-left-primary h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estadisticas['total_pendientes'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-left-success h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Aprobadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estadisticas['aprobadas'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-left-danger h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rechazadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estadisticas['rechazadas'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-left-info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Vacaciones Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estadisticas['vacaciones_pendientes'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-umbrella-beach fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary active" data-filter="all" onclick="filtrarSolicitudes('all')">
                                    <i class="fas fa-list"></i> Todas
                                </button>
                                <button class="btn btn-outline-primary" data-filter="vacacion" onclick="filtrarSolicitudes('vacacion')">
                                    <i class="fas fa-umbrella-beach"></i> Vacaciones
                                </button>
                                <button class="btn btn-outline-primary" data-filter="otros" onclick="filtrarSolicitudes('otros')">
                                    <i class="fas fa-arrow-right"></i> Otras Salidas
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nombre, CI...">
                                <button class="btn btn-primary" onclick="buscarSolicitudes()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Solicitudes Pendientes -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock"></i> Solicitudes Pendientes de Aprobación
                        <span class="badge bg-primary ml-2">{{ $solicitudesPendientes->count() }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($solicitudesPendientes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover" id="solicitudesTable">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAll" onchange="seleccionarTodos()">
                                        </th>
                                        <th>Solicitante</th>
                                        <th>Tipo</th>
                                        <th>Fechas</th>
                                        <th>Días</th>
                                        <th>Fecha Solicitud</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($solicitudesPendientes as $solicitud)
                                    <tr class="solicitud-item {{ $solicitud->tiposalida->descripcion == 'Vacación' ? 'vacacion' : 'otro' }}">
                                        <td>
                                            <input type="checkbox" class="solicitud-checkbox" value="{{ $solicitud->id }}">
                                        </td>
                                        <td>
                                            <strong>{{ $solicitud->persona->nombre }} {{ $solicitud->persona->apellido }}</strong>
                                            <br>
                                            <small class="text-muted">CI: {{ $solicitud->persona->ci }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $solicitud->tiposalida->descripcion == 'Vacación' ? 'badge-info' : 'badge-secondary' }}">
                                                {{ $solicitud->tiposalida->descripcion }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="fas fa-calendar-alt"></i>
                                                {{ Carbon\Carbon::parse($solicitud->fechasal)->format('d/m/Y') }}
                                                <br>
                                                <i class="fas fa-calendar-check"></i>
                                                {{ Carbon\Carbon::parse($solicitud->fecharet)->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $solicitud->cantidad }} días</span>
                                        </td>
                                        <td>
                                            <small>{{ Carbon\Carbon::parse($solicitud->fechasol)->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-success btn-action" onclick="aprobarSolicitud({{ $solicitud->id }})">
                                                    <i class="fas fa-check"></i> Aprobar
                                                </button>
                                                <button class="btn btn-sm btn-danger btn-action" onclick="mostrarModalRechazo({{ $solicitud->id }})">
                                                    <i class="fas fa-times"></i> Rechazar
                                                </button>
                                                <button class="btn btn-sm btn-info btn-action" onclick="verDetalle({{ $solicitud->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Acciones masivas -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span id="selectedCount">0</span> solicitudes seleccionadas
                                    </div>
                                    <div>
                                        <button class="btn btn-success" onclick="aprobarMasivo()" id="btnAprobarMasivo" disabled>
                                            <i class="fas fa-check-double"></i> Aprobar Seleccionados
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h5>No hay solicitudes pendientes</h5>
                            <p class="text-muted">Todas las solicitudes han sido procesadas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Solicitudes Procesadas -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">
                        <i class="fas fa-history"></i> Historial de Solicitudes Procesadas
                    </h6>
                </div>
                <div class="card-body">
                    @if($solicitudesProcesadas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Solicitante</th>
                                        <th>Tipo</th>
                                        <th>Fechas</th>
                                        <th>Estado</th>
                                        <th>Fecha Procesado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($solicitudesProcesadas as $solicitud)
                                    <tr>
                                        <td>{{ $solicitud->persona->nombre }} {{ $solicitud->persona->apellido }}</td>
                                        <td>{{ $solicitud->tiposalida->descripcion }}</td>
                                        <td>
                                            {{ Carbon\Carbon::parse($solicitud->fechasal)->format('d/m/Y') }} -
                                            {{ Carbon\Carbon::parse($solicitud->fecharet)->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $solicitud->estado_jefe == 'aprobado' ? 'badge-success' : 'badge-danger' }}">
                                                {{ ucfirst($solicitud->estado_jefe) }}
                                            </span>
                                        </td>
                                        <td>{{ $solicitud->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No hay solicitudes procesadas aún</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rechazo -->
<div class="modal fade" id="modalRechazo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rechazar Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formRechazo">
                    @csrf
                    <input type="hidden" id="rechazo_id" name="id">
                    <div class="mb-3">
                        <label for="observacion_rechazo" class="form-label">Motivo del Rechazo *</label>
                        <textarea class="form-control" id="observacion_rechazo" name="observacion" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarRechazo()">Rechazar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleContent">
                <!-- Cargado vía AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let solicitudesSeleccionadas = [];
let solicitudARechazar = null;

// Seleccionar todos
function seleccionarTodos() {
    const checkboxes = document.querySelectorAll('.solicitud-checkbox');
    const selectAll = document.getElementById('selectAll');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    actualizarSeleccion();
}

// Actualizar contador de seleccionados
function actualizarSeleccion() {
    const checkboxes = document.querySelectorAll('.solicitud-checkbox:checked');
    const count = checkboxes.length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('btnAprobarMasivo').disabled = count === 0;

    solicitudesSeleccionadas = Array.from(checkboxes).map(cb => parseInt(cb.value));
}

// Event listener para checkboxes
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.solicitud-checkbox').forEach(cb => {
        cb.addEventListener('change', actualizarSeleccion);
    });
});

// Aprobar individual
function aprobarSolicitud(id) {
    if (!confirm('¿Está seguro de aprobar esta solicitud?')) return;

    showLoading();

    // Usando Fetch API
    fetch(`/jefe/aprobar/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            observacion: ''
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        showAlert('success', data.mensaje);
        setTimeout(() => location.reload(), 1500);
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'Error al aprobar la solicitud');
        console.error('Error:', error);
    });
}

// Mostrar modal de rechazo
function mostrarModalRechazo(id) {
    solicitudARechazar = id;
    document.getElementById('rechazo_id').value = id;
    document.getElementById('observacion_rechazo').value = '';

    // Usando Bootstrap 5 modal con JavaScript puro
    const modal = new bootstrap.Modal(document.getElementById('modalRechazo'));
    modal.show();
}

// Confirmar rechazo
function confirmarRechazo() {
    const form = document.getElementById('formRechazo');
    const formData = new FormData(form);

    if (!formData.get('observacion')) {
        showAlert('warning', 'Debe ingresar un motivo de rechazo');
        return;
    }

    showLoading();

    fetch(`/jefe/rechazar/${solicitudARechazar}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalRechazo'));
        modal.hide();
        showAlert('success', data.mensaje);
        setTimeout(() => location.reload(), 1500);
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'Error al rechazar la solicitud');
        console.error('Error:', error);
    });
}

// Aprobar masivo
function aprobarMasivo() {
    if (solicitudesSeleccionadas.length === 0) {
        showAlert('warning', 'Seleccione al menos una solicitud');
        return;
    }

    if (!confirm(`¿Está seguro de aprobar ${solicitudesSeleccionadas.length} solicitudes?`)) return;

    showLoading();

    fetch('/jefe/aprobar-masivo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ids: solicitudesSeleccionadas,
            observacion: 'Aprobado masivamente'
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        showAlert('success', data.mensaje);
        setTimeout(() => location.reload(), 1500);
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'Error en aprobación masiva');
        console.error('Error:', error);
    });
}

// Ver detalle
function verDetalle(id) {
    showLoading();

    fetch(`/jefe/ver-solicitud/${id}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        hideLoading();
        document.getElementById('detalleContent').innerHTML = html;
        const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
        modal.show();
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'Error al cargar el detalle');
        console.error('Error:', error);
    });
}

// Filtrar solicitudes
function filtrarSolicitudes(tipo) {
    // Actualizar botones activos
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-filter="${tipo}"]`)?.classList.add('active');

    if (tipo === 'all') {
        location.reload();
        return;
    }

    showLoading();

    fetch(`/jefe/filtrar/${tipo}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        actualizarTabla(data.solicitudes);
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'Error al filtrar');
        console.error('Error:', error);
    });
}

// Buscar solicitudes
function buscarSolicitudes() {
    const q = document.getElementById('searchInput').value;
    if (q.length < 2) {
        showAlert('warning', 'Ingrese al menos 2 caracteres para buscar');
        return;
    }

    showLoading();

    fetch(`/jefe/buscar?q=${encodeURIComponent(q)}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        actualizarTabla(data.solicitudes);
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'Error en la búsqueda');
        console.error('Error:', error);
    });
}

// Actualizar tabla con resultados
function actualizarTabla(solicitudes) {
    const tbody = document.querySelector('#solicitudesTable tbody');
    if (!tbody) return;

    if (solicitudes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-search fa-2x text-muted"></i>
                    <p class="text-muted mt-2">No se encontraron solicitudes</p>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    solicitudes.forEach(s => {
        html += `
            <tr class="solicitud-item ${s.tiposalida.descripcion == 'Vacación' ? 'vacacion' : 'otro'}">
                <td>
                    <input type="checkbox" class="solicitud-checkbox" value="${s.id}">
                </td>
                <td>
                    <strong>${s.persona.nombre} ${s.persona.apellido}</strong>
                    <br><small class="text-muted">CI: ${s.persona.ci}</small>
                </td>
                <td>
                    <span class="badge ${s.tiposalida.descripcion == 'Vacación' ? 'bg-info' : 'bg-secondary'}">
                        ${s.tiposalida.descripcion}
                    </span>
                </td>
                <td>
                    <small>
                        <i class="fas fa-calendar-alt"></i> ${formatDate(s.fechasal)}
                        <br>
                        <i class="fas fa-calendar-check"></i> ${formatDate(s.fecharet)}
                    </small>
                </td>
                <td><span class="badge bg-primary">${s.cantidad} días</span></td>
                <td><small>${formatDate(s.created_at)}</small></td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-success" onclick="aprobarSolicitud(${s.id})">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="mostrarModalRechazo(${s.id})">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn btn-sm btn-info" onclick="verDetalle(${s.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;

    // Reasignar eventos a los nuevos checkboxes
    document.querySelectorAll('.solicitud-checkbox').forEach(cb => {
        cb.addEventListener('change', actualizarSeleccion);
    });
}

function formatDate(date) {
    if (!date) return '';
    try {
        return new Date(date).toLocaleDateString('es-BO', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    } catch {
        return date;
    }
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' :
                      type === 'error' ? 'alert-danger' : 'alert-warning';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alert.style.cssText = 'z-index: 9999; min-width: 300px;';
    alert.role = 'alert';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.insertBefore(alert, document.body.firstChild);

    setTimeout(() => {
        alert.remove();
    }, 5000);
}

function showLoading() {
    // Mostrar overlay de loading
    const overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    `;
    overlay.innerHTML = `
        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
    `;
    document.body.appendChild(overlay);
    document.body.style.cursor = 'wait';
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.remove();
    document.body.style.cursor = 'default';
}

// Inicializar eventos para el enter en búsqueda
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                buscarSolicitudes();
            }
        });
    }
});
</script>
@endsection
