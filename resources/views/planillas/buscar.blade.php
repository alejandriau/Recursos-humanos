@extends('dashboard')

@section('contenido')
<div class="container mt-4">
    {{-- Tarjeta de búsqueda de personas (sin cambios) --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-search me-2"></i>Buscar persona
            </h5>
            <a href="{{ route('planillas.import.form') }}" class="btn btn-light d-flex align-items-center">
                <i class="fas fa-upload me-2"></i>Subir planilla
            </a>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('persona.buscar.planillas') }}">
                <div class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="q" class="form-control" placeholder="Ingrese CI o nombres..." value="{{ request('q') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </div>
            </form>

            @if(isset($personas))
                <hr>
                <h5>Resultados de personas ({{ $personas->count() }})</h5>
                @if($personas->count())
                    <div class="list-group mt-3">
                        @foreach($personas as $persona)
                            <a href="{{ route('persona.planillas.mostrar', $persona->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $persona->ci }} - {{ $persona->nombre }} {{ $persona->apellidoPat }} {{ $persona->apellidoMat }}</h5>
                                    <small>Ingreso: {{ $persona->fechaIngreso }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning mt-3">No se encontraron personas con ese criterio.</div>
                @endif
            @endif
        </div>
    </div>

    {{-- Tarjeta de filtro y listado completo de planillas --}}
    <div class="card shadow">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-file-invoice-dollar me-2"></i>Planillas por mes, año y tipo
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('persona.buscar.planillas') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Mes</label>
                        <select name="mes" class="form-select">
                            <option value="">-- Seleccione --</option>
                            @php
                                $meses = [
                                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
                                    4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                                    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
                                    10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                ];
                            @endphp

                            @foreach($meses as $num => $nombre)
                                <option value="{{ $num }}" {{ request('mes') == $num ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Año</label>
                        <select name="anio" class="form-select">
                            <option value="">-- Seleccione --</option>
                            @for($y = now()->year; $y >= 2010; $y--)
                                <option value="{{ $y }}" {{ request('anio') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="">-- Todos --</option>
                            <option value="planta" {{ request('tipo') == 'planta' ? 'selected' : '' }}>Planta</option>
                            <option value="eventual" {{ request('tipo') == 'eventual' ? 'selected' : '' }}>Eventual</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success w-100">Filtrar planillas</button>
                    </div>
                </div>
                @if(request('mes') || request('anio') || request('tipo'))
                    <div class="mt-2">
                        <a href="{{ route('persona.buscar.planillas') }}" class="btn btn-sm btn-link">Limpiar filtros</a>
                    </div>
                @endif
            </form>

            @if(isset($planillas))
                <h5>Resultados de planillas ({{ $planillas->count() }})</h5>
                @if($planillas->count())
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover" style="min-width: 1800px; font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Persona</th>
                                    <th>CI</th>
                                    <th>Año</th>
                                    <th>Mes</th>
                                    <th>Num</th>
                                    <th>Tipo</th>
                                    <th>Expedido</th>
                                    <th>Partida</th>
                                    <th>Indi</th>
                                    <th>Cargo</th>
                                    <th>H_Basico</th>
                                    <th>H_Basejec</th>
                                    <th>Viatico</th>
                                    <th>F_Ingreso</th>
                                    <th>F_Vencimiento</th>
                                    <th>Categ</th>
                                    <th>Categejec</th>
                                    <th>Tot_Gan</th>
                                    <th>Dia_Trab</th>
                                    <th>Neto</th>
                                    <th>F_Cap_I</th>
                                    <th>R_Comun</th>
                                    <th>C_Afp</th>
                                    <th>A_Sol</th>
                                    <th>S</th>
                                    <th>T_Afp</th>
                                    <th>BBV</th>
                                    <th>Futuro</th>
                                    <th>Gestora</th>
                                    <th>Cuot_Mor</th>
                                    <th>Ret_Jud</th>
                                    <th>Falt_Atr</th>
                                    <th>Fom_101</th>
                                    <th>Pa_Iva</th>
                                    <th>Sal_Iva</th>
                                    <th>Tot_Deo</th>
                                    <th>Otros</th>
                                    <th>Otros_Des</th>
                                    <th>Tot_Des</th>
                                    <th>Tot_Par</th>
                                    <th>Tot_Parcom</th>
                                    <th>Liq_Pag</th>
                                    <th>Cuenta</th>
                                    <th>Sol_P</th>
                                    <th>Afp_P</th>
                                    <th>Fonvi_P</th>
                                    <th>Cns_P</th>
                                    <th>T_Labor</th>
                                    <th>T_Patro</th>
                                    <th>T_Carga</th>
                                    <th>Financia</th>
                                    <th>Separa2</th>
                                    <th>CGA</th>
                                    <th>CUA</th>
                                    <th>Des</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($planillas as $planilla)
                                    <tr>
                                        <td>{{ $planilla->id }}</td>
                                        <td>{{ $planilla->persona->apellidoPat ?? '' }} {{ $planilla->persona->apellidoMat ?? '' }} {{ $planilla->persona->nombre ?? '' }}</td>
                                        <td>{{ $planilla->persona->ci ?? 'N/A' }}</td>
                                        <td>{{ $planilla->anio }}</td>
                                        <td>{{ $planilla->mes }}</td>
                                        <td>{{ $planilla->num }}</td>
                                        <td>{{ $planilla->tipo }}</td>
                                        <td>{{ $planilla->expedido }}</td>
                                        <td>{{ $planilla->partida }}</td>
                                        <td>{{ $planilla->indi }}</td>
                                        <td>{{ $planilla->cargo }}</td>
                                        <td>{{ number_format($planilla->h_basico, 2) }}</td>
                                        <td>{{ number_format($planilla->h_basejec, 2) }}</td>
                                        <td>{{ number_format($planilla->viatico, 2) }}</td>
                                        <td>{{ $planilla->fecha_ingreso }}</td>
                                        <td>{{ $planilla->fecha_vencimiento }}</td>
                                        <td>{{ $planilla->categ }}</td>
                                        <td>{{ $planilla->categejec }}</td>
                                        <td>{{ number_format($planilla->tot_gan, 2) }}</td>
                                        <td>{{ $planilla->dia_trab }}</td>
                                        <td>{{ number_format($planilla->neto, 2) }}</td>
                                        <td>{{ $planilla->f_cap_i }}</td>
                                        <td>{{ number_format($planilla->r_comun, 2) }}</td>
                                        <td>{{ number_format($planilla->c_afp, 2) }}</td>
                                        <td>{{ $planilla->a_sol }}</td>
                                        <td>{{ $planilla->s }}</td>
                                        <td>{{ $planilla->t_afp }}</td>
                                        <td>{{ number_format($planilla->bbv, 2) }}</td>
                                        <td>{{ number_format($planilla->futuro, 2) }}</td>
                                        <td>{{ number_format($planilla->gestora, 2) }}</td>
                                        <td>{{ number_format($planilla->cuot_mor, 2) }}</td>
                                        <td>{{ number_format($planilla->ret_jud, 2) }}</td>
                                        <td>{{ $planilla->falt_atr }}</td>
                                        <td>{{ $planilla->fom_101 }}</td>
                                        <td>{{ $planilla->pa_iva }}</td>
                                        <td>{{ $planilla->sal_iva }}</td>
                                        <td>{{ number_format($planilla->tot_deo, 2) }}</td>
                                        <td>{{ number_format($planilla->otros, 2) }}</td>
                                        <td>{{ number_format($planilla->otros_des, 2) }}</td>
                                        <td>{{ number_format($planilla->tot_des, 2) }}</td>
                                        <td>{{ number_format($planilla->tot_par, 2) }}</td>
                                        <td>{{ number_format($planilla->tot_parcom, 2) }}</td>
                                        <td>{{ number_format($planilla->liq_pag, 2) }}</td>
                                        <td>{{ $planilla->cuenta }}</td>
                                        <td>{{ $planilla->sol_p }}</td>
                                        <td>{{ $planilla->afp_p }}</td>
                                        <td>{{ $planilla->fonvi_p }}</td>
                                        <td>{{ $planilla->cns_p }}</td>
                                        <td>{{ $planilla->t_labor }}</td>
                                        <td>{{ $planilla->t_patro }}</td>
                                        <td>{{ $planilla->t_carga }}</td>
                                        <td>{{ $planilla->financia }}</td>
                                        <td>{{ $planilla->separa2 }}</td>
                                        <td>{{ $planilla->cga }}</td>
                                        <td>{{ $planilla->cua }}</td>
                                        <td>{{ $planilla->des }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mt-3">No hay planillas para los filtros seleccionados.</div>
                @endif
            @elseif(!request()->has('mes') && !request()->has('anio') && !request()->has('tipo'))
                <div class="alert alert-secondary mt-3">
                    Seleccione mes, año y/o tipo y presione "Filtrar planillas".
                </div>
            @endif
        </div>
    </div>
</div>
@endsection