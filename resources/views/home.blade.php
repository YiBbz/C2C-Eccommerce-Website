@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row align-items-center py-5">
        <div class="col-md-6">
            <h1 class="display-4 fw-bold mb-4">Find the Perfect Service for Your Needs</h1>
            <p class="lead mb-4">Connect with skilled professionals and get your tasks done efficiently.</p>
            <a href="{{ route('services.index') }}" class="btn btn-primary btn-lg">Browse Services</a>
        </div>
        <div class="col-md-6">
            <img src="https://via.placeholder.com/600x400" alt="ServiceHub Hero" class="img-fluid rounded">
        </div>
    </div>

    <!-- Categories Section -->
    <section class="py-5">
        <h2 class="text-center mb-4">Popular Categories</h2>
        <div class="row">
            @foreach($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas {{ $category->icon }} fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">{{ $category->name }}</h5>
                        <p class="card-text">{{ $category->description }}</p>
                        <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-primary">View Services</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Featured Services Section -->
    <section class="py-5">
        <h2 class="text-center mb-4">Featured Services</h2>
        <div class="row">
            @foreach($featuredServices as $service)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $service->cover_image ?? 'https://via.placeholder.com/300x200' }}" class="card-img-top service-image" alt="{{ $service->title }}">
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
    </section>

    <!-- How It Works Section -->
    <section class="py-5 bg-light">
        <h2 class="text-center mb-4">How It Works</h2>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <div class="p-4">
                    <i class="fas fa-search fa-3x text-primary mb-3"></i>
                    <h4>Find a Service</h4>
                    <p>Browse through our wide range of services and find the perfect match for your needs.</p>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="p-4">
                    <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                    <h4>Book & Pay</h4>
                    <p>Schedule your service and make secure payments through our platform.</p>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="p-4">
                    <i class="fas fa-check-circle fa-3x text-primary mb-3"></i>
                    <h4>Get It Done</h4>
                    <p>Relax while our verified professionals complete your task efficiently.</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 