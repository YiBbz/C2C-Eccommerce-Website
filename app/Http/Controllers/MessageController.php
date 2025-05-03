<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Booking $booking)
    {
        $this->authorize('view', $booking);

        $messages = $booking->messages()
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(20);

        return view('messages.index', compact('booking', 'messages'));
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
    public function store(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = new Message();
        $message->booking_id = $booking->id;
        $message->sender_id = Auth::id();
        $message->receiver_id = Auth::id() == $booking->customer_id ? $booking->provider_id : $booking->customer_id;
        $message->message = $validated['message'];
        $message->save();

        // Broadcast the message
        Broadcast::channel('booking.' . $booking->id, function ($user) use ($booking) {
            return $user->id == $booking->customer_id || $user->id == $booking->provider_id;
        });

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load(['sender', 'receiver']),
            ]);
        }

        return redirect()->back()->with('success', 'Message sent successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function messages(Booking $booking)
    {
        $this->authorize('view', $booking);

        $messages = $booking->messages()
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(20);

        return response()->json($messages);
    }
}
