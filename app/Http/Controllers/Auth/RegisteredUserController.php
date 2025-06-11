<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ServiceProvider;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:customer,provider',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Create provider profile if user is a provider
        if ($user->role === 'provider') {
            \Log::info('Attempting to create ServiceProvider profile for user: ' . $user->id);
            try {
                $serviceProvider = ServiceProvider::create([
                    'user_id' => $user->id,
                    'business_name' => $user->name . "'s Business",
                    'description' => 'Professional service provider.',
                    'phone' => '',
                    'address' => '',
                    'business_license' => '',
                    'tax_id' => '',
                    'is_verified' => false
                ]);
                 
                // Reload the user to get the fresh relationship data
                $user->load('serviceProvider');
                \Log::info('ServiceProvider profile created.', ['service_provider_id' => $serviceProvider->id, 'user_service_provider_relation' => $user->serviceProvider ? $user->serviceProvider->id : null]);

            } catch (\Exception $e) {
                \Log::error('Failed to create ServiceProvider profile for user: ' . $user->id, ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        if ($user->role === 'customer') {
            return redirect()->route('customer.dashboard');
        } elseif ($user->role === 'provider') {
            return redirect()->route('provider.dashboard');
        } elseif ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('welcome');
    }
}
