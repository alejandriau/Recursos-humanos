<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UGRH - Portal del Empleado</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/funcionesApiUsr.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary: #2874A6;
            --primary-dark: #1a5276;
            --accent-yellow: #FFB300;
            --accent-green: #27ae60;
            --accent-orange: #e67e22;
            --accent-purple: #8e44ad;
            --accent-red: #e74c3c;
            --accent-blue: #3498db;
            --bg: #f0f2f5;
            --white: #ffffff;
            --text: #2c3e50;
            --text-light: #7f8c8d;
            --border: #e1e8ed;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            background: var(--primary);
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            box-shadow: 0 2px 15px rgba(40,116,166,0.25);
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
        }

        .topbar-brand img {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: white;
            padding: 3px;
        }

        .topbar-brand span {
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-topbar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: none;
            background: rgba(255,255,255,0.12);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1rem;
            position: relative;
        }

        .btn-topbar:hover {
            background: rgba(255,255,255,0.25);
        }

        .btn-topbar .badge-count {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--accent-red);
            color: white;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            border: 2px solid var(--primary);
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.4rem 0.8rem 0.4rem 0.4rem;
            background: rgba(255,255,255,0.12);
            border-radius: 50px;
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .user-pill:hover {
            background: rgba(255,255,255,0.2);
        }

        .user-pill img, .user-pill .avatar-circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
        }

        .avatar-circle {
            background: linear-gradient(135deg, var(--accent-yellow), #f39c12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .user-pill-info {
            text-align: left;
            line-height: 1.2;
        }

        .user-pill-info .name {
            font-size: 0.8rem;
            font-weight: 600;
        }

        .user-pill-info .role {
            font-size: 0.65rem;
            opacity: 0.8;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 260px;
            height: calc(100vh - 60px);
            background: var(--white);
            border-right: 1px solid var(--border);
            z-index: 1030;
            overflow-y: auto;
            padding: 1.25rem;
            transition: transform 0.3s;
        }

        .sidebar-section {
            margin-bottom: 1.5rem;
        }

        .sidebar-title {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-light);
            margin-bottom: 0.75rem;
            padding-left: 0.5rem;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text);
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 4px;
            border: 1px solid transparent;
        }

        .sidebar-item:hover {
            background: #f8f9fa;
            border-color: var(--border);
            transform: translateX(3px);
        }

        .sidebar-item.active {
            background: rgba(40,116,166,0.08);
            color: var(--primary);
            border-color: rgba(40,116,166,0.15);
        }

        .sidebar-item .item-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .sidebar-item .item-badge {
            margin-left: auto;
            background: var(--accent-green);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: 260px;
            margin-top: 60px;
            padding: 1.5rem;
            min-height: calc(100vh - 60px);
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 20px;
            padding: 2rem;
            color: white;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 180px;
            height: 180px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .welcome-banner h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .welcome-banner p {
            opacity: 0.9;
            margin: 0;
            font-size: 0.95rem;
        }

        .welcome-banner .btn-action {
            background: var(--accent-yellow);
            color: var(--text);
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            margin-top: 1rem;
            transition: all 0.2s;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255,179,0,0.35);
        }

        /* Section Title */
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: var(--primary);
        }

        /* ===== BIG CARDS GRID ===== */
        .big-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .big-card {
            background: var(--white);
            border-radius: 20px;
            padding: 1.75rem 1.5rem;
            text-align: center;
            text-decoration: none;
            color: var(--text);
            border: 2px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .big-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .big-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--border);
        }

        .big-card:hover::before {
            opacity: 1;
        }

        .big-card-icon {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.8rem;
            transition: all 0.3s;
        }

        .big-card:hover .big-card-icon {
            transform: scale(1.1) rotate(-5deg);
        }

        .big-card h4 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
        }

        .big-card p {
            font-size: 0.8rem;
            color: var(--text-light);
            margin: 0;
        }

        .big-card .arrow {
            margin-top: 1rem;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s;
        }

        .big-card:hover .arrow {
            opacity: 1;
            transform: translateX(0);
        }

        /* Colores por tipo */
        .card-vacacion .big-card-icon { background: rgba(39,174,96,0.1); color: var(--accent-green); }
        .card-vacacion::before { background: var(--accent-green); }
        .card-vacacion:hover { border-color: rgba(39,174,96,0.2); }
        .card-vacacion .arrow { background: rgba(39,174,96,0.1); color: var(--accent-green); }

        .card-comision .big-card-icon { background: rgba(230,126,34,0.1); color: var(--accent-orange); }
        .card-comision::before { background: var(--accent-orange); }
        .card-comision:hover { border-color: rgba(230,126,34,0.2); }
        .card-comision .arrow { background: rgba(230,126,34,0.1); color: var(--accent-orange); }

        .card-particular .big-card-icon { background: rgba(142,68,173,0.1); color: var(--accent-purple); }
        .card-particular::before { background: var(--accent-purple); }
        .card-particular:hover { border-color: rgba(142,68,173,0.2); }
        .card-particular .arrow { background: rgba(142,68,173,0.1); color: var(--accent-purple); }

        .card-medica .big-card-icon { background: rgba(231,76,60,0.1); color: var(--accent-red); }
        .card-medica::before { background: var(--accent-red); }
        .card-medica:hover { border-color: rgba(231,76,60,0.2); }
        .card-medica .arrow { background: rgba(231,76,60,0.1); color: var(--accent-red); }

        /* ===== RESUMEN RÁPIDO ===== */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .stat-info h5 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
            color: var(--text);
        }

        .stat-info p {
            font-size: 0.8rem;
            color: var(--text-light);
            margin: 0;
        }

        /* ===== MODAL MEJORADO ===== */
        .modal-content {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .modal-header-custom {
            background: var(--accent-green);
            color: white;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header-custom h5 {
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-header-custom .btn-close-custom {
            background: rgba(255,255,255,0.2);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-header-custom .btn-close-custom:hover {
            background: rgba(255,255,255,0.35);
        }

        .table-modern {
            margin: 0;
        }

        .table-modern thead th {
            background: #f8f9fa;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-light);
            padding: 1rem;
            border-bottom: 2px solid var(--border);
        }

        .table-modern tbody td {
            padding: 1rem;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .badge-estado {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-pendiente { background: #fff3cd; color: #856404; }
        .badge-aprobado { background: #d4edda; color: #155724; }
        .badge-rechazado { background: #f8d7da; color: #721c24; }

        /* ===== MODAL PERFIL ===== */
        .perfil-card {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .perfil-avatar-big {
            width: 90px;
            height: 90px;
            border-radius: 24px;
            background: linear-gradient(135deg, var(--accent-yellow), #f39c12);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            border: 4px solid rgba(255,255,255,0.3);
        }

        .dato-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.9rem 1.25rem;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 0.6rem;
        }

        .dato-row i {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .dato-row div {
            flex: 1;
        }

        .dato-row label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-light);
            font-weight: 700;
            margin: 0;
        }

        .dato-row pre {
            margin: 0.15rem 0 0;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            white-space: pre-wrap;
        }

        /* ===== FOOTER ===== */
        .app-footer {
            margin-left: 260px;
            background: var(--white);
            border-top: 1px solid var(--border);
            padding: 1rem;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-light);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1050;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .app-footer {
                margin-left: 0;
            }

            .big-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .welcome-banner h2 {
                font-size: 1.2rem;
            }
        }

        /* Overlay móvil */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1045;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Animaciones */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .anim-up {
            animation: fadeUp 0.5s ease forwards;
        }

        .delay-1 { animation-delay: 0.1s; opacity: 0; }
        .delay-2 { animation-delay: 0.2s; opacity: 0; }
        .delay-3 { animation-delay: 0.3s; opacity: 0; }
    </style>
</head>

<body>

    <!-- Overlay móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ===== TOPBAR ===== -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn-topbar d-md-none" id="btnMenuMobile">
                <i class="fas fa-bars"></i>
            </button>
            <a href="/homeusr" class="topbar-brand">
                <img src="{{ URL::asset('img/cbba.png') }}" alt="Logo">
                <span>UGRH</span>
            </a>
        </div>

        <div class="topbar-actions">
            <button class="btn-topbar" id="btnNotif" title="Notificaciones">
                <i class="fas fa-bell"></i>
                <span class="badge-count" id="badgeNotif" style="display:none;">0</span>
            </button>

            <div class="dropdown">
                <button class="user-pill dropdown-toggle" data-bs-toggle="dropdown">
                    <div class="avatar-circle">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-pill-info d-none d-sm-block">
                        <div class="name" id="topbarNombre">Usuario</div>
                        <div class="role">Empleado</div>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius:16px; min-width:260px;">
                    <li class="perfil-card">
                        <div class="perfil-avatar-big"><i class="fas fa-user"></i></div>
                        <h5 class="mb-1" id="dropdownNombre">--</h5>
                        <small id="dropdownUsuario">@usuario</small>
                    </li>
                    <li class="p-2">
                        <a class="dropdown-item rounded-3 py-2" href="#" data-bs-toggle="modal" data-bs-target="#modalPerfil">
                            <i class="fas fa-id-card me-2 text-muted"></i> Mi Perfil
                        </a>
                        <div class="dropdown-divider mx-2"></div>
                        <a class="dropdown-item rounded-3 py-2 text-danger" href="/">
                            <i class="fas fa-arrow-right-from-bracket me-2"></i> Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" id="sidebar">
        <!-- Solicitar Salida -->
        <div class="sidebar-section">
            <div class="sidebar-title">Solicitar Salida</div>
            <a href="/vacacion/usuario" class="sidebar-item">
                <div class="item-icon" style="background:rgba(39,174,96,0.1); color:var(--accent-green);">
                    <i class="fas fa-umbrella-beach"></i>
                </div>
                <span>Vacaciones</span>
            </a>
            <a href="/comision/usuario" class="sidebar-item">
                <div class="item-icon" style="background:rgba(230,126,34,0.1); color:var(--accent-orange);">
                    <i class="fas fa-person-walking-arrow-right"></i>
                </div>
                <span>Comisiones</span>
            </a>
            <a href="/salida/particular" class="sidebar-item">
                <div class="item-icon" style="background:rgba(142,68,173,0.1); color:var(--accent-purple);">
                    <i class="fas fa-person-walking-arrow-loop-left"></i>
                </div>
                <span>Salida Particular</span>
            </a>
            <a href="/salud/usuario" class="sidebar-item">
                <div class="item-icon" style="background:rgba(231,76,60,0.1); color:var(--accent-red);">
                    <i class="fas fa-truck-medical"></i>
                </div>
                <span>Salida Médica</span>
            </a>
        </div>

        <!-- Visto Bueno -->
        <div class="sidebar-section">
            <div class="sidebar-title">Visto Bueno</div>
            <button class="sidebar-item" id="listSol">
                <div class="item-icon" style="background:rgba(40,116,166,0.1); color:var(--primary);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <span>Solicitudes Pendientes</span>
                <span class="item-badge" id="badgePendientes">0</span>
            </button>
        </div>

        <!-- Reportes -->
        <div class="sidebar-section">
            <div class="sidebar-title">Reportes</div>
            <a href="/reportesusr" class="sidebar-item">
                <div class="item-icon" style="background:rgba(52,152,219,0.1); color:var(--accent-blue);">
                    <i class="fas fa-table-list"></i>
                </div>
                <span>Reporte de Salidas</span>
            </a>
            <a href="/listarbeneficio" class="sidebar-item">
                <div class="item-icon" style="background:rgba(255,179,0,0.1); color:var(--accent-yellow);">
                    <i class="fas fa-list-check"></i>
                </div>
                <span>Listar Beneficios</span>
            </a>
            <a href="/datos-personal" class="sidebar-item">
                <div class="item-icon" style="background:rgba(129,212,250,0.15); color:#0277BD;">
                    <i class="fas fa-street-view"></i>
                </div>
                <span>Datos del Personal</span>
            </a>
        </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">


        <!-- Contenido específico de la página -->
        <div class="anim-up delay-3">
            <div class="row">
                <div class="col-md-2 text-center">
                    @yield('comunicado')
                    <hr>
                </div>
                <div class="col-md-10 text-center">
                    @yield('cuerpo')
                </div>
            </div>
        </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="app-footer">
        <i class="fas fa-code-branch me-1"></i> Desarrollado por UGRH © 2026 - GADC
    </footer>

    <!-- ===== MODAL: Vacaciones Registradas (ejemplo del tipo tabla) ===== -->
    <div class="modal fade" id="modalVacaciones" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-custom">
                    <h5><i class="fas fa-umbrella-beach"></i> Vacaciones Registradas</h5>
                    <button class="btn-close-custom" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Fecha Salida</th>
                                    <th>Fecha Retorno</th>
                                    <th>Días</th>
                                    <th>Visto Bueno</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tablaVacaciones">
                                <!-- Se llena por JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MODAL: Perfil ===== -->
    <div class="modal fade" id="modalPerfil" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-custom" style="background:var(--primary);">
                    <h5><i class="fas fa-id-card"></i> Mi Perfil</h5>
                    <button class="btn-close-custom" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body p-4">
                    <div class="dato-row">
                        <i class="fas fa-user-tag"></i>
                        <div>
                            <label>Nombre Completo</label>
                            <pre id="perfilNomC">--</pre>
                        </div>
                    </div>
                    <div class="dato-row">
                        <i class="fas fa-at"></i>
                        <div>
                            <label>Usuario</label>
                            <pre id="perfilUsr">--</pre>
                        </div>
                    </div>
                    <div class="dato-row">
                        <i class="fas fa-briefcase"></i>
                        <div>
                            <label>Cargo</label>
                            <pre id="perfilCar">--</pre>
                        </div>
                    </div>
                    <div class="dato-row">
                        <i class="fas fa-building"></i>
                        <div>
                            <label>Dependencia</label>
                            <pre id="perfilDep">--</pre>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button class="btn-action" data-bs-dismiss="modal">
                        <i class="fas fa-check me-2"></i>Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== DATOS USUARIO =====
        const storedData = localStorage.getItem('responseData');
        const parsedData = storedData ? JSON.parse(storedData) : null;

        if (parsedData?.data?.[0]) {
            const d = parsedData.data[0];
            const nombreC = `${d.nombres} ${d.paterno} ${d.materno}`;

            document.getElementById('topbarNombre').textContent = d.nombres;
            document.getElementById('dropdownNombre').textContent = nombreC;
            document.getElementById('dropdownUsuario').textContent = `@${d.usuario}`;

            document.getElementById('perfilNomC').textContent = nombreC;
            document.getElementById('perfilUsr').textContent = d.usuario;
            document.getElementById('perfilCar').textContent = d.cargo || 'No especificado';
            document.getElementById('perfilDep').textContent = d.dependencia || 'No especificada';
        }

        // ===== SIDEBAR MÓVIL =====
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        document.getElementById('btnMenuMobile').addEventListener('click', () => {
            sidebar.classList.add('mobile-open');
            overlay.classList.add('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });

        document.querySelectorAll('.sidebar-item').forEach(item => {
            item.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });
        });

        // ===== ACTIVE STATE =====
        const path = window.location.pathname;
        document.querySelectorAll('.sidebar-item').forEach(item => {
            if (item.getAttribute('href') === path) {
                item.classList.add('active');
            }
        });

        // ===== STATS DEMO (reemplazar con endpoint real) =====
        document.getElementById('statAprobadas').textContent = '12';
        document.getElementById('statPendientes').textContent = '2';
        document.getElementById('statRechazadas').textContent = '1';
        document.getElementById('statTotal').textContent = '15';

        // ===== FERIADOS =====
        window._feriados = @json($feriado ?? []);
    </script>

    @stack('scripts')
</body>
</html>