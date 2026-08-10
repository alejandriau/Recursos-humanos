@extends('layouts.baseadm')

@section('title', 'Crear Usuario')

@section('contenido')

<div class="max-w-6xl mx-auto py-8 px-4">

    <!-- Encabezado -->
    <div class="bg-gray-100 border rounded-lg p-4 flex justify-between items-center mb-8">
        <h1 class="font-bold text-gray-700 uppercase">
            Registrar cuenta de usuario administrador
        </h1>

        <div class="flex items-center text-blue-600">
            <i class="fas fa-user text-xl mr-2"></i>
            <span>{{ auth()->user()->name }}</span>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="bg-white rounded-xl shadow-lg border">

        <!-- Título -->
        <div class="text-center py-6 border-b">
            <h2 class="text-2xl font-semibold text-gray-700">
                Registro de usuario
            </h2>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="p-8">
            @csrf

            <!-- Datos -->
            <div class="grid md:grid-cols-2 gap-6">

                <div>
                    <label class="block mb-2 font-medium text-gray-700">
                        Nombre completo
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <div>
                    <label class="block mb-2 font-medium text-gray-700">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <div>
                    <label class="block mb-2 font-medium text-gray-700">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <div>
                    <label class="block mb-2 font-medium text-gray-700">
                        Confirmar contraseña
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

            </div>

            <!-- Roles -->
            <div class="mt-8">

                <h3 class="font-semibold text-gray-700 mb-4">
                    Roles del usuario
                </h3>

                <div class="grid md:grid-cols-3 gap-3">

                    @foreach($roles as $role)

                        <label
                            class="flex items-center gap-3 border rounded-lg p-3 hover:bg-blue-50 cursor-pointer transition"
                        >
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->name }}"
                                class="w-4 h-4 text-blue-600"
                            >

                            <span>{{ $role->name }}</span>
                        </label>

                    @endforeach

                </div>

            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t">

                <a
                    href="{{ route('users.index') }}"
                    class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white hover:bg-gray-100 transition"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-md transition"
                >
                    <i class="fas fa-plus mr-2"></i>
                    Crear usuario
                </button>

            </div>

        </form>

    </div>

</div>

@endsection