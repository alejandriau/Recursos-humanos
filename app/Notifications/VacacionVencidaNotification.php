<?php

namespace App\Notifications;

use App\Models\VacacionPeriodo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class VacacionVencidaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param VacacionPeriodo $periodoVencido El período que se venció (perdió días).
     * @param VacacionPeriodo|null $periodoNuevo Si en el mismo ciclo se asignó un período nuevo, se avisa junto.
     */
    public function __construct(
        protected VacacionPeriodo $periodoVencido,
        protected ?VacacionPeriodo $periodoNuevo = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $v = $this->periodoVencido;
        $anio = $v->fecha_habilitacion->year;

        $mensaje = (new MailMessage)
            ->subject('Vencimiento de vacaciones')
            ->greeting('¡Hola!')
            ->line("Tu período de vacaciones N° {$v->numero_periodo} (gestión {$anio}) venció por acumulación: se perdieron {$v->dias_vencidos} días que no se usaron a tiempo.");

        if ($this->periodoNuevo) {
            $n = $this->periodoNuevo;
            $mensaje->line("Al mismo tiempo, se te asignaron {$n->dias_asignados} días nuevos correspondientes a tu período N° {$n->numero_periodo}, ya disponibles para usar.")
                ->line("Saldo disponible actual: {$n->saldo_disponible} días.");
        }

        $mensaje->line('Recuerda solicitar tus vacaciones con anticipación para evitar que se vuelvan a vencer.');

        return $mensaje;
    }

    public function toArray($notifiable): array
    {
        return [
            'tipo' => 'vacacion_vencida',
            'periodo_vencido_id' => $this->periodoVencido->id,
            'numero_periodo_vencido' => $this->periodoVencido->numero_periodo,
            'dias_perdidos' => $this->periodoVencido->dias_vencidos,
            'periodo_nuevo_id' => $this->periodoNuevo?->id,
            'dias_asignados_nuevo' => $this->periodoNuevo?->dias_asignados,
            'mensaje' => $this->periodoNuevo
                ? "Vencieron {$this->periodoVencido->dias_vencidos} días del período {$this->periodoVencido->numero_periodo}, pero se te asignaron {$this->periodoNuevo->dias_asignados} días nuevos."
                : "Vencieron {$this->periodoVencido->dias_vencidos} días del período {$this->periodoVencido->numero_periodo}.",
        ];
    }
}