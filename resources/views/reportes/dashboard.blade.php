@extends('dashboard')

@section('title', 'Dashboard de Reportes')
@section('header-title', 'Dashboard de Reportes')

@section('contenido')
<div class="space-y-6">
    <!-- Navegación entre Reportes -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Módulos de Reportes</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('reportes.censo-laboral') }}"
               class="bg-blue-50 border border-blue-200 rounded-lg p-4 hover:bg-blue-100 transition-colors">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-medium text-blue-900">Censo Laboral</h3>
                        <p class="text-sm text-blue-600">Personal activo e inactivo</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('reportes.distribucion-unidades') }}"
               class="bg-green-50 border border-green-200 rounded-lg p-4 hover:bg-green-100 transition-colors">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-medium text-green-900">Distribución</h3>
                        <p class="text-sm text-green-600">Por unidades organizacionales</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('reportes.rotacion-personal') }}"
               class="bg-purple-50 border border-purple-200 rounded-lg p-4 hover:bg-purple-100 transition-colors">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-medium text-purple-900">Rotación</h3>
                        <p class="text-sm text-purple-600">Altas, bajas y tasas</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('reportes.estado-documentacion') }}"
               class="bg-orange-50 border border-orange-200 rounded-lg p-4 hover:bg-orange-100 transition-colors">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-medium text-orange-900">Documentación</h3>
                        <p class="text-sm text-orange-600">Estado documental</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas Principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Personal</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total_personal'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-briefcase text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Puestos Ocupados</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['puestos_ocupados'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-chair text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Puestos Vacantes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['puestos_vacantes'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Antigüedad Promedio</p>
                    <p class="text-2xl font-bold text-gray-900">{{ round($estadisticas['antiguedad_promedio']) }} años</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Distribución -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Distribución por Género -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Distribución por Género</h3>
            <div class="h-64">
                <canvas id="sexoChart"></canvas>
            </div>
        </div>

        <!-- Evolución de Personal -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Evolución del Personal (Últimos 12 meses)</h3>
            <div class="h-64">
                <canvas id="evolucionChart"></canvas>
            </div>
        </div>
    </div>




<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">
        Pasivo ex Cordeco y GADC
    </h2>

    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Pasivo Uno -->
        <div class="flex flex-1 items-center justify-between bg-blue-100 border border-blue-200 rounded-xl p-4 hover:bg-blue-200 transition-colors shadow-sm">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-500 text-white shadow">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <h3 class="ml-3 text-blue-900 font-semibold text-base">
                    Pasivo Uno
                </h3>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('reportes.pasivouno.pdf') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm font-medium flex items-center transition shadow-sm">
                    <i class="fas fa-file-pdf mr-2 text-red-200"></i>PDF por Letra
                </a>
                <a href="{{ route('reportes.pasivouno.excel') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md text-sm font-medium flex items-center transition shadow-sm">
                    <i class="fas fa-file-excel mr-2 text-green-200"></i>Excel Completo
                </a>
            </div>
        </div>

        <!-- Pasivo Dos -->
        <div class="flex flex-1 items-center justify-between bg-blue-100 border border-blue-200 rounded-xl p-4 hover:bg-blue-200 transition-colors shadow-sm">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-500 text-white shadow">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <h3 class="ml-3 text-blue-900 font-semibold text-base">
                    Pasivo Dos
                </h3>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('reportes.pasivodos.pdf') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm font-medium flex items-center transition shadow-sm">
                    <i class="fas fa-file-pdf mr-2 text-red-200"></i>PDF por Letra
                </a>
                <a href="{{ route('reportes.pasivodos.excel') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md text-sm font-medium flex items-center transition shadow-sm">
                    <i class="fas fa-file-excel mr-2 text-green-200"></i>Excel Completo
                </a>
            </div>
        </div>

    </div>
</div>





<!-- Para Pasivo Uno -->
<a href="{{ route('reportes.pasivouno.pdf') }}"
   class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg text-sm font-medium text-center transition-colors">
    <i class="fas fa-file-pdf mr-2"></i>PDF por Letra
</a>
<a href="{{ route('reportes.pasivouno.excel') }}"
   class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium text-center transition-colors">
    <i class="fas fa-file-excel mr-2"></i>Excel Completo
</a>

<!-- Para Pasivo Dos -->
<a href="{{ route('reportes.pasivodos.pdf') }}"
   class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg text-sm font-medium text-center transition-colors">
    <i class="fas fa-file-pdf mr-2"></i>PDF por Letra
</a>
<a href="{{ route('reportes.pasivodos.excel') }}"
   class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium text-center transition-colors">
    <i class="fas fa-file-excel mr-2"></i>Excel Completo
</a>




    <!-- Distribución por Unidades -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Distribución por Unidades Organizacionales</h3>
            <a href="{{ route('reportes.distribucion-unidades') }}"
               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Ver reporte completo →
            </a>
        </div>


        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Puestos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ocupados</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vacantes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">% Ocupación</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($distribucionUnidades as $distribucion)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $distribucion['unidad'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $distribucion['total_puestos'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $distribucion['puestos_ocupados'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $distribucion['vacantes'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($distribucion['porcentaje_ocupacion'] >= 80) bg-green-100 text-green-800
                                @elseif($distribucion['porcentaje_ocupacion'] >= 50) bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $distribucion['porcentaje_ocupacion'] }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Cargar Chart.js primero -->

<script>
// Esperar a que jQuery esté disponible
function initializeCharts() {
    console.log('✅ Inicializando gráficos...');

    // Verificar que Chart.js esté cargado
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js no está cargado');
        return;
    }

    // Gráfico de distribución por género
    const sexoCtx = document.getElementById('sexoChart');
    if (sexoCtx) {
        const sexoData = {!! json_encode($estadisticas['distribucion_sexo']) !!};

        if (sexoData && Object.keys(sexoData).length > 0) {
            console.log('Datos para gráfico de género:', sexoData);

            new Chart(sexoCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(sexoData),
                    datasets: [{
                        data: Object.values(sexoData),
                        backgroundColor: ['#3B82F6', '#EF4444', '#10B981']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        } else {
            sexoCtx.innerHTML = '<p class="text-gray-500 text-center">No hay datos</p>';
        }
    }

    // Gráfico de evolución
    const evolucionCtx = document.getElementById('evolucionChart');
    if (evolucionCtx) {
        const evolucionData = {!! json_encode($evolucionPersonal) !!};

        if (evolucionData && evolucionData.length > 0) {
            console.log('Datos para gráfico de evolución:', evolucionData);

            new Chart(evolucionCtx, {
                type: 'line',
                data: {
                    labels: evolucionData.map(item => item.mes),
                    datasets: [{
                        label: 'Total Personal',
                        data: evolucionData.map(item => item.total),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        } else {
            evolucionCtx.innerHTML = '<p class="text-gray-500 text-center">No hay datos</p>';
        }
    }
}

// Esperar a que el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCharts);
} else {
    initializeCharts();
}
</script>
@endsection

@push('scripts')
@endpush
