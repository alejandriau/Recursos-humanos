@if($tipo == 'por_discapacidad')
    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Datos de Discapacidad</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tipo de Discapacidad *</label>
                        <select name="tipo_discapacidad" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <option value="Física">Física</option>
                            <option value="Visual">Visual</option>
                            <option value="Auditiva">Auditiva</option>
                            <option value="Intelectual">Intelectual</option>
                            <option value="Psicosocial">Psicosocial</option>
                            <option value="Múltiple">Múltiple</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Grado de Discapacidad *</label>
                        <select name="grado_discapacidad" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <option value="leve">Leve</option>
                            <option value="moderado">Moderado</option>
                            <option value="severa">Severa</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Porcentaje de Discapacidad (%) *</label>
                        <input type="number" name="porcentaje_discapacidad" class="form-control" step="0.01" min="0" max="100" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Código Certificado</label>
                        <input type="text" name="codigo_certificado" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Entidad Certificadora *</label>
                        <input type="text" name="entidad_certificadora" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Fecha Certificación *</label>
                        <input type="date" name="fecha_certificacion" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Fecha Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control">
                        <small class="text-muted">Opcional - Dejar en blanco si no tiene vencimiento</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Certificado Médico *</label>
                        <input type="file" name="certificado_medico" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if($tipo == 'por_tutor_discapacitado')
    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Datos del Tutor Discapacitado</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Busque a la persona con discapacidad que será tutelada
            </div>
            <div class="form-group">
                <label>Persona con Discapacidad (Tutelado) *</label>
                <div class="input-group">
                    <input type="text" id="buscar_tutorado" class="form-control" placeholder="Buscar persona con discapacidad">
                    <div class="input-group-append">
                        <button type="button" id="btn_buscar_tutorado" class="btn btn-primary">Buscar</button>
                    </div>
                </div>
                <div id="resultados_tutorados" class="mt-2"></div>
                <input type="hidden" name="tutor_id" id="tutor_id" required>
            </div>
            <div id="datos_tutorado" style="display: none;" class="alert alert-success">
                <i class="fas fa-user-check"></i> <strong>Tutelado seleccionado:</strong>
                <span id="nombre_tutorado"></span>
                <button type="button" id="cambiar_tutorado" class="close">&times;</button>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Parentesco con el Tutelado *</label>
                        <select name="parentesco_tutor" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <option value="Padre">Padre</option>
                            <option value="Madre">Madre</option>
                            <option value="Hijo">Hijo</option>
                            <option value="Hija">Hija</option>
                            <option value="Hermano">Hermano</option>
                            <option value="Hermana">Hermana</option>
                            <option value="Cónyuge">Cónyuge</option>
                            <option value="Conviviente">Conviviente</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tipo de Discapacidad del Tutelado *</label>
                        <select name="tipo_discapacidad_tutor" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <option value="Física">Física</option>
                            <option value="Visual">Visual</option>
                            <option value="Auditiva">Auditiva</option>
                            <option value="Intelectual">Intelectual</option>
                            <option value="Psicosocial">Psicosocial</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Grado de Discapacidad *</label>
                        <select name="grado_discapacidad_tutor" class="form-control" required>
                            <option value="leve">Leve</option>
                            <option value="moderado">Moderado</option>
                            <option value="severa">Severa</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Porcentaje de Discapacidad (%) *</label>
                        <input type="number" name="porcentaje_discapacidad_tutor" class="form-control" step="0.01" min="0" max="100" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Entidad Certificadora *</label>
                        <input type="text" name="entidad_certificadora_tutor" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Fecha Certificación *</label>
                        <input type="date" name="fecha_certificacion_tutor" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Certificado Médico del Tutelado *</label>
                        <input type="file" name="certificado_medico_tutor" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if($tipo == 'por_dependiente_discapacitado')
    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Datos del Dependiente Discapacitado</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nombre Completo del Dependiente *</label>
                        <input type="text" name="nombre_dependiente" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Parentesco *</label>
                        <select name="parentesco_dependiente" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <option value="Hijo/a">Hijo/a</option>
                            <option value="Cónyuge">Cónyuge</option>
                            <option value="Padre">Padre</option>
                            <option value="Madre">Madre</option>
                            <option value="Hermano/a">Hermano/a</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Fecha Nacimiento *</label>
                        <input type="date" name="fecha_nacimiento_dependiente" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tipo de Discapacidad *</label>
                        <input type="text" name="tipo_discapacidad_dependiente" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Porcentaje Discapacidad (%) *</label>
                        <input type="number" name="porcentaje_discapacidad_dependiente" class="form-control" step="0.01" min="0" max="100" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Grado de Dependencia *</label>
                        <select name="grado_dependencia" class="form-control" required>
                            <option value="total">Dependencia Total</option>
                            <option value="parcial">Dependencia Parcial</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Certificado de Discapacidad *</label>
                        <input type="file" name="certificado_discapacidad_dependiente" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Necesidades Especiales</label>
                        <textarea name="necesidades_especiales" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if(in_array($tipo, ['por_embarazo', 'por_lactancia', 'por_paternidad']))
    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                @if($tipo == 'por_embarazo')
                    Datos de Embarazo
                @elseif($tipo == 'por_lactancia')
                    Datos de Lactancia
                @else
                    Datos de Paternidad
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if($tipo == 'por_embarazo')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha Probable de Parto *</label>
                            <input type="date" name="fecha_probable_parto" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Días Prenatales</label>
                            <input type="number" name="dias_prenatales" class="form-control" value="45" readonly>
                            <small class="text-muted">45 días antes del parto</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Días Postnatales</label>
                            <input type="number" name="dias_postnatales" class="form-control" value="45" readonly>
                            <small class="text-muted">45 días después del parto</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Certificado de Embarazo *</label>
                            <input type="file" name="certificado_embarazo" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                    </div>
                </div>
            @endif

            @if($tipo == 'por_lactancia')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha Inicio Lactancia *</label>
                            <input type="date" name="fecha_inicio_lactancia" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha Fin Lactancia *</label>
                            <input type="date" name="fecha_fin_lactancia" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Días de Lactancia</label>
                            <input type="number" name="dias_lactancia" class="form-control" id="dias_lactancia" readonly>
                            <small class="text-muted">Se calcula automáticamente</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Certificado de Lactancia</label>
                            <input type="file" name="certificado_lactancia" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
            @endif

            @if($tipo == 'por_paternidad')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Nacimiento *</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Días de Paternidad</label>
                            <input type="number" name="dias_paternidad" class="form-control" value="10" readonly>
                            <small class="text-muted">10 días hábiles según ley</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Certificado de Nacimiento *</label>
                            <input type="file" name="certificado_nacimiento" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                    </div>
                </div>
            @endif

            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i>
                <strong>Nota:</strong> Los períodos de inmovilidad por embarazo, lactancia y paternidad son temporales y requieren documentación médica oficial.
            </div>
        </div>
    </div>
@endif

@push('scripts')
@if($tipo == 'por_tutor_discapacitado')
<script>
$(document).ready(function() {
    let tutoradoSeleccionado = false;

    $('#btn_buscar_tutorado').click(function() {
        let termino = $('#buscar_tutorado').val();
        if (termino.length >= 2) {
            $.get('{{ route("rrhh.inmovilidades.buscar-persona") }}', { termino: termino }, function(data) {
                let html = '<div class="list-group">';
                data.forEach(persona => {
                    html += `<a href="#" class="list-group-item list-group-item-action"
                                onclick="seleccionarTutorado(${persona.id}, '${persona.nombres} ${persona.apellidos}')">
                                <strong>${persona.nombres} ${persona.apellidos}</strong><br>
                                <small>DNI: ${persona.documento_identidad}</small>
                            </a>`;
                });
                html += '</div>';
                $('#resultados_tutorados').html(html);
            });
        }
    });

    $('#cambiar_tutorado').click(function() {
        $('#tutor_id').val('');
        $('#datos_tutorado').hide();
        $('#buscar_tutorado').val('').focus();
        tutoradoSeleccionado = false;
    });
});

function seleccionarTutorado(id, nombre) {
    $('#tutor_id').val(id);
    $('#nombre_tutorado').text(nombre);
    $('#datos_tutorado').show();
    $('#resultados_tutorados').html('');
    $('#buscar_tutorado').val('');
}
</script>
@endif

@if($tipo == 'por_lactancia')
<script>
$(document).ready(function() {
    $('#fecha_inicio_lactancia, #fecha_fin_lactancia').change(function() {
        let inicio = $('#fecha_inicio_lactancia').val();
        let fin = $('#fecha_fin_lactancia').val();
        if (inicio && fin) {
            let dias = Math.ceil((new Date(fin) - new Date(inicio)) / (1000 * 60 * 60 * 24));
            if (dias > 0) {
                $('#dias_lactancia').val(dias);
            } else {
                $('#dias_lactancia').val(0);
                Swal.fire('Error', 'La fecha fin debe ser posterior a la fecha inicio', 'error');
            }
        }
    });
});
</script>
@endif
@endpush
