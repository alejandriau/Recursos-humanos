{{-- resources/views/empleado/beneficios/index.blade.php --}}
@extends('layouts.baseusr')

@section('cuerpo')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-gift text-primary me-2"></i>Mis Beneficios
                    </h2>
                    <p class="text-muted mb-0 mt-1">
                        <i class="fas fa-user me-1"></i>
                        {{ auth()->user()->persona->nombre ?? 'Usuario' }}
                        {{ auth()->user()->persona->apellidoPat ?? '' }}
                        {{ auth()->user()->persona->apellidoMat ?? '' }}
                    </p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <a href="{{ route('vacacion.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Solicitar Vacación
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLA RESUMEN DE TODOS LOS BENEFICIOS -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2 text-primary"></i>
                            Resumen de Beneficios
                        </h6>
                        <span class="badge bg-primary">{{ count($beneficiosAgrupados) }} tipos</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Beneficio</th>
                                    <th class="text-center">Períodos</th>
                                    <th class="text-center">Asignado</th>
                                    <th class="text-center">Usado</th>
                                    <th class="text-center">Vencido</th>
                                    <th class="text-center">Disponible</th>
                                    <th class="text-center">Progreso</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totales = [
                                        'periodos' => 0,
                                        'asignado' => 0,
                                        'usado' => 0,
                                        'vencido' => 0,
                                        'disponible' => 0,
                                    ];
                                @endphp

                                @forelse($beneficiosAgrupados as $tipo => $data)
                                @php
                                    $porcentaje = $data['totales']['asignado'] > 0
                                        ? (($data['totales']['asignado'] - $data['totales']['disponible']) / $data['totales']['asignado']) * 100
                                        : 0;
                                    $colorBarra = $porcentaje > 80 ? 'danger' : ($porcentaje > 50 ? 'warning' : 'success');
                                    $icono = $tipo === 'VACACION' ? 'fa-umbrella-beach' : 'fa-gift';
                                    $colorIcono = $tipo === 'VACACION' ? 'primary' : 'success';

                                    // Acumular totales
                                    $totales['periodos'] += count($data['periodos']);
                                    $totales['asignado'] += $data['totales']['asignado'];
                                    $totales['usado'] += $data['totales']['usado'];
                                    $totales['vencido'] += $data['totales']['vencido'];
                                    $totales['disponible'] += $data['totales']['disponible'];
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-sm bg-{{ $colorIcono }}-subtle rounded-circle me-2">
                                                <i class="fas {{ $icono }} text-{{ $colorIcono }}"></i>
                                            </div>
                                            <span class="fw-bold">{{ $tipo }}</span>
                                            @if($tipo === 'VACACION')
                                                <span class="badge bg-primary ms-2">Principal</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ count($data['periodos']) }}</span>
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($data['totales']['asignado'], 1) }}</td>
                                    <td class="text-center text-info">{{ number_format($data['totales']['usado'], 1) }}</td>
                                    <td class="text-center text-warning">{{ number_format($data['totales']['vencido'], 1) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $data['totales']['disponible'] > 0 ? 'success' : 'secondary' }} py-2 px-3">
                                            {{ number_format($data['totales']['disponible'], 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="progress flex-grow-1" style="height: 6px; max-width: 100px;">
                                                <div class="progress-bar bg-{{ $colorBarra }}"
                                                     style="width: {{ $porcentaje }}%">
                                                </div>
                                            </div>
                                            <span class="ms-2 small">{{ number_format($porcentaje, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-outline-primary btn-sm ver-detalle"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalBeneficio"
                                                data-tipo="{{ $tipo }}"
                                                data-asignado="{{ number_format($data['totales']['asignado'], 1) }}"
                                                data-disponible="{{ number_format($data['totales']['disponible'], 1) }}"
                                                data-usado="{{ number_format($data['totales']['usado'], 1) }}"
                                                data-vencido="{{ number_format($data['totales']['vencido'], 1) }}"
                                                data-periodos="{{ count($data['periodos']) }}"
                                                data-unidad="{{ $data['tipo_salida']->unidad ?? 'días' }}"
                                                data-periodos-data='@json($data["periodos"])'>
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox text-muted fs-4 mb-2 d-block"></i>
                                        <p class="text-muted mb-0">No tienes beneficios asignados</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($beneficiosAgrupados) > 0)
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td class="ps-3">TOTALES</td>
                                    <td class="text-center">{{ $totales['periodos'] }}</td>
                                    <td class="text-center">{{ number_format($totales['asignado'], 1) }}</td>
                                    <td class="text-center text-info">{{ number_format($totales['usado'], 1) }}</td>
                                    <td class="text-center text-warning">{{ number_format($totales['vencido'], 1) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success py-2 px-3">
                                            {{ number_format($totales['disponible'], 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas de Vencimientos -->
    @if(!empty($proximosVencimientos))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center flex-wrap">
                        <i class="fas fa-exclamation-triangle text-warning me-2 fs-5"></i>
                        <span class="fw-bold me-3">Próximos vencimientos:</span>
                        @foreach($proximosVencimientos as $vencimiento)
                        <span class="badge bg-{{ $vencimiento['estado'] === 'vencido' ? 'danger' : 'warning' }} me-2 mb-1">
                            {{ $vencimiento['tipo'] }}: {{ $vencimiento['mensaje'] }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Solicitudes Recientes -->
    @if($solicitudesRecientes->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        Solicitudes Recientes
                    </h6>
                    <a href="{{ route('empleado.vacaciones.mi-historial') }}" class="btn btn-sm btn-link">
                        Ver todas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Tipo</th>
                                    <th>Desde</th>
                                    <th>Hasta</th>
                                    <th>Días</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudesRecientes->take(5) as $solicitud)
                                <tr>
                                    <td class="ps-3">
                                        <span class="badge bg-info">
                                            {{ $solicitud->tipoSalida->descripcion ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($solicitud->fechasal)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($solicitud->fecharet)->format('d/m/Y') }}</td>
                                    <td>{{ $solicitud->cantidad }}</td>
                                    <td class="text-center">
                                        @php
                                            $estados = [
                                                'aprobado' => ['class' => 'success', 'text' => 'Aprobado', 'icon' => 'fa-check-circle'],
                                                'pendiente_jefe' => ['class' => 'warning', 'text' => 'Pendiente Jefe', 'icon' => 'fa-clock'],
                                                'pendiente_rrhh' => ['class' => 'warning', 'text' => 'Pendiente RRHH', 'icon' => 'fa-clock'],
                                                'rechazado' => ['class' => 'danger', 'text' => 'Rechazado', 'icon' => 'fa-times-circle'],
                                            ];
                                            $estado = $estados[$solicitud->estado] ?? ['class' => 'secondary', 'text' => $solicitud->estado, 'icon' => 'fa-circle'];
                                        @endphp
                                        <span class="badge bg-{{ $estado['class'] }}">
                                            <i class="fas {{ $estado['icon'] }} me-1"></i>
                                            {{ $estado['text'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="tooltip"
                                                title="Ver detalles de la solicitud">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- MODAL DE DETALLE -->
<div class="modal fade" id="modalBeneficio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-gift text-primary me-2"></i>
                    <span id="modalTitle">Detalle del Beneficio</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Resumen rápido en cards -->
                <div class="row g-2 mb-4">
                    <div class="col-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded text-center">
                            <small class="text-muted d-block">Asignado</small>
                            <span class="fw-bold fs-5" id="modalAsignado">0</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded text-center">
                            <small class="text-muted d-block">Disponible</small>
                            <span class="fw-bold fs-5 text-success" id="modalDisponible">0</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded text-center">
                            <small class="text-muted d-block">Usado</small>
                            <span class="fw-bold fs-5 text-info" id="modalUsado">0</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="bg-warning bg-opacity-10 p-3 rounded text-center">
                            <small class="text-muted d-block">Vencido</small>
                            <span class="fw-bold fs-5 text-warning" id="modalVencido">0</span>
                        </div>
                    </div>
                </div>

                <!-- Períodos -->
                <h6 class="mb-3">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                    Períodos
                    <span class="badge bg-secondary ms-2" id="modalPeriodosCount">0</span>
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Período</th>
                                <th class="text-center">Asignado</th>
                                <th class="text-center">Usado</th>
                                <th class="text-center">Vencido</th>
                                <th class="text-center">Disponible</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="modalPeriodosBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <div class="spinner-border spinner-border-sm me-2" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    Cargando períodos...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
                <a href="#" class="btn btn-primary" id="modalVerDetalle">
                    <i class="fas fa-eye me-1"></i>Ver detalle completo
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.icon-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1);
}

.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1);
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem 0.5rem;
}

.progress {
    background-color: #f0f0f0;
    border-radius: 10px;
}

.card {
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

/* Animación modal */
.modal.fade .modal-dialog {
    transform: scale(0.95);
    transition: transform 0.2s ease;
}

.modal.show .modal-dialog {
    transform: scale(1);
}

/* Hover en filas de tabla */
.table tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.04);
}

/* Badge de disponibilidad */
.badge.bg-success {
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.85rem;
    }

    .table th, .table td {
        padding: 0.5rem 0.3rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Manejar clic en botones "Ver" de la tabla
    document.querySelectorAll('.ver-detalle').forEach(button => {
        button.addEventListener('click', function() {
            // Obtener datos
            const tipo = this.dataset.tipo;
            const asignado = this.dataset.asignado;
            const disponible = this.dataset.disponible;
            const usado = this.dataset.usado;
            const vencido = this.dataset.vencido;
            const periodosCount = this.dataset.periodos;
            const unidad = this.dataset.unidad;
            const periodosData = JSON.parse(this.dataset.periodosData);

            // Llenar modal
            document.getElementById('modalTitle').textContent = tipo;
            document.getElementById('modalAsignado').textContent = asignado + ' ' + unidad;
            document.getElementById('modalDisponible').textContent = disponible + ' ' + unidad;
            document.getElementById('modalUsado').textContent = usado + ' ' + unidad;
            document.getElementById('modalVencido').textContent = vencido + ' ' + unidad;
            document.getElementById('modalPeriodosCount').textContent = periodosCount;

            // Llenar tabla de períodos
            const tbody = document.getElementById('modalPeriodosBody');
            tbody.innerHTML = '';

            if (!periodosData || periodosData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            <i class="fas fa-inbox me-2"></i>
                            No hay períodos registrados
                        </td>
                    </tr>
                `;
            } else {
                periodosData.forEach(p => {
                    const tr = document.createElement('tr');
                    const estado = p.estado || 'activo';
                    const estadoColors = {
                        'activo': 'success',
                        'pendiente': 'warning',
                        'vencido': 'danger',
                        'agotado': 'secondary'
                    };
                    const estadoTexts = {
                        'activo': 'Activo',
                        'pendiente': 'Pendiente',
                        'vencido': 'Vencido',
                        'agotado': 'Agotado'
                    };

                    const periodoNum = p.numero || p.periodo?.numero_periodo || 'N/A';
                    const asignadoVal = p.asignado || p.periodo?.dias_asignados || p.periodo?.cantidad_asignada || 0;
                    const usadoVal = p.usado || p.periodo?.dias_usados || p.periodo?.cantidad_usada || 0;
                    const vencidoVal = p.vencido || p.periodo?.dias_vencidos || p.periodo?.cantidad_vencida || 0;
                    const disponibleVal = p.disponible || p.periodo?.saldo_disponible || 0;

                    tr.innerHTML = `
                        <td><strong>${periodoNum}</strong></td>
                        <td class="text-center">${asignadoVal}</td>
                        <td class="text-center text-info">${usadoVal}</td>
                        <td class="text-center text-warning">${vencidoVal}</td>
                        <td class="text-center">
                            <span class="badge bg-success">${disponibleVal}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-${estadoColors[estado] || 'secondary'}">
                                ${estadoTexts[estado] || estado}
                            </span>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            // Configurar enlace "Ver detalle completo"
            const detalleLink = document.getElementById('modalVerDetalle');
            detalleLink.href = `{{ url('beneficios/detalle') }}/${encodeURIComponent(tipo)}`;
        });
    });
});
</script>
@endsection
