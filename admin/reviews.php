<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Check if the user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$conn = getDB();

// Get all reviews with user and service details
try {
    $stmt = $conn->query("
        SELECT r.*, s.title as service_title, 
               u_reviewer.full_name as reviewer_name,
               u_reviewed.full_name as reviewed_name
        FROM reviews r
        JOIN services s ON r.service_id = s.id
        JOIN users u_reviewer ON r.reviewer_id = u_reviewer.id
        JOIN users u_reviewed ON r.reviewed_id = u_reviewed.id
        ORDER BY r.created_at DESC
    ");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error loading reviews: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Admin Dashboard</title>
    <link href="../assets/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
<?php include 'admin-navbar.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Manage Reviews</h1>
        <p class="text-xl text-blue-100">View and manage all service reviews</p>
    </div>
</section>

<!-- Reviews List -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">All Reviews</h2>
            </div>
            <div class="p-6">
                <?php if (empty($reviews)): ?>
                    <p class="text-gray-600 text-center py-4">No reviews found</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewed</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($reviews as $review): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($review['service_title']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($review['reviewer_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($review['reviewed_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="review-details.php?id=<?php echo $review['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="review-details.php?id=<?php echo $review['id']; ?>&action=edit" 
                                               class="text-yellow-600 hover:text-yellow-900 mr-3">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="review-details.php?id=<?php echo $review['id']; ?>&action=delete" 
                                               class="text-red-600 hover:text-red-900"
                                               onclick="return confirm('Are you sure you want to delete this review?')">
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