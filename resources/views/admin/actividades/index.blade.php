@extends('layouts.baseadm')

@section('title', 'Actividades')

@section('content')

<div class=" mx-auto">

    {{-- ═══════════════════════════════════════════════
         ENCABEZADO
    ═══════════════════════════════════════════════ --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-xl lg:text-3xl font-bold text-gray-800 leading-tight">
                Actividades
            </h1>
            <p class="text-xs lg:text-sm text-gray-500 mt-0.5">
                Eventos externos y control de asistencia
            </p>
        </div>
        <a href="{{ route('actividades.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white px-4 py-3 lg:py-2.5 rounded-lg text-sm font-semibold text-center sm:w-auto whitespace-nowrap shadow-sm transition">
            <span class="text-base leading-none">+</span>
            <span>Nueva actividad</span>
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════
         FILTROS DE BÚSQUEDA
    ═══════════════════════════════════════════════ --}}
    <div class="bg-white rounded-lg shadow mb-5 p-3 lg:p-4">
        <form method="GET" action="{{ route('actividades.index') }}"
              class="flex flex-col sm:flex-row gap-2">
            {{-- Búsqueda por nombre/lugar --}}
            <div class="flex-1 relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none">
                    🔍
                </span>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Buscar por nombre o lugar…"
                       class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 lg:py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition">
            </div>

            {{-- Filtro estado --}}
            <select name="estado"
                    class="border border-gray-300 rounded-lg px-3 py-2.5 lg:py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition">
                <option value="">Todos los estados</option>
                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activas</option>
                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Cerradas</option>
            </select>

            {{-- Filtro fecha --}}
            <input type="date" name="fecha" value="{{ request('fecha') }}"
                   class="border border-gray-300 rounded-lg px-3 py-2.5 lg:py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition">

            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 sm:flex-initial bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white px-4 py-2.5 lg:py-2 rounded-lg text-sm font-semibold whitespace-nowrap shadow-sm transition">
                    Filtrar
                </button>
                @if(request()->hasAny(['q', 'estado', 'fecha']))
                    <a href="{{ route('actividades.index') }}"
                       class="flex-1 sm:flex-initial bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 px-4 py-2.5 lg:py-2 rounded-lg text-sm font-semibold text-center whitespace-nowrap transition">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════════
         SIN RESULTADOS
    ═══════════════════════════════════════════════ --}}
    @if($actividades->count() === 0)

        <div class="bg-white rounded-lg shadow p-8 lg:p-16 text-center text-gray-500">
            <p class="text-4xl lg:text-6xl mb-3">
                {{ request()->hasAny(['q', 'estado', 'fecha']) ? '🔍' : '📭' }}
            </p>
            <p class="text-sm lg:text-base">
                @if(request()->hasAny(['q', 'estado', 'fecha']))
                    No se encontraron actividades con esos filtros.
                @else
                    No hay actividades registradas todavía.
                @endif
            </p>
            @if(request()->hasAny(['q', 'estado', 'fecha']))
                <a href="{{ route('actividades.index') }}"
                   class="inline-block mt-4 text-sky-600 hover:underline text-sm font-semibold">
                    Ver todas las actividades
                </a>
            @else
                <a href="{{ route('actividades.create') }}"
                   class="inline-block mt-4 bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition">
                    + Crear la primera actividad
                </a>
            @endif
        </div>

    @else

        {{-- ═══════════ CONTADOR DE RESULTADOS ═══════════ --}}
        <p class="text-xs lg:text-sm text-gray-500 mb-3">
            Mostrando <strong class="text-gray-700">{{ $actividades->count() }}</strong>
            de <strong class="text-gray-700">{{ $actividades->total() }}</strong> actividades
        </p>

        {{-- ═══════════════════════════════════════════════
             📱 MÓVIL / TABLET: TARJETAS
        ═══════════════════════════════════════════════ --}}
        <div class="lg:hidden space-y-3">
            @foreach($actividades as $actividad)
                <div class="bg-white rounded-lg shadow overflow-hidden">

                    {{-- Cabecera --}}
                    <div class="p-3 flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('actividades.show', $actividad) }}"
                               class="font-semibold text-sky-700 hover:underline text-sm leading-tight block truncate">
                                {{ $actividad->nombre }}
                            </a>
                            <p class="text-xs text-gray-500 mt-1 flex items-center gap-2 flex-wrap">
                                <span>📅 {{ $actividad->fecha->format('d/m/Y') }}</span>
                                @if($actividad->hora_inicio)
                                    <span>· {{ \Carbon\Carbon::parse($actividad->hora_inicio)->format('H:i') }}</span>
                                @endif
                            </p>
                            @if($actividad->lugar)
                                <p class="text-xs text-gray-500 mt-0.5 truncate">
                                    📍 {{ $actividad->lugar }}
                                </p>
                            @endif
                        </div>

                        {{-- Badge de estado (con círculo nativo, no unicode) --}}
                        <div class="flex-shrink-0">
                            @if($actividad->estaActiva())
                                <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                    Activa
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                    Cerrada
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Métricas (asistencias clicables → reporte) --}}
                    <div class="grid grid-cols-2 divide-x divide-gray-100 text-center border-t border-b border-gray-100">
                        <a href="{{ route('actividades.reporte', $actividad) }}"
                           class="py-2.5 hover:bg-sky-50 active:bg-sky-100 transition">
                            <p class="text-lg font-bold text-sky-700 leading-none">
                                {{ $actividad->asistencias_count }}
                            </p>
                            <p class="text-[10px] text-gray-500 uppercase mt-0.5">Asistencias</p>
                        </a>
                        <div class="py-2.5">
                            <p class="text-lg font-bold text-gray-700 leading-none">
                                {{ $actividad->fechaRegistro?->diffForHumans(null, true) ?? '—' }}
                            </p>
                            <p class="text-[10px] text-gray-500 uppercase mt-0.5">Creada</p>
                        </div>
                    </div>

                    {{-- Acción principal: Escanear (solo si activa) --}}
                    @if($actividad->estaActiva())
                        <a href="{{ route('actividades.escaner', $actividad) }}"
                           class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white text-sm font-bold py-2.5 transition">
                            📷 Escanear asistencias
                        </a>
                    @endif

                    {{-- Botonera secundaria: Ver / Reporte / Editar --}}
                    <div class="grid grid-cols-3 gap-2 p-3 bg-gray-50">
                        <a href="{{ route('actividades.show', $actividad) }}"
                           class="flex flex-col items-center justify-center gap-0.5 bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white text-[11px] font-semibold py-2.5 rounded-lg shadow-sm transition">
                            <span class="text-base leading-none">👁</span>
                            <span>Ver</span>
                        </a>

                        <a href="{{ route('actividades.reporte', $actividad) }}"
                           class="flex flex-col items-center justify-center gap-0.5 bg-gray-600 hover:bg-gray-700 active:bg-gray-800 text-white text-[11px] font-semibold py-2.5 rounded-lg shadow-sm transition">
                            <span class="text-base leading-none">📊</span>
                            <span>Reporte</span>
                        </a>

                        <a href="{{ route('actividades.edit', $actividad) }}"
                           class="flex flex-col items-center justify-center gap-0.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white text-[11px] font-semibold py-2.5 rounded-lg shadow-sm transition">
                            <span class="text-base leading-none">✏️</span>
                            <span>Editar</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ═══════════════════════════════════════════════
             💻 PC: TABLA
        ═══════════════════════════════════════════════ --}}
        <div class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold">Actividad</th>
                            <th class="text-left px-5 py-3 font-semibold">Fecha</th>
                            <th class="text-left px-5 py-3 font-semibold">Lugar</th>
                            <th class="text-center px-5 py-3 font-semibold">Asistencias</th>
                            <th class="text-center px-5 py-3 font-semibold">Estado</th>
                            <th class="text-right px-5 py-3 font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($actividades as $actividad)
                            <tr class="hover:bg-gray-50 transition">
                                {{-- Actividad --}}
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('actividades.show', $actividad) }}"
                                       class="font-semibold text-sky-700 hover:underline">
                                        {{ $actividad->nombre }}
                                    </a>
                                    @if($actividad->descripcion)
                                        <p class="text-xs text-gray-500 truncate max-w-md mt-0.5">
                                            {{ $actividad->descripcion }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Fecha --}}
                                <td class="px-5 py-3.5 text-gray-700 whitespace-nowrap">
                                    {{ $actividad->fecha->format('d/m/Y') }}
                                    @if($actividad->hora_inicio)
                                        <span class="block text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($actividad->hora_inicio)->format('H:i') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Lugar --}}
                                <td class="px-5 py-3.5 text-gray-700">
                                    {{ $actividad->lugar ?? '—' }}
                                </td>

                                {{-- Asistencias (link al reporte) --}}
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('actividades.reporte', $actividad) }}"
                                       class="inline-block bg-sky-100 hover:bg-sky-200 text-sky-800 px-2.5 py-1 rounded-full text-xs font-semibold transition">
                                        {{ $actividad->asistencias_count }}
                                    </a>
                                </td>

                                {{-- Estado --}}
                                <td class="px-5 py-3.5 text-center">
                                    @if($actividad->estaActiva())
                                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-800 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                            Activa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 bg-gray-200 text-gray-700 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                            Cerrada
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-3.5 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        {{-- Ver --}}
                                        <a href="{{ route('actividades.show', $actividad) }}"
                                           class="inline-flex items-center gap-1.5 bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm transition">
                                            👁 <span>Ver</span>
                                        </a>

                                        {{-- Escanear (si activa) o Reporte (si cerrada) --}}
                                        @if($actividad->estaActiva())
                                            <a href="{{ route('actividades.escaner', $actividad) }}"
                                               class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm transition">
                                                📷 <span>Escanear</span>
                                            </a>
                                        @else
                                            <a href="{{ route('actividades.reporte', $actividad) }}"
                                               class="inline-flex items-center gap-1.5 bg-gray-600 hover:bg-gray-700 active:bg-gray-800 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm transition">
                                                📊 <span>Reporte</span>
                                            </a>
                                        @endif

                                        {{-- Editar --}}
                                        <a href="{{ route('actividades.edit', $actividad) }}"
                                           class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm transition">
                                            ✏️ <span>Editar</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ═══════════ PAGINACIÓN ═══════════ --}}
        <div class="mt-5">
            {{ $actividades->withQueryString()->links() }}
        </div>

    @endif
</div>

@endsection