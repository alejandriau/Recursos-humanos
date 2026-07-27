{{-- resources/views/admin/solicitudes/ver-solicitud.blade.php --}}
@php
    use Carbon\Carbon;
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Estado de la solicitud -->
            <div class="alert {{ $solicitud->estado_rrhh == 'pendiente' ? 'alert-warning' : ($solicitud->estado_rrhh == 'aprobado' ? 'alert-success' : 'alert-danger') }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas {{ $solicitud->estado_rrhh == 'pendiente' ? 'fa-clock' : ($solicitud->estado_rrhh == 'aprobado' ? 'fa-check-circle' : 'fa-times-circle') }}"></i>
                        <strong>Estado:</strong>
                        {{ ucfirst($solicitud->estado_rrhh) }}
                        @if($solicitud->estado_rrhh == 'aprobado')
                            <span class="badge bg-success ms-2">Días descontados</span>
                        @endif
                    </div>
                    <div>
                        <small class="text-muted">Solicitud #{{ $solicitud->id }}</small>
                    </div>
                </div>
            </div>

            <!-- Información del Solicitante -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-user"></i> Información del Solicitante</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nombre:</strong> {{ $solicitud->persona->nombre ?? 'N/A' }} {{ $solicitud->persona->apellido ?? '' }}</p>
                            <p><strong>CI:</strong> {{ $solicitud->persona->ci ?? 'N/A' }}</p>
                            <p><strong>Cargo:</strong> {{ $solicitud->persona->cargo ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Unidad:</strong> {{ $solicitud->persona->unidad ?? 'N/A' }}</p>
                            <p><strong>Fecha de Ingreso:</strong> {{ $solicitud->persona->fechaIngreso ? Carbon::parse($solicitud->persona->fechaIngreso)->format('d/m/Y') : 'N/A' }}</p>
                            <p><strong>Jefe Inmediato:</strong> {{ $solicitud->jefe->nombre ?? 'N/A' }} {{ $solicitud->jefe->apellido ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de la Solicitud -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Detalle de la Solicitud</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Tipo de Salida:</strong>
                                <span class="badge {{ ($solicitud->tiposalida->descripcion ?? '') == 'Vacación' ? 'bg-info' : 'bg-secondary' }}">
                                    {{ $solicitud->tiposalida->descripcion ?? 'N/A' }}
                                </span>
                            </p>
                            <p><strong>Fecha Solicitud:</strong> {{ Carbon::parse($solicitud->fechasol)->format('d/m/Y H:i') }}</p>
                            <p><strong>Fecha Salida:</strong> {{ Carbon::parse($solicitud->fechasal)->format('d/m/Y') }}</p>
                            <p><strong>Fecha Retorno:</strong> {{ Carbon::parse($solicitud->fecharet)->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Días Solicitados:</strong> <span class="badge bg-primary">{{ $solicitud->cantidad }}</span></p>
                            @if(isset($periodo) && $periodo)
                                <p><strong>Período de Vacación:</strong> #{{ $periodo->numero_periodo ?? 'N/A' }}</p>
                                <p><strong>Antigüedad:</strong> {{ $periodo->anios_antiguedad ?? 0 }} años</p>
                                <p><strong>Días Asignados:</strong> {{ $periodo->dias_asignados ?? 0 }}</p>
                            @endif
                        </div>
                        <div class="col-md-4">
                            @if(isset($periodo) && $periodo)
                                <p><strong>Días Usados:</strong> {{ $periodo->dias_usados ?? 0 }}</p>
                                <p><strong>Saldo Anterior:</strong> {{ ($periodo->saldo_disponible ?? 0) + ($solicitud->cantidad ?? 0) }}</p>
                                <p><strong>Saldo Disponible:</strong>
                                    <span class="badge {{ ($periodo->saldo_disponible ?? 0) > 10 ? 'bg-success' : (($periodo->saldo_disponible ?? 0) > 5 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $periodo->saldo_disponible ?? 0 }}
                                    </span>
                                </p>
                            @endif
                            <p><strong>Estado del Jefe:</strong>
                                <span class="badge {{ ($solicitud->estado_jefe ?? '') == 'aprobado' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($solicitud->estado_jefe ?? 'pendiente') }}
                                </span>
                            </p>
                            @if($solicitud->observacion_jefe)
                                <p><strong>Obs. Jefe:</strong> {{ $solicitud->observacion_jefe }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Movimientos -->
            @if(isset($movimientos) && $movimientos && $movimientos->count() > 0)
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-history"></i> Historial de Movimientos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Saldo Anterior</th>
                                    <th>Saldo Posterior</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movimientos as $movimiento)
                                <tr>
                                    <td>{{ Carbon::parse($movimiento->fecha)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge {{ $movimiento->tipo == 'credito' ? 'bg-success' : ($movimiento->tipo == 'debito' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ ucfirst($movimiento->tipo) }}
                                        </span>
                                    </td>
                                    <td>{{ $movimiento->cantidad }}</td>
                                    <td>{{ $movimiento->saldo_anterior }}</td>
                                    <td>{{ $movimiento->saldo_posterior }}</td>
                                    <td><small>{{ $movimiento->descripcion }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Observaciones -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-comments"></i> Observaciones</h6>
                </div>
                <div class="card-body">
                    @if($solicitud->observacion)
                        <p><strong>Solicitante:</strong> {{ $solicitud->observacion }}</p>
                    @endif
                    @if($solicitud->observacion_rrhh)
                        <p><strong>RRHH:</strong> {{ $solicitud->observacion_rrhh }}</p>
                    @endif
                    @if(!$solicitud->observacion && !$solicitud->observacion_rrhh)
                        <p class="text-muted text-center">Sin observaciones</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
