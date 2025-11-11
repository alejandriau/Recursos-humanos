@extends('dashboard')

@section('title', 'Reportes de Vacaciones')

@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Reportes de Vacaciones</h5>
            </div>
            <div class="card-body">
                <!-- Estadísticas generales -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h4 class="card-title">{{ $totalVacaciones }}</h4>
                                <p class="card-text">Total Solicitudes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h4 class="card-title">{{ $vacacionesAprobadas }}</h4>
                                <p class="card-text">Aprobadas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h4 class="card-title">{{ $vacacionesPendientes }}</h4>
                                <p class="card-text">Pendientes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <h4 class="card-title">{{ $totalVacaciones - $vacacionesAprobadas - $vacacionesPendientes }}</h4>
                                <p class="card-text">Rechazadas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top empleados con más vacaciones -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Top 10 Empleados con Más Días de Vacaciones</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Empleado</th>
                                                <th>Días Totales</th>
                                                <th>Solicitudes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($empleadosConMasVacaciones as $empleado)
                                                <tr>
                                                    <td>{{ $empleado->persona->nombre }} {{ $empleado->persona->apellidoPat }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $empleado->total_dias }} días</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            {{ $empleado->persona->vacaciones->where('estado', 'aprobado')->count() }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Solicitudes por Mes ({{ now()->year }})</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Mes</th>
                                                <th>Total</th>
                                                <th>Aprobadas</th>
                                                <th>Pendientes</th>
                                                <th>Rechazadas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $meses = [
                                                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                                ];
                                            @endphp
                                            @foreach($meses as $numero => $mes)
                                                @php
                                                    $mesData = $vacacionesPorMes->where('mes', $numero);
                                                    $total = $mesData->sum('total');
                                                    $aprobadas = $mesData->where('estado', 'aprobado')->sum('total');
                                                    $pendientes = $mesData->where('estado', 'pendiente')->sum('total');
                                                    $rechazadas = $mesData->where('estado', 'rechazado')->sum('total');
                                                @endphp
                                                <tr>
                                                    <td>{{ $mes }}</td>
                                                    <td><span class="badge bg-primary">{{ $total }}</span></td>
                                                    <td><span class="badge bg-success">{{ $aprobadas }}</span></td>
                                                    <td><span class="badge bg-warning">{{ $pendientes }}</span></td>
                                                    <td><span class="badge bg-danger">{{ $rechazadas }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de exportación -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Exportar Reportes</h6>
                            </div>
                            <div class="card-body">
                                <button class="btn btn-success">
                                    <i class="fas fa-file-excel me-2"></i>Exportar a Excel
                                </button>
                                <button class="btn btn-danger">
                                    <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
