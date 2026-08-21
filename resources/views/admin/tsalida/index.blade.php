@extends('layouts.baseadm')

@section('title', 'Tipo de Salidas')

@section('content_header')
    <div class="alert alert-secondary" role="alert">
        <div class="row justify-content-start">
            <div class="col-9">
                <b>GESTIÓN DE TIPOS DE SALIDA</b>
            </div>
            <div class="col-3 text-primary d-flex justify-content-end">
                <i class="fa-solid fa-user fs-4"></i>&nbsp;{{ auth()->user()->name }}
            </div>
        </div>
    </div>
@stop

@section('contenido')

    {{-- Botón para abrir modal de creación --}}
    <button type="button" class="btn btn-primary w-25 mb-3" data-bs-toggle="modal" data-bs-target="#crearModal">
        <i class="fa-solid fa-plus"></i> Añadir tipo de salida
    </button>

    {{-- ==================== TABLA COMPACTA ==================== --}}
    <div class="container-fluid mt-2 pt-3 pb-2 rounded shadow bg-white">
        <h5 class="text-center mb-3">
            <span class="text-secondary">Lista de registros - Tipo de salidas</span>
        </h5>

        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Sustento</th>
                        <th>Expresa</th>
                        <th>Dependencia</th>
                        <th>Cupo</th>
                        <th>Unidad</th>
                        <th>Periodicidad</th>
                        <th>Cant.</th>
                        <th>Arrastre</th>
                        <th>Máx.</th>
                        <th>Antigüedad</th>
                        <th>Aprob.</th>
                        <th>Estado</th>
                        <th>Modificar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tipoSal as $sal)
                        <tr>
                            <td><b>{{ $sal->id }}</b></td>
                            <td>{{ $sal->descripcion }}</td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 120px;" title="{{ $sal->sustLegal }}">
                                    {{ $sal->sustLegal }}
                                </span>
                            </td>
                            <td>{{ $sal->expresa }}</td>
                            <td>
                                @if($sal->id_padre)
                                    <span class="badge bg-info">{{ $sal->padre->descripcion ?? $sal->id_padre }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($sal->tiene_cupo)
                                    <span class="badge bg-success"><i class="fa-solid fa-check"></i> Sí</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fa-solid fa-xmark"></i> No</span>
                                @endif
                            </td>
                            <td>{{ $sal->unidad ?? '-' }}</td>
                            <td>
                                <span class="badge 
                                    @if($sal->periodicidad == 'mensual') bg-primary
                                    @elseif($sal->periodicidad == 'anual') bg-success
                                    @elseif($sal->periodicidad == 'evento') bg-warning text-dark
                                    @else bg-light text-dark border
                                    @endif">
                                    {{ ucfirst($sal->periodicidad) }}
                                </span>
                            </td>
                            <td>{{ $sal->cantidad_default ?? '-' }}</td>
                            <td>
                                @if($sal->permite_arrastre)
                                    <i class="fa-solid fa-rotate text-success" title="Sí permite arrastre"></i>
                                @else
                                    <i class="fa-solid fa-xmark text-muted" title="No permite arrastre"></i>
                                @endif
                            </td>
                            <td>{{ $sal->max_veces_periodo ?? '∞' }}</td>
                            <td>
                                @if($sal->usa_tabla_antiguedad)
                                    <span class="badge bg-warning text-dark">Sí</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1" style="font-size: 0.75rem;">
                                    @if($sal->requiere_aprobacion_jefe)
                                        <span class="badge bg-primary"><i class="fa-solid fa-user-tie"></i> Jefe</span>
                                    @endif
                                    @if($sal->requiere_aprobacion_rrhh)
                                        <span class="badge bg-secondary"><i class="fa-solid fa-users"></i> RRHH</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($sal->activo)
                                    <span class="badge bg-success"><i class="fa-solid fa-power-off"></i> Activo</span>
                                @else
                                    <span class="badge bg-danger"><i class="fa-solid fa-power-off"></i> Inactivo</span>
                                @endif
                            </td>
                            <td>
                                {{-- Botón que abre el modal de edición con los datos del registro --}}
                                <button type="button" class="btn btn-warning btn-sm btn-editar"
                                    data-id="{{ $sal->id }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editarModal">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </td>
                            <td>
                                <form action="{{ route('gestion.destroy', $sal) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('¿Está seguro de eliminar este tipo de salida?')"
                                        type="submit" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $tipoSal->links() }}
        </div>
    </div>

    {{-- ==================== MODAL CREAR ==================== --}}
{{-- Modal Crear --}}
<div class="modal fade" id="crearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa-solid fa-plus"></i> Nuevo Tipo de Salida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('gestion.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('admin.tsalida._form', ['tipoSal' => $tipoSal, 'edit' => false, 'tiposalida' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar --}}
<div class="modal fade" id="editarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fa-regular fa-pen-to-square"></i> Editar Tipo de Salida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div id="contenido-editar">
                        @include('admin.tsalida._form', ['tipoSal' => $tipoSal, 'edit' => true, 'tiposalida' => null])
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="fa-regular fa-floppy-disk"></i> Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        /* Estilos compactos */
        .form-floating > .form-control,
        .form-floating > .form-select {
            height: calc(2.5rem + 2px);
            padding: 0.5rem 0.75rem;
        }
        .form-floating > label {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
        .table td, .table th {
            padding: 0.4rem 0.5rem;
            vertical-align: middle;
        }
        @media (max-width: 768px) {
            .table td .badge .fa-solid {
                margin-right: 0;
            }
            .table td .badge {
                font-size: 0.7rem;
            }
        }
    </style>
@stop

@push('scripts')
    {{-- Mensajes de sesión (con SweetAlert) --}}
    @if (session('status'))
        <script>
            Swal.fire({
                text: "Tipo de salida Registrado",
                icon: "success",
                confirmButtonText: "Aceptar",
                color: "#0e47ec",
            });
        </script>
    @endif
    @if (session('upstatus'))
        <script>
            Swal.fire({
                text: "Tipo de salida Modificado",
                icon: "success",
                color: "#FFB300",
                confirmButtonText: "Aceptar",
                confirmButtonColor: "#FFB300"
            });
        </script>
    @endif
    @if (session('delstatus'))
        <script>
            Swal.fire({
                text: "Tipo de salida Eliminado",
                icon: "success",
                color: "#dc3545",
                confirmButtonText: "Aceptar",
                confirmButtonColor: "#dc3545"
            });
        </script>
    @endif


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Editar - cargar datos con Axios
            document.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const url = "{{ route('gestion.edit', ':id') }}".replace(':id', id);

                    axios.get(url)
                        .then(response => {
                            const data = response.data;
                            const form = document.getElementById('formEditar');
                            form.action = "{{ route('gestion.update', ':id') }}".replace(':id', id);

                            // Campos de texto
                            document.getElementById('edit_descripcion').value = data.descripcion || '';
                            document.getElementById('edit_sustLegal').value = data.sustLegal || '';
                            document.getElementById('edit_expresa').value = data.expresa || '';
                            document.getElementById('edit_id_padre').value = data.id_padre || '';

                            // Campos de cupo y configuración
                            document.getElementById('edit_unidad').value = data.unidad || '';
                            document.getElementById('edit_periodicidad').value = data.periodicidad || 'ninguna';
                            document.getElementById('edit_cantidad_default').value = data.cantidad_default || '';
                            document.getElementById('edit_max_veces_periodo').value = data.max_veces_periodo || '';

                            // Checkboxes
                            document.getElementById('edit_tiene_cupo').checked = !!data.tiene_cupo;
                            document.getElementById('edit_permite_arrastre').checked = !!data.permite_arrastre;
                            document.getElementById('edit_usa_tabla_antiguedad').checked = !!data.usa_tabla_antiguedad;
                            document.getElementById('edit_requiere_aprobacion_jefe').checked = !!data.requiere_aprobacion_jefe;
                            document.getElementById('edit_requiere_aprobacion_rrhh').checked = !!data.requiere_aprobacion_rrhh;
                            document.getElementById('edit_activo').checked = !!data.activo;
                        })
                        .catch(error => {
                            console.error('Error al cargar datos:', error);
                            alert('No se pudieron cargar los datos. Revisa la consola.');
                        });
                });
            });
        });
    </script>
@endpush