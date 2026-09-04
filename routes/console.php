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
Schedule::command('zk:importar-todos')
    ->dailyAt('09:15')
    ->withoutOverlapping(30)
    ->appendOutputTo(storage_path('logs/zkteco-schedule.log'));

Schedule::command('zk:importar-todos')
    ->dailyAt('19:30')
    ->withoutOverlapping(30)
    ->appendOutputTo(storage_path('logs/zkteco-schedule.log'));

// Barrido nocturno: recoge marcaciones tardías + recupera cortes
Schedule::command('zk:importar-todos')
    ->dailyAt('21:30')
    ->withoutOverlapping(30)
    ->appendOutputTo(storage_path('logs/zkteco-schedule.log'));

// ═══════════════════════════════════════════════════════════
// RE-GENERAR ASISTENCIA (ventana móvil de 10 días)
// Esto cubre: cortes de biométricos, marcaciones atrasadas, recuperación
// ═══════════════════════════════════════════════════════════
Schedule::call(function () {
    $inicio = Carbon::today()->subDays(10)->toDateString();
    $fin = Carbon::today()->toDateString();
    
    GenerarAsistenciaJob::dispatch($inicio, $fin);
    
    \Log::info("Scheduler: Asistencia regenerada automáticamente del {$inicio} al {$fin}");
})->dailyAt('21:45');

// Laravel 10: app/Console/Kernel.php
// Laravel 11+: routes/console.php

$schedule->command('beneficios:replicar-gestion')->dailyAt('00:05');

// ═══════════════════════════════════════════════════════════
// CIERRE DEFINITIVO: lo "pendiente" pasa a falta real
// ═══════════════════════════════════════════════════════════
Schedule::command('asistencia:cierre-diario')->dailyAt('01:00');

// Backup por si falla el cierre
Schedule::command('asistencia:cierre-diario')->dailyAt('03:00');