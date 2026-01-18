<?php

namespace App\Policies;

use App\Models\CargoJugador;
use App\Models\User;

class CargoJugadorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isGestorClub() || $user->isJugador();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CargoJugador $cargo): bool
    {
        // Admin puede ver todos
        if ($user->isAdmin()) {
            return true;
        }

        // Gestor club puede ver cargos de sus clubes
        if ($user->isGestorClub()) {
            return true; // TODO: verificar que el club pertenece al gestor
        }

        // Jugador puede ver sus propios cargos
        if ($user->isJugador()) {
            return $cargo->user_id_jugador === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isGestorClub();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CargoJugador $cargo): bool
    {
        // Solo gestor club puede actualizar cargos en estado draft
        return $user->isGestorClub() 
            && $cargo->estado === CargoJugador::ESTADO_DRAFT;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CargoJugador $cargo): bool
    {
        // Solo gestor club puede eliminar cargos en estado draft
        return $user->isGestorClub() 
            && $cargo->estado === CargoJugador::ESTADO_DRAFT;
    }

    /**
     * Determine whether the user can emit the cargo.
     */
    public function emit(User $user, CargoJugador $cargo): bool
    {
        return $user->isGestorClub() 
            && $cargo->estado === CargoJugador::ESTADO_DRAFT
            && $cargo->canBeEmitted();
    }

    /**
     * Determine whether the user can cancel the cargo.
     */
    public function cancel(User $user, CargoJugador $cargo): bool
    {
        return $user->isGestorClub() 
            && $cargo->estado !== CargoJugador::ESTADO_PAID
            && $cargo->estado !== CargoJugador::ESTADO_CANCELLED;
    }
}
