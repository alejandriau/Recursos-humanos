@extends('dashboard')

@section('title', 'Editar Roles de Usuario')

@section('contenido')
<div class="container mt-4">
    <div class="mb-3">
        <a href="{{ route('users.index') }}" class="btn btn-link text-decoration-none">
            <i class="fas fa-arrow-left me-2"></i> Volver a la lista de usuarios
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 fw-bold text-dark">Editar Roles para: {{ $user->name }}</h2>
                <span class="text-muted small">{{ $user->email }}</span>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            @can('asignar roles a usuarios')
                <form action="{{ route('users.roles.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <h5 class="fw-semibold mb-3">Selecciona los roles para este usuario:</h5>

                        <div class="row">
                            @foreach($roles as $role)
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="role-{{ $role->id }}" name="roles[]"
                                               value="{{ $role->name }}"
                                               {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="role-{{ $role->id }}">
                                            {{ ucfirst($role->name) }}
                                        </label>
                                    </div>
                                    @if($role->permissions->count() > 0)
                                        <small class="text-muted">
                                            Incluye: {{ $role->permissions->take(3)->pluck('name')->implode(', ') }}
                                            @if($role->permissions->count() > 3)
                                                y {{ $role->permissions->count() - 3 }} más...
                                            @endif
                                        </small>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @error('roles')
                            <p class="text-danger small">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Guardar Roles
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No tienes permiso para asignar roles a usuarios.
                </div>
            @endcan
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h5 class="fw-semibold mb-3">Roles Actualmente Asignados</h5>

            @if($user->roles->count() > 0)
                <div class="d-flex flex-wrap gap-2">
                    @foreach($user->roles as $role)
                        <span class="badge bg-primary d-flex align-items-center">
                            {{ $role->name }}
                            @can('asignar roles a usuarios')
                                <form action="{{ route('users.roles.remove', [$user, $role]) }}" method="POST" class="ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-white p-0"
                                            onclick="return confirm('¿Quitar este rol al usuario?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endcan
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-muted">Este usuario no tiene roles asignados.</p>
            @endif
        </div>
    </div>
</div>

<script>
    // Validación con JS para evitar guardar sin roles
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const checkedRoles = document.querySelectorAll('input[name="roles[]"]:checked');
                if (checkedRoles.length === 0) {
                    e.preventDefault();
                    alert('Debes seleccionar al menos un rol para el usuario.');
                }
            });
        }
    });
</script>
@endsection
