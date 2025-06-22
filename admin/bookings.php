<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Check if the user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$conn = getDB();

// Get all bookings with user and service details
try {
    $stmt = $conn->query("
        SELECT b.*, s.title as service_title, 
               u_customer.full_name as customer_name,
               u_provider.full_name as provider_name
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users u_customer ON b.customer_id = u_customer.id
        JOIN users u_provider ON b.provider_id = u_provider.id
        ORDER BY b.created_at DESC
    ");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error loading bookings: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin Dashboard</title>
    <link href="../assets/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
<?php include 'admin-navbar.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Manage Bookings</h1>
        <p class="text-xl text-blue-100">View and manage all service bookings</p>
    </div>
</section>

<!-- Bookings List -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">All Bookings</h2>
            </div>
            <div class="p-6">
                <?php if (empty($bookings)): ?>
                    <p class="text-gray-600 text-center py-4">No bookings found</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($bookings as $booking): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($booking['service_title']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($booking['customer_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($booking['provider_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                <?php echo match($booking['status']) {
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'accepted' => 'bg-blue-100 text-blue-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                                    default => 'bg-blue-100 text-blue-800'
                                                }; ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                <?php echo match($booking['payment_status']) {
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'refunded' => 'bg-yellow-100 text-yellow-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                }; ?>">
                                                <?php echo ucfirst($booking['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <?php echo ucfirst($booking['payment_method']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            $<?php echo number_format($booking['total_amount'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="booking-details.php?id=<?php echo $booking['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="booking-details.php?id=<?php echo $booking['id']; ?>&action=edit" 
                                               class="text-yellow-600 hover:text-yellow-900 mr-3">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="booking-details.php?id=<?php echo $booking['id']; ?>&action=delete" 
                                               class="text-red-600 hover:text-red-900"
                                               onclick="return confirm('Are you sure you want to delete this booking?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
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
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>