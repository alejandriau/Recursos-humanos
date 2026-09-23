<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Escáner - {{ $actividad->nombre }}</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; padding: 16px; background: #f3f4f6; }
        .header { background: #0284c7; color: #fff; padding: 12px 16px; border-radius: 8px; margin-bottom: 12px; }
        .header h1 { margin: 0 0 4px; font-size: 18px; }
        .header p { margin: 0; font-size: 13px; opacity: .9; }
        #reader { width: 100%; border-radius: 8px; overflow: hidden; }
        #result {
            margin-top: 12px; padding: 14px; border-radius: 8px;
            font-weight: 600; text-align: center; font-size: 15px;
            background: #e5e7eb; color: #111;
        }
        .ok       { background: #dcfce7 !important; color: #166534 !important; }
        .tardanza { background: #fef9c3 !important; color: #854d0e !important; }
        .error    { background: #fee2e2 !important; color: #991b1b !important; }
        .info     { font-size: 13px; color: #555; margin-top: 6px; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $actividad->nombre }}</h1>
        <p>{{ $actividad->lugar }} · {{ $actividad->fecha->format('d/m/Y') }}</p>
        <p>Asistencias registradas: <span id="contador">{{ $totalAsistencias }}</span></p>
    </div>

    <div id="reader"></div>
    <div id="result">Apunta la cámara al código QR…</div>
    <div class="info" id="info"></div>

    <script>
        const URL_REGISTRO = "{{ route('actividades.asistencia.registrar', $actividad) }}";
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        const resultBox = document.getElementById('result');
        const infoBox   = document.getElementById('info');
        const contador  = document.getElementById('contador');

        // Evita procesar el mismo QR dos veces seguidas (bloqueo de 3 s)
        let ultimoQr = null;
        let ultimoTimestamp = 0;

        function pintarResultado(clase, mensaje, info = '') {
            resultBox.className = clase;
            resultBox.textContent = mensaje;
            infoBox.textContent = info;
        }

        function onScanSuccess(decodedText) {
            const ahora = Date.now();

            // Si es el mismo QR escaneado hace menos de 3 segundos, lo ignoramos
            if (decodedText === ultimoQr && (ahora - ultimoTimestamp) < 3000) {
                return;
            }
            ultimoQr = decodedText;
            ultimoTimestamp = ahora;

            pintarResultado('', 'Procesando…');

            fetch(URL_REGISTRO, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ qr_texto: decodedText })
            })
            .then(async (res) => {
                const data = await res.json();
                data._status = res.status;
                return data;
            })
            .then(data => {
                if (data.success) {
                    const clase = data.tipo === 'tardanza' ? 'tardanza' : 'ok';
                    pintarResultado(clase, data.message, `Hora: ${data.hora} · CI: ${data.persona.ci}`);
                    if (data.total !== undefined) contador.textContent = data.total;
                    beep(clase === 'ok' ? 880 : 440);
                } else {
                    pintarResultado('error', data.message, data.persona ? `CI: ${data.persona.ci}` : '');
                    beep(220);
                }
            })
            .catch(err => {
                console.error(err);
                pintarResultado('error', 'Error de conexión con el servidor.');
                beep(220);
            });
        }

        // Iniciar el escáner
        const scanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: { width: 250, height: 250 } },
            /* verbose= */ false
        );
        scanner.render(onScanSuccess);

        // Beep simple con Web Audio API
        function beep(frecuencia) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.frequency.value = frecuencia;
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.12);
            } catch (e) { /* sin audio, no pasa nada */ }
        }
    </script>
</body>
</html>