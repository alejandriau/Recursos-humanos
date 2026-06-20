@extends('layouts.baseadm')
@section('title', 'vacaciones')
@section('plugins.Sweetalert2', true)
@section('content_header')
    <div class="alert alert-secondary" role="alert">
        <div class="row justify-content-start">
            <div class="col-9">

                <b>LISTA DE SALIDAS EN ESPERA</b>
            </div>
            <div class="col-3 text-primary d-flex justify-content-end">
                <i class="fa-solid fa-user fs-4"></i>&nbsp;{{ auth()->user()->name }}
            </div>
        </div>
    </div>
@stop
@section('contenido')

    <div class="container-fluid pb-4 mt-2 pt-4 rounded shadow bg-white ">
        <h5 style="text-align: center"><span class=" text-secondary"> Validar o rechazar salidas </span></h5>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tipo Salida</th>
                    <th>Nombre</th>
                    <th>Cargo</th>
                    <th>Dependencia</th>
                    <th>Fecha Solicitud</th>
                    <th>Salida</th>
                    <th>Retorno</th>
                    <th>Cantidad</th>
                    <th>Motivo</th>
                    <th>VoBo</th>
                    <th>Observación</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportes as $salida)
                    <tr>
                        <td>{{ $salida->tiposalida->descripcion ?? '' }}</td>
                        <td>{{ $salida->personal->nombre ?? '' }} {{ $salida->personal->apellidopat ?? '' }}
                            {{ $salida->personal->apellidomat ?? '' }}</td>
                        <td>{{ $salida->personal->kardex->cargo ?? '' }}</td>
                        <td>{{ $salida->personal->kardex->dependencia ?? '' }}</td>
                        <td>{{ $salida->fechasol }}</td>
                        <td>{{ $salida->fechasal }} {{ $salida->horasal }}</td>
                        <td>{{ $salida->fecharet }} {{ $salida->horaret }}</td>
                        <td>{{ $salida->cantidad }}</td>
                        <td>{{ $salida->motivo }}</td>
                        <td><b class="text-success">[{{ $salida->vobo }}]</b> {{ $salida->nvobo->nombre }}
                            {{ $salida->nvobo->apellidopat }}
                            {{ $salida->nvobo->apellidomat }} </td>
                        <td>{{ $salida->observacion }}</td>

                        @if ($salida->estado !== 'validado' && $salida->estado !== 'rechazado')
                            <td>
                                <form method="POST" action="{{ route('salida.validar', $salida->id) }}"
                                    style="display:inline" title="Validar salida">
                                    @csrf
                                    <button class="btn btn-success" type="submit"
                                        onclick="return confirm('¿Validar esta salida?')">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('salida.rechazar', $salida->id) }}"
                                    style="display:inline">
                                    @csrf
                                    <button class="btn btn-danger" type="submit"
                                        onclick="return confirm('¿Rechazar esta salida?')" title="Rechazar salida">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning btn-editar-retorno"
                                    data-id="{{ $salida->id }}" data-fecharet="{{ $salida->fecharet }}"
                                    data-horaret="{{ $salida->horaret }}" title="Modificar retorno">
                                    <i class="fa-solid fa-clock"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Modal para editar fecha y hora de retorno -->
        <div class="modal fade" id="modalEditarRetorno" tabindex="-1" aria-labelledby="modalEditarRetornoLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" id="formEditarRetorno">
                        @csrf
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title" id="modalEditarRetornoLabel">Modificar Fecha y Hora de Retorno</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="fecharet" class="form-label">Nueva Fecha de Retorno</label>
                                <input type="date" class="form-control" name="fecharet" id="fecharet" required>
                            </div>
                            <div class="mb-3">
                                <label for="horaret" class="form-label">Nueva Hora de Retorno</label>
                                <input type="time" class="form-control" name="horaret" id="horaret" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- Botón Validar Todos --}}
        <form method="POST" action="{{ route('salidas.validarTodas') }}">
            @csrf
            <button class="btn btn-primary" type="submit"
                onclick="return confirm('¿Validar todos los registros mostrados?')" title="Validar todas las salidas">
                <i class="fa-solid fa-check-double"></i> Validar Todos
            </button>
        </form>

        {{-- Paginación Bootstrap 5 --}}
        <div class="mt-3">
            {{ $reportes->links('pagination::bootstrap-5') }}
        </div>
    </div>


@stop

@section('css')
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
@stop

@section('js')
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/fontawesome.js') }}"></script>
    <script src="{{ asset('js/funcionesJs.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                text: "La salida ha sido validada correctamente. ",
                icon: "success",
                confirmButtonText: "Aceptar",

            });
        </script>
    @endif
    @if (session('success1'))
        <script>
            Swal.fire({
                text: "La salida ha sido rechazada correctamente. ",
                icon: "error",
                confirmButtonText: "Aceptar",


            });
        </script>
    @endif
    @if (session('success2'))
        <script>
            Swal.fire({
                text: "Todos los registros fueron validados correctamente. ",
                icon: "success",
                confirmButtonText: "Aceptar",
            });
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('modalEditarRetorno'));
            const form = document.getElementById('formEditarRetorno');
            let salidaId = null;

            document.querySelectorAll('.btn-editar-retorno').forEach(btn => {
                btn.addEventListener('click', () => {
                    salidaId = btn.getAttribute('data-id');
                    document.getElementById('fecharet').value = btn.getAttribute('data-fecharet');
                    document.getElementById('horaret').value = btn.getAttribute('data-horaret');
                    form.action = `/salida/${salidaId}/actualizar-retorno`;
                    modal.show();
                });
            });
        });
    </script>
    @if (session('success3'))
        <script>
            Swal.fire({
                text: "Fecha y hora de retorno actualizadas correctamente.",
                icon: "success",
                confirmButtonText: "Aceptar",
            });
        </script>
    @endif
@stop
