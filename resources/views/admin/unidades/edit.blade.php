@extends('dashboard')

@section('title', 'Editar Unidad Organizacional')
@section('header-title', 'Editar Unidad Organizacional')

@section('contenido')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm">
            <li>
                <a href="{{ route('unidades.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-building mr-1"></i>Unidades
                </a>
            </li>
            <li class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            </li>
            <li>
                <a href="{{ route('unidades.show', $unidad) }}" class="text-gray-500 hover:text-gray-700">
                    {{ Str::limit($unidad->denominacion, 30) }}
                </a>
            </li>
            <li class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            </li>
            <li class="text-gray-900 font-medium">Editar</li>
        </ol>
    </nav>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <!-- Header -->
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        <i class="fas fa-edit mr-2 text-blue-600"></i>
                        Editar Unidad Organizacional
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-600">
                        Actualice la información de la unidad organizacional.
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($unidad->esActivo) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                        <i class="fas fa-circle text-xs mr-1"></i>
                        {{ $unidad->esActivo ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>
        </div>

        <form action="{{ route('unidades.update', $unidad) }}" method="POST" id="edit-unidad-form">
            @csrf
            @method('PUT')

            <div class="px-4 py-5 sm:p-6">
                <!-- Alertas de validación -->
                @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Existen errores en el formulario
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Denominación -->
                    <div class="sm:col-span-2">
                        <label for="denominacion" class="block text-sm font-medium text-gray-700 required">
                            Denominación
                        </label>
                        <div class="mt-1 relative">
                            <input type="text" name="denominacion" id="denominacion"
                                   value="{{ old('denominacion', $unidad->denominacion) }}"
                                   maxlength="800"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors
                                   @error('denominacion') border-red-300 @enderror"
                                   placeholder="Ingrese la denominación completa"
                                   required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-sm" id="denominacion-counter">
                                    {{ strlen(old('denominacion', $unidad->denominacion)) }}/800
                                </span>
                            </div>
                        </div>
                        @error('denominacion')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">
                                Nombre completo de la unidad organizacional
                            </p>
                        @enderror
                    </div>

                    <!-- Código y Sigla -->
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700">
                            Código
                        </label>
                        <div class="mt-1">
                            <input type="text" name="codigo" id="codigo"
                                   value="{{ old('codigo', $unidad->codigo) }}"
                                   maxlength="45"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors
                                   @error('codigo') border-red-300 @enderror"
                                   placeholder="Ej: UO-001">
                        </div>
                        @error('codigo')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">
                                Código único identificador
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="sigla" class="block text-sm font-medium text-gray-700">
                            Sigla
                        </label>
                        <div class="mt-1">
                            <input type="text" name="sigla" id="sigla"
                                   value="{{ old('sigla', $unidad->sigla) }}"
                                   maxlength="20"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors
                                   @error('sigla') border-red-300 @enderror"
                                   placeholder="Ej: UO">
                        </div>
                        @error('sigla')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">
                                Abreviatura de la unidad
                            </p>
                        @enderror
                    </div>

                    <!-- Tipo y Unidad Padre -->
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-gray-700 required">
                            Tipo
                        </label>
                        <div class="mt-1">
                            <select name="tipo" id="tipo"
                                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors
                                    @error('tipo') border-red-300 @enderror"
                                    required>
                                <option value="">Seleccione un tipo</option>
                                @foreach(['SECRETARIA', 'SERVICIO', 'DIRECCION', 'UNIDAD', 'AREA', 'PROGRAMA', 'PROYECTO'] as $tipo)
                                    <option value="{{ $tipo }}"
                                            {{ old('tipo', $unidad->tipo) == $tipo ? 'selected' : '' }}
                                            data-color="@switch($tipo)
                                                @case('SECRETARIA') purple @break
                                                @case('SERVICIO') indigo @break
                                                @case('DIRECCION') blue @break
                                                @case('UNIDAD') green @break
                                                @case('AREA') yellow @break
                                                @default gray
                                            @endswitch">
                                        {{ $tipo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('tipo')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">
                                Tipo de unidad organizacional
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="idPadre" class="block text-sm font-medium text-gray-700">
                            Unidad Padre
                        </label>
                        <div class="mt-1">
                            <select name="idPadre" id="idPadre"
                                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors
                                    @error('idPadre') border-red-300 @enderror">
                                <option value="">Sin unidad padre</option>
                                @foreach($unidadesPadre as $padre)
                                    @if($padre->id != $unidad->id) <!-- Evitar auto-referencia -->
                                    <option value="{{ $padre->id }}"
                                            {{ old('idPadre', $unidad->idPadre) == $padre->id ? 'selected' : '' }}
                                            data-tipo="{{ $padre->tipo }}">
                                        {{ $padre->denominacion }}
                                        <span class="text-gray-400">({{ $padre->tipo }})</span>
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        @error('idPadre')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">
                                Unidad organizacional superior
                            </p>
                        @enderror
                    </div>

                    <!-- Estado y Campos Adicionales -->
                    <div class="sm:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 rounded-lg">
                            <!-- Estado -->
                            <div class="flex items-center">
                                <input type="checkbox" name="esActivo" id="esActivo"
                                       value="1" {{ old('esActivo', $unidad->esActivo) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors">
                                <label for="esActivo" class="ml-2 block text-sm text-gray-900 font-medium">
                                    Unidad activa
                                </label>
                            </div>

                            <!-- Información de auditoría -->
                            <div class="text-sm text-gray-500">
                                <div class="flex items-center space-x-4">
                                    <span>
                                        <i class="fas fa-calendar-plus mr-1"></i>
                                        Creado: {{ optional($unidad->created_at)->format('d/m/Y') }}
                                    </span>
                                    <span>
                                        <i class="fas fa-edit mr-1"></i>
                                        Actualizado: {{ optional($unidad->updated_at)->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <a href="{{ route('unidades.show', $unidad) }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-eye mr-2"></i>Ver Detalles
                        </a>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('unidades.index') }}"
                           class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contador de caracteres para denominación
    const denominacionInput = document.getElementById('denominacion');
    const denominacionCounter = document.getElementById('denominacion-counter');

    denominacionInput.addEventListener('input', function() {
        const length = this.value.length;
        denominacionCounter.textContent = `${length}/800`;

        if (length > 750) {
            denominacionCounter.classList.add('text-orange-500');
            denominacionCounter.classList.remove('text-gray-400');
        } else {
            denominacionCounter.classList.remove('text-orange-500');
            denominacionCounter.classList.add('text-gray-400');
        }
    });

    // Validación en tiempo real
    const form = document.getElementById('edit-unidad-form');
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });

        field.addEventListener('input', function() {
            if (this.classList.contains('border-red-300')) {
                validateField(this);
            }
        });
    });

    function validateField(field) {
        if (!field.value.trim()) {
            field.classList.add('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
            field.classList.remove('focus:ring-blue-500', 'focus:border-blue-500');
        } else {
            field.classList.remove('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
            field.classList.add('focus:ring-blue-500', 'focus:border-blue-500');
        }
    }

    // Prevenir envío duplicado del formulario
    let isSubmitting = false;
    form.addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return;
        }

        isSubmitting = true;
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...';
    });

    // Mejorar la experiencia del select de tipo
    const tipoSelect = document.getElementById('tipo');
    tipoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const color = selectedOption.getAttribute('data-color');
        // Podrías añadir estilos dinámicos aquí si lo deseas
    });
});
</script>
@endpush
