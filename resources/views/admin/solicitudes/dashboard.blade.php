{{-- resources/views/rrhh/dashboard.blade.php --}}
@extends('layouts.baseadm')

@section('title', 'Panel de RRHH')

@section('styles')
<style>
    .stat-card {
        transition: all 0.3s;
        border-left: 4px solid transparent;
        cursor: default;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .stat-card.primary { border-left-color: #4e73df; }
    .stat-card.success { border-left-color: #1cc88a; }
    .stat-card.danger { border-left-color: #e74a3b; }
    .stat-card.info { border-left-color: #36b9cc; }
    .stat-card.warning { border-left-color: #f6c23e; }

    .solicitud-pendiente {
        animation: pulse-bg 2s infinite;
    }
    @keyframes pulse-bg {
        0% { background-color: transparent; }
        50% { background-color: #fff3cd; }
        100% { background-color: transparent; }
    }

    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #4e73df;
        font-weight: bold;
    }

    .badge-estado {
        padding: 5px 10px;
        border-radius: 20px;
    }

    .scrollable-tab {
        max-height: 400px;
        overflow-y: auto;
    }

    /* Spinner de carga */
    .spinner-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.7);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    .spinner-overlay.active {
        display: flex;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Spinner de carga global -->
    <div class="spinner-overlay" id="globalSpinner">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="fas fa-building text-primary"></i>
                Panel de Recursos Humanos
            </h2>
            <small class="text-muted">
                {{ $rrhh->nombre ?? '' }} {{ $rrhh->apellido ?? '' }}
            </small>
        </div>
        <div>
            <a href="{{ route('rrhh.reporte-periodos') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> Reporte de Períodos
            </a>
            <span class="badge bg-primary p-2 ms-2">
                <i class="fas fa-clock"></i> {{ now()->format('d/m/Y H:i') }}
            </span>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <i class="fas fa-clock"></i> Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPendientes">
                                {{ $estadisticas['total_pendientes'] }}
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-check-circle"></i> Aprobadas Este Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="aprobadasMes">
                                {{ $estadisticas['aprobadas_mes'] }}
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                <i class="fas fa-times-circle"></i> Rechazadas Este Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rechazadasMes">
                                {{ $estadisticas['rechazadas_mes'] }}
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <i class="fas fa-umbrella-beach"></i> Vacaciones Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="vacacionesPendientes">
                                {{ $estadisticas['vacaciones_pendientes'] }}
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-sun fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs de Solicitudes -->
    <div class="card shadow">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="solicitudTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pendientes-tab" data-bs-toggle="tab" href="#pendientes" role="tab">
                        <i class="fas fa-clock"></i> Pendientes
                        <span class="badge bg-danger" id="badgePendientes">{{ $solicitudesPendientes->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="aprobados-tab" data-bs-toggle="tab" href="#aprobados" role="tab">
                        <i class="fas fa-check-circle text-success"></i> Aprobados
                        <span class="badge bg-success" id="badgeAprobados">{{ $solicitudesAprobadas->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="rechazados-tab" data-bs-toggle="tab" href="#rechazados" role="tab">
                        <i class="fas fa-times-circle text-danger"></i> Rechazados
                        <span class="badge bg-danger" id="badgeRechazados">{{ $solicitudesRechazadas->count() }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
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

    <!-- Períodos de Vacación Activos -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-calendar-alt"></i> Períodos de Vacación Activos
                    </h6>
                </div>
                <div class="card-body">
                    @if($periodosActivos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Servidor</th>
                                        <th>Antigüedad</th>
                                        <th>Días Asignados</th>
                                        <th>Días Usados</th>
                                        <th>Saldo Disponible</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($periodosActivos as $periodo)
                                    <tr>
                                        <td>{{ $periodo->persona->nombre }} {{ $periodo->persona->apellido }}</td>
                                        <td>{{ $periodo->anios_antiguedad }} años</td>
                                        <td>{{ $periodo->dias_asignados }}</td>
                                        <td>{{ $periodo->dias_usados }}</td>
                                        <td>
                                            <span class="badge {{ $periodo->saldo_disponible > 10 ? 'bg-success' : ($periodo->saldo_disponible > 5 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $periodo->saldo_disponible }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $periodo->estado == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($periodo->estado) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No hay períodos activos</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales -->
@include('admin.solicitudes.modals.modal-rechazo')
@include('admin.solicitudes.modals.modal-detalle')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar Axios con CSRF
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // Variables globales
    window.solicitudesSeleccionadas = [];

    // Event listeners para checkboxes
    document.querySelectorAll('.solicitud-checkbox').forEach(cb => {
        cb.addEventListener('change', actualizarSeleccion);
    });

    // Event listener para "Seleccionar todos"
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.solicitud-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            actualizarSeleccion();
        });
    }
});

// Funciones globales
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

    axios.post(`/solicitudes/aprobar/${id}`, {
        observacion: ''
    })
    .then(function(response) {
        hideLoading();
        showAlert('success', response.data.mensaje);
        setTimeout(() => location.reload(), 1500);
    })
    .catch(function(error) {
        hideLoading();

        console.error('Error completo:', error);
        console.error('Response:', error.response);
        console.error('Data:', error.response?.data);

        let mensajeError = 'Error al aprobar la solicitud';
        let detalles = '';

        if (error.response) {
            const data = error.response.data;

            if (data.error) {
                mensajeError = data.error;
            }

            if (data.message) {
                detalles += `Mensaje: ${data.message}\n`;
            }

            if (data.line) {
                detalles += `Línea: ${data.line}\n`;
            }

            if (data.file) {
                detalles += `Archivo: ${data.file}\n`;
            }

            if (data.errors) {
                const erroresValidacion = Object.values(data.errors).flat();
                detalles += `Errores de validación:\n${erroresValidacion.join('\n')}\n`;
            }

            if (data.trace) {
                detalles += `\nTrace:\n${data.trace}`;
            }

            // Si hay datos adicionales de debug
            if (data.debug) {
                detalles += `\n\nDebug:\n${JSON.stringify(data.debug, null, 2)}`;
            }

            console.error('Detalles del error:', data);
        } else if (error.request) {
            mensajeError = 'No se recibió respuesta del servidor';
            detalles = 'Verifique su conexión a internet';
        } else {
            mensajeError = 'Error al realizar la petición';
            detalles = error.message || 'Error desconocido';
        }

        // Mostrar el error detallado en el modal
        if (typeof mostrarErrorDetallado === 'function') {
            mostrarErrorDetallado(mensajeError, detalles);
        } else {
            // Fallback a alerta simple
            showAlert('error', mensajeError + '\n' + detalles);
        }
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

    axios.post(`/solicitudes/rechazar/${id}`, {
        observacion: observacion
    })
    .then(function(response) {
        hideLoading();
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalRechazo'));
        if (modal) modal.hide();
        showAlert('success', response.data.mensaje);
        setTimeout(() => location.reload(), 1500);
    })
    .catch(function(error) {
        hideLoading();
        const mensaje = error.response?.data?.error || 'Error al rechazar la solicitud';
        showAlert('error', mensaje);
    });
};

window.aprobarMasivo = function() {
    if (window.solicitudesSeleccionadas.length === 0) {
        showAlert('warning', 'Seleccione al menos una solicitud');
        return;
    }

    if (!confirm(`¿Está seguro de aprobar ${window.solicitudesSeleccionadas.length} solicitudes? Esta acción descontará los días.`)) return;

    showLoading();

    axios.post('/solicitudes/aprobar-masivo', {
        ids: window.solicitudesSeleccionadas,
        observacion: 'Aprobado masivamente por RRHH'
    })
    .then(function(response) {
        hideLoading();
        showAlert('success', response.data.mensaje);
        setTimeout(() => location.reload(), 1500);
    })
    .catch(function(error) {
        hideLoading();
        const mensaje = error.response?.data?.error || 'Error en aprobación masiva';
        showAlert('error', mensaje);
    });
};

window.verDetalle = function(id) {
    showLoading();

    axios.get(`/solicitudes/ver-solicitud/${id}`)
    .then(function(response) {
        hideLoading();
        document.getElementById('detalleContent').innerHTML = response.data;
        const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
        modal.show();
    })
    .catch(function(error) {
        hideLoading();
        showAlert('error', 'Error al cargar el detalle de la solicitud');
    });
};

window.filtrarSolicitudes = function(tipo) {
    showLoading();

    axios.get('/solicitudes/filtrar', {
        params: { filtro: tipo }
    })
    .then(function(response) {
        hideLoading();
        actualizarTabla(response.data.solicitudes);

        // Actualizar badges
        document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
        document.querySelector(`[data-filter="${tipo}"]`)?.classList.add('active');
    })
    .catch(function(error) {
        hideLoading();
        showAlert('error', 'Error al filtrar solicitudes');
    });
};

window.buscarSolicitudes = function() {
    const q = document.getElementById('searchInput')?.value || '';
    if (q.length < 2) {
        showAlert('warning', 'Ingrese al menos 2 caracteres');
        return;
    }

    showLoading();

    axios.get('/solicitudes/buscar', {
        params: { q: q }
    })
    .then(function(response) {
        hideLoading();
        actualizarTabla(response.data.solicitudes);
    })
    .catch(function(error) {
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
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted"></i>
                    <p class="text-muted mt-2">No hay solicitudes</p>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    solicitudes.forEach(s => {
        const isVacacion = s.tiposalida?.descripcion == 'Vacación';
        html += `
            <tr class="solicitud-item ${isVacacion ? 'vacacion' : 'otro'}">
                <td><input type="checkbox" class="solicitud-checkbox" value="${s.id}"></td>
                <td>
                    <strong>${s.persona?.nombre || 'N/A'} ${s.persona?.apellido || ''}</strong>
                    <br><small class="text-muted">CI: ${s.persona?.ci || 'N/A'}</small>
                </td>
                <td>
                    <span class="badge ${isVacacion ? 'bg-info' : 'bg-secondary'}">
                        ${s.tiposalida?.descripcion || 'N/A'}
                    </span>
                </td>
                <td>
                    <small>${formatDate(s.fechasal)} - ${formatDate(s.fecharet)}</small>
                </td>
                <td>${s.cantidad || 0}</td>
                <td>
                    <span class="badge ${s.estado_jefe == 'aprobado' ? 'bg-success' : 'bg-warning'}">
                        ${s.estado_jefe || 'pendiente'}
                    </span>
                </td>
                <td>${formatDate(s.created_at)}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-success" onclick="aprobarSolicitud(${s.id})" title="Aprobar">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-danger" onclick="mostrarModalRechazo(${s.id})" title="Rechazar">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn btn-info" onclick="verDetalle(${s.id})" title="Ver Detalle">
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

    // Actualizar contador de seleccionados
    window.actualizarSeleccion();
};

window.formatDate = function(date) {
    if (!date) return 'N/A';
    try {
        return new Date(date).toLocaleDateString('es-BO', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    } catch(e) {
        return 'N/A';
    }
};

window.showAlert = function(type, message) {
    const alertClass = type === 'success' ? 'alert-success' :
                      type === 'error' ? 'alert-danger' : 'alert-warning';
    const icon = type === 'success' ? 'fa-check-circle' :
                 type === 'error' ? 'fa-exclamation-circle' : 'fa-exclamation-triangle';

    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alert.style.cssText = 'z-index: 9999; min-width: 300px; max-width: 500px;';
    alert.innerHTML = `
        <i class="fas ${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alert);

    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
};

window.showLoading = function() {
    const spinner = document.getElementById('globalSpinner');
    if (spinner) {
        spinner.classList.add('active');
    }
    document.body.style.cursor = 'wait';
};

window.hideLoading = function() {
    const spinner = document.getElementById('globalSpinner');
    if (spinner) {
        spinner.classList.remove('active');
    }
    document.body.style.cursor = 'default';
};

// Actualizar estadísticas cada 30 segundos
setInterval(function() {
    axios.get('/solicitudes/estadisticas')
    .then(function(response) {
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
    .catch(function(error) {
        console.error('Error al actualizar estadísticas:', error);
    });
}, 30000);

// Función para cerrar modales con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modales = document.querySelectorAll('.modal.show');
        modales.forEach(modal => {
            const instance = bootstrap.Modal.getInstance(modal);
            if (instance) instance.hide();
        });
    }
});
</script>
@endsection
