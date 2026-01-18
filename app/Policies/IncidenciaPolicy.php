<?php

namespace App\Policies;

use App\Models\Incidencia;
use App\Models\User;

class IncidenciaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos los usuarios autenticados pueden ver incidencias
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Incidencia $incidencia): bool
    {
        // Admin puede ver todas
        if ($user->isAdmin()) {
            return true;
        }

        // El creador puede ver su incidencia
        return $incidencia->user_id_creador === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Todos los usuarios autenticados pueden crear incidencias
    }

    /**
     * Determine whether the user can update the model (cambiar estado).
     */
    public function update(User $user, Incidencia $incidencia): bool
    {
        // Solo admin puede cambiar estado de incidencias
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Incidencia $incidencia): bool
    {
        // Solo admin puede eliminar incidencias
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can close the incidencia.
     */
    public function close(User $user, Incidencia $incidencia): bool
    {
        return $user->isAdmin();
    }
}
