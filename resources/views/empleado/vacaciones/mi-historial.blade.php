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
</style>
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-umbrella-beach text-primary me-2"></i>Mi Historial de Vacaciones
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                            <li class="breadcrumb-item active">Mi Historial</li>
                        </ol>
                    </nav>
                </div>
                {{-- Botón que abre el modal --}}
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearVacacion">
                    <i class="fas fa-plus me-2"></i>Nueva Solicitud
                </button>
            </div>
        </div>
    </div>

    <!-- Tarjetas de resumen (igual) -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Saldo Disponible</h6>
                            <h3 class="mb-0 text-success">{{ number_format($resumen['saldo_actual'], 1) }}</h3>
                            <small class="text-muted">días disponibles</small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-calendar-check text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height:5px;">
                        @php
                            $porcentaje = $resumen['total_asignado'] > 0 ? ($resumen['saldo_actual'] / $resumen['total_asignado']) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" style="width:{{ $porcentaje }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Asignado</h6>
                            <h3 class="mb-0 text-primary">{{ number_format($resumen['total_asignado'], 1) }}</h3>
                            <small class="text-muted">días totales</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-arrow-up text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Días Usados</h6>
                            <h3 class="mb-0 text-info">{{ number_format($resumen['total_usado'], 1) }}</h3>
                            <small class="text-muted">días utilizados</small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-clock text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-hover h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Días Vencidos</h6>
                            <h3 class="mb-0 text-warning">{{ number_format($resumen['total_vencido'], 1) }}</h3>
                            <small class="text-muted">días por vencer</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
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
                <div class="tab-pane fade show active" id="history" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha Solicitud</th><th>Desde</th><th>Hasta</th><th>Días</th>
                                    <th>Estado</th><th>Responsable</th><th>Observaciones</th><th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($solicitudes as $solicitud)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($solicitud->fechasol)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($solicitud->fechasal)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($solicitud->fecharet)->format('d/m/Y') }}</td>
                                    <td class="fw-bold">{{ $solicitud->cantidad }}</td>
                                    <!-- Columna Estado (ya existente) -->
                                    <td>
                                        @php
                                            $estados = [
                                                'aprobado' => ['class'=>'success','icon'=>'fa-check-circle','text'=>'Aprobado'],
                                                'pendiente_jefe' => ['class'=>'warning','icon'=>'fa-clock','text'=>'Pendiente Jefe'],
                                                'pendiente_rrhh' => ['class'=>'warning','icon'=>'fa-clock','text'=>'Pendiente RRHH'],
                                                'rechazado' => ['class'=>'danger','icon'=>'fa-times-circle','text'=>'Rechazado'],
                                                'cancelado' => ['class'=>'secondary','icon'=>'fa-ban','text'=>'Cancelado'],
                                            ];
                                            $est = $estados[$solicitud->estado] ?? ['class'=>'secondary','icon'=>'fa-question-circle','text'=>'Desconocido'];
                                            $editable = in_array($solicitud->estado, ['pendiente_jefe','pendiente_rrhh']);
                                        @endphp
                                        <span class="badge bg-{{ $est['class'] }} py-2 px-3">
                                            <i class="fas {{ $est['icon'] }} me-1"></i>{{ $est['text'] }}
                                        </span>
                                    </td>

                                    <!-- NUEVA COLUMNA: Responsable -->
                                    <td>
                                        @php
                                            $responsable = '-';
                                            $fecha = '';

                                            switch ($solicitud->estado) {
                                                case 'pendiente_jefe':
                                                    $responsable = $solicitud->jefe 
                                                        ? $solicitud->jefe->nombre . ' ' . $solicitud->jefe->apellidoPat
                                                        : 'Sin jefe asignado';
                                                    break;

                                                case 'pendiente_rrhh':
                                                    $responsable = 'Recursos Humanos';
                                                    break;

                                                case 'aprobado':
                                                    // ¿Quién aprobó? Si tiene fecha de aprobación de jefe, fue el jefe; 
                                                    // si tiene fecha de RRHH, fue RRHH.
                                                    if ($solicitud->fecha_aprobacion_jefe) {
                                                        $responsable = $solicitud->jefe 
                                                            ? $solicitud->jefe->nombre . ' ' . $solicitud->jefe->apellidoPat
                                                            : 'Jefe (sin nombre)';
                                                        $fecha = $solicitud->fecha_aprobacion_jefe;
                                                    } elseif ($solicitud->fecha_aprobacion_rrhh) {
                                                        $responsable = $solicitud->rrhh 
                                                            ? $solicitud->rrhh->nombre . ' ' . $solicitud->rrhh->apellidoPat
                                                            : 'RRHH (sin nombre)';
                                                        $fecha = $solicitud->fecha_aprobacion_rrhh;
                                                    } else {
                                                        $responsable = 'Aprobado (sin registro)';
                                                    }
                                                    break;

                                                case 'rechazado':
                                                    if ($solicitud->fecha_aprobacion_jefe && $solicitud->estado_jefe === 'rechazado') {
                                                        $responsable = $solicitud->jefe 
                                                            ? $solicitud->jefe->nombre . ' ' . $solicitud->jefe->apellidoPat
                                                            : 'Jefe (sin nombre)';
                                                    } elseif ($solicitud->fecha_aprobacion_rrhh && $solicitud->estado_rrhh === 'rechazado') {
                                                        $responsable = $solicitud->rrhh 
                                                            ? $solicitud->rrhh->nombre . ' ' . $solicitud->rrhh->apellidoPat
                                                            : 'RRHH (sin nombre)';
                                                    } else {
                                                        $responsable = 'Rechazado (sin responsable)';
                                                    }
                                                    break;

                                                default:
                                                    $responsable = '-';
                                            }
                                        @endphp

                                        <span class="small">
                                            <i class="fas fa-user-circle me-1 text-muted"></i>
                                            {{ $responsable }}
                                            @if($fecha)
                                                <br><span class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y H:i') }}
                                                </span>
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if($solicitud->observacion)
                                            <span class="text-muted small">{{ Str::limit($solicitud->observacion,30) }}</span>
                                            <button type="button" class="btn btn-sm btn-link p-0 ms-1" data-bs-toggle="tooltip" title="{{ $solicitud->observacion }}">
                                                <i class="fas fa-eye text-muted"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($editable)
                                            <button class="btn btn-sm btn-warning btn-editar me-1" data-id="{{ $solicitud->id }}" title="Editar"><i class="fas fa-edit"></i></button>
                                        @endif
                                        <button class="btn btn-sm btn-danger btn-eliminar me-1" data-id="{{ $solicitud->id }}" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        <a href="{{ route('vacacion.boleta', $solicitud->id) }}" class="btn btn-sm btn-danger" title="PDF"><i class="fas fa-file-pdf"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-4"><i class="fas fa-inbox text-muted fs-1 mb-3 d-block"></i><p class="text-muted mb-0">No has realizado ninguna solicitud de vacación</p></td></tr>
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
    <!-- MODAL CREAR (colocado al final del section) -->
    <!-- ============================================================ -->
    <div class="modal fade" id="modalCrearVacacion" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-vacaciones">
            <div class="modal-content" style="border-radius: 0.4rem;">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalCrearLabel"><i class="fas fa-plus me-2"></i>Nueva Solicitud de Vacación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    {{-- Formulario integrado directamente --}}
                    <div id="formCrear">
                        <input type="hidden" id="idpersona" value="{{ auth()->user()->persona->id ?? '' }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-floating mb-2">
                                        <input type="text" class="form-control form-control-sm" id="nomb" placeholder="Nombres y apellidos" disabled
                                               value="{{ auth()->user()->persona->nombre ?? '' }} {{ auth()->user()->persona->apellidoPat ?? '' }} {{ auth()->user()->persona->apellidoMat ?? '' }}">
                                        <label for="nomb">Servidor Público:</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating mb-2">
                                        <select class="form-select tipoSal" id="salida" disabled>
                                            @foreach ($tipoSal as $sal)
                                                @if ($sal->descripcion == 'VACACION')
                                                    <option value="{{ $sal->id }}">{{ $sal->descripcion }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <label for="salida">Tipo de salida:</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating mb-2">
                                        <input type="date" class="form-control fechasol" required readonly>
                                        <label>Fecha de solicitud:</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-floating mb-2">
                                                <input type="text" class="form-control fsalida" required>
                                                <label>Inicio vacación:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-floating mb-2">
                                                <input type="text" class="form-control fretorno" required>
                                                <label>Fin vacación:</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-floating mb-2">
                                                <input type="text" class="form-control border-warning totaldias" readonly>
                                                <label>Cantidad de días solicitadas:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input mdia" type="checkbox" id="mdia">
                                            <label class="form-check-label" for="mdia">Medio día</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2 form-floating">
                                    <input type="text" class="form-control border border-warning observacion" placeholder="Observacion" required>
                                    <label>Observaciones:</label>
                                </div>
                                <div class="bg-secondary text-white p-2">
                                    <p><strong>Días disponibles de vacación:</strong> <span>{{ number_format($resumen['saldo_actual'], 1) }}</span></p>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <div class="col-md-10">
                                        <div class="form-floating">
                                            <input type="text" class="form-control dato" placeholder="Nombre o apellido" required>
                                            <label>Buscar inmediato superior (nombre o apellido):</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-start">
                                        <button class="btn btn-success btnBuscarSup"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" class="idSup">
                                    <div class="form-floating mb-2">
                                        <input type="text" class="form-control nombreSup" readonly>
                                        <label>Inmediato Superior Seleccionado:</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <table class="table">
                                        <thead><tr><th>Nombre</th><th>Acción</th></tr></thead>
                                        <tbody class="tablaSup"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarCrear"><i class="fas fa-save me-2"></i>Registrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarVacacion" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalEditarLabel"><i class="fas fa-edit me-2"></i>Editar Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Mismo formulario pero con datos precargados (se clona o se llena por JS) -->
                    <div id="formEditar">
                        <input type="hidden" id="edit_solicitud_id" value="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-floating mb-2">
                                        <input type="text" class="form-control form-control-sm" id="edit_nomb" placeholder="Nombres y apellidos" disabled
                                               value="{{ auth()->user()->persona->nombre ?? '' }} {{ auth()->user()->persona->apellidoPat ?? '' }} {{ auth()->user()->persona->apellidoMat ?? '' }}">
                                        <label for="edit_nomb">Servidor Público:</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating mb-2">
                                        <select class="form-select tipoSal" id="edit_salida" disabled>
                                            @foreach ($tipoSal as $sal)
                                                @if ($sal->descripcion == 'VACACION')
                                                    <option value="{{ $sal->id }}">{{ $sal->descripcion }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <label for="edit_salida">Tipo de salida:</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating mb-2">
                                        <input type="date" class="form-control fechasol" id="edit_fechasol" required readonly>
                                        <label>Fecha de solicitud:</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-floating mb-2">
                                                <input type="text" class="form-control fsalida" id="edit_fsalida" required>
                                                <label>Inicio vacación:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-floating mb-2">
                                                <input type="text" class="form-control fretorno" id="edit_fretorno" required>
                                                <label>Fin vacación:</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-floating mb-2">
                                                <input type="text" class="form-control border-warning totaldias" id="edit_totaldias" readonly>
                                                <label>Cantidad de días solicitadas:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input mdia" type="checkbox" id="edit_mdia">
                                            <label class="form-check-label" for="edit_mdia">Medio día</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2 form-floating">
                                    <input type="text" class="form-control border border-warning observacion" id="edit_observacion" placeholder="Observacion" required>
                                    <label>Observaciones:</label>
                                </div>
                                <div class="bg-secondary text-white p-2">
                                    <p><strong>Días disponibles de vacación:</strong> <span class="diasDisponiblesSpan">0</span></p>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <div class="col-md-10">
                                        <div class="form-floating">
                                            <input type="text" class="form-control dato" id="edit_dato" placeholder="Nombre o apellido" required>
                                            <label>Buscar inmediato superior (nombre o apellido):</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-start">
                                        <button class="btn btn-success btnBuscarSup"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" class="idSup" id="edit_idSup">
                                    <div class="form-floating mb-2">
                                        <input type="text" class="form-control nombreSup" id="edit_nombreSup" readonly>
                                        <label>Inmediato Superior Seleccionado:</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <table class="table">
                                        <thead><tr><th>Nombre</th><th>Acción</th></tr></thead>
                                        <tbody class="tablaSup" id="edit_tablaSup"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" id="btnGuardarEditar"><i class="fas fa-save me-2"></i>Actualizar</button>
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
    // Usamos json_encode con opciones para evitar caracteres especiales
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
            const fs = inputFsalida.value;
            const fr = inputFretorno.value;
            const md = checkboxMedio ? checkboxMedio.checked : false;
            if (!fs || !fr) { inputTotal.value = ''; return; }
            inputTotal.value = calcularDias(fs, fr, feriados, md);
        }

        if (inputFsalida) {
            flatpickr(inputFsalida, {
                minDate: 'today',
                disable: [deshabilitar],
                locale: 'es',
                dateFormat: 'Y-m-d',
                onChange: recalc
            });
        }
        if (inputFretorno) {
            flatpickr(inputFretorno, {
                minDate: 'today',
                disable: [deshabilitar],
                locale: 'es',
                dateFormat: 'Y-m-d',
                onChange: recalc
            });
        }
        if (checkboxMedio) {
            checkboxMedio.addEventListener('change', recalc);
        }
        recalc();
    }

    // Cargar días disponibles en un contenedor
    // Función para cargar días disponibles desde el servidor
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
            span.textContent = '0'; // Valor por defecto en caso de error
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
        // Fecha de solicitud hoy
        const fechasol = container.querySelector('.fechasol');
        if (fechasol) fechasol.value = new Date().toISOString().split('T')[0];
        // Limpiar selección de superior
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
        // Los datos se cargan al abrir el modal de edición (ver evento btn-editar)
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
                    // Recalcular días (Flatpickr ya está inicializado al abrir el modal)
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
        axios.put(`/vacacion/update-vacacion/${id}/salida`, data)
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
