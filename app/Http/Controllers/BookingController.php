<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Auth::user()->bookings()
            ->with(['service', 'service.provider'])
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // For initial booking, only service_id is required
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $booking = new Booking();
        $booking->service_id = $service->id;
        $booking->customer_id = Auth::id();
        // Assuming service model has a provider_id or a relationship to get the provider_id
        // If your Service model has a direct provider_id foreign key:
        $booking->provider_id = $service->provider_id;
        // If your Service model has a relationship to Provider, and Provider has a user_id:
        // $booking->provider_id = $service->provider->user_id;
        
        // Set initial status to pending
        $booking->status = 'pending';
        
        // You can set other fields to null or default values to be negotiated later in chat
        // $booking->booking_date = null;
        // $booking->total_amount = null;
        // $booking->location = null;
        // $booking->special_instructions = null;

        $booking->save();

        // Redirect to the booking show page for negotiation/chat
        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking initiated. Please discuss details with the provider via chat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        // Load relationships including messages for the chat
        $booking->load(['service.provider.user', 'customer.user', 'messages.sender']);

        return Inertia::render('BookingShow', [
            'booking' => $booking,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'booking_date' => 'required_if:status,confirmed|date|after:now',
            'location' => 'required_if:status,confirmed|string|max:255',
        ]);

        $booking->update($validated);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function providerBookings()
    {
        $bookings = Auth::user()->serviceProvider->bookings()
            ->with(['service', 'customer'])
            ->latest()
            ->paginate(10);

        return view('bookings.provider-index', compact('bookings'));
    }

    public function adminIndex()
    {
        $bookings = Booking::with(['service', 'customer', 'provider'])
            ->latest()
            ->paginate(15);

        return view('bookings.admin-index', compact('bookings'));
    }
}
