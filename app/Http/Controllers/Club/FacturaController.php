<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFacturaRequest;
use App\Http\Requests\UpdateFacturaRequest;
use App\Http\Requests\EmitFacturaRequest;
use App\Models\Factura;
use App\Models\FacturaConcepto;
use App\Models\Club;
use App\Models\Proveedor;
use App\Models\ArchivoAdjunto;
use App\Services\AuditLoggerService;
use App\Services\InvoiceNumberService;
use App\Notifications\FacturaEmitidaNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class FacturaController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected AuditLoggerService $auditLogger,
        protected InvoiceNumberService $invoiceNumberService
    ) {}

    /**
     * Display a listing of facturas emitidas.
     */
    public function index(Request $request): Response
    {
        // Obtener el club del usuario autenticado
        $user = Auth::user();
        $club = Club::where('gestor_id', $user->id)->first();

        if (!$club) {
            abort(403, 'No tienes un club asignado.');
        }

        $query = Factura::with(['receptor', 'conceptos', 'pagos'])
            ->where('emisor_type', Club::class)
            ->where('emisor_id', $club->id);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('fecha_factura', $request->mes);
        }

        $facturas = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Club/Facturas/Index', [
            'facturas' => $facturas,
            'filters' => $request->only(['estado', 'mes']),
            'club' => $club,
        ]);
    }

    /**
     * Show the form for creating a new factura.
     */
    public function create(): Response
    {
        $user = Auth::user();
        $club = Club::where('gestor_id', $user->id)->first();

        if (!$club) {
            abort(403, 'No tienes un club asignado.');
        }

        $proveedores = Proveedor::all();
        $clubes = Club::where('id', '!=', $club->id)->get();

        return Inertia::render('Club/Facturas/Create', [
            'club' => $club,
            'proveedores' => $proveedores,
            'clubes' => $clubes,
        ]);
    }

    /**
     * Store a newly created factura in storage.
     */
    public function store(StoreFacturaRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $club = Club::where('gestor_id', $user->id)->first();

        if (!$club) {
            abort(403, 'No tienes un club asignado.');
        }

        DB::beginTransaction();
        try {
            // Determinar el receptor
            $receptorType = $request->receptor_type === 'proveedor' ? Proveedor::class : Club::class;
            $receptorId = $request->receptor_id;

            // Calcular totales
            $subtotal = 0;
            foreach ($request->conceptos as $concepto) {
                $subtotal += $concepto['total_linea'];
            }

            // Crear factura
            $factura = Factura::create([
                'numero' => null, // Se asignará al emitir
                'serie' => $request->serie ?? 'CLUB',
                'fecha_factura' => $request->fecha_factura,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'estado' => Factura::ESTADO_DRAFT,
                'subtotal' => $subtotal,
                'impuestos' => $request->impuestos ?? 0,
                'total' => $subtotal + ($request->impuestos ?? 0),
                'emisor_type' => Club::class,
                'emisor_id' => $club->id,
                'receptor_type' => $receptorType,
                'receptor_id' => $receptorId,
                'created_by' => $user->id,
            ]);

            // Crear conceptos
            foreach ($request->conceptos as $concepto) {
                FacturaConcepto::create([
                    'factura_id' => $factura->id,
                    'descripcion' => $concepto['descripcion'],
                    'cantidad' => $concepto['cantidad'] ?? 1,
                    'precio_unitario' => $concepto['precio_unitario'] ?? $concepto['total_linea'],
                    'tipo_impuesto' => $concepto['tipo_impuesto'] ?? 0,
                    'total_linea' => $concepto['total_linea'],
                    'created_by' => $user->id,
                ]);
            }

            // Guardar archivos adjuntos
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    $path = $archivo->store('facturas/' . $factura->id, 'private');
                    
                    ArchivoAdjunto::create([
                        'nombre_original' => $archivo->getClientOriginalName(),
                        'ruta' => $path,
                        'tipo' => $archivo->getMimeType(),
                        'tamano' => $archivo->getSize(),
                        'documentable_type' => Factura::class,
                        'documentable_id' => $factura->id,
                        'created_by' => $user->id,
                    ]);
                }
            }

            // Auditoría
            $this->auditLogger->created($factura);

            DB::commit();

            return redirect()->route('club.facturas.show', $factura)
                ->with('success', 'Factura creada correctamente en modo borrador.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified factura.
     */
    public function show(Factura $factura): Response
    {
        $this->authorize('view', $factura);

        $factura->load(['emisor', 'receptor', 'conceptos', 'pagos.validatedBy', 'archivoAdjuntos', 'modificaciones.createdBy']);

        return Inertia::render('Club/Facturas/Show', [
            'factura' => $factura,
        ]);
    }

    /**
     * Show the form for editing the specified factura.
     */
    public function edit(Factura $factura): Response
    {
        $this->authorize('update', $factura);

        if ($factura->estado !== Factura::ESTADO_DRAFT) {
            return redirect()->route('club.facturas.show', $factura)
                ->with('error', 'Solo se pueden editar facturas en estado borrador.');
        }

        $user = Auth::user();
        $club = Club::where('gestor_id', $user->id)->first();
        $proveedores = Proveedor::all();
        $clubes = Club::where('id', '!=', $club->id)->get();

        $factura->load(['conceptos', 'archivoAdjuntos']);

        return Inertia::render('Club/Facturas/Edit', [
            'factura' => $factura,
            'club' => $club,
            'proveedores' => $proveedores,
            'clubes' => $clubes,
        ]);
    }

    /**
     * Update the specified factura in storage.
     */
    public function update(UpdateFacturaRequest $request, Factura $factura): RedirectResponse
    {
        $this->authorize('update', $factura);

        if ($factura->estado !== Factura::ESTADO_DRAFT) {
            return back()->with('error', 'Solo se pueden editar facturas en estado borrador.');
        }

        DB::beginTransaction();
        try {
            $oldData = $factura->toArray();

            // Calcular totales
            $subtotal = 0;
            foreach ($request->conceptos as $concepto) {
                $subtotal += $concepto['total_linea'];
            }

            // Actualizar factura
            $factura->update([
                'fecha_factura' => $request->fecha_factura,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'subtotal' => $subtotal,
                'impuestos' => $request->impuestos ?? 0,
                'total' => $subtotal + ($request->impuestos ?? 0),
            ]);

            // Eliminar conceptos antiguos y crear nuevos
            $factura->conceptos()->delete();
            foreach ($request->conceptos as $concepto) {
                FacturaConcepto::create([
                    'factura_id' => $factura->id,
                    'descripcion' => $concepto['descripcion'],
                    'cantidad' => $concepto['cantidad'] ?? 1,
                    'precio_unitario' => $concepto['precio_unitario'] ?? $concepto['total_linea'],
                    'tipo_impuesto' => $concepto['tipo_impuesto'] ?? 0,
                    'total_linea' => $concepto['total_linea'],
                    'created_by' => Auth::id(),
                ]);
            }

            // Auditoría
            $this->auditLogger->updated($factura, $oldData, $factura->toArray());

            DB::commit();

            return redirect()->route('club.facturas.show', $factura)
                ->with('success', 'Factura actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al actualizar la factura: ' . $e->getMessage());
        }
    }

    /**
     * Emit the factura.
     */
    public function emit(EmitFacturaRequest $request, Factura $factura): RedirectResponse
    {
        $this->authorize('emit', $factura);

        DB::beginTransaction();
        try {
            // Generar número de factura
            $numero = $this->invoiceNumberService->nextNumberForEmisor(
                $factura->emisor_type,
                $factura->emisor_id,
                $factura->serie
            );

            $factura->update([
                'numero' => $numero,
                'estado' => Factura::ESTADO_PENDING_PAYMENT,
            ]);

            // Auditoría
            $this->auditLogger->emitted($factura);

            // Notificar al receptor
            $receptor = $factura->receptor;
            if ($receptor && method_exists($receptor, 'notify')) {
                $receptor->notify(new FacturaEmitidaNotification($factura));
            }

            DB::commit();

            return redirect()->route('club.facturas.show', $factura)
                ->with('success', 'Factura emitida correctamente con número: ' . $numero);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al emitir la factura: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified factura from storage.
     */
    public function destroy(Factura $factura): RedirectResponse
    {
        $this->authorize('delete', $factura);

        if ($factura->estado !== Factura::ESTADO_DRAFT) {
            return back()->with('error', 'Solo se pueden eliminar facturas en estado borrador.');
        }

        DB::beginTransaction();
        try {
            // Eliminar archivos físicos
            foreach ($factura->archivoAdjuntos as $archivo) {
                Storage::disk('private')->delete($archivo->ruta);
            }

            // Auditoría
            $this->auditLogger->deleted($factura);

            $factura->delete();

            DB::commit();

            return redirect()->route('club.facturas.index')
                ->with('success', 'Factura eliminada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar la factura: ' . $e->getMessage());
        }
    }
}
