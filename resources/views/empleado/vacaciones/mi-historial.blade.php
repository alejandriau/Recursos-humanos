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
    <!-- ============================================================ -->
    <!-- MODAL CREAR -->
    <!-- ============================================================ -->
    <div class="modal fade" id="modalCrearVacacion" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-vacaciones">
            <div class="modal-content" style="border-radius: 1rem; border: none; overflow: hidden;">
                
                <div class="modal-header text-white position-relative overflow-hidden" style="border: none;">

                    <!-- Imagen de fondo suave -->
                    <div class="position-absolute top-0 start-0 w-100 h-100"
                        style="
                            background: url('{{ asset('images/tejido-horizontal.jpg') }}') center/cover no-repeat;
                            opacity: 0.45;
                        ">
                    </div>

                    <!-- Overlay suave -->
                    <div class="position-absolute top-0 start-0 w-100 h-100"
                        style="
                            background: linear-gradient(
                                135deg,
                                rgba(159, 199, 121, 0.8) 0%,
                                rgba(47, 136, 20, 0.8) 100%
                            );
                        ">
                    </div>

                    <!-- Contenido -->
                    <div class="d-flex align-items-center position-relative z-1">

                        <div class="rounded-circle bg-white bg-opacity-25
                                    d-flex align-items-center justify-content-center me-3"
                            style="width: 42px; height: 42px;">
                            <i class="fas fa-umbrella-beach fa-lg" style="color: #0B5D1E;"></i>
                        </div>

                        <div>
                            <h5 class="modal-title mb-0 fw-bold"
                                id="modalCrearLabel"
                                style="color: #0B5D1E;">
                                Nueva Solicitud de Vacación
                            </h5>

                            <small style="color: #0B5D1E; font-weight: 600;">
                                Complete los datos de su período de descanso
                            </small>
                        </div>

                    </div>

                    <button type="button"
                            class="btn-close btn-close-white position-relative z-1"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar">
                    </button>

                </div>

                <div class="modal-body p-0">
                    <div id="formCrear" class="p-4">
                        <input type="hidden" id="idpersona" value="{{ auth()->user()->persona->id ?? '' }}">
                        
                        <!-- Fila superior compacta -->
                        <div class="row g-2 mb-4">
                            <div class="col-md-3">
                                <div class="alert alert-success py-2 mb-0 d-flex align-items-center h-100">
                                    <i class="fas fa-wallet me-2 fs-5"></i>
                                    <div>
                                        <div class="small text-success" style="font-size: 0.7rem;">Días Disponibles</div>
                                        <div class="fw-bold">{{ number_format($resumen['saldo_actual'], 1) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-floating">
                                    <input type="text" class="form-control" disabled
                                           value="{{ auth()->user()->persona->nombre ?? '' }} {{ auth()->user()->persona->apellidoPat ?? '' }} {{ auth()->user()->persona->apellidoMat ?? '' }}">
                                    <label>Servidor Público</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-floating">
                                    <select class="form-select tipoSal" id="salida" disabled>
                                        @foreach ($tipoSal as $sal)
                                            @if ($sal->descripcion == 'VACACION')
                                                <option value="{{ $sal->id }}">{{ $sal->descripcion }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label>Tipo de Salida</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-floating">
                                    <input type="date" class="form-control fechasol" required readonly>
                                    <label>Fecha de Solicitud</label>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de fechas -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                <h6 class="fw-bold text-dark mb-0">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i>Período de Vacaciones
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark mb-2">
                                            <i class="fas fa-plane-departure text-success me-2"></i>Fecha de Inicio
                                        </label>
                                        <div class="form-floating">
                                            <input type="text" class="form-control fsalida fs-5 fw-bold text-success border-success border-opacity-25" required placeholder="Inicio" style="border-radius: 10px;">
                                            <label class="text-muted">Seleccione la fecha de inicio</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-arrow-right text-muted"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark mb-2">
                                            <i class="fas fa-plane-arrival text-danger me-2"></i>Fecha de Retorno
                                        </label>
                                        <div class="form-floating">
                                            <input type="text" class="form-control fretorno fs-5 fw-bold text-danger border-danger border-opacity-25" required placeholder="Fin" style="border-radius: 10px;">
                                            <label class="text-muted">Seleccione la fecha de retorno</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-dark mb-2">
                                            <i class="fas fa-calculator text-info me-2"></i>Total de Días
                                        </label>
                                        <div class="position-relative">
                                            <div class="form-floating">
                                                <input type="text" class="form-control totaldias fs-4 fw-bold text-info border-info border-opacity-25 text-center" readonly style="border-radius: 10px; background: #e3f2fd;">
                                                <label class="text-muted">Días hábiles calculados</label>
                                            </div>
                                            <div class="position-absolute top-50 end-0 translate-middle-y me-2">
                                                <span class="badge bg-info">auto</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 p-3 rounded-3 row" >
                                    <div class="col-md-4 d-flex align-items-center justify-content-between" style="background: #fff8e1; border: 1px dashed #ffc107;">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                                <i class="fas fa-adjust text-warning"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">¿Solicita medio día?</div>
                                                <div class="text-muted">Marque esta opción si solo necesita medio día</div>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch" style="transform: scale(1.3);">
                                            <input class="form-check-input mdia" type="checkbox" id="mdia">
                                            <label class="form-check-label visually-hidden" for="mdia">Medio día</label>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold text-dark mb-2">
                                            <i class="fas fa-comment-alt text-warning me-2"></i>Motivo
                                        </label>
                                        <div class="form-floating">
                                            <input type="text" class="form-control observacion border-warning" placeholder="Observaciones" required style="border-radius: 10px;">
                                            <label class="text-muted">Ingrese el motivo o detalle adicional de la solicitud</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>


                        <!-- Sección de Superior -->
                        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff, #f8f9fa);">
                            <div class="card-header bg-transparent border-bottom-0 pt-3">
                                <h6 class="fw-bold text-dark mb-0">
                                    <i class="fas fa-user-tie text-secondary me-2"></i>Aprobación del Inmediato Superior
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <label class="form-label fw-bold text-dark mb-2">Buscar inmediato superior por nombre o apellido</label>
                                        <div class="input-group input-group-lg" style="border-radius: 10px; overflow: hidden;">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control dato border-start-0" placeholder="Ejemplo: Juan Pérez..." required>
                                            <button class="btn btn-success btnBuscarSup px-4 fw-bold" type="button">
                                                <i class="fa fa-search me-1"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold text-dark mb-2">Inmediato superior seleccionado</label>
                                        <input type="hidden" class="idSup">
                                        <div class="form-floating">
                                            <input type="text" class="form-control nombreSup bg-light" readonly placeholder="Superior" style="border-radius: 10px;">
                                            <label class="text-muted"><i class="fas fa-user-check text-success me-1"></i>Nombre del superior</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="table-responsive rounded-3 border" style="max-height: 200px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th class="ps-3 fw-bold"><i class="fas fa-user me-1 text-muted"></i>Nombre Completo</th>
                                                    <th class="text-center fw-bold" style="width: 100px;">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody class="tablaSup">
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted py-4">
                                                        <i class="fas fa-search text-muted mb-2 d-block" style="font-size: 1.5rem;"></i>
                                                        Realice una búsqueda para ver resultados
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top" style="padding: 1.2rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary px-4 fw-bold" id="btnGuardarCrear" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                    </button>
                </div>
            </div>
        </div>
    </div>

<!-- ============================================================ -->
<!-- MODAL EDITAR (ahora idéntico en diseño al de crear) -->
<!-- ============================================================ -->
<div class="modal fade" id="modalEditarVacacion" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-vacaciones">
        <div class="modal-content" style="border-radius: 1rem; border: none; overflow: hidden;">
            
            <!-- Header con mismo fondo/overlay que el crear -->
            <div class="modal-header text-white position-relative overflow-hidden" style="border: none;">

                <!-- Imagen de fondo suave -->
                <div class="position-absolute top-0 start-0 w-100 h-100"
                    style="
                        background: url('{{ asset('images/tejido-horizontal.jpg') }}') center/cover no-repeat;
                        opacity: 0.45;
                    ">
                </div>

                <!-- Overlay con el mismo gradiente azul/violeta que el crear -->
                <div class="position-absolute top-0 start-0 w-100 h-100"
                    style="
                        background: linear-gradient(
                            135deg,
                            rgba(245, 225, 138, 0.8) 0%,
                            rgba(172, 160, 5, 0.8) 100%
                        );
                    ">
                </div>

                <!-- Contenido (mismos estilos de texto que el crear) -->
                <div class="d-flex align-items-center position-relative z-1">

                    <div class="rounded-circle bg-white bg-opacity-25
                                d-flex align-items-center justify-content-center me-3"
                        style="width: 42px; height: 42px;">
                        <i class="fas fa-edit fa-lg" style="color: #0B5D1E;"></i>
                    </div>

                    <div>
                        <h5 class="modal-title mb-0 fw-bold"
                            id="modalEditarLabel"
                            style="color: #0B5D1E;">
                            Editar Solicitud de Vacación
                        </h5>

                        <small style="color: #0B5D1E; font-weight: 600;">
                            Modifique los datos de su solicitud
                        </small>
                    </div>

                </div>

                <button type="button"
                        class="btn-close btn-close-white position-relative z-1"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                </button>

            </div>

            <div class="modal-body p-0">
                <div id="formEditar" class="p-4">
                    <input type="hidden" id="edit_solicitud_id" value="">
                    
                    <!-- Fila superior compacta (misma que el crear) -->
                    <div class="row g-2 mb-4">
                        <div class="col-md-3">
                            <!-- Cambiado a alert-success para que sea igual -->
                            <div class="alert alert-success py-2 mb-0 d-flex align-items-center h-100">
                                <i class="fas fa-wallet me-2 fs-5"></i>
                                <div>
                                    <div class="small text-success" style="font-size: 0.7rem;">Días Disponibles</div>
                                    <div class="fw-bold diasDisponiblesSpan">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="edit_nomb" disabled
                                       value="{{ auth()->user()->persona->nombre ?? '' }} {{ auth()->user()->persona->apellidoPat ?? '' }} {{ auth()->user()->persona->apellidoMat ?? '' }}">
                                <label>Servidor Público</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <select class="form-select tipoSal" id="edit_salida" disabled>
                                    @foreach ($tipoSal as $sal)
                                        @if ($sal->descripcion == 'VACACION')
                                            <option value="{{ $sal->id }}">{{ $sal->descripcion }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <label>Tipo de Salida</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="date" class="form-control fechasol" id="edit_fechasol" required readonly>
                                <label>Fecha de Solicitud</label>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de fechas (igual que el crear) -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Período de Vacaciones
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark mb-2">
                                        <i class="fas fa-plane-departure text-success me-2"></i>Fecha de Inicio
                                    </label>
                                    <div class="form-floating">
                                        <input type="text" class="form-control fsalida fs-5 fw-bold text-success border-success border-opacity-25" id="edit_fsalida" required placeholder="Inicio" style="border-radius: 10px;">
                                        <label class="text-muted">Seleccione la fecha de inicio</label>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-center justify-content-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-arrow-right text-muted"></i>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark mb-2">
                                        <i class="fas fa-plane-arrival text-danger me-2"></i>Fecha de Retorno
                                    </label>
                                    <div class="form-floating">
                                        <input type="text" class="form-control fretorno fs-5 fw-bold text-danger border-danger border-opacity-25" id="edit_fretorno" required placeholder="Fin" style="border-radius: 10px;">
                                        <label class="text-muted">Seleccione la fecha de retorno</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-dark mb-2">
                                        <i class="fas fa-calculator text-info me-2"></i>Total de Días
                                    </label>
                                    <div class="position-relative">
                                        <div class="form-floating">
                                            <input type="text" class="form-control totaldias fs-4 fw-bold text-info border-info border-opacity-25 text-center" id="edit_totaldias" readonly style="border-radius: 10px; background: #e3f2fd;">
                                            <label class="text-muted">Días hábiles calculados</label>
                                        </div>
                                        <div class="position-absolute top-50 end-0 translate-middle-y me-2">
                                            <span class="badge bg-info">auto</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aquí está el bloque combinado de medio día + observaciones (igual que el crear) -->
                            <div class="mt-3 p-3 rounded-3 row" >
                                <div class="col-md-4 d-flex align-items-center justify-content-between" style="background: #fff8e1; border: 1px dashed #ffc107;">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                            <i class="fas fa-adjust text-warning"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">¿Solicita medio día?</div>
                                            <div class="text-muted">Marque esta opción si solo necesita medio día</div>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch" style="transform: scale(1.3);">
                                        <input class="form-check-input mdia" type="checkbox" id="edit_mdia">
                                        <label class="form-check-label visually-hidden" for="edit_mdia">Medio día</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-dark mb-2">
                                        <i class="fas fa-comment-alt text-warning me-2"></i>Motivo
                                    </label>
                                    <div class="form-floating">
                                        <input type="text" class="form-control observacion border-warning" id="edit_observacion" placeholder="Observaciones" required style="border-radius: 10px;">
                                        <label class="text-muted">Ingrese el motivo o detalle adicional de la solicitud</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin del bloque combinado -->
                        </div>
                    </div>

                    <!-- Sección de Superior (igual que el crear) -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff, #f8f9fa);">
                        <div class="card-header bg-transparent border-bottom-0 pt-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="fas fa-user-tie text-secondary me-2"></i>Aprobación del Inmediato Superior
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-bold text-dark mb-2">Buscar inmediato superior por nombre o apellido</label>
                                    <div class="input-group input-group-lg" style="border-radius: 10px; overflow: hidden;">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control dato border-start-0" id="edit_dato" placeholder="Ejemplo: Juan Pérez..." required>
                                        <button class="btn btn-success btnBuscarSup px-4 fw-bold" type="button">
                                            <i class="fa fa-search me-1"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-dark mb-2">Inmediato superior seleccionado</label>
                                    <input type="hidden" class="idSup" id="edit_idSup">
                                    <div class="form-floating">
                                        <input type="text" class="form-control nombreSup bg-light" id="edit_nombreSup" readonly placeholder="Superior" style="border-radius: 10px;">
                                        <label class="text-muted"><i class="fas fa-user-check text-success me-1"></i>Nombre del superior</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="table-responsive rounded-3 border" style="max-height: 200px; overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th class="ps-3 fw-bold"><i class="fas fa-user me-1 text-muted"></i>Nombre Completo</th>
                                                <th class="text-center fw-bold" style="width: 100px;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tablaSup" id="edit_tablaSup">
                                            <tr>
                                                <td colspan="2" class="text-center text-muted py-4">
                                                    <i class="fas fa-search text-muted mb-2 d-block" style="font-size: 1.5rem;"></i>
                                                    Realice una búsqueda para ver resultados
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light border-top" style="padding: 1.2rem 1.5rem;">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <!-- Botón con el mismo gradiente que el crear -->
                <button type="button" class="btn btn-primary px-4 fw-bold" id="btnGuardarEditar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <i class="fas fa-save me-2"></i>Actualizar Solicitud
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