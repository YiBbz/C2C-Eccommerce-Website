<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $user = auth()->user();
        if ($user->role !== 'provider') {   
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $user->services; // assuming relationship is defined

    }

    /**
     * Display a listing of the resource for customers.
     */
    public function listServices()
    {
        $services = Service::with('provider.user')->where('is_available', true)->get();
        
        return Inertia::render('Services', [
            'services' => $services,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('ServiceCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Explicitly reload the user with the serviceProvider relationship
        $user = $request->user()->load('serviceProvider');

        \Log::info('ServiceController@store method reached.', [
            'user_id' => $user->id,
            'request_data' => $request->except('cover_image'),
            'has_file' => $request->hasFile('cover_image'),
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $provider = $user->serviceProvider;

        if (!$provider) {
            \Log::error('User is not a service provider', ['user_id' => $user->id]);
            return redirect()->back()->withErrors(['error' => 'Only service providers can add services.']);
        }

        try {
            // Create service with validated data excluding image
            $serviceData = $request->except('cover_image');
            $service = new Service($serviceData);
            $service->provider_id = $provider->id;
            $service->is_available = true; // Set default value

            // Handle file upload if cover_image is in the form
            if ($request->hasFile('cover_image')) {
                $path = $request->file('cover_image')->store('services', 'public');
                $service->cover_image = $path;
                \Log::info('Cover image uploaded successfully', ['path' => $path]);
            }

            $service->save();
            \Log::info('Service created successfully', ['service_id' => $service->id]);

            return redirect()->route('provider.dashboard')
                ->with('success', 'Service created successfully!');
        } catch (\Exception $e) {
            \Log::error('Error creating service', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create service. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }
}
