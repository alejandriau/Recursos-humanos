<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Sistema de Recursos Humanos</title>

        <!-- Fonts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bundy.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */
                /* Se mantienen los estilos de Tailwind CSS */
                /* ... (estilos tailwind existentes) ... */

                /* Estilos adicionales para el sistema de RRHH */
                .dashboard-card {
                    transition: all 0.3s ease;
                }
                .dashboard-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
                }
                .progress-bar {
                    transition: width 1s ease-in-out;
                }
                .stat-card {
                    border-left: 4px solid;
                }
            </style>
        @endif
    </head>
    <body class="bg-[#F5F7F9] text-[#2D3436] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-6xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
            @if (Route::has('login'))
                <nav class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 32C24.8366 32 32 24.8366 32 16C32 7.16344 24.8366 0 16 0C7.16344 0 0 7.16344 0 16C0 24.8366 7.16344 32 16 32Z" fill="#3B82F6"/>
                            <path d="M21 13H11C10.4477 13 10 13.4477 10 14V22C10 22.5523 10.4477 23 11 23H21C21.5523 23 22 22.5523 22 22V14C22 13.4477 21.5523 13 21 13Z" fill="white"/>
                            <path d="M16 12C17.1046 12 18 11.1046 18 10C18 8.89543 17.1046 8 16 8C14.8954 8 14 8.89543 14 10C14 11.1046 14.8954 12 16 12Z" fill="white"/>
                        </svg>
                        <span class="font-semibold text-lg">HRSystem</span>
                    </div>
                    <div class="flex items-center justify-end gap-4">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="inline-block px-5 py-1.5 border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] rounded-sm text-sm leading-normal"
                            >
                                Dashboard
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-block px-5 py-1.5 text-[#1b1b18] border border-transparent hover:border-[#19140035] rounded-sm text-sm leading-normal"
                            >
                                Iniciar Sesión
                            </a>

                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-block px-5 py-1.5 bg-blue-600 hover:bg-blue-700 border border-blue-600 text-white rounded-sm text-sm leading-normal">
                                    Registrarse
                                </a>
                            @endif
                        @endauth
                    </div>
                </nav>
            @endif
        </header>

        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <main class="w-full max-w-6xl">
                <!-- Hero Section -->
                <div class="bg-white rounded-lg shadow-sm p-8 mb-8">
                    <div class="flex flex-col lg:flex-row items-center justify-between">
                        <div class="lg:w-1/2 mb-8 lg:mb-0">
                            <h1 class="text-3xl lg:text-4xl font-bold mb-4">Sistema Integral de Recursos Humanos</h1>
                            <p class="text-lg text-gray-600 mb-6">Gestiona tu talento humano de manera eficiente con nuestra plataforma todo en uno.</p>
                            <div class="flex flex-wrap gap-3">
                                <a href="#" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                                    Demo Interactivo
                                </a>
                                <a href="#" class="px-6 py-3 border border-gray-300 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                                    Más Información
                                </a>
                            </div>
                        </div>
                        <div class="lg:w-1/2 flex justify-center">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgPHJlY3Qgd2lkdGg9IjQwMCIgaGVpZ2h0PSIzMDAiIGZpbGw9IiNGNUY3RjkiLz4KICA8Y2lyY2xlIGN4PSIyMDAiIGN5PSIxNTAiIHI9IjcwIiBmaWxsPSIjM0I4MkY2IiBmaWxsLW9wYWNpdHk9IjAuMiIvPgogIDxjaXJjbGUgY3g9IjE1MCIgY3k9IjEyMCIgcj0iNDAiIGZpbGw9IndoaXRlIi8+CiAgPGNpcmNsZSBjeD0iMjUwIiBjeT0iMTIwIiByPSI0MCIgZmlsbD0id2hpdGUiLz4KICA8cmVjdCB4PSIxNTAiIHk9IjE4MCIgd2lkdGg9IjEwMCIgaGVpZ2h0PSI0MCIgcng9IjIwIiBmaWxsPSJ3aGl0ZSIvPgogIDxwYXRoIGQ9Ik0xMDAgMjQwQzEzMCAyNDAgMTUwIDI2MCAxNTAgMjkwIiBzdHJva2U9IiMzQjgyRjYiIHN0cm9rZS13aWR0aD0iOCIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+CiAgPHBhdGggZD0iTTMwMCAyNDBDMjcwIDI0MCAyNTAgMjYwIDI1MCAyOTAiIHN0cm9rZT0iIzNCODJGNiIgc3Ryb2tlLXdpZHRoPSI4IiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KPC9zdmc+Cg==" alt="Sistema de RRHH" class="max-w-full h-auto">
                        </div>
                    </div>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Card 1 -->
                    <div class="dashboard-card bg-white rounded-lg shadow-sm p-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Gestión de Empleados</h3>
                        <p class="text-gray-600">Administra toda la información de tus colaboradores en un solo lugar.</p>
                    </div>

                    <!-- Card 2 -->
                    <div class="dashboard-card bg-white rounded-lg shadow-sm p-6">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Nómina y Beneficios</h3>
                        <p class="text-gray-600">Automatiza el cálculo de nómina y gestión de beneficios para empleados.</p>
                    </div>

                    <!-- Card 3 -->
                    <div class="dashboard-card bg-white rounded-lg shadow-sm p-6">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Control de Asistencia</h3>
                        <p class="text-gray-600">Registro y seguimiento de horarios, vacaciones y permisos.</p>
                    </div>

                    <!-- Card 4 -->
                    <div class="dashboard-card bg-white rounded-lg shadow-sm p-6">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Evaluaciones de Desempeño</h3>
                        <p class="text-gray-600">Sistema completo para evaluar y desarrollar el talento en tu organización.</p>
                    </div>

                    <!-- Card 5 -->
                    <div class="dashboard-card bg-white rounded-lg shadow-sm p-6">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m7 1V3m-3 18v-4m6 4v-4" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Reclutamiento</h3>
                        <p class="text-gray-600">Atrae, selecciona y contrata al mejor talento para tu empresa.</p>
                    </div>

                    <!-- Card 6 -->
                    <div class="dashboard-card bg-white rounded-lg shadow-sm p-6">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Reportes y Analytics</h3>
                        <p class="text-gray-600">Obtén insights valiosos sobre tu fuerza laboral con reportes detallados.</p>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="bg-white rounded-lg shadow-sm p-8 mb-8">
                    <h2 class="text-2xl font-bold mb-6">Nuestro Impacto</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="stat-card border-blue-500 bg-blue-50 p-4 rounded-r-lg">
                            <p class="text-3xl font-bold text-blue-600">500+</p>
                            <p class="text-gray-600">Empresas</p>
                        </div>
                        <div class="stat-card border-green-500 bg-green-50 p-4 rounded-r-lg">
                            <p class="text-3xl font-bold text-green-600">50k+</p>
                            <p class="text-gray-600">Empleados</p>
                        </div>
                        <div class="stat-card border-purple-500 bg-purple-50 p-4 rounded-r-lg">
                            <p class="text-3xl font-bold text-purple-600">98%</p>
                            <p class="text-gray-600">Satisfacción</p>
                        </div>
                        <div class="stat-card border-yellow-500 bg-yellow-50 p-4 rounded-r-lg">
                            <p class="text-3xl font-bold text-yellow-600">24/7</p>
                            <p class="text-gray-600">Soporte</p>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="bg-blue-600 rounded-lg shadow-sm p-8 text-center text-white">
                    <h2 class="text-2xl font-bold mb-4">¿Listo para transformar tu gestión de recursos humanos?</h2>
                    <p class="mb-6 opacity-90">Comienza hoy mismo y descubre cómo podemos ayudar a tu organización.</p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="#" class="px-6 py-3 bg-white text-blue-600 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                            Solicitar Demo
                        </a>
                        <a href="#" class="px-6 py-3 border border-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            Contactar Ventas
                        </a>
                    </div>
                </div>
            </main>
        </div>

        <footer class="w-full lg:max-w-6xl max-w-[335px] mt-12 text-center text-sm text-gray-500">
            <p>© 2023 HRSystem. Todos los derechos reservados.</p>
        </footer>
    </body>
</html>
