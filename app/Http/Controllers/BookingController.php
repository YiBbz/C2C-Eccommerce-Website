<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after:now',
            'location' => 'required_if:service.location_type,onsite,both',
            'special_instructions' => 'nullable|string|max:1000',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $booking = new Booking();
        $booking->service_id = $service->id;
        $booking->customer_id = Auth::id();
        $booking->provider_id = $service->service_provider_id;
        $booking->booking_date = $validated['booking_date'];
        $booking->location = $validated['location'] ?? null;
        $booking->special_instructions = $validated['special_instructions'] ?? null;
        $booking->status = 'pending';
        $booking->total_amount = $service->price;
        $booking->save();

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully. Please wait for provider confirmation.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['service', 'service.provider', 'customer']);

        return view('bookings.show', compact('booking'));
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
