<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CanalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\PaperCoilController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Usuarios - Solo Gestor
    Route::middleware(['role:gestor'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Canales - Solo Gestor
    Route::middleware(['role:gestor'])->group(function () {
        Route::resource('canales', CanalController::class)->except(['show']);
    });

    // Clientes - Gestor y Vendedor
    Route::middleware(['role:gestor,vendedor'])->group(function () {
        Route::resource('clients', ClientController::class);
    });

    // Productos - Todos los roles pueden ver, Gestor puede editar
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Rutas de gestor ANTES de la ruta dinámica
    Route::middleware(['role:gestor'])->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Ruta show después (para que no capture /products/create)
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Transportes - Solo Gestor
    Route::middleware(['role:gestor'])->group(function () {
        Route::resource('transports', TransportController::class)->except(['show']);
    });

    // Bobinas de papel - Gestor y Operario
    Route::middleware(['role:gestor,operario'])->group(function () {
        Route::resource('paper-coils', PaperCoilController::class);
    });

    // Órdenes de Producción - Gestor y Operario
    Route::middleware(['role:gestor,operario'])->group(function () {
        Route::resource('production-orders', ProductionOrderController::class);
        Route::post('/production-orders/{productionOrder}/start', [ProductionOrderController::class, 'start'])->name('production-orders.start');
        Route::post('/production-orders/{productionOrder}/pause', [ProductionOrderController::class, 'pause'])->name('production-orders.pause');
        Route::post('/production-orders/{productionOrder}/finish', [ProductionOrderController::class, 'finish'])->name('production-orders.finish');
    });

    // Ventas - Gestor y Vendedor
    Route::middleware(['role:gestor,vendedor'])->group(function () {
        Route::resource('sales', SaleController::class);
    });
});

require __DIR__.'/auth.php';
