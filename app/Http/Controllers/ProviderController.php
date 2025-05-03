<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProviderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('can:create,App\Models\Provider')->only(['create', 'store']);
        $this->middleware('can:update,provider')->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        $query = Provider::with(['user', 'categories'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // Search by name
        if ($request->has('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('business_name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->having('reviews_avg_rating', '>=', $request->rating);
        }

        $providers = $query->paginate(12);

        return view('providers.index', compact('providers'));
    }

    public function show(Provider $provider)
    {
        $provider->load(['user', 'services', 'reviews.user', 'categories']);
        return view('providers.show', compact('provider'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('providers.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'description' => 'required|string',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'business_license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tax_id' => 'required|string|max:50',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'terms' => 'required|accepted',
        ]);

        // Store business license
        $licensePath = $request->file('business_license')->store('licenses');

        // Create provider
        $provider = new Provider($validated);
        $provider->user_id = Auth::id();
        $provider->business_license = $licensePath;
        $provider->save();

        // Attach categories
        $provider->categories()->attach($request->categories);

        return redirect()->route('providers.show', $provider)
            ->with('success', 'Your provider profile has been created successfully!');
    }

    public function edit(Provider $provider)
    {
        $this->authorize('update', $provider);
        $categories = Category::all();
        return view('providers.edit', compact('provider', 'categories'));
    }

    public function update(Request $request, Provider $provider)
    {
        $this->authorize('update', $provider);

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'description' => 'required|string',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'business_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tax_id' => 'required|string|max:50',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        // Update business license if provided
        if ($request->hasFile('business_license')) {
            // Delete old license
            if ($provider->business_license) {
                Storage::delete($provider->business_license);
            }
            $validated['business_license'] = $request->file('business_license')->store('licenses');
        }

        // Update profile photo if provided
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $provider->user->update(['profile_photo_path' => $path]);
        }

        // Update provider
        $provider->update($validated);

        // Sync categories
        $provider->categories()->sync($request->categories);

        return redirect()->route('providers.show', $provider)
            ->with('success', 'Your provider profile has been updated successfully!');
    }
} 