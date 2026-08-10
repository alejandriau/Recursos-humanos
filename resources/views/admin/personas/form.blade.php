@extends('layouts.baseadm')

@section('contenido')
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            {{ $persona->exists ? 'Editar Persona' : 'Registrar Persona' }}
        </h2>

        <form method="POST"
            action="{{ $persona->exists ? route('personas.update', $persona->id) : route('personas.store') }}"
            enctype="multipart/form-data"
            id="personaForm"
            class="bg-white rounded-lg shadow-md p-6">
            @csrf
            @if($persona->exists)
                @method('PUT')
            @endif

            {{-- Errores generales --}}
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                    <strong class="font-bold">Por favor corrige los siguientes errores:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Fila 1: CI, Nombre, Apellido Paterno, Apellido Materno --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="ci" class="block text-sm font-medium text-gray-700 mb-1">Carnet de Identidad</label>
                    <input type="text" id="ci" name="ci"
                        value="{{ old('ci', $persona->ci) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('ci') border-red-500 @enderror"
                        required>
                    @error('ci')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" id="nombre" name="nombre"
                        value="{{ old('nombre', $persona->nombre) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nombre') border-red-500 @enderror"
                        required
                        pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+"
                        title="Solo letras, espacios y acentos permitidos">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-red-500 text-xs mt-1 hidden" id="nombreError">Solo letras, espacios y acentos permitidos.</p>
                </div>

                <div>
                    <label for="apellidoPat" class="block text-sm font-medium text-gray-700 mb-1">Apellido Paterno</label>
                    <input type="text" id="apellidoPat" name="apellidoPat"
                        value="{{ old('apellidoPat', $persona->apellidoPat) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('apellidoPat') border-red-500 @enderror"
                        pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]*"
                        title="Solo letras, espacios y acentos permitidos">
                    @error('apellidoPat')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-red-500 text-xs mt-1 hidden" id="apellidoPatError">Solo letras, espacios y acentos permitidos.</p>
                </div>

                <div>
                    <label for="apellidoMat" class="block text-sm font-medium text-gray-700 mb-1">Apellido Materno</label>
                    <input type="text" id="apellidoMat" name="apellidoMat"
                        value="{{ old('apellidoMat', $persona->apellidoMat) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('apellidoMat') border-red-500 @enderror"
                        pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]*"
                        title="Solo letras, espacios y acentos permitidos">
                    @error('apellidoMat')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-red-500 text-xs mt-1 hidden" id="apellidoMatError">Solo letras, espacios y acentos permitidos.</p>
                </div>
            </div>

            {{-- Fila 2: Fechas, Sexo, Teléfono --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                <div>
                    <label for="fechaIngreso" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Ingreso</label>
                    <input type="date" id="fechaIngreso" name="fechaIngreso"
                        value="{{ old('fechaIngreso', optional($persona->fechaIngreso)->format('Y-m-d')) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('fechaIngreso') border-red-500 @enderror">
                    @error('fechaIngreso')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fechaNacimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                    <input type="date" id="fechaNacimiento" name="fechaNacimiento"
                        value="{{ old('fechaNacimiento', optional($persona->fechaNacimiento)->format('Y-m-d')) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('fechaNacimiento') border-red-500 @enderror">
                    @error('fechaNacimiento')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sexo" class="block text-sm font-medium text-gray-700 mb-1">Sexo</label>
                    <select id="sexo" name="sexo"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('sexo') border-red-500 @enderror"
                        required>
                        <option value="">Seleccione</option>
                        <option value="M" {{ old('sexo', $persona->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('sexo', $persona->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                    @error('sexo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" id="telefono" name="telefono"
                        value="{{ old('telefono', $persona->telefono) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('telefono') border-red-500 @enderror">
                    @error('telefono')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Observaciones --}}
            <div class="mt-4">
                <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('observaciones') border-red-500 @enderror">{{ old('observaciones', $persona->observaciones) }}</textarea>
                @error('observaciones')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Foto --}}
            <div class="mt-4 flex flex-col md:flex-row md:items-center md:space-x-6">
                <div class="flex-1">
                    <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto de perfil</label>
                    <input type="file" id="foto" name="foto" accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, máximo 2MB</p>
                    @error('foto')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-center mt-4 md:mt-0">
                    <div class="relative">
                        <img id="preview-foto"
                            src="{{ $persona->foto ? route('persona.foto', $persona->id) : asset('images/avatar-default.png') }}"
                            alt="Foto de perfil"
                            class="w-32 h-32 rounded-full object-cover border-2 border-gray-200 shadow-md cursor-pointer"
                            onclick="if(this.src.includes('avatar-default')) return; document.getElementById('modalFoto')?.classList.toggle('hidden')">
                        @if($persona->foto)
                            <div class="absolute bottom-0 right-0 bg-white rounded-full p-1 shadow">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('reportes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    {{ $persona->exists ? 'Actualizar' : 'Registrar' }}
                </button>
            </div>
        </form>
    </div>

    {{-- Modal para ampliar foto (opcional) --}}
    @if($persona->foto)
        <div id="modalFoto" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden" onclick="this.classList.add('hidden')">
            <img src="{{ route('persona.foto', $persona->id) }}" alt="Foto ampliada" class="max-w-full max-h-full object-contain">
        </div>
    @endif

    <script>
        // Previsualización de la imagen al seleccionar un archivo
        document.getElementById('foto').addEventListener('change', function (event) {
            const input = event.target;
            const preview = document.getElementById('preview-foto');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    // Si hay un modal, también actualizamos su imagen (opcional)
                    const modalImg = document.querySelector('#modalFoto img');
                    if (modalImg) modalImg.src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        });

        // Validación frontend para nombre y apellidos (solo letras, espacios y acentos)
        const nombreInput = document.getElementById('nombre');
        const apellidoPatInput = document.getElementById('apellidoPat');
        const apellidoMatInput = document.getElementById('apellidoMat');
        const nombreError = document.getElementById('nombreError');
        const apellidoPatError = document.getElementById('apellidoPatError');
        const apellidoMatError = document.getElementById('apellidoMatError');

        function validateNameInput(input, errorElement) {
            const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]*$/;
            if (!regex.test(input.value)) {
                input.classList.add('border-red-500');
                errorElement.classList.remove('hidden');
                return false;
            } else {
                input.classList.remove('border-red-500');
                errorElement.classList.add('hidden');
                return true;
            }
        }

        nombreInput?.addEventListener('input', () => validateNameInput(nombreInput, nombreError));
        apellidoPatInput?.addEventListener('input', () => validateNameInput(apellidoPatInput, apellidoPatError));
        apellidoMatInput?.addEventListener('input', () => validateNameInput(apellidoMatInput, apellidoMatError));

        // Validación antes de enviar el formulario
        document.getElementById('personaForm').addEventListener('submit', function(e) {
            let isValid = true;
            isValid &= validateNameInput(nombreInput, nombreError);
            isValid &= validateNameInput(apellidoPatInput, apellidoPatError);
            isValid &= validateNameInput(apellidoMatInput, apellidoMatError);

            // Si el campo es requerido, también verificamos que no esté vacío (además del pattern)
            if (nombreInput && nombreInput.value.trim() === '') {
                nombreInput.classList.add('border-red-500');
                // Puedes mostrar un mensaje personalizado si quieres
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                // Opcional: mostrar un mensaje de error general
                alert('Por favor corrige los errores en el formulario.');
            }
        });
    </script>
@endsection