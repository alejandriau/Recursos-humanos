@extends('dashboard')

@section('title', 'Ver Usuario')

@section('contenido')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Detalles del Usuario</h2>
            <div class="flex space-x-2">
                <a href="{{ route('users.edit', $user) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                    <i class="fas fa-edit mr-1"></i>Editar
                </a>
                <a href="{{ route('users.roles.edit', $user) }}" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded text-sm">
                    <i class="fas fa-user-tag mr-1"></i>Roles
                </a>
                <a href="{{ route('users.permissions.edit', $user) }}" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                    <i class="fas fa-key mr-1"></i>Permisos
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Información Personal</h3>
                <div class="space-y-2">
                    <p><span class="font-medium text-gray-600">ID:</span> {{ $user->id }}</p>
                    <p><span class="font-medium text-gray-600">Nombre:</span> {{ $user->name }}</p>
                    <p><span class="font-medium text-gray-600">Email:</span> {{ $user->email }}</p>
                    <p><span class="font-medium text-gray-600">Creado:</span> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Roles Asignados</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($user->roles as $role)
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                            {{ $role->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Permisos Directos</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($user->permissions as $permission)
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                        {{ $permission->name }}
                    </span>
                @endforeach
                @if($user->permissions->isEmpty())
                    <p class="text-gray-500 text-sm">No tiene permisos directos asignados</p>
                @endif
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md transition duration-200">
                Volver a la lista
            </a>
        </div>
    </div>
</div>
@endsection
