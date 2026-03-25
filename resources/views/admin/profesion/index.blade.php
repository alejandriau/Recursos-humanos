@extends('dashboard')

@section('contenido')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            
            <!-- Título -->
            <h5 class="mb-0 d-flex align-items-center">
                <i class="fas fa-graduation-cap text-primary me-2"></i>
                <span class="fw-semibold">Listado de Profesiones Registradas</span>
            </h5>

            <!-- Acciones -->
            <div class="d-flex gap-2 flex-wrap">

                <a href="{{ route('catalogos.areas') }}" 
                class="btn btn-outline-primary btn-sm d-flex align-items-center">
                    <i class="fas fa-layer-group me-1"></i>
                    Áreas
                </a>

                <a href="{{ route('catalogos.carreras') }}" 
                class="btn btn-outline-success btn-sm d-flex align-items-center">
                    <i class="fas fa-book me-1"></i>
                    Carreras
                </a>

                <a href="{{ route('catalogos.niveles') }}" 
                class="btn btn-outline-warning btn-sm d-flex align-items-center">
                    <i class="fas fa-signal me-1"></i>
                    Niveles
                </a>

            </div>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <form method="GET" action="{{ route('profesion.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="buscar" class="form-control" 
                                   placeholder="Buscar por nombre, apellido, carrera o provisión..." 
                                   value="{{ request('buscar') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="idAreaConocimiento" class="form-select" onchange="this.form.submit()">
                            <option value="">Todas las áreas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ request('idAreaConocimiento') == $area->id ? 'selected' : '' }}>
                                    {{ $area->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="idNivelAcademico" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos los niveles</option>
                            @foreach($niveles as $nivel)
                                <option value="{{ $nivel->id }}" {{ request('idNivelAcademico') == $nivel->id ? 'selected' : '' }}>
                                    {{ $nivel->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="esPrincipal" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="1" {{ request('esPrincipal') === '1' ? 'selected' : '' }}>Profesión Principal</option>
                            <option value="0" {{ request('esPrincipal') === '0' ? 'selected' : '' }}>Secundaria</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('profesion.index') }}" class="btn btn-secondary w-100" title="Limpiar filtros">
                            <i class="fas fa-eraser"></i>
                        </a>
                    </div>
                </div>
            </form>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Persona</th>
                            <th>Profesión</th>
                            <th>Nivel</th>
                            <th>Área</th>
                            <th>Fecha Titulación</th>
                            <th>Experiencia</th>
                            <th>Provisión</th>
                            <th>Principal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profesiones as $profesion)
                        <tr class="{{ $profesion->esPrincipal ? 'table-primary' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $profesion->persona ? $profesion->persona->nombre_completo : 'N/A' }}</strong>
                            </td>
                            <td>
                                {{ $profesion->carrera ? $profesion->carrera->nombre : 'Sin carrera' }}
                                @if($profesion->universidad)
                                    <br><small class="text-muted">{{ $profesion->universidad }}</small>
                                @endif
                            </td>
                            <td>
                                @if($profesion->carrera && $profesion->carrera->nivelAcademico)
                                    <span class="badge bg-info">
                                        {{ $profesion->carrera->nivelAcademico->nombre }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($profesion->carrera && $profesion->carrera->areaConocimiento)
                                    <span class="badge bg-secondary">
                                        {{ $profesion->carrera->areaConocimiento->nombre }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                {{ $profesion->fechaTitulo ? \Carbon\Carbon::parse($profesion->fechaTitulo)->format('d/m/Y') : '—' }}
                            </td>
                            <td>
                                @if($profesion->fechaTitulo)
                                    <span class="badge {{ $profesion->aniosExperienciaDesdeTitulacion >= 3 ? 'bg-success' : 'bg-warning' }}">
                                        {{ number_format($profesion->aniosExperienciaDesdeTitulacion, 1) }} años
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($profesion->tieneTituloProvision)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Sí
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle"></i> No
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($profesion->esPrincipal)
                                    <span class="badge bg-primary">
                                        <i class="fas fa-star"></i> Principal
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('profesion.show', $profesion->persona->id) }}" 
                                       class="btn btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('profesion.edit', $profesion->id) }}" 
                                       class="btn btn-warning" data-bs-toggle="tooltip" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            onclick="confirmDelete({{ $profesion->id }})" 
                                            class="btn btn-danger" data-bs-toggle="tooltip" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $profesion->id }}" 
                                      action="{{ route('profesion.destroy', $profesion->id) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <a href="{{ route('profesion.create', $profesion->persona->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Nueva Profesión
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                                <p>No se encontraron profesiones registradas.</p>
                                <a href="{{ route('profesion.create', $profesion->persona->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Registrar primera profesión
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $profesiones->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33'
    });
</script>
@endif

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Eliminar profesión?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endsection