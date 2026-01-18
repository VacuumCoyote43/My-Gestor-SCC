<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidatePagoRequest;
use App\Http\Requests\RejectPagoRequest;
use App\Models\Pago;
use App\Services\AuditLoggerService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected AuditLoggerService $auditLogger
    ) {}

    /**
     * Validate a payment.
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

            // Recalcular estado de la factura
            if ($pago->factura) {
                $pago->factura->recalcularEstado();
            }

            DB::commit();

            return back()->with('success', 'Pago validado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al validar el pago: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a payment.
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

            // Cambiar estado de la factura a pending_payment
            if ($pago->factura) {
                $pago->factura->update(['estado' => \App\Models\Factura::ESTADO_PENDING_PAYMENT]);
            }

            DB::commit();

            return back()->with('success', 'Pago rechazado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al rechazar el pago: ' . $e->getMessage()]);
        }
    }
}
