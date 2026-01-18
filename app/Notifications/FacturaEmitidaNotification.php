<?php

namespace App\Notifications;

use App\Models\Factura;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FacturaEmitidaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Factura $factura;

    /**
     * Create a new notification instance.
     */
    public function __construct(Factura $factura)
    {
        $this->factura = $factura;
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
        $emisor = $this->factura->emisor;
        $emisorNombre = $emisor instanceof \App\Models\Proveedor 
            ? $emisor->nombre_legal 
            : $emisor->nombre;

        return (new MailMessage)
            ->subject('Nueva Factura Emitida - ' . $this->factura->numero)
            ->greeting('¡Hola!')
            ->line('Se ha emitido una nueva factura a su nombre.')
            ->line('**Número de Factura:** ' . $this->factura->numero)
            ->line('**Serie:** ' . $this->factura->serie)
            ->line('**Emisor:** ' . $emisorNombre)
            ->line('**Fecha:** ' . $this->factura->fecha_factura->format('d/m/Y'))
            ->line('**Total:** €' . number_format($this->factura->total, 2, ',', '.'))
            ->line('**Fecha de Vencimiento:** ' . ($this->factura->fecha_vencimiento ? $this->factura->fecha_vencimiento->format('d/m/Y') : 'No especificada'))
            ->action('Ver Factura', url('/facturas/' . $this->factura->id))
            ->line('Por favor, revise la factura y proceda con el pago correspondiente.')
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
            'factura_id' => $this->factura->id,
            'numero' => $this->factura->numero,
            'total' => $this->factura->total,
        ];
    }
}
