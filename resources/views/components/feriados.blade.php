@if ($gestion->first())
    @php $anio = $gestion->first()->anio; @endphp
    <div class="alert alert-success py-1 px-2 d-flex align-items-center gap-2 flex-wrap" role="alert">
        <b class="fs-6"><i class="fa-solid fa-calendar-days me-1"></i>Feriados {{ $anio }}</b>
        <span class="badge bg-info text-white fs-6">{{ count($feriado) }}</span>
    </div>
    <div class="">
        <div class="card-body ">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="py-1 text-nowrap px-0">Descripción</th>
                            <th scope="col" class="py-1 text-nowrap px-0">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($feriado as $fer)
                            <tr>
                                <td class="py-1 text-truncate px-0" style="max-width: 120px;" title="{{ $fer->descripcion }}">
                                    <i class="px-0 fa-solid fa-calendar-days me-1 text-warning"></i>{{ $fer->descripcion }}
                                </td>
                                <td class="py-1 text-nowrap">{{ \Carbon\Carbon::parse($fer->fechaf)->format('d-m-Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <p class="small text-muted">Feriados gestión: Ninguno disponible</p>
@endif
