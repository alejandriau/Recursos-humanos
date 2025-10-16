{{-- resources/views/audit-logs/index.blade.php --}}
@extends('dashboard')

@section('title', 'Registros de Auditoría')

@section('contenido')
<div class="container-fluid">
    <!-- Tarjetas de Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_activities'] ?? 0 }}</h3>
                    <p>Total de Actividades</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <a href="{{ route('audit-logs.dashboard') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['today_activities'] ?? 0 }}</h3>
                    <p>Actividades Hoy</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <a href="{{ route('audit-logs.index', ['date_from' => today()->format('Y-m-d')]) }}" class="small-box-footer">
                    Ver hoy <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['unique_users'] ?? 0 }}</h3>
                    <p>Usuarios Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('audit-logs.user-statistics') }}" class="small-box-footer">
                    Ver estadísticas <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['suspicious_count'] ?? 0 }}</h3>
                    <p>Actividades Sospechosas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('audit-logs.suspicious-activities') }}" class="small-box-footer">
                    Investigar <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Registros de Auditoría</h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="{{ route('audit-logs.dashboard') }}" class="btn btn-info">
                                <i class="fas fa-chart-pie"></i> Dashboard
                            </a>
                            <a href="{{ route('audit-logs.user-statistics') }}" class="btn btn-success">
                                <i class="fas fa-user-chart"></i> Estadísticas por Usuario
                            </a>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('audit-logs.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="event">Evento</label>
                                    <select name="event" id="event" class="form-control">
                                        <option value="">Todos</option>
                                        @foreach($events ?? [] as $event)
                                            <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                                                {{ ucfirst($event) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="user_id">Usuario</label>
                                    <select name="user_id" id="user_id" class="form-control">
                                        <option value="">Todos</option>
                                        @foreach($users ?? [] as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_from">Desde</label>
                                    <input type="date" name="date_from" id="date_from"
                                           value="{{ request('date_from') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_to">Hasta</label>
                                    <input type="date" name="date_to" id="date_to"
                                           value="{{ request('date_to') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-filter"></i> Filtrar
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-redo"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Evento</th>
                                    <th>Descripción</th>
                                    <th>Usuario</th>
                                    <th>IP</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditLogs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>
                                        <span class="badge badge-{{ $log->getEventBadge() }}">
                                            {{ ucfirst($log->event) }}
                                        </span>
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td>
                                        <a href="{{ route('audit-logs.user-statistics', $log->user_id) }}">
                                            {{ $log->user->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('audit-logs.show', $log) }}"
                                           class="btn btn-info btn-sm">Ver Detalles</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $auditLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
