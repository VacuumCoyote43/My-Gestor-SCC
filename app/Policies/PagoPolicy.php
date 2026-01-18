<?php

namespace App\Policies;

use App\Models\Pago;
use App\Models\User;

class PagoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos los roles autenticados pueden ver pagos relacionados con ellos
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pago $pago): bool
    {
        // Admin puede ver todos
        if ($user->isAdmin()) {
            return true;
        }

        // Proveedor puede ver pagos de sus facturas
        if ($user->isProveedor() && $pago->factura) {
            return $pago->factura->emisor_type === 'App\\Models\\Proveedor';
        }

        // Gestor club puede ver pagos de facturas recibidas o de cargos de sus clubes
        if ($user->isGestorClub()) {
            if ($pago->factura) {
                return $pago->factura->receptor_type === 'App\\Models\\Club';
            }
            if ($pago->cargoJugador) {
                return true; // TODO: verificar que el club pertenece al gestor
            }
        }

        // Jugador puede ver pagos de sus cargos
        if ($user->isJugador() && $pago->cargoJugador) {
            return $pago->cargoJugador->user_id_jugador === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models (registrar pago).
     */
    public function create(User $user): bool
    {
        return $user->isGestorClub() || $user->isJugador();
    }

    /**
     * Determine whether the user can validate the pago.
     */
    public function validate(User $user, Pago $pago): bool
    {
        // Proveedor puede validar pagos de sus facturas
        if ($user->isProveedor() && $pago->factura) {
            return $pago->factura->emisor_type === 'App\\Models\\Proveedor'
                && $pago->canBeValidated();
        }

        // Gestor club puede validar pagos de cargos de sus clubes
        if ($user->isGestorClub() && $pago->cargoJugador) {
            return $pago->canBeValidated(); // TODO: verificar que el club pertenece al gestor
        }

        return false;
    }

    /**
     * Determine whether the user can reject the pago.
     */
    public function reject(User $user, Pago $pago): bool
    {
        // Misma lógica que validate
        return $this->validate($user, $pago);
    }
}
