<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\StandardUserController;
use App\Http\Controllers\ViewerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Rutas públicas (sin autenticación)
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

// Rutas autenticadas
Route::middleware('auth')->group(function () {
    // Rutas de perfil (disponibles para todos los roles autenticados)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Redirección al dashboard correspondiente
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Asegurar que el rol está cargado
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        $roleSlug = $user->role?->slug ?? 'standard';

        \Log::debug('Dashboard redirect check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role_slug' => $roleSlug,
        ]);

        // Redirigir según el rol
        return match ($roleSlug) {
            'admin' => redirect('/admin/dashboard'),
            'viewer' => redirect('/viewer/dashboard'),
            default => redirect('/standard/dashboard'),
        };
    })->name('dashboard');
});

// Rutas para Administrador
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/section1', function () {
        return Inertia::render('Admin/Section1');
    })->name('section1');
    Route::get('/section2', function () {
        return Inertia::render('Admin/Section2');
    })->name('section2');
});

// Rutas API para gestión de usuarios (solo admin)
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('api')->name('api.')->group(function () {
    Route::apiResource('users', AdminUserController::class);
    Route::get('/roles', function () {
        return response()->json(\App\Models\Role::all());
    })->name('roles.index');
});

// Rutas para Usuario Estándar
Route::middleware(['auth', 'verified', 'role:standard'])->prefix('standard')->name('standard.')->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', [StandardUserController::class, 'dashboard'])->name('dashboard');
    Route::get('/section1', function () {
        return Inertia::render('Standard/Section1');
    })->name('section1');
    Route::get('/section2', function () {
        return Inertia::render('Standard/Section2');
    })->name('section2');

    // API para gestión de cursantes/talleres/inscripciones
    Route::get('/cursantes', [\App\Http\Controllers\Standard\CursanteController::class, 'index'])->name('cursantes.index');
    Route::post('/cursantes', [\App\Http\Controllers\Standard\CursanteController::class, 'store'])->name('cursantes.store');
    Route::put('/cursantes/{cursante}', [\App\Http\Controllers\Standard\CursanteController::class, 'update'])->name('cursantes.update');
    Route::delete('/cursantes/{cursante}', [\App\Http\Controllers\Standard\CursanteController::class, 'destroy'])->name('cursantes.destroy');
    Route::get('/cursantes/buscar/{dni}', [\App\Http\Controllers\Standard\CursanteController::class, 'buscarPorDni'])->name('cursantes.buscar');
    Route::get('/talleres/hoy', [\App\Http\Controllers\Standard\TallerController::class, 'disponibles'])->name('talleres.hoy');
    Route::get('/inscripciones/hoy', [\App\Http\Controllers\Standard\InscripcionController::class, 'indexHoy'])->name('inscripciones.hoy');
    Route::post('/inscripciones', [\App\Http\Controllers\Standard\InscripcionController::class, 'store'])->name('inscripciones.store');
});

// Rutas cortas para Usuario Estándar (sin prefijo)
Route::middleware(['auth', 'verified', 'role:standard'])->group(function () {
    Route::redirect('/dashboard', '/standard/dashboard');
    Route::get('/section1', function () {
        return Inertia::render('Standard/Section1');
    });
    Route::get('/section2', function () {
        return Inertia::render('Standard/Section2');
    });
});

// Rutas para Usuario Visor
Route::middleware(['auth', 'verified', 'role:viewer'])->prefix('viewer')->name('viewer.')->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', [ViewerController::class, 'dashboard'])->name('dashboard');
    Route::get('/view1', function () {
        return Inertia::render('Viewer/View1');
    })->name('view1');
    Route::get('/view2', function () {
        return Inertia::render('Viewer/View2');
    })->name('view2');
});

require __DIR__.'/auth.php';
