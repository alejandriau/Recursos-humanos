@extends('layouts.baseadm')

@section('title', 'Nueva actividad')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="mb-5">
        <a href="{{ route('actividades.index') }}" class="text-sm text-sky-600 hover:underline">
            ← Volver al listado
        </a>
        <h1 class="text-2xl font-bold text-gray-800 mt-1">Nueva actividad</h1>
        <p class="text-sm text-gray-500">Registra un evento o actividad externa</p>
    </div>

    <form action="{{ route('actividades.store') }}" method="POST"
          class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf

        {{-- Nombre --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Nombre <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="150"
                   placeholder="Ej: Actividad en Plaza Principal"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
            @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Descripción --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
            <textarea name="descripcion" rows="3" maxlength="1000"
                      placeholder="Detalles de la actividad…"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">{{ old('descripcion') }}</textarea>
        </div>

        {{-- Lugar --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Lugar</label>
            <input type="text" name="lugar" value="{{ old('lugar') }}" maxlength="200"
                   placeholder="Ej: Plaza 14 de Septiembre"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Fecha --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Fecha <span class="text-red-500">*</span>
                </label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
                @error('fecha') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Hora límite puntual --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Hora límite puntual
                    <span class="text-xs text-gray-400 font-normal">(tardanza)</span>
                </label>
                <input type="time" name="hora_limite_puntual" value="{{ old('hora_limite_puntual') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
                <p class="text-xs text-gray-400 mt-1">Si se registra después, se marca como tardanza.</p>
            </div>

            {{-- Hora inicio --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Hora de inicio</label>
                <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
            </div>

            {{-- Hora fin --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Hora de fin</label>
                <input type="time" name="hora_fin" value="{{ old('hora_fin') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
            </div>
        </div>

        {{-- Permitir manual --}}
        <div class="flex items-center gap-2 pt-1">
            <input type="hidden" name="permite_manual" value="0">
            <input type="checkbox" name="permite_manual" value="1" id="permite_manual"
                   {{ old('permite_manual', '1') == '1' ? 'checked' : '' }}
                   class="w-4 h-4 text-sky-600">
            <label for="permite_manual" class="text-sm text-gray-700">
                Permitir registro manual (por si alguien no trae QR)
            </label>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
            <a href="{{ route('actividades.index') }}"
               class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit"
                    class="bg-sky-600 hover:bg-sky-700 text-white px-5 py-2 rounded-lg text-sm font-semibold">
                Guardar actividad
            </button>
        </div>
    </form>
</div>

@endsection