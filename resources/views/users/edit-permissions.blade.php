@extends('dashboard')

@section('title', 'Editar Permisos Directos de Usuario')

@section('contenido')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('users.show', $user) }}" class="text-blue-500 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i> Volver al detalle del usuario
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Editar Permisos Directos para: {{ $user->name }}</h2>
            <span class="text-sm text-gray-600">{{ $user->email }}</span>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-lg font-medium text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-2"></i>Información importante
            </h3>
            <p class="text-sm text-blue-700">
                Los permisos directos se asignan adicionalmente a los permisos que el usuario obtiene a través de sus roles.
                Los permisos directos tienen prioridad sobre los permisos de roles.
            </p>
        </div>

        <form action="{{ route('users.permissions.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-800">Selecciona los permisos directos:</h3>
                    <div class="flex space-x-2">
                        <button type="button" onclick="selectAll()" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 px-2 py-1 rounded">
                            <i class="fas fa-check-square mr-1"></i> Seleccionar todos
                        </button>
                        <button type="button" onclick="deselectAll()" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 px-2 py-1 rounded">
                            <i class="fas fa-times-circle mr-1"></i> Deseleccionar todos
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto p-2 border border-gray-200 rounded-lg">
                    @foreach($permissions as $permission)
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="permission-{{ $permission->id }}" name="permissions[]" type="checkbox"
                                value="{{ $permission->name }}"
                                {{ $user->hasDirectPermission($permission->name) ? 'checked' : '' }}
                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded permission-checkbox">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="permission-{{ $permission->id }}" class="font-medium text-gray-700">{{ $permission->name }}</label>
                            <p class="text-xs text-gray-500 mt-1">ID: {{ $permission->id }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                @error('permissions')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('users.show', $user) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition duration-200">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>Guardar Permisos
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden p-6">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Permisos Directos Actualmente Asignados</h3>

        @if($user->permissions->count() > 0)
        <div class="flex flex-wrap gap-2">
            @foreach($user->permissions as $permission)
            <span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full flex items-center">
                {{ $permission->name }}
                <form action="{{ route('users.permissions.remove', [$user, $permission]) }}" method="POST" class="ml-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-green-500 hover:text-green-700" onclick="return confirm('¿Quitar este permiso directo al usuario?')">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </span>
            @endforeach
        </div>
        @else
        <p class="text-gray-500">Este usuario no tiene permisos directos asignados.</p>
        @endif
    </div>

    <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg overflow-hidden p-6">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Permisos Heredados de Roles</h3>

        @if($user->roles->count() > 0)
            @foreach($user->roles as $role)
                @if($role->permissions->count() > 0)
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700 mb-2">Del rol: <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm">{{ $role->name }}</span></h4>
                    <div class="flex flex-wrap gap-2 ml-4">
                        @foreach($role->permissions as $permission)
                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-1 rounded-full">
                            {{ $permission->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach
        @else
        <p class="text-gray-500">Este usuario no tiene roles asignados.</p>
        @endif
    </div>
</div>

<script>
    // Funciones para seleccionar/deseleccionar todos los permisos
    function selectAll() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deselectAll() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    // Validación básica para el formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        const checkedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked');
        if (checkedPermissions.length === 0) {
            if (!confirm('¿Estás seguro de que quieres quitar todos los permisos directos?')) {
                e.preventDefault();
            }
        }
    });
</script>
@endsection
