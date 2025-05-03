<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Auth::user()->reviews()
            ->with(['service', 'provider'])
            ->latest()
            ->paginate(10);

        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Booking $booking)
    {
        $this->authorize('create', [Review::class, $booking]);

        return view('reviews.create', compact('booking'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Booking $booking)
    {
        $this->authorize('create', [Review::class, $booking]);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review = new Review();
        $review->booking_id = $booking->id;
        $review->service_id = $booking->service_id;
        $review->customer_id = Auth::id();
        $review->provider_id = $booking->provider_id;
        $review->rating = $validated['rating'];
        $review->comment = $validated['comment'];
        $review->is_visible = true;
        $review->save();

        // Update service rating
        $service = Service::findOrFail($booking->service_id);
        $service->updateRating();

        // Update provider rating
        $provider = $booking->provider;
        $provider->updateRating();

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Review submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        $this->authorize('view', $review);

        $review->load(['service', 'customer', 'provider']);

        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review->update($validated);

        // Update service rating
        $service = $review->service;
        $service->updateRating();

        // Update provider rating
        $provider = $review->provider;
        $provider->updateRating();

        return redirect()->route('reviews.show', $review)
            ->with('success', 'Review updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $service = $review->service;
        $provider = $review->provider;

        $review->delete();

        // Update service rating
        $service->updateRating();

        // Update provider rating
        $provider->updateRating();

        return redirect()->route('reviews.index')
            ->with('success', 'Review deleted successfully.');
    }

    public function providerReviews()
    {
        $reviews = Auth::user()->serviceProvider->reviews()
            ->with(['service', 'customer'])
            ->latest()
            ->paginate(10);

        return view('reviews.provider-index', compact('reviews'));
    }

    public function adminIndex()
    {
        $reviews = Review::with(['service', 'customer', 'provider'])
            ->latest()
            ->paginate(15);

        return view('reviews.admin-index', compact('reviews'));
    }

    public function toggleVisibility(Review $review)
    {
        $this->authorize('update', $review);

        $review->update(['is_visible' => !$review->is_visible]);

        return redirect()->back()
            ->with('success', 'Review visibility updated successfully.');
    }
}
