@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Service Providers</h1>
            <p class="text-muted">Find the perfect service provider for your needs</p>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('providers.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search providers..." value="{{ request('search') }}">
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
                        <div class="col-md-3">
                            <select name="rating" class="form-select">
                                <option value="">All Ratings</option>
                                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Providers Grid -->
    <div class="row">
        @forelse($providers as $provider)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $provider->user->profile_photo_url ?? 'https://via.placeholder.com/50' }}" 
                                 class="rounded-circle me-3" 
                                 alt="{{ $provider->user->name }}" 
                                 width="50" 
                                 height="50">
                            <div>
                                <h5 class="card-title mb-0">{{ $provider->user->name }}</h5>
                                <p class="text-muted mb-0">{{ $provider->business_name }}</p>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="rating-display mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $provider->rating ? 'filled' : '' }}">★</span>
                                @endfor
                                <span class="ms-2">({{ $provider->total_reviews }} reviews)</span>
                            </div>
                            <p class="card-text">{{ Str::limit($provider->description, 100) }}</p>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">{{ $provider->services_count }} Services</span>
                            <a href="{{ route('providers.show', $provider) }}" class="btn btn-outline-primary">View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No service providers found matching your criteria.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-12">
            {{ $providers->links() }}
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