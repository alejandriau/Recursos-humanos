{{-- resources/views/admin/solicitudes/dashboard.blade.php --}}
@extends('layouts.baseadm')

@section('title', 'Panel de Recursos Humanos')

@section('styles')
<style>
    /* ===== ESTILOS GENERALES ===== */
    body {
        background-color: #f8f9fc;
    }

    .stat-card {
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        border: none;
        border-radius: 12px;
        overflow: hidden;
        cursor: default;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #fff;
    }
    .stat-card .stat-icon.primary { background: linear-gradient(135deg, #4e73df, #224abe); }
    .stat-card .stat-icon.success { background: linear-gradient(135deg, #1cc88a, #13855c); }
    .stat-card .stat-icon.danger { background: linear-gradient(135deg, #e74a3b, #be2617); }
    .stat-card .stat-icon.warning { background: linear-gradient(135deg, #f6c23e, #dda20a); }
    .stat-card .stat-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.2;
        color: #2d3748;
    }
    .stat-card .stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6c757d;
        font-weight: 600;
    }

    /* ===== TABS ===== */
    .custom-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 600;
        padding: 0.75rem 1.25rem;
        transition: all 0.2s;
        border-radius: 0;
    }
    .custom-tabs .nav-link:hover {
        color: #4e73df;
        border-bottom-color: #b7c9f2;
    }
    .custom-tabs .nav-link.active {
        color: #4e73df;
        border-bottom-color: #4e73df;
        background: transparent;
    }
    .custom-tabs .nav-link .badge {
        margin-left: 6px;
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
    }

    /* ===== TABLAS ===== */
    .table-solicitudes {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .table-solicitudes thead th {
        background: #f8f9fc;
        border-bottom: 2px solid #e3e6f0;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #4a5568;
        padding: 0.75rem 0.5rem;
    }
    .table-solicitudes tbody td {
        vertical-align: middle;
        padding: 0.6rem 0.5rem;
        border-bottom: 1px solid #edf2f7;
    }
    .table-solicitudes tbody tr:hover {
        background-color: #f8faff;
        transition: background 0.15s;
    }
    .table-solicitudes tbody tr.solicitud-pendiente {
        animation: pulse-bg 2s infinite;
    }
    @keyframes pulse-bg {
        0% { background-color: transparent; }
        50% { background-color: #fff8e7; }
        100% { background-color: transparent; }
    }

    .badge-estado {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.7rem;
        text-transform: capitalize;
    }
    .btn-group-sm .btn {
        padding: 0.2rem 0.5rem;
        font-size: 0.7rem;
        border-radius: 6px;
    }

    /* ===== SPINNER ===== */
    .spinner-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        transition: opacity 0.3s;
    }
    .spinner-overlay.active {
        display: flex;
    }
    .spinner-overlay .spinner-border {
        width: 3.5rem;
        height: 3.5rem;
        border-width: 0.3rem;
        color: #4e73df;
    }

    /* ===== TOOLBAR ===== */
    .toolbar-search {
        border-radius: 30px;
        border: 1px solid #e2e8f0;
        padding: 0.4rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s;
        background: #fff;
    }
    .toolbar-search:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
        outline: none;
    }
    .btn-filter {
        border-radius: 30px;
        padding: 0.4rem 1.2rem;
        font-weight: 500;
        font-size: 0.8rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #4a5568;
        transition: all 0.15s;
    }
    .btn-filter:hover {
        background: #f8f9fc;
        border-color: #b7c9f2;
    }
    .btn-filter.active {
        background: #4e73df;
        border-color: #4e73df;
        color: #fff;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .stat-card .stat-number {
            font-size: 1.6rem;
        }
        .custom-tabs .nav-link {
            padding: 0.5rem 0.8rem;
            font-size: 0.8rem;
        }
        .toolbar-search {
            width: 100% !important;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">


    <!-- ===== HEADER ===== -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">
                <i class="fas fa-user-tie text-primary me-2"></i>
                Panel de Recursos Humanos
            </h2>
            <small class="text-muted">
                <i class="fas fa-user-circle me-1"></i>
                {{ $rrhh->nombre ?? '' }} {{ $rrhh->apellido ?? '' }}
                <span class="mx-2">|</span>
                <i class="far fa-clock me-1"></i>
                {{ now()->format('d/m/Y H:i') }}
            </small>
        </div>
        <div>
            <a href="{{ route('rrhh.reporte-periodos') }}" class="btn btn-outline-info btn-sm rounded-pill px-3">
                <i class="fas fa-chart-bar me-1"></i> Reportes
            </a>
            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i> Actualizar
            </button>
        </div>
    </div>

    <!-- ===== ESTADÍSTICAS ===== -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon primary me-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="stat-number" id="totalPendientes">{{ $estadisticas['total_pendientes'] }}</div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon success me-3">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <div class="stat-number" id="aprobadasMes">{{ $estadisticas['aprobadas_mes'] }}</div>
                        <div class="stat-label">Aprobadas este mes</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon danger me-3">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div>
                        <div class="stat-number" id="rechazadasMes">{{ $estadisticas['rechazadas_mes'] }}</div>
                        <div class="stat-label">Rechazadas este mes</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon warning me-3">
                        <i class="fas fa-umbrella-beach"></i>
                    </div>
                    <div>
                        <div class="stat-number" id="vacacionesPendientes">{{ $estadisticas['vacaciones_pendientes'] }}</div>
                        <div class="stat-label">Vacaciones pendientes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== TOOLBAR (Búsqueda y Filtros) ===== -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-5 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control toolbar-search ps-0" 
                               placeholder="Buscar por nombre, CI..." 
                               onkeyup="if(event.key==='Enter') buscarSolicitudes()">
                    </div>
                </div>
                <div class="col-md-7 col-lg-8 d-flex flex-wrap gap-2 justify-content-md-end">
                    <button class="btn-filter active" data-filter="todos" onclick="filtrarSolicitudes('todos')">
                        <i class="fas fa-list me-1"></i> Todos
                    </button>
                    <button class="btn-filter" data-filter="vacacion" onclick="filtrarSolicitudes('vacacion')">
                        <i class="fas fa-umbrella-beach me-1"></i> Vacaciones
                    </button>
                    <button class="btn-filter" data-filter="permiso" onclick="filtrarSolicitudes('permiso')">
                        <i class="fas fa-file-alt me-1"></i> Permisos
                    </button>
                    <button class="btn btn-success btn-sm rounded-pill px-3" id="btnAprobarMasivo" disabled onclick="aprobarMasivo()">
                        <i class="fas fa-check me-1"></i> Aprobar seleccionados 
                        <span id="selectedCount" class="badge bg-light text-dark ms-1">0</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== TABS DE SOLICITUDES ===== -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent border-bottom-0 pt-3">
            <ul class="nav nav-tabs custom-tabs" id="solicitudTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pendientes-tab" data-bs-toggle="tab" href="#pendientes" role="tab">
                        <i class="fas fa-clock me-1"></i> Pendientes
                        <span class="badge bg-danger" id="badgePendientes">{{ $solicitudesPendientes->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="aprobados-tab" data-bs-toggle="tab" href="#aprobados" role="tab">
                        <i class="fas fa-check-circle text-success me-1"></i> Aprobados
                        <span class="badge bg-success" id="badgeAprobados">{{ $solicitudesAprobadas->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="rechazados-tab" data-bs-toggle="tab" href="#rechazados" role="tab">
                        <i class="fas fa-times-circle text-danger me-1"></i> Rechazados
                        <span class="badge bg-danger" id="badgeRechazados">{{ $solicitudesRechazadas->count() }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body pt-0">
            <div class="tab-content" id="solicitudTabsContent">
                <!-- Tab Pendientes -->
                <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
                    @include('admin.solicitudes.partials.tab-pendientes', ['solicitudes' => $solicitudesPendientes])
                </div>

                <!-- Tab Aprobados -->
                <div class="tab-pane fade" id="aprobados" role="tabpanel">
                    @include('admin.solicitudes.partials.tab-aprobados', ['solicitudes' => $solicitudesAprobadas])
                </div>

                <!-- Tab Rechazados -->
                <div class="tab-pane fade" id="rechazados" role="tabpanel">
                    @include('admin.solicitudes.partials.tab-rechazados', ['solicitudes' => $solicitudesRechazadas])
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODALES ===== -->
@include('admin.solicitudes.modals.modal-rechazo')
@include('admin.solicitudes.modals.modal-detalle')

<!-- ===== SCRIPTS ===== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    window.solicitudesSeleccionadas = [];

    // Checkboxes
    document.querySelectorAll('.solicitud-checkbox').forEach(cb => {
        cb.addEventListener('change', actualizarSeleccion);
    });

    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.solicitud-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            actualizarSeleccion();
        });
    }

    // Inicializar estado del botón masivo
    actualizarSeleccion();
});

// ===== FUNCIONES GLOBALES =====
window.actualizarSeleccion = function() {
    const checkboxes = document.querySelectorAll('.solicitud-checkbox:checked');
    const count = checkboxes.length;
    const selectedCount = document.getElementById('selectedCount');
    const btnAprobarMasivo = document.getElementById('btnAprobarMasivo');

    if (selectedCount) selectedCount.textContent = count;
    if (btnAprobarMasivo) btnAprobarMasivo.disabled = count === 0;

    window.solicitudesSeleccionadas = Array.from(checkboxes).map(cb => parseInt(cb.value));
};

window.aprobarSolicitud = function(id) {
    if (!confirm('¿Está seguro de aprobar esta solicitud? Esta acción descontará los días.')) return;
    showLoading();
    axios.post(`/solicitudes/aprobar/${id}`, { observacion: '' })
        .then(response => {
            hideLoading();
            showAlert('success', response.data.mensaje);
            setTimeout(() => location.reload(), 1500);
        })
        .catch(error => {
            hideLoading();
            const mensaje = error.response?.data?.error || 'Error al aprobar';
            showAlert('error', mensaje);
        });
};

window.mostrarModalRechazo = function(id) {
    document.getElementById('rechazo_id').value = id;
    document.getElementById('observacion_rechazo').value = '';
    const modal = new bootstrap.Modal(document.getElementById('modalRechazo'));
    modal.show();
};

window.confirmarRechazo = function() {
    const id = document.getElementById('rechazo_id').value;
    const observacion = document.getElementById('observacion_rechazo').value;
    if (!observacion) {
        showAlert('warning', 'Debe ingresar un motivo de rechazo');
        return;
    }
    showLoading();
    axios.post(`/solicitudes/rechazar/${id}`, { observacion: observacion })
        .then(response => {
            hideLoading();
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalRechazo'));
            if (modal) modal.hide();
            showAlert('success', response.data.mensaje);
            setTimeout(() => location.reload(), 1500);
        })
        .catch(error => {
            hideLoading();
            showAlert('error', error.response?.data?.error || 'Error al rechazar');
        });
};

window.aprobarMasivo = function() {
    if (window.solicitudesSeleccionadas.length === 0) {
        showAlert('warning', 'Seleccione al menos una solicitud');
        return;
    }
    if (!confirm(`¿Aprobar ${window.solicitudesSeleccionadas.length} solicitud(es)?`)) return;
    showLoading();
    axios.post('/solicitudes/aprobar-masivo', {
        ids: window.solicitudesSeleccionadas,
        observacion: 'Aprobado masivamente por RRHH'
    })
    .then(response => {
        hideLoading();
        showAlert('success', response.data.mensaje);
        setTimeout(() => location.reload(), 1500);
    })
    .catch(error => {
        hideLoading();
        showAlert('error', error.response?.data?.error || 'Error en aprobación masiva');
    });
};

window.verDetalle = function(id) {
    showLoading();
    axios.get(`/solicitudes/ver-solicitud/${id}`)
        .then(response => {
            hideLoading();
            document.getElementById('detalleContent').innerHTML = response.data;
            const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
            modal.show();
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Error al cargar el detalle');
        });
};

window.filtrarSolicitudes = function(tipo) {
    showLoading();
    axios.get('/solicitudes/filtrar', { params: { filtro: tipo } })
        .then(response => {
            hideLoading();
            actualizarTabla(response.data.solicitudes);
            // Actualizar clases de botones de filtro
            document.querySelectorAll('.btn-filter').forEach(btn => btn.classList.remove('active'));
            document.querySelector(`.btn-filter[data-filter="${tipo}"]`)?.classList.add('active');
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Error al filtrar');
        });
};

window.buscarSolicitudes = function() {
    const q = document.getElementById('searchInput')?.value?.trim() || '';
    if (q.length > 0 && q.length < 2) {
        showAlert('warning', 'Ingrese al menos 2 caracteres');
        return;
    }
    if (q.length === 0) {
        // Si está vacío, recargar la tabla con todas
        filtrarSolicitudes('todos');
        return;
    }
    showLoading();
    axios.get('/solicitudes/buscar', { params: { q: q } })
        .then(response => {
            hideLoading();
            actualizarTabla(response.data.solicitudes);
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Error en la búsqueda');
        });
};

window.actualizarTabla = function(solicitudes) {
    const tbody = document.querySelector('#solicitudesTable tbody');
    if (!tbody) return;

    if (solicitudes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x d-block mb-2"></i>
                    No hay solicitudes que coincidan
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    solicitudes.forEach(s => {
        const isVacacion = s.tiposalida?.descripcion == 'Vacación';
        html += `
            <tr class="solicitud-item">
                <td><input type="checkbox" class="solicitud-checkbox" value="${s.id}"></td>
                <td>
                    <strong>${s.persona?.nombre || 'N/A'} ${s.persona?.apellido || ''}</strong>
                    <br><small class="text-muted">CI: ${s.persona?.ci || 'N/A'}</small>
                </td>
                <td>
                    <span class="badge ${isVacacion ? 'bg-info' : 'bg-secondary'} badge-estado">
                        ${s.tiposalida?.descripcion || 'N/A'}
                    </span>
                </td>
                <td>
                    <small>${formatDate(s.fechasal)} - ${formatDate(s.fecharet)}</small>
                </td>
                <td class="text-center">${s.cantidad || 0}</td>
                <td>
                    <span class="badge ${s.estado_jefe == 'aprobado' ? 'bg-success' : 'bg-warning'} badge-estado">
                        ${s.estado_jefe || 'pendiente'}
                    </span>
                </td>
                <td>${formatDate(s.created_at)}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-success" onclick="aprobarSolicitud(${s.id})" title="Aprobar">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="mostrarModalRechazo(${s.id})" title="Rechazar">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn btn-outline-info" onclick="verDetalle(${s.id})" title="Detalle">
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
        cb.addEventListener('change', window.actualizarSeleccion);
    });
    window.actualizarSeleccion();
};

// ===== UTILIDADES =====
window.formatDate = function(date) {
    if (!date) return 'N/A';
    try {
        return new Date(date).toLocaleDateString('es-BO', {
            day: '2-digit', month: '2-digit', year: 'numeric'
        });
    } catch(e) { return 'N/A'; }
};

window.showAlert = function(type, message) {
    const alertClass = type === 'success' ? 'alert-success' :
                      type === 'error' ? 'alert-danger' : 'alert-warning';
    const icon = type === 'success' ? 'fa-check-circle' :
                 type === 'error' ? 'fa-exclamation-circle' : 'fa-exclamation-triangle';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alert.style.cssText = 'z-index: 9999; min-width: 300px; max-width: 500px; box-shadow: 0 8px 20px rgba(0,0,0,0.15);';
    alert.innerHTML = `
        <i class="fas ${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alert);
    setTimeout(() => { if (alert.parentNode) alert.remove(); }, 5000);
};

window.showLoading = function() {
    document.getElementById('globalSpinner')?.classList.add('active');
    document.body.style.cursor = 'wait';
};

window.hideLoading = function() {
    document.getElementById('globalSpinner')?.classList.remove('active');
    document.body.style.cursor = 'default';
};

// ===== ACTUALIZACIÓN PERIÓDICA DE ESTADÍSTICAS =====
setInterval(function() {
    axios.get('/solicitudes/estadisticas')
        .then(response => {
            const data = response.data;
            const pendientesEl = document.getElementById('totalPendientes');
            const aprobadasEl = document.getElementById('aprobadasMes');
            const rechazadasEl = document.getElementById('rechazadasMes');
            const vacacionesEl = document.getElementById('vacacionesPendientes');
            const badgePendientes = document.getElementById('badgePendientes');
            if (pendientesEl) pendientesEl.textContent = data.pendientes || 0;
            if (aprobadasEl) aprobadasEl.textContent = data.aprobados_hoy || 0;
            if (rechazadasEl) rechazadasEl.textContent = data.rechazados_hoy || 0;
            if (vacacionesEl) vacacionesEl.textContent = data.vacaciones_pendientes || 0;
            if (badgePendientes) badgePendientes.textContent = data.pendientes || 0;
        })
        .catch(error => console.error('Error actualizando estadísticas:', error));
}, 30000);

// Cerrar modales con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.show').forEach(modal => {
            const instance = bootstrap.Modal.getInstance(modal);
            if (instance) instance.hide();
        });
    }
});
</script>
@endsection