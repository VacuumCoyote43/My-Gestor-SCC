<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardRedirectController extends Controller
{
    /**
     * Redirect user to their role-specific dashboard.
     */
    public function __invoke(): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            return redirect()->route('login');
        }

        return match ($user->role->nombre) {
            'admin' => redirect()->route('admin.dashboard'),
            'proveedor' => redirect()->route('proveedor.dashboard'),
            'gestor_club' => redirect()->route('club.dashboard'),
            'jugador' => redirect()->route('jugador.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
