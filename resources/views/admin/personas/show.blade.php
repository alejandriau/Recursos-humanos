@extends('dashboard')
@section('contenido')

<div class="container-fluid mt-4">
    <!-- Encabezado -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center">
                    <div class="bg-light rounded p-2 me-4">
                        <div class="bg-white rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            @if ($persona->foto)
                                <img src="{{ route('persona.foto', $persona->id) }}" 
                                    alt="Foto de {{ $persona->nombre }}" 
                                    class="rounded-circle shadow-sm cursor-pointer"
                                    data-bs-toggle="modal" data-bs-target="#modalFoto{{ $persona->id }}"
                                    style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="bg-white rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    <span class="fs-4 text-secondary fw-bold">
                                        {{ strtoupper(substr($persona->nombre, 0, 1)) }}{{ strtoupper(substr($persona->apellidoPat ?? '', 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h1 class="h4 mb-1 text-dark">{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</h1>
                        <p class="text-muted mb-2">{{ $historial->puesto->denominacion ?? 'Sin puesto asignado' }}</p>
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="bi bi-circle-fill fs-6 me-1"></i>
                            Activo desde {{ \Carbon\Carbon::parse($persona->fechaIngreso)->format('M Y') }}
                        </span>
                    </div>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i> Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar la foto en grande -->
    @if ($persona->foto)
    <div class="modal fade" id="modalFoto{{ $persona->id }}" tabindex="-1" aria-labelledby="modalFotoLabel{{ $persona->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="modalFotoLabel{{ $persona->id }}">Foto de {{ $persona->nombre }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body d-flex justify-content-center">
                    <img src="{{ route('persona.foto', $persona->id) }}" 
                        alt="Foto grande de {{ $persona->nombre }}" 
                        class="img-fluid rounded shadow-sm" 
                        style="max-height: 600px;">
                </div>
            </div>
        </div>
    </div>
    @endif


    <div class="row">
        <!-- Columna izquierda - Información básica -->
        <div class="col-md-3 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-uppercase fs-6 text-muted mb-3">Información básica</h5>
                    
                    <div class="mb-3">
                        <p class="text-muted small mb-1">CI</p>
                        <p class="mb-0">{{ $persona->ci }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Edad</p>
                        <p class="mb-0">{{ \Carbon\Carbon::parse($persona->fechaNacimiento)->age }} años</p>
                    </div>
                    
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Contacto</p>
                        <p class="mb-0">{{ $persona->telefono ?? '-' }}</p>
                        <p class="mb-0">{{ $persona->email ?? '-' }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Profesión</p>
                        <p class="mb-0">{{ $persona->profesion?->provisionN ?? '-' }}</p>
                    </div>
                    
                    <hr class="my-3">
                    
                    <h5 class="card-title text-uppercase fs-6 text-muted mb-3">Indicadores clave</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Productividad</span>
                            <span>82%</span>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 82%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Asistencia</span>
                            <span>96%</span>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 96%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Evaluación</span>
                            <span>4.2/5</span>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 84%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna central - Situación laboral -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-uppercase fs-6 text-muted mb-3">Situación laboral</h5>
                    
                    @if($historial)
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <p class="text-muted small mb-1">Puesto actual</p>
                            <p class="fw-bold mb-0">{{ $historial->puesto->denominacion }}</p>
                            <p class="small text-muted mt-1">Nivel: {{ $historial->puesto->nivelgerarquico ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted small mb-1">Salario</p>
                            <p class="fw-bold mb-0">{{ number_format($historial->puesto->haber ?? 0, 2) }} Bs.</p>
                            <p class="small text-muted mt-1">Tipo: {{ $historial->tipoContrato ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Estructura</p>
                            <div class="grid grid-cols-1 gap-2 text-sm text-gray-800">
                                <div>
                                    <strong>Ubicación:</strong>
                                    @php
                                        $puesto = $historial->puesto;

                                        $ubicacion = [];

                                        // Área
                                        if ($puesto->area) {
                                            $ubicacion[] = 'Área de ' . $puesto->area->denominacion;

                                            // Área → Unidad
                                            if ($puesto->area->unidad) {
                                                $ubicacion[] = 'Unidad de ' . $puesto->area->unidad->denominacion;

                                                // Unidad → Dirección
                                                if ($puesto->area->unidad->direccion) {
                                                    $ubicacion[] = 'Dirección de ' . $puesto->area->unidad->direccion->denominacion;
                                                    // Dirección → Secretaría
                                                    if ($puesto->area->unidad->direccion->secretaria) {
                                                        $ubicacion[] = 'Secretaría de ' . $puesto->area->unidad->direccion->secretaria->denominacion;
                                                    }
                                                }
                                                // Unidad → Secretaría directa
                                                elseif ($puesto->area->unidad->secretaria) {
                                                    $ubicacion[] = 'Secretaría de ' . $puesto->area->unidad->secretaria->denominacion;
                                                }
                                            }
                                            // Área → Dirección directa
                                            elseif ($puesto->area->direccion) {
                                                $ubicacion[] = 'Dirección de ' . $puesto->area->direccion->denominacion;
                                                if ($puesto->area->direccion->secretaria) {
                                                    $ubicacion[] = 'Secretaría de ' . $puesto->area->direccion->secretaria->denominacion;
                                                }
                                            }
                                            // Área → Secretaría directa
                                            elseif ($puesto->area->secretaria) {
                                                $ubicacion[] = 'Secretaría de ' . $puesto->area->secretaria->denominacion;
                                            }
                                        }

                                        // Si no hay área, buscamos directo en unidad/dirección/secretaría
                                        elseif ($puesto->unidad) {
                                            $ubicacion[] = 'Unidad de ' . $puesto->unidad->denominacion;

                                            if ($puesto->unidad->direccion) {
                                                $ubicacion[] = 'Dirección de ' . $puesto->unidad->direccion->denominacion;
                                                if ($puesto->unidad->direccion->secretaria) {
                                                    $ubicacion[] = 'Secretaría de ' . $puesto->unidad->direccion->secretaria->denominacion;
                                                }
                                            } elseif ($puesto->unidad->secretaria) {
                                                $ubicacion[] = 'Secretaría de ' . $puesto->unidad->secretaria->denominacion;
                                            }
                                        }
                                        elseif ($puesto->direccion) {
                                            $ubicacion[] = 'Dirección de ' . $puesto->direccion->denominacion;
                                            if ($puesto->direccion->secretaria) {
                                                $ubicacion[] = 'Secretaría de ' . $puesto->direccion->secretaria->denominacion;
                                            }
                                        }
                                        elseif ($puesto->secretaria) {
                                            $ubicacion[] = 'Secretaría de ' . $puesto->secretaria->denominacion;
                                        }
                                    @endphp

                                    {{ implode(' → ', $ubicacion) }}
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Jefe directo</p>
                            <p class="mb-0">Lic. María Fernández</p>
                            <p class="small text-muted">Gerente de Área</p>
                        </div>
                    </div>
                    
                    <h6 class="text-uppercase fs-6 text-muted mb-3">Trayectoria en la empresa</h6>
                    
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-point timeline-point-primary"></div>
                            <div class="timeline-event">
                                <div class="timeline-heading">
                                    <h6 class="mb-0">{{ $historial->puesto->denominacion }}</h6>
                                </div>
                                <div class="timeline-body">
                                    <p class="small text-muted mb-0">{{ \Carbon\Carbon::parse($historial->fechaInicio)->format('M Y') }} - Actual</p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-point"></div>
                            <div class="timeline-event">
                                <div class="timeline-heading">
                                    <h6 class="mb-0">Asistente Administrativo</h6>
                                </div>
                                <div class="timeline-body">
                                    <p class="small text-muted mb-0">Ene 2019 - Dic 2021</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="text-uppercase fs-6 text-muted mb-3">Potencial de desarrollo</h6>
                        <div class="alert alert-primary bg-light border-primary border-opacity-25">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold">Preparación para promoción</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary">En 6-12 meses</span>
                            </div>
                            <p class="small mb-2">El empleado muestra habilidades de liderazgo y ha completado el 80% del plan de desarrollo.</p>
                            <div>
                                <span class="badge bg-success bg-opacity-10 text-success me-1">Liderazgo</span>
                                <span class="badge bg-info bg-opacity-10 text-info me-1">Gestión</span>
                                <span class="badge bg-purple bg-opacity-10 text-purple">Estrategia</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                        <h6 class="mt-2 mb-1">Sin asignación actual</h6>
                        <p class="small text-muted">Este colaborador no tiene un puesto asignado actualmente.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna derecha - Análisis RRHH -->
        <div class="col-md-3 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-uppercase fs-6 text-muted mb-3">Análisis RRHH</h5>
                    
                    <div class="mb-4">
                        <h6 class="text-muted small mb-2">Riesgos</h6>
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                Salario en percentil 85 para su puesto
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                Última promoción hace 2.3 años
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                3 ofertas externas en último año
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-muted small mb-2">Oportunidades</h6>
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Completó programa de liderazgo
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Evaluación superior al 90% de su grupo
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Dominio avanzado de inglés
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-muted small mb-2">Recomendaciones</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="small mb-2">Considerar para promoción a puesto de coordinación en los próximos 6 meses.</p>
                            <p class="small mb-0">Priorizar para programa de mentoría ejecutiva.</p>
                        </div>
                    </div>
                    
                    <div>
                        <h6 class="text-muted small mb-2">Acciones</h6>
                        <div class="d-grid gap-2">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-journal-bookmark"></i> Plan de desarrollo
                            </button>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-people"></i> Evaluación 360°
                            </button>
                            <button class="btn btn-sm btn-outline-success">
                                <i class="bi bi-graph-up"></i> Iniciar promoción
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 1rem;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .timeline-point {
        position: absolute;
        left: -8px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #dee2e6;
        z-index: 1;
    }
    .timeline-point-primary {
        background-color: #0d6efd;
    }
    .timeline-event {
        margin-left: 1rem;
    }
    .timeline:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #dee2e6;
    }
    .bg-purple {
        background-color: #6f42c1;
    }
    .text-purple {
        color: #6f42c1;
    }
</style>

@endsection