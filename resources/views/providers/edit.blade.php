@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Provider Profile</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('providers.update', $provider) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Business Information -->
                        <div class="mb-4">
                            <h5 class="mb-3">Business Information</h5>
                            
                            <div class="mb-3">
                                <label for="business_name" class="form-label">Business Name</label>
                                <input type="text" 
                                       class="form-control @error('business_name') is-invalid @enderror" 
                                       id="business_name" 
                                       name="business_name" 
                                       value="{{ old('business_name', $provider->business_name) }}" 
                                       required>
                                @error('business_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Business Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4" 
                                          required>{{ old('description', $provider->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="categories" class="form-label">Service Categories</label>
                                <select class="form-select @error('categories') is-invalid @enderror" 
                                        id="categories" 
                                        name="categories[]" 
                                        multiple 
                                        required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ in_array($category->id, old('categories', $provider->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categories')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple categories</small>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mb-4">
                            <h5 class="mb-3">Contact Information</h5>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $provider->phone) }}" 
                                       required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Business Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" 
                                          name="address" 
                                          rows="3" 
                                          required>{{ old('address', $provider->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Business Documents -->
                        <div class="mb-4">
                            <h5 class="mb-3">Business Documents</h5>
                            
                            <div class="mb-3">
                                <label for="business_license" class="form-label">Business License</label>
                                @if($provider->business_license)
                                    <div class="mb-2">
                                        <a href="{{ Storage::url($provider->business_license) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            View Current License
                                        </a>
                                    </div>
                                @endif
                                <input type="file" 
                                       class="form-control @error('business_license') is-invalid @enderror" 
                                       id="business_license" 
                                       name="business_license" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @error('business_license')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Upload a new license if you need to update it (PDF, JPG, or PNG)</small>
                            </div>

                            <div class="mb-3">
                                <label for="tax_id" class="form-label">Tax ID Number</label>
                                <input type="text" 
                                       class="form-control @error('tax_id') is-invalid @enderror" 
                                       id="tax_id" 
                                       name="tax_id" 
                                       value="{{ old('tax_id', $provider->tax_id) }}" 
                                       required>
                                @error('tax_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Profile Photo -->
                        <div class="mb-4">
                            <h5 class="mb-3">Profile Photo</h5>
                            
                            <div class="mb-3">
                                @if($provider->user->profile_photo_path)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($provider->user->profile_photo_path) }}" 
                                             alt="Current Profile Photo" 
                                             class="img-thumbnail" 
                                             style="max-width: 200px;">
                                    </div>
                                @endif
                                <input type="file" 
                                       class="form-control @error('profile_photo') is-invalid @enderror" 
                                       id="profile_photo" 
                                       name="profile_photo" 
                                       accept="image/*">
                                @error('profile_photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Upload a new profile photo if you want to change it</small>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                            <a href="{{ route('providers.show', $provider) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-label {
        font-weight: 500;
    }
</style>
@endpush
@endsection 