<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UGRH - Sistema Integrado')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts & Bootstrap 5 + Font Awesome 6 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #eef1f5;
            overflow-x: hidden;
        }

        /* ===== SIDEBAR ESTILO GADC ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 260px;
            background: #5a5e6a;
            color: #e8eaed;
            transition: all 0.3s ease;
            z-index: 1050;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .sidebar-brand span,
        .sidebar.collapsed .nav-link span:not(.badge),
        .sidebar.collapsed .sidebar-user-info {
            display: none;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
            font-size: 1.3rem;
        }

        .sidebar.collapsed .nav-link {
            text-align: center;
            padding: 0.75rem 0;
            justify-content: center;
        }

        .sidebar.collapsed .dropdown-toggle::after {
            display: none;
        }

        /* Header del sidebar con logo */
        .sidebar-header {
            background: #6b6f7c;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            min-height: 60px;
        }

        .sidebar-brand {
            padding: 0.8rem 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        .sidebar-brand img {
            width: 36px;
            height: 36px;
            border-radius: 8px;
        }

        .sidebar-brand h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
            color: white;
            letter-spacing: 0.5px;
        }

        /* Toggle button dentro del sidebar */
        .sidebar-toggle-btn {
            background: none;
            border: none;
            color: rgba(255,255,255,0.7);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.8rem 1rem;
            border-right: 1px solid rgba(255,255,255,0.1);
            transition: color 0.2s;
        }

        .sidebar-toggle-btn:hover {
            color: white;
        }

        .sidebar.collapsed .sidebar-toggle-btn {
            border-right: none;
            width: 100%;
            text-align: center;
        }

        /* Usuario en sidebar */
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.15);
            margin: 0.5rem;
            border-radius: 8px;
        }

        .sidebar-user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #7cb342;
        }

        .sidebar-user-info h6 {
            font-size: 0.85rem;
            font-weight: 600;
            margin: 0;
            color: white;
        }

        .sidebar-user-info span {
            font-size: 0.7rem;
            opacity: 0.75;
            color: #c5c9d0;
        }

        /* Navegación */
        .sidebar-nav {
            flex: 1;
            padding: 0.5rem 0;
        }

        .nav-link {
            color: #d0d3d8;
            padding: 0.65rem 1.2rem;
            margin: 1px 0.5rem;
            border-radius: 6px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .nav-link i {
            width: 22px;
            font-size: 1rem;
            text-align: center;
            color: #b0b4bc;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link:hover i {
            color: white;
        }

        .nav-item.active > .nav-link {
            background: #4a7c59;
            color: white;
        }

        .nav-item.active > .nav-link i {
            color: white;
        }

        /* Dropdown items */
        .dropdown-menu {
            background-color: #4e515c;
            border: none;
            border-radius: 6px;
            padding: 0.3rem;
            margin: 0 0.5rem 0.5rem 0.5rem;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        }

        .dropdown-menu .dropdown-item {
            color: #d0d3d8;
            border-radius: 4px;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dropdown-menu .dropdown-item i {
            width: 18px;
            text-align: center;
            font-size: 0.85rem;
            color: #b0b4bc;
        }

        .dropdown-menu .dropdown-item:hover {
            background: #5a7d6a;
            color: white;
        }

        .dropdown-menu .dropdown-item:hover i {
            color: white;
        }

        .dropdown-toggle::after {
            margin-left: auto;
            font-size: 0.7rem;
        }

        /* Botón de validar salidas estilo especial */
        .nav-item.validar-salidas .nav-link {
            background: #7e57c2;
            color: white;
            margin: 0.5rem;
        }

        .nav-item.validar-salidas .nav-link i {
            color: white;
        }

        .nav-item.validar-salidas .nav-link:hover {
            background: #6a4aaa;
        }

        .badge-salidas {
            background: #5c6bc0;
            color: white;
            font-size: 0.75rem;
            padding: 0.15rem 0.5rem;
            border-radius: 10px;
            margin-left: auto;
        }

        /* CONTENIDO PRINCIPAL */
        .main-content {
            margin-left: 260px;
            transition: margin-left 0.3s;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: 70px;
        }

        /* TOPBAR */
        .topbar {
            background: white;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1040;
            min-height: 60px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .topbar-title {
            background: #dbeafe;
            color: #1e4a62;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            flex: 1;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0 1.5rem;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4a5568;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .topbar-user img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
        }

        .btn-icon {
            background: none;
            border: none;
            color: #6b7280;
            font-size: 1.1rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s;
            position: relative;
        }

        .btn-icon:hover {
            background: #f3f4f6;
            color: #374151;
        }

        /* NOTIFICACIONES */
        .notification-dropdown {
            min-width: 360px;
            max-height: 450px;
            overflow-y: auto;
            background: #2d3748;
            border-radius: 12px;
            border: none;
            padding: 0;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }

        .notification-header {
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .notification-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .notification-item.unread {
            background: rgba(91, 192, 222, 0.1);
            border-left-color: #5bc0de;
        }

        .notification-item.read {
            opacity: 0.7;
        }

        .notification-item:hover {
            background: rgba(255,255,255,0.05);
        }

        .notification-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(91, 192, 222, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-icon i {
            color: #5bc0de;
            font-size: 0.9rem;
        }

        /* CHATBOT FLOTANTE */
        .chatbot-float {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 56px;
            height: 56px;
            background: #5a5e6a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.2s;
            z-index: 1060;
        }

        .chatbot-float:hover {
            transform: scale(1.05);
            background: #4a7c59;
        }

        .chatbot-modal .modal-dialog {
            max-width: 800px;
            margin: 1.75rem auto;
        }

        .chatbot-iframe {
            width: 100%;
            height: 550px;
            border: none;
            border-radius: 20px;
        }

        /* FOOTER */
        .footer {
            background: white;
            padding: 1rem 1.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            margin-top: auto;
        }

        .footer a {
            color: #4a7c59;
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 260px;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0 !important;
            }
            .notification-dropdown {
                min-width: 300px;
            }
            .topbar-title {
                font-size: 0.8rem;
                padding: 0.8rem 1rem;
            }
        }

        .content-wrapper {
            padding: 1.5rem;
            flex: 1;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #9ca3af;
            border-radius: 6px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="sidebar-toggle-btn" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <div class="sidebar-brand">
                <img src="{{ asset('dashmin/img/logo-gob.png') }}" alt="Logo">
                <h3>UGRH</h3>
            </div>
        </div>

        <div class="sidebar-user">
            <img src="{{ asset('dashmin/img/user.jpg') }}" alt="User">
            <div class="sidebar-user-info">
                <h6>{{ Auth::user()->name }}</h6>
                <span>{{ Auth::user()->getRoleNames()->first() ?? 'Sin rol' }}</span>
            </div>
        </div>

        <nav class="nav flex-column sidebar-nav">
            <!-- ========== MENÚ UNIFICADO ========== -->
            @can('ver inicio')
                <div class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-home"></i> <span>Inicio</span>
                    </a>
                </div>
            @endcan

            <!-- USUARIOS -->
            @can('ver usuarios')
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-users"></i> <span>Usuarios</span>
                    </a>
                    <div class="dropdown-menu">
                        @can('ver usuarios')
                            <a href="{{ asset('/users') }}" class="dropdown-item"><i class="fas fa-user-plus"></i>Crear/Modificar</a>
                        @endcan
                        @can('ver roles')
                            <a href="{{ asset('/roles') }}" class="dropdown-item"><i class="fas fa-user-shield"></i>Roles</a>
                        @endcan
                    </div>
                </div>
            @endcan

            <!-- Personal / Reportes -->
            @can('ver personal')
                <div class="nav-item">
                    <a href="{{ asset('/reporte') }}" class="nav-link"><i class="fas fa-users"></i> <span>Personal</span></a>
                </div>
            @endcan

            <!-- Documentación -->
            @can('ver inicio')
                <div class="nav-item">
                    <a href="{{ asset('/inicio/archivos') }}" class="nav-link"><i class="fas fa-folder-open"></i> <span>Documentación</span></a>
                </div>
            @endcan

            <!-- Organizacional -->
            @can('ver organizacional')
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-sitemap"></i> <span>Organizacional</span></a>
                    <div class="dropdown-menu">
                        <a href="{{ asset('/admin/puestos') }}" class="dropdown-item"><i class="fas fa-briefcase"></i> Puestos</a>
                        <a href="{{ asset('/unidades') }}" class="dropdown-item"><i class="fas fa-project-diagram"></i> Organigrama</a>
                    </div>
                </div>
            @endcan

            <!-- Asignar Item -->
            @can('ver asignar item')
                <div class="nav-item">
                    <a href="{{ asset('/historial') }}" class="nav-link"><i class="fas fa-tasks"></i> <span>Asignar item</span></a>
                </div>
            @endcan

            <!-- Biblioteca Planillas -->
            @can('ver biblioteca planillas')
                <div class="nav-item">
                    <a href="{{ asset('/planillas-pdf/index') }}" class="nav-link"><i class="fas fa-file-alt"></i> <span>Biblioteca planillas</span></a>
                </div>
            @endcan

            <!-- Pasivos -->
            @can('ver pasivos')
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-archive"></i> <span>Pasivos</span></a>
                    <div class="dropdown-menu">
                        <a href="{{ asset('/pasivouno') }}" class="dropdown-item"><i class="fas fa-user-alt-slash"></i> 1 EX CORDECO</a>
                        <a href="{{ asset('/pasivodos') }}" class="dropdown-item"><i class="fas fa-user-slash"></i> 2 GADC</a>
                    </div>
                </div>
            @endcan

            <!-- Bajas -->
            @can('ver bajas')
                <div class="nav-item">
                    <a href="{{ asset('/altasbajas/index') }}" class="nav-link"><i class="fas fa-user-minus"></i> <span>Bajas</span></a>
                </div>
            @endcan

            <!-- Reportes avanzados -->
            @can('ver configuracion')
                <div class="nav-item">
                    <a href="{{ route('reportes.dashboard') }}" class="nav-link"><i class="fas fa-chart-line"></i> <span>Reportes</span></a>
                </div>
            @endcan

            <!-- Documentos respaldo -->
            @can('ver configuracion')
                <div class="nav-item">
                    <a href="{{ route('documentos.index') }}" class="nav-link"><i class="fas fa-folder"></i> <span>Doc. Respaldo</span></a>
                </div>
            @endcan

            <!-- Opciones del primer sistema -->
            @can('ver configuracion')
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-cog"></i> <span>Opciones</span></a>
                    <div class="dropdown-menu">
                        <a href="{{ asset('/apertura-gestion/gestion') }}" class="dropdown-item"><i class="fas fa-calendar"></i> Gestión</a>
                        <a href="{{ asset('/feriado-gestion/feriado') }}" class="dropdown-item"><i class="fas fa-calendar-day"></i> Feriados</a>
                        <a href="{{ asset('/tipo-salida/salida') }}" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Tipo de salida</a>
                        <a href="{{ asset('/beneficios/asignar') }}" class="dropdown-item"><i class="fas fa-gift"></i> Beneficios</a>
                        <a href="{{ asset('/cas') }}" class="dropdown-item"><i class="fas fa-id-badge"></i> CAS</a>
                        <a href="{{ asset('/kardex') }}" class="dropdown-item"><i class="fas fa-clipboard-list"></i> Kardex</a>
                    </div>
                </div>
            @endcan

            <!-- Vacaciones (admin) -->
            @role('admin')
                <div class="nav-item">
                    <a href="{{ route('admin.vacaciones.index') }}" class="nav-link"><i class="fas fa-umbrella-beach"></i> <span>Vacaciones</span></a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link"><i class="fas fa-clock"></i> <span>Asistencias</span></a>
                </div>
            @endrole

            <!-- Prestamos (archivo) -->
            @role('archivo')
                <div class="nav-item">
                    <a href="{{ route('prestamos.index') }}" class="nav-link"><i class="fas fa-hand-holding-heart"></i> <span>Préstamos</span></a>
                </div>
            @endrole

            <!-- Empleado -->
            @role('empleado')
                <div class="nav-item">
                    <a href="{{ route('empleado.dashboard') }}" class="nav-link"><i class="fas fa-id-card"></i> <span>Mi Dashboard</span></a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('empleado.asistencias.index') }}" class="nav-link"><i class="fas fa-calendar-check"></i> <span>Mis Asistencias</span></a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('empleado.vacaciones.index') }}" class="nav-link"><i class="fas fa-umbrella-beach"></i> <span>Mis Vacaciones</span></a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('empleado.perfil') }}" class="nav-link"><i class="fas fa-user"></i> <span>Mi Perfil</span></a>
                </div>
            @endrole

            <!-- Validar salidas -->
            <div class="nav-item validar-salidas">
                <a href="{{ asset('/solicitudes/dashboard') }}" class="nav-link">
                    <i class="fas fa-check-double"></i> <span>Validar salidas</span>
                    <span class="badge-salidas">0</span>
                </a>
            </div>

        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <div class="topbar-title">
                    <i class="fas fa-building me-2"></i>
                    SISTEMA DE SALIDAS, UNIDAD DE GESTIÓN DE RECURSOS HUMANOS
                </div>
            </div>
            <div class="topbar-right">
                <!-- NOTIFICACIONES -->
                <div class="dropdown">
                    <button class="btn-icon" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span id="notifCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.6rem;">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0" id="notificationList">
                        <div class="notification-header">Notificaciones</div>
                        <div class="text-center p-4 text-white-50">Cargando...</div>
                    </div>
                </div>

                <!-- PERFIL -->
                <div class="dropdown">
                    <a href="#" class="topbar-user dropdown-toggle text-decoration-none" data-bs-toggle="dropdown">
                        <img src="{{ asset('dashmin/img/user.jpg') }}" alt="User">
                        <span class="d-none d-sm-block">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 200px;">
                        <li><a class="dropdown-item py-2" href="#"><i class="fas fa-user me-2 text-muted"></i> Mi Perfil</a></li>
                        <li><a class="dropdown-item py-2" href="#"><i class="fas fa-cog me-2 text-muted"></i> Ajustes</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger"><i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>

                <a href="#" class="btn-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-power-off"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>

        <div class="content-wrapper">
            @yield('content')
            @yield('contenido')
        </div>

        <!-- FOOTER -->
<footer class="footer">
    <div class="container-fluid">
        <div class="text-center">
            <i class="far fa-copyright me-1"></i>
            {{ date('Y') }} Sistema UGRH - GADC

            Todos los Derechos Reservados
            <br>
            Desarrollado por Jhesvany Bozo Condori - Archivo de Recursos Humanos UGRH
        </div>
    </div>
</footer>
    </div>

    <!-- CHATBOT FLOTANTE + MODAL -->
    <div class="chatbot-float" id="openChatbotBtn">
        <i class="fas fa-robot"></i>
    </div>

    <div class="modal fade" id="chatbotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0">
                    <iframe src="{{ url('/chatbot-content') }}" class="chatbot-iframe" title="Asistente Bozo"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF VIEWER MODAL GLOBAL -->
    <div class="modal fade" id="pdfViewerModalGlobal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Visualizador de Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="pdfIframe" src="" width="100%" height="600px" style="border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <a id="descargarPdfLink" href="#" class="btn btn-primary" download>Descargar PDF</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Sidebar toggle
            $('#sidebarToggle').on('click', function() {
                $('#sidebar').toggleClass('collapsed');
                if ($(window).width() <= 768) {
                    $('#sidebar').toggleClass('mobile-open');
                }
            });

            // Responsive: cerrar sidebar en móvil al hacer clic en link
            $('.sidebar .nav-link').on('click', function() {
                if ($(window).width() <= 768) {
                    $('#sidebar').removeClass('mobile-open');
                }
            });

            // ==================== NOTIFICACIONES ====================
            let userInteracted = false;
            let ultimoConteo = null;
            let audioNotif = new Audio('/sounds/clin.mp3');
            audioNotif.volume = 0.6;

            function marcarInteraccion() { userInteracted = true; }
            document.body.addEventListener('click', marcarInteraccion, { once: true });
            document.body.addEventListener('keydown', marcarInteraccion, { once: true });

            function playSound() {
                if (!userInteracted) return;
                audioNotif.play().catch(e => console.log("Audio no permitido"));
            }

            function cargarNotificaciones() {
                $.get('/notificaciones', function(data) {
                    let html = '<div class="notification-header">Notificaciones</div>';
                    let noLeidas = 0;
                    if (data.length === 0) {
                        html += '<div class="text-center p-4 text-white-50"><i class="far fa-bell mb-2 d-block fs-4"></i>No hay notificaciones</div>';
                    } else {
                        data.forEach(notif => {
                            const leida = notif.read_at !== null;
                            if (!leida) noLeidas++;
                            let icono = 'fas fa-bell';
                            if (notif.data.tipo === 'nueva_solicitud') icono = 'fas fa-clock';
                            if (notif.data.tipo === 'prestamo_aprobado') icono = 'fas fa-check-circle';
                            if (notif.data.tipo === 'prestamo_rechazado') icono = 'fas fa-times-circle';
                            html += `
                                <a href="${notif.data.url || '#'}" class="notification-item ${leida ? 'read' : 'unread'}" data-id="${notif.id}">
                                    <div class="notification-icon"><i class="${icono}"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-white" style="font-size: 0.85rem;">${notif.data.titulo || 'Notificación'}</div>
                                        <div class="small text-white-50" style="font-size: 0.75rem;">${notif.data.mensaje}</div>
                                        <div class="small text-white-50" style="font-size: 0.7rem;"><i class="far fa-clock me-1"></i>${new Date(notif.created_at).toLocaleString()}</div>
                                    </div>
                                </a>
                            `;
                        });
                    }
                    $('#notificationList').html(html);
                    if (noLeidas > 0) {
                        $('#notifCount').text(noLeidas).show();
                        if (ultimoConteo !== null && noLeidas > ultimoConteo) playSound();
                    } else {
                        $('#notifCount').hide();
                    }
                    ultimoConteo = noLeidas;
                });
            }

            cargarNotificaciones();
            setInterval(cargarNotificaciones, 30000);

            $(document).on('click', '.notification-item', function(e) {
                let id = $(this).data('id');
                if (id) {
                    $.post('/notificaciones/marcar-leida/' + id, { _token: '{{ csrf_token() }}' });
                }
            });

            // ==================== CHATBOT ====================
            $('#openChatbotBtn').on('click', function() {
                const modalEl = new bootstrap.Modal(document.getElementById('chatbotModal'));
                modalEl.show();
            });
        });

        // función global para ver PDF
        function verPDF(url) {
            document.getElementById('pdfIframe').src = url;
            document.getElementById('descargarPdfLink').href = url;
            const modal = new bootstrap.Modal(document.getElementById('pdfViewerModalGlobal'));
            modal.show();
        }
    </script>

    @stack('scripts')
</body>
</html>s







