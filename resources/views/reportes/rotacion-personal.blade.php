@extends('dashboard')

@section('title', 'Rotación de Personal')
@section('header-title', 'Rotación de Personal')

@section('contenido')
<div class="space-y-6">
    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Seleccionar Período</h3>
        <form method="GET" action="{{ route('reportes.rotacion-personal') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Mes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mes</label>
                <select name="mes" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @foreach(range(1, 12) as $mes)
                        <option value="{{ $mes }}" {{ $mes == $mesSeleccionado ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $mes)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Año -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                <select name="anio" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @foreach(range(now()->year - 2, now()->year) as $anio)
                        <option value="{{ $anio }}" {{ $anio == $anioSeleccionado ? 'selected' : '' }}>
                            {{ $anio }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Botones -->
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-chart-bar mr-2"></i>Generar Reporte
                </button>
            </div>
        </form>
    </div>

    <!-- Estadísticas del Mes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Altas del Mes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $altas }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-user-minus"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Bajas del Mes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $bajas }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tasa de Rotación</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $tasaRotacion }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Rotación Anual -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Rotación Mensual - Año {{ $anioSeleccionado }}</h3>
        <div class="h-80">
            <canvas id="rotacionChart"></canvas>
        </div>
    </div>

    <!-- Interpretación -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Interpretación de la Tasa de Rotación</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p><strong>{{ $tasaRotacion }}%</strong> -
                    @if($tasaRotacion < 5)
                        <span class="text-green-600">Excelente: Rotación muy baja</span>
                    @elseif($tasaRotacion < 10)
                        <span class="text-yellow-600">Aceptable: Rotación dentro de parámetros normales</span>
                    @else
                        <span class="text-red-600">Alerta: Rotación elevada, requiere atención</span>
                    @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rotacionCtx = document.getElementById('rotacionChart').getContext('2d');
    const rotacionData = {!! json_encode($rotacionMensual) !!};

    new Chart(rotacionCtx, {
        type: 'bar',
        data: {
            labels: rotacionData.map(item => item.mes),
            datasets: [
                {
                    label: 'Altas',
                    data: rotacionData.map(item => item.altas),
                    backgroundColor: '#10B981',
                    borderColor: '#10B981',
                    borderWidth: 1
                },
                {
                    label: 'Bajas',
                    data: rotacionData.map(item => item.bajas),
                    backgroundColor: '#EF4444',
                    borderColor: '#EF4444',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad de Personas'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Meses'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
});
</script>
@endpush
