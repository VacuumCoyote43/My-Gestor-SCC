<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        $query = User::with('role');

        // Búsqueda por nombre o email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por rol
        if ($request->filled('role')) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('nombre', $request->role);
            });
        }

        // Filtro por estado activo
        if ($request->filled('active')) {
            $query->where('active', $request->active === 'true');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'active']),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        $roles = Role::all();

        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'active' => 'boolean',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'active' => $validated['active'] ?? true,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): Response
    {
        $user->load('role');

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response
    {
        $roles = Role::all();

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->load('role'),
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'active' => 'boolean',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Login as another user (Super Login)
     */
    public function loginAs(User $user): RedirectResponse
    {
        // Guardar el ID del admin actual en sesión
        Session::put('admin_user_id', Auth::id());
        Session::put('is_impersonating', true);

        // Hacer login como el usuario seleccionado
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Has iniciado sesión como ' . $user->name);
    }

    /**
     * Volver al usuario admin original
     */
    public function stopImpersonating(): RedirectResponse
    {
        if (!Session::has('admin_user_id')) {
            return redirect()->route('dashboard');
        }

        $adminId = Session::get('admin_user_id');
        $adminUser = User::find($adminId);

        Session::forget('admin_user_id');
        Session::forget('is_impersonating');

        if ($adminUser) {
            Auth::login($adminUser);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Has vuelto a tu cuenta de administrador.');
    }
}
