@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Category Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>{{ $category->name }}</h1>
            <p class="text-muted">{{ $category->description }}</p>
        </div>
        @if(auth()->check() && auth()->user()->isAdmin())
            <div class="col-md-4 text-end">
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-edit"></i> Edit Category
                </a>
            </div>
        @endif
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('categories.show', $category) }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           placeholder="Search services..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="rating">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="sort">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="row">
        @forelse($services as $service)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $service->cover_image ? Storage::url($service->cover_image) : 'https://via.placeholder.com/300x200' }}" 
                         class="card-img-top service-image" 
                         alt="{{ $service->title }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->title }}</h5>
                        <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-primary fw-bold">${{ number_format($service->price, 2) }}</span>
                            <div>
                                <i class="fas fa-star text-warning"></i>
                                <span>{{ number_format($service->rating, 1) }}</span>
                                <span class="text-muted">({{ $service->total_reviews }})</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('providers.show', $service->provider) }}" class="text-muted">
                                <i class="fas fa-user"></i> {{ $service->provider->user->name }}
                            </a>
                            <a href="{{ route('services.show', $service) }}" class="btn btn-primary">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <h3 class="text-muted">No services found in this category</h3>
                    <p class="text-muted">Try adjusting your search or filter criteria</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $services->appends(request()->query())->links() }}
    </div>
</div>

@push('styles')
<style>
    .service-image {
        height: 200px;
        object-fit: cover;
    }
</style>
@endpush
@endsection 