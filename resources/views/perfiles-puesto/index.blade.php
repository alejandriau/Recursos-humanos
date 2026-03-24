@extends('dashboard')
@section('contenido')

<div class="container-fluid pt-4 px-4">
    <!-- Título -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-briefcase me-2"></i>
                    Puestos y Perfiles
                </h2>
                <a href="{{ route('puestos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Puesto
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fas fa-briefcase fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Puestos</p>
                    <h6 class="mb-0">{{ $stats['total'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fas fa-check-circle fa-3x text-success"></i>
                <div class="ms-3">
                    <p class="mb-2">Con Perfil Definido</p>
                    <h6 class="mb-0">{{ $stats['con_perfil'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                <div class="ms-3">
                    <p class="mb-2">Sin Perfil</p>
                    <h6 class="mb-0">{{ $stats['sin_perfil'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fas fa-building fa-3x text-info"></i>
                <div class="ms-3">
                    <p class="mb-2">Unidades</p>
                    <h6 class="mb-0">{{ $unidades->count() }}</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('perfil-puesto.index') }}" id="filtroForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Nombre, código..."
                               value="{{ $search }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Unidad Organizacional</label>
                        <select name="unidad_id" class="form-control">
                            <option value="">Todas las unidades</option>
                            @foreach($unidades as $unidad)
                                <option value="{{ $unidad->id }}" 
                                    {{ $unidadId == $unidad->id ? 'selected' : '' }}>
                                    {{ $unidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Nivel Jerárquico</label>
                        <select name="nivel_jerarquico" class="form-control">
                            <option value="">Todos</option>
                            @foreach($nivelesJerarquicos as $key => $value)
                                <option value="{{ $key }}" 
                                    {{ $nivelJerarquico == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Estado del Perfil</label>
                        <select name="tiene_perfil" class="form-control">
                            <option value="">Todos</option>
                            <option value="si" {{ $tienePerfil == 'si' ? 'selected' : '' }}>Con Perfil</option>
                            <option value="no" {{ $tienePerfil == 'no' ? 'selected' : '' }}>Sin Perfil</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                        <a href="{{ route('perfil-puesto.index') }}" class="btn btn-secondary w-100 ms-2">
                            <i class="fas fa-undo me-2"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de Puestos -->
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Listado de Puestos
                </h5>
                <span class="badge bg-primary">{{ $puestos->total() }} puestos encontrados</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">ITEM</th>
                            <th width="15%">Código</th>
                            <th width="20%">Nombre del Puesto</th>
                            <th width="15%">Unidad</th>
                            <th width="10%">Nivel Jerárquico</th>
                            <th width="20%">Requisitos</th>
                            <th width="15%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($puestos as $puesto)
                        <tr>
                            <td>{{ $puesto->item }}</td>
                            <td>
                                <strong>{{ $puesto->codigo }}</strong>
                                @if($puesto->estado == 'inactivo')
                                    <span class="badge bg-danger ms-1">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                {{ $puesto->denominacion }}
                                @if($puesto->descripcion)
                                    <small class="d-block text-muted">{{ Str::limit($puesto->descripcion, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-building me-1"></i>
                                {{ $puesto->unidadOrganizacional->denominacion ?? 'N/A' }}
                                @if($puesto->unidadOrganizacional && $puesto->unidadOrganizacional->sigla)
                                    <small class="d-block text-muted">{{ $puesto->unidadOrganizacional->sigla }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $nivelColors = [
                                        'ESTRATEGICO' => 'danger',
                                        'TACTICO' => 'warning',
                                        'OPERATIVO' => 'success'
                                    ];

                                    $nivel = $puesto->nivelJerarquico;
                                    $nivelColor = $nivelColors[$nivel] ?? 'secondary';
                                @endphp

                                <span class="badge bg-{{ $nivelColor }}">
                                    {{ $nivelesJerarquicos[$nivel] ?? $nivel }}
                                </span>
                            </td>
                            <td>
                                @if($puesto->perfilRequisitos)
                                    <div class="small">
                                        @if(optional($puesto->perfilRequisitos->first())->aniosExperienciaMinimos)
                                            <span class="badge bg-info me-1">
                                                <i class="fas fa-clock"></i> {{ optional($puesto->perfilRequisitos->first())->aniosExperienciaMinimos}} años exp.
                                            </span>
                                        @endif
                                        
                                        @if(optional($puesto->perfilRequisitos->first())->nivelAcademicoRequerido)
                                            <span class="badge bg-success me-1">
                                                <i class="fas fa-graduation-cap"></i> {{ optional($puesto->perfilRequisitos->first())->nivelAcademicoRequerido }}
                                            </span>
                                        @endif
                                        
                                        @if(count(optional($puesto->perfilRequisitos->first())->areasConocimientoPermitidas ?? []))
                                            <span class="badge bg-warning me-1">
                                                <i class="fas fa-brain"></i> {{ count(optional($puesto->perfilRequisitos->first())->areasConocimientoPermitidas ?? []) }} áreas
                                            </span>
                                        @endif
                                        
                                        @if(count(optional($puesto->perfilRequisitos->first())->carrerasEspecificas ?? []))
                                            <span class="badge bg-primary me-1">
                                                <i class="fas fa-university"></i> {{ count(optional($puesto->perfilRequisitos->first())->carrerasEspecificas ?? []) }} carreras
                                            </span>
                                        @endif
                                        
                                        @if(optional($puesto->perfilRequisitos->first())->requiereTituloEnProvisionNacional)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-certificate"></i> Titulación Nacional
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-exclamation-circle"></i> Sin perfil definido
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('perfil-puesto.show', $puesto->id) }}" 
                                       class="btn btn-sm btn-info"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($puesto->perfilRequisitos)
                                        <a href="{{ route('perfil-puesto.edit', $puesto->id) }}" 
                                           class="btn btn-sm btn-warning"
                                           title="Editar perfil">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form action="{{ route('perfil-puesto.destroy', $puesto->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('¿Está seguro de eliminar el perfil de este puesto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar perfil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('perfil-puesto.edit', $puesto->id) }}" 
                                           class="btn btn-sm btn-success"
                                           title="Definir perfil">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('puestos.show', $puesto->id) }}" 
                                       class="btn btn-sm btn-secondary"
                                       title="Ver puesto">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="mb-0">No se encontraron puestos con los filtros seleccionados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $puestos->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit al cambiar filtros (opcional)
    $('select[name="unidad_id"], select[name="nivel_jerarquico"], select[name="tiene_perfil"]').on('change', function() {
        $('#filtroForm').submit();
    });
    
    // Preservar filtros al presionar Enter en búsqueda
    $('input[name="search"]').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#filtroForm').submit();
        }
    });
});
</script>
@endpush

@endsection