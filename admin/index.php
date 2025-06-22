<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Check if the user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$conn = getDB();

// Get statistics
try {
    // Total users
    $stmt = $conn->query("SELECT COUNT(*) FROM users");
    $total_users = $stmt->fetchColumn();

    // Total services
    $stmt = $conn->query("SELECT COUNT(*) FROM services");
    $total_services = $stmt->fetchColumn();

    // Total bookings
    $stmt = $conn->query("SELECT COUNT(*) FROM bookings");
    $total_bookings = $stmt->fetchColumn();

    // Total reviews
    $stmt = $conn->query("SELECT COUNT(*) FROM reviews");
    $total_reviews = $stmt->fetchColumn();

    // Recent bookings
    $stmt = $conn->query("
        SELECT b.*, s.title as service_title, u.full_name as customer_name
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users u ON b.customer_id = u.id
        ORDER BY b.created_at DESC
        LIMIT 5
    ");
    $recent_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent users
    $stmt = $conn->query("
        SELECT * FROM users 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error loading dashboard data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="../assets/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
<?php include 'admin-navbar.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Welcome to Admin Dashboard</h1>
        <p class="text-xl text-blue-100">Manage your service marketplace</p>
    </div>
</section>

<!-- Statistics -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 ease-in-out p-6 border border-gray-100">
                <div class="flex flex-col items-center justify-center text-center"> <div class="p-4 rounded-full bg-blue-100 text-blue-600 mb-4"> <i class="fas fa-users text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-md uppercase tracking-wide font-semibold">Total Users</h3>
                        <p class="text-4xl font-extrabold text-gray-800 mt-1"><?php echo number_format($total_users); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 ease-in-out p-6 border border-gray-100">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="p-4 rounded-full bg-green-100 text-green-600 mb-4">
                        <i class="fas fa-briefcase text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-md uppercase tracking-wide font-semibold">Total Services</h3>
                        <p class="text-4xl font-extrabold text-gray-800 mt-1"><?php echo number_format($total_services); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 ease-in-out p-6 border border-gray-100">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="p-4 rounded-full bg-purple-100 text-purple-600 mb-4">
                        <i class="fas fa-calendar-check text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-md uppercase tracking-wide font-semibold">Total Bookings</h3>
                        <p class="text-4xl font-extrabold text-gray-800 mt-1"><?php echo number_format($total_bookings); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 ease-in-out p-6 border border-gray-100">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="p-4 rounded-full bg-yellow-100 text-yellow-600 mb-4">
                        <i class="fas fa-star text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-md uppercase tracking-wide font-semibold">Total Reviews</h3>
                        <p class="text-4xl font-extrabold text-gray-800 mt-1"><?php echo number_format($total_reviews); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Activity -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Bookings -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800">Recent Bookings</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($recent_bookings)): ?>
                        <p class="text-gray-600 text-center py-4">No recent bookings</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recent_bookings as $booking): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($booking['service_title']); ?></h3>
                                        <p class="text-sm text-gray-600">Customer: <?php echo htmlspecialchars($booking['customer_name']); ?></p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php echo $booking['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                            ($booking['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800">Recent Users</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($recent_users)): ?>
                        <p class="text-gray-600 text-center py-4">No recent users</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recent_users as $user): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                            ($user['role'] === 'provider' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?> 