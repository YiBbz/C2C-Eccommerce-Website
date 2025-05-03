@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Write a Review</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>{{ $booking->service->title }}</h5>
                        <p class="text-muted">Provider: {{ $booking->provider->business_name }}</p>
                        <p>Booking Date: {{ $booking->booking_date->format('F j, Y g:i A') }}</p>
                    </div>

                    <form action="{{ route('reviews.store', $booking) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Rating</label>
                            <div class="rating">
                                <input type="radio" name="rating" value="5" id="5" required>
                                <label for="5">☆</label>
                                <input type="radio" name="rating" value="4" id="4">
                                <label for="4">☆</label>
                                <input type="radio" name="rating" value="3" id="3">
                                <label for="3">☆</label>
                                <input type="radio" name="rating" value="2" id="2">
                                <label for="2">☆</label>
                                <input type="radio" name="rating" value="1" id="1">
                                <label for="1">☆</label>
                            </div>
                            @error('rating')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="form-label">Your Review</label>
                            <textarea name="comment" id="comment" class="form-control @error('comment') is-invalid @enderror" rows="5" required>{{ old('comment') }}</textarea>
                            @error('comment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }

    .rating input {
        display: none;
    }

    .rating label {
        font-size: 2rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }

    .rating input:checked ~ label,
    .rating label:hover,
    .rating label:hover ~ label {
        color: #ffc107;
    }

    .rating input:checked + label {
        color: #ffc107;
    }
</style>
@endpush
@endsection 