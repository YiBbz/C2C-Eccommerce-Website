<?php
require_once 'includes/auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get booking ID from URL
$booking_id = $_GET['id'] ?? null;

if (!$booking_id) {
    $_SESSION['error'] = "Invalid booking ID";
    header('Location: dashboard.php');
    exit;
}

// Get booking details
$stmt = $conn->prepare("
    SELECT b.*, 
           s.title as service_title, 
           s.description as service_description,
           s.price as service_price,
           s.image as service_image,
           u_provider.full_name as provider_name,
           u_provider.email as provider_email,
           u_customer.full_name as customer_name,
           u_customer.email as customer_email
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN users u_provider ON b.provider_id = u_provider.id
    JOIN users u_customer ON b.customer_id = u_customer.id
    WHERE b.id = ? AND (b.customer_id = ? OR b.provider_id = ?)
");
$stmt->execute([$booking_id, $_SESSION['user_id'], $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    $_SESSION['error'] = "Booking not found or you don't have permission to view it";
    header('Location: dashboard.php');
    exit;
}

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    if ($booking['status'] === 'pending') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        if ($stmt->execute([$booking_id])) {
            $_SESSION['success'] = "Booking cancelled successfully";
            header('Location: booking-details.php?id=' . $booking_id);
            exit;
        } else {
            $_SESSION['error'] = "Failed to cancel booking";
        }
    } else {
        $_SESSION['error'] = "Only pending bookings can be cancelled";
    }
}

// Handle booking acceptance/rejection (for service providers)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_booking'])) {
    if ($booking['status'] === 'pending' && $_SESSION['user_id'] === $booking['provider_id']) {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'accepted' WHERE id = ?");
        if ($stmt->execute([$booking_id])) {
            $_SESSION['success'] = "Booking accepted successfully";
            header('Location: booking-details.php?id=' . $booking_id);
            exit;
        } else {
            $_SESSION['error'] = "Failed to accept booking";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_booking'])) {
    if ($booking['status'] === 'pending' && $_SESSION['user_id'] === $booking['provider_id']) {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'rejected' WHERE id = ?");
        if ($stmt->execute([$booking_id])) {
            $_SESSION['success'] = "Booking rejected successfully";
            header('Location: booking-details.php?id=' . $booking_id);
            exit;
        } else {
            $_SESSION['error'] = "Failed to reject booking";
        }
    }
}

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="bg-grey-900 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Booking Details</h1>
        <p class="text-xl text-purple-200">View and manage your booking</p>
    </div>
</section>

<!-- Booking Details -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <!-- Booking Header -->
                <div class="p-6 border-b border-gray-700">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-white">Booking #<?php echo $booking['id']; ?></h2>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full 
                            <?php echo match($booking['status']) {
                                'pending' => 'bg-yellow-900 text-yellow-200',
                                'accepted' => 'bg-blue-900 text-blue-200',
                                'completed' => 'bg-green-900 text-green-200',
                                'rejected' => 'bg-red-900 text-red-200',
                                'cancelled' => 'bg-gray-700 text-gray-200',
                                default => 'bg-blue-900 text-blue-200'
                            }; ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </div>
                </div>

                <!-- Booking Content -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Service Details -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-4">Service Details</h3>
                                <div class="bg-gray-700 rounded-lg p-4">
                                    <h4 class="text-xl font-semibold text-white mb-2">
                                        <?php echo htmlspecialchars($booking['service_title']); ?>
                                    </h4>
                                    <p class="text-gray-300 mb-4">
                                        <?php echo htmlspecialchars($booking['service_description']); ?>
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-2xl font-bold text-purple-400">
                                            R<?php echo number_format($booking['service_price'], 2); ?>
                                        </span>
                                        <span class="text-sm text-gray-400">
                                            Booked on <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Provider Details -->
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-4">Service Provider</h3>
                                <div class="bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-center space-x-4">
                                        <img src="<?php echo htmlspecialchars($booking['service_image'] ?? 'assets/images/default-avatar.png'); ?>" 
                                             alt="Provider" 
                                             class="w-12 h-12 rounded-full object-cover border-2 border-purple-500">
                                        <div>
                                            <h4 class="text-lg font-semibold text-white">
                                                <?php echo htmlspecialchars($booking['provider_name']); ?>
                                            </h4>
                                            <p class="text-gray-400">
                                                <?php echo htmlspecialchars($booking['provider_email']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Information -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-4">Booking Information</h3>
                                <div class="bg-gray-700 rounded-lg p-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400">Booking Date</label>
                                        <p class="text-white">
                                            <?php echo date('F d, Y', strtotime($booking['booking_date'])); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400">Payment Status</label>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php echo match($booking['payment_status']) {
                                                'pending' => 'bg-yellow-900 text-yellow-200',
                                                'completed' => 'bg-green-900 text-green-200',
                                                'refunded' => 'bg-red-900 text-red-200',
                                                default => 'bg-blue-900 text-blue-200'
                                            }; ?>">
                                            <?php echo ucfirst($booking['payment_status']); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400">Payment Method</label>
                                        <p class="text-white">
                                            <?php echo ucfirst($booking['payment_method']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-4">Actions</h3>
                                <div class="bg-gray-700 rounded-lg p-4">
                                    <div class="flex flex-col space-y-3">
                                        <?php if ($booking['status'] === 'pending'): ?>
                                            <?php if ($_SESSION['role'] === 'customer'): ?>
                                                <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>" 
                                                   class="bg-red-600 text-white px-4 py-2 rounded-lg text-center font-semibold hover:bg-red-700 transition duration-300"
                                                   onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                    Cancel Booking
                                                </a>
                                            <?php else: ?>
                                                <div class="flex space-x-3">
                                                    <a href="accept-booking.php?id=<?php echo $booking['id']; ?>" 
                                                       class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg text-center font-semibold hover:bg-green-700 transition duration-300">
                                                        Accept
                                                    </a>
                                                    <a href="reject-booking.php?id=<?php echo $booking['id']; ?>" 
                                                       class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg text-center font-semibold hover:bg-red-700 transition duration-300"
                                                       onclick="return confirm('Are you sure you want to reject this booking?')">
                                                        Reject
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if ($booking['status'] === 'accepted' && $_SESSION['role'] === 'provider'): ?>
                                            <a href="complete-booking.php?id=<?php echo $booking['id']; ?>" 
                                               class="bg-green-600 text-white px-4 py-2 rounded-lg text-center font-semibold hover:bg-green-700 transition duration-300">
                                                Mark as Completed
                                            </a>
                                        <?php endif; ?>

                                        <a href="<?php echo $_SESSION['role'] === 'customer' ? 'customer-messages.php' : 'provider-messages.php'; ?>" 
                                           class="bg-purple-600 text-white px-4 py-2 rounded-lg text-center font-semibold hover:bg-purple-700 transition duration-300">
                                            Contact <?php echo $_SESSION['role'] === 'customer' ? 'Provider' : 'Customer'; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?> 