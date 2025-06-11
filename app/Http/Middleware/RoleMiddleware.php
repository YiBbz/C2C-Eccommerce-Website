<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Debug information
        \Log::info('RoleMiddleware', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'expected_role' => $role,
            'is_admin' => $user->isAdmin(),
            'is_provider' => $user->isProvider(),
            'is_customer' => $user->isCustomer(),
        ]);

        // Allow admin to access all routes
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check specific role
        if ($user->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized action.'], 403);
            }
            
            // Redirect to the appropriate dashboard based on user's role
            $redirectRoute = match($user->role) {
                'customer' => 'customer.dashboard',
                'provider' => 'provider.dashboard',
                'admin' => 'admin.dashboard',
                default => 'welcome'
            };
            
            return redirect()->route($redirectRoute)->with('error', 'Unauthorized action.');
        }

        return $next($request);
    }
}
