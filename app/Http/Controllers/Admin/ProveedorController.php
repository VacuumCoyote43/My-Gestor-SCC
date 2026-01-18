<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProveedorController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of proveedores.
     */
    public function index(Request $request): Response
    {
        $query = Proveedor::with('createdBy');

        if ($request->filled('search')) {
            $query->where('nombre_legal', 'like', '%' . $request->search . '%')
                  ->orWhere('nif_cif', 'like', '%' . $request->search . '%');
        }

        $proveedores = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Admin/Proveedores/Index', [
            'proveedores' => $proveedores,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new proveedor.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Proveedores/Create');
    }

    /**
     * Store a newly created proveedor in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_legal' => 'required|string|max:255',
            'nif_cif' => 'required|string|max:20|unique:proveedores,nif_cif',
            'email' => 'required|email|max:255',
            'direccion' => 'nullable|string|max:500',
            'es_liga' => 'boolean',
        ]);

        Proveedor::create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    /**
     * Display the specified proveedor.
     */
    public function show(Proveedor $proveedor): Response
    {
        $proveedor->load(['createdBy', 'facturasEmitidas']);

        return Inertia::render('Admin/Proveedores/Show', [
            'proveedor' => $proveedor,
        ]);
    }

    /**
     * Show the form for editing the specified proveedor.
     */
    public function edit(Proveedor $proveedor): Response
    {
        return Inertia::render('Admin/Proveedores/Edit', [
            'proveedor' => $proveedor,
        ]);
    }

    /**
     * Update the specified proveedor in storage.
     */
    public function update(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_legal' => 'required|string|max:255',
            'nif_cif' => 'required|string|max:20|unique:proveedores,nif_cif,' . $proveedor->id,
            'email' => 'required|email|max:255',
            'direccion' => 'nullable|string|max:500',
            'es_liga' => 'boolean',
        ]);

        $proveedor->update($validated);

        return redirect()->route('admin.proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Remove the specified proveedor from storage.
     */
    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        $proveedor->delete();

        return redirect()->route('admin.proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
