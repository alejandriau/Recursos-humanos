@extends('dashboard')

@section('title', 'Crear Unidad Organizacional')
@section('header-title', 'Crear Unidad Organizacional')

@section('contenido')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Nueva Unidad Organizacional
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Complete la información para crear una nueva unidad organizacional.
            </p>
        </div>

        <form action="{{ route('unidades.store') }}" method="POST">
            @csrf
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Denominación -->
                    <div class="sm:col-span-2">
                        <label for="denominacion" class="block text-sm font-medium text-gray-700">
                            Denominación *
                        </label>
                        <input type="text" name="denominacion" id="denominacion"
                               value="{{ old('denominacion') }}"
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('denominacion') border-red-300 @enderror"
                               required>
                        @error('denominacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Código y Sigla -->
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700">
                            Código
                        </label>
                        <input type="text" name="codigo" id="codigo"
                               value="{{ old('codigo') }}"
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('codigo') border-red-300 @enderror">
                        @error('codigo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sigla" class="block text-sm font-medium text-gray-700">
                            Sigla
                        </label>
                        <input type="text" name="sigla" id="sigla"
                               value="{{ old('sigla') }}"
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('sigla') border-red-300 @enderror">
                        @error('sigla')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo y Unidad Padre -->
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-gray-700">
                            Tipo *
                        </label>
                        <select name="tipo" id="tipo"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('tipo') border-red-300 @enderror"
                                required>
                            <option value="">Seleccione un tipo</option>
                            @foreach(['SECRETARIA', 'SERVICIO', 'DIRECCION', 'UNIDAD', 'AREA', 'PROGRAMA', 'PROYECTO'] as $tipo)
                                <option value="{{ $tipo }}" {{ old('tipo') == $tipo ? 'selected' : '' }}>
                                    {{ $tipo }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="idPadre" class="block text-sm font-medium text-gray-700">
                            Unidad Padre
                        </label>
                        <select name="idPadre" id="idPadre"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('idPadre') border-red-300 @enderror">
                            <option value="">Sin unidad padre</option>
                            @foreach($unidadesPadre as $unidad)
                                <option value="{{ $unidad->id }}" {{ old('idPadre') == $unidad->id ? 'selected' : '' }}>
                                    {{ $unidad->denominacion }} ({{ $unidad->tipo }})
                                </option>
                            @endforeach
                        </select>
                        @error('idPadre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div class="sm:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="esActivo" id="esActivo"
                                   value="1" {{ old('esActivo', true) ? 'checked' : '' }}
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="esActivo" class="ml-2 block text-sm text-gray-900">
                                Unidad activa
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <a href="{{ route('unidades.index') }}"
                   class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>Crear Unidad
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validación en tiempo real
        const formulario = document.querySelector('form');
        const inputs = formulario.querySelectorAll('input[required], select[required]');

        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.classList.add('border-red-300');
                } else {
                    this.classList.remove('border-red-300');
                }
            });
        });
    });
</script>
@endsection
