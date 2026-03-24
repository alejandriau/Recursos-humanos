<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h6>Información del Préstamo</h6>
            <table class="table table-sm">
                <tr>
                    <th>ID:</th>
                    <td>{{ $prestamo->id }}</td>
                </tr>
                <tr>
                    <th>Estado:</th>
                    <td>
                        <span class="badge bg-{{ 
                            $prestamo->estado == 'pendiente' ? 'warning' : 
                            ($prestamo->estado == 'aprobado' ? 'info' : 
                            ($prestamo->estado == 'prestado' ? 'success' : 
                            ($prestamo->estado == 'devuelto' ? 'secondary' : 'danger'))) 
                        }}">
                            {{ ucfirst($prestamo->estado) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Fecha Solicitud:</th>
                    <td>{{ $prestamo->fecha_solicitud }}</td>
                </tr>
                <tr>
                    <th>Fecha Préstamo:</th>
                    <td>{{ $prestamo->fecha_prestamo ?? 'Pendiente' }}</td>
                </tr>
                <tr>
                    <th>Devolución Estimada:</th>
                    <td>{{ $prestamo->fecha_devolucion_estimada ?? 'No definida' }}</td>
                </tr>
                <tr>
                    <th>Devolución Real:</th>
                    <td>{{ $prestamo->fecha_devolucion_real ?? 'Pendiente' }}</td>
                </tr>
                <tr>
                    <th>Tipo:</th>
                    <td>{{ $prestamo->es_verbal ? 'Verbal' : 'Formal' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6>Información de la Carpeta</h6>
            <table class="table table-sm">
                <tr>
                    <th>Código:</th>
                    <td>{{ $prestamo->carpeta_data->letra ?? '' }} {{ $prestamo->carpeta_data->codigo ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Nombre:</th>
                    <td>{{ $prestamo->carpeta_data->nombrecompleto ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Observaciones:</th>
                    <td>{{ $prestamo->carpeta_data->observacion ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-12">
            <h6>Información del Solicitante</h6>
            <table class="table table-sm">
                <tr>
                    <th>Nombre:</th>
                    <td>{{ $prestamo->solicitante->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td>{{ $prestamo->solicitante->email ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    @if($prestamo->motivo_solicitud)
    <div class="row mt-3">
        <div class="col-12">
            <h6>Motivo de la Solicitud</h6>
            <p>{{ $prestamo->motivo_solicitud }}</p>
        </div>
    </div>
    @endif
    
    @if($prestamo->motivo_rechazo)
    <div class="row mt-3">
        <div class="col-12">
            <h6>Motivo del Rechazo</h6>
            <p class="text-danger">{{ $prestamo->motivo_rechazo }}</p>
        </div>
    </div>
    @endif
    
    @if($prestamo->notas_archivero)
    <div class="row mt-3">
        <div class="col-12">
            <h6>Notas del Archivero</h6>
            <p>{{ $prestamo->notas_archivero }}</p>
        </div>
    </div>
    @endif
</div>