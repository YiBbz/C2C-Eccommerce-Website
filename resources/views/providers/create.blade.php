@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Become a Service Provider</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('providers.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Business Information -->
                        <div class="mb-4">
                            <h5 class="mb-3">Business Information</h5>
                            
                            <div class="mb-3">
                                <label for="business_name" class="form-label">Business Name</label>
                                <input type="text" 
                                       class="form-control @error('business_name') is-invalid @enderror" 
                                       id="business_name" 
                                       name="business_name" 
                                       value="{{ old('business_name') }}" 
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
                                          required>{{ old('description') }}</textarea>
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
                                                {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
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
                                       value="{{ old('phone') }}" 
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
                                          required>{{ old('address') }}</textarea>
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
                                <input type="file" 
                                       class="form-control @error('business_license') is-invalid @enderror" 
                                       id="business_license" 
                                       name="business_license" 
                                       accept=".pdf,.jpg,.jpeg,.png" 
                                       required>
                                @error('business_license')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Upload a scanned copy of your business license (PDF, JPG, or PNG)</small>
                            </div>

                            <div class="mb-3">
                                <label for="tax_id" class="form-label">Tax ID Number</label>
                                <input type="text" 
                                       class="form-control @error('tax_id') is-invalid @enderror" 
                                       id="tax_id" 
                                       name="tax_id" 
                                       value="{{ old('tax_id') }}" 
                                       required>
                                @error('tax_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('terms') is-invalid @enderror" 
                                       type="checkbox" 
                                       id="terms" 
                                       name="terms" 
                                       required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Submit Application</button>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Service Provider Responsibilities</h6>
                <p>As a service provider, you agree to:</p>
                <ul>
                    <li>Provide accurate and truthful information about your business and services</li>
                    <li>Maintain professional conduct with customers</li>
                    <li>Respond to customer inquiries and booking requests in a timely manner</li>
                    <li>Honor all bookings and appointments</li>
                    <li>Maintain proper licensing and insurance as required by law</li>
                </ul>

                <h6>2. Service Quality</h6>
                <p>You agree to:</p>
                <ul>
                    <li>Provide services at the quality level advertised</li>
                    <li>Use only qualified personnel to perform services</li>
                    <li>Maintain proper equipment and materials</li>
                    <li>Follow all safety protocols and regulations</li>
                </ul>

                <h6>3. Payment and Fees</h6>
                <p>You understand that:</p>
                <ul>
                    <li>ServiceHub charges a commission on each booking</li>
                    <li>Payments will be processed through our secure payment system</li>
                    <li>You are responsible for all applicable taxes</li>
                </ul>

                <h6>4. Privacy and Data Protection</h6>
                <p>You agree to:</p>
                <ul>
                    <li>Protect customer data and privacy</li>
                    <li>Not share customer information with third parties</li>
                    <li>Comply with all data protection regulations</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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