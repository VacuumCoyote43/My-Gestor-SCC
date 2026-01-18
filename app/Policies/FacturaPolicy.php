<?php

namespace App\Policies;

use App\Models\Factura;
use App\Models\User;

class FacturaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProveedor() || $user->isGestorClub();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Factura $factura): bool
    {
        // Admin puede ver todas
        if ($user->isAdmin()) {
            return true;
        }

        // Proveedor puede ver las que emitió
        if ($user->isProveedor()) {
            return $factura->emisor_type === 'App\\Models\\Proveedor';
        }

        // Gestor club puede ver las recibidas por sus clubes
        if ($user->isGestorClub()) {
            return $factura->receptor_type === 'App\\Models\\Club';
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isProveedor();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Factura $factura): bool
    {
        // Solo proveedor puede actualizar sus facturas en estado draft
        return $user->isProveedor() 
            && $factura->emisor_type === 'App\\Models\\Proveedor'
            && $factura->estado === Factura::ESTADO_DRAFT;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Factura $factura): bool
    {
        // Solo proveedor puede eliminar sus facturas en estado draft
        return $user->isProveedor() 
            && $factura->emisor_type === 'App\\Models\\Proveedor'
            && $factura->estado === Factura::ESTADO_DRAFT;
    }

    /**
     * Determine whether the user can emit the factura.
     */
    public function emit(User $user, Factura $factura): bool
    {
        return $user->isProveedor() 
            && $factura->emisor_type === 'App\\Models\\Proveedor'
            && $factura->estado === Factura::ESTADO_DRAFT
            && $factura->canBeEmitted();
    }

    /**
     * Determine whether the user can cancel the factura.
     */
    public function cancel(User $user, Factura $factura): bool
    {
        return $user->isProveedor() 
            && $factura->emisor_type === 'App\\Models\\Proveedor'
            && $factura->estado !== Factura::ESTADO_PAID
            && $factura->estado !== Factura::ESTADO_CANCELLED;
    }
}
