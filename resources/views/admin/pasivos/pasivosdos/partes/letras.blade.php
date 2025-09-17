                    @foreach ($resultados as $row)
                        <tr>
                            <form method="POST" action="{{ route('pasivodos', $row->id) }}">
                                @csrf
                                @method('PUT')
                                <td class="pasivocod">{{ $row->letra }} {{ $row->codigo }}</td>
                                <td><input type="text" class="inpu inpu-pasivomod w-100" style="all: unset;" name="nombrecompleto" value="{{ $row->nombrecompleto }}"></td>
                                <td ><input type="text" class="inpu inpu-pasivomod" style="all: unset;" name="observacion" value="{{ $row->observacion }}"></td>
                                <td><button type="submit" name="editarp" class="btn btn-warning">Actualizar</button></td>
                            </form>

                            <td>
                                <form action="" method="GET">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $row->id }}">
                                    <button type="submit" class="btn btn-primary">Reservar</button>
                                </form>
                            </td>

                            <!--<td>
                                <form action="{{ route('pasivodos.eliminar', $row->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="eliminarcof()" class="btn btn-danger" name="elminarp">Eliminar</button>
                                </form>
                            </td>-->

                            <td>
                                <form class="seleccionar-pasivod">
                                    @csrf
                                    <input type="hidden" name="idselecc" value="{{ $row->id }}">
                                    <button type="submit" class="btn btn-primary" name="selccionarp">Seleccionar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
