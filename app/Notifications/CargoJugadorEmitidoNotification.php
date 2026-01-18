<?php

namespace App\Notifications;

use App\Models\CargoJugador;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CargoJugadorEmitidoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected CargoJugador $cargo;

    /**
     * Create a new notification instance.
     */
    public function __construct(CargoJugador $cargo)
    {
        $this->cargo = $cargo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo Cargo Emitido - ' . $this->cargo->club->nombre)
            ->greeting('¡Hola ' . $this->cargo->jugador->name . '!')
            ->line('Se ha emitido un nuevo cargo a tu nombre.')
            ->line('**Club:** ' . $this->cargo->club->nombre)
            ->line('**Fecha de Emisión:** ' . $this->cargo->fecha_emision->format('d/m/Y'))
            ->line('**Total:** €' . number_format($this->cargo->total, 2, ',', '.'))
            ->line('**Fecha de Vencimiento:** ' . ($this->cargo->fecha_vencimiento ? $this->cargo->fecha_vencimiento->format('d/m/Y') : 'No especificada'))
            ->action('Ver Cargo', url('/jugador/cargos/' . $this->cargo->id))
            ->line('Por favor, revisa el cargo y procede con el pago correspondiente.')
            ->salutation('Saludos, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'cargo_id' => $this->cargo->id,
            'club' => $this->cargo->club->nombre,
            'total' => $this->cargo->total,
        ];
    }
}
