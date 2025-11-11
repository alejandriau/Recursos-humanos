@extends('dashboard')

@section('title', 'Estado de Documentación')
@section('header-title', 'Estado de Documentación del Personal')

@section('contenido')
<div class="space-y-6">
    <!-- Estadísticas Documentales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Personal</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $personas->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Documentación Completa</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $estadisticasDocumentos['completos'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Documentación Incompleta</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $estadisticasDocumentos['incompletos'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">% Completitud</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $estadisticasDocumentos['porcentaje_completos'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Estado Documental -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Estado General de Documentación</h3>
            <div class="h-64">
                <canvas id="documentacionChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Distribución por Tipo de Documento</h3>
            <div class="space-y-4">
                @php
                    $conDiploma = $personas->filter(function($persona) {
                        return $persona->profesiones->whereNotNull('pdfDiploma')->isNotEmpty();
                    })->count();

                    $conProvision = $personas->filter(function($persona) {
                        return $persona->profesiones->whereNotNull('pdfProvision')->isNotEmpty();
                    })->count();

                    $conCedula = $personas->filter(function($persona) {
                        return $persona->profesiones->whereNotNull('pdfcedulap')->isNotEmpty();
                    })->count();
                @endphp

                <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Diploma Académico</span>
                        <span>{{ $conDiploma }}/{{ $personas->count() }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($conDiploma / $personas->count()) * 100 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Provisión Nacional</span>
                        <span>{{ $conProvision }}/{{ $personas->count() }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($conProvision / $personas->count()) * 100 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Cédula Profesional</span>
                        <span>{{ $conCedula }}/{{ $personas->count() }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ ($conCedula / $personas->count()) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Documentación -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Detalle de Documentación por Persona</h3>
                <form method="GET" action="{{ route('reportes.estado-documentacion') }}">
                    <input type="hidden" name="exportar" value="excel">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre Completo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Puesto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diploma</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provisión</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cédula</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($personas as $persona)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $persona->ci }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $persona->nombre_completo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $persona->puesto ? $persona->puesto->denominacion : 'Sin puesto' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $persona->puesto && $persona->puesto->unidadOrganizacional ? $persona->puesto->unidadOrganizacional->denominacion : 'Sin unidad' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            @if($persona->profesiones->whereNotNull('pdfDiploma')->isNotEmpty())
                                <i class="fas fa-check text-green-500"></i>
                            @else
                                <i class="fas fa-times text-red-500"></i>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            @if($persona->profesiones->whereNotNull('pdfProvision')->isNotEmpty())
                                <i class="fas fa-check text-green-500"></i>
                            @else
                                <i class="fas fa-times text-red-500"></i>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            @if($persona->profesiones->whereNotNull('pdfcedulap')->isNotEmpty())
                                <i class="fas fa-check text-green-500"></i>
                            @else
                                <i class="fas fa-times text-red-500"></i>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($persona->documentacion_completa)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Completa
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                    Incompleta
                                </span>
                                <div class="text-xs text-red-600 mt-1">
                                    {{ implode(', ', $persona->documentos_faltantes) }}
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const docCtx = document.getElementById('documentacionChart').getContext('2d');

    new Chart(docCtx, {
        type: 'doughnut',
        data: {
            labels: ['Completa', 'Incompleta'],
            datasets: [{
                data: [
                    {{ $estadisticasDocumentos['completos'] }},
                    {{ $estadisticasDocumentos['incompletos'] }}
                ],
                backgroundColor: ['#10B981', '#EF4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
