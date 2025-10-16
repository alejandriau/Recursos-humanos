@extends('dashboard')

@section('title', 'Gestión de Roles')

@section('contenido')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-dark">Gestión de Roles</h1>
        @can('crear_roles')
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuevo Rol
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Permisos</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td class="fw-semibold">{{ ucfirst($role->name) }}</td>
                            <td>
                                @if($role->permissions->count() > 0)
                                    @foreach($role->permissions as $permission)
                                        <span class="badge bg-success">{{ $permission->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Sin permisos</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @can('editar_roles')
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-warning" title="Editar Rol">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan

                                    @can('gestionar_permisos_roles')
                                        <a href="{{ route('roles.permissions.edit', $role) }}" class="btn btn-sm btn-success" title="Gestionar Permisos">
                                            <i class="fas fa-key"></i>
                                        </a>
                                    @endcan

                                    @can('eliminar_roles')
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este rol?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar Rol">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan

                                    @cannot(['editar_roles', 'gestionar_permisos_roles', 'eliminar_roles'])
                                        <span class="text-muted">Sin acceso</span>
                                    @endcannot
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $roles->links() }}
    </div>
</div>
@endsection
