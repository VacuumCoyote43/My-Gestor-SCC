<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Factura;
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
        // TODO: Filtrar por clubes del gestor actual
        // Por ahora mostramos todas las facturas donde receptor es Club
        $query = Factura::where('receptor_type', 'App\\Models\\Club')
            ->with(['emisor', 'conceptos', 'pagos']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('fecha_factura', $request->mes);
        }

        if ($request->filled('anio')) {
            $query->whereYear('fecha_factura', $request->anio);
        }

        // Obtener el club del usuario para mostrar en la vista
        $user = auth()->user();
        $club = \App\Models\Club::where('gestor_id', $user->id)->first();

        if (!$club) {
            abort(403, 'No tienes un club asignado.');
        }

        // Filtrar solo facturas recibidas por el club del gestor
        $query->where('receptor_id', $club->id);

        $facturas = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Club/FacturasRecibidas/Index', [
            'facturas' => $facturas,
            'filters' => $request->only(['estado', 'mes', 'anio']),
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
