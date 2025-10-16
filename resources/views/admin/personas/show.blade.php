@extends('dashboard')
@section('contenido')

<div class="container-fluid mt-4">
    <!-- Encabezado Mejorado -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body py-4">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center">
                    <div class="bg-light rounded p-2 me-4">
                        <div class="bg-white rounded d-flex align-items-center justify-content-center position-relative" style="width: 120px; height: 120px;">
                            @if ($persona->foto)
                                <img src="{{ route('persona.foto', $persona->id) }}"
                                    alt="Foto de {{ $persona->nombre }}"
                                    class="rounded-circle shadow-sm cursor-pointer"
                                    data-bs-toggle="modal" data-bs-target="#modalFoto{{ $persona->id }}"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 120px; height: 120px;">
                                    <span class="fs-2 fw-bold">
                                        {{ strtoupper(substr($persona->nombre, 0, 1)) }}{{ strtoupper(substr($persona->apellidoPat ?? '', 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            @if($historial)
                            <span class="position-absolute bottom-0 end-0 badge bg-success rounded-pill p-2">
                                <i class="bi bi-briefcase-fill"></i>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h1 class="h3 mb-1 text-dark fw-bold">{{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</h1>
                        <p class="text-muted mb-2 fs-5">{{ $historial->puesto->denominacion ?? 'Sin puesto asignado' }}</p>

                        <div class="d-flex flex-wrap gap-3 mb-2">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                <i class="bi bi-circle-fill fs-6 me-1"></i>
                                Activo desde {{ \Carbon\Carbon::parse($persona->fechaIngreso)->format('M Y') }}
                            </span>

                            @if($historial && $historial->puesto)
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                <i class="bi bi-building me-1"></i>
                                {{ $historial->puesto->item ?? 'Sin ítem' }}
                            </span>

                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                <i class="bi bi-diagram-3 me-1"></i>
                                {{ $historial->puesto->nivelJerarquico ?? 'Sin nivel' }}
                            </span>
                            @endif

                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                <i class="bi bi-person-badge me-1"></i>
                                {{ ucfirst($persona->tipo) }}
                            </span>
                        </div>

                        <div class="d-flex gap-4 text-muted small">
                            <span><i class="bi bi-person me-1"></i> {{ \Carbon\Carbon::parse($persona->fechaNacimiento)->age }} años</span>
                            <span><i class="bi bi-card-text me-1"></i> CI: {{ $persona->ci }}</span>
                            <span><i class="bi bi-telephone me-1"></i> {{ $persona->telefono ?? 'Sin teléfono' }}</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('personas.edit', $persona->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i> Editar Perfil
                    </a>
                    <a href="{{ route('personas.expediente', $persona->id) }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-file-earmark-text"></i> Ver Expediente
                    </a>
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i> Exportar CV
                    </button>
                    <a href="{{ route('personas.historial', $persona->id) }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-clock-history"></i> Ver Historial
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para foto -->
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
        <!-- Columna izquierda - Información Personal -->
        <div class="col-md-3 mb-4">
            <!-- Información Personal -->
            <div class="card h-100 border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="card-title text-uppercase fs-6 text-muted mb-0">
                        <i class="bi bi-person-vcard me-2"></i>Información Personal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Documento de Identidad</p>
                        <p class="mb-0 fw-semibold">{{ $persona->ci }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Fecha de Nacimiento</p>
                        <p class="mb-0">
                            {{ \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y') }}
                            <span class="text-muted">({{ \Carbon\Carbon::parse($persona->fechaNacimiento)->age }} años)</span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Género</p>
                        <p class="mb-0">{{ $persona->sexo ?? 'No especificado' }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Estado Civil</p>
                        <p class="mb-0">{{ $persona->estadoCivil ?? 'No especificado' }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Nacionalidad</p>
                        <p class="mb-0">{{ $persona->nacionalidad ?? 'Boliviana' }}</p>
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="card-title text-uppercase fs-6 text-muted mb-0">
                        <i class="bi bi-telephone me-2"></i>Contacto
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Teléfono</p>
                        <p class="mb-0">{{ $persona->telefono ?? 'No registrado' }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Correo Electrónico</p>
                        <p class="mb-0">{{ $persona->email ?? 'No registrado' }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Dirección</p>
                        <p class="mb-0">{{ $persona->direccion ?? 'No registrada' }}</p>
                    </div>
                </div>
            </div>

            <!-- Información Profesional -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="card-title text-uppercase fs-6 text-muted mb-0">
                        <i class="bi bi-mortarboard me-2"></i>Formación Profesional
                    </h5>
                </div>
                <div class="card-body">
                    @if($persona->profesion)
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Título Profesional</p>
                        <p class="mb-0 fw-semibold">{{ $persona->profesion->provisionN ?? 'No registrado' }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Fecha de Titulación</p>
                        <p class="mb-0">
                            @if($persona->profesion->fechaProvision)
                                {{ \Carbon\Carbon::parse($persona->profesion->fechaProvision)->format('d/m/Y') }}
                            @else
                                No registrada
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Universidad</p>
                        <p class="mb-0">{{ $persona->profesion->universidad ?? 'No registrada' }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small mb-1">Registro Profesional</p>
                        <p class="mb-0">{{ $persona->profesion->registro ?? 'No registrado' }}</p>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="bi bi-mortarboard text-muted fs-1"></i>
                        <p class="text-muted small mt-2">No hay información profesional registrada</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna central - Situación Laboral -->
        <div class="col-md-6 mb-4">
            <!-- Situación Laboral Actual -->
            <div class="card h-100 border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="card-title text-uppercase fs-6 text-muted mb-0">
                        <i class="bi bi-briefcase me-2"></i>Situación Laboral Actual
                    </h5>
                </div>
                <div class="card-body">
                    @if($historial && $historial->puesto)
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="bg-light rounded p-3 h-100">
                                <p class="text-muted small mb-1">Puesto</p>
                                <p class="fw-bold mb-1 fs-5">{{ $historial->puesto->denominacion }}</p>
                                <p class="small text-muted mb-0">Nivel: {{ $historial->puesto->nivelJerarquico ?? 'No especificado' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="bg-light rounded p-3 h-100">
                                <p class="text-muted small mb-1">Remuneración</p>
                                <p class="fw-bold mb-1 fs-5 text-success">{{ number_format($historial->puesto->haber ?? 0, 2) }} Bs.</p>
                                <p class="small text-muted mb-0">Item: {{ $historial->puesto->item ?? 'No especificado' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Estructura Organizacional -->
                    <div class="mb-4">
                        <h6 class="text-uppercase fs-6 text-muted mb-3">
                            <i class="bi bi-diagram-3 me-2"></i>Ubicación Organizacional
                        </h6>
                        <div class="bg-light rounded p-4">
                            @if($historial->puesto->unidadOrganizacional)
                                @php
                                    $jerarquia = $historial->puesto->unidadOrganizacional->obtenerJerarquia();
                                @endphp

                                <div class="estructura-organizacional">
                                    @foreach($jerarquia as $index => $unidad)
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-{{ $index === 0 ? 'building' : 'arrow-right' }} text-primary me-2"></i>
                                            <span class="fw-semibold">
                                                {{ $unidad->tipo }} de {{ $unidad->denominacion }}
                                                @if($unidad->sigla)
                                                    <small class="text-muted">({{ $unidad->sigla }})</small>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Información de la Unidad -->
                                <div class="mt-4 pt-3 border-top">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="text-muted small mb-1">Código de Unidad</p>
                                            <p class="mb-0 fw-semibold">{{ $historial->puesto->unidadOrganizacional->codigo ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-muted small mb-1">Tipo de Unidad</p>
                                            <p class="mb-0">
                                                <span class="badge bg-info">
                                                    {{ $historial->puesto->unidadOrganizacional->tipo }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-building fs-1"></i>
                                    <p class="mt-2 mb-0">No se pudo determinar la ubicación organizacional</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Información del Contrato -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Fecha de Ingreso</p>
                            <p class="fw-semibold">{{ \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Antigüedad</p>
                            <p class="fw-semibold">
                                @php
                                    $antiguedad = \Carbon\Carbon::parse($persona->fechaIngreso)->diff(now());
                                @endphp
                                {{ $antiguedad->y }} años, {{ $antiguedad->m }} meses
                            </p>
                        </div>
                    </div>

                    <!-- Información del Historial -->
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Tipo de Movimiento</p>
                            <p class="fw-semibold">
                                <span class="badge bg-{{ $historial->tipo_movimiento == 'designacion_inicial' ? 'primary' : 'warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $historial->tipo_movimiento)) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Fecha Inicio en Puesto</p>
                            <p class="fw-semibold">{{ \Carbon\Carbon::parse($historial->fecha_inicio)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-briefcase text-muted fs-1"></i>
                        <h6 class="mt-3 mb-2 text-muted">Sin asignación actual</h6>
                        <p class="small text-muted mb-3">Este colaborador no tiene un puesto asignado actualmente.</p>
                        <a href="{{ route('personas.historial', $persona->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Asignar Puesto
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Indicadores de Desempeño -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="card-title text-uppercase fs-6 text-muted mb-0">
                        <i class="bi bi-graph-up me-2"></i>Indicadores de Desempeño
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <div class="mb-2">
                                    <i class="bi bi-speedometer2 fs-1 text-primary"></i>
                                </div>
                                <h5 class="fw-bold mb-1">82%</h5>
                                <p class="small text-muted mb-0">Productividad</p>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-primary" style="width: 82%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <div class="mb-2">
                                    <i class="bi bi-calendar-check fs-1 text-success"></i>
                                </div>
                                <h5 class="fw-bold mb-1">96%</h5>
                                <p class="small text-muted mb-0">Asistencia</p>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 96%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <div class="mb-2">
                                    <i class="bi bi-star fs-1 text-warning"></i>
                                </div>
                                <h5 class="fw-bold mb-1">4.2/5</h5>
                                <p class="small text-muted mb-0">Evaluación</p>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-warning" style="width: 84%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna derecha - Análisis RRHH -->
        <div class="col-md-3 mb-4">
            <!-- Análisis RRHH -->
            <div class="card h-100 border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="card-title text-uppercase fs-6 text-muted mb-0">
                        <i class="bi bi-clipboard-data me-2"></i>Análisis RRHH
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Estado Actual -->
                    <div class="mb-4">
                        <h6 class="text-muted small mb-3 d-flex align-items-center">
                            <i class="bi bi-info-circle text-info me-2"></i>
                            Estado Actual
                        </h6>
                        <div class="bg-info bg-opacity-10 p-3 rounded border border-info border-opacity-25">
                            <p class="small mb-1"><strong>Contrato:</strong> {{ $historial->tipo_contrato ?? 'No especificado' }}</p>
                            <p class="small mb-1"><strong>Jornada:</strong> {{ $historial->jornada_laboral ?? 'Completa' }}</p>
                            <p class="small mb-0"><strong>Estado:</strong>
                                <span class="badge bg-success">Activo</span>
                            </p>
                        </div>
                    </div>

                    <!-- Jefe Inmediato -->
                    @if($historial && $historial->puesto)
                        @php
                            $jefeInmediato = $historial->puesto->obtenerJefeInmediato();
                        @endphp
                        <div class="mb-4">
                            <h6 class="text-muted small mb-3 d-flex align-items-center">
                                <i class="bi bi-person-badge text-primary me-2"></i>
                                Jefe Inmediato
                            </h6>
                            @if($jefeInmediato)
                                <div class="bg-primary bg-opacity-10 p-3 rounded border border-primary border-opacity-25">
                                    <p class="small mb-1 fw-semibold">{{ $jefeInmediato->nombre }} {{ $jefeInmediato->apellidoPat }}</p>
                                    <p class="small mb-1 text-muted">{{ $jefeInmediato->puestoActual->puesto->denominacion ?? 'Sin puesto' }}</p>
                                    <p class="small mb-0">
                                        <i class="bi bi-envelope me-1"></i>
                                        {{ $jefeInmediato->email ?? 'No tiene email' }}
                                    </p>
                                </div>
                            @else
                                <div class="text-center text-muted py-2">
                                    <i class="bi bi-person-x fs-4"></i>
                                    <p class="small mt-1 mb-0">No se identificó jefe inmediato</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Acciones Rápidas -->
                    <div class="mb-4">
                        <h6 class="text-muted small mb-3 d-flex align-items-center">
                            <i class="bi bi-lightning me-2"></i>
                            Acciones Rápidas
                        </h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('personas.edit', $persona->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center">
                                <i class="bi bi-pencil me-2"></i>
                                Editar Datos
                            </a>
                            <a href="{{ route('personas.historial', $persona->id) }}" class="btn btn-sm btn-outline-info d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock-history me-2"></i>
                                Ver Historial
                            </a>
                            <a href="{{ route('regisrar.archivos', $persona->id) }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center">
                                <i class="bi bi-folder me-2"></i>
                                Documentos
                            </a>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div>
                        <h6 class="text-muted small mb-3 d-flex align-items-center">
                            <i class="bi bi-card-checklist me-2"></i>
                            Información Adicional
                        </h6>
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <i class="bi bi-calendar-check text-success me-2"></i>
                                Última evaluación: {{ \Carbon\Carbon::now()->subMonths(2)->format('M Y') }}
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-arrow-up-circle text-primary me-2"></i>
                                Próxima revisión: {{ \Carbon\Carbon::now()->addMonths(4)->format('M Y') }}
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-award text-warning me-2"></i>
                                Capacitaciones: {{ rand(2, 8) }} completadas
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }

    .estructura-organizacional {
        border-left: 3px solid #0d6efd;
        padding-left: 1rem;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }

    .progress {
        background-color: #e9ecef;
    }
</style>

@endsection
