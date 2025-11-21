@extends('dashboard')

@section('contenidouno')
    <meta content="Lista de personal" name="description">
    <title>Inicio</title>
@endsection

@section('contenido')
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
            --light-bg: #f8f9fa;
        }

        .dashboard-header {
            background: linear-gradient(120deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
            border-radius: 0 0 1rem 1rem;
        }

        .section-title {
            color: var(--secondary-color);
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .link-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .link-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 1rem;
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }

        .link-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            text-decoration: none;
        }

        .link-content {
            display: flex;
            align-items: center;
            color: #333;
        }

        .link-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            border-radius: 8px;
            margin-right: 1rem;
            color: white;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .link-text {
            flex: 1;
        }

        .link-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--secondary-color);
        }

        .link-desc {
            font-size: 0.85rem;
            color: #666;
            margin: 0;
        }

        @media (max-width: 768px) {
            .link-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-header h1 {
                font-size: 1.5rem;
            }

            .dashboard-header p {
                font-size: 1rem;
            }
        }
    </style>

    <!-- Header compacto -->
    <header class="dashboard-header">
        <div class="container">
            <h1 class="h3 fw-bold mb-2">Gestión de Personas y Documentación</h1>
            <p class="mb-0 opacity-90">Administre información personal y documentos en un solo lugar</p>
        </div>
    </header>

    <main class="container">
        @php
            $sections = [
                'Documentación de Incorporación' => [
                    ['route' => 'djbrentas.index', 'icon' => 'fa-file-signature', 'text' => 'DJBRentas', 'desc' => 'Ver y generar DJBRentas'],
                    ['route' => 'afps.index', 'icon' => 'fa-users', 'text' => 'Afps', 'desc' => 'Ver y generar Afps'],
                    ['route' => 'cajacordes.index', 'icon' => 'fa-hospital-o', 'text' => 'Caja Cordes', 'desc' => 'Ver y generar Caja Cordes'],
                    ['route' => 'cenvis.index', 'icon' => 'fa-ban', 'text' => 'Cenvi', 'desc' => 'Certificado de no violencia'],
                    ['route' => 'formularios1.index', 'icon' => 'fa-clipboard', 'text' => 'Formulario 1', 'desc' => 'Curriculum vitae'],
                    ['route' => 'formularios2.index', 'icon' => 'fa-clipboard', 'text' => 'Formulario 2', 'desc' => 'Inventario de personal'],
                    ['route' => 'consanguinidades.index', 'icon' => 'fa-code-branch', 'text' => 'Consanguinidad', 'desc' => 'Declaración consanguinidad'],
                    ['route' => 'compromisos.index', 'icon' => 'fa-handshake-o', 'text' => 'Compromisos', 'desc' => 'Compromisos adquiridos'],
                    ['route' => 'croquis.index', 'icon' => 'fa-lightbulb-o', 'text' => 'Croquis', 'desc' => 'Croquis o dirección'],
                    ['route' => 'cedulas.index', 'icon' => 'fa-id-card', 'text' => 'Carnet de Identidad', 'desc' => 'Carnet de Identidad'],
                    ['route' => 'certificados-nacimiento.index', 'icon' => 'fa-child', 'text' => 'Certificado de Nacimiento', 'desc' => 'Certificado de Nacimiento'],
                    ['route' => 'licencias-militares.index', 'icon' => 'fa-shield', 'text' => 'Licencia Militar', 'desc' => 'Licencia Militar'],
                    ['route' => 'licencias-conducir.index', 'icon' => 'fa-id-card-o', 'text' => 'Licencia de Conducir', 'desc' => 'Licencia de Conducir'],
                ],
                'Documentación Perfil' => [
                    ['route' => 'curriculums.index', 'icon' => 'bi-journal-text', 'text' => 'Currículum', 'desc' => 'Ver y generar currículums'],
                    ['route' => 'bachilleres.index', 'icon' => 'bi-shield-check', 'text' => 'Títulos de Bachiller', 'desc' => 'Ver títulos de bachiller'],
                    ['route' => 'profesion.index', 'icon' => 'fa-certificate', 'text' => 'Profesiones', 'desc' => 'Administrar profesiones'],
                    ['route' => 'certificados.index', 'icon' => 'bi-award', 'text' => 'Certificados', 'desc' => 'Gestión de certificados'],
                    ['route' => 'cas.index', 'icon' => 'bi-file-earmark-text', 'text' => 'CAS', 'desc' => 'Contratos Administrativos'],
                ],
                'Documentación Institucional' => [
                    ['route' => 'cajacordes.index', 'icon' => 'bi-safe', 'text' => 'Memorandums', 'desc' => 'Ver memorandums institucionales'],
                ],
                'Documentos de Desvinculación' => [
                    ['route' => 'cajacordes.index', 'icon' => 'bi-safe', 'text' => 'Renuncias y Agradecimientos', 'desc' => 'Ver renuncias y agradecimientos'],
                    ['route' => 'cas.index', 'icon' => 'bi-handshake', 'text' => 'Solvencia', 'desc' => 'Ver solvencias'],
                ]
            ];
        @endphp

        @foreach($sections as $title => $links)
            <h2 class="section-title">{{ $title }}</h2>
            <div class="link-grid">
                @foreach($links as $link)
                    <a href="{{ route($link['route']) }}" class="link-card">
                        <div class="link-content">
                            <div class="link-icon">
                                <i class="fa {{ $link['icon'] }}"></i>
                            </div>
                            <div class="link-text">
                                <div class="link-title">{{ $link['text'] }}</div>
                                <p class="link-desc">{{ $link['desc'] }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endforeach
    </main>
@endsection
