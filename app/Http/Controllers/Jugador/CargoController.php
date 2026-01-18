<?php

namespace App\Http\Controllers\Jugador;

use App\Http\Controllers\Controller;
use App\Models\CargoJugador;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CargoController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of cargos for the authenticated jugador.
     */
    public function index(Request $request): Response
    {
        $query = CargoJugador::where('user_id_jugador', Auth::id())
            ->with(['club', 'conceptos', 'pagos']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $cargos = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Jugador/Cargos/Index', [
            'cargos' => $cargos,
            'filters' => $request->only(['estado']),
        ]);
    }

    /**
     * Display the specified cargo.
     */
    public function show(CargoJugador $cargo): Response
    {
        $this->authorize('view', $cargo);

        $cargo->load(['club', 'conceptos', 'pagos.archivoAdjuntos']);

        return Inertia::render('Jugador/Cargos/Show', [
            'cargo' => $cargo,
        ]);
    }
}
