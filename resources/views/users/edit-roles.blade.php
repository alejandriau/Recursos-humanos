@extends('dashboard')

@section('title', 'Editar Roles de Usuario')

@section('contenido')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="text-blue-500 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i> Volver a la lista de usuarios
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Editar Roles para: {{ $user->name }}</h2>
            <span class="text-sm text-gray-600">{{ $user->email }}</span>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('users.roles.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Selecciona los roles para este usuario:</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($roles as $role)
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="role-{{ $role->id }}" name="roles[]" type="checkbox"
                                value="{{ $role->name }}"
                                {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="role-{{ $role->id }}" class="font-medium text-gray-700">{{ $role->name }}</label>
                            @if($role->permissions->count() > 0)
                            <p class="text-xs text-gray-500 mt-1">
                                Incluye permisos:
                                {{ $role->permissions->take(3)->pluck('name')->implode(', ') }}
                                @if($role->permissions->count() > 3)
                                y {{ $role->permissions->count() - 3 }} más...
                                @endif
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                @error('roles')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('users.show', $user) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition duration-200">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>Guardar Roles
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden p-6">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Roles Actualmente Asignados</h3>

        @if($user->roles->count() > 0)
        <div class="flex flex-wrap gap-2">
            @foreach($user->roles as $role)
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full flex items-center">
                {{ $role->name }}
                <form action="{{ route('users.roles.remove', [$user, $role]) }}" method="POST" class="ml-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-blue-500 hover:text-blue-700" onclick="return confirm('¿Quitar este rol al usuario?')">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </span>
            @endforeach
        </div>
        @else
        <p class="text-gray-500">Este usuario no tiene roles asignados.</p>
        @endif
    </div>
</div>

<script>
    // Validación básica para evitar enviar el formulario sin seleccionar al menos un rol
    document.querySelector('form').addEventListener('submit', function(e) {
        const checkedRoles = document.querySelectorAll('input[name="roles[]"]:checked');
        if (checkedRoles.length === 0) {
            e.preventDefault();
            alert('Debes seleccionar al menos un rol para el usuario.');
        }
    });
</script>
@endsection
