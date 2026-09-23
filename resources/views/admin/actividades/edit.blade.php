@extends('layouts.baseadm')

@section('title', 'Editar actividad')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="mb-5">
        <a href="{{ route('actividades.show', $actividad) }}" class="text-sm text-sky-600 hover:underline">
            ← Volver a la actividad
        </a>
        <h1 class="text-2xl font-bold text-gray-800 mt-1">Editar actividad</h1>
    </div>

    <form action="{{ route('actividades.update', $actividad) }}" method="POST"
          class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Nombre <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nombre" value="{{ old('nombre', $actividad->nombre) }}" required maxlength="150"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
            @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
            <textarea name="descripcion" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">{{ old('descripcion', $actividad->descripcion) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Lugar</label>
            <input type="text" name="lugar" value="{{ old('lugar', $actividad->lugar) }}" maxlength="200"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Fecha <span class="text-red-500">*</span>
                </label>
                <input type="date" name="fecha"
                       value="{{ old('fecha', $actividad->fecha->format('Y-m-d')) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Hora límite puntual</label>
                <input type="time" name="hora_limite_puntual"
                       value="{{ old('hora_limite_puntual', $actividad->hora_limite_puntual) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Hora inicio</label>
                <input type="time" name="hora_inicio"
                       value="{{ old('hora_inicio', $actividad->hora_inicio) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Hora fin</label>
                <input type="time" name="hora_fin"
                       value="{{ old('hora_fin', $actividad->hora_fin) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
            <select name="estado"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
                <option value="1" {{ old('estado', $actividad->estado) == 1 ? 'selected' : '' }}>Activa</option>
                <option value="0" {{ old('estado', $actividad->estado) == 0 ? 'selected' : '' }}>Cerrada</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <input type="hidden" name="permite_manual" value="0">
            <input type="checkbox" name="permite_manual" value="1" id="permite_manual"
                   {{ old('permite_manual', $actividad->permite_manual) ? 'checked' : '' }}
                   class="w-4 h-4 text-sky-600">
            <label for="permite_manual" class="text-sm text-gray-700">Permitir registro manual</label>
        </div>

        <div class="flex justify-between pt-3 border-t border-gray-100">
            <form action="{{ route('actividades.destroy', $actividad) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar esta actividad y todas sus asistencias?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                    🗑️ Eliminar actividad
                </button>
            </form>

            <div class="flex gap-3">
                <a href="{{ route('actividades.show', $actividad) }}"
                   class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-sky-600 hover:bg-sky-700 text-white px-5 py-2 rounded-lg text-sm font-semibold">
                    Guardar cambios
                </button>
            </div>
        </div>
    </form>
</div>

@endsection