@extends('dashboard')

@section('title', 'Reporte de Validaciones')
@section('header', 'Reporte Estadístico de Validaciones')

@section('contenido')
<div class="container-fluid fade-in">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>
                        Filtros del Reporte
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" name="fechaInicio" class="form-control" value="{{ request('fechaInicio') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" name="fechaFin" class="form-control" value="{{ request('fechaFin') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-chart-line me-1"></i> Generar Reporte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tarjetas de Resumen -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">Total Validaciones</h6>
                                    <h2 class="mt-2 mb-0">{{ $total }}</h2>
                                </div>
                                <i class="fas fa-chart-line fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">Cumplen Perfil</h6>
                                    <h2 class="mt-2 mb-0">{{ $cumplen }}</h2>
                                </div>
                                <i class="fas fa-check-circle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">No Cumplen</h6>
                                    <h2 class="mt-2 mb-0">{{ $noCumplen }}</h2>
                                </div>
                                <i class="fas fa-times-circle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">Tasa de Cumplimiento</h6>
                                    <h2 class="mt-2 mb-0">{{ number_format($porcentajeCumplimiento, 1) }}%</h2>
                                </div>
                                <i class="fas fa-percent fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de Cumplimiento por Puesto -->
            @if($porPuesto->count() > 0)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie me-2 text-primary"></i>
                                Cumplimiento por Puesto
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartPorPuesto" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Tabla Detallada -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2 text-primary"></i>
                        Detalle de Validaciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaReporte">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Puesto</th>
                                    <th>Candidato</th>
                                    <th>Resultado</th>
                                    <th>Formación</th>
                                    <th>Experiencia</th>
                                    <th>Provisión</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($validaciones as $validacion)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($validacion->fechaValidacion)->format('d/m/Y') }}</td>
                                    <td>{{ $validacion->puesto->denominacion ?? 'N/A' }}</td>
                                    <td>{{ $validacion->persona->nombre_completo ?? 'N/A' }}</td>
                                    <td>
                                        @if($validacion->resultado)
                                            <span class="badge-cumple">CUMPLE</span>
                                        @else
                                            <span class="badge-no-cumple">NO CUMPLE</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $detalle = $validacion->detalleValidacion;
                                            $cumpleFormacion = $detalle['formacion']['cumple'] ?? false;
                                        @endphp
                                        <span class="badge {{ $cumpleFormacion ? 'bg-success' : 'bg-danger' }}">
                                            {{ $cumpleFormacion ? '✓' : '✗' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $cumpleExp = $detalle['experiencia']['cumple'] ?? false;
                                        @endphp
                                        <span class="badge {{ $cumpleExp ? 'bg-success' : 'bg-danger' }}">
                                            {{ $cumpleExp ? '✓' : '✗' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $cumpleProv = $detalle['titulo_provision']['cumple'] ?? false;
                                        @endphp
                                        <span class="badge {{ $cumpleProv ? 'bg-success' : 'bg-danger' }}">
                                            {{ $cumpleProv ? '✓' : '✗' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="fas fa-print me-1"></i> Imprimir Reporte
                    </button>
                    <button onclick="exportToExcel()" class="btn btn-success">
                        <i class="fas fa-file-excel me-1"></i> Exportar a Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('#tablaReporte').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [[0, 'desc']],
            pageLength: 25
        });
        
        @if($porPuesto->count() > 0)
        // Gráfico de cumplimiento por puesto
        var ctx = document.getElementById('chartPorPuesto').getContext('2d');
        var labels = @json($porPuesto->pluck('puesto'));
        var data = @json($porPuesto->pluck('porcentaje'));
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tasa de Cumplimiento (%)',
                    data: data,
                    backgroundColor: function(context) {
                        var value = context.dataset.data[context.dataIndex];
                        if (value >= 80) return 'rgba(40, 167, 69, 0.7)';
                        if (value >= 50) return 'rgba(255, 193, 7, 0.7)';
                        return 'rgba(220, 53, 69, 0.7)';
                    },
                    borderColor: 'rgba(0,0,0,0.1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        title: {
                            display: true,
                            text: 'Porcentaje de Cumplimiento (%)'
                        },
                        max: 100,
                        min: 0
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Puestos'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw.toFixed(1) + '% de candidatos cumplen';
                            }
                        }
                    }
                }
            }
        });
        @endif
    });
    
    function exportToExcel() {
        var table = document.getElementById('tablaReporte');
        var html = table.outerHTML;
        var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        var link = document.createElement('a');
        link.download = 'reporte_validaciones.xls';
        link.href = url;
        link.click();
    }
</script>
@endpush
@endsection