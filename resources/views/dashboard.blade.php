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


 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>


    <!-- Template Stylesheet -->
    <link href="<?php echo asset('dashmin'); ?>/css/style.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/js/app.js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>



</head>
<style>
    /* Botón flotante */
.chatbot-floating-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 28px;
    cursor: pointer;
    box-shadow:
        0 6px 20px rgba(99, 102, 241, 0.4),
        0 0 0 0 rgba(99, 102, 241, 0.5);
    transition: all 0.3s ease;
    z-index: 1000;
    animation: pulse 2s infinite;
}

.chatbot-floating-btn:hover {
    transform: scale(1.1);
    box-shadow:
        0 8px 25px rgba(99, 102, 241, 0.6),
        0 0 0 15px rgba(99, 102, 241, 0.1);
}

@keyframes pulse {
    0% {
        box-shadow:
            0 6px 20px rgba(99, 102, 241, 0.4),
            0 0 0 0 rgba(99, 102, 241, 0.5);
    }
    70% {
        box-shadow:
            0 6px 20px rgba(99, 102, 241, 0.4),
            0 0 0 15px rgba(99, 102, 241, 0);
    }
    100% {
        box-shadow:
            0 6px 20px rgba(99, 102, 241, 0.4),
            0 0 0 0 rgba(99, 102, 241, 0);
    }
}

.notification-dot {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 12px;
    height: 12px;
    background: #10b981;
    border-radius: 50%;
    border: 2px solid white;
    animation: ping 1.5s infinite;
}

@keyframes ping {
    0% {
        transform: scale(0.8);
        opacity: 1;
    }
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

/* Modal del chatbot */
.chatbot-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
    z-index: 1001;
    animation: fadeIn 0.3s ease-out;
}

.chatbot-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 800px;
    height: 85vh;
    max-height: 700px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow:
        0 25px 50px rgba(0, 0, 0, 0.5),
        0 0 0 1px rgba(99, 102, 241, 0.3);
    animation: scaleIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .chatbot-modal-content {
        width: 95%;
        height: 90vh;
    }

    .chatbot-floating-btn {
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        font-size: 24px;
    }
}



.back-to-top {
    position: fixed;
    bottom: 90px;      /* Más arriba */
    right: 20px;
    z-index: 999;
}

.chatbot-floating-btn {
    position: fixed;
    bottom: 20px;      /* Más abajo */
    right: 20px;
    width: 60px;
    height: 60px;
    background: #0d6efd;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    cursor: pointer;
    z-index: 999;
}

</style>

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
                <a href="{{route('dashboard')}}" class="navbar-brand mx-4 mb-3 d-flex align-items-center">
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


                    @can('ver bajas')
                    <a href="{{ asset('/altasbajas/index') }}" class="nav-item nav-link"><i class="fas fa-user-minus me-2"></i><span>Bajas</span></a>
                    @endcan

                    <!--@can('ver configuracion')
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
                    </div>-->
                    @endcan
                    <!--@can('ver configuracion')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('audit-logs.index') }}">
                            <i class="fas fa-eye"></i>
                            <span>Auditoría</span>
                        </a>
                    </li>
                    @endcan-->
                    @can('ver configuracion')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reportes.dashboard') }}">
                            <i class="fas fa-eye"></i>
                            <span>Reportes</span>
                        </a>
                    </li>
                    @endcan
                    @role('admin')
                        <a href="{{ route('admin.vacaciones.index') }}" class="nav-link">
                            <i class="fas fa-umbrella-beach me-2"></i>Vacaciones
                        </a>
                        <a href="{{ route('admin.asistencias.index') }}" class="nav-link">
                            <i class="fas fa-clock me-2"></i>Asistencias
                        </a>
                    @endrole
@role('empleado')
<!-- Menu Específico para Empleados -->
<li class="nav-item">
    <a class="nav-link" href="{{ route('empleado.dashboard') }}">
        <i class="fas fa-tachometer-alt me-2"></i>
        <span>Mi Dashboard</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('empleado.asistencias.index') }}">
        <i class="fas fa-clock me-2"></i>
        <span>Mis Asistencias</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('empleado.vacaciones.index') }}">
        <i class="fas fa-umbrella-beach me-2"></i>
        <span>Mis Vacaciones</span>
    </a>
</li>
<!--<li class="nav-item">
    <a class="nav-link" href="{{ route('empleado.historial') }}">
        <i class="fas fa-briefcase me-2"></i>
        <span>Mi Historial</span>
    </a>
</li>-->
<li class="nav-item">
    <a class="nav-link" href="{{ route('empleado.perfil') }}">
        <i class="fas fa-user me-2"></i>
        <span>Mi Perfil</span>
    </a>
</li>
@endrole



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
                    <!--<div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-bell me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex text-white">Notificaciones</span>
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
                    </div>-->

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
        <div class="chatbot-floating-btn" id="chatbotFloatingBtn">
            <i class="fas fa-robot"></i>
            <span class="notification-dot"></span>
        </div>



        <div class="chatbot-modal" id="chatbotModal">
            <div class="chatbot-modal-content">
                <!-- Aquí irá el contenido del chatbot -->
            </div>
        </div>
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



            document.addEventListener('DOMContentLoaded', function() {
            const floatingBtn = document.getElementById('chatbotFloatingBtn');
            const modal = document.getElementById('chatbotModal');
            const modalContent = document.querySelector('.chatbot-modal-content');

            // Cargar el contenido del chatbot
            fetch('/chatbot-content')
                .then(response => response.text())
                .then(html => {
                    modalContent.innerHTML = html;
                    initializeChatbot();
                });

            // Abrir modal
            floatingBtn.addEventListener('click', function() {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Prevenir scroll
            });

            // Cerrar modal al hacer clic fuera
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });

            // Cerrar con tecla Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'block') {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        });

        function initializeChatbot() {
    // Crear partículas visuales
    function createParticles() {
        const particlesContainer = document.getElementById('particles');
        const particleCount = 20;

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');

            const size = Math.random() * 10 + 5;
            const posX = Math.random() * 100;
            const posY = Math.random() * 100;

            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.left = `${posX}%`;
            particle.style.top = `${posY}%`;

            particle.style.animationDelay = `${Math.random() * 10}s`;
            particle.style.animationDuration = `${Math.random() * 10 + 10}s`;

            particlesContainer.appendChild(particle);
        }
    }

    // Obtener hora actual
    function getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // Enviar mensaje al backend y mostrar respuesta real
    function sendMessage() {
        const messageInput = document.getElementById('message');
        const message = messageInput.value.trim();
        if (!message) return;

        const chatbox = document.getElementById('chatbox');

        const userMsg = document.createElement('div');
        userMsg.className = 'user message';
        userMsg.innerHTML = `<strong>Tú:</strong> ${message}<div class="message-time">${getCurrentTime()}</div>`;
        chatbox.appendChild(userMsg);

        messageInput.value = '';
        chatbox.scrollTop = chatbox.scrollHeight;

        // Indicador de "Bozo está pensando..."
        const typingMsg = document.createElement('div');
        typingMsg.className = 'bot message typing-indicator';
        typingMsg.innerHTML = 'Bueno Bozo está pensando...<div class="typing-dots"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>';
        chatbox.appendChild(typingMsg);
        chatbox.scrollTop = chatbox.scrollHeight;

        fetch('/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message })
        })
        .then(response => response.json())
        .then(data => {
            typingMsg.remove();

            const botMsg = document.createElement('div');
            botMsg.className = 'bot message';
            chatbox.appendChild(botMsg);

            let i = 0;
            const text = data.response;

            const typingInterval = setInterval(() => {
                if (i < text.length) {
                    botMsg.innerHTML = `<strong>Bozo:</strong> ${text.substring(0, i + 1)}<div class="message-time">${getCurrentTime()}</div>`;
                    i++;
                    chatbox.scrollTop = chatbox.scrollHeight;
                } else {
                    clearInterval(typingInterval);
                }
            }, 25);
        });
    }

    // Enviar con Enter
    document.getElementById('message').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    // Inicializar
    window.onload = function () {
        createParticles();
        document.getElementById('message').focus();
    };
        }
    </script>
</body>

</html>
