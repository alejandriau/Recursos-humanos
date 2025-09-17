                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>CODIGO</th>
                            <th>NOMBRE COMPLETO</th>
                            <th>OBSERVACIONES</th>
                            <th>Acciones</th>
                        </tr>
                    </thead >
                    <tbody id="tablaBody">
                        @if ($selecciones)
                            @foreach ($selecciones as $seleccion)
                                <tr>
                                    <td><input type="hidden" name="idreporte[]" value="{{$seleccion->pasivodos->id}}">{{ $seleccion->pasivodos->letra ?? '' }} {{ $seleccion->pasivodos->codigo ?? '' }}</td>
                                    <td>{{ $seleccion->pasivodos->nombrecompleto ?? ''}}</td>
                                    <td>{{ $seleccion->pasivodos->observacion ?? ''}}</td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-eliminar" data-id="{{ $seleccion->id }}">X</button>
                                    </td>

                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
