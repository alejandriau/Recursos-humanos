<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Panel de Administración UGRH</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    @yield('contenidouno')

    <!-- Favicon -->
    <link href="<?php echo asset('dashmin'); ?>/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="<?php echo asset('dashmin'); ?>/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="<?php echo asset('dashmin'); ?>/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="<?php echo asset('dashmin'); ?>/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>




    <!-- Template Stylesheet -->
    <link href="<?php echo asset('dashmin'); ?>/css/style.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/js/app.js')


    <style>
        :root {
            --primary-color: #2c5282; /* Azul medio oscuro principal */
            --secondary-color: #3182ce; /* Azul medio */
            --accent-color: #4299e1; /* Azul claro */
            --dark-color: #1a365d; /* Azul oscuro */
            --light-color: #ebf8ff; /* Azul muy claro */
            --text-color: #2d3748;
            --text-light: #ffffff;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --transition-speed: 0.3s;
        }

        body {
            color: var(--text-color);
            background-color: #f7fafc;
            font-family: 'Heebo', sans-serif;
        }

        /* Layout principal */
        .container-xxl {
            max-width: 100%;
            padding: 0;
        }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(to bottom, var(--primary-color), var(--dark-color)) !important;
            width: var(--sidebar-width);
            transition: all var(--transition-speed) ease;
            position: fixed;
            z-index: 1000;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar.collapsed .navbar-brand h3,
        .sidebar.collapsed .ms-3,
        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .navbar-brand h3 {
            color: var(--text-light) !important;
            font-weight: 700;
            transition: opacity var(--transition-speed);
        }

        .navbar-light .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin: 0.25rem 0.5rem;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }

        .navbar-light .nav-item .nav-link i {
            min-width: 1.75rem;
            text-align: center;
        }

        .navbar-light .nav-item .nav-link:hover,
        .navbar-light .nav-item .nav-link.active {
            color: var(--text-light) !important;
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        .dropdown-menu {
            background-color: var(--dark-color) !important;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .dropdown-item {
            color: var(--light-color) !important;
            padding: 0.5rem 1rem;
        }

        .dropdown-item:hover {
            background-color: var(--secondary-color) !important;
            color: var(--text-light) !important;
        }

        /* Contenido principal */
        .content {
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed) ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Top Navbar */
        .navbar-expand.sticky-top {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color)) !important;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 0.5rem 1rem;
        }

        .navbar-light .navbar-nav .nav-link {
            color: var(--text-light) !important;
        }

        .sidebar-toggler {
            color: var(--text-light);
            font-size: 1.25rem;
            cursor: pointer;
        }

        .form-control.border-0 {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: white;
        }

        .form-control.border-0::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        /* Cards */
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 2rem rgba(58, 59, 69, 0.2);
        }

        .card-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            border-bottom: none;
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
        }

        /* Tables */
        .table {
            background-color: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }

        .table thead th {
            background-color: var(--primary-color);
            color: var(--text-light);
            border-bottom: none;
            padding: 1rem;
            font-weight: 600;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Form controls */
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25);
        }

        /* Spinner */
        .spinner-border.text-primary {
            color: var(--accent-color) !important;
        }

        /* Back to top button */
        .back-to-top {
            background-color: var(--secondary-color);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 999;
            opacity: 0.8;
            transition: all 0.3s;
        }

        .back-to-top:hover {
            opacity: 1;
            transform: translateY(-5px);
        }

        /* Footer */
        .rounded-top {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color)) !important;
            color: var(--light-color);
            margin-top: auto;
        }

        /* User info in sidebar */
        .ms-3 h6 {
            color: var(--text-light);
            font-weight: 600;
        }

        .ms-3 span {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .sidebar {
                width: var(--sidebar-collapsed-width);
                transform: translateX(-100%);
            }

            .sidebar.mobile-expanded {
                transform: translateX(0);
                width: var(--sidebar-width);
            }

            .content {
                margin-left: 0;
            }

            .content.mobile-expanded {
                margin-left: 0;
            }

            .navbar-brand h3 {
                display: none;
            }

            .sidebar.mobile-expanded .navbar-brand h3 {
                display: block;
            }

            .sidebar.mobile-expanded .ms-3,
            .sidebar.mobile-expanded .nav-link span {
                display: block;
            }

            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .overlay.active {
                display: block;
            }
        }

        /* Select2 customization */
        .select2-container {
            z-index: 9999 !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--secondary-color);
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
    </style>
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Overlay para móviles -->
        <div class="overlay"></div>

        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar navbar-light">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-white"><img class="img-fluid" width="30" src="<?php echo asset('dashmin'); ?>/img/logo-gob.png" alt="">UGRH</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="<?php echo asset('dashmin'); ?>/img/user.jpg" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">{{ Auth::user()->name }}</h6>
                        <span class="text-light">Administrador</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    @can('ver usuarios')
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-archive me-2"></i><span>Admin usuarios</span></a>
                        <div class="dropdown-menu bg-transparent border-0">
                            @can('ver usuarios')
                            <a href="{{ asset('/users') }}" class="dropdown-item"><i class="fas fa-user-alt-slash me-2"></i><span>Usuarios</span></a>
                            @endcan

                            @can('ver roles')
                            <a href="{{ asset('/roles') }}" class="dropdown-item"><i class="fas fa-user-alt-slash me-2"></i>Roles</a>
                            @endcan
                        </div>
                    </div>
                    @endcan

                    @can('ver inicio')
                    <a href="{{ asset('/inicio/archivos') }}" class="nav-item nav-link"><i class="fa fa-home me-2"></i><span>Inicio</span></a>
                    @endcan

                    @can('ver personal')
                    <a href="{{ asset('/reporte') }}" class="nav-item nav-link"><i class="fa fa-users me-2"></i><span>Personal</span></a>
                    @endcan

                    @can('ver perfiles')
                    <a href="{{ asset('/archivos') }}" class="nav-item nav-link"><i class="fa fa-id-card me-2"></i><span>Perfiles</span></a>
                    @endcan

                    @can('ver puestos')
                    <a href="{{ asset('/puesto') }}" class="nav-item nav-link"><i class="fa fa-briefcase me-2"></i><span>Puestos</span></a>
                    @endcan

                    @can('ver pasivos')
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-archive me-2"></i><span>Pasivos</span></a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="{{ asset('/pasivouno') }}" class="dropdown-item"><i class="fas fa-user-alt-slash me-2"></i>Pasivos 1 EX CORDECO</a>
                            <a href="{{ asset('/pasivodos') }}" class="dropdown-item"><i class="fas fa-user-slash me-2"></i>Pasivos 2 GADC</a>
                        </div>
                    </div>
                    @endcan

                    @can('ver perfil profesional')
                    <a href="profesion/index" class="nav-item nav-link"><i class="fas fa-graduation-cap me-2"></i><span>Perfil Profesional</span></a>
                    @endcan

                    @can('ver biblioteca planillas')
                    <a href="form.html" class="nav-item nav-link"><i class="fas fa-file-alt me-2"></i><span>Biblioteca planillas</span></a>
                    @endcan

                    @can('ver asignar item')
                    <a href="{{ asset('/historial') }}" class="nav-item nav-link"><i class="fas fa-tasks me-2"></i><span>Asignar item</span></a>
                    @endcan

                    @can('ver altas bajas')
                    <a href="{{ asset('/altasbajas') }}" class="nav-item nav-link"><i class="fas fa-user-plus me-2"></i><span>Altas y bajas</span></a>
                    @endcan

                    @can('ver bajas')
                    <a href="{{ asset('/altasbajas/index') }}" class="nav-item nav-link"><i class="fas fa-user-minus me-2"></i><span>Bajas</span></a>
                    @endcan

                    @can('ver configuracion')
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-cog me-2"></i><span>Configuración</span></a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="#" class="dropdown-item"><i class="fas fa-user-cog me-2"></i>Perfil</a>
                            <a href="#" class="dropdown-item"><i class="fas fa-sliders-h me-2"></i>Ajustes</a>
                            <hr class="dropdown-divider">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
                    @endcan
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0 text-light me-3">
                    <i class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4 flex-grow-1">
                    <input class="form-control border-0 bg-dark text-light" type="search" placeholder="Buscar..." style="background-color: rgba(255,255,255,0.1) !important;">
                </form>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-envelope me-lg-2 text-light"></i>
                            <span class="d-none d-lg-inline-flex text-light">Mensajes</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end border-0 rounded-0 rounded-bottom m-0" style="background-color: var(--dark-color);">
                            <a href="#" class="dropdown-item text-light">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="<?php echo asset('dashmin'); ?>/img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">{{ Auth::user()->name }}</h6>
                                        <small>Hace 15 minutos</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider bg-light">
                            <a href="#" class="dropdown-item text-center text-light">Ver todos los mensajes</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-bell me-lg-2 text-light"></i>
                            <span class="d-none d-lg-inline-flex text-light">Notificaciones</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end border-0 rounded-0 rounded-bottom m-0" style="background-color: var(--dark-color);">
                            <a href="#" class="dropdown-item text-light">
                                <h6 class="fw-normal mb-0">Perfil actualizado</h6>
                                <small>Hace 15 minutos</small>
                            </a>
                            <hr class="dropdown-divider bg-light">
                            <a href="#" class="dropdown-item text-center text-light">Ver todas las notificaciones</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="<?php echo asset('dashmin'); ?>/img/user.jpg" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex text-light">{{ Auth::user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end border-0 rounded-0 rounded-bottom m-0" style="background-color: var(--dark-color);">
                            <a href="#" class="dropdown-item text-light"><i class="fas fa-user me-2"></i>Mi Perfil</a>
                            <a href="#" class="dropdown-item text-light"><i class="fas fa-cog me-2"></i>Ajustes</a>
                            <hr class="dropdown-divider bg-light">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-light"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

            <!-- Main Content -->
            <div class="container-fluid p-4">
                @yield('contenido')
            </div>

            <!-- Footer Start -->
            <div class="container-fluid pt-4 px-4 mt-auto">
                <div class="rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start text-light">
                            &copy; {{ date('Y') }} <a href="#" class="text-light">UGRH</a>, Todos los derechos reservados.
                        </div>
                        <div class="col-12 col-sm-6 text-center text-sm-end text-light">
                            Versión 1.0.0
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>


    <script src="<?php echo asset('dashmin'); ?>/lib/chart/chart.min.js"></script>
    <script src="<?php echo asset('dashmin'); ?>/lib/easing/easing.min.js"></script>
    <script src="<?php echo asset('dashmin'); ?>/lib/waypoints/waypoints.min.js"></script>
    <script src="<?php echo asset('dashmin'); ?>/lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="<?php echo asset('dashmin'); ?>/lib/tempusdominus/js/moment.min.js"></script>
    <script src="<?php echo asset('dashmin'); ?>/lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="<?php echo asset('dashmin'); ?>/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- JS de Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Template Javascript -->
    <script src="<?php echo asset('dashmin'); ?>/js/main.js"></script>

    <script>
        // Toggle sidebar
        document.querySelector('.sidebar-toggler').addEventListener('click', function(e) {
            e.preventDefault();

            if (window.innerWidth < 992) {
                // Para dispositivos móviles
                document.querySelector('.sidebar').classList.toggle('mobile-expanded');
                document.querySelector('.overlay').classList.toggle('active');
                document.body.classList.toggle('overflow-hidden');
            } else {
                // Para pantallas grandes
                document.querySelector('.sidebar').classList.toggle('collapsed');
                document.querySelector('.content').classList.toggle('expanded');
            }
        });

        // Cerrar sidebar al hacer clic en el overlay (móviles)
        document.querySelector('.overlay').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.remove('mobile-expanded');
            document.querySelector('.overlay').classList.remove('active');
            document.body.classList.remove('overflow-hidden');
        });

        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Back to top button
        var backToTopButton = document.querySelector('.back-to-top');
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.style.display = 'flex';
            } else {
                backToTopButton.style.display = 'none';
            }
        });

        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({top: 0, behavior: 'smooth'});
        });

        // Cerrar automáticamente el menú desplegable al hacer clic en un elemento (en móviles)
        if (window.innerWidth < 992) {
            document.querySelectorAll('.nav-link').forEach(function(element) {
                element.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.remove('mobile-expanded');
                    document.querySelector('.overlay').classList.remove('active');
                    document.body.classList.remove('overflow-hidden');
                });
            });
        }
</body>

</html>
