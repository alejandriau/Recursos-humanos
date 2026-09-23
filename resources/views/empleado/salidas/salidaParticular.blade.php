@extends('layouts.baseusr')

@section('comunicado')
    @php
        $ges = $gestion->first();
    @endphp
    @if ($ges)
        <div class="alert alert-success" role="alert">
            <div class="row justify-content-start">
                <div>
                    <b><i class="fa-solid fa-calendar-days me-2 fs-5"></i> FERIADOS GESTIÓN: {{ $ges->anio }}</b>
                    <span class="badge bg-info text-white">{{ count($feriado) }} feriado(s)</span>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="col-md-7">Descripción</th>
                            <th scope="col" class="col-md-5">Fecha</th>
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
            <div class="alert alert-success" role="alert">
                <h5 class="text-center">Gestión de Salidas Particulares</h5>
            </div>

            <div class="text-start mb-3">
                <button type="button" class="btn btn-success" id="btnNuevoParticular">
                    <i class="fa-solid fa-plus"></i> Nueva Salida Particular
                </button>
                <a href="/homeusr" class="btn btn-secondary">
                    <i class="fa fa-times"></i> Cancelar
                </a>
            </div>

            <div class="mt-5">
                <h5 class="mb-3">Mis salidas particulares</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaParticulares">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Fecha solicitud</th>
                                <th>Fecha salida</th>
                                <th>Fecha retorno</th>
                                <th>Cantidad</th>
                                <th>Motivo</th>
                                <th>🧑‍💼 Jefe Inmediato</th>
                                <th>🏢 RRHH</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodySalidas">
                            @foreach ($salidas as $salida)
                                @php
                                    $estadoJefe = strtolower($salida->estado_jefe ?? '');
                                    $estadoRRHH = strtolower($salida->estado_rrhh ?? '');
                                @endphp
                                <tr id="fila-{{ $salida->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($salida->fechasol)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($salida->fechasal)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($salida->fecharet)->format('d-m-Y') }}</td>
                                    <td>{{ $salida->cantidad }}</td>
                                    <td style="max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $salida->motivo }}">
                                        {{ Str::limit($salida->motivo, 30) }}
                                    </td>

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
                                            @if($salida->jefe)
                                                <i class="fas fa-user-tie me-1 text-muted"></i>
                                                {{ $salida->jefe->nombre }} {{ $salida->jefe->apellidoPat }}
                                                @if($salida->fecha_aprobacion_jefe)
                                                    <br>
                                                    <span class="text-muted" style="font-size: 0.75rem;">
                                                        <i class="far fa-calendar-alt me-1"></i>
                                                        {{ \Carbon\Carbon::parse($salida->fecha_aprobacion_jefe)->format('d/m/Y H:i') }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted fst-italic" style="font-size: 0.8rem;">
                                                    <i class="fas fa-user-slash me-1"></i>Sin jefe asignado
                                                </span>
                                            @endif
                                        </div>
                                    </td>

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
                                                {{ $salida->rrhh->nombre ?? '' }} {{ $salida->rrhh->apellidoPat ?? '' }}
                                                @if($salida->fecha_aprobacion_rrhh)
                                                    <br>
                                                    <span class="text-muted" style="font-size: 0.75rem;">
                                                        <i class="far fa-calendar-alt me-1"></i>
                                                        {{ \Carbon\Carbon::parse($salida->fecha_aprobacion_rrhh)->format('d/m/Y H:i') }}
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
                                        @php
                                            $editable = empty($estadoJefe) || $estadoJefe === 'pendiente';
                                            $pdfDisponible = $estadoJefe === 'aprobado';
                                        @endphp
                                        @if($editable)
                                            <button class="btn btn-sm btn-info btnEditar" data-id="{{ $salida->id }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger btnEliminar" data-id="{{ $salida->id }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @else
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="text-muted me-1" style="font-size: 0.8rem;">No editable</span>
                                                @if($pdfDisponible)
                                                    <a href="{{ route('salidas.particular.pdf', $salida->id) }}" class="btn btn-sm btn-success" target="_blank">
                                                        <i class="fa fa-file-pdf"></i> PDF
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if ($salidas->isEmpty())
                                <tr><td colspan="9" class="text-center text-muted py-4">No hay salidas particulares registradas.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-2 container py-2 my-4 px-1 bg-white shadow rounded">
            @include('components.feriados')
        </div>
    </div>
@endsection

@section('modales')
<style>
    .modal-particular-min { max-width: 900px; width: 95%; }
    .modal-particular-min .modal-content { border: none; border-radius: 10px; overflow: hidden; box-shadow: 0 12px 40px rgba(0, 0, 0, .16); }
    .modal-particular-min .modal-header { position: relative; min-height: 78px; padding: 17px 22px; border: none; overflow: hidden; background: #4DA3FF; }
    .modal-particular-min .header-bg { position: absolute; inset: 0; background: url('{{ asset('images/tejido-horizontal.jpg') }}') center center / cover no-repeat; opacity: .28; }
    .modal-particular-min .header-overlay { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(126, 87, 194, .95), rgba(126, 87, 194, .72)); }
    .modal-particular-min .header-content { position: relative; z-index: 2; display: flex; align-items: center; }
    .modal-particular-min .header-icon { width: 42px; height: 42px; min-width: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px; background: rgba(255,255,255,.18); color: #fff; }
    .modal-particular-min .modal-title { color: #fff; font-size: 17px; font-weight: 600; margin: 0; }
    .modal-particular-min .modal-subtitle { color: rgba(255,255,255,.85); font-size: 11px; margin-top: 2px; }
    .modal-particular-min .btn-close { position: relative; z-index: 3; filter: brightness(0) invert(1); opacity: .9; }
    .modal-particular-min .modal-body { padding: 22px; background: #fff; }
    .modal-particular-min .form-section { margin-bottom: 20px; }
    .modal-particular-min .section-title { display: flex; align-items: center; gap: 7px; margin-bottom: 12px; color: #343a40; font-size: 13px; font-weight: 600; }
    .modal-particular-min .section-title i { color: #4DA3FF; font-size: 12px; }
    .modal-particular-min .form-label { color: #6c757d; font-size: 11px; font-weight: 600; margin-bottom: 5px; }
    .modal-particular-min .form-control, .modal-particular-min .form-select { border-color: #dee2e6; border-radius: 8px; font-size: 13px; box-shadow: none; }
    .modal-particular-min .form-control { min-height: 40px; }
    .modal-particular-min .form-control:focus, .modal-particular-min .form-select:focus { border-color: #4DA3FF; box-shadow: 0 0 0 3px rgba(77,163,255,.10); }
    .modal-particular-min .form-control:disabled, .modal-particular-min .form-control[readonly], .modal-particular-min .form-select:disabled { background-color: #f8f9fa; }
    .modal-particular-min .form-floating { position: relative; }
    .modal-particular-min .form-floating > .form-control, .modal-particular-min .form-floating > .form-select { height: 52px; min-height: 52px; padding: 1.25rem .85rem .35rem; }
    .modal-particular-min .form-floating > textarea.form-control { height: auto; min-height: 90px; padding-top: 1.5rem; }
    .modal-particular-min .form-floating > label { padding: .55rem .85rem; font-size: 11px; color: #6c757d; }
    .cantidad-box { height: 90px; border: 2px solid #d9efff; background: #f4faff; border-radius: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 8px; text-align: center; transition: all .2s; }
    .cantidad-box:focus-within { border-color: #4DA3FF; box-shadow: 0 0 0 3px rgba(77,163,255,.10); }
    .cantidad-box .cantidad-label { font-size: 10px; color: #6c757d; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 2px; }
    .cantidad-box .cantidad-input { font-size: 26px; font-weight: 700; color: #198754; line-height: 1; background: transparent; border: none; text-align: center; width: 100%; padding: 0; outline: none; }
    .cantidad-box .cantidad-unit { font-size: 11px; color: #4DA3FF; font-weight: 600; }
    .modal-particular-min .fecha-input { font-size: 14px !important; font-weight: 600; }
    .busqueda-superior .input-group { border-radius: 8px; overflow: hidden; }
    .busqueda-superior .input-group .form-control { border-right: none; }
    .busqueda-superior .btn { width: 45px; border-radius: 0; }
    .superior-selected { background: #f8f9fa !important; }
    .tabla-superior { border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden; max-height: 145px; overflow-y: auto; margin-top: 9px; }
    .tabla-superior table { margin-bottom: 0; font-size: 12px; }
    .tabla-superior thead th { position: sticky; top: 0; z-index: 1; background: #f8f9fa; color: #6c757d; font-size: 10px; font-weight: 600; border-bottom: 1px solid #e9ecef; }
    .tabla-superior tbody td { padding: 7px 10px; }
    .modal-particular-min .modal-footer { background: #fafafa; border-top: 1px solid #e9ecef; padding: 12px 20px; }
    .modal-particular-min .modal-footer .btn { border-radius: 7px; font-size: 12px; padding: 7px 15px; }
    .btn-guardar-particular { background: #4DA3FF; border-color: #4DA3FF; color: #fff; }
    .btn-guardar-particular:hover { background: #318fe8; border-color: #318fe8; color: #fff; }
    .btn-guardar-particular:disabled { background: #b8d9ff; border-color: #b8d9ff; cursor: not-allowed; }
    @media (max-width: 767px) {
        .modal-particular-min { width: 96%; max-width: 96%; }
        .modal-particular-min .modal-body { padding: 16px; }
        .modal-particular-min .modal-header { padding: 15px 17px; }
        .modal-particular-min .modal-title { font-size: 15px; }
        .cantidad-box { height: 70px; }
        .cantidad-box .cantidad-input { font-size: 22px; }
    }
</style>

<div class="modal fade" id="modalParticular" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-particular-min">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-bg"></div>
                <div class="header-overlay"></div>
                <div class="header-content">
                    <div class="header-icon"><i class="fas fa-door-open"></i></div>
                    <div>
                        <h5 class="modal-title" id="modalTitle">Registrar Salida Particular</h5>
                        <div class="modal-subtitle">Complete los datos de su permiso</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formParticular">
                    @csrf
                    <input type="hidden" name="_method" id="methodField" value="POST">
                    <input type="hidden" name="id_editar" id="idEditar" value="">
                    <input type="hidden" id="idserv" value="{{ $persona->id }}">

                    <div id="avisoValidacion" class="alert d-none mb-3 py-2 small" role="alert"></div>

                    <div class="form-section">
                        <div class="section-title"><i class="fas fa-user"></i> Información del servidor</div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="nomb" disabled placeholder=" "
                                        value="{{ $persona->nombre ?? '' }} {{ $persona->apellidoPat ?? '' }} {{ $persona->apellidoMat ?? '' }}">
                                    <label>Servidor público</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select" id="tipoSal" name="tipoSal">
                                        @foreach ($tiposHijos as $hijo)
                                            <option value="{{ $hijo->id }}">{{ $hijo->descripcion }}</option>
                                        @endforeach
                                    </select>
                                    <label>Tipo de salida (*)</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="fechasol" name="fechasol" readonly>
                                    <label>Fecha de solicitud</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title"><i class="fas fa-clock"></i> Período del permiso</div>

                        <div id="selectorModo" class="mb-3">
                            <label class="form-label d-block mb-1">Modalidad del permiso</label>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Modalidad">
                                <input type="radio" class="btn-check" name="modoPermiso" id="modoDias" value="dias" checked>
                                <label class="btn btn-outline-primary" for="modoDias">
                                    <i class="fas fa-calendar-day me-1"></i> Días completos
                                </label>
                                <input type="radio" class="btn-check" name="modoPermiso" id="modoHoras" value="horas">
                                <label class="btn btn-outline-primary" for="modoHoras">
                                    <i class="fas fa-clock me-1"></i> Horas específicas
                                </label>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-3" id="grupoFsalida">
                                <div class="form-floating">
                                    <input type="text" class="form-control fecha-input" id="fsalida" name="fsalida" placeholder=" ">
                                    <label>Fecha de salida (*)</label>
                                </div>
                            </div>
                            <div class="col-md-3" id="grupoHorasal">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="horasal" name="horasal" placeholder=" ">
                                    <label>Hora de salida (*)</label>
                                </div>
                            </div>
                            <div class="col-md-3" id="grupoFretorno">
                                <div class="form-floating">
                                    <input type="text" class="form-control fecha-input" id="fretorno" name="fretorno" placeholder=" ">
                                    <label>Fecha de retorno (*)</label>
                                </div>
                            </div>
                            <div class="col-md-3" id="grupoHoraret">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="horaret" name="horaret" placeholder=" ">
                                    <label>Hora de retorno (*)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title"><i class="fas fa-calculator"></i> Cantidad y motivo</div>
                        <div class="row g-2 align-items-start">
                            <div class="col-md-3">
                                <div class="cantidad-box">
                                    <span class="cantidad-label">Cantidad solicitada</span>
                                    <input type="text" class="cantidad-input" id="cantidad" name="cantidad" readonly tabindex="-1" value="">
                                    <span class="cantidad-unit" id="cantidadUnit">días / horas</span>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 10px; text-align:center">
                                    Calculado según fechas, horas y tipo
                                </small>
                            </div>
                            <div class="col-md-9">
                                <div class="form-floating">
                                    <textarea class="form-control" id="motivo" name="motivo" style="min-height: 90px; height: 90px;" placeholder=" "></textarea>
                                    <label>Motivo / Sustento legal (*)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section mb-0">
                        <div class="section-title"><i class="fas fa-user-tie"></i> Inmediato superior</div>
                        <div class="row g-2">
                            <div class="col-md-7 busqueda-superior">
                                <label class="form-label">Buscar por nombre o apellido</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dato" placeholder="Ej. Juan Pérez...">
                                    <button class="btn btn-outline-primary" type="button" id="btnBuscarSuperior">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Superior seleccionado</label>
                                <input type="hidden" id="idSup" name="idSup">
                                <input type="text" class="form-control superior-selected" id="nombreSup" readonly placeholder="Ninguno seleccionado">
                            </div>
                        </div>
                        <div class="tabla-superior">
                            <div id="resultadosSuperior">
                                <p class="text-center text-muted py-3 mb-0">
                                    <i class="fas fa-search me-1"></i> Busque un superior para continuar
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-guardar-particular" id="btnGuardar" form="formParticular">
                    <i class="fa-solid fa-floppy-disk me-1"></i> <span id="btnGuardarText">Guardar</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // 1. REFERENCIAS DOM
    // ============================================================
    const form           = document.getElementById('formParticular');
    const tipoSalSelect  = document.getElementById('tipoSal');
    const motivoTextarea = document.getElementById('motivo');
    const fsalidaInput   = document.getElementById('fsalida');
    const fretornoInput  = document.getElementById('fretorno');
    const horasalInput   = document.getElementById('horasal');
    const horaretInput   = document.getElementById('horaret');
    const cantidadInput  = document.getElementById('cantidad');
    const cantidadUnit   = document.getElementById('cantidadUnit');
    const btnGuardar     = document.getElementById('btnGuardar');
    const avisoBox       = document.getElementById('avisoValidacion');

    const grupoHorasal  = document.getElementById('grupoHorasal');
    const grupoHoraret  = document.getElementById('grupoHoraret');
    const grupoFretorno = document.getElementById('grupoFretorno');
    const selectorModo  = document.getElementById('selectorModo');

    const radiosModo    = document.querySelectorAll('input[name="modoPermiso"]');
    const radioDias     = document.getElementById('modoDias');
    const radioHoras    = document.getElementById('modoHoras');

    // Regla: 8 horas = 1 día
    const HORAS_POR_DIA = 8;

    // ============================================================
    // 2. CONFIGURACIÓN INICIAL
    // ============================================================
    const hoyStr = new Date().toISOString().split('T')[0];
    document.getElementById('fechasol').value = hoyStr;

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

    const pickerSalida = flatpickr("#fsalida", {
        minDate: "today",
        disable: [deshabilitarFechas],
        locale: "es",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-m-Y",
        allowInput: true,
    });

    const pickerRetorno = flatpickr("#fretorno", {
        minDate: "today",
        disable: [deshabilitarFechas],
        locale: "es",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-m-Y",
        allowInput: true,
    });

    // ============================================================
    // 3. HELPERS
    // ============================================================
    function mostrarAviso(msg, tipo = 'warning') {
        if (!avisoBox) return;
        avisoBox.className = `alert alert-${tipo} mb-3 py-2 small`;
        avisoBox.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> ${msg}`;
        avisoBox.classList.remove('d-none');
    }

    function ocultarAviso() {
        if (avisoBox) avisoBox.classList.add('d-none');
    }

    function getModoActual() {
        const unidad = tipoSalSelect.dataset.unidad || 'dias';
        if (unidad === 'horas') return 'horas';
        const checked = document.querySelector('input[name="modoPermiso"]:checked');
        return checked ? checked.value : 'dias';
    }

    // ============================================================
    // 4. CONFIGURAR CAMPOS SEGÚN TIPO + MODO
    // ============================================================
    function configurarCamposPorTipo() {
        const unidad = tipoSalSelect.dataset.unidad || 'dias';
        const maxDias = parseInt(tipoSalSelect.dataset.maxDias) || 0;

        // Selector de modalidad
        if (unidad === 'horas') {
            radioHoras.checked = true;
            selectorModo.style.display = 'none';
        } else if (unidad === 'mixto') {
            if (!document.querySelector('input[name="modoPermiso"]:checked')) {
                radioHoras.checked = true;
            }
            selectorModo.style.display = '';
        } else {
            selectorModo.style.display = '';
        }

        // Mostrar/ocultar horas según modo
        const modo = getModoActual();
        if (modo === 'horas') {
            grupoHorasal.style.display = '';
            grupoHoraret.style.display = '';
        } else {
            grupoHorasal.style.display = 'none';
            grupoHoraret.style.display = 'none';
            // NO tocamos los valores: quedan vacíos por defecto
        }

        // Etiqueta del contador
        if (unidad === 'horas') {
            cantidadUnit.textContent = 'horas';
        } else {
            cantidadUnit.textContent = 'días';
        }

        // Limitar fecha retorno
        if (maxDias > 0 && fsalidaInput.value) {
            const maxFecha = new Date(fsalidaInput.value);
            maxFecha.setDate(maxFecha.getDate() + maxDias - 1);
            pickerRetorno.set('maxDate', maxFecha);
        } else {
            pickerRetorno.set('maxDate', null);
        }
    }

    // ============================================================
    // 5. CALCULAR Y VALIDAR
    // ============================================================
    function calcularCantidad() {
        const fsalida  = fsalidaInput.value;
        const fretorno = fretornoInput.value || fsalidaInput.value;

        if (!fsalida) {
            cantidadInput.value = '';
            ocultarAviso();
            btnGuardar.disabled = false;
            return;
        }

        const modo = getModoActual();

        // Valores efectivos SOLO para el cálculo (no se escriben en los inputs)
        let horasalEf, horaretEf;
        if (modo === 'horas') {
            horasalEf = horasalInput.value || '00:00';
            horaretEf = horaretInput.value || '23:59';
        } else {
            horasalEf = '00:00';
            horaretEf = '23:59';
        }

        const salida  = new Date(`${fsalida}T${horasalEf}`);
        const retorno = new Date(`${fretorno}T${horaretEf}`);

        if (retorno < salida) {
            cantidadInput.value = '';
            mostrarAviso('La fecha/hora de retorno no puede ser anterior a la de salida.', 'danger');
            btnGuardar.disabled = true;
            return;
        }

        const diffHoras = (retorno - salida) / (1000 * 60 * 60);
        const diffDias  = diffHoras / 24;

        const unidad    = tipoSalSelect.dataset.unidad || 'dias';
        const maxUnidad = parseFloat(tipoSalSelect.dataset.maxUnidad) || 0;
        const maxDias   = parseInt(tipoSalSelect.dataset.maxDias) || 0;

        let aviso    = '';
        let cantidad = 0;

        // ============== MODO HORAS ==============
        if (modo === 'horas') {
            if (unidad === 'horas') {
                // Tipo puro de horas → se queda en horas
                cantidad = diffHoras;

                if (maxUnidad > 0 && diffHoras > maxUnidad) {
                    aviso = `Este tipo permite máximo <b>${maxUnidad} hora(s)</b>. Ingresaste ${diffHoras.toFixed(1)}.`;
                }
                if (!aviso && maxDias > 0 && diffDias > maxDias) {
                    aviso = `Máximo <b>${maxDias} día(s)</b> de rango. Ingresaste ${diffDias.toFixed(2)}.`;
                }
            } else {
                // Tipo dias/mixto + usuario eligió horas → convertir
                // 8 horas = 1 día
                cantidad = diffHoras / HORAS_POR_DIA;

                if (maxUnidad > 0 && cantidad > maxUnidad) {
                    aviso = `Este tipo permite máximo <b>${maxUnidad} día(s)</b>. ` +
                            `${diffHoras.toFixed(1)} h equivalen a ${cantidad.toFixed(2)} día(s).`;
                }
            }
        // ============== MODO DÍAS ==============
        } else {
            cantidad = diffDias;

            if (maxUnidad > 0 && diffDias > maxUnidad) {
                aviso = `Este tipo permite máximo <b>${maxUnidad} día(s)</b>. Ingresaste ${diffDias.toFixed(2)}.`;
            }
        }

        cantidadInput.value = cantidad.toFixed(2);

        if (aviso) {
            mostrarAviso(aviso, 'warning');
            btnGuardar.disabled = true;
        } else {
            ocultarAviso();
            btnGuardar.disabled = false;
        }
    }

    // ============================================================
    // 6. EVENTOS
    // ============================================================
    [fsalidaInput, fretornoInput, horasalInput, horaretInput].forEach(input => {
        input.addEventListener('change', calcularCantidad);
        input.addEventListener('keyup', calcularCantidad);
    });

    fsalidaInput.addEventListener('change', () => {
        configurarCamposPorTipo();
        calcularCantidad();
    });

    radiosModo.forEach(r => r.addEventListener('change', () => {
        configurarCamposPorTipo();
        calcularCantidad();
    }));

    // ============================================================
    // 7. CAMBIO DE TIPO DE SALIDA
    // ============================================================
    tipoSalSelect.addEventListener('change', function () {
        const tipoId = this.value;
        if (!tipoId) return;

        axios.get(`/tiposalida/${tipoId}`)
            .then(response => {
                const d = response.data;

                const unidadRaw = (d.unidad || d.tipo_medida || 'dias').toString().toLowerCase().trim();
                const unidad    = ['dias','horas','mixto'].includes(unidadRaw) ? unidadRaw : 'dias';

                motivoTextarea.value = d.sustLegal || '';
                tipoSalSelect.dataset.unidad    = unidad;
                tipoSalSelect.dataset.maxUnidad = d.max_unidad || d.cantidad_default || 0;
                tipoSalSelect.dataset.maxDias   = d.max_dias || (unidad === 'horas' ? 2 : 0);

                if (unidad === 'horas') {
                    radioHoras.checked = true;
                } else if (unidad === 'mixto') {
                    radioHoras.checked = true;
                } else {
                    radioDias.checked = true;
                }

                configurarCamposPorTipo();
                calcularCantidad();
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'No se pudo cargar la configuración del tipo', 'error');
            });
    });

    if (tipoSalSelect.value) {
        tipoSalSelect.dispatchEvent(new Event('change'));
    }

    // ============================================================
    // 8. BUSCAR SUPERIOR
    // ============================================================
    function buscarSuperior() {
        const dato = document.getElementById('dato').value.trim();
        if (!dato) {
            Swal.fire('Advertencia', 'Debe ingresar un nombre o apellido', 'warning');
            return;
        }

        const contenedor = document.getElementById('resultadosSuperior');
        contenedor.innerHTML = '<p class="text-muted">Buscando...</p>';

        axios.get('/vacacion/buscar-superior', { params: { buscar: dato } })
            .then(response => {
                const data = response.data;
                if (data.length === 0) {
                    contenedor.innerHTML = '<p class="text-muted">No se encontraron resultados</p>';
                    return;
                }
                let html = `<table class="table table-sm">
                            <thead><tr><th>Nombre</th><th>Acción</th></tr></thead>
                            <tbody>`;
                data.forEach(p => {
                    const nombre = `${p.nombre} ${p.apellidoPat} ${p.apellidoMat}`;
                    html += `<tr>
                                <td>${nombre}</td>
                                <td>
                                    <button class="btn btn-success btn-sm asignar-sup" data-id="${p.id}" data-nombre="${nombre}">
                                        <i class="fa-solid fa-angles-right"></i>
                                    </button>
                                </td>
                            </tr>`;
                });
                html += '</tbody></table>';
                contenedor.innerHTML = html;

                document.querySelectorAll('.asignar-sup').forEach(btn => {
                    btn.addEventListener('click', function () {
                        document.getElementById('idSup').value = this.dataset.id;
                        document.getElementById('nombreSup').value = this.dataset.nombre;
                        contenedor.innerHTML = '';
                        document.getElementById('dato').value = '';
                    });
                });
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
                contenedor.innerHTML = '<p class="text-danger">Error en la búsqueda</p>';
            });
    }

    document.getElementById('btnBuscarSuperior').addEventListener('click', buscarSuperior);
    document.getElementById('dato').addEventListener('keyup', function (e) {
        if (e.key === 'Enter') buscarSuperior();
    });

    // ============================================================
    // 9. ENVÍO DEL FORMULARIO
    // ============================================================
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (btnGuardar.disabled) {
            Swal.fire('Validación', 'Corrija los errores antes de continuar', 'warning');
            return;
        }

        const idserv   = document.getElementById('idserv').value;
        const tipoSal  = tipoSalSelect.value;
        const fechasol = document.getElementById('fechasol').value;
        const motivo   = motivoTextarea.value;
        const fsalida  = fsalidaInput.value;
        const fretorno = fretornoInput.value;
        const idSup    = document.getElementById('idSup').value;
        const cantidad = cantidadInput.value;
        const modo     = getModoActual();

        // En modo "dias" enviamos horas vacías; en modo "horas" enviamos lo que el usuario haya escrito (o vacío)
        const horasal = modo === 'horas' ? (horasalInput.value || '') : '';
        const horaret = modo === 'horas' ? (horaretInput.value || '') : '';

        if (!idserv || !tipoSal || !fechasol || !motivo || !fsalida || !fretorno || !idSup || !cantidad) {
            Swal.fire('Faltan datos', 'Complete todos los campos obligatorios (*)', 'warning');
            return;
        }

        // En modo horas, exigir que el usuario escriba ambas horas
        if (modo === 'horas' && (!horasal || !horaret)) {
            Swal.fire('Faltan horas', 'Debe indicar hora de salida y hora de retorno.', 'warning');
            return;
        }

        if (isNaN(parseFloat(cantidad)) || parseFloat(cantidad) <= 0) {
            Swal.fire('Error', 'La cantidad calculada no es válida', 'error');
            return;
        }

        const data = {
            persona_id: idserv,
            tipoSal: tipoSal,
            fechasol: fechasol,
            fsalida: fsalida,
            horasal: horasal,
            fretorno: fretorno,
            horaret: horaret,
            motivo: motivo,
            cantidad: cantidad,
            idSup: idSup,
            modo: modo,
        };

        const method   = document.getElementById('methodField').value;
        const idEditar = document.getElementById('idEditar').value;
        const url = method === 'PUT'
            ? `/salidas/particulares/${idEditar}`
            : '/particular/registrar';

        Swal.fire({
            title: 'Procesando...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        axios({
            method: method === 'PUT' ? 'PUT' : 'POST',
            url: url,
            data: data,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            Swal.close();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: response.data.message || 'Operación exitosa',
                timer: 1500,
                showConfirmButton: false,
                timerProgressBar: true
            }).then(() => {
                cargarSalidas();
                bootstrap.Modal.getInstance(document.getElementById('modalParticular')).hide();
            });
        })
        .catch(error => {
            Swal.close();
            let mensaje = 'Ocurrió un error al procesar la solicitud.';
            if (error.response) {
                const d = error.response.data;
                if (d.errors) {
                    mensaje = Object.values(d.errors).flat().join('<br>');
                } else if (d.message) {
                    mensaje = d.message;
                } else if (d.error) {
                    mensaje = d.error;
                }
            }
            Swal.fire('Error', mensaje, 'error');
        });
    });

    // ============================================================
    // 10. BOTÓN "NUEVO"
    // ============================================================
    document.getElementById('btnNuevoParticular').addEventListener('click', function () {
        form.reset();
        document.getElementById('methodField').value = 'POST';
        document.getElementById('idEditar').value = '';
        document.getElementById('modalTitle').textContent = 'Registrar Salida Particular';
        btnGuardar.innerHTML = '<i class="fa-solid fa-floppy-disk me-1"></i> <span id="btnGuardarText">Guardar</span>';
        document.getElementById('nombreSup').value = '';
        document.getElementById('idSup').value = '';
        document.getElementById('resultadosSuperior').innerHTML =
            '<p class="text-center text-muted py-3 mb-0"><i class="fas fa-search me-1"></i> Busque un superior para continuar</p>';
        cantidadInput.value = '';
        document.getElementById('fechasol').value = hoyStr;

        // Asegurar horas vacías
        horasalInput.value = '';
        horaretInput.value = '';

        ocultarAviso();
        btnGuardar.disabled = false;

        pickerSalida.setDate(null);
        pickerRetorno.setDate(null);
        pickerRetorno.set('maxDate', null);

        radioDias.checked = true;

        if (tipoSalSelect.value) {
            tipoSalSelect.dispatchEvent(new Event('change'));
        }

        new bootstrap.Modal(document.getElementById('modalParticular')).show();
    });

    // ============================================================
    // 11. EDITAR
    // ============================================================
    function bindEditar() {
        document.querySelectorAll('.btnEditar').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                axios.get(`/particular/${id}/edit`)
                    .then(response => {
                        const data = response.data;

                        tipoSalSelect.value = data.tiposalida_id;
                        document.getElementById('fechasol').value = data.fechasol;
                        motivoTextarea.value = data.motivo;
                        fsalidaInput.value   = data.fechasal;
                        fretornoInput.value  = data.fecharet;
                        cantidadInput.value  = data.cantidad;

                        // Detectar modo: si las horas vienen vacías o son 00:00/23:59 → días
                        const h1 = (data.horasal || '').substring(0, 5);
                        const h2 = (data.horaret || '').substring(0, 5);

                        if (!h1 || !h2 || (h1 === '00:00' && h2 === '23:59')) {
                            radioDias.checked = true;
                            horasalInput.value = '';
                            horaretInput.value = '';
                        } else {
                            radioHoras.checked = true;
                            horasalInput.value = data.horasal;
                            horaretInput.value = data.horaret;
                        }

                        if (data.jefe) {
                            document.getElementById('idSup').value = data.jefe_id;
                            document.getElementById('nombreSup').value =
                                data.jefe.nombre + ' ' + data.jefe.apellidoPat;
                        }

                        document.getElementById('methodField').value = 'PUT';
                        document.getElementById('idEditar').value = data.id;
                        document.getElementById('modalTitle').textContent = 'Editar Salida Particular';
                        btnGuardar.innerHTML = '<i class="fa-solid fa-pen-to-square me-1"></i> <span id="btnGuardarText">Actualizar</span>';

                        tipoSalSelect.dispatchEvent(new Event('change'));

                        pickerSalida.setDate(data.fechasal, false);
                        pickerRetorno.setDate(data.fecharet, false);

                        ocultarAviso();
                        btnGuardar.disabled = false;

                        new bootstrap.Modal(document.getElementById('modalParticular')).show();
                    })
                    .catch(error => {
                        console.error('Error al cargar datos:', error);
                        Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
                    });
            });
        });
    }

    // ============================================================
    // 12. ELIMINAR
    // ============================================================
    function bindEliminar() {
        document.querySelectorAll('.btnEliminar').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (result.isConfirmed) {
                        axios.delete(`/salidas/particulares/${id}`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: response.data.message || 'Eliminado correctamente',
                                timer: 1500,
                                showConfirmButton: false,
                                timerProgressBar: true
                            });
                            cargarSalidas();
                        })
                        .catch(error => {
                            const msg = error.response?.data?.error || 'Error al eliminar';
                            Swal.fire('Error', msg, 'error');
                        });
                    }
                });
            });
        });
    }

    // ============================================================
    // 13. RECARGAR TABLA
    // ============================================================
    function cargarSalidas() {
        axios.get('/particular/mis-salidas')
            .then(res => {
                if (!res.data.success) {
                    Swal.fire('Error', res.data.message, 'error');
                    return;
                }

                const data = res.data.data;
                const tbody = document.getElementById('tbodySalidas');
                if (!tbody) return;

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay salidas particulares registradas.</td></tr>';
                    return;
                }

                let html = '';
                data.forEach((sol, index) => {
                    const estadoJefe = (sol.estado_jefe || '').toLowerCase();
                    const estadoRRHH = (sol.estado_rrhh || '').toLowerCase();

                    let badgeJefe = 'bg-secondary', textoJefe = 'Sin estado', iconoJefe = 'fa-question-circle';
                    if (estadoJefe === 'pendiente')      { badgeJefe = 'bg-warning text-dark'; textoJefe = 'Pendiente';  iconoJefe = 'fa-clock'; }
                    else if (estadoJefe === 'aprobado')  { badgeJefe = 'bg-success';            textoJefe = 'Aprobado';   iconoJefe = 'fa-check-circle'; }
                    else if (estadoJefe === 'rechazado') { badgeJefe = 'bg-danger';             textoJefe = 'Rechazado';  iconoJefe = 'fa-times-circle'; }
                    else if (!estadoJefe)                { badgeJefe = 'bg-light text-dark border'; textoJefe = 'Sin asignar'; iconoJefe = 'fa-user-slash'; }

                    const nombreJefe = sol.jefe_nombre && sol.jefe_apellido_pat
                        ? `${sol.jefe_nombre} ${sol.jefe_apellido_pat}` : null;

                    const htmlJefe = nombreJefe
                        ? `<i class="fas fa-user-tie me-1 text-muted"></i>${nombreJefe}
                           ${sol.fecha_aprobacion_jefe ? `<br><span class="text-muted" style="font-size:0.75rem;"><i class="far fa-calendar-alt me-1"></i>${sol.fecha_aprobacion_jefe}</span>` : ''}`
                        : `<span class="text-muted fst-italic" style="font-size:0.8rem;"><i class="fas fa-user-slash me-1"></i>Sin jefe asignado</span>`;

                    const rrhhPuedeActuar = estadoJefe === 'aprobado';
                    const rrhhNoAplica    = estadoJefe === 'rechazado' || !estadoJefe;
                    let badgeRRHH = 'bg-secondary', textoRRHH = 'Desconocido', iconoRRHH = 'fa-question-circle';
                    if (rrhhNoAplica)                          { badgeRRHH = 'bg-light text-muted border'; textoRRHH = 'N/A';         iconoRRHH = 'fa-minus-circle'; }
                    else if (!rrhhPuedeActuar)                 { badgeRRHH = 'bg-light text-dark border';  textoRRHH = 'En espera';   iconoRRHH = 'fa-hourglass-half'; }
                    else if (estadoRRHH === 'pendiente')       { badgeRRHH = 'bg-warning text-dark';       textoRRHH = 'Pendiente';   iconoRRHH = 'fa-clock'; }
                    else if (estadoRRHH === 'aprobado')        { badgeRRHH = 'bg-success';                 textoRRHH = 'Aprobado';    iconoRRHH = 'fa-check-circle'; }
                    else if (estadoRRHH === 'rechazado')       { badgeRRHH = 'bg-danger';                  textoRRHH = 'Rechazado';   iconoRRHH = 'fa-times-circle'; }

                    const nombreRRHH = sol.rrhh_nombre && sol.rrhh_apellido_pat
                        ? `${sol.rrhh_nombre} ${sol.rrhh_apellido_pat}` : null;

                    let htmlRRHH = '';
                    if (rrhhPuedeActuar && !rrhhNoAplica) {
                        htmlRRHH = `<i class="fas fa-user-shield me-1 text-muted"></i>${nombreRRHH}
                            ${sol.fecha_aprobacion_rrhh ? `<br><span class="text-muted" style="font-size:0.75rem;"><i class="far fa-calendar-alt me-1"></i>${sol.fecha_aprobacion_rrhh}</span>` : ''}`;
                    } else if (rrhhNoAplica) {
                        htmlRRHH = `<span class="text-muted fst-italic" style="font-size:0.8rem;">No aplica por estado del jefe</span>`;
                    } else {
                        htmlRRHH = `<span class="text-muted fst-italic" style="font-size:0.8rem;">Esperando aprobación del jefe</span>`;
                    }

                    const editable = !estadoJefe || estadoJefe === 'pendiente';
                    const pdfDisponible = estadoJefe === 'aprobado';
                    let acciones = '';
                    if (editable) {
                        acciones = `
                            <button class="btn btn-sm btn-info btnEditar" data-id="${sol.id}"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger btnEliminar" data-id="${sol.id}"><i class="fa fa-trash"></i></button>
                        `;
                    } else {
                        const pdfBtn = pdfDisponible
                            ? `<a href="/salidas/particulares/pdf/${sol.id}" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-file-pdf"></i> PDF</a>`
                            : '';
                        acciones = `
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-muted me-1" style="font-size:0.8rem;">No editable</span>
                                ${pdfBtn}
                            </div>
                        `;
                    }

                    html += `
                        <tr id="fila-${sol.id}">
                            <td>${index + 1}</td>
                            <td>${sol.fechasol || ''}</td>
                            <td>${sol.fechasal || ''}</td>
                            <td>${sol.fecharet || ''}</td>
                            <td>${sol.cantidad || ''}</td>
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
                bindEditar();
                bindEliminar();
            })
            .catch(err => {
                console.error(err.response?.data);
                Swal.fire('Error', err.response?.data?.message || 'No se pudo cargar la lista de salidas', 'error');
            });
    }

    bindEditar();
    bindEliminar();
});
</script>

@endpush