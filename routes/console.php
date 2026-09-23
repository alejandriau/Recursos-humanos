<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\GenerarAsistenciaJob;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ═══════════════════════════════════════════════════════════
// VACACIONES (ya lo tenías)
// ═══════════════════════════════════════════════════════════
Schedule::command('vacaciones:generar-periodos')->dailyAt('08:00');


// ═══════════════════════════════════════════════════════════
// DESCARGAS DE BIOMÉTRICOS (3 veces al día)
// ═══════════════════════════════════════════════════════════

// Mañana: marcaciones de entrada del turno día
Schedule::command('zk:importar-todos')
    ->dailyAt('09:15')
    ->withoutOverlapping(30)
    ->appendOutputTo(storage_path('logs/zkteco-schedule.log'));

// Tarde/noche: salida del turno día + entrada del turno noche
Schedule::command('zk:importar-todos')
    ->dailyAt('19:00')
    ->withoutOverlapping(30)
    ->appendOutputTo(storage_path('logs/zkteco-schedule.log'));

// Final de la noche: DESPUÉS de medianoche, para capturar todo hasta
// las 23:59 del turno noche (a las 21:30/23:xx todavía no terminan de marcar).
Schedule::command('zk:importar-todos')
    ->dailyAt('00:15')
    ->withoutOverlapping(30)
    ->appendOutputTo(storage_path('logs/zkteco-schedule.log'));

// ═══════════════════════════════════════════════════════════
// RE-GENERAR ASISTENCIA (ventana móvil de 10 días)
// Corre DESPUÉS del import de las 00:15, con margen de 30 min para
// que termine de bajar las marcaciones de los 8 biométricos.
// ═══════════════════════════════════════════════════════════
Schedule::call(function () {
    $inicio = Carbon::today()->subDays(10)->toDateString();
    $fin = Carbon::today()->toDateString();

    GenerarAsistenciaJob::dispatch($inicio, $fin);

    \Log::info("Scheduler: Asistencia regenerada automáticamente del {$inicio} al {$fin}");
})->dailyAt('00:45');

// Laravel 10: app/Console/Kernel.php
// Laravel 11+: routes/console.php

Schedule::command('beneficios:replicar-gestion')->dailyAt('00:05');

// ═══════════════════════════════════════════════════════════
// CIERRE DEFINITIVO: lo "pendiente" pasa a falta real
// Corre después de que termine el regenerado de las 00:45.
// ═══════════════════════════════════════════════════════════
Schedule::command('asistencia:cierre-diario')->dailyAt('01:15');

// Backup por si falla el cierre
Schedule::command('asistencia:cierre-diario')->dailyAt('03:00');