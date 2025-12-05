<table class="table table-striped small" style="width:100%">
    <thead>
        <tr>
            <th>N°</th>
            <th>APELLIDO 1</th>
            <th>APELLIDO 2</th>
            <th>NOMBRE</th>
            <th>CI</th>
            <!--<th>HABER</th>-->
            <th>FECHA INGRESO</th>
            <th>FECHA NACIMIENTO</th>
            <th>TITULO PROVISION NACIONAL</th>
            <th>FECHA TITULO</th>
            <th>TELEFONO</th>
            <th>ESTADO ACTUAL</th>
            <th>ACCIONES</th>
        </tr>
    </thead>
    <tbody id="table-body">
        @forelse ($personas as $persona)
            <tr>
                <!-- Numeración con paginación -->
                <td>{{ ($personas->currentPage() - 1) * $personas->perPage() + $loop->iteration }}</td>

                <!-- Información Personal -->
                <td>{{ $persona->apellidoPat }}</td>
                <td>{{ $persona->apellidoMat }}</td>
                <td>{{ $persona->nombre }}</td>
                <td>{{ $persona->ci }}</td>

                <!-- Haber -->
                <!--<td>
                    @if($persona->puestoActual && $persona->puestoActual->puesto)
                        {{ number_format($persona->puestoActual->puesto->haber ?? 0, 2, ',', '.') }}
                    @else
                        <span class="text-muted">0,00</span>
                    @endif
                </td>-->

                <!-- Fechas -->
                <td>{{ !empty($persona->fechaIngreso) ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') : '' }}</td>
                <td>{{ !empty($persona->fechaNacimiento) ? \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y') : '' }}</td>

                <!-- Información Profesional -->
                <td>{{ $persona->profesion->provisionN ?? '' }}</td>
                <td>{{ !empty($persona->profesion->fechaProvision) ? \Carbon\Carbon::parse($persona->profesion->fechaProvision)->format('d/m/Y') : '' }}</td>
                <td>{{ $persona->telefono }}</td>

                <!-- Estado Actual -->
                <td>
                    @if($persona->puestoActual)
                        @if($persona->puestoActual->estado == 'activo')
                            <span class="badge bg-success" title="Desde {{ \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') }}">
                                Activo
                            </span>
                        @elseif($persona->puestoActual->estado == 'concluido')
                            <span class="badge bg-secondary" title="Finalizó: {{ $persona->puestoActual->fecha_fin ? $persona->puestoActual->fecha_fin->format('d/m/Y') : 'N/A' }}">
                                Concluido
                            </span>
                        @else
                            <span class="badge bg-warning">{{ ucfirst($persona->puestoActual->estado) }}</span>
                        @endif
                    @else
                        <span class="badge bg-light text-dark">Sin puesto</span>
                    @endif
                </td>

                <!-- Acciones -->
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
                            <li><a class="dropdown-item" href="{{ route('persona.dashboard', $persona->id)}}">📊 Documentación</a></li>
                            <li><a class="dropdown-item" href="{{ route('personas.historial', $persona->id)}}">
                                📋 Historial
                                @if($persona->historials->count() > 0)
                                    <span class="badge bg-primary">{{ $persona->historials->count() }}</span>
                                @endif
                            </a></li>

                            <!-- Información rápida del historial -->
                            @if($persona->historials->count() > 0)
                                <li><hr class="dropdown-divider"></li>
                                <li class="dropdown-header text-primary">Últimos puestos:</li>
                                @foreach($persona->historials->sortByDesc('fecha_inicio')->take(2) as $historial)
                                    <li>
                                        <a class="dropdown-item text-wrap small" href="#" title="{{ $historial->puesto->denominacion ?? 'N/A' }}">
                                            <div>
                                                <strong>{{ $historial->puesto->item ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($historial->fecha_inicio)->format('d/m/Y') }}
                                                    @if($historial->fecha_fin)
                                                        - {{ \Carbon\Carbon::parse($historial->fecha_fin)->format('d/m/Y') }}
                                                    @else
                                                        - Actual
                                                    @endif
                                                </small>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="13" class="text-center text-muted py-4">
                    <i class="fa fa-search fa-2x mb-2"></i><br>
                    No se encontraron resultados con los filtros aplicados.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if($personas->count() > 0)
<div class="d-flex justify-content-between align-items-center mt-3">
    <!-- Información de registros -->
    <div class="text-muted">
        Mostrando {{ $personas->firstItem() }} a {{ $personas->lastItem() }} de {{ $personas->total() }} registros
        @php
            $conPuesto = 0;
            $sinPuesto = 0;
            foreach($personas as $persona) {
                if($persona->puestoActual) {
                    $conPuesto++;
                } else {
                    $sinPuesto++;
                }
            }
        @endphp
        • Con puesto: {{ $conPuesto }} • Sin puesto: {{ $sinPuesto }}
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center">
        {{ $personas->onEachSide(1)->links() }}
    </div>

    <!-- Información de página -->
    <div class="text-muted">
        Página {{ $personas->currentPage() }} de {{ $personas->lastPage() }}
    </div>
</div>
@endif
