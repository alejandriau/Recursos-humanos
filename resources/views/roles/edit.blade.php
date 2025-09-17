@extends('dashboard')

@section('title', 'Editar Roles de Usuario')

@section('contenido')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Editar Roles de: {{ $user->name }}</h2>

        <form method="POST" action="{{ route('users.roles.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Seleccionar Roles</label>
                <div class="space-y-3">
                    @foreach($roles as $role)
                    <div class="flex items-center">
                        <input type="checkbox" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->name }}"
                            {{ $user->hasRole($role->name) ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="role-{{ $role->id }}" class="ml-2 text-sm text-gray-700">{{ $role->name }}</label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('users.show', $user) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md transition duration-200">
                    Cancelar
                </a>
                <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md transition duration-200">
                    Actualizar Roles
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
