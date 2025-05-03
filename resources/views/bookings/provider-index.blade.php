@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Service Bookings</h1>

    <div class="row">
        @forelse($bookings as $booking)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title">{{ $booking->service->title }}</h5>
                                <p class="text-muted mb-0">Booked by: {{ $booking->customer->name }}</p>
                            </div>
                            <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'primary') }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1"><strong>Booking Date:</strong> {{ $booking->booking_date->format('F j, Y g:i A') }}</p>
                            @if($booking->location)
                                <p class="mb-1"><strong>Location:</strong> {{ $booking->location }}</p>
                            @endif
                            <p class="mb-1"><strong>Total Amount:</strong> ${{ number_format($booking->total_amount, 2) }}</p>
                        </div>

                        @if($booking->special_instructions)
                            <div class="mb-3">
                                <p class="mb-1"><strong>Special Instructions:</strong></p>
                                <p class="text-muted">{{ $booking->special_instructions }}</p>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-primary">View Details</a>
                            
                            @if($booking->status == 'pending')
                                <div class="btn-group">
                                    <form action="{{ route('bookings.update', $booking) }}" method="POST" class="me-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="btn btn-success">Confirm</button>
                                    </form>
                                    <form action="{{ route('bookings.update', $booking) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-danger">Reject</button>
                                    </form>
                                </div>
                            @elseif($booking->status == 'confirmed')
                                <form action="{{ route('bookings.update', $booking) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-success">Mark as Completed</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    You don't have any bookings yet.
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection 