@extends('dashboard')

@section('contenido')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📄 Lista de Certificados</h2>
        <div>
            <a href="{{ route('certificados.reporte-vencimientos') }}" class="btn btn-warning me-2">
                ⚠️ Reporte de Vencimientos
            </a>
            <a href="{{ route('certificados.create') }}" class="btn btn-success">➕ Nuevo Certificado</a>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" action="{{ route('certificados.index') }}" class="row g-3 mb-4 bg-light p-3 rounded shadow-sm">
        <div class="col-md-3">
            <label for="buscar" class="form-label">Buscar por persona o certificado</label>
            <input type="text" name="buscar" class="form-control" placeholder="Ej: Juan Pérez o Excel Básico" value="{{ request('buscar') }}">
        </div>

        <div class="col-md-2">
            <label for="categoria" class="form-label">Categoría</label>
            <select name="categoria" class="form-select">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria }}" {{ request('categoria') == $categoria ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $categoria)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label for="estado_vencimiento" class="form-label">Estado</label>
            <select name="estado_vencimiento" class="form-select">
                <option value="">Todos</option>
                <option value="vigentes" {{ request('estado_vencimiento') == 'vigentes' ? 'selected' : '' }}>Vigentes</option>
                <option value="por_vencer" {{ request('estado_vencimiento') == 'por_vencer' ? 'selected' : '' }}>Por vencer (30 días)</option>
                <option value="vencidos" {{ request('estado_vencimiento') == 'vencidos' ? 'selected' : '' }}>Vencidos</option>
                <option value="sin_vencimiento" {{ request('estado_vencimiento') == 'sin_vencimiento' ? 'selected' : '' }}>Sin vencimiento</option>
            </select>
        </div>

        <div class="col-md-2">
            <label for="desde" class="form-label">Desde</label>
            <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
        </div>

        <div class="col-md-2">
            <label for="hasta" class="form-label">Hasta</label>
            <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
        </div>

        <div class="col-md-1 d-flex align-items-end">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
                <a href="{{ route('certificados.index') }}" class="btn btn-secondary">❌ Limpiar</a>
            </div>
        </div>
    </form>

    <!-- Tabla de resultados -->
    <div class="table-responsive shadow-sm">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>Persona</th>
                    <th>Nombre del Certificado</th>
                    <th>Categoría</th>
                    <th>Tipo</th>
                    <th>Fecha Emisión</th>
                    <th>Fecha Vencimiento</th>
                    <th>Instituto</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($certificados as $cert)
                    @php
                        $estadoClass = '';
                        $estadoText = 'Vigente';

                        if ($cert->fecha_vencimiento) {
                            if ($cert->fecha_vencimiento->isPast()) {
                                $estadoClass = 'bg-danger text-white';
                                $estadoText = 'Vencido';
                            } elseif ($cert->fecha_vencimiento->diffInDays(now()) <= 30) {
                                $estadoClass = 'bg-warning';
                                $estadoText = 'Por vencer';
                            }
                        } else {
                            $estadoClass = 'bg-success text-white';
                            $estadoText = 'Sin vencimiento';
                        }
                    @endphp
                    <tr>
                        <td>
                            {{ $cert->persona->nombre }} {{ $cert->persona->apellidoPat }} {{ $cert->persona->apellidoMat }}
                        </td>
                        <td>{{ $cert->nombre }}</td>
                        <td>
                            <span class="badge bg-info">
                                {{ ucfirst(str_replace('_', ' ', $cert->categoria)) }}
                            </span>
                        </td>
                        <td>{{ $cert->tipo }}</td>
                        <td>{{ $cert->fecha ? \Carbon\Carbon::parse($cert->fecha)->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if($cert->fecha_vencimiento)
                                <span class="{{ $cert->fecha_vencimiento->isPast() ? 'text-danger fw-bold' : '' }}">
                                    {{ $cert->fecha_vencimiento->format('d/m/Y') }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $cert->instituto }}</td>
                        <td class="text-center">
                            <span class="badge {{ $estadoClass }} p-2">
                                {{ $estadoText }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                @if($cert->pdfcerts)
                                    <a href="{{ Storage::url($cert->pdfcerts) }}" target="_blank" class="btn btn-sm btn-info" title="Ver PDF">
                                        📄 Ver
                                    </a>
                                @endif
                                <a href="{{ route('certificados.edit', $cert->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                    ✏️
                                </a>
                                <form action="{{ route('certificados.destroy', $cert->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este certificado?')" title="Eliminar">
                                        🗑️
                                    </button>
                                </form>
                                <a href="{{ route('certificados.create', ['idPersona' => $cert->idPersona]) }}" class="btn btn-sm btn-secondary" title="Agregar otro">
                                    ➕
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No se encontraron certificados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
