@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Service Categories</h1>
            <p class="text-muted">Browse services by category</p>
        </div>
        @if(auth()->check() && auth()->user()->isAdmin())
            <div class="col-md-4 text-end">
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Category
                </a>
            </div>
        @endif
    </div>

    <div class="row">
        @forelse($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($category->image)
                        <img src="{{ Storage::url($category->image) }}" 
                             class="card-img-top category-image" 
                             alt="{{ $category->name }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $category->name }}</h5>
                        <p class="card-text">{{ Str::limit($category->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">
                                {{ $category->services_count }} Services
                            </span>
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-primary">
                                View Services
                            </a>
                        </div>
                    </div>
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Are you sure you want to delete this category?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <h3 class="text-muted">No categories available</h3>
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <a href="{{ route('categories.create') }}" class="btn btn-primary mt-3">
                            Create Your First Category
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $categories->links() }}
    </div>
</div>

@push('styles')
<style>
    .category-image {
        height: 200px;
        object-fit: cover;
    }
</style>
@endpush
@endsection 