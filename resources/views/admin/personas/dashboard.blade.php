@extends('dashboard')

@section('title', 'Dashboard - ' . $persona->nombre)

@section('contenido')
<div class="container-fluid">
    <!-- Header Compacto -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>{{ $persona->nombre }}
                                <span class="badge bg-{{ $persona->estado ? 'success' : 'danger' }} ms-2">
                                    {{ $persona->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </h5>
                            <small class="text-muted">ID: {{ $persona->id }} | Registro: {{ $persona->fechaRegistro->format('d/m/Y') }}</small>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('personas.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Documentos en Columnas -->
    <div class="row">

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-file-contract text-primary me-2"></i>
                        DJB Renta
                        <span class="badge bg-primary">{{ $persona->djbRenta->count() }}</span>
                    </h6>
                    <div>
                        @can('crear djbrentas')
                            <a href="{{ route('djbrentas.create', ['from_show' => 1, 'persona_id' => $persona->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Agregar DJBRenta
                            </a>


                        @endcan
                    </div>
                </div>
                <div class="card-body p-3">
                    @if($persona->djbRenta->count() > 0)
                    <div class="documentos-list">
                        @foreach($persona->djbRenta as $djbrenta)
                        <div class="documento-item mb-3">
                            <div class="card border card-hover">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-{{ $djbrenta->estado ? 'success' : 'danger' }} status-badge">
                                            {{ $djbrenta->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                        <div class="btn-group action-buttons">
                                            @can('ver djbrentas')
                                            <a href="{{ route('djbrentas.show', $djbrenta) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('editar djbrentas')
                                            <a href="{{ route('djbrentas.edit', $djbrenta) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        </div>
                                    </div>


                                    <div class="info-item mb-2">
                                        <div class="info-label">Fecha:</div>
                                        <div class="info-value">{{ $djbrenta->fecha->format('d/m/Y') }}</div>
                                    </div>

                                    <div class="info-item mb-2">
                                        <div class="info-label">Tipo:</div>
                                        <div class="info-value">{{ $djbrenta->tipo ?? 'Sin tipo' }}</div>
                                    </div>

                                    @if($djbrenta->pdfrenta)
                                    <div class="info-item mb-2">
                                        <div class="info-label">Archivo:</div>
                                        <div class="info-value">
                                            <a href="{{ route('djbrentas.download', $djbrenta) }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-download"></i> Descargar PDF
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state text-center py-4">
                        <i class="fas fa-file-contract fa-2x text-muted mb-2"></i>
                        <p class="mb-0 text-muted">No hay registros de DJB Renta</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Afps-->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-piggy-bank text-primary me-2"></i>
                        AFP
                        <span class="badge bg-primary">{{ $persona->afps->count() }}</span>
                    </h6>
                    <div>
                        @can('crear afps')
                        <a href="{{ route('afps.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar AFP">
                            <i class="fas fa-plus"></i> Agregar
                        </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body p-3">
                    @if($persona->afps->count() > 0)
                    <div class="documentos-list">
                        @foreach($persona->afps as $afp)
                        <div class="documento-item mb-3">
                            <div class="card border card-hover">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-{{ $afp->estado ? 'success' : 'danger' }} status-badge">
                                            {{ $afp->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                        <div class="btn-group action-buttons">
                                            @can('ver afps')
                                            <a href="{{ route('afps.show', $afp) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('editar afps')
                                            <a href="{{ route('afps.edit', $afp) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @if($afp->pdfafps)
                                            <a href="{{ route('afps.download', $afp) }}" class="btn btn-sm btn-outline-success" title="Descargar PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="info-item mb-2">
                                        <div class="info-label">CUA:</div>
                                        <div class="info-value">{{ $afp->cua }}</div>
                                    </div>

                                    <div class="info-item mb-2">
                                        <div class="info-label">Registro:</div>
                                        <div class="info-value text-muted small">
                                            {{ $afp->fechaRegistro->format('d/m/Y H:i') }}
                                        </div>
                                    </div>

                                    @if($afp->observacion)
                                    <div class="info-item mb-2">
                                        <div class="info-label">Observación:</div>
                                        <div class="info-value text-muted small">{{ $afp->observacion }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state text-center py-4">
                        <i class="fas fa-piggy-bank fa-2x text-muted mb-2"></i>
                        <p class="mb-0 text-muted">No hay registros de AFP</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Caja cordes -->

<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-box text-primary me-2"></i>
                Cajas de Cordes
                <span class="badge bg-primary">{{ $persona->cajacordes->count() }}</span>
            </h6>
            <div>
                @can('crear cajacordes')
                <a href="{{ route('cajacordes.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Caja">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($persona->cajacordes->count() > 0)
            <div class="documentos-list">
                @foreach($persona->cajacordes as $cajacorde)
                <div class="documento-item mb-3">
                    <div class="card border card-hover">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $cajacorde->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $cajacorde->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver cajacordes')
                                    <a href="{{ route('cajacordes.show', $cajacorde) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar cajacordes')
                                    <a href="{{ route('cajacordes.edit', $cajacorde) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @if($cajacorde->pdfcaja)
                                    <a href="{{ route('cajacordes.download', $cajacorde) }}" class="btn btn-sm btn-outline-success" title="Descargar PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Fecha:</div>
                                <div class="info-value">{{ $cajacorde->fecha->format('d/m/Y') }}</div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Código:</div>
                                <div class="info-value">{{ $cajacorde->codigo ?? 'N/A' }}</div>
                            </div>

                            @if($cajacorde->otros)
                            <div class="info-item mb-2">
                                <div class="info-label">Otros:</div>
                                <div class="info-value text-muted small">{{ $cajacorde->otros }}</div>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-box fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay registros de Caja de Cordes</p>
            </div>
            @endif
        </div>
    </div>
</div>

        <!-- Certificado no violencia -->
<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-file-alt text-primary me-2"></i>
                CENVI
                <span class="badge bg-primary">{{ $persona->cenvis->count() }}</span>
            </h6>
            <div>
                @can('crear cenvis')
                <a href="{{ route('cenvis.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar CENVI">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($persona->cenvis->count() > 0)
            <div class="documentos-list">
                @foreach($persona->cenvis as $cenvi)
                    @php
                        $fechaInicio = $cenvi->fecha;
                        $fechaVencimiento = $fechaInicio->copy()->addYear();
                        $hoy = now();
                        $estaVencido = $hoy->gt($fechaVencimiento);
                        $diasRestantes = $hoy->diffInDays($fechaVencimiento, false);
                    @endphp

                <div class="documento-item mb-3">
                    <div class="card border card-hover {{ $estaVencido ? 'border-danger' : 'border-success' }}">
                        <div class="card-body p-3">
                            <!-- Alert de vencimiento -->
                            @if($estaVencido)
                            <div class="alert alert-danger py-2 mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>VENCIDO</strong> - Venció hace {{ abs($diasRestantes) }} días
                            </div>
                            @else
                            <div class="alert alert-success py-2 mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>VIGENTE</strong> - Vence en {{ $diasRestantes }} días
                            </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $cenvi->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $cenvi->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver cenvis')
                                    <a href="{{ route('cenvis.show', $cenvi) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar cenvis')
                                    <a href="{{ route('cenvis.edit', $cenvi) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @if($cenvi->pdfcenvi)
                                    <a href="{{ route('cenvis.download', $cenvi) }}" class="btn btn-sm btn-outline-success" title="Descargar PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Fecha Emisión:</div>
                                <div class="info-value">{{ $cenvi->fecha->format('d/m/Y') }}</div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Fecha Vencimiento:</div>
                                <div class="info-value {{ $estaVencido ? 'text-danger fw-bold' : '' }}">
                                    {{ $fechaVencimiento->format('d/m/Y') }}
                                    @if($estaVencido)
                                    <i class="fas fa-exclamation-circle text-danger ms-1"></i>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Estado Vigencia:</div>
                                <div class="info-value">
                                    <span class="badge bg-{{ $estaVencido ? 'danger' : 'success' }}">
                                        {{ $estaVencido ? 'Vencido' : 'Vigente' }}
                                    </span>
                                </div>
                            </div>

                            @if($cenvi->observacion)
                            <div class="info-item mb-2">
                                <div class="info-label">Observación:</div>
                                <div class="info-value text-muted small">{{ $cenvi->observacion }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-file-alt fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay registros de CENVI</p>
                @can('crear cenvis')
                <a href="{{ route('cenvis.create', $persona->id) }}" class="btn btn-primary btn-sm mt-2">
                    <i class="fas fa-plus"></i> Crear Registro
                </a>
                @endcan
            </div>
            @endif
        </div>
    </div>
</div>
<!-- Formularios 1 -->

<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-file-invoice text-primary me-2"></i>
                Formularios 1
                <span class="badge bg-primary">{{ $persona->formularios1->count() }}</span>
            </h6>
            <div>
                @can('crear formularios1')
                <a href="{{ route('formularios1.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Formulario 1">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($persona->formularios1->count() > 0)
            <div class="documentos-list">
                @foreach($persona->formularios1 as $formulario1)
                    @php
                        $fechaFormulario = $formulario1->fecha;
                        $fechaVencimiento = $fechaFormulario ? $fechaFormulario->copy()->addYears(5) : null;
                        $hoy = now();
                        $estaVencido = $fechaVencimiento ? $hoy->gt($fechaVencimiento) : false;
                        $aniosDesdeFecha = $fechaFormulario ? $hoy->diffInYears($fechaFormulario) : null;
                        $esReciente = $fechaFormulario ? $hoy->diffInYears($fechaFormulario) <= 2 : false;
                    @endphp

                <div class="documento-item mb-3">
                    <div class="card border card-hover {{ $estaVencido ? 'border-danger' : 'border-success' }}">
                        <div class="card-body p-3">
                            <!-- Alert de vencimiento -->
                            @if($fechaFormulario)
                                @if($estaVencido)
                                <div class="alert alert-danger py-2 mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>VENCIDO</strong> - Excede los 5 años
                                </div>
                                @else
                                <div class="alert alert-success py-2 mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>VIGENTE</strong>
                                </div>
                                @endif
                            @endif

                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $formulario1->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $formulario1->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver formularios1')
                                    <a href="{{ route('formularios1.show', $formulario1) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar formularios1')
                                    <a href="{{ route('formularios1.edit', $formulario1) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @if($formulario1->pdfform1)
                                    <a href="{{ route('formularios1.download', $formulario1) }}" class="btn btn-sm btn-outline-success" title="Descargar PDF" target="_blank">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>


                            @if($formulario1->fecha)
                            <div class="info-item mb-2">
                                <div class="info-label">Fecha:</div>
                                <div class="info-value">
                                    {{ $formulario1->fecha->format('d/m/Y') }}
                                    @if($esReciente)
                                    <span class="badge bg-info ms-1">Reciente</span>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Antigüedad:</div>
                                <div class="info-value text-muted small">
                                    Hace {{ $aniosDesdeFecha }} años
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Estado:</div>
                                <div class="info-value">
                                    <span class="badge bg-{{ $estaVencido ? 'danger' : 'success' }}">
                                        {{ $estaVencido ? 'Por actualizar' : 'Vigente' }}
                                    </span>
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-file-invoice fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay registros de Formulario 1</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- formulario 2 -->

<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-file-invoice text-primary me-2"></i>
                Formularios 2
                <span class="badge bg-primary">{{ $persona->formularios2->count() }}</span>
            </h6>
            <div>
                @can('crear formularios2')
                <a href="{{ route('formularios2.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Formulario 2">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($persona->formularios2->count() > 0)
            <div class="documentos-list">
                @foreach($persona->formularios2 as $formulario2)
                    @php
                        $fechaFormulario = $formulario2->fecha;
                        $fechaVencimiento = $fechaFormulario ? $fechaFormulario->copy()->addYears(5) : null;
                        $hoy = now();
                        $estaVencido = $fechaVencimiento ? $hoy->gt($fechaVencimiento) : false;
                        $aniosDesdeFecha = $fechaFormulario ? $hoy->diffInYears($fechaFormulario) : null;
                        $esReciente = $fechaFormulario ? $hoy->diffInYears($fechaFormulario) <= 2 : false;
                    @endphp

                <div class="documento-item mb-3">
                    <div class="card border card-hover {{ $estaVencido ? 'border-danger' : 'border-success' }}">
                        <div class="card-body p-3">
                            <!-- Alert de vencimiento -->
                            @if($fechaFormulario)
                                @if($estaVencido)
                                <div class="alert alert-danger py-2 mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>VENCIDO</strong> - Excede los 5 años
                                </div>
                                @else
                                <div class="alert alert-success py-2 mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>VIGENTE</strong>
                                </div>
                                @endif
                            @endif

                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $formulario2->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $formulario2->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver formularios2')
                                    <a href="{{ route('formularios2.show', $formulario2) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar formularios2')
                                    <a href="{{ route('formularios2.edit', $formulario2) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @if($formulario2->pdfform2)
                                    <a href="{{ route('formularios2.download', $formulario2) }}" class="btn btn-sm btn-outline-success" title="Descargar PDF" target="_blank">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>

                            @if($formulario2->fecha)
                            <div class="info-item mb-2">
                                <div class="info-label">Fecha:</div>
                                <div class="info-value">
                                    {{ $formulario2->fecha->format('d/m/Y') }}
                                    @if($esReciente)
                                    <span class="badge bg-info ms-1">Reciente</span>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Antigüedad:</div>
                                <div class="info-value text-muted small">
                                    Hace {{ $aniosDesdeFecha }} años
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Vencimiento:</div>
                                <div class="info-value {{ $estaVencido ? 'text-danger fw-bold' : '' }}">
                                    @if($fechaVencimiento)
                                        {{ $fechaVencimiento->format('d/m/Y') }}
                                        @if($estaVencido)
                                        <i class="fas fa-exclamation-circle text-danger ms-1"></i>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Estado:</div>
                                <div class="info-value">
                                    <span class="badge bg-{{ $estaVencido ? 'danger' : 'success' }}">
                                        {{ $estaVencido ? 'Por actualizar' : 'Vigente' }}
                                    </span>
                                </div>
                            </div>
                            @endif


                            @if($formulario2->observacion)
                            <div class="info-item mb-2">
                                <div class="info-label">Observación:</div>
                                <div class="info-value text-muted small">{{ $formulario2->observacion }}</div>
                            </div>
                            @endif

                            @if($formulario2->informacion_completa)
                            <div class="info-item mb-2">
                                <div class="info-label">Información:</div>
                                <div class="info-value text-muted small">{{ $formulario2->informacion_completa }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-file-invoice fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay registros de Formulario 2</p>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-handshake text-primary me-2"></i>
                Compromisos
                <span class="badge bg-primary">@if($persona->bachilleres)
    {{ $persona->bachilleres->count() }}
@else
    0
@endif
</span>
            </h6>
            <div>
                @can('crear compromisos')
                <a href="{{ route('compromisos.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Compromiso">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if(optional($persona->compromisos)->count() > 0)
            <div class="documentos-list">
                @foreach($persona->compromisos as $compromiso)
                <div class="documento-item mb-3">
                    <div class="card border card-hover">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $compromiso->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $compromiso->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver compromisos')
                                    <a href="{{ route('compromisos.show', $compromiso) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar compromisos')
                                    <a href="{{ route('compromisos.edit', $compromiso) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">ID:</div>
                                <div class="info-value">#{{ $compromiso->id }}</div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Total Compromisos:</div>
                                <div class="info-value">
                                    <span class="badge bg-primary">{{ $compromiso->total_compromisos }}</span>
                                </div>
                            </div>

                            <!-- Lista compacta de compromisos individuales -->
                            @if($compromiso->total_compromisos > 0)
                            <div class="compromisos-mini-list mt-2">
                                <div class="info-label mb-1 small">Detalles:</div>
                                @foreach($compromiso->compromisos as $comp)
                                <div class="d-flex justify-content-between align-items-center mb-1 p-1 border rounded">
                                    <div class="flex-grow-1">
                                        <strong class="small">Comp. {{ $comp['numero'] }}</strong>
                                        <p class="mb-0 text-muted small">{{ Str::limit($comp['descripcion'], 50) }}</p>
                                    </div>
                                    @if($comp['archivo'])
                                    <a href="{{ route('compromisos.download', ['compromiso' => $compromiso, 'numero' => $comp['numero']]) }}"
                                       class="btn btn-outline-success btn-xs ms-2" title="Descargar">
                                        <i class="fas fa-download fa-xs"></i>
                                    </a>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="empty-state-mini text-center py-2">
                                <p class="mb-0 text-muted small">No hay compromisos individuales</p>
                            </div>
                            @endif

                            <div class="info-item mb-2">
                                <div class="info-label">Registro:</div>
                                <div class="info-value text-muted small">
                                    {{ $compromiso->fechaRegistro->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            @if($compromiso->fechaActualizacion)
                            <div class="info-item mb-2">
                                <div class="info-label">Actualización:</div>
                                <div class="info-value text-muted small">
                                    {{ $compromiso->fechaActualizacion->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-handshake fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay registros de Compromisos</p>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="col-md-6 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-map-marked-alt text-primary me-2"></i>
                Direccion y croquis
                <span class="badge bg-primary">{{ $persona->croquis->count() }}</span>
            </h6>
            <div>
                @can('crear croquis')
                <a href="{{ route('croquis.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Nuevo Lugar">
                    <i class="fas fa-plus"></i> Agregar Lugar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($persona->croquis->count() > 0)
            <div class="lugares-list">
                @foreach($persona->croquis as $index => $croqui)
                <div class="lugar-item mb-3">
                    <div class="card border card-hover">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $croqui->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $croqui->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver croquis')
                                    <a href="{{ route('croquis.show', $croqui) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar croquis')
                                    <a href="{{ route('croquis.edit', $croqui) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    <a href="{{ $croqui->google_maps_link }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver en Google Maps">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Lugar #{{ $index + 1 }}:</div>
                                <div class="info-value">{{ $croqui->direccion }}</div>
                            </div>

                            @if($croqui->descripcion)
                            <div class="info-item mb-2">
                                <div class="info-label">Descripción:</div>
                                <div class="info-value text-muted small">{{ $croqui->descripcion }}</div>
                            </div>
                            @endif

                            <div class="info-item mb-2">
                                <div class="info-label">Coordenadas:</div>
                                <div class="info-value text-muted small">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    Lat: {{ number_format($croqui->latitud, 6) }}, Lng: {{ number_format($croqui->longitud, 6) }}
                                </div>
                            </div>

                            <!-- Mapa mini para cada lugar -->
                            <div class="mt-2">
                                <div class="mapa-mini">
                                    <iframe
                                        width="100%"
                                        height="150"
                                        frameborder="0"
                                        scrolling="no"
                                        marginheight="0"
                                        marginwidth="0"
                                        src="{{ $croqui->google_maps_iframe }}"
                                        style="border: 1px solid #ddd; border-radius: 4px;"
                                        title="Ubicación de {{ $croqui->direccion }}">
                                    </iframe>
                                </div>
                                <div class="text-center mt-1">
                                    <small class="text-muted">Ubicación aproximada</small>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-6">
                                    <div class="info-item">
                                        <div class="info-label small">Registro:</div>
                                        <div class="info-value text-muted smaller">
                                            {{ $croqui->fechaRegistro->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                                @if($croqui->fechaActualizacion)
                                <div class="col-6">
                                    <div class="info-item">
                                        <div class="info-label small">Actualizado:</div>
                                        <div class="info-value text-muted smaller">
                                            {{ $croqui->fechaActualizacion->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-map-marked-alt fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay registros de lugares donde ha vivido</p>
                <p class="text-muted small">Agregue los diferentes lugares donde ha residido</p>
            </div>
            @endif
        </div>
    </div>
</div>
@php
    // Verificar si la relación existe y es una colección
    $cedulas = $persona->cedulas ?? collect();
    $totalCedulas = is_countable($cedulas) ? count($cedulas) : 0;
@endphp
<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-id-card text-primary me-2"></i>
                Cédulas de Identidad
                <span class="badge bg-primary">{{ $totalCedulas }}</span>
            </h6>
            <div>
                @can('crear cedulas')
                <a href="{{ route('cedulas.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Cédula">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($totalCedulas > 0)
            <div class="documentos-list">
                @foreach($cedulas as $cedula)
                    @php
                        $estaVencida = $cedula->fechaVencimiento ? now()->gt($cedula->fechaVencimiento) : false;
                    @endphp

                <div class="documento-item mb-3">
                    <div class="card border card-hover {{ $estaVencida ? 'border-danger' : 'border-success' }}">
                        <div class="card-body p-3">
                            <!-- Alert de vencimiento -->
                            @if($cedula->fechaVencimiento)
                                @if($estaVencida)
                                <div class="alert alert-danger py-2 mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>VENCIDA</strong>
                                </div>
                                @else
                                <div class="alert alert-success py-2 mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>VIGENTE</strong>
                                </div>
                                @endif
                            @endif

                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $cedula->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $cedula->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver cedulas')
                                    <a href="{{ route('cedulas.show', $cedula) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar cedulas')
                                    <a href="{{ route('cedulas.edit', $cedula) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @if($cedula->pdfcedula)
                                    <a href="{{ route('cedulas.download', $cedula) }}" class="btn btn-sm btn-outline-success" title="Descargar PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">N° de C.I.:</div>
                                <div class="info-value">{{ $cedula->ci ?? 'N/A' }}</div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Expedido en:</div>
                                <div class="info-value">{{ $cedula->expedido ?? 'N/A' }}</div>
                            </div>

                            @if($cedula->fechaVencimiento)
                            <div class="info-item mb-2">
                                <div class="info-label">Vencimiento:</div>
                                <div class="info-value {{ $estaVencida ? 'text-danger fw-bold' : '' }}">
                                    {{ $cedula->fechaVencimiento->format('d/m/Y') }}
                                </div>
                            </div>
                            @endif

                            <div class="info-item mb-2">
                                <div class="info-label">Registro:</div>
                                <div class="info-value text-muted small">
                                    {{ $cedula->fechaRegistro->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-id-card fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay registros de Cédulas</p>
                <p class="text-muted small">La relación de cédulas no está disponible</p>
            </div>
            @endif
        </div>
    </div>
</div>


@php
    $certificados = $persona->certificadosNacimiento ?? collect();
    $totalCertificados = is_countable($certificados) ? count($certificados) : 0;
@endphp

<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-birthday-cake text-primary me-2"></i>
                Certificados de Nacimiento
                <span class="badge bg-primary">{{ $totalCertificados }}</span>
            </h6>
            <div>
                @can('crear certificados-nacimiento')
                <a href="{{ route('persona.certificado-nacimiento.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Certificado">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($totalCertificados > 0)
            <div class="documentos-list">
                @foreach($certificados as $certificado)
                <div class="documento-item mb-3">
                    <div class="card border card-hover">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $certificado->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $certificado->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver certificados-nacimiento')
                                    <a href="{{ route('certificados-nacimiento.show', $certificado) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar certificados-nacimiento')
                                    <a href="{{ route('certificados-nacimiento.edit', $certificado) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @if($certificado->pdfcern)
                                    <a href="{{ route('certificados-nacimiento.download', $certificado) }}" class="btn btn-sm btn-outline-success" title="Descargar PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>


                            <div class="info-item mb-2">
                                <div class="info-label">Fecha Certificado:</div>
                                <div class="info-value">
                                    {{ $certificado->fecha ? $certificado->fecha->format('d/m/Y') : 'N/A' }}
                                    @if($certificado->fecha && $certificado->es_reciente)
                                    <span class="badge bg-info ms-1">Reciente</span>
                                    @endif
                                </div>
                            </div>

                            @if($certificado->persona && $certificado->persona->fechanacimiento && $certificado->edad_en_certificado)
                            <div class="info-item mb-2">
                                <div class="info-label">Edad registrada:</div>
                                <div class="info-value">{{ $certificado->edad_en_certificado }} años</div>
                            </div>
                            @endif

                            <div class="info-item mb-2">
                                <div class="info-label">Registro:</div>
                                <div class="info-value text-muted small">
                                    {{ $certificado->fechaRegistro->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            @if($certificado->descripcion)
                            <div class="info-item mb-2">
                                <div class="info-label">Descripción:</div>
                                <div class="info-value text-muted small">{{ Str::limit($certificado->descripcion, 60) }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-birthday-cake fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay certificados de nacimiento</p>
            </div>
            @endif
        </div>
    </div>
</div>


@php
    $licencias = $persona->licenciasConducir ?? collect();
    $totalLicencias = is_countable($licencias) ? count($licencias) : 0;
@endphp

<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-id-card-alt text-primary me-2"></i>
                Licencias de Conducir
                <span class="badge bg-primary">{{ $totalLicencias }}</span>
            </h6>
            <div>
                @can('crear licencias-conducir')
                <a href="{{ route('persona.licencia-conducir.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Licencia">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($totalLicencias > 0)
            <div class="documentos-list">
                @foreach($licencias as $licencia)
                    @php
                        $estaVencida = $licencia->fechavencimiento ? now()->gt($licencia->fechavencimiento) : false;
                        $diasRestantes = $licencia->fechavencimiento ? now()->diffInDays($licencia->fechavencimiento, false) : null;
                    @endphp

                <div class="documento-item mb-3">
                    <div class="card border card-hover {{ $estaVencida ? 'border-danger' : 'border-success' }}">
                        <div class="card-body p-3">
                            <!-- Alert de vencimiento -->
                            @if($licencia->fechavencimiento)
                                @if($estaVencida)
                                <div class="alert alert-danger py-2 mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>VENCIDA</strong>
                                </div>
                                @else
                                <div class="alert alert-success py-2 mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>VIGENTE</strong>
                                </div>
                                @endif
                            @endif

                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $licencia->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $licencia->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver licencias-conducir')
                                    <a href="{{ route('licencias-conducir.show', $licencia) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar licencias-conducir')
                                    <a href="{{ route('licencias-conducir.edit', $licencia) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @if($licencia->pdflicc)
                                    <a href="{{ route('licencias-conducir.download', $licencia) }}" class="btn btn-sm btn-outline-success" title="Descargar PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">ID:</div>
                                <div class="info-value">#{{ $licencia->id }}</div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Categoría:</div>
                                <div class="info-value">
                                    <span class="badge bg-primary">{{ $licencia->categoria }}</span>
                                </div>
                            </div>

                            @if($licencia->fechavencimiento)
                            <div class="info-item mb-2">
                                <div class="info-label">Vencimiento:</div>
                                <div class="info-value {{ $estaVencida ? 'text-danger fw-bold' : '' }}">
                                    {{ $licencia->fechavencimiento->format('d/m/Y') }}
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Estado:</div>
                                <div class="info-value">
                                    <span class="badge bg-{{ $estaVencida ? 'danger' : 'success' }}">
                                        {{ $estaVencida ? 'Vencida' : 'Vigente' }}
                                    </span>
                                </div>
                            </div>
                            @endif

                            <div class="info-item mb-2">
                                <div class="info-label">Registro:</div>
                                <div class="info-value text-muted small">
                                    {{ $licencia->fechaRegistro->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            @if($licencia->descripcion)
                            <div class="info-item mb-2">
                                <div class="info-label">Descripción:</div>
                                <div class="info-value text-muted small">{{ Str::limit($licencia->descripcion, 50) }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-id-card-alt fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay licencias de conducir</p>
            </div>
            @endif
        </div>
    </div>
</div>

@php
    $licencias = $persona->licenciasConducir ?? collect();
    $totalLicencias = is_countable($licencias) ? count($licencias) : 0;
@endphp

<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-id-card-alt text-primary me-2"></i>
                Licencias de Conducir
                <span class="badge bg-primary">{{ $totalLicencias }}</span>
            </h6>
            <div>
                @can('crear licencias-conducir')
                <a href="{{ route('persona.licencia-conducir.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Licencia">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($totalLicencias > 0)
            <div class="documentos-list">
                @foreach($licencias as $licencia)
                    @php
                        $estaVencida = $licencia->fechavencimiento ? now()->gt($licencia->fechavencimiento) : false;
                        $diasRestantes = $licencia->fechavencimiento ? now()->diffInDays($licencia->fechavencimiento, false) : null;
                    @endphp

                <div class="documento-item mb-3">
                    <div class="card border card-hover {{ $estaVencida ? 'border-danger' : 'border-success' }}">
                        <div class="card-body p-3">
                            <!-- Alert de vencimiento -->
                            @if($licencia->fechavencimiento)
                                @if($estaVencida)
                                <div class="alert alert-danger py-2 mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>VENCIDA</strong>
                                </div>
                                @else
                                <div class="alert alert-success py-2 mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>VIGENTE</strong>
                                </div>
                                @endif
                            @endif

                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $licencia->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $licencia->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver licencias-conducir')
                                    <a href="{{ route('licencias-conducir.show', $licencia) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar licencias-conducir')
                                    <a href="{{ route('licencias-conducir.edit', $licencia) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @if($licencia->pdflicc)
                                    <a href="{{ route('licencias-conducir.download', $licencia) }}" class="btn btn-sm btn-outline-success" title="Descargar PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">ID:</div>
                                <div class="info-value">#{{ $licencia->id }}</div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Categoría:</div>
                                <div class="info-value">
                                    <span class="badge bg-primary">{{ $licencia->categoria }}</span>
                                </div>
                            </div>

                            @if($licencia->fechavencimiento)
                            <div class="info-item mb-2">
                                <div class="info-label">Vencimiento:</div>
                                <div class="info-value {{ $estaVencida ? 'text-danger fw-bold' : '' }}">
                                    {{ $licencia->fechavencimiento->format('d/m/Y') }}
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Estado:</div>
                                <div class="info-value">
                                    <span class="badge bg-{{ $estaVencida ? 'danger' : 'success' }}">
                                        {{ $estaVencida ? 'Vencida' : 'Vigente' }}
                                    </span>
                                </div>
                            </div>
                            @endif

                            <div class="info-item mb-2">
                                <div class="info-label">Registro:</div>
                                <div class="info-value text-muted small">
                                    {{ $licencia->fechaRegistro->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            @if($licencia->descripcion)
                            <div class="info-item mb-2">
                                <div class="info-label">Descripción:</div>
                                <div class="info-value text-muted small">{{ Str::limit($licencia->descripcion, 50) }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-id-card-alt fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay licencias de conducir</p>
            </div>
            @endif
        </div>
    </div>
</div>

@php
    $licencias = $persona->licenciasMilitares ?? collect();
    $totalLicencias = is_countable($licencias) ? count($licencias) : 0;
@endphp

<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-shield-alt text-primary me-2"></i>
                Licencias Militares
                <span class="badge bg-primary">{{ $totalLicencias }}</span>
            </h6>
            <div>
                @can('crear licencias-militares')
                <a href="{{ route('persona.licencia-militar.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Licencia">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($totalLicencias > 0)
            <div class="documentos-list">
                @foreach($licencias as $licencia)
                <div class="documento-item mb-3">
                    <div class="card border card-hover">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $licencia->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $licencia->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver licencias-militares')
                                    <a href="{{ route('licencias-militares.show', $licencia) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar licencias-militares')
                                    <a href="{{ route('licencias-militares.edit', $licencia) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @if($licencia->pdflic)
                                    <a href="{{ route('licencias-militares.download', $licencia) }}" class="btn btn-sm btn-outline-success" title="Descargar PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">ID:</div>
                                <div class="info-value">#{{ $licencia->id }}</div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Código:</div>
                                <div class="info-value">{{ $licencia->codigo ?? 'N/A' }}</div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Serie:</div>
                                <div class="info-value">{{ $licencia->serie ?? 'N/A' }}</div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Fecha:</div>
                                <div class="info-value">
                                    {{ $licencia->fecha ? $licencia->fecha->format('d/m/Y') : 'N/A' }}
                                    @if($licencia->fecha && $licencia->es_reciente)
                                    <span class="badge bg-info ms-1">Reciente</span>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Registro:</div>
                                <div class="info-value text-muted small">
                                    {{ $licencia->fechaRegistro->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            @if($licencia->descripcion)
                            <div class="info-item mb-2">
                                <div class="info-label">Descripción:</div>
                                <div class="info-value text-muted small">{{ Str::limit($licencia->descripcion, 50) }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-shield-alt fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay licencias militares</p>
            </div>
            @endif
        </div>
    </div>
</div>

@php
    $curriculums = $persona->curriculums ?? collect();
    $totalCurriculums = is_countable($curriculums) ? count($curriculums) : 0;
@endphp

<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-file-contract text-primary me-2"></i>
                Currículums
                <span class="badge bg-primary">{{ $totalCurriculums }}</span>
            </h6>
            <div>
                @can('crear curriculums')
                <a href="{{ route('curriculums.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Curriculum">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($totalCurriculums > 0)
            <div class="documentos-list">
                @foreach($curriculums as $curriculum)
                <div class="documento-item mb-3">
                    <div class="card border card-hover">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $curriculum->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $curriculum->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver curriculums')
                                    <a href="{{ route('curriculums.show', $curriculum) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar curriculums')
                                    <a href="{{ route('curriculums.edit', $curriculum) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @if($curriculum->pdfcorri)
                                    <a href="{{ route('curriculums.download', $curriculum) }}" class="btn btn-sm btn-outline-success" title="Descargar PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>


                            <div class="info-item mb-2">
                                <div class="info-label">Registro:</div>
                                <div class="info-value text-muted small">
                                    {{ $curriculum->fechaRegistro->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            @if($curriculum->fechaActualizacion)
                            <div class="info-item mb-2">
                                <div class="info-label">Actualización:</div>
                                <div class="info-value text-muted small">
                                    {{ $curriculum->fechaActualizacion->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            @endif

                            @if($curriculum->descripcion)
                            <div class="info-item mb-2">
                                <div class="info-label">Descripción:</div>
                                <div class="info-value text-muted small">{{ Str::limit($curriculum->descripcion, 60) }}</div>
                            </div>
                            @endif

                            @if($curriculum->informacion_resumida)
                            <div class="info-item mb-2">
                                <div class="info-label">Resumen:</div>
                                <div class="info-value text-muted small">{{ Str::limit($curriculum->informacion_resumida, 80) }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-file-contract fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay currículums registrados</p>
            </div>
            @endif
        </div>
    </div>
</div>

@php
    $profesiones = $persona->profesiones ?? collect();
    $totalProfesiones = is_countable($profesiones) ? count($profesiones) : 0;
@endphp

<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-graduation-cap text-primary me-2"></i>
                Profesiones
                <span class="badge bg-primary">{{ $totalProfesiones }}</span>
            </h6>
            <div>
                @can('crear profesiones')
                <a href="{{ route('profesion.create', ['persona' => $persona->id]) }}" class="btn btn-sm btn-primary" title="Agregar Profesión">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($totalProfesiones > 0)
            <div class="documentos-list">
                @foreach($profesiones as $profesion)
                <div class="documento-item mb-3">
                    <div class="card border card-hover">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $profesion->estado == 1? 'success' : 'danger' }} status-badge">
                                    {{ $profesion->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver profesiones')
                                    <a href="{{ route('profesion.show', $profesion) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar profesiones')
                                    <a href="{{ route('profesion.edit', $profesion) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @if($profesion->pdfprofesion) {{-- Ajusta según tu campo de archivo --}}
                                    <a href="{{ route('profesion.download', $profesion) }}" class="btn btn-sm btn-outline-success" title="Descargar PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Diploma:</div>
                                <div class="info-value">{{ $profesion->diploma ?? 'N/A' }}</div>
                            </div>

                            @if($profesion->fechaDiploma)
                            <div class="info-item mb-2">
                                <div class="info-label">Fecha Diploma:</div>
                                <div class="info-value text-muted small">
                                    {{ \Carbon\Carbon::parse($profesion->fechaDiploma)->format('d/m/Y') }}
                                </div>
                            </div>
                            @endif

                            @if($profesion->provisionN)
                            <div class="info-item mb-2">
                                <div class="info-label">Prov. Nacional:</div>
                                <div class="info-value">{{ $profesion->provisionN }}</div>
                            </div>
                            @endif

                            @if($profesion->fechaProvision)
                            <div class="info-item mb-2">
                                <div class="info-label">Fecha Prov. Nacional:</div>
                                <div class="info-value text-muted small">
                                    {{ \Carbon\Carbon::parse($profesion->fechaProvision)->format('d/m/Y') }}
                                </div>
                            </div>
                            @endif

                            @if($profesion->descripcion)
                            <div class="info-item mb-2">
                                <div class="info-label">Descripción:</div>
                                <div class="info-value text-muted small">{{ Str::limit($profesion->descripcion, 60) }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-graduation-cap fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay profesiones registradas</p>
            </div>
            @endif
        </div>
    </div>
</div>


@php
    $certificados = $persona->certificados ?? collect();
    $totalCertificados = is_countable($certificados) ? count($certificados) : 0;
@endphp

<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-certificate text-primary me-2"></i>
                Certificados
                <span class="badge bg-primary">{{ $totalCertificados }}</span>
            </h6>
            <div>
                @can('crear certificados')
                <a href="{{ route('certificados.create', ['idPersona' => $persona->id]) }}" class="btn btn-sm btn-primary" title="Agregar Certificado">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-3">
            @if($totalCertificados > 0)
            <div class="documentos-list">
                @foreach($certificados as $certificado)
                <div class="documento-item mb-3">
                    <div class="card border card-hover">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $certificado->estado ? 'success' : 'danger' }} status-badge">
                                    {{ $certificado->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <div class="btn-group action-buttons">
                                    @can('ver certificados')
                                    <a href="#" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('editar certificados')
                                    <a href="{{ route('certificados.edit', $certificado) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('eliminar certificados')
                                    <form action="{{ route('certificados.destroy', $certificado->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Eliminar" onclick="return confirm('¿Eliminar este certificado?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Certificado:</div>
                                <div class="info-value fw-bold">{{ $certificado->nombre }}</div>
                            </div>

                            <div class="info-item mb-2">
                                <div class="info-label">Tipo:</div>
                                <div class="info-value">
                                    <span class="badge bg-secondary">{{ $certificado->tipo }}</span>
                                </div>
                            </div>

                            @if($certificado->fecha)
                            <div class="info-item mb-2">
                                <div class="info-label">Fecha:</div>
                                <div class="info-value text-muted small">
                                    {{ \Carbon\Carbon::parse($certificado->fecha)->format('d/m/Y') }}
                                </div>
                            </div>
                            @endif

                            @if($certificado->instituto)
                            <div class="info-item mb-2">
                                <div class="info-label">Instituto:</div>
                                <div class="info-value text-muted small">{{ $certificado->instituto }}</div>
                            </div>
                            @endif

                            <div class="info-item mb-2">
                                <div class="info-label">Registro:</div>
                                <div class="info-value text-muted small">
                                    {{ $certificado->fechaRegistro->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            @if($certificado->fechaActualizacion)
                            <div class="info-item mb-2">
                                <div class="info-label">Actualización:</div>
                                <div class="info-value text-muted small">
                                    {{ $certificado->fechaActualizacion->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            @endif

                            @if($certificado->descripcion)
                            <div class="info-item mb-2">
                                <div class="info-label">Descripción:</div>
                                <div class="info-value text-muted small">{{ Str::limit($certificado->descripcion, 60) }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state text-center py-4">
                <i class="fas fa-certificate fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay certificados registrados</p>
            </div>
            @endif
        </div>
    </div>
</div>

@if (session('success'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#28a745',
        color: '#fff',
        customClass: {
            popup: 'custom-toast'
        },
    });
</script>
@endif

<style>
    .custom-toast {
        width: 320px !important;
        border-radius: 8px !important;
        font-size: 14px !important;
    }

    .card-hover {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }

    .info-item {
        margin-bottom: 0.5rem;
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
    }

    .info-value {
        font-size: 0.875rem;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
</style>

        <!-- Bachiller -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-graduation-cap text-primary me-2"></i>
                        Registros de Bachiller
                        <span class="badge bg-primary">{{ $persona->bachilleres->count() }}</span>
                    </h6>
                    <div>
                        @can('crear bachilleres')
                        <a href="{{ route('persona.bachiller.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Bachiller">
                            <i class="fas fa-plus"></i> Agregar
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body p-3">
                    @if($persona->bachilleres->count() > 0)
                    <div class="documentos-list">
                        @foreach($persona->bachilleres as $bachiller)
                        <div class="documento-item mb-3">
                            <div class="card border card-hover">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-{{ $bachiller->estado ? 'success' : 'danger' }} status-badge">
                                            {{ $bachiller->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                        <div class="btn-group action-buttons">
                                            @can('ver bachilleres')
                                            <a href="{{ route('bachilleres.show', $bachiller) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('editar bachilleres')
                                            <a href="{{ route('persona.bachiller.edit', [$persona->id, $bachiller]) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        </div>
                                    </div>

                                    <div class="info-item mb-2">
                                        <div class="info-label">Fecha:</div>
                                        <div class="info-value">{{ $bachiller->fecha ? $bachiller->fecha->format('d/m/Y') : 'N/A' }}</div>
                                    </div>

                                    @if($bachiller->observacion)
                                    <div class="info-item mb-2">
                                        <div class="info-label">Observación:</div>
                                        <div class="info-value text-muted small">{{ $bachiller->observacion }}</div>
                                    </div>
                                    @endif

                                    @if($bachiller->otros)
                                    <div class="info-item mb-2">
                                        <div class="info-label">Otros:</div>
                                        <div class="info-value text-muted small">{{ $bachiller->otros }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state text-center py-4">
                        <i class="fas fa-graduation-cap fa-2x mb-2"></i>
                        <p class="mb-0">No hay registros de bachiller</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Formulario 1 -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-file-alt text-success me-2"></i>
                        Formularios 1
                        <span class="badge bg-success">{{ $persona->formularios1->count() }}</span>
                    </h6>
                    <div>
                        @can('crear formularios1')
                        <a href="{{ route('formularios1.create', $persona->id) }}" class="btn btn-sm btn-success" title="Agregar Formulario 1">
                            <i class="fas fa-plus"></i> Agregar
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body p-3">
                    @if($persona->formularios1->count() > 0)
                    <div class="documentos-list">
                        @foreach($persona->formularios1 as $formulario)
                        <div class="documento-item mb-3">
                            <div class="card border card-hover">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-{{ $formulario->estado ? 'success' : 'danger' }} status-badge">
                                            {{ $formulario->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                        <div class="btn-group action-buttons">
                                            @can('ver formularios1')
                                            <a href="{{ route('formularios1.show', $formulario) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('editar formularios1')
                                            <a href="{{ route('formularios1.edit', [$persona->id, $formulario]) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        </div>
                                    </div>

                                    <div class="info-item mb-2">
                                        <div class="info-label">Fecha:</div>
                                        <div class="info-value">{{ $formulario->fecha ? $formulario->fecha->format('d/m/Y') : 'N/A' }}</div>
                                    </div>

                                    @if($formulario->observacion)
                                    <div class="info-item mb-2">
                                        <div class="info-label">Observación:</div>
                                        <div class="info-value text-muted small">{{ $formulario->observacion }}</div>
                                    </div>
                                    @endif

                                    @if($formulario->pdfform1)
                                    <div class="info-item mb-2">
                                        <div class="info-label">PDF:</div>
                                        <div class="info-value">
                                            <a href="{{ route('formularios1.download', $formulario) }}" class="btn btn-sm btn-success" title="Descargar PDF">
                                                <i class="fas fa-download"></i> Descargar
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state text-center py-4">
                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                        <p class="mb-0">No hay formularios 1</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Formulario 2 y Consanguinidad -->
        <div class="col-md-4">
            <!-- Formulario 2 -->
            <div class="mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-file-pdf text-info me-2"></i>
                            Formularios 2
                            <span class="badge bg-info">{{ $persona->formularios2->count() }}</span>
                        </h6>
                        <div>
                            @can('crear formularios2')
                            <a href="{{ route('formularios2.create', $persona->id) }}" class="btn btn-sm btn-info" title="Agregar Formulario 2">
                                <i class="fas fa-plus"></i> Agregar
                            </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body p-3">
                        @if($persona->formularios2->count() > 0)
                        <div class="documentos-list">
                            @foreach($persona->formularios2 as $formulario)
                            <div class="documento-item mb-3">
                                <div class="card border card-hover">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-{{ $formulario->estado ? 'success' : 'danger' }} status-badge">
                                                {{ $formulario->estado ? 'Activo' : 'Inactivo' }}
                                            </span>
                                            <div class="btn-group action-buttons">
                                                @can('ver formularios2')
                                                <a href="{{ route('formularios2.show', $formulario) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('editar formularios2')
                                                <a href="{{ route('formularios2.edit', [$persona->id, $formulario]) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                            </div>
                                        </div>

                                        <div class="info-item mb-2">
                                            <div class="info-label">Fecha:</div>
                                            <div class="info-value">{{ $formulario->fecha ? $formulario->fecha->format('d/m/Y') : 'N/A' }}</div>
                                        </div>

                                        @if($formulario->observacion)
                                        <div class="info-item mb-2">
                                            <div class="info-label">Observación:</div>
                                            <div class="info-value text-muted small">{{ $formulario->observacion }}</div>
                                        </div>
                                        @endif

                                        @if($formulario->pdfform2)
                                        <div class="info-item mb-2">
                                            <div class="info-label">PDF:</div>
                                            <div class="info-value">
                                                <a href="{{ route('formularios2.download', $formulario) }}" class="btn btn-sm btn-success" title="Descargar PDF">
                                                    <i class="fas fa-download"></i> Descargar
                                                </a>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="empty-state text-center py-4">
                            <i class="fas fa-file-pdf fa-2x mb-2"></i>
                            <p class="mb-0">No hay formularios 2</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Consanguinidad -->
            <div class="mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-handshake text-warning me-2"></i>
                            Consanguinidades
                            <span class="badge bg-warning">{{ $persona->consanguinidades->count() }}</span>
                        </h6>
                        <div>
                            @can('crear consanguinidades')
                            <a href="{{ route('persona.consanguinidad.create', $persona->id) }}" class="btn btn-sm btn-warning" title="Agregar Consanguinidad">
                                <i class="fas fa-plus"></i> Agregar
                            </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body p-3">
                        @if($persona->consanguinidades->count() > 0)
                        <div class="documentos-list">
                            @foreach($persona->consanguinidades as $consanguinidad)
                            <div class="documento-item mb-3">
                                <div class="card border card-hover">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-{{ $consanguinidad->estado ? 'success' : 'danger' }} status-badge">
                                                {{ $consanguinidad->estado ? 'Activo' : 'Inactivo' }}
                                            </span>
                                            <div class="btn-group action-buttons">
                                                @can('ver consanguinidades')
                                                <a href="{{ route('consanguinidades.show', $consanguinidad) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('editar consanguinidades')
                                                <a href="{{ route('persona.consanguinidad.edit', [$persona->id, $consanguinidad]) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                            </div>
                                        </div>

                                        <div class="info-item mb-2">
                                            <div class="info-label">Fecha:</div>
                                            <div class="info-value">{{ $consanguinidad->fecha ? $consanguinidad->fecha->format('d/m/Y') : 'N/A' }}</div>
                                        </div>

                                        @if($consanguinidad->observacion)
                                        <div class="info-item mb-2">
                                            <div class="info-label">Observación:</div>
                                            <div class="info-value text-muted small">{{ $consanguinidad->observacion }}</div>
                                        </div>
                                        @endif

                                        @if($consanguinidad->pdfconsag)
                                        <div class="info-item mb-2">
                                            <div class="info-label">PDF:</div>
                                            <div class="info-value">
                                                <a href="{{ route('consanguinidades.download', $consanguinidad) }}" class="btn btn-sm btn-success" title="Descargar PDF">
                                                    <i class="fas fa-download"></i> Descargar
                                                </a>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="empty-state text-center py-4">
                            <i class="fas fa-handshake fa-2x mb-2"></i>
                            <p class="mb-0">No hay consanguinidades</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .status-badge {
        font-size: 0.7rem;
        padding: 0.25em 0.5em;
    }
    .empty-state {
        color: #6c757d;
    }
    .empty-state i {
        opacity: 0.5;
    }
    .info-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.2rem;
        font-weight: 500;
    }
    .info-value {
        font-size: 0.9rem;
        margin-bottom: 0.8rem;
        word-break: break-word;
    }
    .action-buttons .btn {
        border-radius: 4px;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .documentos-list {
        max-height: 400px;
        overflow-y: auto;
    }
    .documento-item:last-child {
        margin-bottom: 0 !important;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
</style>
@endsection
