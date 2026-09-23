<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0284c7">
    <title>Escáner - {{ $actividad->nombre }}</title>

    @vite(['resources/css/app.css'])

    {{-- html5-qrcode vía CDN (o cámbialo a npm si lo prefieres) --}}
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        /* Prevenir zoom accidental en móvil */
        * { -webkit-tap-highlight-color: transparent; }
        body {
            overscroll-behavior: none;
            background: #0f172a;
        }
        /* El video del escáner siempre cuadrado y adaptado */
        #reader video {
            border-radius: 12px;
            object-fit: cover !important;
        }
        #reader {
            border: none !important;
        }
        /* Ocultar botones feos por defecto de html5-qrcode */
        #reader__dashboard_section_csr button {
            background: #0284c7 !important;
            color: white !important;
            border: none !important;
            padding: 10px 16px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
        }
        #reader__dashboard_section_csr select {
            padding: 8px !important;
            border-radius: 6px !important;
            border: 1px solid #cbd5e1 !important;
        }
        #reader__scan_region img {
            display: none !important;
        }
        /* Animación del resultado */
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: slideDown 0.25s ease-out; }
        /* Pulso para "escaneando" */
        @keyframes pulseBorder {
            0%, 100% { box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.7); }
            50%      { box-shadow: 0 0 0 12px rgba(56, 189, 248, 0); }
        }
        .pulse-border { animation: pulseBorder 2s infinite; }
    </style>
</head>
<body class="min-h-screen text-white">

    {{-- ═══════════ HEADER ═══════════ --}}
    <header class="sticky top-0 z-30 bg-sky-700 shadow-lg">
        <div class="px-4 py-3 flex items-center justify-between gap-3">
            <div class="min-w-0 flex-1">
                <h1 class="font-bold text-base leading-tight truncate">
                    {{ $actividad->nombre }}
                </h1>
                <p class="text-xs text-sky-100 truncate">
                    {{ $actividad->lugar ?? 'Sin lugar' }} · {{ $actividad->fecha->format('d/m/Y') }}
                </p>
            </div>

            <div class="flex-shrink-0 text-right">
                <p class="text-[10px] text-sky-200 uppercase tracking-wide">Asistencias</p>
                <p id="contador" class="text-2xl font-bold leading-none">{{ $totalAsistencias }}</p>
            </div>
        </div>

        {{-- Barra de progreso superior (indicador de cámara activa) --}}
        <div id="cameraBar" class="h-1 bg-sky-800 overflow-hidden">
            <div id="cameraBarFill" class="h-full bg-green-400 transition-all duration-500 w-0"></div>
        </div>
    </header>

    {{-- ═══════════ CONTENIDO PRINCIPAL ═══════════ --}}
    <main class="p-3 pb-32">

        {{-- Estado: Cargando cámara --}}
        <div id="loadingBox" class="bg-slate-800 rounded-xl p-8 text-center">
            <div class="inline-block w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mb-3"></div>
            <p class="text-sm text-slate-300">Iniciando cámara…</p>
            <p class="text-xs text-slate-500 mt-1">Concede permisos si te los pide</p>
        </div>

        {{-- Estado: Error de cámara --}}
        <div id="errorBox" class="hidden bg-red-900/50 border border-red-500 rounded-xl p-5 text-center">
            <p class="text-3xl mb-2">📵</p>
            <h2 class="font-bold text-red-200 mb-1">No se pudo acceder a la cámara</h2>
            <p id="errorMessage" class="text-sm text-red-200/80 mb-4"></p>
            <button id="reintentarBtn"
                    class="bg-red-600 hover:bg-red-700 active:bg-red-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Reintentar
            </button>
        </div>

        {{-- Contenedor del escáner --}}
        <div id="scannerWrapper" class="hidden">
            {{-- Visor --}}
            <div class="relative rounded-xl overflow-hidden bg-black mb-3">
                <div id="reader" class="w-full"></div>

                {{-- Overlay de mira --}}
                <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                    <div class="w-56 h-56 border-4 border-sky-400 rounded-2xl pulse-border opacity-80"></div>
                </div>
            </div>

            {{-- Controles de cámara --}}
            <div class="grid grid-cols-3 gap-2 mb-3">
                <button id="switchCameraBtn"
                        class="flex flex-col items-center justify-center gap-0.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-600 text-white text-[11px] font-semibold py-2.5 rounded-lg transition disabled:opacity-40">
                    <span class="text-lg leading-none">🔄</span>
                    <span>Cambiar</span>
                </button>
                <button id="torchBtn"
                        class="flex flex-col items-center justify-center gap-0.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-600 text-white text-[11px] font-semibold py-2.5 rounded-lg transition disabled:opacity-40">
                    <span class="text-lg leading-none">🔦</span>
                    <span>Linterna</span>
                </button>
                <button id="pauseBtn"
                        class="flex flex-col items-center justify-center gap-0.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-600 text-white text-[11px] font-semibold py-2.5 rounded-lg transition">
                    <span id="pauseIcon" class="text-lg leading-none">⏸</span>
                    <span id="pauseText">Pausar</span>
                </button>
            </div>

            {{-- Caja de resultado --}}
            <div id="result"
                 class="rounded-xl p-4 text-center font-semibold text-sm min-h-[68px] flex flex-col items-center justify-center gap-1 bg-slate-800 text-slate-300 transition-all">
                Apunta la cámara al código QR…
            </div>

            {{-- Lista de escaneos recientes --}}
            <div class="mt-4 bg-slate-800 rounded-xl overflow-hidden">
                <div class="px-3 py-2 border-b border-slate-700 flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-300 uppercase tracking-wide">
                        Escaneos recientes
                    </h3>
                    <span id="sessionCount" class="text-[10px] text-slate-500">0 en esta sesión</span>
                </div>
                <ul id="recentList" class="divide-y divide-slate-700 max-h-64 overflow-y-auto">
                    <li class="px-3 py-3 text-xs text-slate-500 text-center">
                        Los escaneos aparecerán aquí
                    </li>
                </ul>
            </div>

            {{-- Registro manual (si está permitido) --}}
            @if($actividad->permite_manual)
                <details class="mt-4 bg-slate-800 rounded-xl overflow-hidden">
                    <summary class="px-3 py-3 cursor-pointer text-sm font-semibold text-slate-300 flex items-center justify-between">
                        <span>✍️ Registro manual</span>
                        <span class="text-slate-500 text-xs">Toca para expandir</span>
                    </summary>
                    <div class="px-3 pb-3 border-t border-slate-700 pt-3">
                        <p class="text-xs text-slate-400 mb-3">
                            Si alguien no trae su QR, ingresa su CI para registrarlo manualmente.
                        </p>
                        <form id="manualForm" class="flex gap-2">
                            <input type="text" id="manualCi" inputmode="numeric" pattern="[0-9]*"
                                   placeholder="CI del empleado"
                                   class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
                            <button type="submit"
                                    class="bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition whitespace-nowrap">
                                Registrar
                            </button>
                        </form>
                    </div>
                </details>
            @endif
        </div>
    </main>

    {{-- ═══════════ FOOTER DE ESTADO ═══════════ --}}
    <footer class="fixed bottom-0 left-0 right-0 bg-slate-900 border-t border-slate-800 px-4 py-2 z-20">
        <div class="flex items-center justify-between text-[11px] text-slate-400">
            <span id="statusText">⚪ Iniciando…</span>
            <span id="clockText">{{ now()->format('H:i') }}</span>
        </div>
    </footer>

    <script>
    // ═══════════════════════════════════════════════════════════════
    //  CONFIGURACIÓN
    // ═══════════════════════════════════════════════════════════════
    const URL_REGISTRO   = "{{ route('actividades.asistencia.registrar', $actividad) }}";
    const URL_MANUAL     = "{{ route('actividades.asistencia.manual', $actividad) }}";
    const CSRF           = document.querySelector('meta[name="csrf-token"]').content;
    const ACTIVIDAD_ID   = {{ $actividad->id }};
    const DEBOUNCE_MS    = 3000;   // Ignorar el mismo QR durante 3 s
    const MAX_RECIENTES  = 10;     // Cuántos escaneos mostrar

    // ═══════════════════════════════════════════════════════════════
    //  DOM
    // ═══════════════════════════════════════════════════════════════
    const loadingBox    = document.getElementById('loadingBox');
    const errorBox      = document.getElementById('errorBox');
    const errorMessage  = document.getElementById('errorMessage');
    const scannerWrapper= document.getElementById('scannerWrapper');
    const resultBox     = document.getElementById('result');
    const contadorEl    = document.getElementById('contador');
    const recentList    = document.getElementById('recentList');
    const sessionCount  = document.getElementById('sessionCount');
    const statusText    = document.getElementById('statusText');
    const clockText     = document.getElementById('clockText');
    const cameraBarFill = document.getElementById('cameraBarFill');

    // ═══════════════════════════════════════════════════════════════
    //  ESTADO GLOBAL
    // ═══════════════════════════════════════════════════════════════
    let scanner          = null;
    let camarasDisponibles = [];
    let camaraActualIdx  = 0;
    let scannerPausado   = false;
    let torchEncendido   = false;
    let wakeLock         = null;
    let ultimoQr         = null;
    let ultimoTimestamp  = 0;
    let totalSesion      = 0;

    // ═══════════════════════════════════════════════════════════════
    //  UTILIDADES
    // ═══════════════════════════════════════════════════════════════

    // Actualiza el reloj del footer cada minuto
    function actualizarReloj() {
        const d = new Date();
        clockText.textContent = String(d.getHours()).padStart(2, '0') + ':' +
                                String(d.getMinutes()).padStart(2, '0');
    }
    setInterval(actualizarReloj, 30000);
    actualizarReloj();

    // Actualiza el texto de estado
    function setStatus(texto, color = 'text-slate-400') {
        statusText.className = color;
        statusText.textContent = texto;
    }

    // Pinta la caja de resultado
    function pintarResultado(clase, mensaje, info = '') {
        const clases = {
            'ok':       'bg-green-900/60 text-green-100 border border-green-500',
            'tardanza': 'bg-yellow-900/60 text-yellow-100 border border-yellow-500',
            'error':    'bg-red-900/60 text-red-100 border border-red-500',
            'info':     'bg-slate-800 text-slate-300',
        };
        resultBox.className = `rounded-xl p-4 text-center font-semibold text-sm min-h-[68px] flex flex-col items-center justify-center gap-1 transition-all animate-in ${clases[clase] || clases.info}`;
        resultBox.innerHTML = `
            <div class="text-base leading-tight">${mensaje}</div>
            ${info ? `<div class="text-xs opacity-80">${info}</div>` : ''}
        `;
    }

    // Sonido con Web Audio API
    function beep(tipo) {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx  = new AudioCtx();
            const osc  = ctx.createOscillator();
            const gain = ctx.createGain();

            if (tipo === 'ok') {
                osc.frequency.value = 880;
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.2);
            } else if (tipo === 'tardanza') {
                osc.frequency.value = 660;
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
            } else {
                osc.frequency.value = 220;
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
            }

            osc.type = 'sine';
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.4);
        } catch (e) { /* silencio */ }
    }

    // Vibración
    function vibrar(patron) {
        if (navigator.vibrate) navigator.vibrate(patron);
    }

    // Añadir escaneo a la lista de recientes
    function agregarReciente({ nombre, ci, hora, estado, exito }) {
        // Quita el placeholder
        const placeholder = recentList.querySelector('li.text-slate-500');
        if (placeholder) placeholder.remove();

        const colores = {
            ok:       'text-green-400',
            tardanza: 'text-yellow-400',
            error:    'text-red-400',
        };

        const li = document.createElement('li');
        li.className = 'px-3 py-2.5 flex items-center gap-3 animate-in';
        li.innerHTML = `
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-sm">
                ${exito ? '✓' : '✕'}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">${nombre}</p>
                <p class="text-[11px] text-slate-400">CI: ${ci} · ${hora}</p>
            </div>
            <span class="text-[10px] font-semibold uppercase ${colores[estado] || 'text-slate-400'}">
                ${estado === 'tardanza' ? 'Tarde' : estado === 'ok' ? 'OK' : 'Error'}
            </span>
        `;
        recentList.prepend(li);

        // Limita la lista
        while (recentList.children.length > MAX_RECIENTES) {
            recentList.lastChild.remove();
        }

        totalSesion++;
        sessionCount.textContent = `${totalSesion} en esta sesión`;
    }

    // ═══════════════════════════════════════════════════════════════
    //  MANEJO DE CÁMARA
    // ═══════════════════════════════════════════════════════════════

    async function iniciarEscaner() {
        loadingBox.classList.remove('hidden');
        errorBox.classList.add('hidden');
        scannerWrapper.classList.add('hidden');

        try {
            // Verifica contexto seguro (HTTPS o localhost)
            if (!window.isSecureContext && location.hostname !== 'localhost') {
                throw new Error('La cámara requiere HTTPS. Abre la app con https://');
            }

            // Carga las cámaras disponibles
            camarasDisponibles = await Html5Qrcode.getCameras();

            if (!camarasDisponibles || camarasDisponibles.length === 0) {
                throw new Error('No se detectaron cámaras en este dispositivo.');
            }

            // Prioriza cámara trasera
            camaraActualIdx = camarasDisponibles.findIndex(c =>
                /back|rear|environment/i.test(c.label)
            );
            if (camaraActualIdx === -1) camaraActualIdx = 0;

            await arrancarCamara(camaraActualIdx);

        } catch (err) {
            console.error('Error al iniciar escáner:', err);
            loadingBox.classList.add('hidden');
            errorBox.classList.remove('hidden');
            errorMessage.textContent = traducirError(err);
            setStatus('🔴 Cámara no disponible', 'text-red-400');
        }
    }

    async function arrancarCamara(idx) {
        // Limpia contenedor
        const readerEl = document.getElementById('reader');
        readerEl.innerHTML = '';

        scanner = new Html5Qrcode('reader');

        const config = {
            fps: 10,
            qrbox: (vw, vh) => {
                const min = Math.min(vw, vh);
                const size = Math.floor(min * 0.7);
                return { width: size, height: size };
            },
            aspectRatio: 1.0,
            disableFlip: false,
        };

        await scanner.start(
            camarasDisponibles[idx].id,
            config,
            onScanSuccess,
            onScanError // ignorar errores de lectura (normales)
        );

        // UI lista
        loadingBox.classList.add('hidden');
        errorBox.classList.add('hidden');
        scannerWrapper.classList.remove('hidden');
        cameraBarFill.style.width = '100%';
        setStatus('🟢 Escáner activo', 'text-green-400');

        // Intenta mantener la pantalla encendida
        solicitarWakeLock();
    }

    async function detenerCamara() {
        if (!scanner) return;
        try {
            const estado = scanner.getState();
            if (estado === Html5QrcodeScannerState.SCANNING ||
                estado === Html5QrcodeScannerState.PAUSED) {
                await scanner.stop();
                scanner.clear();
            }
        } catch (e) { /* ignorar */ }
        scanner = null;
    }

    async function cambiarCamara() {
        if (camarasDisponibles.length < 2) return;

        await detenerCamara();
        camaraActualIdx = (camaraActualIdx + 1) % camarasDisponibles.length;
        torchEncendido = false;
        actualizarBotonTorch(false);

        try {
            await arrancarCamara(camaraActualIdx);
            pintarResultado('info', `📷 Cámara: ${camarasDisponibles[camaraActualIdx].label || '#' + (camaraActualIdx + 1)}`);
        } catch (err) {
            pintarResultado('error', 'No se pudo cambiar la cámara');
            beep('error');
        }
    }

    async function alternarPausa() {
        if (!scanner) return;
        const pauseIcon = document.getElementById('pauseIcon');
        const pauseText = document.getElementById('pauseText');

        try {
            if (scannerPausado) {
                scanner.resume();
                scannerPausado = false;
                pauseIcon.textContent = '⏸';
                pauseText.textContent = 'Pausar';
                setStatus('🟢 Escáner activo', 'text-green-400');
            } else {
                scanner.pause(true);
                scannerPausado = true;
                pauseIcon.textContent = '▶';
                pauseText.textContent = 'Reanudar';
                setStatus('⏸ Pausado', 'text-yellow-400');
                pintarResultado('info', '⏸ Escáner pausado');
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function alternarTorch() {
        if (!scanner) return;
        try {
            const track = scanner.getRunningTrackCameraCapabilities();
            if (!track || typeof track.torchFeature !== 'function') {
                pintarResultado('info', '🔦 Linterna no soportada');
                return;
            }
            const torch = track.torchFeature();
            torchEncendido = !torchEncendido;
            await torch.apply(torchEncendido);
            actualizarBotonTorch(torchEncendido);
        } catch (e) {
            console.error('Error torch:', e);
            pintarResultado('info', '🔦 Linterna no disponible');
        }
    }

    function actualizarBotonTorch(activo) {
        const btn = document.getElementById('torchBtn');
        if (activo) {
            btn.classList.remove('bg-slate-800', 'hover:bg-slate-700');
            btn.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
        } else {
            btn.classList.add('bg-slate-800', 'hover:bg-slate-700');
            btn.classList.remove('bg-yellow-600', 'hover:bg-yellow-700');
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  WAKE LOCK (evita que la pantalla se apague)
    // ═══════════════════════════════════════════════════════════════
    async function solicitarWakeLock() {
        if (!('wakeLock' in navigator)) return;
        try {
            wakeLock = await navigator.wakeLock.request('screen');
        } catch (e) { /* ignorar */ }
    }
    document.addEventListener('visibilitychange', async () => {
        if (document.visibilityState === 'visible' && !wakeLock) {
            await solicitarWakeLock();
        }
    });

    // ═══════════════════════════════════════════════════════════════
    //  MANEJO DE ESCANEO
    // ═══════════════════════════════════════════════════════════════

    function onScanSuccess(decodedText) {
        const ahora = Date.now();

        // Ignora el mismo QR en menos de DEBOUNCE_MS
        if (decodedText === ultimoQr && (ahora - ultimoTimestamp) < DEBOUNCE_MS) return;
        ultimoQr = decodedText;
        ultimoTimestamp = ahora;

        pintarResultado('info', '⏳ Procesando…');

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
            const hora = data.hora || new Date().toLocaleTimeString('es-BO', { hour12: false });

            if (data.success) {
                const clase = data.tipo === 'tardanza' ? 'tardanza' : 'ok';
                pintarResultado(
                    clase,
                    data.message,
                    `Hora: ${hora} · CI: ${data.persona?.ci ?? '—'}`
                );
                if (data.total !== undefined) contadorEl.textContent = data.total;

                beep(clase);
                vibrar(clase === 'ok' ? [80] : [40, 60, 40]);

                agregarReciente({
                    nombre: data.persona?.nombre ?? 'Empleado',
                    ci: data.persona?.ci ?? '—',
                    hora,
                    estado: clase,
                    exito: true,
                });
            } else {
                pintarResultado('error', data.message || 'No reconocido', '');
                beep('error');
                vibrar([120, 60, 120]);

                agregarReciente({
                    nombre: data.persona?.nombre ?? 'QR no reconocido',
                    ci: data.persona?.ci ?? '—',
                    hora,
                    estado: 'error',
                    exito: false,
                });
            }
        })
        .catch(err => {
            console.error('Error fetch:', err);
            pintarResultado('error', 'Error de conexión');
            beep('error');
            vibrar(120);
        });
    }

    function onScanError(err) {
        // Silencioso: errores de "no QR detectado" son constantes y normales
    }

    // ═══════════════════════════════════════════════════════════════
    //  REGISTRO MANUAL (fallback sin QR)
    // ═══════════════════════════════════════════════════════════════
    const manualForm = document.getElementById('manualForm');
    if (manualForm) {
        manualForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const ci = document.getElementById('manualCi').value.trim();
            if (!ci) return;

            try {
                const res = await fetch(URL_MANUAL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ ci, qr_texto: `CI: ${ci}` })
                });
                const data = await res.json();

                if (data.success) {
                    pintarResultado('ok', data.message, `Manual · CI: ${ci}`);
                    if (data.total !== undefined) contadorEl.textContent = data.total;
                    beep('ok');
                    vibrar(80);
                    document.getElementById('manualCi').value = '';
                } else {
                    pintarResultado('error', data.message || 'No se pudo registrar', '');
                    beep('error');
                }
            } catch (err) {
                pintarResultado('error', 'Error de conexión');
                beep('error');
            }
        });
    }

    // ═══════════════════════════════════════════════════════════════
    //  TRADUCCIÓN DE ERRORES DE CÁMARA
    // ═══════════════════════════════════════════════════════════════
    function traducirError(err) {
        const msg = String(err?.message || err || '').toLowerCase();
        if (msg.includes('permission') || msg.includes('denied') || msg.includes('notallowed')) {
            return 'Permiso de cámara denegado. Habilítalo en los ajustes del navegador y vuelve a intentar.';
        }
        if (msg.includes('notfound') || msg.includes('no camera') || msg.includes('not detected')) {
            return 'No se encontró ninguna cámara en este dispositivo.';
        }
        if (msg.includes('notreadable') || msg.includes('in use')) {
            return 'La cámara está siendo usada por otra aplicación. Ciérrala y vuelve a intentar.';
        }
        if (msg.includes('https') || msg.includes('secure')) {
            return 'La cámara solo funciona con HTTPS. Abre la app con https:// o usa localhost.';
        }
        return err?.message || 'Error desconocido al acceder a la cámara.';
    }

    // ═══════════════════════════════════════════════════════════════
    //  EVENTOS
    // ═══════════════════════════════════════════════════════════════
    document.getElementById('switchCameraBtn').addEventListener('click', cambiarCamara);
    document.getElementById('pauseBtn').addEventListener('click', alternarPausa);
    document.getElementById('torchBtn').addEventListener('click', alternarTorch);
    document.getElementById('reintentarBtn').addEventListener('click', iniciarEscaner);

    // Botón "atrás" del navegador → detener cámara correctamente
    window.addEventListener('beforeunload', () => { detenerCamara(); });

    // ═══════════════════════════════════════════════════════════════
    //  ARRANQUE
    // ═══════════════════════════════════════════════════════════════
    iniciarEscaner();
    </script>
</body>
</html>