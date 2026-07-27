<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Boleta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width:420px;">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                @if($valida)
                    <div class="text-success mb-2" style="font-size:2.5rem;">&#10003;</div>
                    <h5>Boleta válida</h5>
                    <p class="text-muted mb-3">Código: <strong>{{ $codigo }}</strong></p>
                    <hr>
                    <p class="mb-1">{{ $nombre }}</p>
                    <p class="mb-1">Salida: {{ $fechasal }} — Retorno: {{ $fecharet }}</p>
                    <span class="badge bg-{{ $estado === 'aprobado' ? 'success' : ($estado === 'rechazado' ? 'danger' : 'warning') }}">
                        {{ ucfirst($estado) }}
                    </span>
                @else
                    <div class="text-danger mb-2" style="font-size:2.5rem;">&#10007;</div>
                    <h5>Boleta no encontrada</h5>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
