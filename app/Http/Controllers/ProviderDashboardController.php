<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProviderDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Load services through the serviceProvider relationship
        // Assuming you have relationships defined in your User, ServiceProvider, Service, Booking, and Message models
        $user->load(['serviceProvider.services.bookings.messages', 'serviceProvider.services.messages']);

        // Access services via the serviceProvider relationship
        $services = $user->serviceProvider ? $user->serviceProvider->services : collect();

        return Inertia::render('ProviderDashboard', [
            'providerData' => [
                'services' => $services,
            ],
        ]);
    }
}
