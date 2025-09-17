@extends('dashboard')

@section('contenidouno')
    <title>Estadísticas del Sistema</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('contenido')
<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Estadísticas del Sistema
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Resumen General -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6>Total Designaciones</h6>
                                    <h3>{{ $stats['total_designaciones'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6>Designaciones Activas</h6>
                                    <h3>{{ $stats['activos'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6>Puestos Vacíos</h6>
                                    <h3>{{ $stats['puestos_vacios'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h6>Designaciones Concluidas</h6>
                                    <h3>{{ $stats['total_designaciones'] - $stats['activos'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div class="row">
                        <!-- Por Tipo de Movimiento -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">Distribución por Tipo de Movimiento</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="movimientoChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Por Tipo de Contrato -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">Distribución por Tipo de Contrato</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="contratoChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tablas Detalladas -->
                    <div class="row">
                        <!-- Por Tipo de Movimiento -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">Detalle por Tipo de Movimiento</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Tipo de Movimiento</th>
                                                    <th>Cantidad</th>
                                                    <th>Porcentaje</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['por_tipo_movimiento'] as $item)
                                                <tr>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $item->tipo_movimiento)) }}</td>
                                                    <td>{{ $item->total }}</td>
                                                    <td>{{ number_format(($item->total / $stats['total_designaciones']) * 100, 1) }}%</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Por Tipo de Contrato -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">Detalle por Tipo de Contrato</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Tipo de Contrato</th>
                                                    <th>Cantidad</th>
                                                    <th>Porcentaje</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['por_tipo_contrato'] as $item)
                                                <tr>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $item->tipo_contrato)) }}</td>
                                                    <td>{{ $item->total }}</td>
                                                    <td>{{ number_format(($item->total / $stats['total_designaciones']) * 100, 1) }}%</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para los gráficos
    const movimientoData = {
        labels: {!! json_encode($stats['por_tipo_movimiento']->pluck('tipo_movimiento')->map(function($item) {
            return ucfirst(str_replace('_', ' ', $item));
        })) !!},
        datasets: [{
            data: {!! json_encode($stats['por_tipo_movimiento']->pluck('total')) !!},
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
            ]
        }]
    };

    const contratoData = {
        labels: {!! json_encode($stats['por_tipo_contrato']->pluck('tipo_contrato')->map(function($item) {
            return ucfirst(str_replace('_', ' ', $item));
        })) !!},
        datasets: [{
            data: {!! json_encode($stats['por_tipo_contrato']->pluck('total')) !!},
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
            ]
        }]
    };

    // Crear gráficos
    new Chart(document.getElementById('movimientoChart'), {
        type: 'pie',
        data: movimientoData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    new Chart(document.getElementById('contratoChart'), {
        type: 'doughnut',
        data: contratoData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
});
</script>
@endsection
