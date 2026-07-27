{{-- resources/views/rrhh/partials/tab-aprobados.blade.php --}}
<div>
    @if($solicitudes->count() > 0)
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Solicitante</th>
                        <th>Tipo</th>
                        <th>Fechas</th>
                        <th>Días</th>
                        <th>Aprobado por</th>
                        <th>Fecha Aprobación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitudes as $solicitud)
                    <tr>
                        <td>
                            <strong>{{ $solicitud->persona->nombre ?? 'N/A' }} {{ $solicitud->persona->apellido ?? '' }}</strong>
                            <br>
                            <small class="text-muted">CI: {{ $solicitud->persona->ci ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $solicitud->tiposalida->descripcion == 'Vacación' ? 'bg-info' : 'bg-secondary' }}">
                                {{ $solicitud->tiposalida->descripcion ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <small>
                                {{ \Carbon\Carbon::parse($solicitud->fechasal)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($solicitud->fecharet)->format('d/m/Y') }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $solicitud->cantidad }}</span>
                        </td>
                        <td>
                            @if($solicitud->rrhh)
                                {{ $solicitud->rrhh->nombre ?? '' }} {{ $solicitud->rrhh->apellido ?? '' }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ \Carbon\Carbon::parse($solicitud->fecha_aprobacion_rrhh)->format('d/m/Y H:i') }}</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-4">
            <i class="fas fa-inbox fa-3x text-muted"></i>
            <p class="text-muted">No hay solicitudes aprobadas</p>
        </div>
    @endif
</div>
