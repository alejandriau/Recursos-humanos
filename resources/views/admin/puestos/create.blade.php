@extends('dashboard')

@section('title', 'Crear Puesto de Trabajo')
@section('header-title', 'Crear Puesto de Trabajo')

@section('contenido')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Nuevo Puesto de Trabajo
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Complete la información para crear un nuevo puesto de trabajo.
            </p>
        </div>

        <form action="{{ route('puestos.store') }}" method="POST">
            @csrf
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Denominación -->
                    <div class="sm:col-span-2">
                        <label for="denominacion" class="block text-sm font-medium text-gray-700">
                            Denominación del Puesto *
                        </label>
                        <input type="text" name="denominacion" id="denominacion"
                               value="{{ old('denominacion') }}"
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('denominacion') border-red-300 @enderror"
                               required>
                        @error('denominacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nivel Jerárquico -->
                    <div>
                        <label for="nivelJerarquico" class="block text-sm font-medium text-gray-700">
                            Nivel Jerárquico *
                        </label>
                        <select name="nivelJerarquico" id="nivelJerarquico"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nivelJerarquico') border-red-300 @enderror"
                                required>
                            <option value="">Seleccione un nivel</option>
                            @foreach([
                                'GOBERNADOR (A)',
                                'SECRETARIA (O) DEPARTAMENTAL',
                                'ASESORA (OR) / DIRECTORA (OR) / DIR. SERV. DPTAL.',
                                'JEFA (E) DE UNIDAD',
                                'PROFESIONAL I',
                                'PROFESIONAL II',
                                'ADMINISTRATIVO I',
                                'ADMINISTRATIVO II',
                                'APOYO ADMINISTRATIVO I',
                                'APOYO ADMINISTRATIVO II',
                                'ASISTENTE'
                            ] as $nivel)
                                <option value="{{ $nivel }}" {{ old('nivelJerarquico') == $nivel ? 'selected' : '' }}>
                                    {{ $nivel }}
                                </option>
                            @endforeach
                        </select>
                        @error('nivelJerarquico')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Item -->
                    <div>
                        <label for="item" class="block text-sm font-medium text-gray-700">
                            Item
                        </label>
                        <input type="text" name="item" id="item"
                               value="{{ old('item') }}"
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('item') border-red-300 @enderror">
                        @error('item')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unidad Organizacional -->
                    <div>
                        <label for="idUnidadOrganizacional" class="block text-sm font-medium text-gray-700">
                            Unidad Organizacional *
                        </label>
                        <select name="idUnidadOrganizacional" id="idUnidadOrganizacional"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('idUnidadOrganizacional') border-red-300 @enderror"
                                required>
                            <option value="">Seleccione una unidad</option>
                            @foreach($unidades as $unidad)
                                <option value="{{ $unidad->id }}" {{ old('idUnidadOrganizacional') == $unidad->id ? 'selected' : '' }}>
                                    {{ $unidad->denominacion }} ({{ $unidad->tipo }})
                                </option>
                            @endforeach
                        </select>
                        @error('idUnidadOrganizacional')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo de Contrato -->
                    <div>
                        <label for="tipoContrato" class="block text-sm font-medium text-gray-700">
                            Tipo de Contrato *
                        </label>
                        <select name="tipoContrato" id="tipoContrato"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('tipoContrato') border-red-300 @enderror"
                                required>
                            <option value="">Seleccione tipo</option>
                            <option value="PERMANENTE" {{ old('tipoContrato') == 'PERMANENTE' ? 'selected' : '' }}>Permanente</option>
                            <option value="EVENTUAL" {{ old('tipoContrato') == 'EVENTUAL' ? 'selected' : '' }}>Eventual</option>
                        </select>
                        @error('tipoContrato')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Haber -->
                    <div>
                        <label for="haber" class="block text-sm font-medium text-gray-700">
                            Haber (Bs.)
                        </label>
                        <input type="number" name="haber" id="haber"
                               value="{{ old('haber') }}" step="0.01" min="0"
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('haber') border-red-300 @enderror">
                        @error('haber')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nivel -->
                    <div>
                        <label for="nivel" class="block text-sm font-medium text-gray-700">
                            Nivel
                        </label>
                        <input type="number" name="nivel" id="nivel"
                               value="{{ old('nivel') }}" min="1" max="10"
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('nivel') border-red-300 @enderror">
                        @error('nivel')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Manual -->
                    <div>
                        <label for="manual" class="block text-sm font-medium text-gray-700">
                            Manual
                        </label>
                        <input type="text" name="manual" id="manual"
                               value="{{ old('manual') }}"
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('manual') border-red-300 @enderror">
                        @error('manual')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jefatura -->
                    <div class="sm:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="esJefatura" id="esJefatura"
                                   value="1" {{ old('esJefatura') ? 'checked' : '' }}
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="esJefatura" class="ml-2 block text-sm text-gray-900">
                                Este puesto es una jefatura
                            </label>
                        </div>
                    </div>

                    <!-- Perfil -->
                    <div class="sm:col-span-2">
                        <label for="perfil" class="block text-sm font-medium text-gray-700">
                            Perfil del Puesto
                        </label>
                        <textarea name="perfil" id="perfil" rows="3"
                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('perfil') border-red-300 @enderror">{{ old('perfil') }}</textarea>
                        @error('perfil')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Experiencia -->
                    <div class="sm:col-span-2">
                        <label for="experencia" class="block text-sm font-medium text-gray-700">
                            Experiencia Requerida
                        </label>
                        <textarea name="experencia" id="experencia" rows="2"
                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('experencia') border-red-300 @enderror">{{ old('experencia') }}</textarea>
                        @error('experencia')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <a href="{{ route('puestos.index') }}"
                   class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>Crear Puesto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
