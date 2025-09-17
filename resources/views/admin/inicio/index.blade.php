@extends('dashboard')

@section('contenidouno')
    <meta content="Lista de personal" name="description">
    <title>Inicio</title>
@endsection
@section('contenido')
<div class="container mt-4">
    <div class="row g-3">
        <!-- Profesiones -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-person-badge fs-2 text-primary me-3"></i>
                        <h5 class="card-title mb-0">Profesiones</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y administrar las profesiones registradas.</p>
                    <a href="{{ route('profesion.index') }}" class="btn btn-outline-primary mt-2">Ver más</a>
                </div>
            </div>
        </div>

        <!-- CAS -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-briefcase fs-2 text-success me-3"></i>
                        <h5 class="card-title mb-0">CAS</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Contratos Administrativos de Servicios.</p>
                    <a href="{{ route('cas.index') }}" class="btn btn-outline-success mt-2">Ver más</a>
                </div>
            </div>
        </div>

        <!-- Certificados -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-award fs-2 text-warning me-3"></i>
                        <h5 class="card-title mb-0">Certificados</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Listado y gestión de certificados.</p>
                    <a href="{{ route('certificados.index') }}" class="btn btn-outline-warning mt-2">Ver más</a>
                </div>
            </div>
        </div>

        <!-- Archivos -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-folder fs-2 text-danger me-3"></i>
                        <h5 class="card-title mb-0">Archivos</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Gestionar documentos y archivos cargados.</p>
                    <a href="" class="btn btn-outline-danger mt-2">Ver más</a>
                </div>
            </div>
        </div>

        <!-- Memorándums -->
        @can('ver personal')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-journal-text fs-2 text-info me-3"></i>
                        <h5 class="card-title mb-0">Memorándums</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar memorándums internos.</p>
                    <a href="" class="btn btn-outline-info mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan

        <!-- Certificados de no violencia -->
        @can('ver cenvis')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-file-earmark-check fs-2 text-purple me-3"></i>
                        <h5 class="card-title mb-0">Certificados de No Violencia</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar certificados de no violencia.</p>
                    <a href="{{ route('cenvis.index') }}" class="btn btn-outline-purple mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan

        <!-- DJBRentas -->
        @can('ver djbrentas')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-receipt fs-2 text-indigo me-3"></i>
                        <h5 class="card-title mb-0">DJBRentas</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar DJBRentas.</p>
                    <a href="{{ route('djbrentas.index') }}" class="btn btn-outline-indigo mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan

        <!-- AFPs -->
        @can('ver afps')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-piggy-bank fs-2 text-teal me-3"></i>
                        <h5 class="card-title mb-0">AFPs</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar AFPs.</p>
                    <a href="{{ route('afps.index') }}" class="btn btn-outline-teal mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan

        <!-- Caja Cordes -->
        @can('ver cajacordes')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-safe fs-2 text-orange me-3"></i>
                        <h5 class="card-title mb-0">Caja Cordes</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar Caja Cordes.</p>
                    <a href="{{ route('cajacordes.index') }}" class="btn btn-outline-orange mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan
        <!-- Compromisos -->
        @can('ver compromisos')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-safe fs-2 text-orange me-3"></i>
                        <h5 class="card-title mb-0">Compromisos</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar Compromisos.</p>
                    <a href="{{ route('compromisos.index') }}" class="btn btn-outline-orange mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan
        <!-- Croquis -->
        @can('ver croquis')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-safe fs-2 text-orange me-3"></i>
                        <h5 class="card-title mb-0">Croquis</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar Croquis.</p>
                    <a href="{{ route('croquis.index') }}" class="btn btn-outline-orange mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan
        @can('ver cedulas')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-safe fs-2 text-orange me-3"></i>
                        <h5 class="card-title mb-0">Cedulas Identidad</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar Cedulas de Identidad.</p>
                    <a href="{{ route('cedulas.index') }}" class="btn btn-outline-orange mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan
        @can('ver certificados nacimiento')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-safe fs-2 text-orange me-3"></i>
                        <h5 class="card-title mb-0">Certificados de Nacimiento</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar Certificados de Nacimiento.</p>
                    <a href="{{ route('certificados-nacimiento.index') }}" class="btn btn-outline-orange mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan
        @can('ver licencias conducir')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-safe fs-2 text-orange me-3"></i>
                        <h5 class="card-title mb-0">Licencias de Conducir</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar Licencias de Conducir.</p>
                    <a href="{{ route('licencias-conducir.index') }}" class="btn btn-outline-orange mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan
        @can('ver licencias militares')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-safe fs-2 text-orange me-3"></i>
                        <h5 class="card-title mb-0">Licencias de Militares</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar Licencias de Militares.</p>
                    <a href="{{ route('licencias-militares.index') }}" class="btn btn-outline-orange mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan
        @can('ver curriculums')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-safe fs-2 text-orange me-3"></i>
                        <h5 class="card-title mb-0">Curriculums</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar Curriculums.</p>
                    <a href="{{ route('curriculums.index') }}" class="btn btn-outline-orange mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan
        @can('ver bachilleres')
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-safe fs-2 text-orange me-3"></i>
                        <h5 class="card-title mb-0">Bachilleres</h5>
                    </div>
                    <p class="card-text text-muted flex-grow-1">Ver y generar Bachilleres.</p>
                    <a href="{{ route('bachilleres.index') }}" class="btn btn-outline-orange mt-2">Ver más</a>
                </div>
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-outline-purple {
        color: #6f42c1;
        border-color: #6f42c1;
    }
    .btn-outline-purple:hover {
        background-color: #6f42c1;
        color: white;
    }
    .btn-outline-indigo {
        color: #6610f2;
        border-color: #6610f2;
    }
    .btn-outline-indigo:hover {
        background-color: #6610f2;
        color: white;
    }
    .btn-outline-teal {
        color: #20c997;
        border-color: #20c997;
    }
    .btn-outline-teal:hover {
        background-color: #20c997;
        color: white;
    }
    .btn-outline-orange {
        color: #fd7e14;
        border-color: #fd7e14;
    }
    .btn-outline-orange:hover {
        background-color: #fd7e14;
        color: white;
    }
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
</style>

@endpush


