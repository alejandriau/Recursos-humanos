@extends('dashboard')

@section('title', 'Organigrama')
@section('header-title', 'Organigrama de la Empresa')

@section('contenido')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Estructura Organizacional
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Vista completa del organigrama de la empresa
                </p>
            </div>
            <div class="flex space-x-3">
                <button onclick="window.print()"
                        class="no-print inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
                <a href="{{ route('unidades.index') }}"
                   class="no-print inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-list mr-2"></i>Ver Lista
                </a>
            </div>
        </div>
    </div>

    <div class="px-4 py-5 sm:p-6">
        <!-- Leyenda -->
        <div class="no-print mb-6 bg-gray-50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Leyenda de Tipos:</h4>
            <div class="flex flex-wrap gap-3">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-600 rounded-full mr-2"></div>
                    <span class="text-xs text-gray-600">Dirección</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-xs text-gray-600">Gerencia</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-xs text-gray-600">Subdirección</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                    <span class="text-xs text-gray-600">Coordinación</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-indigo-500 rounded-full mr-2"></div>
                    <span class="text-xs text-gray-600">Departamento</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-pink-500 rounded-full mr-2"></div>
                    <span class="text-xs text-gray-600">Área</span>
                </div>
            </div>
        </div>

        <div class="organigrama-container overflow-x-auto">
            @foreach($raices as $unidad)
                @include('admin.unidades.partials.nodo-unidad', ['unidad' => $unidad, 'nivel' => 0])
            @endforeach
        </div>
    </div>
</div>

<style>
.organigrama-container {
    min-width: 800px;
}

.nodo-unidad {
    transition: all 0.3s ease;
    position: relative;
}

.nodo-unidad:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.conexion {
    position: relative;
}

.conexion::before {
    content: '';
    position: absolute;
    left: -1rem;
    top: 50%;
    width: 1rem;
    height: 2px;
    background-color: #d1d5db;
}

.conexion-hijos::before {
    content: '';
    position: absolute;
    left: 50%;
    top: -1rem;
    width: 2px;
    height: 1rem;
    background-color: #d1d5db;
}

@media print {
    .no-print {
        display: none;
    }

    .organigrama-container {
        overflow: visible;
        min-width: auto;
    }

    .nodo-unidad {
        break-inside: avoid;
    }
}
</style>
@endsection
