@extends('layouts.baseadm')

@section('title', 'Reporte - ' . $actividad->nombre)

@section('content')

<div class="mb-5 flex items-center justify-between flex-wrap gap-3">
    <div>
        <a href="{{ route('actividades.show', $actividad) }}" class="text-sm text-sky-600 hover:underline">
            ← Volver a la actividad
        </a>
        <h1 class="text-2xl font-bold text-gray-800 mt-1">Reporte de asistencia</h1>
        <p class="text-sm text-gray-500">
            {{ $actividad->nombre }} · {{ $actividad->fecha->format('d/m/Y') }}
        </p>
    </div>
    <button onclick="window.print()"
            class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-semibold print:hidden">
        🖨️ Imprimir
    </button>
</div>

{{-- Resumen --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-xs text-gray-500 uppercase">Total</p>
        <p class="text-2xl font-bold text-gray-800">{{ $resumen['total'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-xs text-green-600 uppercase">Presentes</p>
        <p class="text-2xl font-bold text-green-700">{{ $resumen['presentes'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-xs text-yellow-600 uppercase">Tardanzas</p>
        <p class="text-2xl font-bold text-yellow-700">{{ $resumen['tardanzas'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-xs text-blue-600 uppercase">Justificados</p>
        <p class="text-2xl font-bold text-blue-700">{{ $resumen['justificados'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-xs text-red-600 uppercase">Anulados</p>
        <p class="text-2xl font-bold text-red-700">{{ $resumen['anulados'] }}</p>
    </div>
</div>

{{-- Tabla --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="text-left px-4 py-3">#</th>
                <th class="text-left px-4 py-3">Nombre</th>
                <th class="text-left px-4 py-3">CI</th>
                <th class="text-left px-4 py-3">Hora</th>
                <th class="text-center px-4 py-3">Estado</th>
                <th class="text-center px-4 py-3">Método</th>
                <th class="text-left px-4 py-3">Registrado por</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($asistencias as $i => $a)
                <tr class="{{ $a->estado == 0 ? 'opacity-50' : '' }}">
                    <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $a->persona->nombre_completo ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $a->persona->ci ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $a->hora_registro->format('H:i:s') }}</td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $colores = [
                                1 => 'bg-green-100 text-green-800',
                                2 => 'bg-yellow-100 text-yellow-800',
                                3 => 'bg-blue-100 text-blue-800',
                                4 => 'bg-red-100 text-red-800',
                                0 => 'bg-gray-200 text-gray-700',
                            ];
                        @endphp
                        <span class="{{ $colores[$a->estado] ?? 'bg-gray-100' }} px-2 py-1 rounded-full text-xs font-semibold">
                            {{ $a->estado_texto }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-xs text-gray-500">
                        {{ $a->metodo_texto }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $a->registrador->name ?? '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500">
                        No hay asistencias para mostrar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    @media print {
        nav, .print\:hidden { display: none !important; }
        body { background: white; }
    }
</style>

@endsection