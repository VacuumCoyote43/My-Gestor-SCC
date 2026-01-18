<?php

namespace App\Http\Controllers\Jugador;

use App\Http\Controllers\Controller;
use App\Services\BalanceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected BalanceService $balanceService
    ) {}

    /**
     * Display jugador dashboard.
     */
    public function index(): Response
    {
        $deuda = $this->balanceService->getJugadorDeuda(Auth::id());

        return Inertia::render('Jugador/Dashboard', [
            'deuda' => $deuda,
        ]);
    }
}
