@extends('dashboard') {{-- Usa tu layout principal si tienes uno --}}

@section('contenido')
    <div class="container py-4">
        <h2 class="mb-4 text-center text-uppercase fw-bold">Listado de Bajas de Personal</h2>

        <!-- Filtros -->
        <form method="GET" action="{{ route('bajasaltas.index') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre"
                    value="{{ request('nombre') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100">Filtrar</button>
            </div>
        </form>

        <!-- Tabla -->
        <div class="table-responsive">
            <table class="table table-hover align-middle text-sm">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Ingreso</th>
                        <th>Baja</th>
                        <th>Motivo</th>
                        <th>Observación</th>
                        <th>Tiempo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bajas as $key => $baja)
                        <tr>
                            <td>{{ $baja['nombre'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($baja['fecha_ingreso'])->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($baja['fecha_baja'])->format('d/m/Y') }}</td>
                            <td>{{ $baja['motivo'] }}</td>
                            <td>{{ $baja['observacion'] }}</td>
                            <td>{{ $baja['tiempo_en_institucion'] }}</td>
                            <td class="text-center">
                                <!-- Botón que abre el modal -->
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalEditar{{ $key }}">
                                    Editar
                                </button>
                            </td>
                        </tr>

                        <!-- Modal para editar -->
                        <div class="modal fade" id="modalEditar{{ $key }}" tabindex="-1" aria-labelledby="modalLabel{{ $key }}"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('bajasaltas.update', $baja['id']) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel{{ $key }}">
                                                Editar baja de <strong>{{ $baja['nombre'] }}</strong><br>
                                                <small class="text-muted">Fecha actual de baja:
                                                    {{ \Carbon\Carbon::parse($baja['fecha_baja'])->format('d/m/Y') }}</small>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="fecha_baja{{ $key }}" class="form-label">Fecha de baja</label>
                                                <input type="date" name="fecha_baja" id="fecha_baja{{ $key }}"
                                                    class="form-control" value="{{ $baja['fecha_baja'] }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="motivo{{ $key }}" class="form-label">Motivo</label>
                                                <input type="text" name="motivo" id="motivo{{ $key }}" class="form-control"
                                                    value="{{ $baja['motivo'] }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="observaciones{{ $key }}" class="form-label">Observación</label>
                                                <textarea name="observaciones" id="observaciones{{ $key }}" class="form-control"
                                                    rows="3">{{ $baja['observacion'] }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay datos.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
@endsection