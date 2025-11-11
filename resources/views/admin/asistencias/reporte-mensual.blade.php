@extends('dashboard')

@section('title', 'Reporte Mensual de Asistencias')

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Reporte Mensual de Asistencias</h5>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('admin.asistencias.reporte-mensual') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Mes</label>
                                    <select name="mes" class="form-select">
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Año</label>
                                    <select name="ano" class="form-select">
                                        @for($i = date('Y') - 2; $i <= date('Y'); $i++)
                                            <option value="{{ $i }}" {{ $ano == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Empleado (Opcional)</label>
                                    <select name="persona_id" class="form-select">
                                        <option value="">Todos los empleados</option>
                                        @foreach($personas as $persona)
                                            <option value="{{ $persona->id }}" {{ $personaId == $persona->id ? 'selected' : '' }}>
                                                {{ $persona->nombre }} {{ $persona->apellidoPat }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-2"></i>Filtrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Estadísticas Generales -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body text-center">
                                <h4>{{ $estadisticas['total_personas'] }}</h4>
                                <p class="mb-0">Total Empleados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body text-center">
                                <h4>{{ $estadisticas['total_dias_laborales'] }}</h4>
                                <p class="mb-0">Días Laborales</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body text-center">
                                <h4>{{ $estadisticas['dias_mes'] }}</h4>
                                <p class="mb-0">Días del Mes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body text-center">
                                <h4>{{ $asistencias->count() }}</h4>
                                <p class="mb-0">Empleados con Registro</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporte Detallado -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Empleado</th>
                                <th>Días Presente</th>
                                <th>Días Tardanza</th>
                                <th>Días Ausente</th>
                                <th>Días Permiso</th>
                                <th>Total Retraso (min)</th>
                                <th>Total Horas Extras</th>
                                <th>Asistencia %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($personaId)
                                <!-- Reporte individual -->
                                @php
                                    $empleadoAsistencias = $asistencias->first();
                                    if ($empleadoAsistencias) {
                                        $primerRegistro = $empleadoAsistencias->first();
                                        $totalDias = $empleadoAsistencias->count();
                                        $diasPresente = $empleadoAsistencias->where('estado', 'presente')->count();
                                        $diasTardanza = $empleadoAsistencias->where('estado', 'tardanza')->count();
                                        $diasAusente = $empleadoAsistencias->where('estado', 'ausente')->count();
                                        $diasPermiso = $empleadoAsistencias->where('estado', 'permiso')->count();
                                        $totalRetraso = $empleadoAsistencias->sum('minutos_retraso');
                                        $totalHorasExtras = $empleadoAsistencias->sum('horas_extras');
                                        $asistenciaPorcentaje = $estadisticas['total_dias_laborales'] > 0 ?
                                            round((($diasPresente + $diasTardanza) / $estadisticas['total_dias_laborales']) * 100, 2) : 0;
                                    }
                                @endphp
                                @if($empleadoAsistencias)
                                    <tr>
                                        <td>
                                            <strong>{{ $primerRegistro->persona->nombre }} {{ $primerRegistro->persona->apellidoPat }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $diasPresente }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning">{{ $diasTardanza }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ $diasAusente }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $diasPermiso }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning">{{ $totalRetraso }} min</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $totalHorasExtras }} h</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $asistenciaPorcentaje >= 90 ? 'success' : ($asistenciaPorcentaje >= 80 ? 'warning' : 'danger') }}">
                                                {{ $asistenciaPorcentaje }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @else
                                <!-- Reporte general -->
                                @foreach($asistencias as $personaId => $registros)
                                    @php
                                        $primerRegistro = $registros->first();
                                        $diasPresente = $registros->where('estado', 'presente')->count();
                                        $diasTardanza = $registros->where('estado', 'tardanza')->count();
                                        $diasAusente = $registros->where('estado', 'ausente')->count();
                                        $diasPermiso = $registros->where('estado', 'permiso')->count();
                                        $totalRetraso = $registros->sum('minutos_retraso');
                                        $totalHorasExtras = $registros->sum('horas_extras');
                                        $asistenciaPorcentaje = $estadisticas['total_dias_laborales'] > 0 ?
                                            round((($diasPresente + $diasTardanza) / $estadisticas['total_dias_laborales']) * 100, 2) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $primerRegistro->persona->nombre }} {{ $primerRegistro->persona->apellidoPat }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $diasPresente }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning">{{ $diasTardanza }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ $diasAusente }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $diasPermiso }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning">{{ $totalRetraso }} min</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $totalHorasExtras }} h</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $asistenciaPorcentaje >= 90 ? 'success' : ($asistenciaPorcentaje >= 80 ? 'warning' : 'danger') }}">
                                                {{ $asistenciaPorcentaje }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Botones de Exportación -->
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <button class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>Exportar a Excel
                        </button>
                        <button class="btn btn-danger">
                            <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                        </button>
                        <a href="{{ route('admin.asistencias.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
