@extends('dashboard')

@section('contenidouno')
    <title>Generar Reportes</title>
    <style>
        .report-card {
            border-left: 4px solid #6f42c1;
        }
        .report-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
    </style>
@endsection

@section('contenido')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>Generar Reportes
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Reporte de Personal -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card report-card h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-users me-2"></i>Reporte de Personal
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="report-icon text-info">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <h5>Reporte Completo de Personal</h5>
                                    <p class="text-muted">Incluye todos los puestos con su personal asignado</p>

                                    <form action="{{ route('reportes.personal') }}" method="GET" class="mt-3">
                                        <div class="mb-3">
                                            <label class="form-label">Filtrar por:</label>
                                            <select name="tipo_movimiento" class="form-select mb-2">
                                                <option value="">Todos los tipos</option>
                                                @foreach(['designacion_inicial','movilidad','ascenso','comision','interinato'] as $tipo)
                                                    <option value="{{ $tipo }}">{{ ucfirst(str_replace('_', ' ', $tipo)) }}</option>
                                                @endforeach
                                            </select>
                                            <select name="estado" class="form-select">
                                                <option value="">Todos los estados</option>
                                                <option value="activo">Solo activos</option>
                                                <option value="concluido">Solo concluidos</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="fas fa-download me-2"></i>Descargar PDF
                                        </button>
                                    </form>

                                    <form action="{{ route('reportes.excel') }}" method="GET" class="mt-2">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-file-excel me-2"></i>Descargar Excel
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Reporte de Designaciones -->
                        <div class="col-md-6">
                            <div class="card report-card h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-history me-2"></i>Reporte de Designaciones
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="report-icon text-success">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <h5>Reporte Histórico</h5>
                                    <p class="text-muted">Todas las designaciones en un período específico</p>

                                    <form action="{{ route('reportes.designaciones') }}" method="GET" class="mt-3">
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Desde:</label>
                                                <input type="date" name="fecha_inicio" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Hasta:</label>
                                                <input type="date" name="fecha_fin" class="form-control">
                                            </div>
                                        </div>
                                        <select name="tipo_movimiento" class="form-select mb-2">
                                            <option value="">Todos los tipos</option>
                                            @foreach(['designacion_inicial','movilidad','ascenso','comision','interinato'] as $tipo)
                                                <option value="{{ $tipo }}">{{ ucfirst(str_replace('_', ' ', $tipo)) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-download me-2"></i>Generar Reporte
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reporte de Estadísticas -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card report-card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Reporte de Estadísticas
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="report-icon text-warning">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <h5>Estadísticas del Sistema</h5>
                                    <p class="text-muted">Métricas y análisis del personal</p>

                                    <a href="{{ route('reportes.estadisticas') }}" class="btn btn-warning w-100 mt-3">
                                        <i class="fas fa-download me-2"></i>Descargar Estadísticas
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Reporte de Puestos Vacíos -->
                        <div class="col-md-6">
                            <div class="card report-card h-100">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-user-times me-2"></i>Reporte de Vacantes
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="report-icon text-danger">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <h5>Puestos Vacíos</h5>
                                    <p class="text-muted">Listado de puestos disponibles</p>

                                    <a href="{{ route('historial.vacios') }}" class="btn btn-outline-danger w-100 mt-3">
                                        <i class="fas fa-eye me-2"></i>Ver Vacantes
                                    </a>
                                    <button class="btn btn-secondary w-100 mt-2">
                                        <i class="fas fa-print me-2"></i>Imprimir Listado
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
