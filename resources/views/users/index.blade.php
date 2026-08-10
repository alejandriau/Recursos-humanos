@extends('layouts.baseadm')

@section('contenido')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-dark">Gestión de Usuarios</h1>
        @can('crear usuarios')
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuevo Usuario
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <!-- Formulario de búsqueda y filtro -->
    <form method="GET" action="{{ route('users.index') }}" class="row g-3 mb-3">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, email o CI..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Todos</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-secondary w-100">
                <i class="fas fa-search me-1"></i> Filtrar
            </button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>CI (Usuario)</th>
                        <th>Roles</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="{{ $user->trashed() ? 'table-secondary' : '' }}">
                            <td>{{ $user->id }}</td>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->usuario }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($user->trashed())
                                    <span class="badge bg-danger">Inactivo</span>
                                @else
                                    <span class="badge bg-success">Activo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <!-- Ver -->
                                    @can('ver usuarios')
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan

                                    <!-- Editar -->
                                    @can('editar usuarios')
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan

                                    <!-- Asignar roles -->
                                    @can('asignar roles a usuarios')
                                        <a href="{{ route('users.roles.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Roles">
                                            <i class="fas fa-user-tag"></i>
                                        </a>
                                    @endcan

                                    <!-- Resetear contraseña -->
                                    @can('asignar roles a usuarios')
                                        <form action="{{ route('users.reset-password', $user) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Restablecer contraseña al CI ({{ $user->usuario }})?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-info" title="Resetear contraseña">
                                                <i class="fas fa-key"></i>
                                            </button>
                                        </form>
                                    @endcan

                                    @if($user->trashed())
                                        <!-- Reactivar -->
                                        @can('restaurar usuarios')
                                            <form action="{{ route('users.restore', $user) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('¿Reactivar este usuario?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Reactivar">
                                                    <i class="fas fa-undo-alt"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    @else
                                        <!-- Desactivar (soft delete) -->
                                        @can('eliminar usuarios')
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('¿Desactivar este usuario? (No podrá acceder)')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Desactivar">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection