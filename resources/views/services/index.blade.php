@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Search and Filter Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('services.search') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search services..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="location_type" class="form-select">
                                <option value="">All Locations</option>
                                <option value="remote" {{ request('location_type') == 'remote' ? 'selected' : '' }}>Remote</option>
                                <option value="onsite" {{ request('location_type') == 'onsite' ? 'selected' : '' }}>On-site</option>
                                <option value="both" {{ request('location_type') == 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="row">
        @forelse($services as $service)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $service->cover_image ?? 'https://via.placeholder.com/300x200' }}" class="card-img-top service-image" alt="{{ $service->title }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->title }}</h5>
                        <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-primary fw-bold">${{ number_format($service->price, 2) }}</span>
                            <div>
                                <i class="fas fa-star text-warning"></i>
                                <span>{{ number_format($service->rating, 1) }}</span>
                                <span class="text-muted">({{ $service->total_reviews }})</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-info">{{ $service->category->name }}</span>
                            <span class="badge bg-secondary">{{ $service->location_type }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('services.show', $service) }}" class="btn btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No services found matching your criteria.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-12">
            {{ $services->links() }}
        </div>
    </div>
</div>
@endsection 