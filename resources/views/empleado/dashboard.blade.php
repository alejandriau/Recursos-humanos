@extends('dashboard')

@section('title', 'Mi Panel - Empleado')

@section('contenido')
<div class="container-fluid">
    <!-- Header del Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1">Bienvenido/a, {{ Auth::user()->persona->nombre ?? 'Empleado' }}</h2>
                            <p class="mb-0">Panel de control personal - {{ now()->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            @if(Auth::user()->persona && Auth::user()->persona->foto)
                                <img src="{{ asset('storage/' . Auth::user()->persona->foto) }}"
                                     alt="Foto" class="rounded-circle" width="80" height="80">
                            @else
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-user text-primary fa-2x"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row mb-4">
        <!-- Asistencias del Mes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Asistencias Este Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['asistencias_mes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vacaciones Disponibles -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Días Vacaciones Disp.
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['dias_vacaciones'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-umbrella-beach fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Horas Extras Mes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Horas Extras (Mes)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['horas_extras_mes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Puesto Actual -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Puesto Actual
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800 text-truncate">
                                {{ $puestoActual->denominacion ?? 'No asignado' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Asistencias Recientes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Mis Asistencias Recientes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Entrada</th>
                                    <th>Salida</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asistenciasRecientes as $asistencia)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m') }}</td>
                                        <td>
                                            @if($asistencia->hora_entrada)
                                                <span class="badge bg-success">{{ $asistencia->hora_entrada }}</span>
                                            @else
                                                <span class="badge bg-secondary">--:--</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($asistencia->hora_salida)
                                                <span class="badge bg-info">{{ $asistencia->hora_salida }}</span>
                                            @else
                                                <span class="badge bg-secondary">--:--</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $asistencia->estado == 'presente' ? 'success' : ($asistencia->estado == 'tardanza' ? 'warning' : 'danger') }}">
                                                {{ $asistencia->estado }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No hay asistencias registradas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-2">
                        <a href="{{ route('empleado.asistencias.index') }}" class="btn btn-sm btn-outline-primary">
                            Ver Todas las Asistencias
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vacaciones y Solicitudes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-umbrella-beach me-2"></i>Mis Vacaciones
                    </h6>
                </div>
                <div class="card-body">
                    @if($vacacionesPendientes->count() > 0)
                        <div class="alert alert-info">
                            <strong>Tienes {{ $vacacionesPendientes->count() }} solicitud(es) pendiente(s)</strong>
                        </div>
                    @endif

                    <div class="list-group list-group-flush">
                        @forelse($vacacionesRecientes as $vacacion)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($vacacion->fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($vacacion->fecha_fin)->format('d/m/Y') }}</small>
                                    <br>
                                    <span class="badge bg-{{ $vacacion->estado == 'aprobado' ? 'success' : ($vacacion->estado == 'pendiente' ? 'warning' : 'danger') }}">
                                        {{ $vacacion->estado }}
                                    </span>
                                </div>
                                <span class="badge bg-primary">{{ $vacacion->dias_tomados }} días</span>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                No hay registros de vacaciones
                            </div>
                        @endforelse
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('empleado.vacaciones.create') }}" class="btn btn-sm btn-success me-2">
                            <i class="fas fa-plus me-1"></i>Solicitar Vacaciones
                        </a>
                        <a href="{{ route('empleado.vacaciones.index') }}" class="btn btn-sm btn-outline-success">
                            Ver Historial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Puesto Actual -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-briefcase me-2"></i>Mi Información Laboral
                    </h6>
                </div>
                <div class="card-body">
                    @if($puestoActual)
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Puesto:</strong> {{ $puestoActual->denominacion }}</p>
                                <p><strong>Nivel Jerárquico:</strong> {{ $puestoActual->nivelJerarquico }}</p>
                                <p><strong>Unidad:</strong> {{ $puestoActual->unidadOrganizacional->denominacion ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tipo de Contrato:</strong> {{ $puestoActual->tipoContrato }}</p>
                                <p><strong>Haber Básico:</strong> Bs. {{ number_format($puestoActual->haber, 2) }}</p>
                                <p><strong>Item:</strong> {{ $puestoActual->item ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            No tienes un puesto asignado actualmente.
                        </div>
                    @endif

                    <div class="text-center">
                        <a href="{{ route('empleado.historial') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-history me-1"></i>Ver Mi Historial Completo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
