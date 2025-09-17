@extends('dashboard')

@section('contenido')
<div class="container py-4">
    <h2 class="text-2xl font-bold mb-4">📋 Lista de Puestos</h2>

    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Botón y filtros --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <a href="{{ route('puesto.create')}}" class="btn btn-success">
            ➕ Nuevo Puesto
        </a>

        <form method="GET" action="{{ route('puesto') }}" class="d-flex gap-2">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por denominación..." value="{{ request('buscar') }}">
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
            </select>
            <button type="submit" class="btn btn-outline-primary">Filtrar</button>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Denominación</th>
                    <th>Nivel Jerárquico</th>
                    <th>ITEM</th>
                    <th>Nivel</th>
                    <th>Haber</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($puestos as $puesto)
                <tr>
                    <td>{{ $puesto->id }}</td>
                    <td>{{ $puesto->denominacion }}</td>
                    <td>{{ $puesto->nivelgerarquico }}</td>
                    <td>{{ $puesto->item }}</td>
                    <td>{{ $puesto->nivel }}</td>
                    <td>Bs {{ number_format($puesto->haber, 2, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $puesto->estado ? 'bg-success' : 'bg-secondary' }}">
                            {{ $puesto->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('edit', $puesto->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form action="{{ route('puesto.destroy', $puesto->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">No se encontraron resultados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>


</div>
@endsection
