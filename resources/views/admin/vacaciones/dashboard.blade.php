{{-- resources/views/vacaciones/rrhh/dashboard.blade.php --}}
@extends('layouts.baseadm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Dashboard de Vacaciones</h1>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total Empleados</h5>
                    <h2>{{ $estadisticas['total_empleados'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>En Vacaciones Hoy</h5>
                    <h2>{{ $estadisticas['en_vacaciones'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Sin Saldo</h5>
                    <h2>{{ $estadisticas['sin_saldo'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5>Con Vencimientos</h5>
                    <h2>{{ $estadisticas['con_saldo_vencido'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Próximas vacaciones -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Próximas Vacaciones</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Días</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proximasVacaciones as $vacacion)
                            <tr>
                                <td>{{ $vacacion->persona->full_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($vacacion->fechasal)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($vacacion->fecharet)->format('d/m/Y') }}</td>
                                <td>{{ $vacacion->cantidad }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Saldo bajo -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Alertas - Saldo Bajo</h5>
                </div>
                <div class="card-body">
                    @foreach($saldoBajo as $item)
                    <div class="alert alert-warning">
                        <strong>{{ $item->persona->full_name }}</strong>
                        <span class="badge badge-danger float-right">{{ $item->total_saldo }} días</span>
                    </div>
                    @endforeach
                    @if($saldoBajo->isEmpty())
                        <p class="text-success">✓ Todos los empleados tienen saldo suficiente</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de vacaciones por mes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Vacaciones por Mes - {{ now()->year }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="vacacionesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('vacacionesChart').getContext('2d');
    const data = @json($vacacionesPorMes);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Vacaciones Aprobadas',
                data: data.map(item => item.total),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
