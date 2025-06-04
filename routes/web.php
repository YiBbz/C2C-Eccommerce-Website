<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\RoleMiddleware;

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

// Dashboard routes

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role === 'customer') {
        return redirect()->route('customer.dashboard');
    } elseif ($user->role === 'provider') {
        return redirect()->route('provider.dashboard');
    } elseif ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    // fallback if role is not set
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard/customer', function () {
    return Inertia::render('CustomerDashboard');
    })->middleware(['auth',RoleMiddleware::class . ':customer'])->name('customer.dashboard');

    Route::get('/dashboard/provider', function () {
    return Inertia::render('ProviderDashboard');
    })->middleware(['auth',RoleMiddleware::class . ':provider'])->name('provider.dashboard');

    Route::get('/dashboard/admin', function () {
    return Inertia::render('AdminDashboard');
    })->middleware(['auth',RoleMiddleware::class . ':admin'])->name('admin.dashboard');
   
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';
