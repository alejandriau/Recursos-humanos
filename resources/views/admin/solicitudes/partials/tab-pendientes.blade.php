{{-- resources/views/rrhh/partials/tab-pendientes.blade.php --}}
<div>
    <div class="row mb-3">
        <div class="col-md-8">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-primary active" onclick="filtrarSolicitudes('pendientes')">
                    <i class="fas fa-clock"></i> Pendientes
                </button>
                <button class="btn btn-outline-primary" onclick="filtrarSolicitudes('vacaciones')">
                    <i class="fas fa-umbrella-beach"></i> Solo Vacaciones
                </button>
                <button class="btn btn-outline-success" onclick="filtrarSolicitudes('aprobados')">
                    <i class="fas fa-check"></i> Aprobados
                </button>
                <button class="btn btn-outline-danger" onclick="filtrarSolicitudes('rechazados')">
                    <i class="fas fa-times"></i> Rechazados
                </button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nombre o CI...">
                <button class="btn btn-primary" onclick="buscarSolicitudes()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    @if($solicitudes->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover" id="solicitudesTable">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="selectAll" onchange="seleccionarTodos()">
                        </th>
                        <th>Solicitante</th>
                        <th>Tipo</th>
                        <th>Fechas</th>
                        <th>Días</th>
                        <th>Estado Jefe</th>
                        <th>Fecha Solicitud</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitudes as $solicitud)
                    <tr class="solicitud-item solicitud-pendiente">
                        <td>
                            <input type="checkbox" class="solicitud-checkbox" value="{{ $solicitud->id }}">
                        </td>
                        <td>
                            <strong>{{ $solicitud->persona->nombre ?? 'N/A' }} {{ $solicitud->persona->apellido ?? '' }}</strong>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-id-card"></i> {{ $solicitud->persona->ci ?? 'N/A' }}
                            </small>
                            @if($solicitud->persona->cargo)
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-briefcase"></i> {{ $solicitud->persona->cargo }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $solicitud->tiposalida->descripcion == 'Vacación' ? 'bg-info' : 'bg-secondary' }}">
                                {{ $solicitud->tiposalida->descripcion ?? 'N/A' }}
                                @if($solicitud->tiposalida->descripcion == 'Vacación')
                                    <i class="fas fa-umbrella-beach"></i>
                                @endif
                            </span>
                            @if($solicitud->tiposalida->descripcion == 'Vacación' && $solicitud->periodo_id)
                                <br>
                                <small class="text-muted">
                                    Período: {{ $solicitud->periodo->numero_periodo ?? 'N/A' }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <small>
                                <i class="fas fa-calendar-alt text-primary"></i>
                                {{ \Carbon\Carbon::parse($solicitud->fechasal)->format('d/m/Y') }}
                                <br>
                                <i class="fas fa-calendar-check text-success"></i>
                                {{ \Carbon\Carbon::parse($solicitud->fecharet)->format('d/m/Y') }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $solicitud->cantidad }}</span>
                            @if($solicitud->tiposalida->descripcion == 'Vacación' && $solicitud->periodo_id)
                                <br>
                                <small class="text-muted">
                                    Saldo: {{ $solicitud->periodo->saldo_disponible ?? 0 }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $solicitud->estado_jefe == 'aprobado' ? 'bg-success' : 'bg-warning' }}">
                                {{ ucfirst($solicitud->estado_jefe) }}
                            </span>
                            @if($solicitud->jefe)
                                <br>
                                <small class="text-muted">
                                    Jefe: {{ $solicitud->jefe->nombre ?? '' }} {{ $solicitud->jefe->apellido ?? '' }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <small>{{ \Carbon\Carbon::parse($solicitud->created_at)->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-success" onclick="aprobarSolicitud({{ $solicitud->id }})" title="Aprobar">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-danger" onclick="mostrarModalRechazo({{ $solicitud->id }})" title="Rechazar">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-info" onclick="verDetalle({{ $solicitud->id }})" title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

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
