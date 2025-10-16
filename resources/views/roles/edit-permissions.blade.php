@extends('dashboard')

@section('contenido')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Asignar Permisos al Rol: {{ $role->name }}</h4>
                    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Volver a Roles
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('roles.permissions.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                                    <label class="form-check-label fw-bold" for="selectAllPermissions">
                                        Seleccionar/Deseleccionar Todos los Permisos
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @foreach($permissions->chunk(ceil($permissions->count() / 3)) as $chunk)
                            <div class="col-md-4">
                                @foreach($chunk as $permission)
                                <div class="card permission-card mb-3">
                                    <div class="card-body py-2">
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox"
                                                   type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission->id }}"
                                                   id="permission_{{ $permission->id }}"
                                                   {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="permission_{{ $permission->id }}">
                                                <span class="d-block fw-medium">{{ $permission->name }}</span>
                                                <small class="text-muted">{{ $permission->created_at->format('d/m/Y') }}</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <button type="reset" class="btn btn-light me-2">
                                    <i class="fas fa-undo me-1"></i> Restablecer
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Permisos
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionar/deseleccionar todos los permisos
        const selectAll = document.getElementById('selectAllPermissions');
        const checkboxes = document.querySelectorAll('.permission-checkbox');

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        });

        // Verificar si todos los checkboxes están seleccionados al cargar la página
        const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
        selectAll.checked = allChecked;

        // Actualizar el estado de "Seleccionar todos" cuando cambie cualquier checkbox
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                selectAll.checked = allChecked;
            });
        });
    });
</script>
@endpush
