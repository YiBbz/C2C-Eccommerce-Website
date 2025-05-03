@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>My Reviews</h4>
                </div>
                <div class="card-body">
                    @if($reviews->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">You haven't written any reviews yet.</p>
                            <a href="{{ route('bookings.index') }}" class="btn btn-primary">View My Bookings</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Provider</th>
                                        <th>Rating</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reviews as $review)
                                        <tr>
                                            <td>
                                                <a href="{{ route('services.show', $review->service) }}">
                                                    {{ $review->service->title }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('providers.show', $review->service->provider) }}">
                                                    {{ $review->service->provider->business_name }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="rating-display">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                                                    @endfor
                                                </div>
                                            </td>
                                            <td>{{ $review->created_at->format('F j, Y') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('reviews.show', $review) }}" class="btn btn-sm btn-outline-primary">View</a>
                                                    <a href="{{ route('reviews.edit', $review) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                    <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this review?')">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $reviews->links() }}
                        </div>
                    @endif
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
        font-size: 1rem;
    }

    .rating-display .star.filled {
        color: #ffc107;
    }
</style>
@endpush
@endsection 