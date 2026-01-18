<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BalanceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        protected BalanceService $balanceService
    ) {}

    /**
     * Display admin dashboard.
     */
    public function index(): Response
    {
        $stats = $this->balanceService->getAdminStats();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
        ]);
    }
}
