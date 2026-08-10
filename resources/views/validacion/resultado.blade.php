@extends('layouts.baseadm')

@section('title', 'Resultado de Validación')
@section('header', 'Resultado de Validación de Perfil')

@section('contenido')
<div class="container-fluid fade-in">
    <div class="row">
        <div class="col-md-12">
            <!-- Tarjeta de Resultado Global -->
            <div class="card shadow-sm mb-4">
                <div class="card-header {{ $resultado['cumple'] ? 'bg-success' : 'bg-danger' }} text-white">
                    <h3 class="mb-0">
                        <i class="fas {{ $resultado['cumple'] ? 'fa-check-circle' : 'fa-times-circle' }} me-2"></i>
                        {{ $resultado['mensaje'] }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box p-3 border rounded">
                                <h5><i class="fas fa-briefcase text-primary me-2"></i>Información del Puesto</h5>
                                <hr>
                                <p><strong>Denominación:</strong> {{ $puesto->denominacion }}</p>
                                <p><strong>Nivel Jerárquico:</strong> {{ $puesto->nivelJerarquico }}</p>
                                <p><strong>Unidad:</strong> {{ $puesto->unidadOrganizacional->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box p-3 border rounded">
                                <h5><i class="fas fa-user text-primary me-2"></i>Información del Candidato</h5>
                                <hr>
                                <p><strong>Nombre:</strong> {{ $persona->nombre_completo }}</p>
                                @if($persona->profesionPrincipal)
                                    <p><strong>Profesión:</strong> {{ $persona->profesionPrincipal->nombreCompletoTitulo }}</p>
                                    <p><strong>Fecha Titulación:</strong> {{ $persona->profesionPrincipal->fechaTitulo ? \Carbon\Carbon::parse($persona->profesionPrincipal->fechaTitulo)->format('d/m/Y') : 'No registrada' }}</p>
                                    <p><strong>Experiencia:</strong> {{ number_format($persona->profesionPrincipal->aniosExperienciaDesdeTitulacion, 1) }} años</p>
                                @else
                                    <p class="text-warning">No tiene profesión principal registrada</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Detalle de Validación por Requisito -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Detalle de Validación por Requisito</h5>
                </div>
                <div class="card-body">
                    @if(isset($resultado['detalle']))
                        <!-- Validación de Formación -->
                        @if(isset($resultado['detalle']['formacion']))
                        <div class="requisito-item {{ $resultado['detalle']['formacion']['cumple'] ? 'requisito-cumple' : 'requisito-no-cumple' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="fas fa-graduation-cap me-2"></i>
                                        Formación Académica
                                    </h6>
                                    <p class="mb-1 text-muted">
                                        {{ $resultado['detalle']['formacion']['detalle']['carrera'] ?? 'N/A' }}
                                    </p>
                                    <div class="small">
                                        <span class="badge bg-secondary">Nivel: {{ $resultado['detalle']['formacion']['detalle']['nivel_academico'] ?? 'N/A' }}</span>
                                        <span class="badge bg-secondary ms-1">Área: {{ $resultado['detalle']['formacion']['detalle']['area_conocimiento'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div>
                                    @if($resultado['detalle']['formacion']['cumple'])
                                        <span class="badge-cumple"><i class="fas fa-check me-1"></i>CUMPLE</span>
                                    @else
                                        <span class="badge-no-cumple"><i class="fas fa-times me-1"></i>NO CUMPLE</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Validación de Experiencia -->
                        @if(isset($resultado['detalle']['experiencia']))
                        <div class="requisito-item {{ $resultado['detalle']['experiencia']['cumple'] ? 'requisito-cumple' : 'requisito-no-cumple' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="fas fa-clock me-2"></i>
                                        Experiencia Profesional
                                    </h6>
                                    @if(is_array($resultado['detalle']['experiencia']['detalle']))
                                        <p class="mb-0">
                                            <strong>Fecha de Titulación:</strong> {{ $resultado['detalle']['experiencia']['detalle']['fecha_titulacion'] ?? 'N/A' }}<br>
                                            <strong>Experiencia:</strong> {{ $resultado['detalle']['experiencia']['detalle']['anios_experiencia'] ?? 0 }} años 
                                            ({{ $resultado['detalle']['experiencia']['detalle']['meses_experiencia'] ?? 0 }} meses)<br>
                                            <strong>Requerido:</strong> {{ $resultado['detalle']['experiencia']['detalle']['anios_requeridos'] ?? 0 }} años
                                        </p>
                                    @else
                                        <p class="mb-0">{{ $resultado['detalle']['experiencia']['detalle'] }}</p>
                                    @endif
                                </div>
                                <div>
                                    @if($resultado['detalle']['experiencia']['cumple'])
                                        <span class="badge-cumple"><i class="fas fa-check me-1"></i>CUMPLE</span>
                                    @else
                                        <span class="badge-no-cumple"><i class="fas fa-times me-1"></i>NO CUMPLE</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Validación de Título en Provisión Nacional -->
                        @if(isset($resultado['detalle']['titulo_provision']))
                        <div class="requisito-item {{ $resultado['detalle']['titulo_provision']['cumple'] ? 'requisito-cumple' : 'requisito-no-cumple' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="fas fa-file-certificate me-2"></i>
                                        Título en Provisión Nacional
                                    </h6>
                                    <p class="mb-0">{{ $resultado['detalle']['titulo_provision']['detalle'] }}</p>
                                </div>
                                <div>
                                    @if($resultado['detalle']['titulo_provision']['cumple'])
                                        <span class="badge-cumple"><i class="fas fa-check me-1"></i>CUMPLE</span>
                                    @else
                                        <span class="badge-no-cumple"><i class="fas fa-times me-1"></i>NO CUMPLE</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <p>No hay detalle de validación disponible</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('validacion.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Nueva Validación
                        </a>
                        <button onclick="window.print()" class="btn btn-info">
                            <i class="fas fa-print me-1"></i> Imprimir Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .sidebar, .navbar, .card-footer, .btn {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection