@if (count($personas))
        <option value="">Seleccione una opción</option>
    @foreach ($personas as $persona)
        <option value="{{ $persona->id }}">
            {{ $persona->ci .' '.$persona->nombre . ' ' . $persona->apellidoPat . ' ' . $persona->apellidoMat }}
        </option>
    @endforeach
@else
    <option disabled selected>No se encontraron resultados</option>
@endif
