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
    <!-- CSS de SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- JavaScript de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #eef1f5;
        overflow-x: hidden;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 270px;
        background: linear-gradient(180deg, #3c4150 0%, #2d313e 100%);
        color: #e8eaed;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1050;
        box-shadow: 2px 0 20px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
    }

    .sidebar.collapsed { width: 72px; }

    /* Scrollbar del sidebar */
    .sidebar-nav::-webkit-scrollbar { width: 4px; }
    .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
    .sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.15);
        border-radius: 4px;
    }
    .sidebar-nav::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }

    /* ===== HEADER ===== */
    .sidebar-header {
        background: rgba(0, 0, 0, 0.25);
        padding: 0;
        display: flex;
        align-items: center;
        min-height: 64px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        flex-shrink: 0;
    }

    .sidebar-toggle-btn {
        background: none;
        border: none;
        color: rgba(255,255,255,0.7);
        font-size: 1.25rem;
        cursor: pointer;
        padding: 0 1.1rem;
        height: 64px;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .sidebar-toggle-btn:hover { color: white; background: rgba(255,255,255,0.05); }
    .sidebar.collapsed .sidebar-toggle-btn { padding: 0; width: 100%; }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        overflow: hidden;
        flex: 1;
    }

    .sidebar-brand img {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    .sidebar-brand h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        color: white;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .sidebar-brand small {
        display: block;
        font-size: 0.65rem;
        font-weight: 400;
        opacity: 0.6;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .sidebar.collapsed .sidebar-brand span,
    .sidebar.collapsed .sidebar-brand small { display: none; }

    /* ===== USER CARD ===== */
    .sidebar-user {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.9rem 1rem;
        margin: 0.75rem 0.85rem;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .sidebar-user:hover { background: rgba(255, 255, 255, 0.1); }

    .sidebar-user img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 2px solid #7cb342;
        flex-shrink: 0;
    }

    .sidebar-user-info { overflow: hidden; min-width: 0; }
    .sidebar-user-info h6 {
        font-size: 0.85rem;
        font-weight: 600;
        margin: 0;
        color: white;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sidebar-user-info span {
        font-size: 0.7rem;
        opacity: 0.7;
        color: #c5c9d0;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar.collapsed .sidebar-user {
        padding: 0.6rem;
        justify-content: center;
        margin: 0.75rem 0.4rem;
    }
    .sidebar.collapsed .sidebar-user-info { display: none; }

    /* ===== NAVEGACIÓN ===== */
    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0.5rem 0 1rem;
    }

    /* Separadores con etiqueta de sección */
    .nav-section-title {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: rgba(255,255,255,0.4);
        font-weight: 600;
        padding: 1rem 1.3rem 0.4rem;
        white-space: nowrap;
    }

    .sidebar.collapsed .nav-section-title {
        text-align: center;
        padding: 1rem 0 0.4rem;
        font-size: 0.55rem;
    }
    .sidebar.collapsed .nav-section-title span { display: none; }
    .sidebar.collapsed .nav-section-title::before {
        content: "•••";
        letter-spacing: 2px;
    }

    /* Nav link base */
    .nav-link {
        color: #d0d3d8;
        padding: 0.7rem 1.1rem;
        margin: 2px 0.6rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.875rem;
        text-decoration: none;
        cursor: pointer;
        position: relative;
        white-space: nowrap;
    }

    .nav-link i.nav-icon {
        width: 22px;
        font-size: 1rem;
        text-align: center;
        color: #b0b4bc;
        transition: color 0.2s;
        flex-shrink: 0;
    }

    .nav-link span.label { flex: 1; overflow: hidden; text-overflow: ellipsis; }

    .nav-link:hover {
        background: rgba(255, 255, 255, 0.08);
        color: white;
    }
    .nav-link:hover i.nav-icon { color: white; }

    /* Item activo (con barra lateral) */
    .nav-item.active > .nav-link {
        background: rgba(124, 179, 66, 0.18);
        color: white;
        font-weight: 500;
    }
    .nav-item.active > .nav-link::before {
        content: "";
        position: absolute;
        left: -0.6rem;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 60%;
        background: #7cb342;
        border-radius: 0 4px 4px 0;
    }
    .nav-item.active > .nav-link i.nav-icon { color: #a5d96a; }

    /* ===== ACORDEÓN / SUBMENÚ ===== */
    .has-submenu > .nav-link .chevron {
        font-size: 0.7rem;
        transition: transform 0.3s ease;
        color: #b0b4bc;
        flex-shrink: 0;
    }

    .has-submenu.open > .nav-link .chevron {
        transform: rotate(90deg);
        color: #7cb342;
    }

    .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        margin: 0 0.6rem;
    }

    .has-submenu.open > .submenu {
        max-height: 500px;
        transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .submenu .nav-link {
        padding: 0.55rem 1rem 0.55rem 2.8rem;
        margin: 1px 0;
        font-size: 0.82rem;
        border-radius: 6px;
    }

    .submenu .nav-link::before {
        content: "";
        position: absolute;
        left: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        transition: all 0.2s;
    }

    .submenu .nav-link:hover::before {
        background: #7cb342;
        box-shadow: 0 0 8px rgba(124,179,66,0.5);
    }

    .submenu .nav-link i.nav-icon { display: none; }

    /* ===== MODO COLAPSADO: popout submenu ===== */
    .sidebar.collapsed .nav-link {
        justify-content: center;
        padding: 0.75rem 0;
        margin: 2px 0.5rem;
    }
    .sidebar.collapsed .nav-link span.label,
    .sidebar.collapsed .nav-link .chevron,
    .sidebar.collapsed .nav-section-title,
    .sidebar.collapsed .submenu { display: none; }

    .sidebar.collapsed .has-submenu { position: relative; }

    .sidebar.collapsed .has-submenu > .nav-link:hover + .submenu,
    .sidebar.collapsed .has-submenu:hover > .submenu {
        display: block !important;
        position: absolute;
        left: 100%;
        top: 0;
        min-width: 220px;
        background: #2d313e;
        border-radius: 8px;
        padding: 0.4rem;
        margin-left: 8px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.4);
        max-height: none;
        z-index: 1100;
    }

    .sidebar.collapsed .has-submenu:hover > .submenu .nav-link {
        padding: 0.55rem 0.9rem;
        margin: 1px 0;
    }
    .sidebar.collapsed .has-submenu:hover > .submenu .nav-link::before {
        display: none;
    }
    .sidebar.collapsed .has-submenu:hover > .submenu .nav-link span.label { display: block; }

    /* Tooltip en modo colapsado */
    .sidebar.collapsed .nav-link::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        background: #1f2229;
        color: white;
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
        font-size: 0.78rem;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        margin-left: 12px;
        transition: opacity 0.2s;
        z-index: 1100;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .sidebar.collapsed .nav-link:hover::after { opacity: 1; }
    .sidebar.collapsed .has-submenu .nav-link:hover::after { display: none; }

    /* ===== BADGES ===== */
    .nav-badge {
        background: #5c6bc0;
        color: white;
        font-size: 0.7rem;
        padding: 0.1rem 0.45rem;
        border-radius: 10px;
        font-weight: 600;
        min-width: 20px;
        text-align: center;
    }

    .nav-badge.success { background: #7cb342; }
    .nav-badge.danger  { background: #ef5350; }
    .nav-badge.warning { background: #ff9800; }

    .sidebar.collapsed .nav-badge {
        position: absolute;
        top: 4px;
        right: 4px;
        font-size: 0.6rem;
        padding: 0 0.3rem;
        min-width: 16px;
        height: 16px;
        line-height: 16px;
    }

    /* ===== ITEM DESTACADO: Validar salidas ===== */
    .nav-item.highlight .nav-link {
        background: linear-gradient(135deg, #7e57c2 0%, #5e35b1 100%);
        color: white;
        font-weight: 500;
        margin: 0.5rem 0.6rem;
        box-shadow: 0 4px 12px rgba(126, 87, 194, 0.35);
    }
    .nav-item.highlight .nav-link:hover {
        background: linear-gradient(135deg, #6a4aaa 0%, #4e2d91 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(126, 87, 194, 0.45);
    }
    .nav-item.highlight .nav-link i.nav-icon,
    .nav-item.highlight .nav-link .nav-badge { color: white; }
    .nav-item.highlight .nav-link .nav-badge { background: rgba(255,255,255,0.25); }

    /* ===== CONTENIDO PRINCIPAL ===== */
    .main-content {
        margin-left: 270px;
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .sidebar.collapsed ~ .main-content { margin-left: 72px; }

    /* ===== TOPBAR ===== */
    .topbar {
        background: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 8px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 0;
        z-index: 1040;
        min-height: 64px;
    }
    .topbar-left { display: flex; align-items: center; flex: 1; min-width: 0; }
    .topbar-title {
        background: linear-gradient(90deg, #dbeafe 0%, #eef1f5 100%);
        color: #1e4a62;
        padding: 0.9rem 1.5rem;
        font-weight: 600;
        font-size: 0.92rem;
        letter-spacing: 0.3px;
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-left: 3px solid #1e4a62;
    }
    .topbar-right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0 1.25rem;
    }
    .topbar-user {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #4a5568;
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 500;
        padding: 0.4rem 0.6rem;
        border-radius: 8px;
        transition: background 0.2s;
    }
    .topbar-user:hover { background: #f3f4f6; }
    .topbar-user img {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 2px solid #e2e8f0;
    }
    .btn-icon {
        background: none;
        border: none;
        color: #6b7280;
        font-size: 1.1rem;
        cursor: pointer;
        padding: 0.55rem;
        border-radius: 8px;
        transition: all 0.2s;
        position: relative;
    }
    .btn-icon:hover { background: #f3f4f6; color: #374151; }

    /* ===== NOTIFICACIONES ===== */
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
        display: flex;
        justify-content: space-between;
        align-items: center;
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
    .notification-item.read { opacity: 0.7; }
    .notification-item:hover { background: rgba(255,255,255,0.05); }
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
    .notification-icon i { color: #5bc0de; font-size: 0.9rem; }

    /* ===== CHATBOT FLOTANTE ===== */
    .chatbot-float {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 58px;
        height: 58px;
        background: linear-gradient(135deg, #5a5e6a 0%, #3c4150 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
        transition: all 0.25s;
        z-index: 1060;
        border: 3px solid white;
    }
    .chatbot-float:hover {
        transform: scale(1.08) rotate(5deg);
        background: linear-gradient(135deg, #7cb342 0%, #5a8a3a 100%);
    }
    .chatbot-iframe { width: 100%; height: 550px; border: none; border-radius: 20px; }

    /* ===== FOOTER ===== */
    .footer {
        background: white;
        padding: 1rem 1.5rem;
        text-align: center;
        font-size: 0.8rem;
        color: #6b7280;
        border-top: 1px solid #e5e7eb;
        margin-top: auto;
    }
    .footer a { color: #4a7c59; text-decoration: none; font-weight: 500; }
    .footer a:hover { text-decoration: underline; }

    .content-wrapper { padding: 1.5rem; flex: 1; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .sidebar { transform: translateX(-100%); width: 270px; }
        .sidebar.mobile-open { transform: translateX(0); }
        .sidebar.collapsed { width: 270px; }
        .main-content { margin-left: 0 !important; }
        .topbar-title { font-size: 0.8rem; padding: 0.8rem 1rem; }
    }
    @media (max-width: 768px) {
        .notification-dropdown { min-width: 290px; }
        .topbar-right .topbar-user span { display: none; }
    }

    /* Overlay para móvil */
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1045;
        backdrop-filter: blur(2px);
    }
    .sidebar-overlay.active { display: block; }
    @media (min-width: 993px) { .sidebar-overlay { display: none !important; } }
        /* Dropdown notificaciones */
    .notif-dropdown {
        width: 360px;
        max-height: 450px;
        overflow: hidden;
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }
    .notif-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--color-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .notif-header h6 {
        font-weight: 700;
        margin: 0;
        font-size: 0.95rem;
    }
    .notif-list {
        max-height: 320px;
        overflow-y: auto;
    }
    .notif-list::-webkit-scrollbar { width: 4px; }
    .notif-list::-webkit-scrollbar-thumb { background: var(--color-border); border-radius: 10px; }
    .notif-item {
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        text-decoration: none;
        color: inherit;
    }
    .notif-item:hover { background: #f8fafc; }
    .notif-item.unread { background: #eff6ff; border-left: 3px solid var(--color-primary); }
    .notif-item .notif-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem; flex-shrink: 0;
    }
    .notif-item .notif-content { flex: 1; }
    .notif-item .notif-content p { margin: 0; font-size: 0.82rem; line-height: 1.4; color: var(--color-text); }
    .notif-item .notif-content small { font-size: 0.7rem; color: var(--color-text-muted); }
    .notif-empty {
        text-align: center; padding: 2.5rem 1rem; color: var(--color-text-muted);
    }
    .notif-empty i { font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.4; }

    /* Modal evento */
    .modal-evento .modal-content {
        border: none; border-radius: 24px; overflow: hidden;
    }
    .modal-evento .modal-body {
        position: relative; padding: 3rem 2rem; text-align: center; color: white;
    }
    .globo-float {
        position: absolute; opacity: 0.2; pointer-events: none;
        animation: floatGlobo 4s ease-in-out infinite;
    }
    @keyframes floatGlobo {
        0%,100%{transform:translateY(0) rotate(0deg);}
        50%{transform:translateY(-20px) rotate(5deg);}
    }
</style>

    @stack('styles')
</head>
<body>

<!-- ====== REEMPLAZA EL SIDEBAR COMPLETO ====== -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <!-- Header -->
    <div class="sidebar-header">
        <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo-gober-i.png') }}" alt="Logo">
            <span>
                <h3>UGRH</h3>
                <small>Gestión RRHH</small>
            </span>
        </div>
    </div>

    <!-- Usuario -->
    <div class="sidebar-user">
        <img src="{{ asset('dashmin/img/user.jpg') }}" alt="User">
        <div class="sidebar-user-info">
            <h6>{{ Auth::user()->name }}</h6>
            <span>{{ Auth::user()->getRoleNames()->first() ?? 'Sin rol' }}</span>
        </div>
    </div>

    <!-- Navegación reorganizada -->
    <nav class="sidebar-nav">

        <!-- ===== SECCIÓN: PRINCIPAL ===== -->
        @can('ver inicio')
            <div class="nav-section-title"><span>Principal</span></div>
        @endcan

        @can('ver inicio')
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link" data-tooltip="Inicio">
                    <i class="fas fa-home nav-icon"></i>
                    <span class="label">Inicio</span>
                </a>
            </div>
        @endcan

        @role('empleado')
            <div class="nav-item">
                <a href="{{ route('empleado.dashboard') }}" class="nav-link" data-tooltip="Mi Dashboard">
                    <i class="fas fa-id-card nav-icon"></i>
                    <span class="label">Mi Dashboard</span>
                </a>
            </div>
        @endrole

        @can('ver inicio')
            <div class="nav-item">
                <a href="{{ asset('/inicio/archivos') }}" class="nav-link" data-tooltip="Documentación">
                    <i class="fas fa-folder-open nav-icon"></i>
                    <span class="label">Documentación</span>
                </a>
            </div>
        @endcan

        <!-- ===== SECCIÓN: GESTIÓN DE PERSONAL ===== -->
        @canany(['ver usuarios', 'ver personal', 'ver organizacional', 'ver asignar item'])
            <div class="nav-section-title"><span>Gestión Personal</span></div>
        @endcanany

        @can('ver usuarios')
            <div class="nav-item has-submenu" data-menu="usuarios">
                <a href="javascript:void(0)" class="nav-link" data-tooltip="Usuarios">
                    <i class="fas fa-user-cog nav-icon"></i>
                    <span class="label">Usuarios</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
                <div class="submenu">
                    <a href="{{ asset('/users') }}" class="nav-link">
                        <i class="fas fa-user-plus nav-icon"></i><span class="label">Crear / Modificar</span>
                    </a>
                    <a href="{{ asset('/roles') }}" class="nav-link">
                        <i class="fas fa-user-shield nav-icon"></i><span class="label">Roles y Permisos</span>
                    </a>
                </div>
            </div>
        @endcan

        @can('ver personal')
            <div class="nav-item">
                <a href="{{ asset('/reporte') }}" class="nav-link" data-tooltip="Personal">
                    <i class="fas fa-id-badge nav-icon"></i>
                    <span class="label">Personal</span>
                </a>
            </div>
        @endcan

        @can('ver organizacional')
            <div class="nav-item has-submenu" data-menu="organizacional">
                <a href="javascript:void(0)" class="nav-link" data-tooltip="Organizacional">
                    <i class="fas fa-sitemap nav-icon"></i>
                    <span class="label">Organizacional</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
                <div class="submenu">
                    <a href="{{ asset('/admin/puestos') }}" class="nav-link">
                        <i class="fas fa-briefcase nav-icon"></i><span class="label">Puestos</span>
                    </a>
                    <a href="{{ asset('/unidades') }}" class="nav-link">
                        <i class="fas fa-project-diagram nav-icon"></i><span class="label">Organigrama</span>
                    </a>
                </div>
            </div>
        @endcan

        @can('ver asignar item')
            <div class="nav-item">
                <a href="{{ asset('/historial') }}" class="nav-link" data-tooltip="Asignar Item">
                    <i class="fas fa-tasks nav-icon"></i>
                    <span class="label">Asignar Item</span>
                </a>
            </div>
        @endcan

        <!-- ===== SECCIÓN: ACTIVOS / PASIVOS ===== -->
        @canany(['ver pasivos', 'ver bajas'])
            <div class="nav-section-title"><span>Estado Personal</span></div>
        @endcanany

        @can('ver pasivos')
            <div class="nav-item has-submenu" data-menu="pasivos">
                <a href="javascript:void(0)" class="nav-link" data-tooltip="Pasivos">
                    <i class="fas fa-archive nav-icon"></i>
                    <span class="label">Pasivos</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
                <div class="submenu">
                    <a href="{{ asset('/pasivouno') }}" class="nav-link">
                        <i class="fas fa-user-alt-slash nav-icon"></i><span class="label">Ex CORDECO</span>
                    </a>
                    <a href="{{ asset('/pasivodos') }}" class="nav-link">
                        <i class="fas fa-user-slash nav-icon"></i><span class="label">GADC</span>
                    </a>
                </div>
            </div>
        @endcan

        @can('ver bajas')
            <div class="nav-item">
                <a href="{{ asset('/altasbajas/index') }}" class="nav-link" data-tooltip="Bajas">
                    <i class="fas fa-user-minus nav-icon"></i>
                    <span class="label">Bajas</span>
                </a>
            </div>
        @endcan

        <!-- ===== SECCIÓN: GESTIÓN INTERNA ===== -->
        @canany(['ver configuracion'])
            @role('admin')
                <div class="nav-section-title"><span>CONTROL DE PERSONAL</span></div>
            @endrole
        @endcan

        @role('admin')
            <div class="nav-item has-submenu" data-menu="vacaciones-asistencias">
                <a href="javascript:void(0)" class="nav-link" data-tooltip="Vacaciones y Asistencias">
                    <i class="fas fa-calendar-alt nav-icon"></i>
                    <span class="label">Asistencia</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
                <div class="submenu">
                    <a href="{{ route('dispositivos.index') }}" class="nav-link">
                        <i class="fas fa-umbrella-beach nav-icon"></i><span class="label">Biometricos y importacion</span>
                    </a>
                    <a href="{{ route('horarios.index') }}" class="nav-link">
                        <i class="fas fa-clock nav-icon"></i><span class="label">Horarios</span>
                    </a>
                    <a href="{{ route('asignacion.index') }}" class="nav-link">
                        <i class="fas fa-clock nav-icon"></i><span class="label">Asignar horario</span>
                    </a>
                    <a href="{{ route('asistencia.index') }}" class="nav-link">
                        <i class="fas fa-clock nav-icon"></i><span class="label">Asistencia</span>
                    </a>
                </div>
            </div>
        @endrole

        @role('empleado')
            <div class="nav-item has-submenu" data-menu="mis-datos">
                <a href="javascript:void(0)" class="nav-link" data-tooltip="Mis datos">
                    <i class="fas fa-user nav-icon"></i>
                    <span class="label">Mis datos</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
                <div class="submenu">
                    <a href="#" class="nav-link">
                        <i class="fas fa-calendar-check nav-icon"></i><span class="label">Mis Asistencias</span>
                    </a>
                    <a href="{{ route('empleado.vacaciones.index') }}" class="nav-link">
                        <i class="fas fa-umbrella-beach nav-icon"></i><span class="label">Mis Vacaciones</span>
                    </a>
                    <a href="{{ route('empleado.perfil') }}" class="nav-link">
                        <i class="fas fa-id-card nav-icon"></i><span class="label">Mi Perfil</span>
                    </a>
                </div>
            </div>
        @endrole


        <!-- ===== SECCIÓN: DOCUMENTOS Y REPORTES ===== -->
        @canany(['ver biblioteca planillas', 'ver configuracion'])
            <div class="nav-section-title"><span>Documentos</span></div>
        @endcanany

        @can('ver biblioteca planillas')
            <div class="nav-item">
                <a href="{{ asset('/planillas-pdf/index') }}" class="nav-link" data-tooltip="Biblioteca Planillas">
                    <i class="fas fa-file-pdf nav-icon"></i>
                    <span class="label">Biblioteca Planillas</span>
                </a>
            </div>
        @endcan

        @can('ver configuracion')
            <div class="nav-item">
                <a href="{{ route('documentos.index') }}" class="nav-link" data-tooltip="Documentos Respaldo">
                    <i class="fas fa-folder nav-icon"></i>
                    <span class="label">Doc. Respaldo</span>
                </a>
            </div>
        @role('archivo')
            <div class="nav-item">
                <a href="{{ route('prestamos.index') }}" class="nav-link" data-tooltip="Préstamos">
                    <i class="fas fa-hand-holding-heart nav-icon"></i>
                    <span class="label">Préstamos</span>
                </a>
            </div>
        @endrole
            <div class="nav-item">
                <a href="{{ route('reportes.dashboard') }}" class="nav-link" data-tooltip="Reportes">
                    <i class="fas fa-chart-line nav-icon"></i>
                    <span class="label">Reportes</span>
                </a>
            </div>
        @endcan

        <!-- ===== SECCIÓN: CONFIGURACIÓN ===== -->
        @can('ver configuracion')
            <div class="nav-section-title"><span>Configuración</span></div>

            <div class="nav-item has-submenu" data-menu="configuracion">
                <a href="javascript:void(0)" class="nav-link" data-tooltip="Opciones del sistema">
                    <i class="fas fa-cog nav-icon"></i>
                    <span class="label">Opciones</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </a>
                <div class="submenu">
                    <a href="{{ asset('/apertura-gestion/gestion') }}" class="nav-link">
                        <i class="fas fa-calendar nav-icon"></i><span class="label">Gestión</span>
                    </a>
                    <a href="{{ asset('/feriado-gestion/feriado') }}" class="nav-link">
                        <i class="fas fa-calendar-day nav-icon"></i><span class="label">Feriados</span>
                    </a>
                    <a href="{{ asset('/tipo-salida/salida') }}" class="nav-link">
                        <i class="fas fa-sign-out-alt nav-icon"></i><span class="label">Tipo de Salida</span>
                    </a>
                    <a href="{{ asset('/beneficios/asignar') }}" class="nav-link">
                        <i class="fas fa-gift nav-icon"></i><span class="label">Beneficios</span>
                    </a>
                    <a href="{{ asset('/cas') }}" class="nav-link">
                        <i class="fas fa-id-badge nav-icon"></i><span class="label">CAS</span>
                    </a>
                    <a href="{{ asset('/kardex') }}" class="nav-link">
                        <i class="fas fa-clipboard-list nav-icon"></i><span class="label">Kardex</span>
                    </a>
                </div>
            </div>
        @endcan

        <!-- ===== ACCIÓN DESTACADA ===== -->
        <div class="nav-section-title"><span>Acciones</span></div>
        <div class="nav-item highlight">
            <a href="{{ asset('/solicitudes/dashboard') }}" class="nav-link" data-tooltip="Validar salidas">
                <i class="fas fa-check-double nav-icon"></i>
                <span class="label">Validar Salidas</span>
                <span class="nav-badge" id="salidasBadge">0</span>
            </a>
        </div>

    </nav>
</aside>

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
                {{-- NOTIFICACIONES --}}
                <div class="dropdown">
                    <button class="btn-topbar" id="btnNotif" data-bs-toggle="dropdown" aria-expanded="false" title="Notificaciones">
                        <i class="fas fa-bell"></i>
                        <span class="badge-count" id="badgeNotif" style="display:none;">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0" id="dropdownNotif">
                        <div class="notif-header">
                            <h6><i class="fas fa-bell me-2 text-primary"></i>Notificaciones</h6>
                            <button class="btn btn-sm btn-link text-decoration-none" onclick="marcarTodasLeidas()" style="font-size:0.75rem;">
                                Marcar todas
                            </button>
                        </div>
                        <div class="notif-list" id="listaNotificaciones">
                            <div class="notif-empty">
                                <i class="fas fa-bell-slash"></i>
                                <p class="mb-0 mt-2">No hay notificaciones nuevas</p>
                            </div>
                        </div>
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
document.addEventListener('DOMContentLoaded', function() {

    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');

    // 1) Cargar estado guardado del sidebar
    const savedState = localStorage.getItem('sidebarState');
    if (savedState === 'collapsed' && window.innerWidth > 992) {
        sidebar.classList.add('collapsed');
    }

    // Cargar submenús abiertos
    const openMenus = JSON.parse(localStorage.getItem('openMenus') || '[]');
    openMenus.forEach(menuId => {
        const menuItem = document.querySelector(`[data-menu="${menuId}"]`);
        if (menuItem && !sidebar.classList.contains('collapsed')) {
            menuItem.classList.add('open');
        }
    });

    // 2) Toggle sidebar
    toggleBtn.addEventListener('click', () => {
        if (window.innerWidth <= 992) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarState',
                sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');

            // Al colapsar, cerrar submenús abiertos visualmente
            if (sidebar.classList.contains('collapsed')) {
                document.querySelectorAll('.has-submenu.open').forEach(m => m.classList.remove('open'));
            } else {
                // Al expandir, restaurar submenús guardados
                openMenus.forEach(menuId => {
                    const menuItem = document.querySelector(`[data-menu="${menuId}"]`);
                    if (menuItem) menuItem.classList.add('open');
                });
            }
        }
    });

    // 3) Cerrar overlay al hacer clic fuera
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

    // 4) ACORDEÓN de submenús
    document.querySelectorAll('.has-submenu > .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (sidebar.classList.contains('collapsed')) return; // en colapsado, hover maneja

            const parent = this.closest('.has-submenu');
            const menuId = parent.getAttribute('data-menu');

            // Cerrar otros submenús del mismo nivel (opcional - comentar si quieres varios abiertos)
            const siblings = parent.parentElement.querySelectorAll('.has-submenu.open');
            siblings.forEach(s => {
                if (s !== parent) {
                    s.classList.remove('open');
                    const sId = s.getAttribute('data-menu');
                    const idx = openMenus.indexOf(sId);
                    if (idx > -1) openMenus.splice(idx, 1);
                }
            });

            // Toggle actual
            parent.classList.toggle('open');

            // Guardar estado
            const index = openMenus.indexOf(menuId);
            if (parent.classList.contains('open')) {
                if (index === -1) openMenus.push(menuId);
            } else {
                if (index > -1) openMenus.splice(index, 1);
            }
            localStorage.setItem('openMenus', JSON.stringify(openMenus));
        });
    });

    // 5) Marcar item activo según URL actual
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href !== '#' && href !== 'javascript:void(0)' && currentPath.includes(href.replace(window.location.origin, ''))) {
            const navItem = link.closest('.nav-item');
            if (navItem && !navItem.classList.contains('has-submenu')) {
                // Remover activos previos
                document.querySelectorAll('.nav-item.active').forEach(a => a.classList.remove('active'));
                navItem.classList.add('active');

                // Abrir el submenu padre si existe
                const parentSubmenu = navItem.closest('.has-submenu');
                if (parentSubmenu && !sidebar.classList.contains('collapsed')) {
                    parentSubmenu.classList.add('open');
                    const pId = parentSubmenu.getAttribute('data-menu');
                    if (!openMenus.includes(pId)) {
                        openMenus.push(pId);
                        localStorage.setItem('openMenus', JSON.stringify(openMenus));
                    }
                }
            }
        }
    });

    // 6) Cerrar sidebar móvil al hacer clic en un link
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 992 &&
                !link.closest('.has-submenu')) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            }
        });
    });

    // 7) Actualizar badge de salidas (simulado - reemplaza con tu endpoint real)
    function actualizarBadgeSalidas() {
        fetch('/api/solicitudes/pendientes/count')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('salidasBadge');
                if (badge) {
                    badge.textContent = data.count || 0;
                    badge.style.display = (data.count || 0) > 0 ? 'inline-block' : 'none';
                }
            })
            .catch(() => {
                // Si no hay endpoint, mantener en 0
            });
    }
    cargarNotificaciones();
    actualizarBadgeSalidas();
    setInterval(actualizarBadgeSalidas, 60000);
});
// ================================================================
// SISTEMA DE NOTIFICACIONES CON AXIOS
// ================================================================
function cargarNotificaciones() {
    axios.get('{{ route('notificaciones.no-leidas') }}')
        .then(res => {
            const data = res.data;
            const badge = document.getElementById('badgeNotif');
            const lista = document.getElementById('listaNotificaciones');

            // Badge contador
            if (data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }

            // Lista vacía
            if (data.notificaciones.length === 0) {
                lista.innerHTML = `
                    <div class="notif-empty">
                        <i class="fas fa-bell-slash"></i>
                        <p class="mb-0 mt-2">No hay notificaciones nuevas</p>
                    </div>`;
                return;
            }

            // Renderizar notificaciones
            lista.innerHTML = data.notificaciones.map(n => {
                const iconos = {
                    'nueva_solicitud':   'fa-file-circle-plus',
                    'prestamo_aprobado': 'fa-circle-check',
                    'prestamo_rechazado':'fa-circle-xmark',
                    'prestamo_entregado':'fa-box-open',
                    'prestamo_devuelto': 'fa-rotate-left',
                    'prestamo_vencido':  'fa-triangle-exclamation',
                    'default':           'fa-bell'
                };
                const icono = iconos[n.tipo] || iconos['default'];

                const colores = {
                    'nueva_solicitud':   'bg-primary-subtle text-primary',
                    'prestamo_aprobado': 'bg-success-subtle text-success',
                    'prestamo_rechazado':'bg-danger-subtle text-danger',
                    'prestamo_vencido':  'bg-warning-subtle text-warning',
                    'default':           'bg-info-subtle text-info'
                };
                const colorClass = colores[n.tipo] || colores['default'];

                return `
                    <a href="${n.url}" class="notif-item unread" onclick="marcarLeida('${n.id}', event)">
                        <div class="notif-icon ${colorClass}">
                            <i class="fas ${icono}"></i>
                        </div>
                        <div class="notif-content">
                            <p class="fw-semibold mb-1">${n.titulo}</p>
                            <p class="mb-1">${n.mensaje}</p>
                            <small><i class="far fa-clock me-1"></i>${n.creado}</small>
                        </div>
                    </a>`;
            }).join('');
        })
        .catch(err => {
            console.error('Error cargando notificaciones:', err);
        });
}

function marcarLeida(id, event) {
    event.preventDefault();
    axios.post(`/notificaciones/${id}/leida`)
        .then(() => {
            cargarNotificaciones();
            // Redirigir después de marcar como leída
            const href = event.currentTarget.getAttribute('href');
            if (href && href !== '#') {
                window.location.href = href;
            }
        })
        .catch(err => {
            console.error('Error marcando notificación:', err);
        });
}

function marcarTodasLeidas() {
    axios.post('{{ route('notificaciones.todas-leidas') }}')
        .then(() => {
            cargarNotificaciones();
        })
        .catch(err => {
            console.error('Error marcando todas:', err);
        });
}
</script>

    @stack('scripts')
</body>
</html>
