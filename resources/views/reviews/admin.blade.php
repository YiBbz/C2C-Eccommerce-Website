@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Manage Reviews</h4>
                        <div class="d-flex align-items-center">
                            <span class="me-3">
                                Total Reviews: {{ $totalReviews }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($reviews->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">No reviews in the system yet.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Provider</th>
                                        <th>Customer</th>
                                        <th>Rating</th>
                                        <th>Review</th>
                                        <th>Date</th>
                                        <th>Status</th>
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
                                            <td>{{ $review->user->name }}</td>
                                            <td>
                                                <div class="rating-display">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                                                    @endfor
                                                </div>
                                            </td>
                                            <td>{{ Str::limit($review->comment, 50) }}</td>
                                            <td>{{ $review->created_at->format('F j, Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $review->is_visible ? 'success' : 'danger' }}">
                                                    {{ $review->is_visible ? 'Visible' : 'Hidden' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('reviews.show', $review) }}" class="btn btn-sm btn-outline-primary">View</a>
                                                    <form action="{{ route('reviews.toggle-visibility', $review) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-{{ $review->is_visible ? 'warning' : 'success' }}">
                                                            {{ $review->is_visible ? 'Hide' : 'Show' }}
                                                        </button>
                                                    </form>
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