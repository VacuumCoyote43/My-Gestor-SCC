<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\Club;
use App\Models\Proveedor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FacturaRecibidaController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of facturas recibidas por el club.
     */
    public function index(Request $request): Response
    {
        // Obtener el club del usuario para mostrar en la vista
        $user = auth()->user();
        $club = Club::where('gestor_id', $user->id)->first();

        if (!$club) {
            abort(403, 'No tienes un club asignado.');
        }

        // Filtrar solo facturas recibidas por el club del gestor
        $query = Factura::where('receptor_type', Club::class)
            ->where('receptor_id', $club->id)
            ->with(['emisor', 'conceptos', 'pagos']);

        // Búsqueda por número de factura o emisor
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHasMorph('emisor', [Club::class, Proveedor::class], function($q) use ($search) {
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

        return Inertia::render('Club/FacturasRecibidas/Index', [
            'facturas' => $facturas,
            'filters' => $request->only(['search', 'estado', 'mes', 'anio']),
            'club' => $club,
        ]);
    }

    /**
     * Display the specified factura.
     */
    public function show(Factura $factura): Response
    {
        // Verificar que el gestor puede ver esta factura
        $this->authorize('view', $factura);

        $factura->load([
            'emisor',
            'receptor',
            'conceptos',
            'pagos.archivoAdjuntos',
            'pagos.validatedBy',
            'archivoAdjuntos',
            'modificaciones.createdBy'
        ]);

        return Inertia::render('Club/FacturasRecibidas/Show', [
            'factura' => $factura,
        ]);
    }
}
