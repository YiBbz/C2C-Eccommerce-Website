<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$conn = getDB();

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();

// Get user's bookings if they are a customer
$bookings = [];
if ($user['role'] === 'customer') {
    $stmt = $conn->prepare("
        SELECT b.*, s.title as service_title, s.price, u.full_name as provider_name
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users u ON b.provider_id = u.id
        WHERE b.customer_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $bookings = $stmt->fetchAll();
}

// Get user's services if they are a service provider
$services = [];
if ($user['role'] === 'service_provider') {
    // Get services
    $stmt = $conn->prepare("
        SELECT * FROM services 
        WHERE provider_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $services = $stmt->fetchAll();

    // Get bookings for services
    $stmt = $conn->prepare("
        SELECT b.*, s.title as service_title, u.full_name as customer_name
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users u ON b.customer_id = u.id
        WHERE b.provider_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $service_bookings = $stmt->fetchAll();
}
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-grey-900 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Dashboard</h1>
        <p class="text-xl text-purple-200">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
    </div>
</section>

<!-- Dashboard Content -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <?php if ($user['role'] === 'customer'): ?>
            <!-- Customer Dashboard -->
            <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-700 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-white">My Bookings</h2>
                    <a href="services.php" class="bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                        Find Services
                    </a>
                </div>
                <div class="p-6">
                    <?php if (empty($bookings)): ?>
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-purple-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar text-purple-400 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">No Bookings Yet</h3>
                            <p class="text-gray-400">Start exploring services and make your first booking!</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Service</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Provider</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Payment</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-gray-700">
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr class="hover:bg-gray-700 transition duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                <?php echo htmlspecialchars($booking['service_title']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                <?php echo htmlspecialchars($booking['provider_name']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                R<?php echo number_format($booking['total_amount'], 2); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    <?php echo match($booking['payment_status']) {
                                                        'pending' => 'bg-yellow-900 text-yellow-200',
                                                        'completed' => 'bg-green-900 text-green-200',
                                                        'refunded' => 'bg-red-900 text-red-200',
                                                        default => 'bg-blue-900 text-blue-200'
                                                    }; ?>">
                                                    <?php echo ucfirst($booking['payment_status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="booking-details.php?id=<?php echo $booking['id']; ?>" 
                                                   class="text-purple-400 hover:text-purple-300">View</a>
                                                <?php if ($booking['status'] === 'pending'): ?>
                                                    <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>" 
                                                       class="text-red-400 hover:text-red-300"
                                                       onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php else: ?>
            <!-- Service Provider Dashboard -->
            <div class="space-y-8">
                <!-- Services Section -->
                <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-700 flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-white">My Services</h2>
                        <a href="add-service.php" class="bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                            Add New Service
                        </a>
                    </div>
                    <div class="p-6">
                        <?php if (empty($services)): ?>
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-purple-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-briefcase text-purple-400 text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-white mb-2">No Services Yet</h3>
                                <p class="text-gray-400">Start by adding your first service!</p>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach ($services as $service): ?>
                                    <div class="bg-gray-700 rounded-lg shadow-md overflow-hidden border border-gray-600">
                                        <?php if ($service['image']): ?>
                                            <img src="<?php echo htmlspecialchars($service['image']); ?>" 
                                                 class="w-full h-48 object-cover" 
                                                 alt="<?php echo htmlspecialchars($service['title']); ?>">
                                        <?php endif; ?>
                                        <div class="p-6">
                                            <h3 class="text-xl font-semibold text-white mb-2">
                                                <?php echo htmlspecialchars($service['title']); ?>
                                            </h3>
                                            <p class="text-gray-300 mb-4">
                                                <?php echo htmlspecialchars(substr($service['description'], 0, 100)) . '...'; ?>
                                            </p>
                                            <div class="flex justify-between items-center mb-4">
                                                <span class="text-2xl font-bold text-purple-400">
                                                    R<?php echo number_format($service['price'], 2); ?>
                                                </span>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                    <?php echo $service['status'] === 'active' ? 'bg-green-900 text-green-200' : 'bg-gray-700 text-gray-200'; ?>">
                                                    <?php echo ucfirst($service['status']); ?>
                                                </span>
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="edit-service.php?id=<?php echo $service['id']; ?>" 
                                                   class="flex-1 bg-purple-600 text-white text-center px-4 py-2 rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                                                    Edit
                                                </a>
                                                <a href="service-details.php?id=<?php echo $service['id']; ?>" 
                                                   class="flex-1 bg-gray-600 text-white text-center px-4 py-2 rounded-lg font-semibold hover:bg-gray-500 transition duration-300">
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Bookings Section -->
                <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-700">
                        <h2 class="text-2xl font-bold text-white">Service Bookings</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($service_bookings)): ?>
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-purple-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-calendar-check text-purple-400 text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-white mb-2">No Bookings Yet</h3>
                                <p class="text-gray-400">You haven't received any bookings for your services yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-700">
                                    <thead class="bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Service</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Payment</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                                        <?php foreach ($service_bookings as $booking): ?>
                                            <tr class="hover:bg-gray-700 transition duration-150">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                    <?php echo htmlspecialchars($booking['service_title']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                    <?php echo htmlspecialchars($booking['customer_name']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                    <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                    R<?php echo number_format($booking['total_amount'], 2); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        <?php echo match($booking['payment_status']) {
                                                            'pending' => 'bg-yellow-900 text-yellow-200',
                                                            'completed' => 'bg-green-900 text-green-200',
                                                            'refunded' => 'bg-red-900 text-red-200',
                                                            default => 'bg-blue-900 text-blue-200'
                                                        }; ?>">
                                                        <?php echo ucfirst($booking['payment_status']); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                    <a href="booking-details.php?id=<?php echo $booking['id']; ?>" 
                                                       class="text-purple-400 hover:text-purple-300">View</a>
                                                    <?php if ($booking['status'] === 'pending'): ?>
                                                        <a href="accept-booking.php?id=<?php echo $booking['id']; ?>" 
                                                           class="text-green-400 hover:text-green-300">Accept</a>
                                                        <a href="reject-booking.php?id=<?php echo $booking['id']; ?>" 
                                                           class="text-red-400 hover:text-red-300"
                                                           onclick="return confirm('Are you sure you want to reject this booking?')">Reject</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 