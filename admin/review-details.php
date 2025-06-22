<?php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin(); // Ensure only admins can access this page

$conn = getDB();
$review_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$review = null;
$error_message = '';
$success_message = '';

if (!$review_id) {
    $_SESSION['error_message'] = "No review ID specified.";
    header('Location: index.php'); // Or an admin reviews list page
    exit;
}

// Fetch review details
try {
    $stmt = $conn->prepare("
        SELECT r.*, 
               s.title as service_title,
               u_reviewer.username as reviewer_username,
               u_provider.username as provider_username
        FROM reviews r
        LEFT JOIN services s ON r.service_id = s.id
        LEFT JOIN users u_reviewer ON r.reviewer_id = u_reviewer.id
        LEFT JOIN users u_provider ON r.reviewed_id = u_provider.id
        WHERE r.id = ?
    ");
    $stmt->execute([$review_id]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$review) {
        $_SESSION['error_message'] = "Review not found.";
        header('Location: index.php'); // Or an admin reviews list page
        exit;
    }
} catch (PDOException $e) {
    error_log("Error fetching review details: " . $e->getMessage());
    $_SESSION['error_message'] = "Error fetching review details.";
    header('Location: index.php');
    exit;
}

// Handle Edit Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_review'])) {
    $new_rating = isset($_POST['rating']) ? (int)$_POST['rating'] : $review['rating'];
    $new_comment = isset($_POST['comment']) ? trim($_POST['comment']) : $review['comment'];

    if ($new_rating < 1 || $new_rating > 5) {
        $error_message = "Rating must be between 1 and 5.";
    } else {
        try {
            $stmt_update = $conn->prepare("UPDATE reviews SET rating = ?, comment = ?, updated_at = NOW() WHERE id = ?");
            if ($stmt_update->execute([$new_rating, $new_comment, $review_id])) {
                $success_message = "Review updated successfully.";
                // Refresh review data
                $stmt->execute([$review_id]);
                $review = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error_message = "Failed to update review.";
            }
        } catch (PDOException $e) {
            error_log("Error updating review: " . $e->getMessage());
            $error_message = "An error occurred while updating the review.";
        }
    }
}

// Handle Delete Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review'])) {
    try {
        $stmt_delete = $conn->prepare("DELETE FROM reviews WHERE id = ?");
        if ($stmt_delete->execute([$review_id])) {
            $_SESSION['success_message'] = "Review deleted successfully.";
            header('Location: index.php'); // Redirect to admin dashboard or a reviews list page
            exit;
        } else {
            $error_message = "Failed to delete review.";
        }
    } catch (PDOException $e) {
        error_log("Error deleting review: " . $e->getMessage());
        $error_message = "An error occurred while deleting the review.";
    }
}

include 'admin-navbar.php'; // Using main site header
?>

<!-- Hero Section -->
<section class="bg-grey-900 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Admin - Review Details</h1>
        <p class="text-xl text-purple-200">Manage review ID: <?php echo htmlspecialchars($review_id); ?></p>
    </div>
</section>

<!-- Review Details & Management Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">

            <?php if ($error_message): ?>
                <div class="bg-red-900/50 border border-red-800 text-red-200 px-4 py-3 rounded-lg mb-6">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="bg-green-900/50 border border-green-800 text-green-200 px-4 py-3 rounded-lg mb-6">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($review): ?>
                <!-- Review Information -->
                <div class="bg-gray-800 rounded-xl shadow-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold text-white mb-6">Review Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-300">
                        <p><strong>Review ID:</strong> <?php echo htmlspecialchars($review['id']); ?></p>
                        <p><strong>Service:</strong> <?php echo htmlspecialchars($review['service_title'] ?? 'N/A'); ?></p>
                        <p><strong>Reviewer:</strong> <?php echo htmlspecialchars($review['reviewer_username'] ?? 'N/A'); ?> (ID: <?php echo htmlspecialchars($review['reviewer_id']); ?>)</p>
                        <p><strong>Provider:</strong> <?php echo htmlspecialchars($review['provider_username'] ?? 'N/A'); ?> (ID: <?php echo htmlspecialchars($review['reviewed_id']); ?>)</p>
                        <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($review['booking_id']); ?></p>
                        <p><strong>Original Rating:</strong> <?php echo htmlspecialchars($review['rating']); ?> / 5</p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($review['created_at']))); ?></p>
                        <p><strong>Updated At:</strong> <?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($review['updated_at']))); ?></p>
                        <div class="md:col-span-2">
                            <strong>Original Comment:</strong>
                            <p class="mt-1 p-3 bg-gray-700 rounded-md"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Edit Review Form -->
                <div class="bg-gray-800 rounded-xl shadow-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold text-white mb-6">Edit Review</h2>
                    <form action="review-details.php?id=<?php echo $review_id; ?>" method="POST" class="space-y-6">
                        <div>
                            <label for="rating" class="block text-gray-300 font-semibold mb-2">Rating (1-5)</label>
                            <input type="number" id="rating" name="rating" min="1" max="5" required
                                   value="<?php echo htmlspecialchars($review['rating']); ?>"
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                        </div>
                        <div>
                            <label for="comment" class="block text-gray-300 font-semibold mb-2">Comment</label>
                            <textarea id="comment" name="comment" rows="5" required
                                      class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white"><?php echo htmlspecialchars($review['comment']); ?></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" name="edit_review" class="bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Delete Review Section -->
                <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-white mb-6">Delete Review</h2>
                    <p class="text-gray-300 mb-4">
                        Warning: Deleting this review is permanent and cannot be undone.
                    </p>
                    <form action="review-details.php?id=<?php echo $review_id; ?>" method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete this review? This action is irreversible.');">
                        <div class="flex justify-end">
                            <button type="submit" name="delete_review" class="bg-red-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition duration-300">
                                Delete Review Permanently
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="bg-gray-800 rounded-xl shadow-lg p-8 text-center">
                    <p class="text-xl text-red-400">Review not found or could not be loaded.</p>
                    <a href="index.php" class="mt-4 inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition duration-300">
                        Back to Admin Dashboard
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
include '../includes/footer.php'; // Using main site footer
?>

<script>
// Any specific JavaScript for this admin page can go here.
// For example, enhancing the delete confirmation if needed.
</script>

</body>
</html>
