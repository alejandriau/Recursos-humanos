@extends('layouts.baseadm')

@section('title', $actividad->nombre)

@section('content')

{{-- ═══════════════════════════════════════════════
     CONTENEDOR PRINCIPAL
     Móvil: 1 columna  |  PC: 2 columnas (2fr + 1fr)
═══════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto">

    {{-- ═══════════ ENCABEZADO ═══════════ --}}
    <div class="mb-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="min-w-0">
            <a href="{{ route('actividades.index') }}" class="text-xs text-sky-600 hover:underline">
                ← Volver al listado
            </a>
            <h1 class="text-xl lg:text-3xl font-bold text-gray-800 mt-1 leading-tight truncate">
                {{ $actividad->nombre }}
            </h1>
            <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                <span class="text-xs lg:text-sm text-gray-500">
                    📍 {{ $actividad->lugar ?? 'Sin lugar' }}
                </span>
                <span class="text-gray-300 hidden sm:inline">·</span>
                <span class="text-xs lg:text-sm text-gray-500">
                    📅 {{ $actividad->fecha->format('d/m/Y') }}
                    @if($actividad->hora_inicio)
                        · {{ \Carbon\Carbon::parse($actividad->hora_inicio)->format('H:i') }}
                    @endif
                </span>
                @if($actividad->estaActiva())
                    <span class="bg-green-100 text-green-700 text-[10px] lg:text-xs px-2 py-0.5 rounded-full font-semibold uppercase">
                        ● Activa
                    </span>
                @else
                    <span class="bg-gray-200 text-gray-700 text-[10px] lg:text-xs px-2 py-0.5 rounded-full font-semibold uppercase">
                        ○ Cerrada
                    </span>
                @endif
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="grid grid-cols-2 lg:flex lg:flex-wrap gap-2 lg:shrink-0">
            @if($actividad->estaActiva())
                <a href="{{ route('actividades.escaner', $actividad) }}"
                   class="col-span-2 lg:col-span-1 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white px-4 py-3 lg:py-2.5 rounded-lg text-sm font-bold text-center whitespace-nowrap">
                    📷 Escanear asistencias
                </a>
                <form action="{{ route('actividades.cerrar', $actividad) }}" method="POST">
                    @csrf
                    <button class="w-full bg-gray-600 hover:bg-gray-700 text-white px-3 py-2.5 rounded-lg text-xs font-semibold whitespace-nowrap">
                        🔒 Cerrar
                    </button>
                </form>
            @else
                <form action="{{ route('actividades.abrir', $actividad) }}" method="POST">
                    @csrf
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2.5 rounded-lg text-xs font-semibold whitespace-nowrap">
                        🔓 Reabrir
                    </button>
                </form>
            @endif

            <a href="{{ route('actividades.reporte', $actividad) }}"
               class="bg-sky-600 hover:bg-sky-700 text-white px-3 py-2.5 rounded-lg text-xs font-semibold text-center whitespace-nowrap">
                📊 Reporte
            </a>
            <a href="{{ route('actividades.edit', $actividad) }}"
               class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-2.5 rounded-lg text-xs font-semibold text-center whitespace-nowrap {{ $actividad->estaActiva() ? 'col-span-2' : '' }}">
                ✏️ Editar
            </a>
        </div>
    </div>

    {{-- ═══════════ LAYOUT DE 2 COLUMNAS EN PC ═══════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ════════════ COLUMNA IZQUIERDA (principal) ════════════ --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- ─── TOTALES ─── --}}
            {{-- Móvil: 1 fila compacta  |  PC: tarjetas más grandes con descripción --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">

                {{-- Vista móvil (compacta) --}}
                <div class="lg:hidden grid grid-cols-4 divide-x divide-gray-100 text-center">
                    <div class="py-2.5">
                        <p class="text-lg font-bold text-gray-800 leading-none">
                            {{ $actividad->asistencias->where('estado', '!=', 0)->count() }}
                        </p>
                        <p class="text-[10px] text-gray-500 uppercase mt-0.5">Total</p>
                    </div>
                    <div class="py-2.5">
                        <p class="text-lg font-bold text-green-700 leading-none">
                            {{ $actividad->asistencias->where('estado', 1)->count() }}
                        </p>
                        <p class="text-[10px] text-green-600 uppercase mt-0.5">Presentes</p>
                    </div>
                    <div class="py-2.5">
                        <p class="text-lg font-bold text-yellow-700 leading-none">
                            {{ $actividad->asistencias->where('estado', 2)->count() }}
                        </p>
                        <p class="text-[10px] text-yellow-600 uppercase mt-0.5">Tardanzas</p>
                    </div>
                    <div class="py-2.5">
                        <p class="text-lg font-bold text-red-700 leading-none">
                            {{ $actividad->asistencias->where('estado', 0)->count() }}
                        </p>
                        <p class="text-[10px] text-red-600 uppercase mt-0.5">Anuladas</p>
                    </div>
                </div>

                {{-- Vista PC (tarjetas espaciosas) --}}
                <div class="hidden lg:grid grid-cols-4 divide-x divide-gray-100">
                    <div class="p-5 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center text-xl">
                            👥
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Total</p>
                            <p class="text-3xl font-bold text-gray-800 leading-none">
                                {{ $actividad->asistencias->where('estado', '!=', 0)->count() }}
                            </p>
                        </div>
                    </div>
                    <div class="p-5 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xl">
                            ✓
                        </div>
                        <div>
                            <p class="text-xs text-green-600 uppercase tracking-wide">Presentes</p>
                            <p class="text-3xl font-bold text-green-700 leading-none">
                                {{ $actividad->asistencias->where('estado', 1)->count() }}
                            </p>
                        </div>
                    </div>
                    <div class="p-5 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center text-xl">
                            ⏱
                        </div>
                        <div>
                            <p class="text-xs text-yellow-600 uppercase tracking-wide">Tardanzas</p>
                            <p class="text-3xl font-bold text-yellow-700 leading-none">
                                {{ $actividad->asistencias->where('estado', 2)->count() }}
                            </p>
                        </div>
                    </div>
                    <div class="p-5 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-red-100 text-red-700 flex items-center justify-center text-xl">
                            ✕
                        </div>
                        <div>
                            <p class="text-xs text-red-600 uppercase tracking-wide">Anuladas</p>
                            <p class="text-3xl font-bold text-red-700 leading-none">
                                {{ $actividad->asistencias->where('estado', 0)->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── LISTA DE ASISTENCIAS ─── --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800 text-sm lg:text-base">
                        Asistencias registradas
                        <span class="text-xs lg:text-sm font-normal text-gray-500">
                            ({{ $actividad->asistencias->count() }})
                        </span>
                    </h2>
                    @if($actividad->permite_manual && $actividad->estaActiva())
                        <a href="{{ route('actividades.escaner', $actividad) }}"
                           class="text-xs text-sky-600 hover:underline hidden lg:inline">
                            Registrar más desde el escáner →
                        </a>
                    @endif
                </div>

                @if($actividad->asistencias->count() === 0)
                    <div class="p-8 lg:p-12 text-center text-gray-500">
                        <p class="text-4xl lg:text-5xl mb-2">👥</p>
                        <p class="text-sm lg:text-base">Aún no hay asistencias registradas.</p>
                    </div>
                @else
                    @php
                        $colores = [
                            1 => 'bg-green-100 text-green-800',
                            2 => 'bg-yellow-100 text-yellow-800',
                            3 => 'bg-blue-100 text-blue-800',
                            4 => 'bg-red-100 text-red-800',
                            0 => 'bg-gray-200 text-gray-700',
                        ];
                    @endphp

                    {{-- 📱 Vista móvil: tarjetas --}}
                    <div class="lg:hidden divide-y divide-gray-100">
                        @foreach($actividad->asistencias->sortBy('hora_registro') as $a)
                            <div class="p-3 flex items-start gap-3 {{ $a->estado == 0 ? 'opacity-50' : '' }}">
                                <div class="flex-shrink-0">
                                    @if($a->persona->foto)
                                        <img src="{{ asset('storage/' . $a->persona->foto) }}"
                                             alt="{{ $a->persona->nombre_completo }}"
                                             class="w-10 h-10 rounded-full object-cover border">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-bold border">
                                            {{ strtoupper(substr($a->persona->nombre ?? '?', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800 text-sm truncate {{ $a->estado == 0 ? 'line-through' : '' }}">
                                        {{ $a->persona->nombre_completo ?? '—' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        CI: {{ $a->persona->ci ?? '—' }} · {{ $a->hora_registro->format('H:i:s') }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                        <span class="{{ $colores[$a->estado] ?? 'bg-gray-100' }} px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase">
                                            {{ $a->estado_texto }}
                                        </span>
                                        <span class="text-[10px] text-gray-400">{{ $a->metodo_texto }}</span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($a->estado != 0)
                                        <form action="{{ route('asistencias.anular', $a) }}" method="POST"
                                              onsubmit="return confirm('¿Anular esta asistencia?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-500 hover:text-red-700 p-2 text-lg leading-none" title="Anular">✕</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- 💻 Vista PC: tabla --}}
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="text-left px-5 py-3 font-semibold">Persona</th>
                                    <th class="text-left px-5 py-3 font-semibold">CI</th>
                                    <th class="text-left px-5 py-3 font-semibold">Hora</th>
                                    <th class="text-center px-5 py-3 font-semibold">Estado</th>
                                    <th class="text-center px-5 py-3 font-semibold">Método</th>
                                    <th class="text-right px-5 py-3 font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($actividad->asistencias->sortBy('hora_registro') as $a)
                                    <tr class="{{ $a->estado == 0 ? 'opacity-50' : '' }} hover:bg-gray-50 transition">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                @if($a->persona->foto)
                                                    <img src="{{ asset('storage/' . $a->persona->foto) }}"
                                                         class="w-8 h-8 rounded-full object-cover border">
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center text-xs font-bold border">
                                                        {{ strtoupper(substr($a->persona->nombre ?? '?', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span class="font-medium text-gray-800 {{ $a->estado == 0 ? 'line-through' : '' }}">
                                                    {{ $a->persona->nombre_completo ?? '—' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-gray-600">{{ $a->persona->ci ?? '—' }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $a->hora_registro->format('H:i:s') }}</td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="{{ $colores[$a->estado] ?? 'bg-gray-100' }} px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                                                {{ $a->estado_texto }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-center text-xs text-gray-500">
                                            {{ $a->metodo_texto }}
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            @if($a->estado != 0)
                                                <form action="{{ route('asistencias.anular', $a) }}" method="POST"
                                                      onsubmit="return confirm('¿Anular esta asistencia?');"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:text-red-800 hover:bg-red-50 text-xs font-semibold px-3 py-1.5 rounded transition">
                                                        Anular
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">Anulada</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- ════════════ COLUMNA DERECHA (sidebar en PC) ════════════ --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- ─── QR DE LA ACTIVIDAD ─── --}}
            <div class="bg-white rounded-lg shadow p-4 lg:p-5 lg:sticky lg:top-4">

                {{-- Encabezado --}}
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-semibold text-gray-800 text-sm lg:text-base">
                        QR de la actividad
                    </h2>
                    <button type="button" onclick="abrirQr()"
                            class="text-xs text-sky-600 hover:underline font-semibold">
                        🔍 Ampliar
                    </button>
                </div>

                {{-- Móvil: miniatura + texto al lado --}}
                <div class="lg:hidden flex items-center gap-3">
                    <button type="button" onclick="abrirQr()"
                            class="flex-shrink-0 border-2 border-sky-200 rounded-lg overflow-hidden hover:border-sky-400 active:scale-95 transition">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($actividad->token_qr) }}"
                             alt="QR de la actividad" class="w-20 h-20 object-cover">
                    </button>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500">
                            Toca el código para ampliarlo y que los empleados puedan escanearlo.
                        </p>
                    </div>
                </div>

                {{-- PC: QR grande centrado --}}
                <div class="hidden lg:block text-center">
                    <button type="button" onclick="abrirQr()"
                            class="border-4 border-sky-100 rounded-xl overflow-hidden hover:border-sky-300 active:scale-95 transition inline-block">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=400x400&data={{ urlencode($actividad->token_qr) }}"
                             alt="QR de la actividad" class="w-56 h-56 object-cover">
                    </button>
                    <p class="text-xs text-gray-500 mt-3 leading-relaxed">
                        Muestra este código a los empleados para que lo escaneen con su celular.
                    </p>
                </div>

                {{-- Acciones sobre el QR --}}
                <div class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
                    <button type="button" onclick="copiarToken()"
                            class="flex-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg font-semibold transition">
                        📋 Copiar token
                    </button>
                    <a href="https://api.qrserver.com/v1/create-qr-code/?size=800x800&data={{ urlencode($actividad->token_qr) }}"
                       download="qr-{{ Str::slug($actividad->nombre) }}.png"
                       class="flex-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg font-semibold transition text-center">
                        ⬇️ Descargar
                    </a>
                </div>
            </div>

            {{-- ─── INFO DE LA ACTIVIDAD ─── --}}
            <div class="bg-white rounded-lg shadow p-4 lg:p-5">
                <h2 class="font-semibold text-gray-800 text-sm lg:text-base mb-3">
                    Detalles
                </h2>
                <dl class="space-y-2.5 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-gray-500">Fecha</dt>
                        <dd class="text-gray-800 font-medium text-right">{{ $actividad->fecha->format('d/m/Y') }}</dd>
                    </div>
                    @if($actividad->hora_inicio)
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500">Inicio</dt>
                            <dd class="text-gray-800 font-medium text-right">{{ \Carbon\Carbon::parse($actividad->hora_inicio)->format('H:i') }}</dd>
                        </div>
                    @endif
                    @if($actividad->hora_fin)
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500">Fin</dt>
                            <dd class="text-gray-800 font-medium text-right">{{ \Carbon\Carbon::parse($actividad->hora_fin)->format('H:i') }}</dd>
                        </div>
                    @endif
                    @if($actividad->hora_limite_puntual)
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500">Límite puntual</dt>
                            <dd class="text-gray-800 font-medium text-right">{{ \Carbon\Carbon::parse($actividad->hora_limite_puntual)->format('H:i') }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between gap-3">
                        <dt class="text-gray-500">Lugar</dt>
                        <dd class="text-gray-800 font-medium text-right">{{ $actividad->lugar ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-gray-500">Registro manual</dt>
                        <dd class="text-gray-800 font-medium text-right">
                            {{ $actividad->permite_manual ? 'Permitido' : 'No permitido' }}
                        </dd>
                    </div>
                </dl>

                @if($actividad->descripcion)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Descripción</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $actividad->descripcion }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ═══════════ MODAL QR PANTALLA COMPLETA ═══════════ --}}
<div id="qrModal"
     class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4"
     onclick="cerrarQr()">

    <div class="text-center max-w-md w-full" onclick="event.stopPropagation()">
        <div class="bg-white rounded-2xl p-5 lg:p-6 shadow-2xl">
            <h3 class="font-bold text-gray-800 text-lg mb-1">{{ $actividad->nombre }}</h3>
            <p class="text-xs text-gray-500 mb-4">
                {{ $actividad->fecha->format('d/m/Y') }}
                @if($actividad->hora_inicio)
                    · {{ \Carbon\Carbon::parse($actividad->hora_inicio)->format('H:i') }}
                @endif
            </p>

            <img src="https://api.qrserver.com/v1/create-qr-code/?size=600x600&data={{ urlencode($actividad->token_qr) }}"
                 alt="QR de la actividad"
                 class="w-full max-w-xs mx-auto rounded-lg border-4 border-sky-100">

            <p class="text-xs text-gray-500 mt-4">
                Muestra este código a los empleados para que lo escaneen.
            </p>

            <button type="button" onclick="cerrarQr()"
                    class="mt-5 w-full bg-sky-600 hover:bg-sky-700 text-white px-4 py-3 rounded-lg text-sm font-semibold">
                Cerrar
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function abrirQr() {
        const modal = document.getElementById('qrModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function cerrarQr() {
        const modal = document.getElementById('qrModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function copiarToken() {
        const token = @json($actividad->token_qr);
        navigator.clipboard.writeText(token).then(() => {
            alert('✅ Token copiado al portapapeles');
        }).catch(() => {
            // Fallback para navegadores viejos
            const input = document.createElement('input');
            input.value = token;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            alert('✅ Token copiado');
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') cerrarQr();
    });
</script>
@endpush

@endsection