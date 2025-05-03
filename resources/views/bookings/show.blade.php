@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Booking Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1 class="card-title mb-0">{{ $booking->service->title }}</h1>
                            <p class="text-muted">Booking #{{ $booking->id }}</p>
                        </div>
                        <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'primary') }} fs-6">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Booking Information</h5>
                            <p><strong>Date:</strong> {{ $booking->booking_date->format('F j, Y g:i A') }}</p>
                            <p><strong>Duration:</strong> {{ $booking->service->duration }} {{ $booking->service->price_type == 'hourly' ? 'hours' : 'days' }}</p>
                            <p><strong>Total Amount:</strong> ${{ number_format($booking->total_amount, 2) }}</p>
                            <p><strong>Payment Status:</strong> {{ $booking->is_paid ? 'Paid' : 'Pending' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Location Details</h5>
                            <p><strong>Service Type:</strong> {{ ucfirst($booking->service->location_type) }}</p>
                            @if($booking->location)
                                <p><strong>Location:</strong> {{ $booking->location }}</p>
                            @endif
                        </div>
                    </div>

                    @if($booking->special_instructions)
                        <div class="mb-4">
                            <h5>Special Instructions</h5>
                            <p class="text-muted">{{ $booking->special_instructions }}</p>
                        </div>
                    @endif

                    <!-- Status Management -->
                    @if(auth()->user()->hasRole('provider') && $booking->status == 'pending')
                        <div class="mb-4">
                            <h5>Manage Booking</h5>
                            <div class="btn-group">
                                <form action="{{ route('bookings.update', $booking) }}" method="POST" class="me-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="btn btn-success">Confirm Booking</button>
                                </form>
                                <form action="{{ route('bookings.update', $booking) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-danger">Reject Booking</button>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if(auth()->user()->hasRole('provider') && $booking->status == 'confirmed')
                        <div class="mb-4">
                            <h5>Complete Booking</h5>
                            <form action="{{ route('bookings.update', $booking) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-success">Mark as Completed</button>
                            </form>
                        </div>
                    @endif

                    @if(auth()->user()->hasRole('customer') && $booking->status == 'pending')
                        <div class="mb-4">
                            <h5>Cancel Booking</h5>
                            <form action="{{ route('bookings.update', $booking) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-danger">Cancel Booking</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Chat Section -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Chat</h5>
                    <div id="chat-messages" class="mb-3" style="height: 300px; overflow-y: auto;">
                        @foreach($booking->messages as $message)
                            <div class="message {{ $message->sender_id == auth()->id() ? 'text-end' : 'text-start' }} mb-2">
                                <div class="d-inline-block p-2 rounded {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                                    {{ $message->message }}
                                    <small class="d-block text-muted">{{ $message->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <form id="chat-form" action="{{ route('chat.send', $booking) }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="message" class="form-control" placeholder="Type your message...">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Service and Provider Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Service Details</h5>
                    <img src="{{ $booking->service->cover_image ?? 'https://via.placeholder.com/300x200' }}" class="img-fluid rounded mb-3" alt="{{ $booking->service->title }}">
                    <p>{{ $booking->service->description }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-primary fw-bold">${{ number_format($booking->service->price, 2) }}</span>
                        <div>
                            <i class="fas fa-star text-warning"></i>
                            <span>{{ number_format($booking->service->rating, 1) }}</span>
                            <span class="text-muted">({{ $booking->service->total_reviews }})</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Provider Information</h5>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $booking->provider->user->profile_photo_url ?? 'https://via.placeholder.com/50' }}" class="rounded-circle me-3" alt="Provider" width="50">
                        <div>
                            <h6 class="mb-0">{{ $booking->provider->user->name }}</h6>
                            <small class="text-muted">{{ $booking->provider->business_name }}</small>
                        </div>
                    </div>
                    <p>{{ $booking->provider->description }}</p>
                    <div class="d-flex justify-content-between">
                        <div>
                            <i class="fas fa-star text-warning"></i>
                            <span>{{ number_format($booking->provider->rating, 1) }}</span>
                        </div>
                        <div>
                            <i class="fas fa-check-circle text-success"></i>
                            <span>{{ $booking->provider->total_reviews }} reviews</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Scroll to bottom of chat
    function scrollToBottom() {
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Handle chat form submission
    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const messageInput = form.querySelector('input[name="message"]');
        const message = messageInput.value.trim();

        if (message) {
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const chatMessages = document.getElementById('chat-messages');
                    const now = new Date();
                    const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    
                    const messageHtml = `
                        <div class="message text-end mb-2">
                            <div class="d-inline-block p-2 rounded bg-primary text-white">
                                ${message}
                                <small class="d-block text-white-50">Just now</small>
                            </div>
                        </div>
                    `;
                    
                    chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                    messageInput.value = '';
                    scrollToBottom();
                }
            });
        }
    });

    // Initialize Pusher for real-time chat
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}'
    });

    const channel = pusher.subscribe('booking.{{ $booking->id }}');
    channel.bind('message', function(data) {
        if (data.sender_id !== {{ auth()->id() }}) {
            const chatMessages = document.getElementById('chat-messages');
            const messageHtml = `
                <div class="message text-start mb-2">
                    <div class="d-inline-block p-2 rounded bg-light">
                        ${data.message}
                        <small class="d-block text-muted">Just now</small>
                    </div>
                </div>
            `;
            
            chatMessages.insertAdjacentHTML('beforeend', messageHtml);
            scrollToBottom();
        }
    });

    // Scroll to bottom on page load
    scrollToBottom();
</script>
@endpush
@endsection 