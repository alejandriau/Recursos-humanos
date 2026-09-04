@extends('layouts.baseusr')

@section('comunicado')
    @if ($gestion->first())
        @php $anio = $gestion->first()->anio; @endphp
        <div class="alert alert-success" role="alert">
            <div class="row justify-content-start">
                <div>
                    <b><i class="fa-solid fa-calendar-days me-2 fs-5"></i> FERIADOS GESTIÓN: {{ $anio }}</b>
                    <span class="badge bg-info text-white">{{ count($feriado) }} feriado(s)</span>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Descripción</th>
                            <th scope="col">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($feriado as $fer)
                            <tr>
                                <td><i class="fa-solid fa-calendar-days me-2 text-warning"></i>{{ $fer->descripcion }}</td>
                                <td>{{ \Carbon\Carbon::parse($fer->fechaf)->format('d-m-Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p>FERIADOS GESTIÓN: Ninguno disponible</p>
    @endif
@endsection

@section('cuerpo')
<div class="row">
    <div class="col-md-10 container mt-4 p-4 bg-white shadow rounded">
        <div class="alert alert-primary" role="alert">
            <h5 class="text-center">Formulario de Solicitud de Comisión</h5>
        </div>
        <div id="app">
            <!-- Botón para abrir modal de creación -->
            <div class="text-start mb-3">
                <button class="btn" id="btnNuevaComision" style="background-color: #E67E22; border-color: #E67E22; color: white;">

                    <i class="fa fa-plus"></i> Nueva Solicitud de Comisión
                </button>
                <a href="/homeusr" class="btn btn-secondary">
                    <i class="fa fa-times"></i> Cancelar
                </a>
            </div>

            <div class="mt-5">
                <h5 class="mb-3">Mis solicitudes de comisión</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaSolicitudes">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Fecha salida</th>
                                <th>Hora salida</th>
                                <th>Fecha retorno</th>
                                <th>Hora retorno</th>
                                <th>Motivo</th>
                                <th>🧑‍💼 Jefe Inmediato</th>
                                <th>🏢 RRHH</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodySolicitudes">
                            @foreach($solicitudes as $sol)
                            <tr id="fila-{{ $sol->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($sol->fechasal)->format('d-m-Y') }}</td>
                                <td>{{ $sol->horasal }}</td>
                                <td>{{ \Carbon\Carbon::parse($sol->fecharet)->format('d-m-Y') }}</td>
                                <td>{{ $sol->horaret }}</td>
                                <td>{{ Str::limit($sol->motivo, 30) }}</td>

                                {{-- ===== COLUMNA JEFE INMEDIATO ===== --}}
                                <td>
                                    @php
                                        $estadoJefe = strtolower($sol->estado_jefe ?? '');
                                        $badgeJefe = 'bg-secondary';
                                        $textoJefe = 'Sin estado';
                                        $iconoJefe = 'fa-question-circle';

                                        if ($estadoJefe === 'pendiente') {
                                            $badgeJefe = 'bg-warning text-dark';
                                            $textoJefe = 'Pendiente';
                                            $iconoJefe = 'fa-clock';
                                        } elseif ($estadoJefe === 'aprobado') {
                                            $badgeJefe = 'bg-success';
                                            $textoJefe = 'Aprobado';
                                            $iconoJefe = 'fa-check-circle';
                                        } elseif ($estadoJefe === 'rechazado') {
                                            $badgeJefe = 'bg-danger';
                                            $textoJefe = 'Rechazado';
                                            $iconoJefe = 'fa-times-circle';
                                        } elseif (empty($estadoJefe)) {
                                            $badgeJefe = 'bg-light text-dark border';
                                            $textoJefe = 'Sin asignar';
                                            $iconoJefe = 'fa-user-slash';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeJefe }} py-2 px-3 mb-1 d-inline-block">
                                        <i class="fas {{ $iconoJefe }} me-1"></i>{{ $textoJefe }}
                                    </span>
                                    <div class="small mt-1">
                                        @if($sol->jefe)
                                            <i class="fas fa-user-tie me-1 text-muted"></i>
                                            {{ $sol->jefe->nombre }} {{ $sol->jefe->apellidoPat }}
                                            @if($sol->fecha_aprobacion_jefe)
                                                <br>
                                                <span class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="far fa-calendar-alt me-1"></i>
                                                    {{ \Carbon\Carbon::parse($sol->fecha_aprobacion_jefe)->format('d/m/Y H:i') }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted fst-italic" style="font-size: 0.8rem;">
                                                <i class="fas fa-user-slash me-1"></i>Sin jefe asignado
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- ===== COLUMNA RRHH ===== --}}
                                <td>
                                    @php
                                        $estadoRRHH = strtolower($sol->estado_rrhh ?? '');
                                        
                                        // RRHH solo actúa si el jefe ya aprobó
                                        $rrhhPuedeActuar = in_array($estadoJefe, ['aprobado']);
                                        $rrhhNoAplica = in_array($estadoJefe, ['rechazado']) || empty($estadoJefe);
                                        
                                        $badgeRRHH = 'bg-secondary';
                                        $textoRRHH = 'Desconocido';
                                        $iconoRRHH = 'fa-question-circle';

                                        if ($rrhhNoAplica) {
                                            $badgeRRHH = 'bg-light text-muted border';
                                            $textoRRHH = 'N/A';
                                            $iconoRRHH = 'fa-minus-circle';
                                        } elseif (!$rrhhPuedeActuar) {
                                            $badgeRRHH = 'bg-light text-dark border';
                                            $textoRRHH = 'En espera';
                                            $iconoRRHH = 'fa-hourglass-half';
                                        } elseif ($estadoRRHH === 'pendiente') {
                                            $badgeRRHH = 'bg-warning text-dark';
                                            $textoRRHH = 'Pendiente';
                                            $iconoRRHH = 'fa-clock';
                                        } elseif ($estadoRRHH === 'aprobado') {
                                            $badgeRRHH = 'bg-success';
                                            $textoRRHH = 'Aprobado';
                                            $iconoRRHH = 'fa-check-circle';
                                        } elseif ($estadoRRHH === 'rechazado') {
                                            $badgeRRHH = 'bg-danger';
                                            $textoRRHH = 'Rechazado';
                                            $iconoRRHH = 'fa-times-circle';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeRRHH }} py-2 px-3 mb-1 d-inline-block">
                                        <i class="fas {{ $iconoRRHH }} me-1"></i>{{ $textoRRHH }}
                                    </span>
                                    <div class="small mt-1">
                                        @if($sol->rrhh && $rrhhPuedeActuar && !$rrhhNoAplica)
                                            <i class="fas fa-user-shield me-1 text-muted"></i>
                                            {{ $sol->rrhh->nombre }} {{ $sol->rrhh->apellidoPat }}
                                            @if($sol->fecha_aprobacion_rrhh)
                                                <br>
                                                <span class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="far fa-calendar-alt me-1"></i>
                                                    {{ \Carbon\Carbon::parse($sol->fecha_aprobacion_rrhh)->format('d/m/Y H:i') }}
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

                                {{-- ===== ACCIONES ===== --}}
                                <td>
                                    @php
                                        // Editable solo si JEFE está pendiente o sin asignar
                                        $editable = empty($estadoJefe) || $estadoJefe === 'pendiente';
                                        // PDF solo si ambos aprobaron (ajusta según tu regla de negocio)
                                        $pdfDisponible = ($estadoJefe === 'aprobado' && $estadoRRHH === 'aprobado');
                                    @endphp

                                    @if($editable)
                                        <button class="btn btn-sm btn-info btn-editar" data-id="{{ $sol->id }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="{{ $sol->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @else
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="text-muted me-1" style="font-size: 0.8rem;">No editable</span>
                                            @if($pdfDisponible)
                                                <a href="{{ route('comision.boleta', $sol->id) }}" class="btn btn-sm btn-success" target="_blank">
                                                    <i class="fa fa-file-pdf"></i> PDF
                                                </a>
                                            @elseif($estadoJefe === 'aprobado' && (empty($estadoRRHH) || $estadoRRHH === 'pendiente'))
                                                {{-- Si tu regla es que PDF sale con solo jefe, cambia la línea de arriba --}}
                                                <a href="{{ route('comision.boleta', $sol->id) }}" class="btn btn-sm btn-success" target="_blank">
                                                    <i class="fa fa-file-pdf"></i> PDF
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2 container py-2 my-4 px-1 bg-white shadow rounded">
        @include('components.feriados')
    </div>
</div>

@section('modales')
<style>
    /* =========================================================
       MODALES DE COMISIÓN - DISEÑO MINIMALISTA
       ========================================================= */

    .modal-comision-min {
        max-width: 880px;
        width: 95%;
    }

    .modal-comision-min .modal-content {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0, 0, 0, .16);
    }

    /* HEADER */
    .modal-comision-min .modal-header {
        position: relative;
        min-height: 78px;
        padding: 17px 22px;
        border: none;
        overflow: hidden;
        background: #4DA3FF;
    }

    .modal-comision-min .header-bg {
        position: absolute;
        inset: 0;
        background: url('{{ asset('images/tejido-horizontal.jpg') }}')
                    center center / cover no-repeat;
        opacity: .28;
    }

    .modal-comision-min .header-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            90deg,
            rgba(255, 152, 0, .95),
            rgba(255, 152, 0, .72)
        );
    }

    .modal-comision-min .header-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
    }

    .modal-comision-min .header-icon {
        width: 42px; height: 42px;
        min-width: 42px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin-right: 12px;
        background: rgba(255,255,255,.18);
        color: #fff;
    }

    .modal-comision-min .modal-title {
        color: #fff;
        font-size: 17px;
        font-weight: 600;
        margin: 0;
    }

    .modal-comision-min .modal-subtitle {
        color: rgba(255,255,255,.85);
        font-size: 11px;
        margin-top: 2px;
    }

    .modal-comision-min .btn-close {
        position: relative;
        z-index: 3;
        filter: brightness(0) invert(1);
        opacity: .9;
    }

    /* BODY */
    .modal-comision-min .modal-body {
        padding: 22px;
        background: #fff;
    }

    .modal-comision-min .form-section {
        margin-bottom: 20px;
    }

    .modal-comision-min .section-title {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 12px;
        color: #343a40;
        font-size: 13px;
        font-weight: 600;
    }

    .modal-comision-min .section-title i {
        color: #4DA3FF;
        font-size: 12px;
    }

    .modal-comision-min .form-label {
        color: #6c757d;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .modal-comision-min .form-control,
    .modal-comision-min .form-select {
        border-color: #dee2e6;
        border-radius: 8px;
        font-size: 13px;
        box-shadow: none;
    }

    .modal-comision-min .form-control {
        min-height: 40px;
    }

    .modal-comision-min .form-control:focus,
    .modal-comision-min .form-select:focus {
        border-color: #4DA3FF;
        box-shadow: 0 0 0 3px rgba(77,163,255,.10);
    }

    .modal-comision-min .form-control:disabled,
    .modal-comision-min .form-control[readonly],
    .modal-comision-min .form-select:disabled {
        background-color: #f8f9fa;
    }

    /* FORM FLOATING */
    .modal-comision-min .form-floating {
        position: relative;
    }

    .modal-comision-min .form-floating > .form-control,
    .modal-comision-min .form-floating > .form-select {
        height: 52px;
        min-height: 52px;
        padding: 1.25rem .85rem .35rem;
    }

    .modal-comision-min .form-floating > textarea.form-control {
        height: auto;
        min-height: 80px;
        padding-top: 1.5rem;
    }

    .modal-comision-min .form-floating > label {
        padding: .55rem .85rem;
        font-size: 11px;
        color: #6c757d;
    }

    .modal-comision-min .form-floating > .form-control:focus ~ label,
    .modal-comision-min .form-floating > .form-control:not(:placeholder-shown) ~ label,
    .modal-comision-min .form-floating > .form-select ~ label {
        color: #6c757d;
    }

    /* FECHAS */
    .modal-comision-min .fecha-input {
        font-size: 14px !important;
        font-weight: 600;
    }

    /* SUPERIOR */
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

    /* FOOTER */
    .modal-comision-min .modal-footer {
        background: #fafafa;
        border-top: 1px solid #e9ecef;
        padding: 12px 20px;
    }

    .modal-comision-min .modal-footer .btn {
        border-radius: 7px;
        font-size: 12px;
        padding: 7px 15px;
    }

    .btn-guardar-comision {
        background: #4DA3FF;
        border-color: #4DA3FF;
        color: #fff;
    }

    .btn-guardar-comision:hover {
        background: #318fe8;
        border-color: #318fe8;
        color: #fff;
    }

    /* RESPONSIVE */
    @media (max-width: 767px) {
        .modal-comision-min {
            width: 96%;
            max-width: 96%;
        }
        .modal-comision-min .modal-body {
            padding: 16px;
        }
        .modal-comision-min .modal-header {
            padding: 15px 17px;
        }
        .modal-comision-min .modal-title {
            font-size: 15px;
        }
        .modal-comision-min .row > div {
            margin-bottom: 5px;
        }
    }
</style>

{{-- ==================== MODAL CREAR ==================== --}}
<div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-comision-min">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <div class="header-bg"></div>
                <div class="header-overlay"></div>
                <div class="header-content">
                    <div class="header-icon"><i class="fas fa-briefcase"></i></div>
                    <div>
                        <h5 class="modal-title">Nueva solicitud de comisión</h5>
                        <div class="modal-subtitle">Complete los datos del viaje</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">
                <input type="hidden" id="createIdpersona" value="{{ $persona->id }}">

                {{-- INFORMACIÓN --}}
                <div class="form-section">
                    <div class="section-title"><i class="fas fa-user"></i> Información</div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" disabled placeholder=" "
                                    value="{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}">
                                <label>Servidor público</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="createSalida" disabled>
                                    @foreach ($tipoSal as $sal)
                                        @if ($sal->descripcion == 'COMISION')
                                            <option value="{{ $sal->id }}">{{ $sal->descripcion }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <label>Tipo</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="createFechasol" readonly
                                    value="{{ now()->toDateString() }}">
                                <label>Fecha de solicitud</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PERÍODO --}}
                <div class="form-section">
                    <div class="section-title"><i class="fas fa-calendar-alt"></i> Período de comisión</div>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" class="form-control fecha-input" id="createFsalida" placeholder=" ">
                                <label>Fecha de salida</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="time" class="form-control" id="createHorasal" placeholder=" ">
                                <label>Hora de salida</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" class="form-control fecha-input" id="createFretorno" placeholder=" ">
                                <label>Fecha de retorno</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="time" class="form-control" id="createHoraret" placeholder=" ">
                                <label>Hora de retorno</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MOTIVO --}}
                <div class="form-section">
                    <div class="section-title"><i class="fas fa-comment-alt"></i> Motivo</div>
                    <div class="form-floating">
                        <textarea class="form-control" id="createMotivo" style="height:80px" placeholder=" "></textarea>
                        <label>Motivo o detalle de la comisión</label>
                    </div>
                </div>

                {{-- SUPERIOR --}}
                <div class="form-section mb-0">
                    <div class="section-title"><i class="fas fa-user-tie"></i> Inmediato superior</div>
                    <div class="row g-2">
                        <div class="col-md-7 busqueda-superior">
                            <label class="form-label">Buscar por nombre o apellido</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="createDato" placeholder="Ej. Juan Pérez...">
                                <button class="btn btn-outline-primary" type="button" id="createBtnBuscarSup">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Superior seleccionado</label>
                            <input type="hidden" id="createIdSup">
                            <input type="text" class="form-control superior-selected" id="createNombreSup" readonly
                                placeholder="Ninguno seleccionado">
                        </div>
                    </div>
                    <div class="tabla-superior">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre completo</th>
                                    <th class="text-center" style="width:70px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="createTbodySuperiores">
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">
                                        <i class="fas fa-search me-1"></i> Busque un superior para continuar
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-guardar-comision" id="createGuardarComision">
                    <i class="fas fa-paper-plane me-1"></i> Enviar solicitud
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ==================== MODAL EDITAR ==================== --}}
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-comision-min">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <div class="header-bg"></div>
                <div class="header-overlay"></div>
                <div class="header-content">
                    <div class="header-icon"><i class="fas fa-edit"></i></div>
                    <div>
                        <h5 class="modal-title">Editar solicitud de comisión</h5>
                        <div class="modal-subtitle">Modifique los datos del viaje</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">
                <input type="hidden" id="editId">

                {{-- INFORMACIÓN --}}
                <div class="form-section">
                    <div class="section-title"><i class="fas fa-user"></i> Información</div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" disabled placeholder=" "
                                    value="{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}">
                                <label>Servidor público</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" disabled>
                                    @foreach ($tipoSal as $sal)
                                        @if ($sal->descripcion == 'COMISION')
                                            <option value="{{ $sal->id }}">{{ $sal->descripcion }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <label>Tipo</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="editFechasol" readonly>
                                <label>Fecha de solicitud</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PERÍODO --}}
                <div class="form-section">
                    <div class="section-title"><i class="fas fa-calendar-alt"></i> Período de comisión</div>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" class="form-control fecha-input" id="editFsalida" placeholder=" ">
                                <label>Fecha de salida</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="time" class="form-control" id="editHorasal" placeholder=" ">
                                <label>Hora de salida</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" class="form-control fecha-input" id="editFretorno" placeholder=" ">
                                <label>Fecha de retorno</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="time" class="form-control" id="editHoraret" placeholder=" ">
                                <label>Hora de retorno</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MOTIVO --}}
                <div class="form-section">
                    <div class="section-title"><i class="fas fa-comment-alt"></i> Motivo</div>
                    <div class="form-floating">
                        <textarea class="form-control" id="editMotivo" style="height:80px" placeholder=" "></textarea>
                        <label>Motivo o detalle de la comisión</label>
                    </div>
                </div>

                {{-- SUPERIOR --}}
                <div class="form-section mb-0">
                    <div class="section-title"><i class="fas fa-user-tie"></i> Inmediato superior</div>
                    <div class="row g-2">
                        <div class="col-md-7 busqueda-superior">
                            <label class="form-label">Buscar por nombre o apellido</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="editDato" placeholder="Ej. Juan Pérez...">
                                <button class="btn btn-outline-primary" type="button" id="editBtnBuscarSup">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Superior seleccionado</label>
                            <input type="hidden" id="editIdSup">
                            <input type="text" class="form-control superior-selected" id="editNombreSup" readonly
                                placeholder="Ninguno seleccionado">
                        </div>
                    </div>
                    <div id="editResultadosBusqueda" style="max-height:150px; overflow-y:auto; margin-top:9px;"></div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-guardar-comision" id="guardarEdicion">
                    <i class="fas fa-save me-1"></i> Guardar cambios
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Flatpickr --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ----- Fechas inhábiles (feriados) -----
        const feriados = @json($feriado->pluck('fechaf')->map(fn($f) => \Carbon\Carbon::parse($f)->format('Y-m-d')));

        const fechasFeriado = feriados.map(f => {
            const [y, m, d] = f.split("-").map(Number);
            return new Date(y, m - 1, d);
        });

        const deshabilitarFechas = (date) => {
            const esFinDeSemana = date.getDay() === 0 || date.getDay() === 6;
            const esFeriado = fechasFeriado.some(f => f.toDateString() === date.toDateString());
            return esFinDeSemana || esFeriado;
        };

        // Configurar flatpickr para el modal de creación
        const createFsalidaPicker = flatpickr("#createFsalida", {
            minDate: "today",
            disable: [deshabilitarFechas],
            locale: "es",
            dateFormat: "Y-m-d"
        });

        const createFretornoPicker = flatpickr("#createFretorno", {
            minDate: "today",
            disable: [deshabilitarFechas],
            locale: "es",
            dateFormat: "Y-m-d"
        });

        // Configurar flatpickr para el modal de edición
        flatpickr("#editFsalida", {
            minDate: "today",
            disable: [deshabilitarFechas],
            locale: "es",
            dateFormat: "Y-m-d"
        });

        flatpickr("#editFretorno", {
            minDate: "today",
            disable: [deshabilitarFechas],
            locale: "es",
            dateFormat: "Y-m-d"
        });

        // ----- FUNCIONALIDAD DEL MODAL DE CREACIÓN -----

        // Abrir modal de creación
        document.getElementById("btnNuevaComision").addEventListener("click", function() {
            // Limpiar campos del modal
            document.getElementById("createFsalida").value = '';
            document.getElementById("createFretorno").value = '';
            document.getElementById("createHorasal").value = '';
            document.getElementById("createHoraret").value = '';
            document.getElementById("createMotivo").value = '';
            document.getElementById("createIdSup").value = '';
            document.getElementById("createNombreSup").value = '';
            document.getElementById("createTbodySuperiores").innerHTML = '';
            document.getElementById("createDato").value = '';

            // Resetear flatpickr
            createFsalidaPicker.setDate(null);
            createFretornoPicker.setDate(null);

            const modal = new bootstrap.Modal(document.getElementById('modalCrear'));
            modal.show();
        });

        // Búsqueda del superior en el modal de creación
        const createDatoInput = document.getElementById("createDato");
        const createBtnBuscar = document.getElementById("createBtnBuscarSup");
        const createTbody = document.getElementById("createTbodySuperiores");
        const createIdSupHidden = document.getElementById("createIdSup");
        const createNombreSupInput = document.getElementById("createNombreSup");

        function buscarSuperioresCreate() {
            const buscar = createDatoInput.value.trim();
            if (!buscar) {
                Swal.fire("Advertencia", "Debe ingresar un nombre o apellido antes de buscar", "warning");
                return;
            }

            createTbody.innerHTML = '<tr><td colspan="2" class="text-center">Buscando...</td></tr>';

            axios.get('/vacacion/buscar-superior', { params: { buscar: buscar } })
                .then(res => {
                    const data = res.data;
                    if (data.length === 0) {
                        createTbody.innerHTML = '<tr><td colspan="2" class="text-center">No se encontraron resultados</td></tr>';
                        return;
                    }
                    let html = '';
                    data.forEach(p => {
                        const nombreCompleto = `${p.nombre} ${p.apellidoPat} ${p.apellidoMat}`;
                        html += `
                            <tr>
                                <td>${nombreCompleto}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm create-asignar-sup" data-id="${p.id}" data-nombre="${nombreCompleto}">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    createTbody.innerHTML = html;

                    document.querySelectorAll(".create-asignar-sup").forEach(btn => {
                        btn.addEventListener("click", function() {
                            createIdSupHidden.value = this.dataset.id;
                            createNombreSupInput.value = this.dataset.nombre;
                            createTbody.innerHTML = '';
                            createDatoInput.value = '';
                        });
                    });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire("Error", "No se pudo completar la búsqueda", "error");
                });
        }

        createBtnBuscar.addEventListener("click", buscarSuperioresCreate);
        createDatoInput.addEventListener("keyup", function(e) {
            if (e.key === "Enter") buscarSuperioresCreate();
        });

        // Registrar comisión desde el modal de creación
        document.getElementById("createGuardarComision").addEventListener("click", async function() {
            const idpersona = document.getElementById("createIdpersona").value;
            const tipoSal = document.getElementById("createSalida").value;
            const fechasol = document.getElementById("createFechasol").value;
            const fsalida = document.getElementById("createFsalida").value;
            const fretorno = document.getElementById("createFretorno").value;
            const horasal = document.getElementById("createHorasal").value;
            const horaret = document.getElementById("createHoraret").value;
            const motivo = document.getElementById("createMotivo").value;
            const idSup = document.getElementById("createIdSup").value;

            // Validación básica
            if (!idpersona || !tipoSal || !fechasol || !fsalida || !fretorno || !horasal || !horaret || !motivo || !idSup) {
                Swal.fire("Faltan datos", "Complete todos los campos obligatorios", "warning");
                return;
            }

            const data = {
                idpersona: idpersona,
                tipoSal: tipoSal,
                fechasol: fechasol,
                fsalida: fsalida,
                fretorno: fretorno,
                horasal: horasal,
                horaret: horaret,
                motivo: motivo,
                idSup: idSup
            };

            try {
                const res = await axios.post('/comision/registrar', data);
                Swal.fire("Éxito", res.data.mensaje || "Solicitud registrada correctamente", "success");
                // Cerrar el modal
                bootstrap.Modal.getInstance(document.getElementById('modalCrear')).hide();
                // Recargar la tabla de solicitudes
                cargarSolicitudes();
            } catch (err) {
                console.error("Error completo:", err);
                console.error("Datos de respuesta:", err.response?.data);

                let mensaje = "No se pudo registrar. Intente nuevamente.";

                if (err.response) {
                    const data = err.response.data;
                    if (data.error) {
                        mensaje = data.error;
                    } else if (data.message && data.errors) {
                        const firstError = Object.values(data.errors)[0]?.[0];
                        mensaje = firstError || data.message;
                    } else if (data.message) {
                        mensaje = data.message;
                    }
                }

                Swal.fire("Error", mensaje, "error");
            }
        });

        // ----- FUNCIONALIDAD DE LA TABLA DE SOLICITUDES (NUEVA VERSIÓN CON COLUMNA RESPONSABLE) -----

        // Función para recargar la tabla de solicitudes
        function cargarSolicitudes() {
            axios.get('/comision/mis-solicitudes')
                .then(res => {
                    if (!res.data.success) {
                        Swal.fire('Error', res.data.message, 'error');
                        return;
                    }

                    const data = res.data.data;
                    const tbody = document.getElementById('tbodySolicitudes');
                    if (!tbody) return;

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No tienes solicitudes registradas</td></tr>';
                        return;
                    }

                    let html = '';
                    data.forEach((sol, index) => {
                        const estadoJefe = (sol.estado_jefe || '').toLowerCase();
                        const estadoRRHH = (sol.estado_rrhh || '').toLowerCase();

                        // === JEFE INMEDIATO ===
                        let badgeJefe = 'bg-secondary';
                        let textoJefe = 'Sin estado';
                        let iconoJefe = 'fa-question-circle';

                        if (estadoJefe === 'pendiente') {
                            badgeJefe = 'bg-warning text-dark'; textoJefe = 'Pendiente'; iconoJefe = 'fa-clock';
                        } else if (estadoJefe === 'aprobado') {
                            badgeJefe = 'bg-success'; textoJefe = 'Aprobado'; iconoJefe = 'fa-check-circle';
                        } else if (estadoJefe === 'rechazado') {
                            badgeJefe = 'bg-danger'; textoJefe = 'Rechazado'; iconoJefe = 'fa-times-circle';
                        } else if (!estadoJefe) {
                            badgeJefe = 'bg-light text-dark border'; textoJefe = 'Sin asignar'; iconoJefe = 'fa-user-slash';
                        }

                        const nombreJefe = sol.jefe_nombre && sol.jefe_apellido_pat
                            ? `${sol.jefe_nombre} ${sol.jefe_apellido_pat}`
                            : null;

                        const htmlJefe = nombreJefe
                            ? `<i class="fas fa-user-tie me-1 text-muted"></i>${nombreJefe}
                            ${sol.fecha_aprobacion_jefe ? `<br><span class="text-muted" style="font-size:0.75rem;"><i class="far fa-calendar-alt me-1"></i>${sol.fecha_aprobacion_jefe}</span>` : ''}`
                            : `<span class="text-muted fst-italic" style="font-size:0.8rem;"><i class="fas fa-user-slash me-1"></i>Sin jefe asignado</span>`;

                        // === RRHH ===
                        const rrhhPuedeActuar = estadoJefe === 'aprobado';
                        const rrhhNoAplica = estadoJefe === 'rechazado' || !estadoJefe;

                        let badgeRRHH = 'bg-secondary';
                        let textoRRHH = 'Desconocido';
                        let iconoRRHH = 'fa-question-circle';

                        if (rrhhNoAplica) {
                            badgeRRHH = 'bg-light text-muted border'; textoRRHH = 'N/A'; iconoRRHH = 'fa-minus-circle';
                        } else if (!rrhhPuedeActuar) {
                            badgeRRHH = 'bg-light text-dark border'; textoRRHH = 'En espera'; iconoRRHH = 'fa-hourglass-half';
                        } else if (estadoRRHH === 'pendiente') {
                            badgeRRHH = 'bg-warning text-dark'; textoRRHH = 'Pendiente'; iconoRRHH = 'fa-clock';
                        } else if (estadoRRHH === 'aprobado') {
                            badgeRRHH = 'bg-success'; textoRRHH = 'Aprobado'; iconoRRHH = 'fa-check-circle';
                        } else if (estadoRRHH === 'rechazado') {
                            badgeRRHH = 'bg-danger'; textoRRHH = 'Rechazado'; iconoRRHH = 'fa-times-circle';
                        }

                        const nombreRRHH = sol.rrhh_nombre && sol.rrhh_apellido_pat
                            ? `${sol.rrhh_nombre} ${sol.rrhh_apellido_pat}`
                            : null;

                        let htmlRRHH = '';
                        if (rrhhPuedeActuar && !rrhhNoAplica) {
                            htmlRRHH = `<i class="fas fa-user-shield me-1 text-muted"></i>${nombreRRHH}
                                ${sol.fecha_aprobacion_rrhh ? `<br><span class="text-muted" style="font-size:0.75rem;"><i class="far fa-calendar-alt me-1"></i>${sol.fecha_aprobacion_rrhh}</span>` : ''}`;
                        } else if (rrhhNoAplica) {
                            htmlRRHH = `<span class="text-muted fst-italic" style="font-size:0.8rem;">No aplica por estado del jefe</span>`;
                        } else {
                            htmlRRHH = `<span class="text-muted fst-italic" style="font-size:0.8rem;">Esperando aprobación del jefee</span>`;
                        }

                        // === ACCIONES ===
                        // PDF: ajusta esta regla según tu negocio
                        // Opción A: solo con jefe aprobado (aunque RRHH no haya visto)
                        // Opción B: solo cuando RRHH también aprobó
                        const pdfDisponible = estadoJefe === 'aprobado'; // ← cambia a: estadoJefe === 'aprobado' && estadoRRHH === 'aprobado' si prefieres

                        let acciones = '';
                        if (sol.editable) {
                            acciones = `
                                <button class="btn btn-sm btn-info btn-editar" data-id="${sol.id}"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger btn-eliminar" data-id="${sol.id}"><i class="fa fa-trash"></i></button>
                            `;
                        } else {
                            const pdfBtn = pdfDisponible
                                ? `<a href="/comision/boleta/${sol.id}" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-file-pdf"></i> PDF</a>`
                                : '';
                            acciones = `
                                <div class="d-flex align-items-center gap-1">
                                    <span class="text-muted me-1" style="font-size:0.8rem;">No editable</span>
                                    ${pdfBtn}
                                </div>
                            `;
                        }

                        // === FILA ===
                        html += `
                            <tr id="fila-${sol.id}">
                                <td>${index + 1}</td>
                                <td>${sol.fechasal || ''}</td>
                                <td>${sol.horasal || ''}</td>
                                <td>${sol.fecharet || ''}</td>
                                <td>${sol.horaret || ''}</td>
                                <td style="max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${sol.motivo || ''}">${sol.motivo || ''}</td>
                                <td>
                                    <span class="badge ${badgeJefe} py-2 px-3 mb-1 d-inline-block"><i class="fas ${iconoJefe} me-1"></i>${textoJefe}</span>
                                    <div class="small mt-1">${htmlJefe}</div>
                                </td>
                                <td>
                                    <span class="badge ${badgeRRHH} py-2 px-3 mb-1 d-inline-block"><i class="fas ${iconoRRHH} me-1"></i>${textoRRHH}</span>
                                    <div class="small mt-1">${htmlRRHH}</div>
                                </td>
                                <td>${acciones}</td>
                            </tr>
                        `;
                    });

                    tbody.innerHTML = html;

                    // Reasignar eventos
                    document.querySelectorAll('.btn-editar').forEach(btn => {
                        btn.addEventListener('click', () => abrirModalEditar(btn.dataset.id));
                    });
                    document.querySelectorAll('.btn-eliminar').forEach(btn => {
                        btn.addEventListener('click', () => eliminarSolicitud(btn.dataset.id));
                    });
                })
                .catch(err => {
                    console.error(err.response?.data);
                    Swal.fire('Error', err.response?.data?.message || 'No se pudo cargar la lista de solicitudes', 'error');
                });
        }

        // Eliminar solicitud
        function eliminarSolicitud(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/comision/eliminar/${id}`)
                        .then(res => {
                            Swal.fire('Eliminado', res.data.mensaje, 'success');
                            cargarSolicitudes();
                        })
                        .catch(err => {
                            let msg = err.response?.data?.error || 'Error al eliminar';
                            Swal.fire('Error', msg, 'error');
                        });
                }
            });
        }

        // Abrir modal de edición
        function abrirModalEditar(id) {
            axios.get(`/comision/obtener/${id}`)
                .then(res => {
                    const data = res.data;
                    document.getElementById('editId').value = data.id;
                    document.getElementById('editFsalida').value = data.fechasal;
                    document.getElementById('editFretorno').value = data.fecharet;
                    document.getElementById('editHorasal').value = data.horasal;
                    document.getElementById('editHoraret').value = data.horaret;
                    document.getElementById('editMotivo').value = data.motivo;

                    if (data.jefe_id) {
                        document.getElementById('editIdSup').value = data.jefe_id;
                        axios.get(`/vacacion/buscar-superior`, { params: { buscar: '' } })
                            .then(res2 => {
                                const jefe = res2.data.find(p => p.id == data.jefe_id);
                                if (jefe) {
                                    document.getElementById('editNombreSup').value = `${jefe.nombre} ${jefe.apellidoPat} ${jefe.apellidoMat}`;
                                }
                            });
                    }

                    document.getElementById('editResultadosBusqueda').innerHTML = '';
                    const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
                    modal.show();
                })
                .catch(err => {
                    Swal.fire('Error', err.response?.data?.error || 'No se pudo cargar la solicitud', 'error');
                });
        }

        // Guardar edición
        document.getElementById('guardarEdicion').addEventListener('click', function() {
            const id = document.getElementById('editId').value;
            const data = {
                fsalida: document.getElementById('editFsalida').value,
                fretorno: document.getElementById('editFretorno').value,
                horasal: document.getElementById('editHorasal').value,
                horaret: document.getElementById('editHoraret').value,
                motivo: document.getElementById('editMotivo').value,
                idSup: document.getElementById('editIdSup').value,
            };
            if (!data.fsalida || !data.fretorno || !data.horasal || !data.horaret || !data.motivo || !data.idSup) {
                Swal.fire('Faltan datos', 'Complete todos los campos', 'warning');
                return;
            }
            axios.put(`/comision/actualizar/${id}`, data)
                .then(res => {
                    Swal.fire('Actualizado', res.data.mensaje, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
                    cargarSolicitudes();
                })
                .catch(err => {
                    let msg = err.response?.data?.error || err.response?.data?.message || 'Error al actualizar';
                    Swal.fire('Error', msg, 'error');
                });
        });

        // Búsqueda de superior en el modal de edición
        document.getElementById('editBtnBuscarSup').addEventListener('click', function() {
            const buscar = document.getElementById('editDato').value.trim();
            if (!buscar) {
                Swal.fire('Advertencia', 'Ingrese un nombre o apellido', 'warning');
                return;
            }
            const contenedor = document.getElementById('editResultadosBusqueda');
            contenedor.innerHTML = '<p class="text-muted">Buscando...</p>';
            axios.get('/vacacion/buscar-superior', { params: { buscar: buscar } })
                .then(res => {
                    const data = res.data;
                    if (data.length === 0) {
                        contenedor.innerHTML = '<p class="text-muted">No se encontraron resultados</p>';
                        return;
                    }
                    let html = '<ul class="list-group">';
                    data.forEach(p => {
                        const nombre = `${p.nombre} ${p.apellidoPat} ${p.apellidoMat}`;
                        html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                    ${nombre}
                                    <button class="btn btn-sm btn-primary asignar-sup-modal" data-id="${p.id}" data-nombre="${nombre}">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </li>`;
                    });
                    html += '</ul>';
                    contenedor.innerHTML = html;

                    document.querySelectorAll('.asignar-sup-modal').forEach(btn => {
                        btn.addEventListener('click', function() {
                            document.getElementById('editIdSup').value = this.dataset.id;
                            document.getElementById('editNombreSup').value = this.dataset.nombre;
                            contenedor.innerHTML = '';
                            document.getElementById('editDato').value = '';
                        });
                    });
                })
                .catch(err => {
                    console.error(err);
                    contenedor.innerHTML = '<p class="text-danger">Error en la búsqueda</p>';
                });
        });

        // Cargar solicitudes al iniciar
        cargarSolicitudes();
    });
</script>
@endsection