@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Provider Profile -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ $provider->user->profile_photo_url ?? 'https://via.placeholder.com/150' }}" 
                         class="rounded-circle mb-3" 
                         alt="{{ $provider->user->name }}" 
                         width="150" 
                         height="150">
                    <h2 class="card-title">{{ $provider->user->name }}</h2>
                    <h5 class="text-muted">{{ $provider->business_name }}</h5>
                    
                    <div class="rating-display mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="star {{ $i <= $provider->rating ? 'filled' : '' }}">★</span>
                        @endfor
                        <span class="ms-2">({{ $provider->total_reviews }} reviews)</span>
                    </div>

                    <p class="card-text">{{ $provider->description }}</p>

                    @if(auth()->id() == $provider->user_id)
                        <div class="d-grid gap-2">
                            <a href="{{ route('providers.edit', $provider) }}" class="btn btn-primary">Edit Profile</a>
                            <a href="{{ route('services.create') }}" class="btn btn-outline-primary">Add New Service</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Contact Information</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            {{ $provider->user->email }}
                        </li>
                        @if($provider->phone)
                            <li class="mb-2">
                                <i class="fas fa-phone me-2"></i>
                                {{ $provider->phone }}
                            </li>
                        @endif
                        @if($provider->address)
                            <li class="mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                {{ $provider->address }}
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Services -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Services Offered</h4>
                </div>
                <div class="card-body">
                    @if($provider->services->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">No services available yet.</p>
                            @if(auth()->id() == $provider->user_id)
                                <a href="{{ route('services.create') }}" class="btn btn-primary">Add Your First Service</a>
                            @endif
                        </div>
                    @else
                        <div class="row">
                            @foreach($provider->services as $service)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <img src="{{ $service->cover_image ?? 'https://via.placeholder.com/300x200' }}" 
                                             class="card-img-top service-image" 
                                             alt="{{ $service->title }}">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $service->title }}</h5>
                                            <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-primary fw-bold">${{ number_format($service->price, 2) }}</span>
                                                <div>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <span>{{ number_format($service->rating, 1) }}</span>
                                                    <span class="text-muted">({{ $service->total_reviews }})</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <a href="{{ route('services.show', $service) }}" class="btn btn-primary w-100">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reviews -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Customer Reviews</h4>
                </div>
                <div class="card-body">
                    @if($provider->reviews->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">No reviews yet.</p>
                        </div>
                    @else
                        @foreach($provider->reviews as $review)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong>{{ $review->user->name }}</strong>
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                                <p>{{ $review->comment }}</p>
                                <small class="text-muted">Service: {{ $review->service->title }}</small>
                            </div>
                        @endforeach
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
        font-size: 1.5rem;
    }

    .rating-display .star.filled {
        color: #ffc107;
    }

    .service-image {
        height: 200px;
        object-fit: cover;
    }
</style>
@endpush
@endsection 