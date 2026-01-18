<?php

namespace App\Policies;

use App\Models\MensajeIncidencia;
use App\Models\Incidencia;
use App\Models\User;

class MensajeIncidenciaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Incidencia $incidencia): bool
    {
        // Admin puede ver todos los mensajes
        if ($user->isAdmin()) {
            return true;
        }

        // El creador de la incidencia puede ver los mensajes
        return $incidencia->user_id_creador === $user->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MensajeIncidencia $mensaje): bool
    {
        // Admin puede ver todos
        if ($user->isAdmin()) {
            return true;
        }

        // Mensajes internos solo para admin
        if ($mensaje->isInterno() && !$user->isAdmin()) {
            return false;
        }

        // El creador de la incidencia puede ver mensajes públicos
        return $mensaje->incidencia->user_id_creador === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Incidencia $incidencia): bool
    {
        // Admin puede crear mensajes en cualquier incidencia
        if ($user->isAdmin()) {
            return true;
        }

        // El creador puede crear mensajes en su incidencia
        return $incidencia->user_id_creador === $user->id;
    }

    /**
     * Determine whether the user can create internal messages.
     */
    public function createInternal(User $user): bool
    {
        return $user->isAdmin();
    }
}
