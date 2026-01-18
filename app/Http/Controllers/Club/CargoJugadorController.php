<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCargoJugadorRequest;
use App\Http\Requests\EmitCargoJugadorRequest;
use App\Models\CargoJugador;
use App\Models\CargoConcepto;
use App\Models\Club;
use App\Models\User;
use App\Services\AuditLoggerService;
use App\Notifications\CargoJugadorEmitidoNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CargoJugadorController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected AuditLoggerService $auditLogger
    ) {}

    /**
     * Display a listing of cargos.
     */
    public function index(Request $request): Response
    {
        // TODO: Filtrar por clubes del gestor actual
        $query = CargoJugador::with(['club', 'jugador', 'conceptos', 'pagos']);

        // Búsqueda por nombre de jugador
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('jugador', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por mes
        if ($request->filled('mes')) {
            $query->whereMonth('fecha_emision', $request->mes);
        }

        $cargos = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Club/Cargos/Index', [
            'cargos' => $cargos,
            'filters' => $request->only(['search', 'estado', 'mes']),
        ]);
    }

    /**
     * Show the form for creating a new cargo.
     */
    public function create(): Response
    {
        $clubes = Club::all();
        $jugadores = User::whereHas('role', function($query) {
            $query->where('nombre', 'jugador');
        })->get();

        return Inertia::render('Club/Cargos/Create', [
            'clubes' => $clubes,
            'jugadores' => $jugadores,
        ]);
    }

    /**
     * Store a newly created cargo in storage.
     */
    public function store(StoreCargoJugadorRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // Calcular total
            $total = 0;
            foreach ($request->conceptos as $concepto) {
                $total += $concepto['total_linea'];
            }

            // Crear cargo
            $cargo = CargoJugador::create([
                'club_id' => $request->club_id,
                'user_id_jugador' => $request->user_id_jugador,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'total' => $total,
                'estado' => CargoJugador::ESTADO_DRAFT,
                'created_by' => Auth::id(),
            ]);

            // Crear conceptos
            foreach ($request->conceptos as $concepto) {
                CargoConcepto::create([
                    'cargo_jugador_id' => $cargo->id,
                    'descripcion' => $concepto['descripcion'],
                    'total_linea' => $concepto['total_linea'],
                    'created_by' => Auth::id(),
                ]);
            }

            // Log de auditoría
            $this->auditLogger->logCreated($cargo, 'Cargo creado en estado draft');

            DB::commit();

            return redirect()->route('club.cargos.show', $cargo)
                ->with('success', 'Cargo creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el cargo: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified cargo.
     */
    public function show(CargoJugador $cargo): Response
    {
        $this->authorize('view', $cargo);

        $cargo->load(['club', 'jugador', 'conceptos', 'pagos.archivoAdjuntos', 'modificaciones.createdBy']);

        return Inertia::render('Club/Cargos/Show', [
            'cargo' => $cargo,
        ]);
    }

    /**
     * Emit the cargo (change status to pending_payment).
     */
    public function emit(EmitCargoJugadorRequest $request, CargoJugador $cargo): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $cargo->update(['estado' => CargoJugador::ESTADO_PENDING_PAYMENT]);

            // Log de auditoría
            $this->auditLogger->logEmitted($cargo, 'Cargo emitido');

            // Notificar al jugador
            $cargo->jugador->notify(new CargoJugadorEmitidoNotification($cargo));

            DB::commit();

            return redirect()->route('club.cargos.show', $cargo)
                ->with('success', 'Cargo emitido correctamente. Se ha notificado al jugador.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al emitir el cargo: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified cargo from storage.
     */
    public function destroy(CargoJugador $cargo): RedirectResponse
    {
        $this->authorize('delete', $cargo);

        DB::beginTransaction();
        try {
            // Log de auditoría antes de eliminar
            $this->auditLogger->logDeleted($cargo, 'Cargo eliminado');

            $cargo->delete();

            DB::commit();

            return redirect()->route('club.cargos.index')
                ->with('success', 'Cargo eliminado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar el cargo: ' . $e->getMessage()]);
        }
    }
}
