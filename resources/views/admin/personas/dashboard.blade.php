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

    <!-- Sección de Documentos en Tablas -->
    <div class="row">

        <!-- DJB Renta -->
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-file-contract text-primary me-2"></i>
                        DJB Renta
                        <span class="badge bg-primary">{{ $persona->djbRenta->count() }}</span>
                    </h6>
                    <div>
                        @can('crear djbrentas')
                            <a href="{{ route('djbrentas.create', ['from_show' => 1, 'persona_id' => $persona->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Agregar DJBRenta
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($persona->djbRenta->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Estado</th>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Archivo</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($persona->djbRenta as $djbrenta)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $djbrenta->estado ? 'success' : 'danger' }}">
                                            {{ $djbrenta->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>{{ $djbrenta->fecha->format('d/m/Y') }}</td>
                                    <td>{{ $djbrenta->tipo ?? 'Sin tipo' }}</td>
                                    <td>
                                        @if($djbrenta->pdfrenta)
                                        <a href="{{ route('djbrentas.download', $djbrenta) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                        @else
                                        <span class="text-muted">Sin archivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
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
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

        <!-- AFP -->
        <div class="col-md-12 mb-4">
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
                <div class="card-body p-0">
                    @if($persona->afps->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Estado</th>
                                    <th>CUA</th>
                                    <th>Fecha Registro</th>
                                    <th>Observación</th>
                                    <th>Archivo</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($persona->afps as $afp)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $afp->estado ? 'success' : 'danger' }}">
                                            {{ $afp->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>{{ $afp->cua }}</td>
                                    <td>{{ $afp->fechaRegistro->format('d/m/Y H:i') }}</td>
                                    <td>{{ $afp->observacion ? Str::limit($afp->observacion, 30) : 'N/A' }}</td>
                                    <td>
                                        @if($afp->pdfafps)
                                        <a href="{{ route('afps.download', $afp) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                        @else
                                        <span class="text-muted">Sin archivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
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
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

        <!-- Caja Cordes -->
        <div class="col-md-12 mb-4">
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
                <div class="card-body p-0">
                    @if($persona->cajacordes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Estado</th>
                                    <th>Fecha</th>
                                    <th>Código</th>
                                    <th>Otros</th>
                                    <th>Archivo</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($persona->cajacordes as $cajacorde)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $cajacorde->estado ? 'success' : 'danger' }}">
                                            {{ $cajacorde->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>{{ $cajacorde->fecha->format('d/m/Y') }}</td>
                                    <td>{{ $cajacorde->codigo ?? 'N/A' }}</td>
                                    <td>{{ $cajacorde->otros ? Str::limit($cajacorde->otros, 30) : 'N/A' }}</td>
                                    <td>
                                        @if($cajacorde->pdfcaja)
                                        <a href="{{ route('cajacordes.download', $cajacorde) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                        @else
                                        <span class="text-muted">Sin archivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
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
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

        <!-- CENVI -->
        <div class="col-md-12 mb-4">
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
                <div class="card-body p-0">
                    @if($persona->cenvis->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Estado</th>
                                    <th>Fecha Emisión</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Estado Vigencia</th>
                                    <th>Observación</th>
                                    <th>Archivo</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($persona->cenvis as $cenvi)
                                    @php
                                        $fechaInicio = $cenvi->fecha;
                                        $fechaVencimiento = $fechaInicio->copy()->addYear();
                                        $hoy = now();
                                        $estaVencido = $hoy->gt($fechaVencimiento);
                                        $diasRestantes = $hoy->diffInDays($fechaVencimiento, false);
                                    @endphp
                                <tr class="{{ $estaVencido ? 'table-danger' : 'table-success' }}">
                                    <td>
                                        <span class="badge bg-{{ $cenvi->estado ? 'success' : 'danger' }}">
                                            {{ $cenvi->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>{{ $cenvi->fecha->format('d/m/Y') }}</td>
                                    <td>{{ $fechaVencimiento->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $estaVencido ? 'danger' : 'success' }}">
                                            {{ $estaVencido ? 'Vencido' : 'Vigente' }}
                                            @if(!$estaVencido)
                                                ({{ $diasRestantes }} días)
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $cenvi->observacion ? Str::limit($cenvi->observacion, 30) : 'N/A' }}</td>
                                    <td>
                                        @if($cenvi->pdfcenvi)
                                        <a href="{{ route('cenvis.download', $cenvi) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                        @else
                                        <span class="text-muted">Sin archivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
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
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
        <div class="col-md-12 mb-4">
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
                <div class="card-body p-0">
                    @if($persona->formularios1->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Estado</th>
                                    <th>Fecha</th>
                                    <th>Antigüedad</th>
                                    <th>Estado Vigencia</th>
                                    <th>Archivo</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($persona->formularios1 as $formulario1)
                                    @php
                                        $fechaFormulario = $formulario1->fecha;
                                        $fechaVencimiento = $fechaFormulario ? $fechaFormulario->copy()->addYears(5) : null;
                                        $hoy = now();
                                        $estaVencido = $fechaVencimiento ? $hoy->gt($fechaVencimiento) : false;
                                        $aniosDesdeFecha = $fechaFormulario ? $hoy->diffInYears($fechaFormulario) : null;
                                        $esReciente = $fechaFormulario ? $hoy->diffInYears($fechaFormulario) <= 2 : false;
                                    @endphp
                                <tr class="{{ $estaVencido ? 'table-danger' : 'table-success' }}">
                                    <td>
                                        <span class="badge bg-{{ $formulario1->estado ? 'success' : 'danger' }}">
                                            {{ $formulario1->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $formulario1->fecha->format('d/m/Y') }}
                                        @if($esReciente)
                                        <span class="badge bg-info ms-1">Reciente</span>
                                        @endif
                                    </td>
                                    <td>{{ $aniosDesdeFecha }} años</td>
                                    <td>
                                        <span class="badge bg-{{ $estaVencido ? 'danger' : 'success' }}">
                                            {{ $estaVencido ? 'Por actualizar' : 'Vigente' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($formulario1->pdfform1)
                                        <a href="{{ route('formularios1.download', $formulario1) }}" class="btn btn-success btn-sm" target="_blank">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                        @else
                                        <span class="text-muted">Sin archivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
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
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

        <!-- Formularios 2 -->
        <div class="col-md-12 mb-4">
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
                <div class="card-body p-0">
                    @if($persona->formularios2->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Estado</th>
                                    <th>Fecha</th>
                                    <th>Antigüedad</th>
                                    <th>Vencimiento</th>
                                    <th>Estado Vigencia</th>
                                    <th>Observación</th>
                                    <th>Archivo</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($persona->formularios2 as $formulario2)
                                    @php
                                        $fechaFormulario = $formulario2->fecha;
                                        $fechaVencimiento = $fechaFormulario ? $fechaFormulario->copy()->addYears(5) : null;
                                        $hoy = now();
                                        $estaVencido = $fechaVencimiento ? $hoy->gt($fechaVencimiento) : false;
                                        $aniosDesdeFecha = $fechaFormulario ? $hoy->diffInYears($fechaFormulario) : null;
                                        $esReciente = $fechaFormulario ? $hoy->diffInYears($fechaFormulario) <= 2 : false;
                                    @endphp
                                <tr class="{{ $estaVencido ? 'table-danger' : 'table-success' }}">
                                    <td>
                                        <span class="badge bg-{{ $formulario2->estado ? 'success' : 'danger' }}">
                                            {{ $formulario2->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $formulario2->fecha->format('d/m/Y') }}
                                        @if($esReciente)
                                        <span class="badge bg-info ms-1">Reciente</span>
                                        @endif
                                    </td>
                                    <td>{{ $aniosDesdeFecha }} años</td>
                                    <td>
                                        @if($fechaVencimiento)
                                            {{ $fechaVencimiento->format('d/m/Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $estaVencido ? 'danger' : 'success' }}">
                                            {{ $estaVencido ? 'Por actualizar' : 'Vigente' }}
                                        </span>
                                    </td>
                                    <td>{{ $formulario2->observacion ? Str::limit($formulario2->observacion, 30) : 'N/A' }}</td>
                                    <td>
                                        @if($formulario2->pdfform2)
                                        <a href="{{ route('formularios2.download', $formulario2) }}" class="btn btn-success btn-sm" target="_blank">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                        @else
                                        <span class="text-muted">Sin archivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
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
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

        <!-- Compromisos -->
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-handshake text-primary me-2"></i>
                        Compromisos
                        <span class="badge bg-primary">{{ optional($persona->compromisos)->count() ?? 0 }}</span>
                    </h6>
                    <div>
                        @can('crear compromisos')
                        <a href="{{ route('compromisos.create', $persona->id) }}" class="btn btn-sm btn-primary" title="Agregar Compromiso">
                            <i class="fas fa-plus"></i> Agregar
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(optional($persona->compromisos)->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Estado</th>
                                    <th>ID</th>
                                    <th>Total Compromisos</th>
                                    <th>Fecha Registro</th>
                                    <th>Fecha Actualización</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($persona->compromisos as $compromiso)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $compromiso->estado ? 'success' : 'danger' }}">
                                            {{ $compromiso->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>#{{ $compromiso->id }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $compromiso->total_compromisos }}</span>
                                    </td>
                                    <td>{{ $compromiso->fechaRegistro->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($compromiso->fechaActualizacion)
                                            {{ $compromiso->fechaActualizacion->format('d/m/Y H:i') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
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
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

        <!-- Croquis y Direcciones -->
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-map-marked-alt text-primary me-2"></i>
                        Direcciones y Croquis
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
                <div class="card-body p-0">
                    @if($persona->croquis->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Estado</th>
                                    <th>Dirección</th>
                                    <th>Descripción</th>
                                    <th>Coordenadas</th>
                                    <th>Fecha Registro</th>
                                    <th width="140">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($persona->croquis as $index => $croqui)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $croqui->estado ? 'success' : 'danger' }}">
                                            {{ $croqui->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($croqui->direccion, 40) }}</td>
                                    <td>{{ $croqui->descripcion ? Str::limit($croqui->descripcion, 30) : 'N/A' }}</td>
                                    <td>
                                        <small class="text-muted">
                                            Lat: {{ number_format($croqui->latitud, 6) }}<br>
                                            Lng: {{ number_format($croqui->longitud, 6) }}
                                        </small>
                                    </td>
                                    <td>{{ $croqui->fechaRegistro->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group">
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
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

        <!-- Cédulas de Identidad -->
        @php
            $cedulas = $persona->cedulas ?? collect();
            $totalCedulas = is_countable($cedulas) ? count($cedulas) : 0;
        @endphp
        <div class="col-md-12 mb-4">
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
                <div class="card-body p-0">
                    @if($totalCedulas > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Estado</th>
                                    <th>N° de C.I.</th>
                                    <th>Expedido en</th>
                                    <th>Vencimiento</th>
                                    <th>Estado Vigencia</th>
                                    <th>Archivo</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cedulas as $cedula)
                                    @php
                                        $estaVencida = $cedula->fechaVencimiento ? now()->gt($cedula->fechaVencimiento) : false;
                                    @endphp
                                <tr class="{{ $estaVencida ? 'table-danger' : 'table-success' }}">
                                    <td>
                                        <span class="badge bg-{{ $cedula->estado ? 'success' : 'danger' }}">
                                            {{ $cedula->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>{{ $cedula->ci ?? 'N/A' }}</td>
                                    <td>{{ $cedula->expedido ?? 'N/A' }}</td>
                                    <td>
                                        @if($cedula->fechaVencimiento)
                                            {{ $cedula->fechaVencimiento->format('d/m/Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $estaVencida ? 'danger' : 'success' }}">
                                            {{ $estaVencida ? 'Vencida' : 'Vigente' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($cedula->pdfcedula)
                                        <a href="{{ route('cedulas.download', $cedula) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                        @else
                                        <span class="text-muted">Sin archivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
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
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

        <!-- Continuar con las demás secciones en formato tabla... -->

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

    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .empty-state {
        color: #6c757d;
    }

    .empty-state i {
        opacity: 0.5;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.02);
    }
</style>
@endsection
