<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UGRH · Portal del Empleado</title>
    <link href="{{ asset('images/logo-gober-i.png') }}" rel="icon" type="image/png">

    <!-- Bootstrap 5 + Iconos + Fuentes -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300..700&display=swap" rel="stylesheet">
    <!-- Librerías auxiliares -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Animate.css + Confetti --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<style>
    /* Variables dinámicas desde Laravel */
    :root {
        --theme-primary: {{ $contextoEvento['tema']['color_primario'] }};
        --theme-secondary: {{ $contextoEvento['tema']['color_secundario'] }};
    }

    /* Tema dinámico en navbar */
    .topbar-themed {
        background: linear-gradient(135deg, var(--theme-primary), var(--theme-secondary)) !important;
        transition: background 0.6s ease;
    }

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

    <style>
        /* ============================================================
               VARIABLES CSS (colores base → Cochabamba)
               ============================================================ */
        :root {
            --color-primary: #4DA3FF;      /* Celeste Cochabamba */
            --color-primary-dark: #2F80ED; /* Celeste oscuro */
            --color-secondary: #FFFFFF;    /* Blanco */
            --color-accent: #87CEEB;       /* Celeste claro */
            /* Amarillo */
            --color-accent: #1D9C4B;
            /* Verde */
            --color-bg: #f4f6fa;
            --color-white: #ffffff;
            --color-text: #1e293b;
            --color-text-muted: #64748b;
            --color-border: #e2e8f0;
            --color-shadow: rgba(0, 0, 0, 0.06);
            --radius-card: 20px;
            --radius-sm: 12px;
            --transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ============================================================
               RESET Y BASE
               ============================================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--color-bg);
            color: var(--color-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ============================================================
               TOPBAR
               ============================================================ */
        .topbar {
            background: var(--color-primary);
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.75rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            box-shadow: 0 4px 24px rgba(193, 39, 45, 0.25);
            transition: background var(--transition);
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: white;
        }

        .topbar-brand .logo-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            transition: background var(--transition);
            position: relative;
        }

        .topbar-brand .logo-wrapper img {
            width: 70%;
            height: 70%;
            object-fit: contain;
            filter: brightness(0) invert(1);
            transition: all var(--transition);
        }

        .topbar-brand .logo-wrapper .logo-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 800;
            color: white;
            opacity: 0;
            transition: opacity var(--transition);
            background: rgba(0, 0, 0, 0.2);
            border-radius: 14px;
        }

        .topbar-brand .logo-wrapper.has-theme .logo-overlay {
            opacity: 1;
        }

        .topbar-brand .brand-text {
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }

        .topbar-brand .brand-text small {
            display: block;
            font-weight: 400;
            font-size: 0.7rem;
            opacity: 0.8;
            letter-spacing: 0.5px;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-topbar {
            width: 42px;
            height: 42px;
            border-radius: var(--radius-sm);
            border: none;
            background: rgba(255, 255, 255, 0.12);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition);
            font-size: 1.05rem;
            position: relative;
        }

        .btn-topbar:hover {
            background: rgba(255, 255, 255, 0.22);
            transform: scale(1.04);
        }

        .btn-topbar .badge-count {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--color-accent);
            color: white;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 30px;
            border: 2px solid var(--color-primary);
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.3rem 0.9rem 0.3rem 0.3rem;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 50px;
            border: none;
            color: white;
            cursor: pointer;
            transition: all var(--transition);
        }

        .user-pill:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .user-pill .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--color-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--color-primary);
            overflow: hidden;
        }

        .user-pill .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-pill-info {
            text-align: left;
            line-height: 1.2;
        }

        .user-pill-info .name {
            font-size: 0.85rem;
            font-weight: 600;
        }

        .user-pill-info .role {
            font-size: 0.65rem;
            opacity: 0.75;
        }

        /* Dropdown perfil */
        .perfil-card {
            text-align: center;
            padding: 1.75rem 1.5rem;
            background: var(--color-primary);
            color: white;
            border-radius: 16px 16px 0 0;
            margin: -12px -12px 8px -12px;
        }

        .perfil-avatar-big {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--color-secondary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-size: 2.2rem;
            color: var(--color-primary);
            border: 3px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 0.75rem;
        }

        .perfil-avatar-big img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ============================================================
               SIDEBAR
               ============================================================ */
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            width: 270px;
            height: calc(100vh - 70px);
            background: var(--color-white);
            border-right: 1px solid var(--color-border);
            z-index: 1030;
            overflow-y: auto;
            padding: 1.5rem 1rem;
            transition: transform 0.3s ease;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--color-border);
            border-radius: 10px;
        }

        .sidebar-section {
            margin-bottom: 2rem;
        }

        .sidebar-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--color-text-muted);
            margin-bottom: 0.75rem;
            padding-left: 0.5rem;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--color-text);
            font-size: 0.9rem;
            font-weight: 500;
            transition: all var(--transition);
            margin-bottom: 4px;
            border: 1px solid transparent;
        }

        .sidebar-item:hover {
            background: var(--color-bg);
            border-color: var(--color-border);
            transform: translateX(4px);
        }

        .sidebar-item.active {
            background: rgba(193, 39, 45, 0.07);
            color: var(--color-primary);
            border-color: rgba(193, 39, 45, 0.15);
            font-weight: 600;
        }

        .sidebar-item .item-icon {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
            transition: all var(--transition);
        }

        .sidebar-item:hover .item-icon {
            transform: scale(1.05);
        }

        .sidebar-item .item-badge {
            margin-left: auto;
            background: var(--color-primary);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 30px;
        }

        /* ============================================================
               MAIN CONTENT
               ============================================================ */
        .main-content {
            margin-left: 270px;
            margin-top: 70px;
            padding: 1.75rem;
            flex: 1;
        }

        /* Banner de bienvenida */
        .welcome-banner {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            border-radius: var(--radius-card);
            padding: 2rem 2.5rem;
            color: white;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            transition: background var(--transition);
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
        }

        .welcome-banner h2 {
            font-size: 1.65rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
        }

        .welcome-banner .banner-message {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem 1.5rem;
        }

        .welcome-banner p {
            opacity: 0.9;
            margin: 0;
            font-size: 0.95rem;
        }

        .welcome-banner .theme-tag {
            background: rgba(255, 255, 255, 0.18);
            padding: 0.3rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* ============================================================
               TARJETAS PRINCIPALES (solicitudes)
               ============================================================ */
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--color-primary);
        }

        .big-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        .big-card {
            background: var(--color-white);
            border-radius: var(--radius-card);
            padding: 1.75rem 1.5rem;
            text-align: center;
            text-decoration: none;
            color: var(--color-text);
            border: 1px solid var(--color-border);
            transition: all var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px var(--color-shadow);
        }

        .big-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            opacity: 0;
            transition: opacity var(--transition);
        }

        .big-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.07);
            border-color: transparent;
        }

        .big-card:hover::before {
            opacity: 1;
        }

        .big-card-icon {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.8rem;
            transition: all var(--transition);
        }

        .big-card:hover .big-card-icon {
            transform: scale(1.08) rotate(-4deg);
        }

        .big-card h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }

        .big-card p {
            font-size: 0.8rem;
            color: var(--color-text-muted);
            margin: 0;
        }

        .big-card .arrow {
            margin-top: 1rem;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            opacity: 0;
            transform: translateX(-10px);
            transition: all var(--transition);
        }

        .big-card:hover .arrow {
            opacity: 1;
            transform: translateX(0);
        }

        /* Colores por tipo (con variables) */
        .card-vacacion .big-card-icon {
            background: rgba(29, 156, 75, 0.10);
            color: var(--color-accent);
        }
        .card-vacacion::before {
            background: var(--color-accent);
        }
        .card-vacacion .arrow {
            background: rgba(29, 156, 75, 0.12);
            color: var(--color-accent);
        }

        .card-comision .big-card-icon {
            background: rgba(253, 184, 19, 0.15);
            color: #c9940a;
        }
        .card-comision::before {
            background: var(--color-secondary);
        }
        .card-comision .arrow {
            background: rgba(253, 184, 19, 0.15);
            color: #c9940a;
        }

        .card-particular .big-card-icon {
            background: rgba(108, 43, 217, 0.10);
            color: #6c2bd9;
        }
        .card-particular::before {
            background: #6c2bd9;
        }
        .card-particular .arrow {
            background: rgba(108, 43, 217, 0.10);
            color: #6c2bd9;
        }

        .card-medica .big-card-icon {
            background: rgba(239, 68, 68, 0.10);
            color: #ef4444;
        }
        .card-medica::before {
            background: #ef4444;
        }
        .card-medica .arrow {
            background: rgba(239, 68, 68, 0.10);
            color: #ef4444;
        }

        /* ============================================================
               ESTADÍSTICAS RÁPIDAS
               ============================================================ */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: var(--color-white);
            border-radius: var(--radius-card);
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1px solid var(--color-border);
            transition: all var(--transition);
            box-shadow: 0 2px 6px var(--color-shadow);
        }

        .stat-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            border-color: var(--color-primary);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            background: rgba(193, 39, 45, 0.08);
            color: var(--color-primary);
        }

        .stat-info h5 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
            line-height: 1.2;
        }

        .stat-info p {
            font-size: 0.75rem;
            color: var(--color-text-muted);
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* ============================================================
               MODALES MEJORADOS
               ============================================================ */
        .modal-content {
            border: none;
            border-radius: var(--radius-card);
            overflow: hidden;
        }

        .modal-header-custom {
            background: var(--color-primary);
            color: white;
            padding: 1.25rem 1.75rem;
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
            background: rgba(255, 255, 255, 0.15);
            border: none;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition);
        }

        .modal-header-custom .btn-close-custom:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .table-modern {
            margin: 0;
        }

        .table-modern thead th {
            background: #f8fafc;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--color-text-muted);
            padding: 1rem 1.25rem;
            border-bottom: 2px solid var(--color-border);
        }

        .table-modern tbody td {
            padding: 1rem 1.25rem;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .badge-estado {
            padding: 0.35rem 0.85rem;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-pendiente {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-aprobado {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-rechazado {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ============================================================
               FOOTER
               ============================================================ */
        .app-footer {
            margin-left: 270px;
            background: var(--color-white);
            border-top: 1px solid var(--color-border);
            padding: 1.25rem;
            text-align: center;
            font-size: 0.8rem;
            color: var(--color-text-muted);
        }

        .app-footer strong {
            color: var(--color-primary);
        }

        /* ============================================================
               RESPONSIVE
               ============================================================ */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
            }
            .app-footer {
                margin-left: 0;
            }
            .sidebar {
                transform: translateX(-100%);
                z-index: 1050;
                width: 280px;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
        }

        @media (max-width: 576px) {
            .topbar {
                padding: 0 1rem;
            }
            .topbar-brand .brand-text {
                font-size: 0.9rem;
            }
            .topbar-brand .brand-text small {
                display: none;
            }
            .user-pill-info {
                display: none;
            }
            .big-cards-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }
            .stats-row {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }
            .welcome-banner {
                padding: 1.5rem;
            }
            .welcome-banner h2 {
                font-size: 1.25rem;
            }
            .main-content {
                padding: 1rem;
            }
        }

        /* ============================================================
               OVERLAY MÓVIL
               ============================================================ */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            z-index: 1045;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* ============================================================
               ANIMACIONES
               ============================================================ */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .anim-up {
            animation: fadeUp 0.5s ease forwards;
        }

        .delay-1 {
            animation-delay: 0.05s;
            opacity: 0;
        }
        .delay-2 {
            animation-delay: 0.10s;
            opacity: 0;
        }
        .delay-3 {
            animation-delay: 0.15s;
            opacity: 0;
        }

        /* ============================================================
               CLASES PARA TEMAS DINÁMICOS (se aplican via JS)
               ============================================================ */
        .theme-bolivia {
            --color-primary: #C1272D;
            --color-primary-dark: #9E1E23;
            --color-secondary: #FDB813;
            --color-accent: #1D9C4B;
        }

        .theme-cochabamba {
            --color-primary: #C1272D;
            --color-primary-dark: #9E1E23;
            --color-secondary: #FDB813;
            --color-accent: #1D9C4B;
        }

        .theme-navidad {
            --color-primary: #2D7D46;
            --color-primary-dark: #1E5A33;
            --color-secondary: #D4A017;
            --color-accent: #B22222;
        }

        .theme-cumpleanos {
            --color-primary: #8B5CF6;
            --color-primary-dark: #6D28D9;
            --color-secondary: #FCD34D;
            --color-accent: #EC4899;
        }

        /* ============================================================
               UTILIDADES ADICIONALES
               ============================================================ */
        .object-fit-cover {
            object-fit: cover;
        }
        .rounded-3 {
            border-radius: var(--radius-sm) !important;
        }
        .shadow-soft {
            box-shadow: 0 4px 20px var(--color-shadow);
        }
        .gap-2 {
            gap: 0.5rem;
        }
        .gap-3 {
            gap: 1rem;
        }
        .fw-600 {
            font-weight: 600;
        }
    </style>
</head>

<body>

    <!-- ===== OVERLAY MÓVIL ===== -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ============================================================
    TOPBAR
    ============================================================ -->
    <header class="topbar {{ $contextoEvento['tema']['tipo'] !== 'default' ? 'topbar-themed' : '' }}" id="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn-topbar d-md-none" id="btnMenuMobile" aria-label="Abrir menú">
                <i class="fas fa-bars"></i>
            </button>
            <a href="/dashboard" class="topbar-brand">
                <div class="logo-wrapper {{ $contextoEvento['tema']['icono'] ? 'has-theme' : '' }}" id="logoWrapper">
                    <img src="{{ URL::asset('images/logo-gober-i.png') }}" alt="UGRH" id="logoImg">
                    <div class="logo-overlay" id="logoOverlay">
                        <div class="logo-overlay" id="logoOverlay">
                            @if($contextoEvento['tema']['icono'])
                                <span style="font-size:1.8rem;">{{ $contextoEvento['tema']['icono'] }}</span>
                            @else
                                <i class="fas fa-flag"></i>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="brand-text">
                    UGRH
                    <small id="themeLabel">
                        {{ optional($contextoEvento['evento'])->nombre ?? ($contextoEvento['es_cumpleanos'] ? '¡Feliz Cumpleaños!' : 'Portal del Empleado') }}
                    </small>
                </div>
            </a>
        </div>

        <div class="topbar-actions">
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

            {{-- USUARIO --}}
            <div class="dropdown">
                <button class="user-pill dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar-circle">
                        <img src="{{ route('usuario.foto', Auth::user()->id) }}" alt="Foto" id="userAvatar">
                    </div>
                    <div class="user-pill-info d-none d-sm-block">
                        <div class="name" id="topbarNombre">{{ Auth::user()->name }}</div>
                        <div class="role">Empleado</div>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2" style="border-radius:16px; min-width:260px;">
                    <li class="perfil-card">
                        <div class="perfil-avatar-big">
                            <img src="{{ route('usuario.foto', Auth::user()->id) }}" alt="Foto">
                        </div>
                        <h5 class="mb-1" id="dropdownNombre">{{ Auth::user()->name }}</h5>
                        <small id="dropdownUsuario">{{ Auth::user()->email ?? Auth::user()->usuario }}</small>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 py-2" href="#" data-bs-toggle="modal" data-bs-target="#modalPerfil">
                            <i class="fas fa-id-card me-2 text-muted"></i> Mi Perfil
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- ============================================================
    SIDEBAR
    ============================================================ -->
    <aside class="sidebar" id="sidebar">
        <!-- Solicitar Salida -->
        <div class="sidebar-section">
            <div class="sidebar-title">Solicitar Salida</div>
            <a href="/empleado/vacacion/mi-historial" class="sidebar-item">
                <div class="item-icon" style="background:rgba(29,156,75,0.10); color:var(--color-accent);">
                    <i class="fas fa-umbrella-beach"></i>
                </div>
                <span>Vacaciones</span>
            </a>
            <a href="/comision/usuario" class="sidebar-item">
                <div class="item-icon" style="background:rgba(253,184,19,0.15); color:#c9940a;">
                    <i class="fas fa-person-walking-arrow-right"></i>
                </div>
                <span>Comisiones</span>
            </a>
            <a href="/empleado/salida-particular" class="sidebar-item">
                <div class="item-icon" style="background:rgba(108,43,217,0.10); color:#6c2bd9;">
                    <i class="fas fa-person-walking-arrow-loop-left"></i>
                </div>
                <span>Salida Particular</span>
            </a>
            <a href="/salud/usuario" class="sidebar-item">
                <div class="item-icon" style="background:rgba(239,68,68,0.10); color:#ef4444;">
                    <i class="fas fa-truck-medical"></i>
                </div>
                <span>Salida Médica</span>
            </a>
        </div>

        <!-- Visto Bueno -->
        <div class="sidebar-section">
            <div class="sidebar-title">Visto Bueno</div>
            <a href="/jefe/dashboard" class="sidebar-item" id="listSol">
                <div class="item-icon" style="background:rgba(193,39,45,0.08); color:var(--color-primary);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <span>Solicitudes Pendientes</span>
                <span class="item-badge" id="badgePendientes">0</span>
            </a>
        </div>

        <!-- Reportes -->
        <div class="sidebar-section">
            <div class="sidebar-title">Reportes</div>
            <a href="/reportes-empleado/salidas" class="sidebar-item">
                <div class="item-icon" style="background:rgba(52,152,219,0.10); color:#3498db;">
                    <i class="fas fa-table-list"></i>
                </div>
                <span>Reporte de Salidas</span>
            </a>
            <a href="/beneficios/index" class="sidebar-item">
                <div class="item-icon" style="background:rgba(255,179,0,0.10); color:#e6a800;">
                    <i class="fas fa-list-check"></i>
                </div>
                <span>Listar Beneficios</span>
            </a>
            <a href="/asistencia/empleado" class="sidebar-item">
                <div class="item-icon" style="background:rgba(46,204,113,0.12); color:#27ae60;">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <span>Asistencia</span>
            </a>
            <a href="/empleado/perfil" class="sidebar-item">
                <div class="item-icon" style="background:rgba(129,212,250,0.15); color:#0277BD;">
                    <i class="fas fa-street-view"></i>
                </div>
                <span>Datos del Personal</span>
            </a>
        </div>
    </aside>

    <!-- ============================================================
    MAIN CONTENT
    ============================================================ -->
    <main class="main-content">
        {{-- Banner dinámico: bienvenida, cumpleaños o evento --}}
        @if($contextoEvento['tema']['mensaje'])
            <div class="welcome-banner anim-up delay-1"
                style="background: linear-gradient(135deg, {{ $contextoEvento['tema']['color_primario'] }} 0%, {{ $contextoEvento['tema']['color_secundario'] }} 100%);">
                <h2 id="welcomeTitle">
                    @if($contextoEvento['mostrar_bienvenida'])
                        👋 ¡Hola, {{ Auth::user()->name }}!
                    @elseif($contextoEvento['es_cumpleanos'])
                        🎂 ¡Feliz Cumpleaños!
                    @else
                        {{ optional($contextoEvento['evento'])->nombre ?? 'Portal UGRH' }}
                    @endif
                </h2>
                <div class="banner-message">
                    <p id="welcomeSub">{{ $contextoEvento['tema']['mensaje'] }}</p>
                    @if($contextoEvento['evento'] || $contextoEvento['es_cumpleanos'])
                        <span class="theme-tag">
                            <i class="fas fa-star"></i> {{ optional($contextoEvento['evento'])->nombre ?? 'Día Especial' }}
                        </span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Contenido de página --}}
        <div class="anim-up delay-3">
            <div class="row">
                <div class="text-center">
                    @yield('cuerpo')
                </div>
            </div>
        </div>
    </main>

    <!-- ============================================================
    FOOTER
    ============================================================ -->
    <footer class="app-footer">
        <i class="fas fa-laptop-code me-1"></i>
        Desarrollado por <strong>Jhesvany Bozo Condori</strong><br>
        Unidad de Gestión de Recursos Humanos (UGRH) · GADC © 2026
    </footer>

    <!-- ============================================================
    MODALES
    ============================================================ -->
    @if($contextoEvento['mostrar_modal'] ?? false)
        @php $tema = $contextoEvento['tema']; @endphp

        <div class="modal fade modal-evento" id="modalEventoHoy"
            data-bs-backdrop="{{ $contextoEvento['mostrar_bienvenida'] ? 'static' : 'true' }}"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body"
                        style="background: linear-gradient(135deg, {{ $tema['color_primario'] }}, {{ $tema['color_secundario'] }});">

                        <span class="globo-float" style="top:15px; left:10%; font-size:2rem; animation-delay:0s;">🎈</span>
                        <span class="globo-float" style="top:20px; left:70%; font-size:1.5rem; animation-delay:1s;">✨</span>
                        <span class="globo-float" style="top:60px; left:85%; font-size:2.2rem; animation-delay:0.5s;">🎉</span>
                        <span class="globo-float" style="top:80px; left:25%; font-size:1.8rem; animation-delay:1.5s;">🎊</span>

                        <div class="position-relative">
                            <div class="mb-3 animate__animated animate__zoomIn">
                                <span style="font-size: 5rem;">{{ $tema['icono'] ?? '🎉' }}</span>
                            </div>

                            <h2 class="mb-3 animate__animated animate__fadeInUp fw-bold">
                                @if($contextoEvento['mostrar_bienvenida']) ¡Bienvenido! @endif
                                @if($contextoEvento['es_cumpleanos']) ¡Feliz Cumpleaños! @endif
                                @if($contextoEvento['evento'] && !$contextoEvento['es_cumpleanos']) ¡Hoy es especial! @endif
                            </h2>

                            <p class="lead mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                                {{ $tema['mensaje'] }}
                            </p>

                            @if($contextoEvento['mostrar_bienvenida'])
                                <p class="mb-4 opacity-75 animate__animated animate__fadeInUp animate__delay-2s">
                                    Este es tu portal de Recursos Humanos. Aquí podrás gestionar tus asistencias, vacaciones y más.
                                </p>
                            @endif

                            <button type="button"
                                    class="btn btn-light btn-lg px-5 rounded-pill fw-bold shadow"
                                    @if($contextoEvento['mostrar_bienvenida']) onclick="cerrarBienvenida()" @else data-bs-dismiss="modal" @endif>
                                {{ $contextoEvento['mostrar_bienvenida'] ? '¡Comencemos! 🚀' : '¡Gracias! ❤️' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <!-- Modal Perfil -->
    <div class="modal fade" id="modalPerfil" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-custom" style="background:var(--color-primary);">
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
                    <button class="btn-action" data-bs-dismiss="modal" style="background:var(--color-primary); color:white; border:none; padding:0.7rem 2rem; border-radius:50px; font-weight:600;">
                        <i class="fas fa-check me-2"></i>Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>
    @yield('modales')
    <!-- ============================================================
    SCRIPTS
    ============================================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>

    <script>
// ================================================================
// CONFIGURACIÓN BASE DE AXIOS
// ================================================================
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
axios.defaults.headers.common['Accept'] = 'application/json';

// ================================================================
// DATOS DE USUARIO (desde localStorage o Blade)
// ================================================================
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

// ================================================================
// SIDEBAR MÓVIL
// ================================================================
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const btnMenu = document.getElementById('btnMenuMobile');

function toggleSidebar(open) {
    sidebar.classList.toggle('mobile-open', open);
    overlay.classList.toggle('active', open);
}

btnMenu.addEventListener('click', () => toggleSidebar(true));
overlay.addEventListener('click', () => toggleSidebar(false));

document.querySelectorAll('.sidebar-item').forEach(item => {
    item.addEventListener('click', () => toggleSidebar(false));
});

// ================================================================
// ACTIVE STATE EN SIDEBAR
// ================================================================
const path = window.location.pathname;
document.querySelectorAll('.sidebar-item').forEach(item => {
    if (item.getAttribute('href') === path) {
        item.classList.add('active');
    }
});

// ================================================================
// TEMAS DINÁMICOS DESDE LARAVEL
// ================================================================
const contextoEvento = @json($contextoEvento);
const tema = contextoEvento.tema;

if (tema.clase_css) {
    document.body.classList.add(tema.clase_css);
}

document.documentElement.style.setProperty('--theme-primary', tema.color_primario);
document.documentElement.style.setProperty('--theme-secondary', tema.color_secundario);

// ================================================================
// MODAL DE EVENTO / BIENVENIDA / CUMPLEAÑOS
// ================================================================
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('modalEventoHoy');

    // ✅ Solo si el modal existe en el DOM
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        setTimeout(() => {
            switch (tema.efecto) {
                case 'confeti':   lanzarConfeti(); break;
                case 'banderas':  lanzarConfetiBolivia(); break;
                case 'nieve':     lanzarNieve(); break;
                case 'corazones': lanzarCorazones(); break;
            }
        }, 600);
    }

    cargarNotificaciones();
});

// Cerrar bienvenida → marca en BD vía Axios
function cerrarBienvenida() {
    axios.post('{{ route('bienvenida.visto') }}')
        .then(() => {
            bootstrap.Modal.getInstance(document.getElementById('modalEventoHoy')).hide();
        })
        .catch(err => {
            console.error('Error marcando bienvenida:', err);
            bootstrap.Modal.getInstance(document.getElementById('modalEventoHoy')).hide();
        });
}

// ================================================================
// EFECTOS CONFETI (canvas-confetti)
// ================================================================
function lanzarConfeti() {
    confetti({ particleCount: 120, spread: 70, origin: { y: 0.6 } });
}

function lanzarConfetiBolivia() {
    const colors = ['#D32F2F', '#F9A825', '#388E3C'];
    const end = Date.now() + 2500;
    (function frame() {
        confetti({ particleCount: 5, angle: 60, spread: 55, origin: { x: 0 }, colors });
        confetti({ particleCount: 5, angle: 120, spread: 55, origin: { x: 1 }, colors });
        if (Date.now() < end) requestAnimationFrame(frame);
    }());
}

function lanzarNieve() {
    const end = Date.now() + 3000;
    (function frame() {
        confetti({
            particleCount: 3,
            startVelocity: 0,
            ticks: 200,
            gravity: 0.5,
            scalar: 1.5,
            origin: { x: Math.random(), y: 0 },
            colors: ['#FFFFFF', '#E3F2FD']
        });
        if (Date.now() < end) requestAnimationFrame(frame);
    }());
}

function lanzarCorazones() {
    confetti({
        particleCount: 80,
        shapes: ['circle'],
        colors: ['#E91E63', '#FF5722', '#F48FB1', '#9C27B0']
    });
}

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


console.log('✅ Portal UGRH cargado con Axios + Temas dinámicos');
    </script>

    @stack('scripts')
</body>

</html>
