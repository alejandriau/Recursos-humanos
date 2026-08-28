@extends('layouts.baseusr')

@section('cuerpo')
<style>
    .modal-vacaciones {
    max-width: 70%;
}

@media (max-width: 576px) {
    .modal-vacaciones {
        max-width: 100%;
        width: 100%;
        margin: 0;
    }

    .modal-vacaciones .modal-content {
        min-height: 100vh;
        border-radius: 0;
    }

    .modal-vacaciones .modal-body {
        overflow-y: auto;
    }
    
}

    .stats-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }
    .stat-chip {
        flex: 1 1 calc(25% - 12px);
        min-width: 180px;
        background: #fff;
        border-radius: 10px;
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.07);
        border: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: transform .15s;
    }
    .stat-chip:hover { transform: translateY(-2px); }
    .stat-chip-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; flex-shrink: 0;
    }
    .bg-soft-success { background: #d1f2eb; color: #27ae60; }
    .bg-soft-primary { background: #d6eaf8; color: #2980b9; }
    .bg-soft-info    { background: #d1f2eb; color: #1abc9c; }
    .bg-soft-warning { background: #fdebd0; color: #f39c12; }
    .stat-chip-info { min-width: 0; }
    .stat-chip-label { font-size: 12px; color: #6c757d; margin-bottom: 1px; white-space: nowrap; }
    .stat-chip-value { font-size: 20px; font-weight: 700; line-height: 1.2; }
    .stat-chip-unit  { font-size: 11px; color: #adb5bd; }
    .stat-chip-progress { height: 3px; background: #e9ecef; border-radius: 2px; margin-top: 5px; overflow: hidden; }
    .stat-chip-progress-bar { height: 100%; border-radius: 2px; }

    /* Móvil: fila horizontal scrolleable y compacta */
    @media (max-width: 576px) {
        .stats-bar {
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 8px;
            padding-bottom: 4px;
            scrollbar-width: none; /* Firefox */
        }
        .stats-bar::-webkit-scrollbar { display: none; }
        .stat-chip {
            flex: 0 0 auto;
            min-width: 125px;
            padding: 10px 12px;
            gap: 8px;
        }
        .stat-chip-icon { width: 30px; height: 30px; font-size: 13px; border-radius: 8px; }
        .stat-chip-value { font-size: 17px; }
        .stat-chip-label { font-size: 11px; }
        .stat-chip-unit  { font-size: 10px; }
        .stat-chip-progress { margin-top: 3px; }
    }

</style>
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row container-fluid mb-4">
        <!-- Encabezado unificado (igual que Comisiones / Salud / Particular) -->
        <div class="alert alert-secondary text-center" role="alert">
            <h5 class="mb-0"><i class="fas fa-umbrella-beach me-2"></i>Mi Historial de Vacaciones</h5>
        </div>

        <div class="text-start mb-3">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearVacacion">
                <i class="fas fa-plus me-1"></i> Nueva Solicitud
            </button>
            <a href="/homeusr" class="btn btn-secondary">
                <i class="fa fa-times"></i> Cancelar
            </a>
        </div>
    </div>

    <!-- Tarjetas de resumen (igual) -->
    <div class="stats-bar">
        @php
            $porcentaje = $resumen['total_asignado'] > 0
                ? ($resumen['saldo_actual'] / $resumen['total_asignado']) * 100
                : 0;
        @endphp

        <!-- Saldo Disponible -->
        <div class="stat-chip">
            <div class="stat-chip-icon bg-soft-success">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-chip-info">
                <div class="stat-chip-label">Saldo Disponible</div>
                <div class="stat-chip-value text-success">{{ number_format($resumen['saldo_actual'], 1) }}</div>
                <div class="stat-chip-unit">días disponibles</div>
                <div class="stat-chip-progress">
                    <div class="stat-chip-progress-bar bg-success" style="width:{{ $porcentaje }}%"></div>
                </div>
            </div>
        </div>

        <!-- Total Asignado -->
        <div class="stat-chip">
            <div class="stat-chip-icon bg-soft-primary">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-chip-info">
                <div class="stat-chip-label">Total Asignado</div>
                <div class="stat-chip-value text-primary">{{ number_format($resumen['total_asignado'], 1) }}</div>
                <div class="stat-chip-unit">días totales</div>
            </div>
        </div>

        <!-- Días Usados -->
        <div class="stat-chip">
            <div class="stat-chip-icon bg-soft-info">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-chip-info">
                <div class="stat-chip-label">Días Usados</div>
                <div class="stat-chip-value text-info">{{ number_format($resumen['total_usado'], 1) }}</div>
                <div class="stat-chip-unit">días utilizados</div>
            </div>
        </div>

        <!-- Días Vencidos -->
        <div class="stat-chip">
            <div class="stat-chip-icon bg-soft-warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-chip-info">
                <div class="stat-chip-label">Días Vencidos</div>
                <div class="stat-chip-value text-warning">{{ number_format($resumen['total_vencido'], 1) }}</div>
                <div class="stat-chip-unit">días por vencer</div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if($periodosActivos->isEmpty())
        <div class="alert alert-info border-0 shadow-sm">
            <i class="fas fa-info-circle me-2"></i>
            No tienes períodos activos con saldo disponible.
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalCrearVacacion" class="alert-link">Solicita tus vacaciones aquí.</a>
        </div>
    @endif
    @if($resumen['total_vencido'] > 0)
        <div class="alert alert-warning border-0 shadow-sm">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Atención:</strong> Tienes {{ number_format($resumen['total_vencido'], 1) }} días vencidos.
            Recuerda que estos días no se pueden utilizar y se perderán.
        </div>
    @endif

    <!-- Pestañas (igual) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <ul class="nav nav-tabs card-header-tabs" id="vacationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                        <i class="fas fa-list me-2"></i>Solicitudes <span class="badge bg-secondary ms-2">{{ $solicitudes->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="movements-tab" data-bs-toggle="tab" data-bs-target="#movements" type="button" role="tab">
                        <i class="fas fa-exchange-alt me-2"></i>Movimientos <span class="badge bg-secondary ms-2">{{ $movimientos->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                        <i class="fas fa-calendar-day me-2"></i>Períodos Activos <span class="badge bg-primary ms-2">{{ $periodosActivos->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Tab Solicitudes -->
                <!-- Tab Solicitudes -->
                <div class="tab-pane fade show active" id="history" role="tabpanel">
                    <h5 class="mb-3">Mis solicitudes de vacación</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tablaVacaciones">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha solicitud</th>
                                    <th>Desde</th>
                                    <th>Hasta</th>
                                    <th>Días</th>
                                    <th>🧑‍💼 Jefe Inmediato</th>
                                    <th>🏢 RRHH</th>
                                    <th>Observaciones</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyVacaciones">
                                @forelse($solicitudes as $solicitud)
                                    @php
                                        $estadoJefe = strtolower($solicitud->estado_jefe ?? '');
                                        $estadoRRHH = strtolower($solicitud->estado_rrhh ?? '');
                                    @endphp
                                    <tr id="fila-{{ $solicitud->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($solicitud->fechasol)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($solicitud->fechasal)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($solicitud->fecharet)->format('d/m/Y') }}</td>
                                        <td class="fw-bold">{{ $solicitud->cantidad }}</td>

                                        {{-- ===== JEFE INMEDIATO ===== --}}
                                        <td>
                                            @php
                                                $badgeJefe = 'bg-secondary';
                                                $textoJefe = 'Sin estado';
                                                $iconoJefe = 'fa-question-circle';

                                                if ($estadoJefe === 'pendiente') {
                                                    $badgeJefe = 'bg-warning text-dark'; $textoJefe = 'Pendiente'; $iconoJefe = 'fa-clock';
                                                } elseif ($estadoJefe === 'aprobado') {
                                                    $badgeJefe = 'bg-success'; $textoJefe = 'Aprobado'; $iconoJefe = 'fa-check-circle';
                                                } elseif ($estadoJefe === 'rechazado') {
                                                    $badgeJefe = 'bg-danger'; $textoJefe = 'Rechazado'; $iconoJefe = 'fa-times-circle';
                                                } elseif (empty($estadoJefe)) {
                                                    $badgeJefe = 'bg-light text-dark border'; $textoJefe = 'Sin asignar'; $iconoJefe = 'fa-user-slash';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeJefe }} py-2 px-3 mb-1 d-inline-block">
                                                <i class="fas {{ $iconoJefe }} me-1"></i>{{ $textoJefe }}
                                            </span>
                                            <div class="small mt-1">
                                                @if($solicitud->jefe)
                                                    <i class="fas fa-user-tie me-1 text-muted"></i>
                                                    {{ $solicitud->jefe->nombre }} {{ $solicitud->jefe->apellidoPat }}
                                                    @if($solicitud->fecha_aprobacion_jefe)
                                                        <br>
                                                        <span class="text-muted" style="font-size: 0.75rem;">
                                                            <i class="far fa-calendar-alt me-1"></i>
                                                            {{ \Carbon\Carbon::parse($solicitud->fecha_aprobacion_jefe)->format('d/m/Y H:i') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted fst-italic" style="font-size: 0.8rem;">
                                                        <i class="fas fa-user-slash me-1"></i>Sin jefe asignado
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- ===== RRHH ===== --}}
                                        <td>
                                            @php
                                                $rrhhPuedeActuar = $estadoJefe === 'aprobado';
                                                $rrhhNoAplica = $estadoJefe === 'rechazado' || empty($estadoJefe);

                                                $badgeRRHH = 'bg-secondary';
                                                $textoRRHH = 'Desconocido';
                                                $iconoRRHH = 'fa-question-circle';

                                                if ($rrhhNoAplica) {
                                                    $badgeRRHH = 'bg-light text-muted border'; $textoRRHH = 'N/A'; $iconoRRHH = 'fa-minus-circle';
                                                } elseif (!$rrhhPuedeActuar) {
                                                    $badgeRRHH = 'bg-light text-dark border'; $textoRRHH = 'En espera'; $iconoRRHH = 'fa-hourglass-half';
                                                } elseif ($estadoRRHH === 'pendiente') {
                                                    $badgeRRHH = 'bg-warning text-dark'; $textoRRHH = 'Pendiente'; $iconoRRHH = 'fa-clock';
                                                } elseif ($estadoRRHH === 'aprobado') {
                                                    $badgeRRHH = 'bg-success'; $textoRRHH = 'Aprobado'; $iconoRRHH = 'fa-check-circle';
                                                } elseif ($estadoRRHH === 'rechazado') {
                                                    $badgeRRHH = 'bg-danger'; $textoRRHH = 'Rechazado'; $iconoRRHH = 'fa-times-circle';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeRRHH }} py-2 px-3 mb-1 d-inline-block">
                                                <i class="fas {{ $iconoRRHH }} me-1"></i>{{ $textoRRHH }}
                                            </span>
                                            <div class="small mt-1">
                                                @if($rrhhPuedeActuar && !$rrhhNoAplica)
                                                    <i class="fas fa-user-shield me-1 text-muted"></i>
                                                    {{ $solicitud->rrhh?->nombre ?? '' }} {{ $solicitud->rrhh?->apellidoPat ?? '' }}
                                                    @if($solicitud->fecha_aprobacion_rrhh)
                                                        <br>
                                                        <span class="text-muted" style="font-size: 0.75rem;">
                                                            <i class="far fa-calendar-alt me-1"></i>
                                                            {{ \Carbon\Carbon::parse($solicitud->fecha_aprobacion_rrhh)->format('d/m/Y H:i') }}
                                                        </span>
                                                    @endif
                                                @elseif($rrhhNoAplica)
                                                    <span class="text-muted fst-italic" style="font-size: 0.8rem;">
                                                        No aplica por estado del jefe
                                                    </span>
                                                @else
                                                    <span class="text-muted fst-italic" style="font-size: 0.8rem;">
                                                        Esperando aprobación del jefe
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            @if($solicitud->observacion)
                                                <span class="text-muted small">{{ Str::limit($solicitud->observacion, 30) }}</span>
                                                <button type="button" class="btn btn-sm btn-link p-0 ms-1" data-bs-toggle="tooltip" title="{{ $solicitud->observacion }}">
                                                    <i class="fas fa-eye text-muted"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        {{-- ===== ACCIONES ===== --}}
                                        <td>
                                            @php
                                                $editableVac = empty($estadoJefe) || $estadoJefe === 'pendiente';
                                                $pdfVac = $estadoJefe === 'aprobado';
                                            @endphp

                                            @if($editableVac)
                                                <button class="btn btn-sm btn-info btn-editar me-1" data-id="{{ $solicitud->id }}" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger btn-eliminar me-1" data-id="{{ $solicitud->id }}" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <span class="text-muted me-1" style="font-size: 0.8rem;">No editable</span>
                                            @endif
                                            <a href="{{ route('vacacion.boleta', $solicitud->id) }}" class="btn btn-sm btn-danger" title="PDF" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox text-muted fs-1 mb-3 d-block"></i>
                                            No has realizado ninguna solicitud de vacación
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Tab Movimientos -->
                <div class="tab-pane fade" id="movements" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr><th>Fecha</th><th>Tipo</th><th>Cantidad</th><th>Saldo Anterior</th><th>Saldo Posterior</th><th>Descripción</th></tr>
                            </thead>
                            <tbody>
                                @forelse($movimientos as $mov)
                                @php
                                    $tipos = ['credito'=>['class'=>'success','icon'=>'fa-plus-circle'],'debito'=>['class'=>'danger','icon'=>'fa-minus-circle'],'arrastre'=>['class'=>'info','icon'=>'fa-arrow-right'],'vencimiento'=>['class'=>'warning','icon'=>'fa-exclamation-circle']];
                                    $t = $tipos[$mov->tipo] ?? ['class'=>'secondary','icon'=>'fa-circle'];
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y H:i') }}</td>
                                    <td><span class="badge bg-{{ $t['class'] }} py-2 px-3"><i class="fas {{ $t['icon'] }} me-1"></i>{{ ucfirst($mov->tipo) }}</span></td>
                                    <td class="fw-bold">{{ number_format($mov->cantidad,1) }}</td>
                                    <td>{{ number_format($mov->saldo_anterior,1) }}</td>
                                    <td>{{ number_format($mov->saldo_posterior,1) }}</td>
                                    <td>{{ $mov->descripcion ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4"><i class="fas fa-exchange-alt text-muted fs-1 mb-3 d-block"></i><p class="text-muted mb-0">No hay movimientos registrados</p></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Tab Períodos Activos -->
                <div class="tab-pane fade" id="active" role="tabpanel">
                    @if($periodosActivos->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr><th>Período</th><th>Habilitado</th><th>Asignados</th><th>Usados</th><th>Arrastre</th><th>Disponible</th><th>Progreso</th></tr>
                            </thead>
                            <tbody>
                                @foreach($periodosActivos as $periodo)
                                @php $progreso = $periodo->dias_asignados > 0 ? ($periodo->dias_usados / $periodo->dias_asignados) * 100 : 0; @endphp
                                <tr>
                                    <td><span class="fw-bold">Año {{ $periodo->numero_periodo }}</span> @if($periodo->dias_vencidos > 0) <span class="badge bg-warning ms-2">Vence pronto</span> @endif</td>
                                    <td>{{ \Carbon\Carbon::parse($periodo->fecha_habilitacion)->format('d/m/Y') }}</td>
                                    <td>{{ number_format($periodo->dias_asignados,1) }}</td>
                                    <td>{{ number_format($periodo->dias_usados,1) }}</td>
                                    <td>{{ number_format($periodo->dias_arrastre,1) }}</td>
                                    <td><span class="badge bg-success fs-6 py-2 px-3">{{ number_format($periodo->saldo_disponible,1) }}</span></td>
                                    <td><div class="d-flex align-items-center"><div class="progress flex-grow-1" style="height:8px;"><div class="progress-bar bg-info" style="width:{{ $progreso }}%"></div></div><span class="ms-2 small">{{ number_format($progreso,1) }}%</span></div></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4"><i class="fas fa-calendar-times text-muted fs-1 mb-3 d-block"></i><p class="text-muted">No tienes períodos activos con saldo disponible.</p></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de consejos -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0"><h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Resumen de Días</h6></div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3"><div class="border-end"><h5 class="text-primary mb-0">{{ number_format($resumen['total_asignado'],1) }}</h5><small class="text-muted">Asignados</small></div></div>
                        <div class="col-3"><div class="border-end"><h5 class="text-info mb-0">{{ number_format($resumen['total_usado'],1) }}</h5><small class="text-muted">Usados</small></div></div>
                        <div class="col-3"><div class="border-end"><h5 class="text-warning mb-0">{{ number_format($resumen['total_vencido'],1) }}</h5><small class="text-muted">Vencidos</small></div></div>
                        <div class="col-3"><h5 class="text-success mb-0">{{ number_format($resumen['saldo_actual'],1) }}</h5><small class="text-muted">Disponible</small></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0"><h6 class="mb-0"><i class="fas fa-lightbulb me-2 text-warning"></i>Consejos</h6></div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Planifica tus vacaciones con anticipación</li>
                        <li class="mb-2"><i class="fas fa-calendar text-primary me-2"></i>Los días disponibles vencen al final del período</li>
                        <li><i class="fas fa-phone text-info me-2"></i>Contacta a RRHH si tienes dudas sobre tus días</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@section('modales')

<style>
    /* =========================================================
       MODALES DE VACACIONES - DISEÑO MINIMALISTA
       ========================================================= */

    .modal-vacaciones-min {
        max-width: 880px;
        width: 95%;
    }

    .modal-vacaciones-min .modal-content {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0, 0, 0, .16);
    }

    /* =========================================================
       HEADER
       ========================================================= */

    .modal-vacaciones-min .modal-header {
        position: relative;
        min-height: 78px;
        padding: 17px 22px;
        border: none;
        overflow: hidden;
        background: #4DA3FF;
    }

    .modal-vacaciones-min .header-bg {
        position: absolute;
        inset: 0;
        background: url('{{ asset('images/tejido-horizontal.jpg') }}')
                    center center / cover no-repeat;
        opacity: .28;
    }

    .modal-vacaciones-min .header-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            90deg,
            rgba(77, 163, 255, .95),
            rgba(77, 163, 255, .72)
        );
    }

    .modal-vacaciones-min .header-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
    }

    .modal-vacaciones-min .header-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        background: rgba(255,255,255,.18);
        color: #fff;
    }

    .modal-vacaciones-min .modal-title {
        color: #fff;
        font-size: 17px;
        font-weight: 600;
        margin: 0;
    }

    .modal-vacaciones-min .modal-subtitle {
        color: rgba(255,255,255,.85);
        font-size: 11px;
        margin-top: 2px;
    }

    .modal-vacaciones-min .btn-close {
        position: relative;
        z-index: 3;
        filter: brightness(0) invert(1);
        opacity: .9;
    }


    /* =========================================================
       BODY
       ========================================================= */

    .modal-vacaciones-min .modal-body {
        padding: 22px;
        background: #fff;
    }

    .modal-vacaciones-min .form-section {
        margin-bottom: 20px;
    }

    .modal-vacaciones-min .section-title {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 12px;
        color: #343a40;
        font-size: 13px;
        font-weight: 600;
    }

    .modal-vacaciones-min .section-title i {
        color: #4DA3FF;
        font-size: 12px;
    }


    /* =========================================================
       LABELS
       ========================================================= */

    .modal-vacaciones-min .form-label {
        color: #6c757d;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 5px;
    }


    /* =========================================================
       INPUTS
       ========================================================= */

    .modal-vacaciones-min .form-control,
    .modal-vacaciones-min .form-select {
        border-color: #dee2e6;
        border-radius: 8px;
        font-size: 13px;
        box-shadow: none;
    }

    .modal-vacaciones-min .form-control {
        min-height: 40px;
    }

    .modal-vacaciones-min .form-control:focus,
    .modal-vacaciones-min .form-select:focus {
        border-color: #4DA3FF;
        box-shadow: 0 0 0 3px rgba(77,163,255,.10);
    }

    .modal-vacaciones-min .form-control:disabled,
    .modal-vacaciones-min .form-control[readonly],
    .modal-vacaciones-min .form-select:disabled {
        background-color: #f8f9fa;
    }


    /* =========================================================
       FORM FLOATING
       ========================================================= */

    .modal-vacaciones-min .form-floating {
        position: relative;
    }

    .modal-vacaciones-min .form-floating > .form-control,
    .modal-vacaciones-min .form-floating > .form-select {
        height: 52px;
        min-height: 52px;
        padding: 1.25rem .85rem .35rem;
    }

    .modal-vacaciones-min .form-floating > label {
        padding: .55rem .85rem;
        font-size: 11px;
        color: #6c757d;
    }

    .modal-vacaciones-min .form-floating > .form-control:focus ~ label,
    .modal-vacaciones-min .form-floating > .form-control:not(:placeholder-shown) ~ label,
    .modal-vacaciones-min .form-floating > .form-select ~ label {
        color: #6c757d;
    }


    /* =========================================================
       DISPONIBILIDAD
       ========================================================= */

    .saldo-box {
        height: 52px;
        border: 1px solid #d9efff;
        background: #f4faff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 12px;
    }

    .saldo-box .saldo-label {
        font-size: 10px;
        color: #6c757d;
    }

    .saldo-box .saldo-value {
        font-size: 16px;
        font-weight: 700;
        color: #198754;
    }


    /* =========================================================
       FECHAS
       ========================================================= */

    .fecha-input {
        font-size: 14px !important;
        font-weight: 600;
    }

    .fecha-inicio {
        color: #198754;
    }

    .fecha-retorno {
        color: #dc3545;
    }

    .total-dias {
        text-align: center;
        font-size: 16px !important;
        font-weight: 700;
        color: #198754 !important;
        background: #f4faff !important;
    }


    /* =========================================================
       MEDIO DÍA
       ========================================================= */

    .medio-dia {
        height: 52px;
        border: 1px solid #dee2e6;
        background: #fafafa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 12px;
    }

    .medio-dia-text {
        font-size: 11px;
        color: #6c757d;
    }

    .medio-dia .form-switch {
        margin: 0;
    }

    .medio-dia .form-check-input {
        cursor: pointer;
    }


    /* =========================================================
       SUPERIOR
       ========================================================= */

    .busqueda-superior .input-group {
        border-radius: 8px;
        overflow: hidden;
    }

    .busqueda-superior .input-group .form-control {
        border-right: none;
    }

    .busqueda-superior .btn {
        width: 45px;
        border-radius: 0;
    }

    .superior-selected {
        background: #f8f9fa !important;
    }


    /* =========================================================
       TABLA SUPERIOR
       ========================================================= */

    .tabla-superior {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
        max-height: 145px;
        overflow-y: auto;
        margin-top: 9px;
    }

    .tabla-superior table {
        margin-bottom: 0;
        font-size: 12px;
    }

    .tabla-superior thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #f8f9fa;
        color: #6c757d;
        font-size: 10px;
        font-weight: 600;
        border-bottom: 1px solid #e9ecef;
    }

    .tabla-superior tbody td {
        padding: 7px 10px;
    }


    /* =========================================================
       FOOTER
       ========================================================= */

    .modal-vacaciones-min .modal-footer {
        background: #fafafa;
        border-top: 1px solid #e9ecef;
        padding: 12px 20px;
    }

    .modal-vacaciones-min .modal-footer .btn {
        border-radius: 7px;
        font-size: 12px;
        padding: 7px 15px;
    }

    .btn-guardar-vacacion {
        background: #4DA3FF;
        border-color: #4DA3FF;
        color: #fff;
    }

    .btn-guardar-vacacion:hover {
        background: #318fe8;
        border-color: #318fe8;
        color: #fff;
    }


    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 767px) {

        .modal-vacaciones-min {
            width: 96%;
            max-width: 96%;
        }

        .modal-vacaciones-min .modal-body {
            padding: 16px;
        }

        .modal-vacaciones-min .modal-header {
            padding: 15px 17px;
        }

        .modal-vacaciones-min .modal-title {
            font-size: 15px;
        }

        .modal-vacaciones-min .row > div {
            margin-bottom: 5px;
        }
    }
</style>


{{-- ============================================================
     MODAL CREAR
     ============================================================ --}}
<div class="modal fade"
     id="modalCrearVacacion"
     tabindex="-1"
     aria-labelledby="modalCrearLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-vacaciones-min">

        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">

                <div class="header-bg"></div>
                <div class="header-overlay"></div>

                <div class="header-content">

                    <div class="header-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>

                    <div>
                        <h5 class="modal-title"
                            id="modalCrearLabel">
                            Nueva solicitud de vacación
                        </h5>

                        <div class="modal-subtitle">
                            Complete los datos de su solicitud
                        </div>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                </button>

            </div>


            {{-- BODY --}}
            <div class="modal-body">

                <div id="formCrear">

                    <input type="hidden"
                           id="idpersona"
                           value="{{ auth()->user()->persona->id ?? '' }}">


                    {{-- =====================================================
                         INFORMACIÓN
                         ===================================================== --}}
                    <div class="form-section">

                        <div class="section-title">
                            <i class="fas fa-user"></i>
                            Información
                        </div>

                        <div class="row g-2">

                            {{-- Servidor --}}
                            <div class="col-md-5">

                                <div class="form-floating">

                                    <input type="text"
                                           class="form-control"
                                           disabled
                                           placeholder=" "
                                           value="{{ auth()->user()->persona->nombre ?? '' }}
                                                  {{ auth()->user()->persona->apellidoPat ?? '' }}
                                                  {{ auth()->user()->persona->apellidoMat ?? '' }}">

                                    <label>
                                        Servidor público
                                    </label>

                                </div>

                            </div>


                            {{-- Tipo --}}
                            <div class="col-md-2">

                                <div class="form-floating">

                                    <select class="form-select tipoSal"
                                            id="salida"
                                            disabled>

                                        @foreach ($tipoSal as $sal)

                                            @if ($sal->descripcion == 'VACACION')

                                                <option value="{{ $sal->id }}">
                                                    {{ $sal->descripcion }}
                                                </option>

                                            @endif

                                        @endforeach

                                    </select>

                                    <label>
                                        Tipo
                                    </label>

                                </div>

                            </div>


                            {{-- Fecha solicitud --}}
                            <div class="col-md-3">

                                <div class="form-floating">

                                    <input type="date"
                                           class="form-control fechasol"
                                           placeholder=" "
                                           readonly>

                                    <label>
                                        Fecha de solicitud
                                    </label>

                                </div>

                            </div>


                            {{-- Saldo --}}
                            <div class="col-md-2">

                                <div class="saldo-box">

                                    <span class="saldo-label">
                                        Disponible
                                    </span>

                                    <strong class="saldo-value">
                                        {{ number_format($resumen['saldo_actual'], 1) }}
                                    </strong>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
                         PERÍODO
                         ===================================================== --}}
                    <div class="form-section">

                        <div class="section-title">
                            <i class="fas fa-calendar-alt"></i>
                            Período de vacaciones
                        </div>

                        <div class="row g-2">

                            {{-- Inicio --}}
                            <div class="col-md-4">

                                <div class="form-floating">

                                    <input type="text"
                                           class="form-control fsalida fecha-input fecha-inicio"
                                           placeholder=" "
                                           required>

                                    <label>
                                        Fecha de inicio
                                    </label>

                                </div>

                            </div>


                            {{-- Retorno --}}
                            <div class="col-md-4">

                                <div class="form-floating">

                                    <input type="text"
                                           class="form-control fretorno fecha-input fecha-retorno"
                                           placeholder=" "
                                           required>

                                    <label>
                                        Fecha de retorno
                                    </label>

                                </div>

                            </div>


                            {{-- Días --}}
                            <div class="col-md-2">

                                <div class="form-floating">

                                    <input type="text"
                                           class="form-control totaldias total-dias"
                                           placeholder=" "
                                           readonly>

                                    <label>
                                        Días hábiles
                                    </label>

                                </div>

                            </div>


                            {{-- Medio día --}}
                            <div class="col-md-2">

                                <div class="medio-dia">

                                    <span class="medio-dia-text">
                                        Medio día
                                    </span>

                                    <div class="form-check form-switch">

                                        <input class="form-check-input mdia"
                                               type="checkbox"
                                               id="mdia">

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
                         MOTIVO
                         ===================================================== --}}
                    <div class="form-section">

                        <div class="section-title">
                            <i class="fas fa-comment-alt"></i>
                            Motivo
                        </div>

                        <div class="form-floating">

                            <input type="text"
                                   class="form-control observacion"
                                   placeholder=" "
                                   required>

                            <label>
                                Motivo o detalle de la solicitud
                            </label>

                        </div>

                    </div>


                    {{-- =====================================================
                         SUPERIOR
                         ===================================================== --}}
                    <div class="form-section mb-0">

                        <div class="section-title">
                            <i class="fas fa-user-tie"></i>
                            Inmediato superior
                        </div>

                        <div class="row g-2">

                            {{-- Buscar --}}
                            <div class="col-md-7 busqueda-superior">

                                <label class="form-label">
                                    Buscar por nombre o apellido
                                </label>

                                <div class="input-group">

                                    <input type="text"
                                           class="form-control dato"
                                           placeholder="Ej. Juan Pérez..."
                                           required>

                                    <button class="btn btn-outline-primary btnBuscarSup"
                                            type="button">

                                        <i class="fas fa-search"></i>

                                    </button>

                                </div>

                            </div>


                            {{-- Seleccionado --}}
                            <div class="col-md-5">

                                <label class="form-label">
                                    Superior seleccionado
                                </label>

                                <input type="hidden"
                                       class="idSup">

                                <input type="text"
                                       class="form-control nombreSup superior-selected"
                                       readonly
                                       placeholder="Ninguno seleccionado">

                            </div>

                        </div>


                        {{-- Tabla --}}
                        <div class="tabla-superior">

                            <table class="table table-hover align-middle">

                                <thead>

                                    <tr>

                                        <th>
                                            Nombre completo
                                        </th>

                                        <th class="text-center"
                                            style="width:70px;">
                                            Acción
                                        </th>

                                    </tr>

                                </thead>

                                <tbody class="tablaSup">

                                    <tr>

                                        <td colspan="2"
                                            class="text-center text-muted py-3">

                                            <i class="fas fa-search me-1"></i>
                                            Busque un superior para continuar

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}
            <div class="modal-footer">

                <button type="button"
                        class="btn btn-light border"
                        data-bs-dismiss="modal">

                    Cancelar

                </button>

                <button type="button"
                        class="btn btn-guardar-vacacion"
                        id="btnGuardarCrear">

                    <i class="fas fa-paper-plane me-1"></i>
                    Enviar solicitud

                </button>

            </div>

        </div>

    </div>

</div>



{{-- ============================================================
     MODAL EDITAR
     ============================================================ --}}
<div class="modal fade"
     id="modalEditarVacacion"
     tabindex="-1"
     aria-labelledby="modalEditarLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-vacaciones-min">

        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">

                <div class="header-bg"></div>
                <div class="header-overlay"></div>

                <div class="header-content">

                    <div class="header-icon">
                        <i class="fas fa-edit"></i>
                    </div>

                    <div>

                        <h5 class="modal-title"
                            id="modalEditarLabel">
                            Editar solicitud de vacación
                        </h5>

                        <div class="modal-subtitle">
                            Modifique los datos de su solicitud
                        </div>

                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                </button>

            </div>


            {{-- BODY --}}
            <div class="modal-body">

                <div id="formEditar">

                    <input type="hidden"
                           id="edit_solicitud_id">


                    {{-- =====================================================
                         INFORMACIÓN
                         ===================================================== --}}
                    <div class="form-section">

                        <div class="section-title">
                            <i class="fas fa-user"></i>
                            Información
                        </div>

                        <div class="row g-2">

                            {{-- Servidor --}}
                            <div class="col-md-5">

                                <div class="form-floating">

                                    <input type="text"
                                           class="form-control"
                                           id="edit_nomb"
                                           disabled
                                           placeholder=" "
                                           value="{{ auth()->user()->persona->nombre ?? '' }}
                                                  {{ auth()->user()->persona->apellidoPat ?? '' }}
                                                  {{ auth()->user()->persona->apellidoMat ?? '' }}">

                                    <label>
                                        Servidor público
                                    </label>

                                </div>

                            </div>


                            {{-- Tipo --}}
                            <div class="col-md-2">

                                <div class="form-floating">

                                    <select class="form-select tipoSal"
                                            id="edit_salida"
                                            disabled>

                                        @foreach ($tipoSal as $sal)

                                            @if ($sal->descripcion == 'VACACION')

                                                <option value="{{ $sal->id }}">
                                                    {{ $sal->descripcion }}
                                                </option>

                                            @endif

                                        @endforeach

                                    </select>

                                    <label>
                                        Tipo
                                    </label>

                                </div>

                            </div>


                            {{-- Fecha --}}
                            <div class="col-md-3">

                                <div class="form-floating">

                                    <input type="date"
                                           class="form-control fechasol"
                                           id="edit_fechasol"
                                           placeholder=" "
                                           readonly>

                                    <label>
                                        Fecha de solicitud
                                    </label>

                                </div>

                            </div>


                            {{-- Saldo --}}
                            <div class="col-md-2">

                                <div class="saldo-box">

                                    <span class="saldo-label">
                                        Disponible
                                    </span>

                                    <strong class="saldo-value diasDisponiblesSpan">
                                        0
                                    </strong>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
                         PERÍODO
                         ===================================================== --}}
                    <div class="form-section">

                        <div class="section-title">
                            <i class="fas fa-calendar-alt"></i>
                            Período de vacaciones
                        </div>

                        <div class="row g-2">

                            {{-- Inicio --}}
                            <div class="col-md-4">

                                <div class="form-floating">

                                    <input type="text"
                                           class="form-control fsalida fecha-input fecha-inicio"
                                           id="edit_fsalida"
                                           placeholder=" "
                                           required>

                                    <label>
                                        Fecha de inicio
                                    </label>

                                </div>

                            </div>


                            {{-- Retorno --}}
                            <div class="col-md-4">

                                <div class="form-floating">

                                    <input type="text"
                                           class="form-control fretorno fecha-input fecha-retorno"
                                           id="edit_fretorno"
                                           placeholder=" "
                                           required>

                                    <label>
                                        Fecha de retorno
                                    </label>

                                </div>

                            </div>


                            {{-- Días --}}
                            <div class="col-md-2">

                                <div class="form-floating">

                                    <input type="text"
                                           class="form-control totaldias total-dias"
                                           id="edit_totaldias"
                                           placeholder=" "
                                           readonly>

                                    <label>
                                        Días hábiles
                                    </label>

                                </div>

                            </div>


                            {{-- Medio día --}}
                            <div class="col-md-2">

                                <div class="medio-dia">

                                    <span class="medio-dia-text">
                                        Medio día
                                    </span>

                                    <div class="form-check form-switch">

                                        <input class="form-check-input mdia"
                                               type="checkbox"
                                               id="edit_mdia">

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
                         MOTIVO
                         ===================================================== --}}
                    <div class="form-section">

                        <div class="section-title">
                            <i class="fas fa-comment-alt"></i>
                            Motivo
                        </div>

                        <div class="form-floating">

                            <input type="text"
                                   class="form-control observacion"
                                   id="edit_observacion"
                                   placeholder=" "
                                   required>

                            <label>
                                Motivo o detalle de la solicitud
                            </label>

                        </div>

                    </div>


                    {{-- =====================================================
                         SUPERIOR
                         ===================================================== --}}
                    <div class="form-section mb-0">

                        <div class="section-title">
                            <i class="fas fa-user-tie"></i>
                            Inmediato superior
                        </div>

                        <div class="row g-2">

                            {{-- Buscar --}}
                            <div class="col-md-7 busqueda-superior">

                                <label class="form-label">
                                    Buscar por nombre o apellido
                                </label>

                                <div class="input-group">

                                    <input type="text"
                                           class="form-control dato"
                                           id="edit_dato"
                                           placeholder="Ej. Juan Pérez..."
                                           required>

                                    <button class="btn btn-outline-primary btnBuscarSup"
                                            type="button">

                                        <i class="fas fa-search"></i>

                                    </button>

                                </div>

                            </div>


                            {{-- Seleccionado --}}
                            <div class="col-md-5">

                                <label class="form-label">
                                    Superior seleccionado
                                </label>

                                <input type="hidden"
                                       class="idSup"
                                       id="edit_idSup">

                                <input type="text"
                                       class="form-control nombreSup superior-selected"
                                       id="edit_nombreSup"
                                       readonly
                                       placeholder="Ninguno seleccionado">

                            </div>

                        </div>


                        {{-- Tabla --}}
                        <div class="tabla-superior">

                            <table class="table table-hover align-middle">

                                <thead>

                                    <tr>

                                        <th>
                                            Nombre completo
                                        </th>

                                        <th class="text-center"
                                            style="width:70px;">
                                            Acción
                                        </th>

                                    </tr>

                                </thead>

                                <tbody class="tablaSup"
                                       id="edit_tablaSup">

                                    <tr>

                                        <td colspan="2"
                                            class="text-center text-muted py-3">

                                            <i class="fas fa-search me-1"></i>
                                            Busque un superior para continuar

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}
            <div class="modal-footer">

                <button type="button"
                        class="btn btn-light border"
                        data-bs-dismiss="modal">

                    Cancelar

                </button>

                <button type="button"
                        class="btn btn-guardar-vacacion"
                        id="btnGuardarEditar">

                    <i class="fas fa-save me-1"></i>
                    Guardar cambios

                </button>

            </div>

        </div>

    </div>

</div>

@endsection


@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF Token
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (tokenMeta) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.content;
    }

    // Datos
    const idPersona = document.getElementById('idpersona')?.value || '{{ auth()->user()->persona_id ?? 0 }}';
    const feriados = @json($feriado->pluck('fechaf')->map(fn($f) => \Carbon\Carbon::parse($f)->format('Y-m-d'))->toArray(), JSON_HEX_TAG);

    // Funciones auxiliares
    function parseFechaLocal(fechaStr) {
        const [a,m,d] = fechaStr.split('-').map(Number);
        return new Date(a, m-1, d);
    }

    function calcularDias(finicio, ffin, feriados, medioDia) {
        const start = parseFechaLocal(finicio);
        const end = parseFechaLocal(ffin);
        if (isNaN(start) || isNaN(end) || end < start) return 0;
        let total = 0;
        const temp = new Date(start);
        while (temp <= end) {
            const day = temp.getDay();
            const strFecha = temp.toISOString().split('T')[0];
            const esFeriado = feriados.includes(strFecha);
            if (day !== 0 && day !== 6 && !esFeriado) total++;
            temp.setDate(temp.getDate() + 1);
        }
        if (medioDia) total = Math.max(total - 0.5, 0);
        return total;
    }

    // Inicializar Flatpickr en un contenedor específico
    function initFlatpickr(container) {
        const fechasFeriado = feriados.map(f => {
            const [y,m,d] = f.split('-').map(Number);
            return new Date(y, m-1, d);
        });
        const deshabilitar = (date) => {
            const esFin = date.getDay() === 0 || date.getDay() === 6;
            const esFer = fechasFeriado.some(f => f.toDateString() === date.toDateString());
            return esFin || esFer;
        };

        const inputFsalida = container.querySelector('.fsalida');
        const inputFretorno = container.querySelector('.fretorno');
        const inputTotal = container.querySelector('.totaldias');
        const checkboxMedio = container.querySelector('.mdia');

        function recalc() {
            if (!inputFsalida || !inputFretorno || !inputTotal) return;
            // Flatpickr con altInput mantiene el valor real en Y-m-d en el input original
            const fs = inputFsalida.value;
            const fr = inputFretorno.value;
            const md = checkboxMedio ? checkboxMedio.checked : false;
            if (!fs || !fr) { inputTotal.value = ''; return; }
            inputTotal.value = calcularDias(fs, fr, feriados, md);
        }

        const configFlatpickr = {
            minDate: 'today',
            disable: [deshabilitar],
            locale: 'es',
            dateFormat: 'Y-m-d',     // formato interno para JS y servidor
            altInput: true,          // input visual alternativo
            altFormat: 'd/m/Y',      // <-- AQUÍ: usuario ve día/mes/año
            onChange: recalc
        };

        if (inputFsalida) {
            flatpickr(inputFsalida, configFlatpickr);
        }
        if (inputFretorno) {
            flatpickr(inputFretorno, configFlatpickr);
        }
        if (checkboxMedio) {
            checkboxMedio.addEventListener('change', recalc);
        }
        recalc();
    }

    // Cargar días disponibles en un contenedor
    function cargarDiasDisponibles(container) {
        const span = container.querySelector('.diasDisponiblesSpan');
        if (!span) return;
        axios.get('/vacacion/dias-disponibles', {
            params: { idpersona: idPersona }
        })
        .then(res => {
            span.textContent = res.data.dias;
        })
        .catch(err => {
            console.error('Error al cargar días disponibles:', err);
            span.textContent = '0';
        });
    }

    // Obtener datos del formulario (para crear o editar)
    function obtenerDatosFormulario(container) {
        const fsalida = container.querySelector('.fsalida').value;
        const fretorno = container.querySelector('.fretorno').value;
        const totaldias = container.querySelector('.totaldias').value;
        const idSup = container.querySelector('.idSup').value;
        const observacion = container.querySelector('.observacion').value;
        const tipoSal = container.querySelector('.tipoSal')?.value || '';
        const fechasol = container.querySelector('.fechasol').value;

        if (!fsalida || !fretorno || !totaldias) {
            Swal.fire('Advertencia', 'Complete las fechas y verifique que los días sean válidos.', 'warning');
            return null;
        }
        if (!idSup) {
            Swal.fire('Advertencia', 'Debe seleccionar un inmediato superior.', 'warning');
            return null;
        }
        return {
            tipoSal, fechasol, fsalida, fretorno, totaldias, idSup, observacion
        };
    }

    function manejarError(error) {
        let msg = 'Ocurrió un error.';
        if (error.response) {
            if (error.response.status === 422) {
                const errs = error.response.data.errors;
                if (errs) msg = Object.values(errs).flat().join('\n');
                else msg = error.response.data.message || msg;
            } else {
                msg = error.response.data.message || msg;
            }
        }
        Swal.fire('Error', msg, 'error');
    }

    // --- Eventos para modales ---
    const modalCrear = document.getElementById('modalCrearVacacion');
    const modalEditar = document.getElementById('modalEditarVacacion');

    modalCrear.addEventListener('shown.bs.modal', function() {
        const container = this.querySelector('.modal-body');
        initFlatpickr(container);
        cargarDiasDisponibles(container);
        const fechasol = container.querySelector('.fechasol');
        if (fechasol) fechasol.value = new Date().toISOString().split('T')[0];
        const idSup = container.querySelector('.idSup');
        const nombreSup = container.querySelector('.nombreSup');
        if (idSup) idSup.value = '';
        if (nombreSup) nombreSup.value = '';
        const tablaSup = container.querySelector('.tablaSup');
        if (tablaSup) tablaSup.innerHTML = '';
        const observacion = container.querySelector('.observacion');
        if (observacion) observacion.value = '';
    });

    modalEditar.addEventListener('shown.bs.modal', function() {
        const container = this.querySelector('.modal-body');
        initFlatpickr(container);
    });

    // --- Búsqueda de superior (delegación) ---
    document.addEventListener('click', function(e) {
        const btnBuscar = e.target.closest('.btnBuscarSup');
        if (!btnBuscar) return;
        const container = btnBuscar.closest('.modal-body') || document;
        const inputBuscar = container.querySelector('.dato');
        const tablaSup = container.querySelector('.tablaSup');
        const buscar = inputBuscar.value.trim();
        if (!buscar) {
            Swal.fire('Advertencia', 'Debe ingresar un nombre o apellido antes de buscar', 'warning');
            return;
        }
        axios.get('/vacacion/buscar-superior', { params: { buscar: buscar } })
            .then(response => {
                const personas = response.data;
                tablaSup.innerHTML = '';
                if (personas.length === 0) {
                    tablaSup.innerHTML = '<tr><td colspan="2" class="text-center">No se encontraron personas</td></tr>';
                    return;
                }
                personas.forEach(p => {
                    const nombreCompleto = `${p.nombre} ${p.apellidoPat} ${p.apellidoMat}`;
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${nombreCompleto}</td>
                        <td>
                            <button class="btn btn-primary btn-sm asignar-sup"
                                    data-id="${p.id}"
                                    data-nombre="${nombreCompleto}">
                                <i class="fa fa-check"></i>
                            </button>
                        </td>
                    `;
                    tablaSup.appendChild(tr);
                });
                tablaSup.querySelectorAll('.asignar-sup').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.dataset.id;
                        const nombre = this.dataset.nombre;
                        const idSup = container.querySelector('.idSup');
                        const nombreSup = container.querySelector('.nombreSup');
                        if (idSup) idSup.value = id;
                        if (nombreSup) nombreSup.value = nombre;
                        tablaSup.innerHTML = '';
                    });
                });
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
                Swal.fire('Error', 'Ocurrió un problema al buscar', 'error');
            });
    });

    // Enter para búsqueda
    document.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            const input = e.target.closest('.dato');
            if (input) {
                const container = input.closest('.modal-body') || document;
                const btn = container.querySelector('.btnBuscarSup');
                if (btn) btn.click();
            }
        }
    });

    // --- CREAR ---
    document.getElementById('btnGuardarCrear').addEventListener('click', function() {
        const container = document.querySelector('#modalCrearVacacion .modal-body');
        const data = obtenerDatosFormulario(container);
        if (!data) return;
        data.persona_id = idPersona;

        Swal.fire({ title: 'Registrando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        axios.post('/vacacion/registrar-vacacion', data)
            .then(res => {
                Swal.fire('Éxito', res.data.message, 'success').then(() => location.reload());
            })
            .catch(err => {
                if (err.response && err.response.status === 422) {
                    const errors = err.response.data.errors;
                    let mensaje = '';
                    Object.keys(errors).forEach(campo => {
                        mensaje += `${campo}: ${errors[campo].join(', ')}\n`;
                    });
                    Swal.fire('Error de validación', mensaje, 'error');
                } else {
                    manejarError(err);
                }
            });
    });

    // --- EDITAR (cargar datos) ---
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('edit_solicitud_id').value = id;
            const modal = document.getElementById('modalEditarVacacion');
            const container = modal.querySelector('.modal-body');

            axios.get(`/vacacion/editar-vacacion/${id}/salida`)
                .then(response => {
                    const sol = response.data;
                    container.querySelector('.fechasol').value = sol.fechasol;
                    container.querySelector('.fsalida').value = sol.fechasal;
                    container.querySelector('.fretorno').value = sol.fecharet;
                    container.querySelector('.totaldias').value = sol.cantidad;
                    container.querySelector('.observacion').value = sol.observacion || '';
                    container.querySelector('.idSup').value = sol.superior_id || '';
                    container.querySelector('.nombreSup').value = sol.superior_nombre || '';
                    cargarDiasDisponibles(container);
                    const fs = container.querySelector('.fsalida');
                    const fr = container.querySelector('.fretorno');
                    const td = container.querySelector('.totaldias');
                    const md = container.querySelector('.mdia');
                    if (fs && fr && td) {
                        td.value = calcularDias(fs.value, fr.value, feriados, md ? md.checked : false);
                    }
                    const modalInstance = new bootstrap.Modal(modal);
                    modalInstance.show();
                })
                .catch(err => {
                    console.error('Error al cargar solicitud:', err);
                    Swal.fire('Error', 'No se pudo cargar la solicitud para editar', 'error');
                });
        });
    });

    // --- ACTUALIZAR ---
    document.getElementById('btnGuardarEditar').addEventListener('click', function() {
        const container = document.querySelector('#modalEditarVacacion .modal-body');
        const id = document.getElementById('edit_solicitud_id').value;
        const data = obtenerDatosFormulario(container);
        if (!data) return;

        Swal.fire({ title: 'Actualizando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        axios.put(`/vacacion/update-vacacion/${id}`, data)
            .then(res => {
                Swal.fire('Éxito', res.data.message, 'success').then(() => location.reload());
            })
            .catch(err => manejarError(err));
    });

    // --- ELIMINAR ---
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            Swal.fire({
                title: '¿Eliminar solicitud?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    axios.delete(`/vacacion/destroy-vacacion/${id}/salida`)
                        .then(res => {
                            Swal.fire('Eliminado', res.data.message, 'success').then(() => location.reload());
                        })
                        .catch(err => manejarError(err));
                }
            });
        });
    });

    console.log('✅ Script de vacaciones cargado correctamente');
});
</script>
@endpush