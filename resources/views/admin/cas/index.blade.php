@extends('dashboard')

@section('contenido')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Lista de CAS</h2>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-4 mb-6">
        <input type="text" name="nombre" placeholder="Buscar por nombre" class="form-input border rounded px-3 py-1" value="{{ request('nombre') }}">
        <input type="date" name="fechaDesde" class="form-input border rounded px-3 py-1" value="{{ request('fechaDesde') }}">
        <input type="date" name="fechaHasta" class="form-input border rounded px-3 py-1" value="{{ request('fechaHasta') }}">
        <select name="estado" class="form-select border rounded px-3 py-1">
            <option value="">-- Estado --</option>
            <option value="1" @selected(request('estado') == '1')>Activo</option>
            <option value="0" @selected(request('estado') == '0')>Inactivo</option>
        </select>
        <button class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600" type="submit">Filtrar</button>
    </form>

    {{-- Tabla --}}
    <div class="overflow-auto">
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="px-4 py-2">Nombre</th>
                    <th class="px-4 py-2">Fecha Emisión</th>
                    <th class="px-4 py-2">Tiempo</th>
                    <th class="px-4 py-2">PDF</th>
                    <th class="px-4 py-2">Estado</th>
                    <th class="px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cas as $item)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $item->persona->nombre ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $item->fechaEmision }}</td>
                        <td class="px-4 py-2">{{ $item->anios }} años, {{ $item->meses }} meses, {{ $item->dias }} días</td>
                        <td class="px-4 py-2">
                            @if($item->pdfcas)
                                <a href="{{ asset('storage/' . $item->pdfcas) }}" target="_blank" class="text-blue-500 underline">Ver PDF</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-white {{ $item->estado ? 'bg-green-500' : 'bg-red-500' }}">
                                {{ $item->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 space-x-2">
                            <a href="{{ route('cas.edit', $item) }}" class="text-blue-600 hover:underline">Editar</a>
                            <form action="{{ route('cas.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este registro?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4">No se encontraron registros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
