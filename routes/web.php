<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

// Dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard/customer', function () {
        return Inertia::render('CustomerDashboard');
    })->middleware('role:customer')->name('customer.dashboard');

    Route::get('/dashboard/provider', function () {
        return Inertia::render('ProviderDashboard');
    })->middleware('role:provider')->name('provider.dashboard');

    Route::get('/dashboard/admin', function () {
        return Inertia::render('AdminDashboard');
    })->middleware('role:admin')->name('admin.dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
