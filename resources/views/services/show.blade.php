@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Service Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <img src="{{ $service->cover_image ?? 'https://via.placeholder.com/800x400' }}" class="card-img-top" alt="{{ $service->title }}">
                <div class="card-body">
                    <h1 class="card-title">{{ $service->title }}</h1>
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <i class="fas fa-star text-warning"></i>
                            <span>{{ number_format($service->rating, 1) }}</span>
                            <span class="text-muted">({{ $service->total_reviews }} reviews)</span>
                        </div>
                        <div class="me-3">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ ucfirst($service->location_type) }}</span>
                        </div>
                        <div>
                            <i class="fas fa-clock"></i>
                            <span>{{ $service->duration }} {{ $service->price_type == 'hourly' ? 'hours' : 'days' }}</span>
                        </div>
                    </div>
                    <h4 class="text-primary mb-3">${{ number_format($service->price, 2) }} {{ $service->price_type == 'hourly' ? 'per hour' : 'fixed' }}</h4>
                    <h5 class="mb-3">About This Service</h5>
                    <p class="card-text">{{ $service->description }}</p>
                    
                    @if($service->images)
                        <h5 class="mt-4 mb-3">Gallery</h5>
                        <div class="row">
                            @foreach($service->images as $image)
                                <div class="col-md-4 mb-3">
                                    <img src="{{ $image }}" class="img-fluid rounded" alt="Service Image">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Reviews</h5>
                    @forelse($service->reviews as $review)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>{{ $review->customer->name }}</strong>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                            <p>{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p class="text-muted">No reviews yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Booking Section -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Book This Service</h5>
                    @auth
                        @if(auth()->user()->hasRole('customer'))
                            <form action="{{ route('bookings.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                                
                                <div class="mb-3">
                                    <label for="booking_date" class="form-label">Booking Date</label>
                                    <input type="datetime-local" class="form-control" id="booking_date" name="booking_date" required>
                                </div>

                                @if($service->location_type != 'remote')
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="location" name="location" required>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="special_instructions" class="form-label">Special Instructions</label>
                                    <textarea class="form-control" id="special_instructions" name="special_instructions" rows="3"></textarea>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Book Now</button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info">
                                Only customers can book services. Please log in with a customer account.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            Please <a href="{{ route('login') }}">login</a> to book this service.
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Provider Info -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">About the Provider</h5>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $service->provider->user->profile_photo_url ?? 'https://via.placeholder.com/50' }}" class="rounded-circle me-3" alt="Provider" width="50">
                        <div>
                            <h6 class="mb-0">{{ $service->provider->user->name }}</h6>
                            <small class="text-muted">{{ $service->provider->business_name }}</small>
                        </div>
                    </div>
                    <p>{{ $service->provider->description }}</p>
                    <div class="d-flex justify-content-between">
                        <div>
                            <i class="fas fa-star text-warning"></i>
                            <span>{{ number_format($service->provider->rating, 1) }}</span>
                        </div>
                        <div>
                            <i class="fas fa-check-circle text-success"></i>
                            <span>{{ $service->provider->total_reviews }} reviews</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 