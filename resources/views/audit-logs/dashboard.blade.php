{{-- resources/views/audit-logs/dashboard.blade.php --}}
@extends('dashboard')

@section('title', 'Dashboard de Auditoría')

@section('contenido')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resumen General de Actividades</h3>
                    <div class="card-tools">
                        <form method="GET" class="form-inline">
                            <div class="input-group input-group-sm">
                                <input type="date" name="date_from" value="{{ $dateRange['date_from'] ?? '' }}" class="form-control">
                                <input type="date" name="date_to" value="{{ $dateRange['date_to'] ?? '' }}" class="form-control">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">Filtrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-chart-bar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Actividades</span>
                                    <span class="info-box-number">{{ $globalStats['total_activities'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Usuarios Únicos</span>
                                    <span class="info-box-number">{{ $globalStats['unique_users'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-calendar-day"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Hoy</span>
                                    <span class="info-box-number">{{ $globalStats['activities_today'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Hora Pico</span>
                                    <span class="info-box-number">{{ $globalStats['peak_activity_hour'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Usuarios Activos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top 10 Usuarios Más Activos</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Actividades</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topUsers as $userActivity)
                                <tr>
                                    <td>{{ $userActivity->user->name ?? 'Usuario Eliminado' }}</td>
                                    <td>{{ $userActivity->activity_count }}</td>
                                    <td>
                                        <a href="{{ route('audit-logs.user-statistics', $userActivity->user_id) }}"
                                           class="btn btn-info btn-sm">Ver Detalles</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución de Eventos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribución de Eventos</h3>
                </div>
                <div class="card-body">
                    <canvas id="eventDistributionChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Tendencias de Actividad -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tendencias de Actividad (Últimos 7 días)</h3>
                </div>
                <div class="card-body">
                    <canvas id="activityTrendsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de distribución de eventos
const eventCtx = document.getElementById('eventDistributionChart').getContext('2d');
const eventChart = new Chart(eventCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($eventTrends->pluck('event')) !!},
        datasets: [{
            data: {!! json_encode($eventTrends->pluck('count')) !!},
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Gráfico de tendencias (ejemplo - necesitarías implementar los datos)
const trendCtx = document.getElementById('activityTrendsChart').getContext('2d');
const trendChart = new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
        datasets: [{
            label: 'Actividades',
            data: [12, 19, 3, 5, 2, 3, 15],
            borderColor: '#36A2EB',
            tension: 0.1
        }]
    },
    options: {
        responsive: true
    }
});
</script>
@endpush
