<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body>
            <div class="w-full bg-blue-800 text-white py-2 sm:py-3 px-4 shadow-lg flex flex-col
                border-b-4 border-blue-600" style="background-color: #4DA3FF;">
                <h1 class="text-lg sm:text-2xl font-bold tracking-wide">Sistema de Salidas</h1>
                <p class="text-xs sm:text-sm font-medium text-blue-100">Unidad de Gestión de Recursos Humanos</p>
            </div>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
