@extends('layouts.app')

@section('title', 'Gestión de documentos')

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-archive mr-2"></i>documentos
            </h1>
            <p class="text-muted">Gestiona todos tus documentos en un solo lugar</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('documentos.create') }}" class="btn btn-success btn-lg">
                <i class="fas fa-plus mr-2"></i>Nuevo Archivo
            </a>
            <a href="{{ route('documentos.estadisticas') }}" class="btn btn-info btn-lg">
                <i class="fas fa-chart-bar mr-2"></i>Estadísticas
            </a>
        </div>
    </div>

    <!-- Barra de búsqueda y filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter mr-2"></i>Filtros y Búsqueda
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('documentos.index') }}" class="row">
                <div class="col-md-4 mb-3">
                    <input type="text" name="busqueda" class="form-control" 
                           placeholder="Buscar por título, descripción..." 
                           value="{{ request('busqueda') }}">
                </div>
                
                <div class="col-md-3 mb-3">
                    <select name="tema" class="form-control">
                        <option value="">Todos los temas</option>
                        @foreach($temas as $tema)
                            <option value="{{ $tema }}" {{ request('tema') == $tema ? 'selected' : '' }}>
                                {{ $tema }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <select name="tipo" class="form-control">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo }}" {{ request('tipo') == $tipo ? 'selected' : '' }}>
                                {{ ucfirst($tipo) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
            
            @if(request()->has('busqueda') || request()->has('tema') || request()->has('tipo'))
                <div class="mt-3">
                    <a href="{{ route('documentos.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar filtros
                    </a>
                    <small class="text-muted ml-2">
                        Mostrando {{ $archivos->count() }} de {{ $archivos->total() }} documentos
                    </small>
                </div>
            @endif
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total documentos</h6>
                            <h3>{{ $archivos->total() }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-folder fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">documentos Hoy</h6>
                            <h3>{{ \App\Models\Archivo::whereDate('created_at', today())->count() }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Tipos Diferentes</h6>
                            <h3>{{ $tipos->count() }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Temas Diferentes</h6>
                            <h3>{{ $temas->count() }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de archivos -->
    @if($archivos->count() > 0)
        <div class="row">
            @foreach($archivos as $archivo)
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card file-card">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas {{ App\Http\Controllers\ArchivoController::getIcono($archivo->tipo) }} file-icon"></i>
                        </div>
                        
                        <h5 class="card-title">
                            <a href="{{ route('documentos.show', $archivo->id) }}" class="text-decoration-none text-dark">
                                {{ Str::limit($archivo->titulo, 40) }}
                            </a>
                        </h5>
                        
                        <p class="card-text text-muted small mb-2">
                            {{ Str::limit($archivo->descripcion, 80) }}
                        </p>
                        
                        <div class="mb-2">
                            <span class="badge badge-tema">{{ $archivo->tema }}</span>
                            <span class="badge badge-tipo">{{ $archivo->tipo }}</span>
                        </div>
                        
                        <div class="file-info small text-muted">
                            <div><i class="fas fa-file"></i> {{ strtoupper($archivo->extension) }}</div>
                            <div><i class="fas fa-hdd"></i> {{ number_format($archivo->tamano / 1024, 2) }} KB</div>
                            <div><i class="fas fa-calendar"></i> {{ $archivo->created_at->format('d/m/Y') }}</div>
                            @if($archivo->descargas > 0)
                                <div><i class="fas fa-download"></i> {{ $archivo->descargas }} descargas</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="file-actions">
                            <a href="{{ route('documentos.preview', $archivo->id) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               target="_blank"
                               title="Vista previa">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <a href="{{ route('documentos.download', $archivo->id) }}" 
                               class="btn btn-sm btn-outline-success"
                               title="Descargar">
                                <i class="fas fa-download"></i>
                            </a>
                            
                            <a href="{{ route('documentos.edit', $archivo->id) }}" 
                               class="btn btn-sm btn-outline-info"
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <form action="{{ route('documentos.destroy', $archivo->id) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm btn-outline-danger btn-delete"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Paginación -->
        <div class="d-flex justify-content-center">
            {{ $archivos->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
            <h3>No se encontraron documentos</h3>
            <p class="text-muted">
                @if(request()->has('busqueda') || request()->has('tema') || request()->has('tipo'))
                    Intenta con otros filtros de búsqueda
                @else
                    Comienza subiendo tu primer archivo
                @endif
            </p>
            <a href="{{ route('documentos.create') }}" class="btn btn-primary btn-lg mt-3">
                <i class="fas fa-upload mr-2"></i>Subir Archivo
            </a>
        </div>
    @endif
</div>
@endsection