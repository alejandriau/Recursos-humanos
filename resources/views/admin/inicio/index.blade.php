@extends('dashboard')

@section('contenidouno')
    <meta content="Lista de personal" name="description">
    <title>Inicio</title>
@endsection

@section('contenido')
    <style>        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
            --light-bg: #f8f9fa;
            --card-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 2rem;
        }

        .dashboard-header {
            background: linear-gradient(120deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 1rem 1rem;
        }

        .dashboard-card {
            border: none;
            border-radius: 0.8rem;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            margin-bottom: 1rem;
        }

        .card-icon i {
            font-size: 1.8rem;
            color: white;
        }

        .card-title {
            font-weight: 600;
            color: var(--secondary-color);
        }

        .card-text {
            color: #6c757d;
            min-height: 48px;
        }

        .btn-dashboard {
            background-color: var(--primary-color);
            border: none;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            transition: var(--transition);
        }

        .btn-dashboard:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .section-title {
            color: var(--secondary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent-color);
        }

        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 1.8rem;
            }
        }</style>
            <!-- Header del Dashboard -->
    <header class="dashboard-header text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Sistema de Gestión Integral</h1>
            <p class="lead">Administre todos los procesos desde un solo lugar</p>
        </div>
    </header>
        <main class="container">
        <!-- Sección de Gestión de Personal -->
        <h2 class="section-title">Documentación de Incorporación</h2>
        <div class="row g-4 mb-5">
            <!-- DJBRentas -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-file-signature me-2"></i>
                        </div>
                        <h5 class="card-title">DJBRentas</h5>
                        <p class="card-text">Ver y generar DJBRentas.</p>
                        <a href="{{route('djbrentas.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- Afps -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-users me-2"></i>
                        </div>
                        <h5 class="card-title">Afps</h5>
                        <p class="card-text">Ver y generar Afps.</p>
                        <a href="{{route('afps.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- Caja Cordes -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-hospital-o me-2"></i>
                        </div>
                        <h5 class="card-title">Caja Cordes</h5>
                        <p class="card-text">Ver y generar Caja Cordes.</p>
                        <a href="{{route('cajacordes.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- Cenvi -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-ban me-2"></i>
                        </div>
                        <h5 class="card-title">Cenvi</h5>
                        <p class="card-text">Ver y generar Certificado de no violencia.</p>
                        <a href="{{route('cenvis.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- Formulario 1 -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-clipboard me-2"></i>
                        </div>
                        <h5 class="card-title">formulario 1</h5>
                        <p class="card-text">Curriculum vitae</p>
                        <a href="{{route('formularios1.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- formulario 2 -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-clipboard me-2"></i>
                        </div>
                        <h5 class="card-title">formulario 2</h5>
                        <p class="card-text">Inventario de personal</p>
                        <a href="{{route('formularios2.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- Consanguinidad -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-code-branch me-2"></i>
                        </div>
                        <h5 class="card-title">Consanguinidad</h5>
                        <p class="card-text">Declaración consanguinidad</p>
                        <a href="{{route('consanguinidades.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- Compromisos -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-handshake-o me-2"></i>
                        </div>
                        <h5 class="card-title">Compromisos</h5>
                        <p class="card-text">Compromisos adquiridos</p>
                        <a href="{{route('compromisos.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- Croquis -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-lightbulb-o me-2"></i>
                        </div>
                        <h5 class="card-title">Croquis</h5>
                        <p class="card-text">Croquis o direccion</p>
                        <a href="{{route('croquis.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- cedula identidad -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-id-card me-2"></i>
                        </div>
                        <h5 class="card-title">Carnet de Identidad</h5>
                        <p class="card-text">Carnet de Identidad</p>
                        <a href="{{route('cedulas.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- Certificado de Nacimiento -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-child me-2"></i>
                        </div>
                        <h5 class="card-title">Certificado de Nacimiento</h5>
                        <p class="card-text">Certificado de Nacimiento</p>
                        <a href="{{route('certificados-nacimiento.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- licencia militar -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-shield me-2"></i>
                        </div>
                        <h5 class="card-title">Licencia Militar</h5>
                        <p class="card-text">Licencia Militar</p>
                        <a href="{{route('licencias-militares.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- licencia conducir -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-id-card-o me-2"></i>
                        </div>
                        <h5 class="card-title">Licencia de Conducir</h5>
                        <p class="card-text">Licencia de Conducir</p>
                        <a href="{{route('licencias-conducir.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>





        <!-- Sección de Documentación -->
        <h2 class="section-title">Documentación perfil</h2>
        <div class="row g-4 mb-5">
            <!-- currículum -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <h5 class="card-title">Currículum</h5>
                        <p class="card-text">Ver y generar currículums hoja de vida.</p>
                        <a href="{{route('curriculums.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>

            <!-- títulos bachiller -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h5 class="card-title">Títulos de Bachiller</h5>
                        <p class="card-text">Ver títulos de bachiller.</p>
                        <a href="{{route('bachilleres.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>

            <!-- Profesiones -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="fa fa-certificate me-2"></i>
                        </div>
                        <h5 class="card-title">Profesiones</h5>
                        <p class="card-text">Ver y administrar las profesiones registradas.</p>
                        <a href="{{route('profesion.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- Certificados -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="bi bi-award"></i>
                        </div>
                        <h5 class="card-title">Certificados</h5>
                        <p class="card-text">Listado y gestión de certificados.</p>
                        <a href="{{route('certificados.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>
            <!-- CAS -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h5 class="card-title">CAS</h5>
                        <p class="card-text">Contratos Administrativos de Servicios.</p>
                        <a href="{{route('cas.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>


        <!-- Sección de Gestión Adicional -->
        <h2 class="section-title">Documentación de generados en la institución</h2>
        <div class="row g-4">
            <!-- Caja Cordes -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="bi bi-safe"></i>
                        </div>
                        <h5 class="card-title">memorandums</h5>
                        <p class="card-text">Ver memorandums.</p>
                        <a href="{{route('cajacordes.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>

            <!-- Cas
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="bi bi-handshake"></i>
                        </div>
                        <h5 class="card-title">Cas</h5>
                        <p class="card-text">Ver Cas.</p>
                        <a href="{{route('cas.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div> -->

        </div>
        <!-- Sección de Gestión Adicional -->
        <h2 class="section-title">Documentos de desvinculación</h2>
        <div class="row g-4">
            <!-- Caja Cordes -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="bi bi-safe"></i>
                        </div>
                        <h5 class="card-title">renuncias y agradecimientos</h5>
                        <p class="card-text">Ver renuncias y agradecimientos.</p>
                        <a href="{{route('cajacordes.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>

            <!-- Cas -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon mx-auto">
                            <i class="bi bi-handshake"></i>
                        </div>
                        <h5 class="card-title">Solvencia</h5>
                        <p class="card-text">Ver Solvencias.</p>
                        <a href="{{route('cas.index')}}" class="btn btn-dashboard">Ver más</a>
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection



