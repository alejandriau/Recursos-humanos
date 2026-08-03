{{-- resources/views/jefe/dashboard.blade.php --}}
@extends('layouts.baseusr')

@section('title', 'Panel de Jefe Inmediato')

@section('styles')
<style>
    /* ===== ESTILOS GENERALES ===== */
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: default;
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.10);
    }
    .stat-card .stat-icon {
        font-size: 1.8rem;
        opacity: 0.6;
    }
    .stat-card .stat-number {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .stat-card .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #6c757d;
    }
    /* En móviles reducimos todo */
    @media (max-width: 576px) {
        .stat-card {
            padding: 0.5rem 0.75rem !important;
            border-radius: 8px;
        }
        .stat-card .stat-icon {
            font-size: 1.2rem;
        }
        .stat-card .stat-number {
            font-size: 1.2rem;
        }
        .stat-card .stat-label {
            font-size: 0.6rem;
        }
        .stat-card .col-auto {
            padding-left: 0.25rem !important;
        }
        .stat-card .col {
            padding-right: 0.25rem !important;
        }
        .btn-action {
            min-width: auto !important;
            padding: 0.2rem 0.5rem !important;
            font-size: 0.75rem !important;
        }
        .table-responsive {
            font-size: 0.8rem;
        }
        .table td, .table th {
            padding: 0.4rem 0.3rem;
        }
        .badge {
            font-size: 0.7rem;
        }
        .btn-group .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }
        .filtros-movil {
            flex-wrap: wrap;
            gap: 0.3rem;
        }
        .filtros-movil .btn {
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
        }
    }

    /* ===== ESTILOS PARA LA TABLA ===== */
    .solicitud-item {
        transition: background 0.2s;
        border-left: 4px solid transparent;
    }
    .solicitud-item:hover {
        background: #f8f9fa;
    }
    .solicitud-item.vacacion {
        border-left-color: #007bff;
    }
    .solicitud-item.otro {
        border-left-color: #6c757d;
    }

    .badge-pending { background: #ffc107; color: #000; }
    .badge-approved { background: #28a745; color: #fff; }
    .badge-rejected { background: #dc3545; color: #fff; }

    .fade-in {
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Loading overlay mejorado */
    #loadingOverlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.4);
        backdrop-filter: blur(3px);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    #loadingOverlay .spinner-border {
        width: 3rem;
        height: 3rem;
    }
</style>
@endsection

@section('cuerpo')
<div class="container-fluid py-3 py-md-4">
    <!-- ===== HEADER ===== -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h4 class="mb-0 d-flex align-items-center gap-2">
                        <i class="fas fa-user-tie text-primary"></i>
                        <span>Panel de Jefe</span>
                        <small class="text-muted fs-6 d-none d-sm-inline">{{ $jefe->nombre }} {{ $jefe->apellido }}</small>
                    </h4>
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

    <!-- ===== ESTADÍSTICAS COMPACTAS ===== -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <div class="col-3 col-sm-3 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center p-2 p-sm-3">
                    <div class="col">
                        <div class="stat-label">Pendientes</div>
                        <div class="stat-number text-primary">{{ $estadisticas['total_pendientes'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock stat-icon text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3 col-sm-3 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center p-2 p-sm-3">
                    <div class="col">
                        <div class="stat-label">Aprobadas</div>
                        <div class="stat-number text-success">{{ $estadisticas['aprobadas'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle stat-icon text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3 col-sm-3 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center p-2 p-sm-3">
                    <div class="col">
                        <div class="stat-label">Rechazadas</div>
                        <div class="stat-number text-danger">{{ $estadisticas['rechazadas'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle stat-icon text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3 col-sm-3 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center p-2 p-sm-3">
                    <div class="col">
                        <div class="stat-label">Vac. Pend.</div>
                        <div class="stat-number text-info">{{ $estadisticas['vacaciones_pendientes'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-umbrella-beach stat-icon text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FILTROS Y BÚSQUEDA ===== -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-2 p-md-3">
                    <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2">
                        <div class="d-flex filtros-movil flex-wrap gap-1">
                            <button class="btn btn-outline-primary btn-sm active" data-filter="all" onclick="filtrarSolicitudes('all')">
                                <i class="fas fa-list"></i> <span class="d-none d-sm-inline">Todas</span>
                            </button>
                            <button class="btn btn-outline-primary btn-sm" data-filter="vacacion" onclick="filtrarSolicitudes('vacacion')">
                                <i class="fas fa-umbrella-beach"></i> <span class="d-none d-sm-inline">Vacaciones</span>
                            </button>
                            <button class="btn btn-outline-primary btn-sm" data-filter="otros" onclick="filtrarSolicitudes('otros')">
                                <i class="fas fa-arrow-right"></i> <span class="d-none d-sm-inline">Otras</span>
                            </button>
                        </div>
                        <div class="d-flex flex-grow-1 gap-1">
                            <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Buscar por nombre, CI...">
                            <button class="btn btn-primary btn-sm" onclick="buscarSolicitudes()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SOLICITUDES PENDIENTES ===== -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-2 d-flex align-items-center justify-content-between flex-wrap">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock"></i> Pendientes
                        <span class="badge bg-primary ms-1">{{ $solicitudesPendientes->count() }}</span>
                    </h6>
                    <div>
                        <span id="selectedCount" class="small text-muted">0</span>
                        <span class="small text-muted">seleccionadas</span>
                        <button class="btn btn-success btn-sm ms-2" onclick="aprobarMasivo()" id="btnAprobarMasivo" disabled>
                            <i class="fas fa-check-double"></i> <span class="d-none d-sm-inline">Aprobar</span>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0 p-md-2">
                    @if($solicitudesPendientes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="solicitudesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 30px;">
                                            <input type="checkbox" id="selectAll" onchange="seleccionarTodos()">
                                        </th>
                                        <th>Solicitante</th>
                                        <th>Tipo</th>
                                        <th>Fechas</th>
                                        <th>Días</th>
                                        <th class="d-none d-sm-table-cell">Fecha Sol.</th>
                                        <th style="min-width: 100px;">Acciones</th>
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
                                            <br><small class="text-muted">CI: {{ $solicitud->persona->ci }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $solicitud->tiposalida->descripcion == 'Vacación' ? 'bg-info' : 'bg-secondary' }}">
                                                {{ $solicitud->tiposalida->descripcion }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="fas fa-calendar-alt"></i> {{ Carbon\Carbon::parse($solicitud->fechasal)->format('d/m/Y') }}
                                                <br class="d-md-none">
                                                <i class="fas fa-calendar-check"></i> {{ Carbon\Carbon::parse($solicitud->fecharet)->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        <td><span class="badge bg-primary">{{ $solicitud->cantidad }}</span></td>
                                        <td class="d-none d-sm-table-cell">
                                            <small>{{ Carbon\Carbon::parse($solicitud->fechasol)->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-success" onclick="aprobarSolicitud({{ $solicitud->id }})" title="Aprobar">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-danger" onclick="mostrarModalRechazo({{ $solicitud->id }})" title="Rechazar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <button class="btn btn-info" onclick="verDetalle({{ $solicitud->id }})" title="Detalle">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                            <h6>No hay solicitudes pendientes</h6>
                            <p class="text-muted small">Todas las solicitudes han sido procesadas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ===== HISTORIAL ===== -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-secondary">
                        <i class="fas fa-history"></i> Historial de Solicitudes Procesadas
                    </h6>
                </div>
                <div class="card-body p-0 p-md-2">
                    @if($solicitudesProcesadas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Solicitante</th>
                                        <th>Tipo</th>
                                        <th>Fechas</th>
                                        <th>Estado</th>
                                        <th class="d-none d-sm-table-cell">Procesado</th>
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
                                            <span class="badge {{ $solicitud->estado_jefe == 'aprobado' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($solicitud->estado_jefe) }}
                                            </span>
                                        </td>
                                        <td class="d-none d-sm-table-cell">{{ $solicitud->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center small my-3">No hay solicitudes procesadas aún</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL RECHAZO ===== -->
<div class="modal fade" id="modalRechazo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-times-circle text-danger"></i> Rechazar Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formRechazo">
                    @csrf
                    <input type="hidden" id="rechazo_id" name="id">
                    <div class="mb-3">
                        <label for="observacion_rechazo" class="form-label">Motivo del Rechazo <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="observacion_rechazo" name="observacion" rows="3" required placeholder="Explique el motivo del rechazo..."></textarea>
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

<!-- ===== MODAL DETALLE ===== -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleContent">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== SCRIPTS CON AXIOS ===== -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // ============================================================
    //  CONFIGURACIÓN GLOBAL DE AXIOS
    // ============================================================
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    // Interceptor para manejar errores globalmente (opcional)
    axios.interceptors.response.use(
        response => response,
        error => {
            // Si el error tiene respuesta del backend, mostramos el mensaje exacto
            if (error.response && error.response.data && error.response.data.mensaje) {
                showAlert('error', error.response.data.mensaje);
            } else if (error.response && error.response.status === 422) {
                // Errores de validación
                const errors = error.response.data.errors;
                if (errors) {
                    const firstError = Object.values(errors)[0];
                    showAlert('error', firstError ? firstError[0] : 'Error de validación');
                } else {
                    showAlert('error', 'Error de validación');
                }
            } else {
                showAlert('error', 'Ocurrió un error inesperado. Intente nuevamente.');
            }
            return Promise.reject(error);
        }
    );

    // ============================================================
    //  VARIABLES GLOBALES
    // ============================================================
    let solicitudesSeleccionadas = [];
    let solicitudARechazar = null;

    // ============================================================
    //  FUNCIONES DE UI
    // ============================================================
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' :
                          type === 'error' ? 'alert-danger' : 'alert-warning';
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow`;
        alertDiv.style.cssText = 'z-index: 10000; min-width: 280px; max-width: 90%;';
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.prepend(alertDiv);
        setTimeout(() => alertDiv.remove(), 6000);
    }

    function showLoading() {
        const overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.innerHTML = `
            <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
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

    function formatDate(dateStr) {
        if (!dateStr) return '';
        try {
            return new Date(dateStr).toLocaleDateString('es-BO', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } catch {
            return dateStr;
        }
    }

    // ============================================================
    //  SELECCIÓN MASIVA
    // ============================================================
    function seleccionarTodos() {
        const checkboxes = document.querySelectorAll('.solicitud-checkbox');
        const selectAll = document.getElementById('selectAll');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        actualizarSeleccion();
    }

    function actualizarSeleccion() {
        const checkboxes = document.querySelectorAll('.solicitud-checkbox:checked');
        const count = checkboxes.length;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('btnAprobarMasivo').disabled = count === 0;
        solicitudesSeleccionadas = Array.from(checkboxes).map(cb => parseInt(cb.value));
    }

    // Event listeners para checkboxes (se agregan dinámicamente)
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.solicitud-checkbox').forEach(cb => {
            cb.addEventListener('change', actualizarSeleccion);
        });
    });

    // ============================================================
    //  APROBAR INDIVIDUAL
    // ============================================================
    async function aprobarSolicitud(id) {
        if (!confirm('¿Aprobar esta solicitud?')) return;
        showLoading();
        try {
            const response = await axios.post(`/jefe/aprobar/${id}`, { observacion: '' });
            showAlert('success', response.data.mensaje);
            setTimeout(() => location.reload(), 1500);
        } catch (error) {
            // El interceptor ya muestra el error
            console.error(error);
        } finally {
            hideLoading();
        }
    }

    // ============================================================
    //  RECHAZO
    // ============================================================
    function mostrarModalRechazo(id) {
        solicitudARechazar = id;
        document.getElementById('rechazo_id').value = id;
        document.getElementById('observacion_rechazo').value = '';
        const modal = new bootstrap.Modal(document.getElementById('modalRechazo'));
        modal.show();
    }

    async function confirmarRechazo() {
        const form = document.getElementById('formRechazo');
        const formData = new FormData(form);
        const observacion = formData.get('observacion');
        if (!observacion || observacion.trim() === '') {
            showAlert('warning', 'Debe ingresar un motivo de rechazo');
            return;
        }
        showLoading();
        try {
            const response = await axios.post(`/jefe/rechazar/${solicitudARechazar}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalRechazo'));
            modal.hide();
            showAlert('success', response.data.mensaje);
            setTimeout(() => location.reload(), 1500);
        } catch (error) {
            // Error ya manejado por el interceptor
            console.error(error);
        } finally {
            hideLoading();
        }
    }

    // ============================================================
    //  APROBAR MASIVO
    // ============================================================
    async function aprobarMasivo() {
        if (solicitudesSeleccionadas.length === 0) {
            showAlert('warning', 'Seleccione al menos una solicitud');
            return;
        }
        if (!confirm(`¿Aprobar ${solicitudesSeleccionadas.length} solicitud(es)?`)) return;
        showLoading();
        try {
            const response = await axios.post('/jefe/aprobar-masivo', {
                ids: solicitudesSeleccionadas,
                observacion: 'Aprobado masivamente'
            });
            showAlert('success', response.data.mensaje);
            setTimeout(() => location.reload(), 1500);
        } catch (error) {
            console.error(error);
        } finally {
            hideLoading();
        }
    }

    // ============================================================
    //  VER DETALLE
    // ============================================================
    async function verDetalle(id) {
        showLoading();
        try {
            const response = await axios.get(`/jefe/ver-solicitud/${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            document.getElementById('detalleContent').innerHTML = response.data;
            const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
            modal.show();
        } catch (error) {
            // Error ya mostrado por el interceptor
            console.error(error);
        } finally {
            hideLoading();
        }
    }

    // ============================================================
    //  FILTRAR
    // ============================================================
    async function filtrarSolicitudes(tipo) {
        document.querySelectorAll('[data-filter]').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-filter="${tipo}"]`)?.classList.add('active');

        if (tipo === 'all') {
            location.reload();
            return;
        }
        showLoading();
        try {
            const response = await axios.get(`/jefe/filtrar/${tipo}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            actualizarTabla(response.data.solicitudes);
        } catch (error) {
            console.error(error);
        } finally {
            hideLoading();
        }
    }

    // ============================================================
    //  BUSCAR
    // ============================================================
    async function buscarSolicitudes() {
        const q = document.getElementById('searchInput').value.trim();
        if (q.length < 2) {
            showAlert('warning', 'Ingrese al menos 2 caracteres');
            return;
        }
        showLoading();
        try {
            const response = await axios.get(`/jefe/buscar?q=${encodeURIComponent(q)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            actualizarTabla(response.data.solicitudes);
        } catch (error) {
            console.error(error);
        } finally {
            hideLoading();
        }
    }

    // ============================================================
    //  ACTUALIZAR TABLA (después de filtro/búsqueda)
    // ============================================================
    function actualizarTabla(solicitudes) {
        const tbody = document.querySelector('#solicitudesTable tbody');
        if (!tbody) return;

        if (solicitudes.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-search fa-2x text-muted"></i>
                        <p class="text-muted mt-1 small">No se encontraron solicitudes</p>
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
                            <br class="d-md-none">
                            <i class="fas fa-calendar-check"></i> ${formatDate(s.fecharet)}
                        </small>
                    </td>
                    <td><span class="badge bg-primary">${s.cantidad}</span></td>
                    <td class="d-none d-sm-table-cell">
                        <small>${formatDate(s.created_at)}</small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-success" onclick="aprobarSolicitud(${s.id})" title="Aprobar">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-danger" onclick="mostrarModalRechazo(${s.id})" title="Rechazar">
                                <i class="fas fa-times"></i>
                            </button>
                            <button class="btn btn-info" onclick="verDetalle(${s.id})" title="Detalle">
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

        // Resetear selección
        document.getElementById('selectAll').checked = false;
        actualizarSeleccion();
    }

    // ============================================================
    //  EVENTOS ADICIONALES
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') buscarSolicitudes();
            });
        }
    });
</script>
@endsection
