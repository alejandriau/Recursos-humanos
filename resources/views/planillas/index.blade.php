<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Planillas - Recursos Humanos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header { background: linear-gradient(135deg, #2c3e50, #4a6491); color: white; padding: 2rem 0; margin-bottom: 2rem; }
        .card-custom { background: white; border-radius: 10px; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1); padding: 2rem; margin-bottom: 2rem; }
        .btn-primary { background: #4a6491; border: none; }
        .btn-primary:hover { background: #3a547e; }
        .stats-card { text-align: center; padding: 1.5rem; }
        .stats-number { font-size: 2rem; font-weight: bold; color: #4a6491; }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1 class="text-center">Sistema de Gestión de Planillas</h1>
            <p class="text-center mb-0">Módulo de Recursos Humanos</p>
        </div>
    </div>

    <div class="container">
        <!-- Tarjetas de estadísticas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card-custom stats-card">
                    <div class="stats-number">{{ $totalEmpleados }}</div>
                    <p class="text-muted">Empleados Registrados</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-custom stats-card">
                    <div class="stats-number">{{ $totalRegistros }}</div>
                    <p class="text-muted">Registros Salariales</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-custom stats-card">
                    <div class="stats-number">{{ $ultimoProcesamiento ? $ultimoProcesamiento->created_at->format('d/m/Y') : 'N/A' }}</div>
                    <p class="text-muted">Última Actualización</p>
                </div>
            </div>
        </div>

        <!-- Búsqueda de empleados -->
        <div class="card-custom">
            <h2 class="mb-4">Búsqueda de Empleados</h2>
            <form action="{{ route('planillas.buscar') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label for="nombre" class="form-label">Buscar por Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: María Rodríguez">
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="cedula" class="form-label">Buscar por Cédula:</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Ej: 12345678">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Subida de planillas -->
        <div class="card-custom">
            <h2 class="mb-4">Cargar Nuevas Planillas</h2>
            <form action="{{ route('planillas.subir') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label for="planillas" class="form-label">Seleccionar Archivos PDF:</label>
                        <input type="file" class="form-control" id="planillas" name="planillas[]" multiple accept=".pdf" required>
                        <div class="form-text">Seleccione los archivos PDF de planillas a procesar</div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="year" class="form-label">Año de las Planillas:</label>
                        <select class="form-select" id="year" name="year" required>
                            <option value="">Seleccionar año...</option>
                            @for($y = 2010; $y <= date('Y'); $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">Procesar Planillas</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Mensajes de éxito/error -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <footer class="mt-5 py-3 text-center text-muted">
        <div class="container">
            <p>Sistema de Planillas - Recursos Humanos &copy; {{ date('Y') }}</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
