@if (count($personas))
        @foreach ($personas as $persona)
            <tr>
                <td>{{ $persona->apellidoPat }}</td>
                <td>{{ $persona->apellidoMat }}</td>
                <td>{{ $persona->nombre }}</td>
                <td>{{ $persona->ci }}</td>
                <td>{{ !empty($persona->fechaIngreso) ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') : '' }}</td>
                <td>{{ !empty($persona->fechaNacimiento) ? \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y') : '' }}</td>

                <td>{{ $persona->profesion->diploma ?? '' }}</td>
                <td>{{ !empty($persona->profesion->fechaDiploma) ? \Carbon\Carbon::parse($persona->profesion->fechaDiploma)->format('d/m/Y') : '' }}</td>
                <td>{{ $persona->profesion->provisionN ?? ''}}</td>
                <td>{{ !empty($persona->profesion->fechaProvision) ? \Carbon\Carbon::parse($persona->profesion->fechaProvision)->format('d/m/Y') : '' }}</td>
                <td>{{ $persona->profesion->universidad ?? ''}}</td>
                <td>{{ $persona->profesion->registro ?? ''}}</td>
                <td>
                    <a href="{{ $persona->profesion->pdfProvision ?? ''}}">
                        <img src="{{ asset('cssyjs/pdf.png') }}" width="30" alt="">
                    </a>
                </td>
            </tr>
        @endforeach

    @else
        <tr><td colspan="14">No se encontraron resultados.</td></tr>
    @endif

