<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Check if the user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$conn = getDB();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get booking ID from URL
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'update') {
                // Log the update attempt
                error_log("Attempting to update booking ID: " . $booking_id);
                
                $stmt = $conn->prepare("
                    UPDATE bookings 
                    SET status = ?, payment_status = ?
                    WHERE id = ?
                ");
                
                $params = [
                    $_POST['status'],
                    $_POST['payment_status'],
                    $booking_id
                ];
                
                // Log the parameters
                error_log("Update parameters: " . print_r($params, true));
                
                $result = $stmt->execute($params);
                
                if ($result) {
                    // Redirect to bookings page after successful update
                    header("Location: bookings.php");
                    exit;
                } else {
                    $error = "Failed to update booking";
                    error_log("Booking update failed");
                }
            } elseif ($_POST['action'] === 'delete') {
                $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
                $result = $stmt->execute([$booking_id]);
                
                if ($result) {
                    header("Location: bookings.php");
                    exit;
                } else {
                    $error = "Failed to delete booking";
                }
            }
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
            error_log("Database error: " . $e->getMessage());
        }
    }
}

// Get booking details
try {
    $stmt = $conn->prepare("
        SELECT b.*, s.title as service_title, s.price as service_price,
               c.full_name as customer_name, p.full_name as provider_name
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users c ON b.customer_id = c.id
        JOIN users p ON s.provider_id = p.id
        WHERE b.id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        header("Location: bookings.php");
        exit;
    }
} catch(PDOException $e) {
    $error = "Error loading booking: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - Admin Dashboard</title>
    <link href="../assets/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
<?php include 'admin-navbar.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Booking Details</h1>
        <p class="text-xl text-blue-100">View and manage booking information</p>
    </div>
</section>

<!-- Booking Details -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <?php if (isset($error)): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">Booking Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Service Details</h3>
                        <p class="text-gray-600">Title: <?php echo htmlspecialchars($booking['service_title']); ?></p>
                        <p class="text-gray-600">Price: $<?php echo number_format($booking['service_price'], 2); ?></p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Customer Information</h3>
                        <p class="text-gray-600">Name: <?php echo htmlspecialchars($booking['customer_name']); ?></p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Provider Information</h3>
                        <p class="text-gray-600">Name: <?php echo htmlspecialchars($booking['provider_name']); ?></p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Booking Details</h3>
                        <p class="text-gray-600">Date: <?php echo date('F d, Y', strtotime($booking['booking_date'])); ?></p>
                        <p class="text-gray-600">Total Amount: $<?php echo number_format($booking['total_amount'], 2); ?></p>
                        <p class="text-gray-600">Payment Status: 
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                <?php echo match($booking['payment_status']) {
                                    'completed' => 'bg-green-100 text-green-800',
                                    'refunded' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800'
                                }; ?>">
                                <?php echo ucfirst($booking['payment_status']); ?>
                            </span>
                        </p>
                        <p class="text-gray-600">Payment Method: 
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                <?php echo ucfirst($booking['payment_method']); ?>
                            </span>
                        </p>
                    </div>
                </div>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="completed" <?php echo $booking['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Status</label>
                        <select name="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="pending" <?php echo $booking['payment_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo $booking['payment_status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="refunded" <?php echo $booking['payment_status'] === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                        </select>
                    </div>

                    <div class="flex justify-between">
                        <button type="submit" name="action" value="update" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Update Booking
                        </button>
                        <button type="submit" name="action" value="delete" 
                                class="bg-red-600 text-red-100 px-6 py-2 rounded-lg hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to delete this booking? This action cannot be undone.')">
                            Delete Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?> 