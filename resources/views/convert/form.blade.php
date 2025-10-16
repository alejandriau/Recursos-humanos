<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convertir TXT a Word</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: none;
            border-radius: 10px;
        }
        .alert {
            border-radius: 8px;
        }
        .btn {
            border-radius: 6px;
        }
        .file-info {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">📄 Convertir TXT a Word - Oficio Horizontal</h4>
                    </div>
                    <div class="card-body">
                        <!-- Mensajes -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ✅ {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ❌ {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <h6>Errores de validación:</h6>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Información del sistema -->
                        <div class="alert alert-info">
                            <strong>💡 Información:</strong>
                            Asegúrate de que el archivo tenga extensión <code>.txt</code> y no esté vacío.
                            Tamaño máximo: 10MB.
                        </div>

                        <div class="row">
                            <!-- Opción 1: Validación flexible -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">🔧 Opción Flexible (Tamaño 8)</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('convert.word') }}" method="POST" enctype="multipart/form-data" id="formFlexible">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="txt_file_flex" class="form-label">Seleccionar archivo TXT</label>
                                                <input type="file" class="form-control" id="txt_file_flex" name="txt_file" accept=".txt" required>
                                                <div class="form-text">
                                                    ✅ Acepta varios tipos MIME de texto
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-info w-100" id="btnFlexible">
                                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                                Convertir Flexible (Tamaño 8)
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Opción 2: Tamaño 7 -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0">📏 Tamaño Letra 7</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('convert.size7') }}" method="POST" enctype="multipart/form-data" id="formSize7">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="txt_file_size7" class="form-label">Seleccionar archivo TXT</label>
                                                <input type="file" class="form-control" id="txt_file_size7" name="txt_file" accept=".txt" required>
                                                <div class="form-text">
                                                    ✅ Validación flexible para TXT
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-secondary w-100" id="btnSize7">
                                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                                Convertir a Word (Tamaño 7)
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Opción 3: Validación simple -->
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <div class="card">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">⚡ Opción Simple (Tamaño 8)</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('convert.txt-simple') }}" method="POST" enctype="multipart/form-data" id="formSimple">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="txt_file_simple" class="form-label">Seleccionar archivo TXT</label>
                                                <input type="file" class="form-control" id="txt_file_simple" name="txt_file" accept=".txt" required>
                                                <div class="form-text">
                                                    ⚠️ Solo verifica extensión .txt
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-warning w-100" id="btnSimple">
                                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                                Convertir Simple (Tamaño 8)
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Especificaciones -->
                        <div class="mt-4">
                            <div class="alert alert-success">
                                <h6>📐 Especificaciones del documento generado:</h6>
                                <ul class="mb-0">
                                    <li><strong>Orientación:</strong> Horizontal (Landscape)</li>
                                    <li><strong>Tamaño de papel:</strong> Oficio (8.5" x 11")</li>
                                    <li><strong>Fuente:</strong> Arial</li>
                                    <li><strong>Tamaños disponibles:</strong> 7 u 8 puntos</li>
                                    <li><strong>Formato:</strong> Word (.docx)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enlaces útiles -->
                <div class="text-center mt-3">
                    <a href="/check-dependencies" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                        🔍 Verificar dependencias
                    </a>
                    <small class="text-muted">
                        Si persisten los problemas, verifica que el archivo sea un TXT válido.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Spinners para todos los formularios
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    const spinner = btn.querySelector('.spinner-border');
                    if (spinner) {
                        spinner.classList.remove('d-none');
                    }
                }
            });
        });

        // Mostrar información del archivo seleccionado
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    console.log('Archivo seleccionado:', {
                        name: file.name,
                        size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                        type: file.type
                    });
                }
            });
        });
    </script>
</body>
</html>
