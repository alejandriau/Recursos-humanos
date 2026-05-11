<?php

namespace App\Notifications;

use App\Models\Prestamo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevaSolicitudPrestamo extends Notification
{
    use Queueable;
    protected $prestamo;
    /**
     * Create a new notification instance.
     */
    public function __construct(Prestamo $prestamo)
    {
        $this->prestamo = $prestamo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Solo base de datos, también se puede agregar 'mail'
        //return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'prestamo_id' => $this->prestamo->id,
            'mensaje' => 'Nueva solicitud de préstamo de ' . ($this->prestamo->solicitante->name ?? 'Usuario'),
            'carpeta' => $this->prestamo->carpeta->nombrecompleto ?? 'Sin carpeta',
            'tipo' => 'nueva_solicitud',
            'url' => route('prestamos.index') // Ruta para gestión de préstamos
        ];
    }
}
