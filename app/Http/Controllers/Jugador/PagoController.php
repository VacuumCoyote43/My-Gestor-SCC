<?php

namespace App\Http\Controllers\Jugador;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePagoRequest;
use App\Models\Pago;
use App\Models\CargoJugador;
use App\Models\ArchivoAdjunto;
use App\Services\AuditLoggerService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PagoController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected AuditLoggerService $auditLogger
    ) {}

    /**
     * Show the form for registering a new pago.
     */
    public function create(CargoJugador $cargo): Response
    {
        $this->authorize('view', $cargo);

        return Inertia::render('Jugador/Pagos/Create', [
            'cargo' => $cargo->load('conceptos'),
        ]);
    }

    /**
     * Store a newly created pago in storage.
     */
    public function store(StorePagoRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // Crear pago
            $pago = Pago::create([
                'cargo_jugador_id' => $request->cargo_jugador_id,
                'importe' => $request->importe,
                'fecha_pago' => $request->fecha_pago,
                'metodo_pago' => $request->metodo_pago,
                'estado_pago' => Pago::ESTADO_REGISTRADO,
                'created_by' => Auth::id(),
            ]);

            // Guardar justificantes (obligatorios)
            foreach ($request->file('archivos') as $archivo) {
                $path = $archivo->store('pagos/' . $pago->id, 'private');
                
                ArchivoAdjunto::create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'ruta' => $path,
                    'tipo' => $archivo->getMimeType(),
                    'tamano' => $archivo->getSize(),
                    'documentable_type' => 'App\\Models\\Pago',
                    'documentable_id' => $pago->id,
                    'created_by' => Auth::id(),
                ]);
            }

            // Actualizar estado del cargo
            if ($pago->cargoJugador) {
                $pago->cargoJugador->update(['estado' => CargoJugador::ESTADO_PAYMENT_REGISTERED]);
            }

            // Log de auditoría
            $this->auditLogger->logPaymentRegistered($pago, $request->importe, 'Pago registrado por jugador');

            DB::commit();

            return redirect()->route('jugador.cargos.show', $request->cargo_jugador_id)
                ->with('success', 'Pago registrado correctamente. Pendiente de validación por el club.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }
}
