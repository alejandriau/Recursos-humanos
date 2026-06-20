<script>
    window.addEventListener('DOMContentLoaded', () => {
        const responseData = localStorage.getItem('responseData');
        if (!responseData) {
            location.href = '/';
        }
    });
    const datosUsr = localStorage.getItem('responseData');
    // Convertir de JSON a objeto
    const datosPer = datosUsr ? JSON.parse(datosUsr) : "No hay datos almacenados.";
    //asigna los valores al formulario de localstorage
    window.idserv = datosPer.data[0].idServidor;
</script>
<style>
    .modal-xxl {
        max-width: 1320px; /* O el ancho que desees */
    }
</style>
@extends('layouts.baseusr')
@section('comunicado')
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light border-bottom d-flex align-items-center">
            <h5 class="mb-0 text-success">
                <i class="fa-solid fa-gear me-2"></i>Opciones
            </h5>
        </div>

        <div class="card-body">
            <h6 class="text-primary fw-bold mb-3">
                <i class="fa-solid fa-right-from-bracket me-2"></i>Salidas Registradas
            </h6>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <tbody>
                        <tr>
                            <td class="text-secondary fw-semibold">Vacaciones</td>
                            <td>
                                <button id="listVac" type="button" class="btn btn-outline-success rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#vac">
                                    <i class="fa-solid fa-person-walking-luggage"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-secondary fw-semibold">Comisiones</td>
                            <td>
                                <button id="listCom" type="button" class="btn btn-outline-success rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#com">
                                    <i class="fa-solid fa-person-walking-arrow-right"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-secondary fw-semibold">Salida médica</td>
                            <td>
                                <button id="listSalud" type="button" class="btn btn-outline-success rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#salud">
                                    <i class="fa-solid fa-truck-medical"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-secondary fw-semibold">Salida particular</td>
                            <td>
                                <button id="listParticular" type="button" class="btn btn-outline-success rounded-pill" data-bs-toggle="modal"
                                    data-bs-target="#particular">
                                    <i class="fa-solid fa-person-walking-arrow-loop-left"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <hr class="my-4">

            <h6 class="text-primary fw-bold mb-3">
                <i class="fa-solid fa-eye me-2"></i>Visto Bueno de Solicitudes
            </h6>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <tbody>
                        <tr>
                            <td class="text-secondary fw-semibold">Ver solicitudes pendientes</td>
                            <td>
                                <button id="listSol" type="button" class="btn btn-outline-success rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#sol">
                                    <i class="fa-solid fa-square-check"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal Vacaciones -->
    <div class="modal fade" id="vac" tabindex="-1" aria-labelledby="labelVac" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="labelVac">
                        <i class="fa-solid fa-person-walking-luggage me-2"></i>Vacaciones Registradas
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="table-responsive">
                        <table id="idvac" class="table table-striped table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Fecha Salida</th>
                                    <th>Fecha Retorno</th>
                                    <th>Días</th>
                                    <th>Visto Bueno</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí irán los datos dinámicos -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Comisiones -->
    <div class="modal fade" id="com" tabindex="-1" aria-labelledby="labelCom" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 1400px;">
            <div class="modal-content shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="labelCom">
                        <i class="fa-solid fa-person-walking-arrow-right me-2"></i>Comisiones Registradas
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="table-responsive">
                        <table id="idcom" class="table table-striped table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Fecha Salida</th>
                                    <th>Hora Salida</th>
                                    <th>Fecha Retorno</th>
                                    <th>Hora Retorno</th>
                                    <th style="width: 35%;">Motivo</th>
                                    <th>VoBo</th>
                                    <th>Estado</th>
                                    <th>Detalle</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Salud -->
    <div class="modal fade" id="salud" tabindex="-1" aria-labelledby="labelSal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content shadow">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="labelSal">
                        <i class="fa-solid fa-truck-medical me-2"></i>Salidas por Salud Registradas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="table-responsive">
                        <table id="idsal" class="table table-striped table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Fecha Salida</th>
                                    <th>Hora Salida</th>
                                    <th>Fecha Retorno</th>
                                    <th>Hora Retorno</th>
                                    <th>VoBo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal particular -->
    <div class="modal fade" id="particular" tabindex="-1" aria-labelledby="labelSal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 1400px;">
            <div class="modal-content shadow">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="labelSal">
                        <i class="fa-solid fa-truck-medical me-2"></i>Salidas por particular Registradas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="table-responsive">
                        <table id="idsalpar" class="table table-striped table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Descripcion</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Fecha Salida</th>
                                    <th>Hora Salida</th>
                                    <th>Fecha Retorno</th>
                                    <th>Hora Retorno</th>
                                    <th>Cantidad</th>
                                    <th>VoBo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Solicitudes -->
    <div class="modal fade" id="sol" tabindex="-1" aria-labelledby="labelSol" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="width: 100%; max-width: 1600px;">
            <div class="modal-content shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="labelSol">
                        <i class="fa-solid fa-square-check me-2"></i>Solicitudes de Salida Pendientes
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="table-responsive">
                        <table id="IdListSol" class="table table-striped table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha Sol.</th>
                                    <th style="width: 15%;">Nombre</th>
                                    <th>Descripción</th>
                                    <th>Salida</th>
                                    <th>Hora Sal.</th>
                                    <th>Retorno</th>
                                    <th>Hora Ret.</th>
                                    <th style="width: 25%;">Motivo</th>
                                    <th>Cantidad</th>
                                    <th>VoBo</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <small class="text-muted">Revisa cuidadosamente antes de aprobar.</small>
                    <button id="aprobarTodo" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-check-double me-1"></i>Aprobar Todo
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('cuerpo')
    <img src="{{ URL::asset('img/imgUser.jpg') }}" class="img-fluid" alt="" />
@endsection
<script>


    // ************************************************************************************
</script>
<script src="{{ asset('js/funcionesApiUsr.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
