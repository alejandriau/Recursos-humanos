@extends('dashboard')

@section('contenido')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📄 Lista de Certificados</h2>
        <a href="{{ route('certificados.create') }}" class="btn btn-success">➕ Nuevo Certificado</a>
    </div>



    <!-- Filtros -->
    <form method="GET" action="{{ route('certificados.index') }}" class="row g-3 mb-4 bg-light p-3 rounded shadow-sm">
        <div class="col-md-4">
            <label for="buscar" class="form-label">Buscar por persona o certificado</label>
            <input type="text" name="buscar" class="form-control" placeholder="Ej: Juan Pérez o Excel Básico" value="{{ request('buscar') }}">
        </div>

        <div class="col-md-4">
            <label for="desde" class="form-label">Desde</label>
            <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
        </div>

        <div class="col-md-4">
            <label for="hasta" class="form-label">Hasta</label>
            <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
        </div>

        <div class="col-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary me-2">🔍 Filtrar</button>
            <a href="{{ route('certificados.index') }}" class="btn btn-secondary">❌ Limpiar</a>
        </div>
    </form>

    <!-- Tabla de resultados -->
    <div class="table-responsive shadow-sm">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>Persona</th>
                    <th>Nombre del Certificado</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Instituto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($certificados as $cert)
                    <tr>
                        <td>
                            {{ $cert->persona->nombre }} {{ $cert->persona->apellidoPat }} {{ $cert->persona->apellidoMat }}
                        </td>
                        <td>{{ $cert->nombre }}</td>
                        <td>{{ $cert->tipo }}</td>
                        <td>{{\Carbon\Carbon::parse($cert->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $cert->instituto }}</td>
                        <td class="text-center">
                            <a href="{{ route('certificados.edit', $cert->id) }}" class="btn btn-sm btn-warning mb-1">✏️ Editar</a>

                            <form action="{{ route('certificados.destroy', $cert->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('¿Eliminar este certificado?')">
                                    🗑️ Eliminar
                                </button>
                            </form>

                            <!-- Agregar otro certificado a la misma persona -->
                            <a href="{{ route('certificados.create', ['idPersona' => $cert->idPersona]) }}" class="btn btn-sm btn-info">
                                ➕ Otro para esta persona
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No se encontraron certificados.</td>
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
    
</style>
@endsection
