<?php

namespace App\Notifications;

use App\Models\Incidencia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncidenciaCreadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Incidencia $incidencia;

    /**
     * Create a new notification instance.
     */
    public function __construct(Incidencia $incidencia)
    {
        $this->incidencia = $incidencia;
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
        $creador = $this->incidencia->creador;
        $prioridad = ucfirst($this->incidencia->prioridad);
        $categoria = ucfirst($this->incidencia->categoria);

        return (new MailMessage)
            ->subject('Nueva Incidencia Creada - ' . $this->incidencia->asunto)
            ->greeting('¡Hola Administrador!')
            ->line('Se ha creado una nueva incidencia en el sistema.')
            ->line('**Asunto:** ' . $this->incidencia->asunto)
            ->line('**Categoría:** ' . $categoria)
            ->line('**Prioridad:** ' . $prioridad)
            ->line('**Creada por:** ' . $creador->name . ' (' . $creador->email . ')')
            ->line('**Fecha de Apertura:** ' . $this->incidencia->fecha_apertura->format('d/m/Y H:i'))
            ->action('Ver Incidencia', url('/admin/incidencias/' . $this->incidencia->id))
            ->line('Por favor, revise la incidencia y tome las acciones necesarias.')
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
            'incidencia_id' => $this->incidencia->id,
            'asunto' => $this->incidencia->asunto,
            'prioridad' => $this->incidencia->prioridad,
        ];
    }
}
