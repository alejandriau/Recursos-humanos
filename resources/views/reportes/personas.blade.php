@extends('dashboard')

@section('title', 'Generar Reporte de Personal')

@section('contenido')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt"></i> Generar Reporte de Personal
                        </h3>
                    </div>

                    <div class="card-body">
                        <form id="formReporte" method="POST" action="{{ route('reportes.personas.vista-previa') }}">
                            @csrf

                            <!-- Filtros -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h4 class="card-title mb-0">
                                                <i class="fas fa-filter"></i> Filtros
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Columna 1 -->
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="unidad_id">Unidad Organizacional</label>
                                                        <select name="unidad_id" id="unidad_id" class="form-control">
                                                            <option value="">Todas las unidades</option>
                                                            @foreach ($unidades as $unidad)
                                                                <option value="{{ $unidad->id }}">{{ $unidad->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="tipo_contrato">Tipo de Contrato</label>
                                                        <select name="tipo_contrato" id="tipo_contrato"
                                                            class="form-control">
                                                            <option value="">Todos</option>
                                                            @foreach ($tiposContrato as $tipo)
                                                                <option value="{{ $tipo }}">{{ $tipo }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="nivel_jerarquico">Nivel Jerárquico</label>
                                                        <input type="text" name="nivel_jerarquico" id="nivel_jerarquico"
                                                            class="form-control" placeholder="Ej: 1, 2, 3...">
                                                    </div>
                                                </div>

                                                <!-- Columna 2 -->
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="sexo">Sexo</label>
                                                        <select name="sexo" id="sexo" class="form-control">
                                                            <option value="">Todos</option>
                                                            <option value="MASCULINO">Masculino</option>
                                                            <option value="FEMENINO">Femenino</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="es_jefatura">Es Jefatura</label>
                                                        <select name="es_jefatura" id="es_jefatura" class="form-control">
                                                            <option value="">Todos</option>
                                                            <option value="si">Sí</option>
                                                            <option value="no">No</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="estado_cas">Estado CAS</label>
                                                        <input type="text" name="estado_cas" id="estado_cas"
                                                            class="form-control" placeholder="Ej: VIGENTE, VENCIDO">
                                                    </div>
                                                </div>

                                                <!-- Columna 3 -->
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="busqueda">Búsqueda General</label>
                                                        <input type="text" name="busqueda" id="busqueda"
                                                            class="form-control"
                                                            placeholder="Buscar por CI, nombre, apellido...">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="ordenar_por">Ordenar por</label>
                                                        <select name="ordenar_por" id="ordenar_por" class="form-control">
                                                            <option value="">Seleccionar campo</option>
                                                            <option value="nombre">Nombre</option>
                                                            <option value="apellidoPat">Apellido Paterno</option>
                                                            <option value="ci">CI</option>
                                                            <option value="fecha_ingreso">Fecha de Ingreso</option>
                                                            <option value="salario">Salario</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="ordenar_direccion">Dirección del orden</label>
                                                        <select name="ordenar_direccion" id="ordenar_direccion"
                                                            class="form-control">
                                                            <option value="asc">Ascendente</option>
                                                            <option value="desc">Descendente</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="limite">Límite de registros</label>
                                                        <input type="number" name="limite" id="limite"
                                                            class="form-control" min="0" placeholder="0 para todos">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Columnas a mostrar -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="card-title mb-0">
                                                    <i class="fas fa-columns"></i> Columnas a Mostrar
                                                </h4>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        id="seleccionarTodo">
                                                        <i class="fas fa-check-square"></i> Seleccionar Todo
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        id="deseleccionarTodo">
                                                        <i class="fas fa-square"></i> Deseleccionar Todo
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($columnas as $categoria => $campos)
                                                <div class="mb-4">
                                                    <h5 class="text-primary border-bottom pb-2">
                                                        <i class="fas fa-folder"></i> {{ $categoria }}
                                                    </h5>
                                                    <div class="row">
                                                        @foreach ($campos as $campo => $label)
                                                            <div class="col-md-3 mb-2">
                                                                <div class="form-check">
                                                                    <input type="checkbox" name="columnas[]"
                                                                        value="{{ $campo }}"
                                                                        id="col_{{ $campo }}"
                                                                        class="form-check-input columna-checkbox" checked>
                                                                    <label for="col_{{ $campo }}"
                                                                        class="form-check-label">
                                                                        {{ $label }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="form-group">
                                            <label for="fecha_cenvi_desde">CENVI desde:</label>
                                            <input type="date" name="fecha_cenvi_desde" id="fecha_cenvi_desde"
                                                class="form-control">
                                        </div>

                                        <div class="form-group">
                                            <label for="fecha_cenvi_hasta">CENVI hasta:</label>
                                            <input type="date" name="fecha_cenvi_hasta" id="fecha_cenvi_hasta"
                                                class="form-control">
                                        </div>

                                        <div class="form-group">
                                            <label>Incluir datos CENVI:</label>
                                            <div class="form-check">
                                                <input type="checkbox" name="incluir_cenvi[]" value="ultima_fecha_cenvi"
                                                    class="form-check-input">
                                                <label class="form-check-label">Última fecha CENVI</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="incluir_cenvi[]" value="total_cenvis"
                                                    class="form-check-input">
                                                <label class="form-check-label">Total CENVIs</label>
                                            </div>
                                            <!-- Más opciones según necesites -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <div class="btn-group" role="group">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-eye"></i> Vista Previa
                                                </button>

                                                <button type="button" id="btnExportarExcel" class="btn btn-success">
                                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                                </button>

                                                <button type="button" id="btnExportarPDF" class="btn btn-danger">
                                                    <i class="fas fa-file-pdf"></i> Exportar PDF
                                                </button>

                                                <button type="button" id="btnExportarCSV" class="btn btn-info">
                                                    <i class="fas fa-file-csv"></i> Exportar CSV
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript simplificado sin dependencias -->
    <script>
        // Función para verificar si un elemento existe
        // Función principal que se ejecuta cuando el DOM está listo
        function initReportes() {
            console.log('🚀 Inicializando sistema de reportes...');

            // 1. FUNCIÓN PARA SELECCIONAR TODOS LOS CHECKBOXES
            const seleccionarTodoBtn = document.getElementById('seleccionarTodo');
            const deseleccionarTodoBtn = document.getElementById('deseleccionarTodo');

            if (seleccionarTodoBtn) {
                seleccionarTodoBtn.addEventListener('click', function() {
                    console.log('🖱️ Click en "Seleccionar Todo"');
                    const checkboxes = document.querySelectorAll('.columna-checkbox');

                    // Marcar todos los checkboxes
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = true;
                    });

                    // Cambiar apariencia de botones
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-primary');

                    if (deseleccionarTodoBtn) {
                        deseleccionarTodoBtn.classList.remove('btn-secondary');
                        deseleccionarTodoBtn.classList.add('btn-outline-secondary');
                    }
                });
            }

            if (deseleccionarTodoBtn) {
                deseleccionarTodoBtn.addEventListener('click', function() {
                    console.log('🖱️ Click en "Deseleccionar Todo"');
                    const checkboxes = document.querySelectorAll('.columna-checkbox');

                    // Desmarcar todos los checkboxes
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = false;
                    });

                    // Cambiar apariencia de botones
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-secondary');

                    if (seleccionarTodoBtn) {
                        seleccionarTodoBtn.classList.remove('btn-primary');
                        seleccionarTodoBtn.classList.add('btn-outline-primary');
                    }
                });
            }

            // 2. VALIDACIÓN DEL FORMULARIO DE VISTA PREVIA
            const formReporte = document.getElementById('formReporte');
            if (formReporte) {
                formReporte.addEventListener('submit', function(e) {
                    const checkboxesSeleccionados = document.querySelectorAll('.columna-checkbox:checked');

                    if (checkboxesSeleccionados.length === 0) {
                        e.preventDefault();
                        showNotification('error', 'Debe seleccionar al menos una columna para el reporte');
                        return false;
                    }

                    return true;
                });
            }

            // 3. FUNCIONALIDAD DE EXPORTACIÓN MEJORADA
            // Exportar a Excel
            const btnExportarExcel = document.getElementById('btnExportarExcel');
            if (btnExportarExcel) {
                btnExportarExcel.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('📊 Exportando a Excel');
                    exportarReporte('excel');
                });
            }

            // Exportar a PDF
            const btnExportarPDF = document.getElementById('btnExportarPDF');
            if (btnExportarPDF) {
                btnExportarPDF.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('📄 Exportando a PDF');
                    exportarReporte('pdf');
                });
            }

            // Exportar a CSV
            const btnExportarCSV = document.getElementById('btnExportarCSV');
            if (btnExportarCSV) {
                btnExportarCSV.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('📝 Exportando a CSV');
                    exportarReporte('csv');
                });
            }
        }

        // Función para exportar reporte - VERSIÓN CORREGIDA
        // Función para exportar reporte - VERSIÓN MEJORADA
        function exportarReporte(tipo) {
            console.log(`📦 Iniciando exportación a ${tipo}`);

            // Validar que haya columnas seleccionadas
            const checkboxesSeleccionados = document.querySelectorAll('.columna-checkbox:checked');
            if (checkboxesSeleccionados.length === 0) {
                showNotification('error', 'Debe seleccionar al menos una columna para exportar');
                return;
            }

            // Mostrar indicador de carga
            let loadingSwal = null;
            if (typeof Swal !== 'undefined') {
                loadingSwal = Swal.fire({
                    title: 'Generando archivo...',
                    html: `
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p>Por favor espere mientras se genera el archivo ${tipo.toUpperCase()}</p>
                    <p class="small text-muted">Esto puede tomar unos momentos...</p>
                </div>
            `,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    showCloseButton: false,
                    backdrop: true,
                    didOpen: () => {
                        // Añadir estilos para el spinner
                        const spinner = document.querySelector('.swal2-popup .spinner-border');
                        if (spinner) {
                            spinner.style.width = '3rem';
                            spinner.style.height = '3rem';
                        }
                    }
                }).then((result) => {
                    // Si el usuario cancela
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        console.log('Exportación cancelada por el usuario');
                        showNotification('info', 'Exportación cancelada');
                    }
                });
            }

            // Crear nuevo formulario para POST
            const formPost = document.createElement('form');
            formPost.method = 'POST';
            formPost.style.display = 'none';

            // Determinar la URL según el tipo
            let url = '';
            switch (tipo) {
                case 'excel':
                    url = "{{ route('reportes.personas.exportar.excel') }}";
                    break;
                case 'pdf':
                    url = "{{ route('reportes.personas.exportar.pdf') }}";
                    break;
                case 'csv':
                    url = "{{ route('reportes.personas.exportar.csv') }}";
                    break;
                default:
                    showNotification('error', 'Tipo de exportación no válido');
                    return;
            }

            formPost.action = url;

            // Agregar token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = "{{ csrf_token() }}";
            formPost.appendChild(csrfToken);

            // Agregar todas las columnas seleccionadas
            checkboxesSeleccionados.forEach(function(checkbox) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'columnas[]';
                input.value = checkbox.value;
                formPost.appendChild(input);
            });

            // Agregar otros campos del formulario
            const campos = ['unidad_id', 'tipo_contrato', 'nivel_jerarquico', 'sexo',
                'es_jefatura', 'estado_cas', 'busqueda', 'ordenar_por',
                'ordenar_direccion', 'limite'
            ];

            campos.forEach(function(campo) {
                const elemento = document.getElementById(campo);
                if (elemento) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = campo;
                    input.value = elemento.value || '';
                    formPost.appendChild(input);
                }
            });

            // Crear iframe invisible para la descarga
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.name = 'downloadFrame_' + Date.now();
            iframe.id = iframe.name;
            document.body.appendChild(iframe);

            // Configurar el formulario para usar el iframe
            formPost.target = iframe.name;

            // Agregar evento al iframe para detectar cuando se carga
            iframe.onload = function() {
                console.log('📥 Iframe cargado - Descarga completada o error');

                // Cerrar el modal de carga si sigue abierto
                if (loadingSwal && !loadingSwal.isClosed) {
                    Swal.close();
                }

                // Verificar si hubo error
                try {
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    const bodyText = iframeDoc.body ? iframeDoc.body.innerText : '';

                    if (bodyText.includes('error') || bodyText.includes('Error') ||
                        bodyText.includes('exception') || bodyText.includes('Exception')) {
                        console.error('Error detectado en la respuesta:', bodyText);
                        showNotification('error', 'Error al generar el archivo. Verifique los datos.');
                    } else {
                        // Éxito
                        showNotification('success', `Archivo ${tipo.toUpperCase()} generado correctamente`);
                    }
                } catch (e) {
                    // Si no podemos acceder al contenido (probablemente porque se descargó un archivo)
                    console.log('Archivo descargado exitosamente');
                    showNotification('success', `Archivo ${tipo.toUpperCase()} descargado correctamente`);
                }

                // Limpiar después de 3 segundos
                setTimeout(() => {
                    if (formPost.parentNode) {
                        formPost.parentNode.removeChild(formPost);
                    }
                    if (iframe.parentNode) {
                        iframe.parentNode.removeChild(iframe);
                    }
                }, 3000);
            };

            // Manejar errores en el iframe
            iframe.onerror = function() {
                console.error('❌ Error en la descarga del archivo');
                if (loadingSwal && !loadingSwal.isClosed) {
                    Swal.close();
                }
                showNotification('error', 'Error en la descarga del archivo');
            };

            // Agregar formulario al documento y enviar
            document.body.appendChild(formPost);
            formPost.submit();

            // Timeout de seguridad (si después de 60 segundos no hay respuesta)
            setTimeout(() => {
                if (loadingSwal && !loadingSwal.isClosed) {
                    Swal.close();
                    showNotification('warning',
                        'La exportación está tomando más tiempo de lo esperado. Intente nuevamente.');
                }
            }, 60000); // 60 segundos timeout
        }

        // Función para mostrar notificaciones
        function showNotification(type, message) {
            // Si Swal está disponible, usarlo
            if (typeof Swal !== 'undefined') {
                const iconMap = {
                    'success': 'success',
                    'error': 'error',
                    'info': 'info',
                    'warning': 'warning'
                };

                Swal.fire({
                    icon: iconMap[type] || 'info',
                    title: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            } else {
                // Si no hay Swal, usar alert nativo
                alert(message);
            }
        }

        // Inicializar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initReportes);
        } else {
            initReportes();
        }
    </script>

    <!-- Estilos CSS -->
    <style>
        .card-header {
            background-color: #f8f9fa;
        }

        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }

        .form-check-input:checked+.form-check-label {
            font-weight: 600;
            color: #0056b3;
        }

        .btn-group .btn {
            margin: 0 5px;
            min-width: 140px;
        }
    </style>
@endsection
