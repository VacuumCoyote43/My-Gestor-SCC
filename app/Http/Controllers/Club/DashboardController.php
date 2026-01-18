<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Club;
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
     * Display club dashboard.
     */
    public function index(): Response
    {
        // TODO: Obtener los clubes asociados al gestor actual
        // Por ahora, obtenemos el primer club como ejemplo
        $club = Club::first();

        $balance = $club 
            ? $this->balanceService->getClubMonthlyBalance($club->id)
            : null;

        return Inertia::render('Club/Dashboard', [
            'club' => $club,
            'balance' => $balance,
        ]);
    }
}
