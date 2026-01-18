<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
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
     * Display proveedor dashboard.
     */
    public function index(): Response
    {
        // TODO: Obtener el proveedor asociado al usuario actual
        // Por ahora, obtenemos el primer proveedor como ejemplo
        $proveedor = Proveedor::first();

        $balance = $proveedor 
            ? $this->balanceService->getProveedorMonthlyBalance($proveedor->id)
            : null;

        return Inertia::render('Proveedor/Dashboard', [
            'proveedor' => $proveedor,
            'balance' => $balance,
        ]);
    }
}
