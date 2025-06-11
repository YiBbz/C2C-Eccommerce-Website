<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Load user's bookings with service and messages relationships
        // Assuming you have relationships defined in your User, Booking, Service, and Message models
        $user->load(['bookings.service', 'bookings.messages']);

        return Inertia::render('CustomerDashboard', [
            'customerData' => $user->only(['bookings']),
        ]);
    }
}
