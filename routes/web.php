<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\ProviderDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

// Services page
Route::get('/services', [ServiceController::class, 'listServices'])
    ->name('services');

// About page
Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

// Contact page
Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

// Contact form submission
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Booking routes
Route::post('/bookings', [BookingController::class, 'store'])
    ->middleware('auth') // Only authenticated users can create bookings
    ->name('bookings.store');

// Service creation route for providers
Route::get('/services/create', [ServiceController::class, 'create'])
    ->middleware(['auth', 'role:provider'])
    ->name('services.create');

// Service store route for providers
Route::post('/services', [ServiceController::class, 'store'])
    ->middleware(['auth', 'role:provider'])
    ->name('services.store');

// Dashboard routes
Route::middleware(['auth'])->group(function () {
    // Main dashboard route that redirects based on role
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        // Redirect based on role
        $redirectRoute = match($user->role) {
            'customer' => 'customer.dashboard',
            'provider' => 'provider.dashboard',
            'admin' => 'admin.dashboard',
            default => 'welcome'
        };
        
        return redirect()->route($redirectRoute);
    })->name('dashboard');
    
    // Role-specific dashboard routes
    Route::get('/dashboard/customer', [CustomerDashboardController::class, 'index'])
        ->middleware('role:customer')
        ->name('customer.dashboard');

    Route::get('/dashboard/provider', [ProviderDashboardController::class, 'index'])
        ->middleware('role:provider')
        ->name('provider.dashboard');

    Route::get('/dashboard/admin', [AdminDashboardController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.dashboard');
   
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';