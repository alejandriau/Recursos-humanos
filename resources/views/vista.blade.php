<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidad de gestion de recursos humanos</title>
        <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
     <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/js/app.js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#1e3a8a',
                        'secondary': '#0ea5e9',
                        'accent': '#8b5cf6',
                        'light': '#f0f9ff',
                        'dark': '#0c4a6e'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            min-height: 100vh;
        }
        
        .sidebar {
            transition: all 0.3s ease;
            background: linear-gradient(to bottom, #1e3a8a, #0c4a6e);
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.15);
        }
        
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .submenu.active {
            max-height: 1000px;
        }
        
        .rotate-icon {
            transform: rotate(0deg);
            transition: transform 0.3s ease;
        }
        
        .rotate-icon.active {
            transform: rotate(90deg);
        }
        
        .user-menu {
            display: none;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .user-menu.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card {
            border-left: 4px solid;
        }
        
        .chart-placeholder {
            background: linear-gradient(to right, #e0f2fe, #f0f9ff);
            border: 1px dashed #0ea5e9;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex flex-col min-h-screen">
        <!-- Barra superior -->
        <header class="bg-primary text-white py-3 px-6 flex justify-between items-center shadow-lg">
            <div class="flex items-center">
                <button id="toggle-menu" class="mr-4 text-white focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl font-bold flex items-center">
                    <i class="fas fa-chart-line mr-2"></i>
                    U.G.R.H.
                </h1>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="relative">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute -top-2 -right-2 bg-accent text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">3</span>
                </div>
                
                <div class="relative">
                    <div id="user-profile" class="flex items-center cursor-pointer">
                        <div class="w-10 h-10 rounded-full bg-secondary flex items-center justify-center text-white font-bold">AD</div>
                        <div class="ml-3">
                            <p class="font-semibold">Admin User</p>
                            <p class="text-sm text-blue-200">admin@ejemplo.com</p>
                        </div>
                        <i class="fas fa-chevron-down ml-2 text-sm"></i>
                    </div>
                    
                    <!-- Menú desplegable de usuario -->
                    <div id="user-menu" class="user-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20">
                        <div class="px-4 py-2 border-b">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user mr-2"></i> Mi perfil
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i> Configuración
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-question-circle mr-2"></i> Ayuda
                        </a>
                        <div class="border-t my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-light"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex flex-1">
            <!-- Menú lateral -->
            <div class="sidebar text-white w-64 min-h-full hidden md:block">
            <!-- Mantenemos esta estructura pero modificaremos el script -->
                <nav class="mt-5">
                    <ul>
                        <li class="mb-1">
                            <a href="#" class="menu-item flex items-center p-4 hover:bg-blue-700/40">
                                <i class="fas fa-tachometer-alt text-xl mr-4"></i>
                                <span class="menu-text">Dashboard</span>
                            </a>
                        </li>
                        
                        <li class="mb-1">
                            <a href="#" class="menu-item flex items-center justify-between p-4 hover:bg-blue-700/40" 
                               onclick="toggleSubmenu('users')">
                                <div class="flex items-center">
                                    <i class="fas fa-users text-xl mr-4"></i>
                                    <span class="menu-text">Usuarios</span>
                                </div>
                                <i class="fas fa-chevron-right rotate-icon text-xs" id="users-icon"></i>
                            </a>
                            <ul class="submenu bg-blue-800/30" id="users-submenu">
                                <li><a href="#" class="block p-3 pl-12 hover:bg-blue-700/40">Todos los usuarios</a></li>
                                <li><a href="#" class="block p-3 pl-12 hover:bg-blue-700/40">Agregar usuario</a></li>
                                <li><a href="#" class="block p-3 pl-12 hover:bg-blue-700/40">Roles</a></li>
                            </ul>
                        </li>
                        <li class="mb-1">
                            <a href="<?php echo asset('/reporte'); ?>" class="menu-item flex items-center p-4 hover:bg-blue-700/40">
                                <i class="fa fa-users me-2"></i>
                                <span class="menu-text">Reportes</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="<?php echo asset('/inicio/archivos'); ?>" class="menu-item flex items-center p-4 hover:bg-blue-700/40">
                                <i class="fa fa-home me-2"></i>
                                <span class="menu-text">Reportes</span>
                            </a>
                        </li>
                        
                        <li class="mb-1">
                            <a href="#" class="menu-item flex items-center justify-between p-4 hover:bg-blue-700/40" 
                               onclick="toggleSubmenu('products')">
                                <div class="flex items-center">
                                    <i class="fas fa-box text-xl mr-4"></i>
                                    <span class="menu-text">Productos</span>
                                </div>
                                <i class="fas fa-chevron-right rotate-icon text-xs" id="products-icon"></i>
                            </a>
                            <ul class="submenu bg-blue-800/30" id="products-submenu">
                                <li><a href="#" class="block p-3 pl-12 hover:bg-blue-700/40">Inventario</a></li>
                                <li><a href="#" class="block p-3 pl-12 hover:bg-blue-700/40">Categorías</a></li>
                                <li><a href="#" class="block p-3 pl-12 hover:bg-blue-700/40">Ofertas</a></li>
                            </ul>
                        </li>
                        
                        <li class="mb-1">
                            <a href="#" class="menu-item flex items-center p-4 hover:bg-blue-700/40">
                                <i class="fas fa-shopping-cart text-xl mr-4"></i>
                                <span class="menu-text">Pedidos</span>
                            </a>
                        </li>
                        
                        
                        <li class="mb-1">
                            <a href="#" class="menu-item flex items-center justify-between p-4 hover:bg-blue-700/40" 
                               onclick="toggleSubmenu('settings')">
                                <div class="flex items-center">
                                    <i class="fas fa-cog text-xl mr-4"></i>
                                    <span class="menu-text">Configuración</span>
                                </div>
                                <i class="fas fa-chevron-right rotate-icon text-xs" id="settings-icon"></i>
                            </a>
                            <ul class="submenu bg-blue-800/30" id="settings-submenu">
                                <li><a href="#" class="block p-3 pl-12 hover:bg-blue-700/40">General</a></li>
                                <li><a href="#" class="block p-3 pl-12 hover:bg-blue-700/40">Apariencia</a></li>
                                <li><a href="#" class="block p-3 pl-12 hover:bg-blue-700/40">Seguridad</a></li>
                            </ul>
                        </li>
                        
                        <li class="mt-10 px-4">
                            <div class="bg-blue-700/30 p-4 rounded-lg">
                                <h3 class="font-bold flex items-center">
                                    <i class="fas fa-star mr-2 text-yellow-300"></i>
                                    Actualización disponible
                                </h3>
                                <p class="text-sm mt-2 text-blue-100">Versión 3.2 con nuevas funciones</p>
                                <button class="mt-3 w-full bg-secondary hover:bg-blue-500 text-white py-2 rounded-lg text-sm">
                                    Actualizar ahora
                                </button>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Contenido principal -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <main class="flex-1 overflow-y-auto p-6 bg-light">
                    @yield('contenido')
                </main>
                
                <!-- Footer -->
                <footer class="bg-white py-4 px-6 border-t border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="text-gray-600 mb-4 md:mb-0">
                            &copy; 2023 AdminDashboard. Todos los derechos reservados.
                        </div>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-600 hover:text-primary">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="#" class="text-gray-600 hover:text-primary">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="text-gray-600 hover:text-primary">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="text-gray-600 hover:text-primary">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- Modal de cierre de sesión -->
    <div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-sign-out-alt text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">¿Cerrar sesión?</h3>
                <p class="text-gray-600 mb-6">¿Estás seguro que deseas salir de tu cuenta?</p>
                
                <div class="flex justify-center space-x-4">
                    <button id="cancel-logout" class="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button id="confirm-logout" class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Sí, cerrar sesión
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
    // Función para alternar los submenús
    function toggleSubmenu(id) {
        event.preventDefault();
        const submenu = document.getElementById(`${id}-submenu`);
        const icon = document.getElementById(`${id}-icon`);
        
        submenu.classList.toggle('active');
        icon.classList.toggle('active');
    }
    
    // Botón para ocultar/mostrar el menú
    document.getElementById('toggle-menu').addEventListener('click', function() {
        const sidebar = document.querySelector('.sidebar');
        //sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('md:block');
    });
    
    // Mostrar fecha actual
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('current-date').textContent = now.toLocaleDateString('es-ES', options);
    
    // Menú desplegable de usuario
    const userProfile = document.getElementById('user-profile');
    const userMenu = document.getElementById('user-menu');
    
    userProfile.addEventListener('click', function() {
        userMenu.classList.toggle('active');
    });
    
    // CERRAR MENÚ AL HACER CLIC FUERA (CORRECCIÓN)
    document.addEventListener('click', function(event) {
        if (!userProfile.contains(event.target)) {  // Paréntesis añadido aquí
            userMenu.classList.remove('active');
        }
    });
    
    // Funcionalidad de cierre de sesión
    const logoutBtn = document.getElementById('logout-btn');
    const logoutModal = document.getElementById('logout-modal');
    const cancelLogout = document.getElementById('cancel-logout');
    const confirmLogout = document.getElementById('confirm-logout');
    
    logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        logoutModal.classList.remove('hidden');
        userMenu.classList.remove('active');
    });
    
    cancelLogout.addEventListener('click', function() {
        logoutModal.classList.add('hidden');
    });
    
    confirmLogout.addEventListener('click', function() {
        logoutModal.classList.add('hidden');
        alert('Sesión cerrada con éxito. Redirigiendo...');
        // Aquí iría la lógica real de cierre de sesión
    });
    
    // Cerrar modal al hacer clic fuera
    logoutModal.addEventListener('click', function(e) {
        if (e.target === logoutModal) {
            logoutModal.classList.add('hidden');
        }
    });
</script>

</body>
</html>