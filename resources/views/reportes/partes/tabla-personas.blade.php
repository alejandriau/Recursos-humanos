<table class="table table-striped small" style="width:100%">
    <thead>
        <tr>
            <th>ITEM</th>
            <th>UNIDAD</th>
            <th>NIVEL GERARQUICO</th>
            <th>APELLIDO 1</th>
            <th>APELLIDO 2</th>
            <th>NOMBRE</th>
            <th>CI</th>
            <th>HABER</th>
            <th>FECHA INGRESO</th>
            <th>FECHA NACIMIENTO</th>
            <th>TITULO PROVISION NACIONAL</th>
            <th>FECHA TITULO</th>
            <th>TELEFONO</th>
            <th>ACCIONES</th>
        </tr>
    </thead>
    <tbody id="table-body">
        @forelse ($personas as $persona)
            <tr>
                <td>{{ $persona->puestoActual->puesto->item ?? '' }}</td>
                <td>{{ $persona->puestoActual->puesto->unidadOrganizacional->nombre ?? '' }}</td>
                <td>{{ $persona->puestoActual->puesto->nivelJerarquico ?? '' }}</td>
                <td>{{ $persona->apellidoPat }}</td>
                <td>{{ $persona->apellidoMat }}</td>
                <td>{{ $persona->nombre }}</td>
                <td>{{ $persona->ci }}</td>
                <td>{{ number_format($persona->puestoActual->puesto->haber ?? 0, 2, ',', '.') }}</td>
                <td>{{ !empty($persona->fechaIngreso) ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') : '' }}</td>
                <td>{{ !empty($persona->fechaNacimiento) ? \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y') : '' }}</td>
                <td>{{ $persona->profesion->provisionN ?? '' }}</td>
                <td>{{ !empty($persona->profesion->fechaProvision) ? \Carbon\Carbon::parse($persona->profesion->fechaProvision)->format('d/m/Y') : '' }}</td>
                <td>{{ $persona->telefono }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn text-dark fw-bold fs-4" type="button" id="dropdownMenu{{ $persona->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                            ⋮
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $persona->id }}">
                            <li><a class="dropdown-item" href="{{ route('personas.show', $persona->id) }}">🔍 Ver</a></li>
                            <li><a class="dropdown-item" href="{{ route('personas.edit', $persona->id) }}">✏️ Editar</a></li>
                            <li>
                                <form action="{{ route('personas.destroy', $persona->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de desactivar a esta persona?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="dropdown-item text-danger">🗑️ Desactivar</button>
                                </form>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('regisrar.archivos', $persona->id)}}">📁 Archivos</a></li>
                            <li><a class="dropdown-item" href="{{ route('persona.dashboard', $persona->id)}}">📊 Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('personas.historial', $persona->id)}}">📋 Historial Puestos</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="14" class="text-center text-muted py-4">
                    <i class="fa fa-search fa-2x mb-2"></i><br>
                    No se encontraron resultados con los filtros aplicados.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if($personas->count() > 0)
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
        Mostrando {{ $personas->count() }} registros
    </div>
</div>
@endif
