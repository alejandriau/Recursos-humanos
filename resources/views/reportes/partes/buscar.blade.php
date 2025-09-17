@if (count($personas))
    @foreach ($personas as $persona)
        <tr>
            <td>{{ $persona->puestoActual->puesto->item ?? '' }}</td>
            <td>{{ $persona->puestoActual->puesto->nivelgerarquico ?? '' }}</td>
            <td>{{ $persona->apellidoPat }}</td>
            <td>{{ $persona->apellidoMat }}</td>
            <td>{{ $persona->nombre }}</td>
            <td>{{ $persona->ci }}</td>
            <td>{{ number_format($persona->puestoActual->puesto->haber ?? 0, 2, ',', '.') }}</td>
            <td>{{ !empty($persona->fechaIngreso) ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') : '' }}</td>
            <td>{{ !empty($persona->fechaNacimiento) ? \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y') : '' }}</td>
            <td>{{ $persona->profesion->provisionN ?? '' }}</td>
            <td>{{ !empty($persona->profesion->fechaProvision) ? \Carbon\Carbon::parse($persona->profesion->fechaProvision)->format('d/m/Y') : '' }}</td>
            <td>{{$persona->telefono }}</td>
            <td>
                <div class="dropdown">
                    <button class="btn text-dark fw-bold fs-4" type="button" id="dropdownMenu{{ $persona->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                        ⋮
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $persona->id }}">
                        <li><a class="dropdown-item" href="#">🔍 Ver</a></li>
                        <li><a class="dropdown-item" href="{{ route('personas.edit', $persona->id) }}">✏️ Editar</a></li>
                        <li><a class="dropdown-item" href="{{ route('personas.show', $persona->id) }}">✏️ ver</a></li>
                        <li>
                            <form action="{{ route('personas.destroy', $persona->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de desactivar a esta persona?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="dropdown-item text-danger">🗑️ Desactivar</button>
                            </form>
                        </li>
                        <li><a class="dropdown-item text-danger" href="{{ route('regisrar.archivos', $persona->id)}}">arch</a></li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr><td colspan="12">No se encontraron resultados.</td></tr>
@endif
