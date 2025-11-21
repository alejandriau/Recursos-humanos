@extends('dashboard')

@section('contenido')
    <meta content="Lista de personal" name="description">
    <title>Seleccionar Letra - {{ $titulo }}</title>

    <style>
        .header-institutional {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            border-bottom: 5px solid #f8c300;
        }
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .institutional-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #2c5aa0;
            border: 3px solid #f8c300;
            margin-right: 20px;
        }
        .letra-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
            border-radius: 15px;
        }
        .letra-card:hover {
            transform: translateY(-5px);
            border-color: #2c5aa0;
            box-shadow: 0 8px 25px rgba(44, 90, 160, 0.15);
        }
        .letra-badge {
            font-size: 1.8rem;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 15px auto;
        }
        .pasivo-uno .letra-badge {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%);
            color: white;
        }
        .pasivo-dos .letra-badge {
            background: linear-gradient(135deg, #27ae60 0%, #219653 100%);
            color: white;
        }
        .info-box {
            background: #e8f4f8;
            border-left: 4px solid #2c5aa0;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
    </style>

    <!-- Encabezado institucional -->
    <div class="header-institutional">
        <div class="container">
            <div class="logo-container">
                <div class="institutional-logo">GADC</div>
                <div class="text-center">
                    <h1 class="display-6 fw-bold mb-2">GOBIERNO AUTÓNOMO DEPARTAMENTAL DE COCHABAMBA</h1>
                    <p class="lead mb-0 opacity-90">Unidad de Gestión de Recursos Humanos - UGRH</p>
                    <p class="mb-0 opacity-75">Sistema Integrado de Gestión de Recursos Humanos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold text-primary">{{ $titulo }}</h2>
                <p class="lead text-muted">Seleccione la letra para generar el reporte PDF oficial</p>
            </div>
        </div>

        <!-- Información del sistema -->
        <div class="info-box">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="fw-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Información del Reporte
                    </h5>
                    <p class="mb-2">
                        <strong>Sistema:</strong> SIGRH - GADC |
                        <strong>Fecha:</strong> {{ date('d/m/Y') }} |
                        <strong>Usuario:</strong> Sistema UGRH
                    </p>
                    <p class="mb-0 text-muted">
                        Este reporte es generado automáticamente por el Sistema Integrado de Gestión de Recursos Humanos
                        del Gobierno Autónomo Departamental de Cochabamba.
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-file-excel me-2"></i>
                        <strong>¿Necesita todos los datos?</strong><br>
                        <small>Use la exportación Excel para obtener todas las letras en un solo archivo</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de letras -->
        <div class="row">
            @foreach($letras as $letra)
            <div class="col-6 col-md-3 col-lg-2 mb-4">
                <a href="{{ route($rutaBase, ['letra' => $letra]) }}"
                   class="card letra-card text-decoration-none h-100
                          {{ str_contains($titulo, 'UNO') ? 'pasivo-uno' : 'pasivo-dos' }}">
                    <div class="card-body text-center">
                        <div class="letra-badge">
                            {{ $letra }}
                        </div>
                        <h6 class="card-title text-dark mb-2">Letra {{ $letra }}</h6>
                        <small class="text-muted">Generar PDF</small>
                        <div class="mt-2">
                            <i class="fas fa-file-pdf text-danger"></i>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <!-- Botones de acción -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-2"></i>Exportación Completa
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ str_contains($titulo, 'UNO') ? route('reportes.pasivouno.excel') : route('reportes.pasivodos.excel') }}">
                                    <i class="fas fa-file-excel text-success me-2"></i>Excel con Todas las Letras
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información legal -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="text-center text-muted">
                    <small>
                        <i class="fas fa-shield-alt me-1"></i>
                        Documento oficial del Gobierno Autónomo Departamental de Cochabamba ·
                        Unidad de Gestión de Recursos Humanos ·
                        {{ date('Y') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection
