<table id="example" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>APELLIDO 1</th>
            <th>APELLIDO 2</th>
            <th>NOMBRE</th>
            <th>CI</th>
            <th>FECHA INGRESO</th>
            <th>FECHA NACIMIENTO</th>
            <th>DIPLOMA</th>
            <th>FECHA DIPLOMA</th>
            <th>PROVISION NACIONAL</th>
            <th>FECHA PROVISION</th>
            <th>UNIVERSIDAD</th>
            <th>REGISTRO</th>
            <th>PDF</th>
        </tr>
    </thead>
    <tbody id="table-body">
        @forelse ($personas as $persona)
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
        @empty
            <tr><td colspan="13">No se encontraron resultados.</td></tr>
        @endforelse
    </tbody>
</table>
