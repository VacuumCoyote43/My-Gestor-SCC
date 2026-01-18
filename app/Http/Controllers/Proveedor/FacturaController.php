<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFacturaRequest;
use App\Http\Requests\UpdateFacturaRequest;
use App\Http\Requests\EmitFacturaRequest;
use App\Models\Factura;
use App\Models\FacturaConcepto;
use App\Models\Proveedor;
use App\Models\Club;
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
        // TODO: Obtener proveedor del usuario actual
        $proveedor = Proveedor::first();

        $query = Factura::where('emisor_type', 'App\\Models\\Proveedor')
            ->where('emisor_id', $proveedor->id)
            ->with(['receptor', 'conceptos', 'pagos']);

        // Búsqueda por número de factura o receptor
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHasMorph('receptor', [Club::class, Proveedor::class], function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%")
                        ->orWhere('nombre_legal', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por mes
        if ($request->filled('mes')) {
            $query->whereMonth('fecha_factura', $request->mes);
        }

        // Filtro por año
        if ($request->filled('anio')) {
            $query->whereYear('fecha_factura', $request->anio);
        }

        $facturas = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Proveedor/Facturas/Index', [
            'facturas' => $facturas,
            'filters' => $request->only(['search', 'estado', 'mes', 'anio']),
        ]);
    }

    /**
     * Show the form for creating a new factura.
     */
    public function create(): Response
    {
        $clubes = Club::all();
        $proveedores = Proveedor::all();

        return Inertia::render('Proveedor/Facturas/Create', [
            'clubes' => $clubes,
            'proveedores' => $proveedores,
        ]);
    }

    /**
     * Store a newly created factura in storage.
     */
    public function store(StoreFacturaRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // TODO: Obtener proveedor del usuario actual
            $proveedor = Proveedor::first();

            // Generar número de factura
            $numero = $this->invoiceNumberService->nextNumberForProveedor($proveedor->id, $request->serie);

            // Calcular totales
            $subtotal = 0;
            $impuestos = 0;
            foreach ($request->conceptos as $concepto) {
                $subtotal += $concepto['total_linea'];
                // TODO: Calcular impuestos si es necesario
            }
            $total = $subtotal + $impuestos;

            // Crear factura
            $factura = Factura::create([
                'numero' => $numero,
                'serie' => $request->serie,
                'fecha_factura' => $request->fecha_factura,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'estado' => Factura::ESTADO_DRAFT,
                'subtotal' => $subtotal,
                'impuestos' => $impuestos,
                'total' => $total,
                'emisor_type' => 'App\\Models\\Proveedor',
                'emisor_id' => $proveedor->id,
                'receptor_type' => $request->receptor_type,
                'receptor_id' => $request->receptor_id,
                'created_by' => Auth::id(),
            ]);

            // Crear conceptos
            foreach ($request->conceptos as $concepto) {
                FacturaConcepto::create([
                    'factura_id' => $factura->id,
                    'descripcion' => $concepto['descripcion'],
                    'cantidad' => $concepto['cantidad'] ?? 1,
                    'precio_unitario' => $concepto['precio_unitario'] ?? 0,
                    'tipo_impuesto' => $concepto['tipo_impuesto'] ?? 0,
                    'total_linea' => $concepto['total_linea'],
                    'created_by' => Auth::id(),
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
                        'documentable_type' => 'App\\Models\\Factura',
                        'documentable_id' => $factura->id,
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            // Log de auditoría
            $this->auditLogger->logCreated($factura, 'Factura creada en estado draft');

            DB::commit();

            return redirect()->route('proveedor.facturas.show', $factura)
                ->with('success', 'Factura creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la factura: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified factura.
     */
    public function show(Factura $factura): Response
    {
        $this->authorize('view', $factura);

        $factura->load(['receptor', 'conceptos', 'pagos.archivoAdjuntos', 'pagos.validatedBy', 'archivoAdjuntos', 'modificaciones.createdBy']);

        return Inertia::render('Proveedor/Facturas/Show', [
            'factura' => $factura,
        ]);
    }

    /**
     * Show the form for editing the specified factura.
     */
    public function edit(Factura $factura): Response
    {
        $this->authorize('update', $factura);

        $factura->load('conceptos');
        $clubes = Club::all();
        $proveedores = Proveedor::all();

        return Inertia::render('Proveedor/Facturas/Edit', [
            'factura' => $factura,
            'clubes' => $clubes,
            'proveedores' => $proveedores,
        ]);
    }

    /**
     * Update the specified factura in storage.
     */
    public function update(UpdateFacturaRequest $request, Factura $factura): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $original = $factura->toArray();

            // Actualizar factura
            $factura->update($request->only([
                'serie',
                'fecha_factura',
                'fecha_vencimiento',
                'receptor_type',
                'receptor_id',
            ]));

            // Actualizar conceptos si se enviaron
            if ($request->has('conceptos')) {
                // Eliminar conceptos existentes
                $factura->conceptos()->delete();

                // Crear nuevos conceptos
                $subtotal = 0;
                foreach ($request->conceptos as $concepto) {
                    FacturaConcepto::create([
                        'factura_id' => $factura->id,
                        'descripcion' => $concepto['descripcion'],
                        'cantidad' => $concepto['cantidad'] ?? 1,
                        'precio_unitario' => $concepto['precio_unitario'] ?? 0,
                        'tipo_impuesto' => $concepto['tipo_impuesto'] ?? 0,
                        'total_linea' => $concepto['total_linea'],
                        'created_by' => Auth::id(),
                    ]);
                    $subtotal += $concepto['total_linea'];
                }

                // Actualizar total
                $factura->update([
                    'subtotal' => $subtotal,
                    'total' => $subtotal, // TODO: Agregar impuestos si es necesario
                ]);
            }

            // Log de auditoría
            $this->auditLogger->logUpdated($factura, $original, 'Factura actualizada');

            DB::commit();

            return redirect()->route('proveedor.facturas.show', $factura)
                ->with('success', 'Factura actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la factura: ' . $e->getMessage()]);
        }
    }

    /**
     * Emit the factura (change status to pending_payment).
     */
    public function emit(EmitFacturaRequest $request, Factura $factura): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $factura->update(['estado' => Factura::ESTADO_PENDING_PAYMENT]);

            // Log de auditoría
            $this->auditLogger->logEmitted($factura, 'Factura emitida');

            // Notificar al receptor
            $receptor = $factura->receptor;
            if ($receptor && method_exists($receptor, 'notify')) {
                // Si el receptor es un modelo que puede recibir notificaciones
                // TODO: Implementar lógica para obtener email del receptor
            }

            DB::commit();

            return redirect()->route('proveedor.facturas.show', $factura)
                ->with('success', 'Factura emitida correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al emitir la factura: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified factura from storage.
     */
    public function destroy(Factura $factura): RedirectResponse
    {
        $this->authorize('delete', $factura);

        DB::beginTransaction();
        try {
            // Log de auditoría antes de eliminar
            $this->auditLogger->logDeleted($factura, 'Factura eliminada');

            $factura->delete();

            DB::commit();

            return redirect()->route('proveedor.facturas.index')
                ->with('success', 'Factura eliminada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar la factura: ' . $e->getMessage()]);
        }
    }
}
