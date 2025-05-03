<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('is_featured', true)
            ->orderBy('name')
            ->take(6)
            ->get();
        
        $services = Service::with(['provider', 'category'])
            ->where('is_available', true)
            ->latest()
            ->paginate(12);

        return view('services.index', compact('categories', 'services'));
    }

    public function publicIndex(Request $request)
    {
        $query = Service::where('is_available', true)
            ->with(['provider', 'category']);

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $services = $query->paginate(12);

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('services.index', compact('services', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('services.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'cover_image' => 'nullable|image|max:2048'
        ]);

        $service = auth()->user()->provider->services()->create($validated);

        if ($request->hasFile('cover_image')) {
            $service->cover_image = $request->file('cover_image')->store('services', 'public');
            $service->save();
        }

        return redirect()->route('services.show', $service)
            ->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        $service->load(['provider', 'category', 'reviews.user']);
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $this->authorize('update', $service);
        
        $categories = Category::orderBy('name')->get();
        return view('services.edit', compact('service', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'is_available' => 'boolean',
            'cover_image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('services', 'public');
        }

        $service->update($validated);

        return redirect()->route('services.show', $service)
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        
        $service->delete();
        
        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = Service::where('is_available', true)
            ->with(['provider', 'category']);

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('location_type')) {
            $query->where('location_type', $request->location_type);
        }

        $services = $query->paginate(12);

        return view('services.search', compact('services'));
    }
}
