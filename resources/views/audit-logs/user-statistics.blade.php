{{-- resources/views/audit-logs/user-statistics.blade.php --}}
@extends('dashboard')

@section('title', 'Estadísticas por Usuario')

@section('contenido')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @if($user)
                            Estadísticas de Actividad - {{ $user->name }}
                        @else
                            Estadísticas por Usuario
                        @endif
                    </h3>
                    <div class="card-tools">
                        <form method="GET" class="form-inline">
                            <select name="user_id" class="form-control mr-2">
                                <option value="">Seleccionar Usuario</option>
                                @foreach($users as $usr)
                                    <option value="{{ $usr->id }}" {{ request('user_id') == $usr->id ? 'selected' : '' }}>
                                        {{ $usr->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="date" name="date_from" value="{{ $dateRange['date_from'] ?? '' }}" class="form-control mr-2">
                            <input type="date" name="date_to" value="{{ $dateRange['date_to'] ?? '' }}" class="form-control mr-2">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($user)
    <div class="row">
        <!-- Estadísticas Principales -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resumen</h3>
                </div>
                <div class="card-body">
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text">Total Actividades</span>
                            <span class="info-box-number">{{ $stats['total_activities'] }}</span>
                        </div>
                    </div>
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text">Primera Actividad</span>
                            <span class="info-box-number">
                                {{ $stats['first_activity'] ? $stats['first_activity']->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text">Última Actividad</span>
                            <span class="info-box-number">
                                {{ $stats['last_activity'] ? $stats['last_activity']->format('d/m/Y H:i') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text">Promedio por Día</span>
                            <span class="info-box-number">{{ $stats['activity_per_day'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución de Eventos -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribución de Eventos</h3>
                </div>
                <div class="card-body">
                    <canvas id="userEventsChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Horarios Más Activos -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Horarios Más Activos</h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($mostActiveHours as $hour)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span>{{ $hour->hour }}:00 - {{ $hour->hour + 1 }}:00</span>
                                <span class="badge badge-primary">{{ $hour->count }} actividades</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Acciones Frecuentes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Acciones Más Frecuentes</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Evento</th>
                                    <th>Modelo</th>
                                    <th>Veces</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($frequentActions as $action)
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ \App\Models\AuditLog::getEventBadgeStatic($action->event) }}">
                                            {{ ucfirst($action->event) }}
                                        </span>
                                    </td>
                                    <td>{{ class_basename($action->model_type) }}</td>
                                    <td>{{ $action->count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline de Actividad -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actividad por Día</h3>
                </div>
                <div class="card-body">
                    <canvas id="activityTimelineChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">
                    <h4>Selecciona un usuario para ver sus estadísticas</h4>
                    <p class="text-muted">Utiliza el filtro superior para seleccionar un usuario específico</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if($user)
// Gráfico de eventos del usuario
const userEventsCtx = document.getElementById('userEventsChart').getContext('2d');
const userEventsChart = new Chart(userEventsCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode(collect($stats['events_breakdown'])->pluck('event')) !!},
        datasets: [{
            data: {!! json_encode(collect($stats['events_breakdown'])->pluck('count')) !!},
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
        }]
    }
});

// Gráfico de timeline
const timelineCtx = document.getElementById('activityTimelineChart').getContext('2d');
const timelineChart = new Chart(timelineCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($activityTimeline->pluck('date')) !!},
        datasets: [{
            label: 'Actividades por Día',
            data: {!! json_encode($activityTimeline->pluck('count')) !!},
            backgroundColor: '#36A2EB'
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
@endif
</script>
@endpush
