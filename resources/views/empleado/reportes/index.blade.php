{{-- resources/views/empleado/dashboard.blade.php --}}

@extends('layouts.baseusr')

@section('cuerpo')
<div class="container-fluid px-4">
    <!-- Banner de bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-banner p-4 rounded-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="text-white mb-2">
                            <i class="fas fa-wave-square me-2"></i>
                            ¡Bienvenido, {{ $persona->nombre ?? 'Usuario' }}!
                        </h1>
                        <p class="text-white-50 mb-0">
                            <i class="far fa-calendar-alt me-2"></i>
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d \\d\\e F \\d\\e Y') }}
                        </p>
                        <p class="text-white-50 mt-2">
                            <i class="fas fa-clock me-2"></i>
                            Hora actual: <span id="horaActual"></span>
                        </p>
                    </div>
                    <div class="col-md-4 text-center">
                        @if($persona->foto)
                            <img src="{{ route('persona.foto', $persona->id) }}"
                                 alt="Foto" class="rounded-circle border border-white border-3"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                 style="width: 100px; height: 100px;">
                                <i class="fas fa-user fa-3x text-secondary"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Saldo Vacaciones</h6>
                            <h3 class="mb-0 text-primary">{{ number_format($estadisticas['dias_vacaciones'], 1) }}</h3>
                        </div>
                        <div class="bg-primary-soft p-3 rounded-circle">
                            <i class="fas fa-umbrella-beach text-primary fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Días disponibles
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Salidas</h6>
                            <h3 class="mb-0 text-success">{{ $estadisticas['total_salidas'] }}</h3>
                        </div>
                        <div class="bg-success-soft p-3 rounded-circle">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-check me-1"></i>
                        Solicitudes aprobadas
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Días Usados</h6>
                            <h3 class="mb-0 text-info">{{ number_format($estadisticas['total_usado'], 1) }}</h3>
                        </div>
                        <div class="bg-info-soft p-3 rounded-circle">
                            <i class="fas fa-clock text-info fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-hourglass-half me-1"></i>
                        Total días/horas usados
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pendientes</h6>
                            <h3 class="mb-0 text-warning">{{ $estadisticas['pendientes'] }}</h3>
                        </div>
                        <div class="bg-warning-soft p-3 rounded-circle">
                            <i class="fas fa-clock text-warning fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-hourglass-start me-1"></i>
                        En espera de aprobación
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>
                        Distribución por Tipo de Salida - {{ \Carbon\Carbon::now()->year }}
                    </h6>
                </div>
                <div class="card-body">
                    <div style="height: 250px;">
                        <canvas id="chartPorTipo"></canvas>
                    </div>
                    <div class="row mt-3">
                        @foreach($resumenPorTipo as $item)
                            <div class="col-6 col-md-4">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-{{ $item['color'] }} me-2">&nbsp;</span>
                                    <small>{{ $item['tipo'] }}</small>
                                    <small class="ms-auto fw-bold">{{ $item['total_solicitudes'] }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>
                        Evolución Anual
                    </h6>
                </div>
                <div class="card-body">
                    <div style="height: 250px;">
                        <canvas id="chartAnual"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Saldo de Vacaciones por Período -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-umbrella-beach me-2 text-primary"></i>
                        Saldo de Vacaciones por Período
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Período</th>
                                    <th>Fecha Habilitación</th>
                                    <th class="text-end">Días Asignados</th>
                                    <th class="text-end">Días Usados</th>
                                    <th class="text-end">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($saldoVacaciones as $periodo)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $periodo->numero_periodo }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($periodo->fecha_habilitacion)->format('d/m/Y') }}</td>
                                        <td class="text-end">{{ $periodo->dias_asignados }}</td>
                                        <td class="text-end">{{ number_format($periodo->dias_usados, 1) }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-{{ $periodo->saldo_disponible > 0 ? 'success' : 'danger' }} fs-6">
                                                {{ number_format($periodo->saldo_disponible, 1) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <i class="fas fa-inbox me-2"></i>
                                            No hay períodos de vacaciones
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Total Saldo</th>
                                    <th class="text-end">
                                        <span class="badge bg-success fs-6">
                                            {{ number_format($saldoVacaciones->sum('saldo_disponible'), 1) }}
                                        </span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Salidas y Pendientes -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2 text-primary"></i>
                        Últimas Salidas Aprobadas
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($ultimasSalidas as $salida)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-{{ $salida->tipoSalida->es_vacacion ? 'primary' : 'info' }} me-2">
                                        {{ $salida->tipoSalida->descripcion ?? 'N/A' }}
                                    </span>
                                    <span class="small">
                                        {{ \Carbon\Carbon::parse($salida->fechasal)->format('d/m/Y') }}
                                        <span class="text-muted">→</span>
                                        {{ \Carbon\Carbon::parse($salida->fecharet)->format('d/m/Y') }}
                                    </span>
                                </div>
                                <span class="badge bg-success">
                                    {{ number_format($salida->cantidad, 1) }} {{ $salida->tipoSalida->unidad ?? 'días' }}
                                </span>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted py-3">
                                <i class="fas fa-inbox me-2"></i>
                                No hay salidas aprobadas
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2 text-warning"></i>
                        Solicitudes Pendientes
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($pendientes as $salida)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-warning me-2">
                                        {{ $salida->tipoSalida->descripcion ?? 'N/A' }}
                                    </span>
                                    <span class="small">
                                        {{ \Carbon\Carbon::parse($salida->fechasal)->format('d/m/Y') }}
                                        <span class="text-muted">→</span>
                                        {{ \Carbon\Carbon::parse($salida->fecharet)->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div>
                                    @if($salida->estado == 'pendiente_jefe')
                                        <span class="badge bg-secondary">Esperando Jefe</span>
                                    @else
                                        <span class="badge bg-info">Esperando RRHH</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted py-3">
                                <i class="fas fa-check-circle me-2 text-success"></i>
                                No hay solicitudes pendientes
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); }
.bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
.bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
.bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }

.welcome-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
}
.welcome-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
}
.welcome-banner::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: 20%;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,0.03);
}

.quick-card {
    cursor: pointer;
    transition: all 0.3s ease;
}
.quick-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Reloj en tiempo real
function actualizarHora() {
    const ahora = new Date();
    const hora = ahora.toLocaleTimeString('es-BO', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
    document.getElementById('horaActual').textContent = hora;
}
setInterval(actualizarHora, 1000);
actualizarHora();

// Gráfico: Distribución por Tipo
document.addEventListener('DOMContentLoaded', function() {
    const ctxTipo = document.getElementById('chartPorTipo').getContext('2d');
    const dataTipo = @json($resumenPorTipo);

    new Chart(ctxTipo, {
        type: 'doughnut',
        data: {
            labels: dataTipo.map(item => item.tipo),
            datasets: [{
                data: dataTipo.map(item => item.total_solicitudes),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Gráfico: Evolución Anual
    const ctxAnual = document.getElementById('chartAnual').getContext('2d');
    const dataAnual = @json($resumenAnual);

    new Chart(ctxAnual, {
        type: 'bar',
        data: {
            labels: dataAnual.map(item => item.anio),
            datasets: [
                {
                    label: 'Días Usados',
                    data: dataAnual.map(item => item.total_dias),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    label: 'Solicitudes',
                    data: dataAnual.map(item => item.total_solicitudes),
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
