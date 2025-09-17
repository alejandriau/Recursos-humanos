@extends('dashboard') {{-- Ajusta esto si usas otro layout --}}

@section('contenido')
<div class="container mt-4">
    <h2 class="mb-4 text-center">Listado de Personas y sus Profesiones</h2>

    <!-- Filtro -->
    <form method="GET" action="{{ route('profesion.index') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, apellido o título..." value="{{ request('buscar') }}">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
    </form>

    <!-- Tabla -->
    <div class="table-responsive shadow-sm rounded border">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nº</th>
                    <th>Persona</th>
                    <th>Diploma</th>
                    <th>Fecha</th>
                    <th>Titulo Provison Nacn.</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($profesiones as $profesion)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($profesion->persona)
                                {{ $profesion->persona->nombre }} 
                                {{ $profesion->persona->apellidoPat }} 
                                {{ $profesion->persona->apellidoMat }}
                            @else
                                <span class="text-muted">Sin persona asignada</span>
                            @endif
                        </td>
                        <td>{{ $profesion->diploma ?? '—' }}</td>
                        <td>{{ $profesion->fechaDiploma ?? '—' }}</td>
                        <td>{{ $profesion->provisionN ?? '—' }}</td>
                        <td>
                            {{ $profesion->fechaProvision 
                                ? \Carbon\Carbon::parse($profesion->fechaProvision)->format('d/m/Y') 
                                : '—' }}
                        </td>
                        <td>
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('profesion.edit', $profesion->id) }}" class="btn btn-sm btn-warning">
                                    Editar
                                </a>
                            @if ($profesion->persona)
                                <a href="{{ route('profesion.create', ['persona' => $profesion->persona->id]) }}" class="btn btn-sm btn-success">
                                    Agregar+
                                </a>
                            @else
                                <span class="text-danger">Sin persona</span>
                            @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No se encontraron resultados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
        timer: 1500,
        timerProgressBar: true,
        background: '#007BFF', // azul
        color: '#fff', // texto blanco
        customClass: {
            popup: 'custom-toast'
        },
    });
</script>
@endif

<!-- Estilos personalizados -->
<style>
    .swal2-popup.custom-toast {
        width: 300px !important;
        height: 80px !important;
        border-radius: 12px;
        font-size: 16px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .d-inline-flex.gap-2 {
        gap: 0.5rem;
    }
</style>
@endsection