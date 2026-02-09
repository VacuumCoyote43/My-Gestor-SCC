<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePagoRequest;
use App\Http\Requests\ValidatePagoRequest;
use App\Http\Requests\RejectPagoRequest;
use App\Models\Pago;
use App\Models\Factura;
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
    public function create(Factura $factura): Response
    {
        $this->authorize('view', $factura);

        $totalPagadoRegistrado = $factura->pagos()
            ->whereIn('estado_pago', [Pago::ESTADO_REGISTRADO, Pago::ESTADO_VALIDADO])
            ->sum('importe');

        return Inertia::render('Club/Pagos/Create', [
            'factura' => $factura->load('conceptos')->setAttribute('total_pagado_registrado', $totalPagadoRegistrado),
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
                'factura_id' => $request->factura_id,
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

            // Actualizar estado de la factura
            if ($pago->factura) {
                $pago->factura->update(['estado' => Factura::ESTADO_PAYMENT_REGISTERED]);
            }

            // Log de auditoría
            $this->auditLogger->logPaymentRegistered($pago, $request->importe, 'Pago registrado por gestor club');

            DB::commit();

            $redirectRoute = $request->factura_id 
                ? route('club.facturas.show', $request->factura_id)
                : route('club.cargos.show', $request->cargo_jugador_id);

            return redirect($redirectRoute)
                ->with('success', 'Pago registrado correctamente. Pendiente de validación.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }

    /**
     * Validate a payment (for cargos jugadores).
     */
    public function validate(ValidatePagoRequest $request, Pago $pago): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $pago->update([
                'estado_pago' => Pago::ESTADO_VALIDADO,
                'validated_at' => now(),
                'validated_by' => Auth::id(),
            ]);

            // Log de auditoría
            $this->auditLogger->logValidated($pago, $request->comentario);

            // Recalcular estado del cargo
            if ($pago->cargoJugador) {
                $pago->cargoJugador->recalcularEstado();
            }

            DB::commit();

            return back()->with('success', 'Pago validado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al validar el pago: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a payment (for cargos jugadores).
     */
    public function reject(RejectPagoRequest $request, Pago $pago): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $pago->update([
                'estado_pago' => Pago::ESTADO_RECHAZADO,
            ]);

            // Log de auditoría
            $this->auditLogger->logRejected($pago, $request->comentario);

            // Cambiar estado del cargo a pending_payment
            if ($pago->cargoJugador) {
                $pago->cargoJugador->update(['estado' => \App\Models\CargoJugador::ESTADO_PENDING_PAYMENT]);
            }

            DB::commit();

            return back()->with('success', 'Pago rechazado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al rechazar el pago: ' . $e->getMessage()]);
        }
    }
}
