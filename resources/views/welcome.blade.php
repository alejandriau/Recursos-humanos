{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('images/logo-gob.png') }}" rel="icon" type="image/png">
    <title>UGRH · GADC</title>
    @vite(['resources/css/app.css'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }
        .card { transition: all 0.2s ease; border: 1px solid rgba(0,0,0,0.05); }
        .card:hover { transform: translateY(-4px); box-shadow: 0 12px 20px -12px rgba(0,0,0,0.15); border-color: #bfdbfe; }
    </style>
</head>
<body class="antialiased">

    <!-- BARRA SUPERIOR: solo información general -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-20 shadow-sm">
        <div class="max-w-6xl mx-auto px-5 py-3 flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-gob.png') }}" alt="GADC" class="h-10 w-auto">
                <div class="leading-tight">
                    <p class="text-sm font-semibold text-gray-800">Gobierno Autónomo Departamental de Cochabamba</p>
                    <p class="text-xs text-blue-700">Unidad de Gestión de Recursos Humanos</p>
                </div>
            </div>
            <div class="text-xs text-gray-500 text-right">
                <p><i class="far fa-building"></i> Gestión pública transparente</p>
                <p class="text-[11px] mt-0.5"><i class="far fa-clock"></i> Información general · Reglamento interno</p>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-5 py-10">
        
        <!-- Título principal -->
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Sistema de Recursos Humanos</h1>
            <p class="text-gray-500 mt-2">Acceso unificado para servidores públicos del GADC</p>
        </div>

        <!-- Tarjetas de acceso: Condicional según autenticación -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-3xl mx-auto">
            
            @guest
                <!-- Usuario no autenticado: mostrar login y registro -->
                <a href="{{ route('login') }}" class="card bg-white rounded-2xl p-6 text-center group">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-600 group-hover:text-white transition">
                        <i class="fas fa-sign-in-alt text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-lg">Iniciar Sesión</h3>
                    <p class="text-xs text-gray-400 mt-1">Accede con tu cuenta institucional</p>
                    <span class="inline-block mt-3 text-blue-500 text-sm">→</span>
                </a>

                <a href="{{ route('register') }}" class="card bg-white rounded-2xl p-6 text-center group">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-emerald-600 group-hover:text-white transition">
                        <i class="fas fa-user-plus text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-lg">Registro</h3>
                    <p class="text-xs text-gray-400 mt-1">Crea una nueva cuenta de usuario</p>
                    <span class="inline-block mt-3 text-emerald-500 text-sm">→</span>
                </a>

                <!-- Tercer espacio: mensaje informativo (opcional) -->
                <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-200">
                    <div class="w-16 h-16 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-info-circle text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-500">Acceso restringido</h3>
                    <p class="text-xs text-gray-400 mt-1">Regístrate o inicia sesión para continuar</p>
                </div>
            @else
                <!-- Usuario autenticado: mostrar solo acceso al Dashboard y bienvenida -->
                <div class="md:col-span-3 flex justify-center">
                    <a href="{{ route('dashboard') }}" class="card bg-white rounded-2xl p-8 text-center group w-full max-w-md">
                        <div class="w-20 h-20 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-purple-600 group-hover:text-white transition">
                            <i class="fas fa-chart-line text-3xl"></i>
                        </div>
                        <h3 class="font-bold text-2xl text-gray-800">¡Bienvenido, {{ Auth::user()->name }}!</h3>
                        <p class="text-sm text-gray-500 mt-2">Accede a tu panel de control y métricas</p>
                        <span class="inline-block mt-4 text-purple-600 font-medium">Ir al Dashboard →</span>
                    </a>
                </div>
            @endguest
        </div>

        <!-- SECCIÓN DE INFORMACIÓN GENERAL / REGLAMENTO -->
        <div class="mt-16 bg-white rounded-xl border border-gray-200 p-6 shadow-sm max-w-3xl mx-auto">
            <div class="flex items-center gap-3 border-b border-gray-100 pb-3 mb-4">
                <i class="fas fa-gavel text-blue-500 text-xl"></i>
                <h2 class="font-semibold text-gray-800 text-lg">Información general y régimen normativo</h2>
            </div>
            <div class="text-sm text-gray-600 space-y-2">
                <p>La Unidad de Gestión de Recursos Humanos del GADC opera en el marco de la Constitución Política del Estado, la Ley Marco de Autonomías y la Ley N° 1178 de Administración y Control Gubernamentales. Todo el personal de la institución se rige por el Estatuto del Funcionario Público y las disposiciones departamentales vigentes.</p>
                <p class="mt-2">El <strong>Reglamento Interno de Personal</strong> establece los derechos, deberes y prohibiciones de las servidoras y servidores públicos, así como los procedimientos para la evaluación del desempeño, licencias, permisos y régimen disciplinario. Para mayor detalle, consulte la normativa publicada en los canales oficiales de la Gobernación.</p>
                <div class="pt-2 text-xs text-blue-500">
                    <i class="fas fa-balance-scale"></i> Base legal: Ley 2027, DS 26115 (NB-SAP), Resoluciones Administrativas vigentes.
                </div>
            </div>
        </div>

        <!-- Pie de página simple -->
        <footer class="mt-12 text-center text-xs text-gray-400 border-t border-gray-200 pt-5">
            <p>Gobierno Autónomo Departamental de Cochabamba · Unidad de Gestión de Recursos Humanos</p>
            <p class="mt-1">Cochabamba - Bolivia</p>
        </footer>
    </main>
</body>
</html>