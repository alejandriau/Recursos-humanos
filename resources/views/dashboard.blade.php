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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>


    <!-- Customized Bootstrap Stylesheet -->
    <link href="<?php echo asset('dashmin'); ?>/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>


    <!-- Template Stylesheet -->
    <link href="<?php echo asset('dashmin'); ?>/css/style.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/js/app.js')


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
                <a href="index.html" class="navbar-brand mx-4 mb-3 d-flex align-items-center">
                    <img class="img-fluid me-2" width="30" src="<?php echo asset('dashmin'); ?>/img/logo-gob.png" alt="">
                    <h3 class="text-white mb-0">UGRH</h3>
                </a>

                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="<?php echo asset('dashmin'); ?>/img/user.jpg" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">{{ Auth::user()->name }}</h6>
                        <span class="text-light">
                            {{ Auth::user()->getRoleNames()->first() ?? 'Sin rol' }}
                        </span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    @can('ver usuarios')
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-users-cog me-2"></i></i><span>usuarios</span></a>
                        <div class="dropdown-menu bg-transparent border-0">
                            @can('ver usuarios')
                            <a href="{{ asset('/users') }}" class="dropdown-item"><i class="fa fa-user me-2"></i><span>Usuarios</span></a>
                            @endcan

                            @can('ver roles')
                            <a href="{{ asset('/roles') }}" class="dropdown-item"><i class="fa fa-user-shield me-2"></i>Roles</a>
                            @endcan
                        </div>
                    </div>
                    @endcan


                    @can('ver personal')
                    <a href="{{ asset('/reporte') }}" class="nav-item nav-link"><i class="fa fa-users me-2"></i><span>Personal</span></a>
                    @endcan
                    @can('ver inicio')
                    <a href="{{ asset('/inicio/archivos') }}" class="nav-item nav-link"><i class="fa fa-home me-2"></i><span>Documentación</span></a>
                    @endcan
                    @can('ver organizacional')
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-sitemap me-2"></i><span>Organizacional</span></a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="{{ asset('/admin/puestos') }}" class="dropdown-item"><i class="fa fa-briefcase me-2"></i>Puestos</a>
                            <a href="{{ asset('/unidades') }}" class="dropdown-item"><i class="fa fa-project-diagram me-2"></i>Organigrama</a>
                        </div>
                    </div>
                    @endcan
                    @can('ver asignar item')
                    <a href="{{ asset('/historial') }}" class="nav-item nav-link"><i class="fas fa-tasks me-2"></i><span>Asignar item</span></a>
                    @endcan
                    @can('ver biblioteca planillas')
                    <a href="{{ asset('/planillas-pdf/index') }}" class="nav-item nav-link"><i class="fas fa-file-alt me-2"></i><span>Biblioteca planillas</span></a>
                    @endcan


                    <!--@can('ver perfiles')
                    <a href="{{ asset('/archivos') }}" class="nav-item nav-link"><i class="fa fa-id-card me-2"></i><span>Perfiles</span></a>
                    @endcan-->


                    @can('ver pasivos')
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-archive me-2"></i><span>Pasivos</span></a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="{{ asset('/pasivouno') }}" class="dropdown-item"><i class="fas fa-user-alt-slash me-2"></i>1 EX CORDECO</a>
                            <a href="{{ asset('/pasivodos') }}" class="dropdown-item"><i class="fas fa-user-slash me-2"></i>2 GADC</a>
                        </div>
                    </div>
                    @endcan

                    <!--@can('ver perfil profesional')
                    <a href="profesion/index" class="nav-item nav-link"><i class="fas fa-graduation-cap me-2"></i><span>Perfil Profesional</span></a>
                    @endcan-->


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

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('audit-logs.index') }}">
                            <i class="fas fa-eye"></i>
                            <span>Auditoría</span>
                        </a>
                    </li>


                </div>
            </nav>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand sticky-top px-4 py-0" style="background-color: var(--dark-color);">
                <a href="{{ asset('/inicio/archivos') }}" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0">
                        <img class="img-fluid me-2" width="30" src="{{ asset('dashmin/img/logo-gob.png') }}" alt="">
                    </h2>
                </a>

                <a href="#" class="sidebar-toggler flex-shrink-0 text-light me-3">
                    <i class="fa fa-bars"></i>
                </a>

                <div class="navbar-nav align-items-center ms-auto">

                    {{-- MENSAJES --}}
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-item nav-link dropdown-toggle text-light" data-bs-toggle="dropdown">
                            <i class="fa fa-envelope me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Mensajes</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg m-0"
                            style="background-color: #2b2b2b;">
                            <a href="#" class="dropdown-item text-white">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="{{ asset('dashmin/img/user.jpg') }}" alt=""
                                        style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">{{ Auth::user()->name }}</h6>
                                        <small class="text-secondary">Hace 15 minutos</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider bg-secondary">
                            <a href="#" class="dropdown-item text-center text-white">Ver todos los mensajes</a>
                        </div>
                    </div>

                    {{-- NOTIFICACIONES --}}
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle text-light" data-bs-toggle="dropdown">
                            <i class="fa fa-bell me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Notificaciones</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg m-0"
                            style="background-color: #2b2b2b;">
                            <a href="#" class="dropdown-item text-white">
                                <h6 class="fw-normal mb-0">Perfil actualizado</h6>
                                <small class="text-secondary">Hace 15 minutos</small>
                            </a>
                            <hr class="dropdown-divider bg-secondary">
                            <a href="#" class="dropdown-item text-center text-white">Ver todas las notificaciones</a>
                        </div>
                    </div>

                    {{-- PERFIL DE USUARIO --}}
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center text-light" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-2" src="{{ asset('dashmin/img/user.jpg') }}" alt=""
                                style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex">{{ Auth::user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg m-0"
                            style="background-color: #2b2b2b;">
                            <a href="#" class="dropdown-item text-white">
                                <i class="fas fa-user me-2 text-primary"></i>Mi Perfil
                            </a>
                            <a href="#" class="dropdown-item text-white">
                                <i class="fas fa-cog me-2 text-warning"></i>Ajustes
                            </a>
                            <hr class="dropdown-divider bg-secondary">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                                </button>
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



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="<?php echo asset('dashmin'); ?>/lib/chart/chart.min.js"></script>
    <script src="<?php echo asset('dashmin'); ?>/lib/easing/easing.min.js"></script>
    <script src="<?php echo asset('dashmin'); ?>/lib/waypoints/waypoints.min.js"></script>
    <script src="<?php echo asset('dashmin'); ?>/lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="<?php echo asset('dashmin'); ?>/lib/tempusdominus/js/moment.min.js"></script>
    <script src="<?php echo asset('dashmin'); ?>/lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="<?php echo asset('dashmin'); ?>/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- jQuery -->


    <!-- JS de Select2 -->

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
    </script>
</body>

</html>
