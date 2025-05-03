@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Review Details</h4>
                        @if(auth()->id() == $review->user_id)
                            <div class="btn-group">
                                <a href="{{ route('reviews.edit', $review) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this review?')">Delete</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>{{ $review->service->title }}</h5>
                        <p class="text-muted">Provider: {{ $review->service->provider->business_name }}</p>
                        <p>Booking Date: {{ $review->booking->booking_date->format('F j, Y g:i A') }}</p>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rating-display">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                                @endfor
                            </div>
                            <span class="ms-2">({{ $review->rating }}/5)</span>
                        </div>
                        <p class="text-muted">Reviewed by {{ $review->user->name }} on {{ $review->created_at->format('F j, Y') }}</p>
                    </div>

                    <div class="mb-4">
                        <h6>Review</h6>
                        <p class="mb-0">{{ $review->comment }}</p>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('services.show', $review->service) }}" class="btn btn-outline-primary">View Service</a>
                        <a href="{{ route('bookings.show', $review->booking) }}" class="btn btn-outline-secondary">View Booking</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .rating-display {
        display: inline-block;
    }

    .rating-display .star {
        color: #ddd;
        font-size: 1.5rem;
    }

    .rating-display .star.filled {
        color: #ffc107;
    }
</style>
@endpush
@endsection 