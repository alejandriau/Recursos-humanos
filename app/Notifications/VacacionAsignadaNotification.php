<?php

namespace App\Notifications;

use App\Models\VacacionPeriodo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class VacacionAsignadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected VacacionPeriodo $periodo)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $p = $this->periodo;
        $mensaje = (new MailMessage)
            ->subject('Se te asignaron nuevas vacaciones')
            ->greeting('¡Hola!')
            ->line("Se habilitó tu período de vacaciones N° {$p->numero_periodo}, correspondiente a {$p->anios_antiguedad} años de antigüedad.")
            ->line("Días asignados: {$p->dias_asignados}");

        if ($p->dias_arrastre > 0) {
            $mensaje->line("Además se arrastraron {$p->dias_arrastre} días del período anterior.");
        }

        $mensaje->line("Saldo disponible: {$p->saldo_disponible} días.")
            ->line('Ya puedes solicitar tus vacaciones desde ' . $p->fecha_habilitacion->format('d/m/Y') . '.');

        return $mensaje;
    }

    public function toArray($notifiable): array
    {
        return [
            'tipo' => 'vacacion_asignada',
            'periodo_id' => $this->periodo->id,
            'numero_periodo' => $this->periodo->numero_periodo,
            'dias_asignados' => $this->periodo->dias_asignados,
            'dias_arrastre' => $this->periodo->dias_arrastre,
            'saldo_disponible' => $this->periodo->saldo_disponible,
            'fecha_habilitacion' => $this->periodo->fecha_habilitacion->format('Y-m-d'),
            'mensaje' => "Se te asignaron {$this->periodo->dias_asignados} días de vacación (período {$this->periodo->numero_periodo}). Ya puedes solicitarlas.",
        ];
    }
}