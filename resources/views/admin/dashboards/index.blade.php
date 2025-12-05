@extends('dashboard')

@section('title', 'Dashboard - Sistema Organizacional')
@section('header-title', 'Dashboard')

@section('contenido')
<div class="space-y-6">
    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Unidades -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-building text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Unidades</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['total_unidades'] }}</p>
                </div>
            </div>
        </div>

        <!-- Total Puestos -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-tie text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Puestos</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['total_puestos'] }}</p>
                </div>
            </div>
        </div>

        <!-- Puestos Vacantes -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-briefcase text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Puestos Vacantes</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['puestos_vacantes'] }}</p>
                </div>
            </div>
        </div>

        <!-- Jefaturas -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-crown text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Jefaturas</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['jefaturas'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda Fila de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Puestos Ocupados -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-check text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Puestos Ocupados</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['puestos_ocupados'] }}</p>
                </div>
            </div>
        </div>

        <!-- Jefaturas Vacantes -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-crown text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Jefaturas Vacantes</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['jefaturas_vacantes'] }}</p>
                </div>
            </div>
        </div>

        <!-- Tasa de Ocupación -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-teal-100 text-teal-600">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Tasa de Ocupación</h3>
                    <p class="text-2xl font-semibold text-gray-900">
                        @if($estadisticas['total_puestos'] > 0)
                            {{ number_format(($estadisticas['puestos_ocupados'] / $estadisticas['total_puestos']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Movimientos -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <i class="fas fa-exchange-alt text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Movimientos</h3>
                    <p class="text-2xl font-semibold text-gray-900">{{ $estadisticas['movimientos_recientes']->count() }}+</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Distribuciones -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Distribución por Tipo de Unidad -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Distribución por Tipo de Unidad</h3>
            <div class="space-y-3">
                @foreach($estadisticas['unidades_por_tipo'] as $tipo)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium">{{ $tipo->tipo }}</span>
                        <span class="text-sm font-medium">{{ $tipo->total }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full"
                             style="width: {{ ($tipo->total / $estadisticas['total_unidades']) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
                @if($estadisticas['unidades_por_tipo']->isEmpty())
                    <p class="text-sm text-gray-500 text-center">No hay unidades registradas</p>
                @endif
            </div>
        </div>

        <!-- Distribución por Tipo de Contrato -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Distribución por Tipo de Contrato</h3>
            <div class="space-y-3">
                @foreach($estadisticas['puestos_por_contrato'] as $contrato)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium">{{ $contrato->tipoContrato }}</span>
                        <span class="text-sm font-medium">{{ $contrato->total }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full"
                             style="width: {{ ($contrato->total / $estadisticas['total_puestos']) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
                @if($estadisticas['puestos_por_contrato']->isEmpty())
                    <p class="text-sm text-gray-500 text-center">No hay puestos registrados</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Últimos Registros -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Últimas Unidades Creadas -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Últimas Unidades Creadas</h3>
                <a href="{{ route('unidades.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Ver todas →
                </a>
            </div>
            <div class="space-y-3">
                @foreach($ultimasUnidades as $unidad)
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ $unidad->denominacion }}</h4>
                    </div>
                    <a href="{{ route('unidades.show', $unidad) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                @endforeach
                @if($ultimasUnidades->isEmpty())
                    <p class="text-sm text-gray-500 text-center">No hay unidades recientes</p>
                @endif
            </div>
        </div>

        <!-- Últimos Puestos Creados -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Últimos Puestos Creados</h3>
                <a href="{{ route('puestos.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Ver todos →
                </a>
            </div>
            <div class="space-y-3">
                @foreach($ultimosPuestos as $puesto)
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ $puesto->denominacion }}</h4>
                        <p class="text-xs text-gray-500">
                            {{ $puesto->unidadOrganizacional->denominacion }} •
                            {{ $puesto->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <a href="{{ route('puestos.show', $puesto) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                @endforeach
                @if($ultimosPuestos->isEmpty())
                    <p class="text-sm text-gray-500 text-center">No hay puestos recientes</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('unidades.create') }}"
               class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                <i class="fas fa-plus-circle text-blue-500 text-xl mb-2"></i>
                <span class="text-sm font-medium">Nueva Unidad</span>
            </a>
            <a href="{{ route('puestos.create') }}"
               class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition">
                <i class="fas fa-user-plus text-green-500 text-xl mb-2"></i>
                <span class="text-sm font-medium">Nuevo Puesto</span>
            </a>
            <a href="{{ route('historial.vacio') }}"
               class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition">
                <i class="fas fa-search text-yellow-500 text-xl mb-2"></i>
                <span class="text-sm font-medium">Ver Vacantes</span>
            </a>
            <a href="{{ route('unidades.arbol') }}"
               class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition">
                <i class="fas fa-sitemap text-purple-500 text-xl mb-2"></i>
                <span class="text-sm font-medium">Organigrama</span>
            </a>
        </div>
    </div>
</div>
@endsection
