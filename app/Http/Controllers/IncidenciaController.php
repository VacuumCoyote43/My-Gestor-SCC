<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidenciaRequest;
use App\Http\Requests\StoreMensajeIncidenciaRequest;
use App\Models\Incidencia;
use App\Models\MensajeIncidencia;
use App\Models\ArchivoAdjunto;
use App\Models\User;
use App\Services\AuditLoggerService;
use App\Notifications\IncidenciaCreadaNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class IncidenciaController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected AuditLoggerService $auditLogger
    ) {}

    /**
     * Display a listing of incidencias.
     */
    public function index(Request $request): Response
    {
        $query = Incidencia::with(['creador', 'mensajes']);

        // Admin puede ver todas, otros solo las suyas
        if (!Auth::user()->isAdmin()) {
            $query->where('user_id_creador', Auth::id());
        }

        // Búsqueda por asunto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('asunto', 'like', "%{$search}%");
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por prioridad
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        $incidencias = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Incidencias/Index', [
            'incidencias' => $incidencias,
            'filters' => $request->only(['search', 'estado', 'prioridad', 'categoria']),
        ]);
    }

    /**
     * Show the form for creating a new incidencia.
     */
    public function create(): Response
    {
        return Inertia::render('Incidencias/Create');
    }

    /**
     * Store a newly created incidencia in storage.
     */
    public function store(StoreIncidenciaRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // Crear incidencia
            $incidencia = Incidencia::create([
                'user_id_creador' => Auth::id(),
                'asunto' => $request->asunto,
                'categoria' => $request->categoria,
                'prioridad' => $request->prioridad,
                'estado' => Incidencia::ESTADO_ABIERTA,
                'fecha_apertura' => now(),
                'created_by' => Auth::id(),
            ]);

            // Log de auditoría
            $this->auditLogger->logCreated($incidencia, 'Incidencia creada');

            // Notificar a todos los admins
            $admins = User::whereHas('role', function($query) {
                $query->where('nombre', 'admin');
            })->get();

            foreach ($admins as $admin) {
                $admin->notify(new IncidenciaCreadaNotification($incidencia));
            }

            DB::commit();

            return redirect()->route('incidencias.show', $incidencia)
                ->with('success', 'Incidencia creada correctamente. Se ha notificado a los administradores.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la incidencia: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified incidencia.
     */
    public function show(Incidencia $incidencia): Response
    {
        $this->authorize('view', $incidencia);

        $incidencia->load([
            'creador',
            'mensajes' => function($query) {
                // Filtrar mensajes internos si no es admin
                if (!Auth::user()->isAdmin()) {
                    $query->where('tipo', MensajeIncidencia::TIPO_PUBLICO);
                }
                $query->with(['user', 'archivoAdjuntos']);
            },
        ]);

        return Inertia::render('Incidencias/Show', [
            'incidencia' => $incidencia,
        ]);
    }

    /**
     * Store a new mensaje in the incidencia.
     */
    public function storeMensaje(StoreMensajeIncidenciaRequest $request, Incidencia $incidencia): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // Crear mensaje
            $mensaje = MensajeIncidencia::create([
                'incidencia_id' => $incidencia->id,
                'user_id' => Auth::id(),
                'mensaje' => $request->mensaje,
                'tipo' => $request->tipo,
                'fecha_mensaje' => now(),
                'created_by' => Auth::id(),
            ]);

            // Guardar archivos adjuntos
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    $path = $archivo->store('incidencias/' . $incidencia->id, 'private');
                    
                    ArchivoAdjunto::create([
                        'nombre_original' => $archivo->getClientOriginalName(),
                        'ruta' => $path,
                        'tipo' => $archivo->getMimeType(),
                        'tamano' => $archivo->getSize(),
                        'documentable_type' => 'App\\Models\\MensajeIncidencia',
                        'documentable_id' => $mensaje->id,
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Mensaje enviado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al enviar el mensaje: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the estado of the incidencia (admin only).
     */
    public function updateEstado(Request $request, Incidencia $incidencia): RedirectResponse
    {
        $this->authorize('update', $incidencia);

        $request->validate([
            'estado' => 'required|in:abierta,en_progreso,cerrada',
        ]);

        $estadoAnterior = $incidencia->estado;

        $incidencia->update([
            'estado' => $request->estado,
            'fecha_cierre' => $request->estado === Incidencia::ESTADO_CERRADA ? now() : null,
        ]);

        // Log de auditoría
        $this->auditLogger->logStatusChanged($incidencia, $estadoAnterior, $request->estado);

        return back()->with('success', 'Estado de la incidencia actualizado correctamente.');
    }
}
