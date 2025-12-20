@extends('dashboard')

@section('title', 'Generador de Reportes de Personal')

@section('contenido')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-file-export me-2"></i>Generador de Reportes
                        </h4>
                        <span class="badge bg-light text-primary">Personalizado</span>
                    </div>
                </div>

                <div class="card-body">
                    <form id="formReporte" method="POST" action="{{ route('reportes.vista-previa') }}">
                        @csrf

                        <!-- Filtros de Búsqueda -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2">
                                    <i class="fas fa-filter me-2"></i>Filtros de Selección
                                </h5>
                            </div>

                            <!-- Fila 1 -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Unidad Organizacional</label>
                                <select name="unidad_id" class="form-select">
                                    <option value="">Todas las unidades</option>
                                    @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tipo de Contrato</label>
                                <select name="tipo_contrato" class="form-select">
                                    <option value="">Todos los contratos</option>
                                    @foreach($tiposContrato as $tipo)
                                    <option value="{{ $tipo }}">{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sexo</label>
                                <select name="sexo" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="MASCULINO">Masculino</option>
                                    <option value="FEMENINO">Femenino</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Es Jefatura</label>
                                <select name="es_jefatura" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            </div>

                            <!-- Fila 2 -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nivel Jerárquico</label>
                                <select name="nivel_jerarquico" class="form-select">
                                    <option value="">Todos los niveles</option>
                                    @php
                                        $niveles = [
                                            'GOBERNADOR (A)',
                                            'SECRETARIA (O) DEPARTAMENTAL',
                                            'DIRECTORA (OR)/DIR. SERV. DPTAL./VOCERA (O) GUB.',
                                            'ASESORA (OR) / DIRECTORA (OR) / DIR. SERV. DPTAL.',
                                            'JEFA (E) DE UNIDAD',
                                            'PROFESIONAL I',
                                            'PROFESIONAL II',
                                            'ADMINISTRATIVO I',
                                            'ADMINISTRATIVO II',
                                            'APOYO ADMINISTRATIVO I',
                                            'APOYO ADMINISTRATIVO II',
                                            'ASISTENTE'
                                        ];
                                    @endphp
                                    @foreach($niveles as $nivel)
                                    <option value="{{ $nivel }}">{{ $nivel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Estado CAS</label>
                                <select name="estado_cas" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="vigente">Vigente</option>
                                    <option value="vencido">Vencido</option>
                                    <option value="procesado">Procesado</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Límite de Registros</label>
                                <input type="number" name="limite" class="form-control" placeholder="0 = Todos" min="0">
                            </div>

                            <!-- Fila 3 -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Buscar por Nombre o CI</label>
                                <input type="text" name="busqueda" class="form-control" placeholder="Ej: Juan Pérez o 1234567">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Ordenar por</label>
                                <select name="ordenar_por" class="form-select">
                                    <option value="apellidoPat">Apellido Paterno</option>
                                    <option value="nombre">Nombre</option>
                                    <option value="ci">CI</option>
                                    <option value="fechaNacimiento">Fecha Nacimiento</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Dirección</label>
                                <select name="ordenar_direccion" class="form-select">
                                    <option value="asc">Ascendente (A-Z)</option>
                                    <option value="desc">Descendente (Z-A)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Selección de Columnas -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-columns me-2"></i>Selecciona las Columnas a Incluir
                                    </h5>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="seleccionarTodo()">
                                            <i class="fas fa-check-square me-1"></i> Seleccionar Todo
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deseleccionarTodo()">
                                            <i class="fas fa-square me-1"></i> Deseleccionar Todo
                                        </button>
                                    </div>
                                </div>

                                <div class="border rounded p-3 bg-light">
                                    @foreach($columnas as $categoria => $campos)
                                    <div class="mb-4">
                                        <h6 class="text-primary border-bottom pb-1">
                                            <i class="fas fa-folder me-1"></i> {{ $categoria }}
                                        </h6>
                                        <div class="row">
                                            @foreach($campos as $key => $label)
                                            <div class="col-md-4 col-lg-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input columna-checkbox"
                                                           type="checkbox"
                                                           name="columnas[]"
                                                           value="{{ $key }}"
                                                           id="col_{{ $key }}"
                                                           checked>
                                                    <label class="form-check-label small" for="col_{{ $key }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-eye me-1"></i> Vista Previa
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="fas fa-redo me-1"></i> Limpiar Filtros
                                        </button>
                                    </div>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success" onclick="exportar('excel')">
                                            <i class="fas fa-file-excel me-1"></i> Exportar Excel
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="exportar('pdf')">
                                            <i class="fas fa-file-pdf me-1"></i> Exportar PDF
                                        </button>
                                        <button type="button" class="btn btn-info" onclick="exportar('csv')">
                                            <i class="fas fa-file-csv me-1"></i> Exportar CSV
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Estadísticas -->
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <small class="text-muted">Total Unidades:</small>
                            <div class="h5 mb-0">{{ $unidades->count() }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Columnas Disponibles:</small>
                            @php
                                $totalColumnas = 0;
                                foreach($columnas as $categoria => $campos) {
                                    $totalColumnas += count($campos);
                                }
                            @endphp
                            <div class="h5 mb-0">{{ $totalColumnas }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Formatos de Exportación:</small>
                            <div class="h5 mb-0">3</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Filtros Disponibles:</small>
                            <div class="h5 mb-0">8</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Seleccionar/Deseleccionar todas las columnas
function seleccionarTodo() {
    document.querySelectorAll('.columna-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deseleccionarTodo() {
    document.querySelectorAll('.columna-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Exportar a diferentes formatos
function exportar(formato) {
    const form = document.getElementById('formReporte');
    const columnasSeleccionadas = Array.from(document.querySelectorAll('.columna-checkbox:checked'))
        .map(cb => cb.value);

    if (columnasSeleccionadas.length === 0) {
        alert('Por favor, selecciona al menos una columna para exportar.');
        return;
    }

    // Crear input oculto con las columnas
    columnasSeleccionadas.forEach(columna => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'columnas[]';
        input.value = columna;
        form.appendChild(input);
    });

    // Redirigir según formato
    let url = '';
    switch(formato) {
        case 'excel':
            url = "{{ route('reportes.exportar.excel') }}";
            break;
        case 'pdf':
            url = "{{ route('reportes.exportar.pdf') }}";
            break;
        case 'csv':
            url = "{{ route('reportes.exportar.csv') }}";
            break;
    }

    form.action = url;
    form.submit();
}

// Resetear formulario
function resetForm() {
    document.getElementById('formReporte').reset();
    seleccionarTodo();
}

// Cargar opciones dinámicas para filtros
document.querySelectorAll('select[name="filtro_campo"]').forEach(select => {
    select.addEventListener('change', function() {
        const campo = this.value;
        if (campo) {
            fetch(`/reportes/opciones-filtro?campo=${campo}`)
                .then(response => response.json())
                .then(data => {
                    const valorSelect = document.querySelector('select[name="filtro_valor"]');
                    valorSelect.innerHTML = '<option value="">Seleccionar valor...</option>';
                    data.forEach(valor => {
                        const option = document.createElement('option');
                        option.value = valor;
                        option.textContent = valor;
                        valorSelect.appendChild(option);
                    });
                });
        }
    });
});
</script>

<style>
.form-check-label {
    cursor: pointer;
    user-select: none;
}

.form-check-input:checked + .form-check-label {
    font-weight: 500;
    color: #0d6efd;
}

.card-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
}
</style>
@endpush
