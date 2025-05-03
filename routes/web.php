<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Service Provider Routes
    Route::middleware(['role:provider'])->group(function () {
        Route::resource('provider', ServiceProviderController::class);
        Route::resource('services', ServiceController::class);
        Route::get('provider/bookings', [BookingController::class, 'providerBookings'])->name('provider.bookings');
        Route::get('provider/reviews', [ReviewController::class, 'providerReviews'])->name('provider.reviews');
    });

    // Customer Routes
    Route::middleware(['role:customer'])->group(function () {
        Route::resource('bookings', BookingController::class);
        Route::resource('reviews', ReviewController::class);
        Route::get('services/search', [ServiceController::class, 'search'])->name('services.search');
    });

    // Chat Routes
    Route::prefix('chat')->group(function () {
        Route::get('/{booking}', [MessageController::class, 'index'])->name('chat.index');
        Route::post('/{booking}/send', [MessageController::class, 'store'])->name('chat.send');
        Route::get('/{booking}/messages', [MessageController::class, 'messages'])->name('chat.messages');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('categories', ServiceCategoryController::class);
        Route::get('admin/providers', [ServiceProviderController::class, 'adminIndex'])->name('admin.providers');
        Route::put('admin/providers/{provider}/verify', [ServiceProviderController::class, 'verify'])->name('admin.verify-provider');
        Route::get('admin/bookings', [BookingController::class, 'adminIndex'])->name('admin.bookings');
        Route::get('admin/reviews', [ReviewController::class, 'adminIndex'])->name('admin.reviews');
    });

    // Service Provider Routes
    Route::resource('providers', ProviderController::class);
    
    // Service Routes
    Route::resource('services', ServiceController::class);
    
    // Category Routes
    Route::resource('categories', CategoryController::class);
    
    // Public Category Routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
});

// Public Routes
Route::get('/', [ServiceController::class, 'index'])->name('home');
Route::get('/services', [ServiceController::class, 'publicIndex'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/categories', [ServiceCategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [ServiceCategoryController::class, 'show'])->name('categories.show');

// Service Provider Routes
Route::get('/providers', [ProviderController::class, 'index'])->name('providers.index');
Route::get('/providers/create', [ProviderController::class, 'create'])->name('providers.create');
Route::post('/providers', [ProviderController::class, 'store'])->name('providers.store');
Route::get('/providers/{provider}', [ProviderController::class, 'show'])->name('providers.show');
Route::get('/providers/{provider}/edit', [ProviderController::class, 'edit'])->name('providers.edit');
Route::put('/providers/{provider}', [ProviderController::class, 'update'])->name('providers.update');

// Category Routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

require __DIR__.'/auth.php';
