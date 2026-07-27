{{-- resources/views/admin/vacaciones/show.blade.php --}}
@extends('layouts.baseadm')

@section('content')
<div class="container-fluid">
    <!-- ============================================ -->
    <!-- 1. CABECERA Y DATOS DEL EMPLEADO             -->
    <!-- ============================================ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.vacaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <h1 class="d-inline-block ml-3">
                        <i class="fas fa-umbrella-beach text-primary"></i>
                        Historial de Vacaciones
                        <small class="text-muted">{{ $persona->nombre }} {{ $persona->apellidoPat }}</small>
                    </h1>
                </div>
                <div>
                    <a href="{{ route('admin.vacaciones.exportar-pdf', $persona->id) }}" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- 2. INFORMACIÓN DEL EMPLEADO                   -->
    <!-- ============================================ -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user"></i> Datos del Empleado</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <small class="text-muted">Nombre Completo</small>
                    <p class="font-weight-bold">{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</p>
                </div>
                <div class="col-md-2">
                    <small class="text-muted">Cédula de Identidad</small>
                    <p class="font-weight-bold">{{ $persona->ci }}</p>
                </div>
                <div class="col-md-2">
                    <small class="text-muted">Área / Unidad</small>
                    <p class="font-weight-bold">{{ $unidadActual->nombre ?? 'N/A' }}</p>
                </div>
                <div class="col-md-2">
                    <small class="text-muted">Fecha de Ingreso</small>
                    <p class="font-weight-bold">{{ \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Antigüedad</small>
                    <p class="font-weight-bold">
                        {{ \Carbon\Carbon::parse($persona->fechaIngreso)->diffInYears(now()) }} años
                        <span class="text-muted">
                            ({{ \Carbon\Carbon::parse($persona->fechaIngreso)->diffInMonths(now()) }} meses)
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- 3. RESUMEN DE VACACIONES (TARJETAS)          -->
    <!-- ============================================ -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-check-circle"></i> Saldo Disponible
                    </h6>
                    <h2 class="card-text">{{ number_format($resumen['saldo_actual'], 1) }} días</h2>
                    <small>Días que puede usar actualmente</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Períodos Activos
                    </h6>
                    <h2 class="card-text">{{ $resumen['periodos_activos'] }}</h2>
                    <small>Períodos con saldo disponible</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-clock"></i> Días Usados
                    </h6>
                    <h2 class="card-text">{{ number_format($resumen['total_usado'], 1) }}</h2>
                    <small>Total de días ya tomados</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Días Vencidos
                    </h6>
                    <h2 class="card-text">{{ number_format($resumen['total_vencido'], 1) }}</h2>
                    <small>Días perdidos por no usarlos a tiempo</small>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- 4. ESTADO ACTUAL DE VACACIONES               -->
    <!-- ============================================ -->
    <div class="row mb-4">
        @if($enVacaciones)
        <div class="col-md-6">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-sun"></i> 🔴 ACTUALMENTE DE VACACIONES</h6>
                </div>
                <div class="card-body">
                    <p>
                        <strong>Desde:</strong> {{ \Carbon\Carbon::parse($enVacaciones->fechasal)->format('d/m/Y') }}<br>
                        <strong>Hasta:</strong> {{ \Carbon\Carbon::parse($enVacaciones->fecharet)->format('d/m/Y') }}<br>
                        <strong>Días:</strong> {{ $enVacaciones->cantidad }}<br>
                        <strong>Período:</strong> Año {{ $enVacaciones->movimientosVacacion->first()->periodo->numero_periodo ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if($proximasVacaciones)
        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> 📅 PRÓXIMAS VACACIONES</h6>
                </div>
                <div class="card-body">
                    <p>
                        <strong>Desde:</strong> {{ \Carbon\Carbon::parse($proximasVacaciones->fechasal)->format('d/m/Y') }}<br>
                        <strong>Hasta:</strong> {{ \Carbon\Carbon::parse($proximasVacaciones->fecharet)->format('d/m/Y') }}<br>
                        <strong>Días:</strong> {{ $proximasVacaciones->cantidad }}
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- ============================================ -->
    <!-- 5. PERÍODOS DE VACACIÓN (TABLA PRINCIPAL)    -->
    <!-- ============================================ -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-layer-group"></i> Períodos de Vacación
                <small class="text-muted">(Cada fila = 1 año de servicio)</small>
            </h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Período</th>
                        <th>Habilitado</th>
                        <th>Antigüedad</th>
                        <th>Asignados</th>
                        <th>Usados</th>
                        <th>Vencidos</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($periodos as $periodo)
                    <tr class="{{
                        $periodo->estado == 'vencido' ? 'table-danger' :
                        ($periodo->estado == 'activo' && $periodo->saldo_disponible > 0 ? 'table-success' :
                        ($periodo->estado == 'espera' ? 'table-secondary' : ''))
                    }}">
                        <td><strong>{{ $periodo->numero_periodo }}</strong></td>
                        <td>
                            {{ \Carbon\Carbon::parse($periodo->fecha_habilitacion)->format('d/m/Y') }}
                            <small class="text-muted d-block">
                                al {{ \Carbon\Carbon::parse($periodo->fecha_habilitacion)->addYear()->subDay()->format('d/m/Y') }}
                            </small>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($periodo->fecha_habilitacion)->format('d/m/Y') }}</td>
                        <td>{{ $periodo->anios_antiguedad }} años</td>
                        <td><strong>{{ $periodo->dias_asignados }}</strong></td>
                        <td>{{ number_format($periodo->dias_usados, 1) }}</td>
                        <td>{{ number_format($periodo->dias_vencidos, 1) }}</td>
                        <td>
                            <span class="badge {{ $periodo->saldo_disponible > 0 ? 'badge-success' : 'badge-secondary' }} badge-lg">
                                {{ number_format($periodo->saldo_disponible, 1) }}
                            </span>
                        </td>
                        <td>
                            @if($periodo->estado == 'activo')
                                <span class="badge badge-success">✅ Activo</span>
                            @elseif($periodo->estado == 'vencido')
                                <span class="badge badge-danger">❌ Vencido</span>
                            @elseif($periodo->estado == 'agotado')
                                <span class="badge badge-secondary">⚪ Agotado</span>
                            @elseif($periodo->estado == 'espera')
                                <span class="badge badge-secondary">⏳ Espera</span>
                            @else
                                <span class="badge badge-warning">⏰ Pendiente</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="toggleMovimientos({{ $periodo->id }})">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                        </td>
                    </tr>
                    <!-- FILA DE MOVIMIENTOS (OCULTA POR DEFECTO) -->
                    <tr id="movimientos-{{ $periodo->id }}" style="display: none; background-color: #f8f9fa;">
                        <td colspan="10">
                            <div class="p-3">
                                <h6 class="text-primary">
                                    <i class="fas fa-list-ul"></i> Movimientos del Período {{ $periodo->numero_periodo }}
                                    <span class="text-muted">(Días usados: {{ number_format($periodo->dias_usados, 1) }} de {{ $periodo->dias_asignados }})</span>
                                </h6>
                                @if($periodo->movimientos->count() > 0)
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Desde</th>
                                            <th>Hasta</th>
                                            <th>Cantidad</th>
                                            <th>Saldo Anterior</th>
                                            <th>Saldo Posterior</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($periodo->movimientos as $mov)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($mov->fecha_inicio)->format('d/m/Y') }}</td>
                                            <td>{{ $mov->fecha_fin ? \Carbon\Carbon::parse($mov->fecha_fin)->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                <span class="badge badge-danger">{{ number_format($mov->cantidad, 1) }}</span>
                                            </td>
                                            <td>{{ number_format($mov->saldo_anterior, 1) }}</td>
                                            <td>{{ number_format($mov->saldo_posterior, 1) }}</td>
                                            <td>{{ $mov->descripcion ?? 'Uso de vacaciones' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle"></i> No hay movimientos registrados en este período
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- 6. GRÁFICO DE ESTADÍSTICAS                   -->
    <!-- ============================================ -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Estadísticas por Año</h5>
        </div>
        <div class="card-body">
            <canvas id="estadisticasChart" height="100"></canvas>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- 7. SOLICITUDES DE VACACIÓN                   -->
    <!-- ============================================ -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-file-signature"></i> Solicitudes de Vacación</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Fecha Solicitud</th>
                        <th>Desde</th>
                        <th>Hasta</th>
                        <th>Días</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Aprobación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitudes as $solicitud)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($solicitud->fechasol)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($solicitud->fechasal)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($solicitud->fecharet)->format('d/m/Y') }}</td>
                        <td><strong>{{ $solicitud->cantidad }}</strong></td>
                        <td>{{ $solicitud->motivo ?? '-' }}</td>
                        <td>
                            @if($solicitud->estado == 'aprobado')
                                <span class="badge badge-success">✅ Aprobado</span>
                            @elseif($solicitud->estado == 'pendiente_jefe')
                                <span class="badge badge-warning">⏳ Pendiente Jefe</span>
                            @elseif($solicitud->estado == 'pendiente_rrhh')
                                <span class="badge badge-info">⏳ Pendiente RRHH</span>
                            @else
                                <span class="badge badge-danger">❌ Rechazado</span>
                            @endif
                        </td>
                        <td>
                            @if($solicitud->estado == 'aprobado')
                                <small>
                                    <i class="fas fa-user-tie"></i> Jefe: {{ $solicitud->fecha_aprobacion_jefe ? \Carbon\Carbon::parse($solicitud->fecha_aprobacion_jefe)->format('d/m/Y') : '-' }}<br>
                                    <i class="fas fa-users"></i> RRHH: {{ $solicitud->fecha_aprobacion_rrhh ? \Carbon\Carbon::parse($solicitud->fecha_aprobacion_rrhh)->format('d/m/Y') : '-' }}
                                </small>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if($solicitudes->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> No hay solicitudes registradas
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- 8. KARDEX - HISTORIAL COMPLETO               -->
    <!-- ============================================ -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-book"></i> Kardex - Historial de Movimientos
                <small class="text-muted">(Registro detallado de cada uso de vacaciones)</small>
            </h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Período</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Saldo Anterior</th>
                        <th>Saldo Posterior</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movimientos as $mov)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge badge-secondary">
                                Año {{ $mov->periodo->numero_periodo ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @switch($mov->tipo)
                                @case('credito')
                                    <span class="badge badge-success">➕ Crédito</span>
                                    @break
                                @case('debito')
                                    <span class="badge badge-danger">➖ Débito</span>
                                    @break
                                @case('arrastre')
                                    <span class="badge badge-info">↗ Arrastre</span>
                                    @break
                                @case('vencimiento')
                                    <span class="badge badge-warning">⚠ Vencimiento</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">{{ $mov->tipo }}</span>
                            @endswitch
                        </td>
                        <td><strong>{{ number_format($mov->cantidad, 1) }}</strong></td>
                        <td>{{ number_format($mov->saldo_anterior, 1) }}</td>
                        <td>{{ number_format($mov->saldo_posterior, 1) }}</td>
                        <td>{{ $mov->descripcion ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $movimientos->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Toggle movimientos (expandir/colapsar)
    function toggleMovimientos(id) {
        const element = document.getElementById('movimientos-' + id);
        if (element.style.display === 'none' || element.style.display === '') {
            element.style.display = 'table-row';
        } else {
            element.style.display = 'none';
        }
    }

    // Gráfico de estadísticas
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('estadisticasChart').getContext('2d');
        const data = @json($estadisticasAnuales);

        const years = Object.keys(data);
        const asignados = years.map(year => data[year].asignados);
        const usados = years.map(year => data[year].usados);
        const vencidos = years.map(year => data[year].vencidos);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: years,
                datasets: [
                    {
                        label: '📊 Días Asignados',
                        data: asignados,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2
                    },
                    {
                        label: '✅ Días Usados',
                        data: usados,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2
                    },
                    {
                        label: '❌ Días Vencidos',
                        data: vencidos,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5,
                            callback: function(value) {
                                return value + ' días';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .badge-lg {
        font-size: 14px;
        padding: 6px 12px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    @media print {
        .btn, .no-print {
            display: none !important;
        }
        .table-responsive {
            overflow-x: visible !important;
        }
    }
</style>
@endpush
@endsection
