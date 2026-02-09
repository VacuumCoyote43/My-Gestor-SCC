<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\ArchivoAdjuntoController;
use App\Http\Controllers\IncidenciaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Dashboard redirect según rol
Route::get('/dashboard', DashboardRedirectController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rutas comunes autenticadas
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Archivos adjuntos
    Route::get('/archivos/{archivo}/download', [ArchivoAdjuntoController::class, 'download'])->name('archivos.download');
    Route::get('/archivos/{archivo}/view', [ArchivoAdjuntoController::class, 'view'])->name('archivos.view');

    // Incidencias (común para todos los roles)
    Route::resource('incidencias', IncidenciaController::class)->except(['edit', 'update']);
    Route::post('incidencias/{incidencia}/mensajes', [IncidenciaController::class, 'storeMensaje'])->name('incidencias.mensajes.store');
    Route::patch('incidencias/{incidencia}/estado', [IncidenciaController::class, 'updateEstado'])->name('incidencias.estado');
});

// Rutas ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Gestión de usuarios
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/login-as', [App\Http\Controllers\Admin\UserController::class, 'loginAs'])->name('users.login-as');
    
    // Gestión de clubes
    Route::resource('clubes', App\Http\Controllers\Admin\ClubController::class)
        ->parameters(['clubes' => 'club']);
    
    // Gestión de proveedores
    Route::resource('proveedores', App\Http\Controllers\Admin\ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor']);
});

// Ruta para salir del modo impersonación (disponible para cualquier usuario autenticado)
Route::post('/stop-impersonating', [App\Http\Controllers\Admin\UserController::class, 'stopImpersonating'])
    ->middleware('auth')
    ->name('stop-impersonating');

// Rutas PROVEEDOR
Route::middleware(['auth', 'role:proveedor'])->prefix('proveedor')->name('proveedor.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Proveedor\DashboardController::class, 'index'])->name('dashboard');
    
    // Facturas
    Route::resource('facturas', App\Http\Controllers\Proveedor\FacturaController::class);
    Route::post('facturas/{factura}/emit', [App\Http\Controllers\Proveedor\FacturaController::class, 'emit'])->name('facturas.emit');
    
    // Pagos (validación/rechazo)
    Route::post('pagos/{pago}/validate', [App\Http\Controllers\Proveedor\PagoController::class, 'validate'])->name('pagos.validate');
    Route::post('pagos/{pago}/reject', [App\Http\Controllers\Proveedor\PagoController::class, 'reject'])->name('pagos.reject');
});

// Rutas GESTOR CLUB
Route::middleware(['auth', 'role:gestor_club'])->prefix('club')->name('club.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Club\DashboardController::class, 'index'])->name('dashboard');
    
    // Facturas recibidas (solo lectura y registro de pagos)
    Route::get('facturas-recibidas', [App\Http\Controllers\Club\FacturaRecibidaController::class, 'index'])->name('facturas-recibidas.index');
    Route::get('facturas-recibidas/{factura}', [App\Http\Controllers\Club\FacturaRecibidaController::class, 'show'])->name('facturas-recibidas.show');
    
    // Facturas emitidas (gestión completa)
    Route::resource('facturas', App\Http\Controllers\Club\FacturaController::class);
    Route::post('facturas/{factura}/emit', [App\Http\Controllers\Club\FacturaController::class, 'emit'])->name('facturas.emit');
    
    // Pagos a proveedores
    Route::get('facturas-recibidas/{factura}/pagos/create', [App\Http\Controllers\Club\PagoController::class, 'create'])->name('facturas.pagos.create');
    Route::post('pagos', [App\Http\Controllers\Club\PagoController::class, 'store'])->name('pagos.store');
    
    // Cargos a jugadores
    Route::resource('cargos', App\Http\Controllers\Club\CargoJugadorController::class);
    Route::post('cargos/{cargo}/emit', [App\Http\Controllers\Club\CargoJugadorController::class, 'emit'])->name('cargos.emit');
    
    // Validación de pagos de jugadores
    Route::post('pagos/{pago}/validate', [App\Http\Controllers\Club\PagoController::class, 'validate'])->name('pagos.validate');
    Route::post('pagos/{pago}/reject', [App\Http\Controllers\Club\PagoController::class, 'reject'])->name('pagos.reject');
});

// Rutas JUGADOR
Route::middleware(['auth', 'role:jugador'])->prefix('jugador')->name('jugador.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Jugador\DashboardController::class, 'index'])->name('dashboard');
    
    // Cargos (solo lectura)
    Route::get('cargos', [App\Http\Controllers\Jugador\CargoController::class, 'index'])->name('cargos.index');
    Route::get('cargos/{cargo}', [App\Http\Controllers\Jugador\CargoController::class, 'show'])->name('cargos.show');
    
    // Pagos (registro)
    Route::get('cargos/{cargo}/pagos/create', [App\Http\Controllers\Jugador\PagoController::class, 'create'])->name('cargos.pagos.create');
    Route::post('pagos', [App\Http\Controllers\Jugador\PagoController::class, 'store'])->name('pagos.store');
});

require __DIR__.'/auth.php';
