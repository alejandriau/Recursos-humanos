{{-- resources/views/empleado/dashboard.blade.php --}}
@extends('layouts.baseusr')

@section('cuerpo')
<div class="container-fluid px-4">
    <!-- Encabezado de bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-header d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-0">
                        <i class="fas fa-hand-peace text-primary me-2"></i>
                        ¡Bienvenido, {{ Auth::user()->name }}!
                    </h1>

                    <p class="text-muted mt-2 mb-0">
                        <i class="fas fa-calendar-day me-1"></i>
                        Hoy es {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}

                        @if($puestoActual)
                            <span class="mx-2">|</span>
                            <i class="fas fa-briefcase me-1"></i>
                            {{ $puestoActual->nombre }} -
                            {{ $puestoActual->unidadOrganizacional->nombre ?? '' }}
                        @endif
                    </p>
                </div>

                <div class="mt-2 mt-sm-0">
                    <span class="badge bg-primary bg-opacity-10 text-primary p-3">
                        <i class="fas fa-clock me-2"></i>
                        {{ now()->format('H:i') }} hrs
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-umbrella-beach text-primary fs-4"></i>
                        </div>
                    </div>
                    <h5 class="mb-0">{{ $estadisticas['dias_vacaciones'] ?? 0 }}</h5>
                    <small class="text-muted">Días de vacación disponibles</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <h5 class="mb-0">{{ $estadisticas['asistencias_mes'] ?? 0 }}</h5>
                    <small class="text-muted">Asistencias este mes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock text-warning fs-4"></i>
                        </div>
                    </div>
                    <h5 class="mb-0">{{ $estadisticas['horas_extras_mes'] ?? 0 }}</h5>
                    <small class="text-muted">Horas extras este mes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-bell text-info fs-4"></i>
                        </div>
                    </div>
                    <h5 class="mb-0">{{ $vacacionesPendientes->count() + $comisionesPendientes ?? 0 }}</h5>
                    <small class="text-muted">Solicitudes pendientes</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="mb-3">
                        <i class="fas fa-rocket text-primary me-2"></i>
                        Acciones rápidas
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('vacacion.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-umbrella-beach me-2"></i>Solicitar Vacación
                        </a>
                        <a href="#" class="btn btn-outline-success">
                            <i class="fas fa-briefcase me-2"></i>Solicitar Comisión
                        </a>
                        <a href="#" class="btn btn-outline-warning">
                            <i class="fas fa-user-clock me-2"></i>Salida Particular
                        </a>
                        <a href="{{ route('beneficios.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-gift me-2"></i>Mis Beneficios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas importantes -->
    @if($vacacionesPendientes->count() > 0 || ($comisionesPendientes ?? 0) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                <div>
                    <strong>Tienes solicitudes pendientes de aprobación:</strong>
                    @if($vacacionesPendientes->count() > 0)
                    <span class="badge bg-warning ms-1">{{ $vacacionesPendientes->count() }} vacación(es)</span>
                    @endif
                    @if(($comisionesPendientes ?? 0) > 0)
                    <span class="badge bg-warning ms-1">{{ $comisionesPendientes }} comisión(es)</span>
                    @endif
                    <a href="{{ route('empleado.vacaciones.mi-historial') }}" class="alert-link ms-2">Ver todas</a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Próximos vencimientos (beneficios) -->
    @if(isset($proximosVencimientos) && $proximosVencimientos->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="fas fa-calendar-alt me-3 fs-4"></i>
                <div>
                    <strong>Próximos vencimientos:</strong>
                    @foreach($proximosVencimientos->take(3) as $vencimiento)
                    <span class="badge bg-info ms-1">{{ $vencimiento->tipo }}: {{ $vencimiento->dias_restantes }} días</span>
                    @endforeach
                    @if($proximosVencimientos->count() > 3)
                    <span class="badge bg-secondary ms-1">+{{ $proximosVencimientos->count() - 3 }} más</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Últimas solicitudes recientes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-history text-primary me-2"></i>
                        Últimas solicitudes
                    </h6>
                    <a href="#" class="btn btn-sm btn-link">
                        Ver todas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($solicitudesRecientes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Tipo</th>
                                    <th>Desde</th>
                                    <th>Hasta</th>
                                    <th>Cantidad</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudesRecientes->take(5) as $solicitud)
                                <tr>
                                    <td class="ps-3">
                                        <span class="badge bg-{{ $solicitud->tipoSalida->color ?? 'secondary' }}">
                                            {{ $solicitud->tipoSalida->descripcion ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($solicitud->fechasal)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($solicitud->fecharet)->format('d/m/Y') }}</td>
                                    <td>{{ $solicitud->cantidad }}</td>
                                    <td class="text-center">
                                        @php
                                            $estados = [
                                                'aprobado' => ['class' => 'success', 'text' => 'Aprobado'],
                                                'pendiente_jefe' => ['class' => 'warning', 'text' => 'Pendiente Jefe'],
                                                'pendiente_rrhh' => ['class' => 'warning', 'text' => 'Pendiente RRHH'],
                                                'rechazado' => ['class' => 'danger', 'text' => 'Rechazado'],
                                            ];
                                            $estado = $estados[$solicitud->estado] ?? ['class' => 'secondary', 'text' => $solicitud->estado];
                                        @endphp
                                        <span class="badge bg-{{ $estado['class'] }}">
                                            {{ $estado['text'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fs-3 mb-2 d-block"></i>
                        <p class="mb-0">No tienes solicitudes recientes</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta de recursos humanos / enlaces útiles -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-3 text-center text-muted">
                    <small>
                        <i class="fas fa-question-circle me-1"></i>
                        ¿Necesitas ayuda? Contacta a Recursos Humanos o revisa el
                        <a href="#" class="text-decoration-none">manual del empleado</a>.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .welcome-header {
        position: relative;
        padding: 1.5rem 2rem;
        border-radius: 12px;
        overflow: hidden;

        background-color: #ffffff;

        background-image:
            linear-gradient(
                rgba(255, 255, 255, 0.90),
                rgba(255, 255, 255, 0.90)
            ),
            url('/images/tejido-horizontal.jpg');

        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;

        border: 1px solid var(--color-border);
    }
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    .btn-outline-primary, .btn-outline-success, .btn-outline-warning, .btn-outline-info {
        transition: all 0.2s;
    }
    .btn-outline-primary:hover, .btn-outline-success:hover, .btn-outline-warning:hover, .btn-outline-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1);
    }
    .card {
        border-radius: 12px;
    }
    .table th {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
</style>
@endsection
