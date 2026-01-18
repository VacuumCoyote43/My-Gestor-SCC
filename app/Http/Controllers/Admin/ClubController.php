<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ClubController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of clubes.
     */
    public function index(Request $request): Response
    {
        $query = Club::with('createdBy', 'gestor');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('cif', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clubes = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Admin/Clubes/Index', [
            'clubes' => $clubes,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new club.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Clubes/Create');
    }

    /**
     * Store a newly created club in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'cif' => 'nullable|string|max:20|unique:clubes,cif',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
        ]);

        Club::create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.clubes.index')
            ->with('success', 'Club creado correctamente.');
    }

    /**
     * Display the specified club.
     */
    public function show(Club $club): Response
    {
        $club->load(['createdBy', 'cargoJugadores.jugador']);

        return Inertia::render('Admin/Clubes/Show', [
            'club' => $club,
        ]);
    }

    /**
     * Show the form for editing the specified club.
     */
    public function edit(Club $club): Response
    {
        return Inertia::render('Admin/Clubes/Edit', [
            'club' => $club,
        ]);
    }

    /**
     * Update the specified club in storage.
     */
    public function update(Request $request, Club $club): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'cif' => 'nullable|string|max:20|unique:clubes,cif,' . $club->id,
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
        ]);

        $club->update($validated);

        return redirect()->route('admin.clubes.index')
            ->with('success', 'Club actualizado correctamente.');
    }

    /**
     * Remove the specified club from storage.
     */
    public function destroy(Club $club): RedirectResponse
    {
        $club->delete();

        return redirect()->route('admin.clubes.index')
            ->with('success', 'Club eliminado correctamente.');
    }
}
