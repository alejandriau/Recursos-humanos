<table class="table table-striped small" style="width:100%">
    <thead>
        <tr>
            <th>ITEM</th>
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
            <th>PDF</th>
        </tr>
    </thead>
    <tbody id="table-body">
        @forelse ($personas as $persona)
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
                <td>
                    @if (!empty($persona->profesion->pdfProvision))
                        <a href="{{ asset($persona->profesion->pdfProvision) }}" target="_blank">
                            <img src="{{ asset('cssyjs/pdf.png') }}" width="30" alt="">
                        </a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="12">No se encontraron resultados.</td>
            </tr>
        @endforelse
    </tbody>
</table>
