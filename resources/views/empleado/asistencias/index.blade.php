@extends('dashboard')

@section('title', 'Mi Asistencia')

@section('contenido')
<div class="row">
    <div class="col-12">
        <!-- Tarjeta de Marcación del Día -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Marcación del Día - {{ \Carbon\Carbon::now()->format('d/m/Y') }}</h5>
            </div>
            <div class="card-body">
                @if($asistenciaHoy)
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="card {{ $asistenciaHoy->hora_entrada ? 'bg-success text-white' : 'bg-light' }}">
                                <div class="card-body">
                                    <h6>Entrada</h6>
                                    <h4>{{ $asistenciaHoy->hora_entrada ? \Carbon\Carbon::parse($asistenciaHoy->hora_entrada)->format('H:i') : '--:--' }}</h4>
                                    @if(!$asistenciaHoy->hora_entrada)
                                        <form action="{{ route('empleado.asistencias.marcar-entrada') }}" method="POST" class="mt-2">
                                            @csrf
                                            <input type="hidden" name="latitud" id="latitud">
                                            <input type="hidden" name="longitud" id="longitud">
                                            <button type="submit" class="btn btn-success btn-sm" onclick="obtenerUbicacion()">
                                                <i class="fas fa-sign-in-alt me-1"></i>Marcar Entrada
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Estado</h6>
                                    <span class="badge
                                        @if($asistenciaHoy->estado == 'presente') bg-success
                                        @elseif($asistenciaHoy->estado == 'tardanza') bg-warning
                                        @elseif($asistenciaHoy->estado == 'ausente') bg-danger
                                        @else bg-info @endif">
                                        {{ $asistenciaHoy->estado }}
                                    </span>
                                    @if($asistenciaHoy->minutos_retraso > 0)
                                        <p class="mt-2 mb-0 text-warning">
                                            <small>Retraso: {{ $asistenciaHoy->minutos_retraso }} min</small>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ $asistenciaHoy->hora_salida ? 'bg-info text-white' : 'bg-light' }}">
                                <div class="card-body">
                                    <h6>Salida</h6>
                                    <h4>{{ $asistenciaHoy->hora_salida ? \Carbon\Carbon::parse($asistenciaHoy->hora_salida)->format('H:i') : '--:--' }}</h4>
                                    @if($asistenciaHoy->hora_entrada && !$asistenciaHoy->hora_salida)
                                        <form action="{{ route('empleado.asistencias.marcar-salida') }}" method="POST" class="mt-2">
                                            @csrf
                                            <input type="hidden" name="latitud" id="latitud-salida">
                                            <input type="hidden" name="longitud" id="longitud-salida">
                                            <button type="submit" class="btn btn-info btn-sm" onclick="obtenerUbicacionSalida()">
                                                <i class="fas fa-sign-out-alt me-1"></i>Marcar Salida
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($asistenciaHoy->horas_extras > 0)
                        <div class="alert alert-info mt-3 text-center">
                            <i class="fas fa-star me-2"></i>
                            <strong>Horas extras:</strong> {{ $asistenciaHoy->horas_extras }} hora(s)
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <h5 class="text-muted">No has marcado asistencia hoy</h5>
                        <form action="{{ route('empleado.asistencias.marcar-entrada') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="latitud" id="latitud">
                            <input type="hidden" name="longitud" id="longitud">
                            <button type="submit" class="btn btn-success btn-lg" onclick="obtenerUbicacion()">
                                <i class="fas fa-sign-in-alt me-2"></i>Marcar Entrada
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Estadísticas del Mes -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h4>{{ $estadisticasMes['dias_presente'] }}</h4>
                        <p class="mb-0">Días Presente</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h4>{{ $estadisticasMes['dias_tardanza'] }}</h4>
                        <p class="mb-0">Días Tardanza</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body text-center">
                        <h4>{{ $estadisticasMes['dias_ausente'] }}</h4>
                        <p class="mb-0">Días Ausente</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h4>{{ $estadisticasMes['total_horas_extras'] }}</h4>
                        <p class="mb-0">Horas Extras</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('empleado.asistencias.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="mes" class="form-select">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="ano" class="form-select">
                                @for($i = date('Y') - 2; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}" {{ $ano == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Historial -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Asistencias - {{ DateTime::createFromFormat('!m', $mes)->format('F') }} {{ $ano }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Día</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Horas Trab.</th>
                                <th>Estado</th>
                                <th>Retraso</th>
                                <th>Horas Extras</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asistencias as $asistencia)
                                <tr>
                                    <td>{{ $asistencia->fecha->format('d/m/Y') }}</td>
                                    <td>{{ $asistencia->fecha->translatedFormat('l') }}</td>
                                    <td>
                                        @if($asistencia->hora_entrada)
                                            <span class="badge bg-success">{{ \Carbon\Carbon::parse($asistencia->hora_entrada)->format('H:i') }}</span>
                                        @else
                                            <span class="badge bg-secondary">--:--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($asistencia->hora_salida)
                                            <span class="badge bg-info">{{ \Carbon\Carbon::parse($asistencia->hora_salida)->format('H:i') }}</span>
                                        @else
                                            <span class="badge bg-secondary">--:--</span>
                                        @endif
                                    </td>
                                    <td>
@if($asistencia->hora_entrada && $asistencia->hora_salida)
    @php
        $horas = floor($asistencia->horas_trabajadas);
        $minutos = round(($asistencia->horas_trabajadas - $horas) * 60);
    @endphp
    <span class="badge bg-primary">{{ $horas }}h {{ $minutos }}min</span>
@else
    <span class="badge bg-secondary">--</span>
@endif

                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($asistencia->estado == 'presente') bg-success
                                            @elseif($asistencia->estado == 'tardanza') bg-warning
                                            @elseif($asistencia->estado == 'ausente') bg-danger
                                            @elseif($asistencia->estado == 'permiso') bg-info
                                            @else bg-secondary @endif">
                                            {{ $asistencia->estado }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($asistencia->minutos_retraso > 0)
                                            <span class="text-warning">{{ $asistencia->minutos_retraso }} min</span>
                                        @else
                                            <span class="text-success">0 min</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($asistencia->horas_extras > 0)
                                            <span class="text-info">{{ $asistencia->horas_extras }}h</span>
                                        @else
                                            <span class="text-muted">0h</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No hay registros de asistencia para este período</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $asistencias->links() }}
                </div>
            </div>
        </div>

        <!-- Botón para justificar ausencia -->
        <div class="text-center mt-4">
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalJustificar">
                <i class="fas fa-file-alt me-2"></i>Justificar Ausencia
            </button>
        </div>
    </div>
</div>

<!-- Modal para justificar ausencia -->
<div class="modal fade" id="modalJustificar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('empleado.asistencias.justificar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Justificar Ausencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha de Ausencia *</label>
                        <input type="date" class="form-control" id="fecha" name="fecha"
                               max="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Justificación *</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="">Seleccionar...</option>
                            <option value="permiso">Permiso</option>
                            <option value="vacaciones">Vacaciones</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Motivo *</label>
                        <textarea class="form-control" id="observaciones" name="observaciones"
                                  rows="3" placeholder="Describe el motivo de tu ausencia..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="archivo" class="form-label">Archivo de Justificación (Opcional)</label>
                        <input type="file" class="form-control" id="archivo" name="archivo"
                               accept=".pdf,.jpg,.png">
                        <div class="form-text">Formatos permitidos: PDF, JPG, PNG (Max. 2MB)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Enviar Justificación</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function obtenerUbicacion() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitud').value = position.coords.latitude;
                document.getElementById('longitud').value = position.coords.longitude;
                // Enviar el formulario automáticamente
                event.target.closest('form').submit();
            },
            function(error) {
                // Si el usuario deniega la ubicación, enviar sin geolocalización
                document.getElementById('latitud').value = '';
                document.getElementById('longitud').value = '';
                event.target.closest('form').submit();
            }
        );
    } else {
        // Navegador no soporta geolocalización
        document.getElementById('latitud').value = '';
        document.getElementById('longitud').value = '';
        event.target.closest('form').submit();
    }
}

function obtenerUbicacionSalida() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitud-salida').value = position.coords.latitude;
                document.getElementById('longitud-salida').value = position.coords.longitude;
                event.target.closest('form').submit();
            },
            function(error) {
                document.getElementById('latitud-salida').value = '';
                document.getElementById('longitud-salida').value = '';
                event.target.closest('form').submit();
            }
        );
    } else {
        document.getElementById('latitud-salida').value = '';
        document.getElementById('longitud-salida').value = '';
        event.target.closest('form').submit();
    }
}
</script>
@endpush
