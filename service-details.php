<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // Good for development, turn off for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log'); // Explicitly set log path for this script

require_once 'includes/auth.php';
error_log("[service-details.php] Script execution started. auth.php included."); // Test log
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get the database connection
$conn = getDB();

// Get service ID from URL
$service_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get service details
$stmt = $conn->prepare("SELECT s.*, u.username, u.full_name, u.email 
                       FROM services s 
                       JOIN users u ON s.provider_id = u.id 
                       WHERE s.id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header('Location: services.php');
    exit();
}

// Get service reviews
$stmt = $conn->prepare("SELECT r.*, u.username, u.full_name 
                       FROM reviews r 
                       JOIN users u ON r.reviewer_id = u.id 
                       WHERE r.service_id = ? 
                       ORDER BY r.created_at DESC");
$stmt->execute([$service_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate average rating
$avg_rating = 0;
if (count($reviews) > 0) {
    $total_rating = array_sum(array_column($reviews, 'rating'));
    $avg_rating = $total_rating / count($reviews);
}
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-grey-900 text-white py-20">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <div class="lg:w-1/2">
                <h1 class="text-5xl font-bold mb-6"><?php echo htmlspecialchars($service['title']); ?></h1>
                <p class="text-xl text-purple-200"><?php echo htmlspecialchars($service['category']); ?></p>
            </div>
            <div class="lg:w-1/2">
                <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="rounded-lg shadow-2xl">
            </div>
        </div>
    </div>
</section>

<!-- Service Details Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-gray-800 rounded-xl p-8 shadow-lg mb-8">
                    <h2 class="text-3xl font-bold mb-6 text-white">Service Description</h2>
                    <p class="text-gray-300 mb-8"><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-xl font-semibold mb-4 text-white">Service Details</h3>
                            <ul class="space-y-3">
                                <li class="flex items-center">
                                    <i class="fas fa-tag text-purple-500 mr-3"></i>
                                    <span class="text-gray-300">Category: <?php echo htmlspecialchars($service['category']); ?></span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-clock text-purple-500 mr-3"></i>
                                    <span class="text-gray-300">Duration: <?php echo htmlspecialchars($service['duration'] ?? 'Not specified'); ?> hours</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-star text-purple-500 mr-3"></i>
                                    <span class="text-gray-300">Rating: <?php echo number_format($avg_rating, 1); ?> (<?php echo count($reviews); ?> reviews)</span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-4 text-white">Service Provider</h3>
                            <ul class="space-y-3">
                                <li class="flex items-center">
                                    <i class="fas fa-user text-purple-500 mr-3"></i>
                                    <span class="text-gray-300"><?php echo htmlspecialchars($service['full_name']); ?></span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-envelope text-purple-500 mr-3"></i>
                                    <span class="text-gray-300"><?php echo htmlspecialchars($service['email']); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="bg-gray-800 rounded-xl p-8 shadow-lg">
                    <h2 class="text-3xl font-bold mb-6 text-white">Customer Reviews</h2>
                    <?php if (empty($reviews)): ?>
                        <p class="text-gray-300">No reviews yet. Be the first to review this service!</p>
                    <?php else: ?>
                        <div class="space-y-8">
                            <?php foreach ($reviews as $review): ?>
                                <div class="border-b border-gray-700 pb-8 last:border-0 last:pb-0">
                                    <div class="flex items-center mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-white"><?php echo htmlspecialchars($review['full_name']); ?></h4>
                                            <p class="text-gray-300"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></p>
                                        </div>
                                        <div class="flex items-center">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-600'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="text-gray-300"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isLoggedIn()): ?>
                        <div class="mt-8">
                            <h3 class="text-xl font-semibold mb-4 text-white">Write a Review</h3>
                            <?php
                            $current_user_id_for_review = $_SESSION['user_id'];
                            $current_service_id_for_review = $service_id;

                            error_log("[service-details.php] Attempting to find completed bookings for review. Customer ID: " . $current_user_id_for_review . ", Service ID: " . $current_service_id_for_review);

                            // Check if user has any bookings for this service that are accepted and marked as completed by the provider
                            $stmt_review_check = $conn->prepare("SELECT id, status, completion_status FROM bookings WHERE customer_id = ? AND service_id = ? AND status = 'accepted' AND completion_status = 'completed'");
                            $stmt_review_check->execute([$current_user_id_for_review, $current_service_id_for_review]);
                            $completed_bookings = $stmt_review_check->fetchAll(PDO::FETCH_ASSOC);

                            error_log("[service-details.php] Found " . count($completed_bookings) . " bookings matching review criteria.");
                            if (!empty($completed_bookings)) {
                                error_log("[service-details.php] Details of first found booking for review: " . print_r($completed_bookings[0], true));
                            }
                            
                            if (empty($completed_bookings)): ?>
                                <p class="text-gray-300">You can only review services for bookings that are 'accepted' and marked as 'completed' by the provider. Please check the booking status.</p>
                                <?php error_log("[service-details.php] No eligible bookings found for review for Customer ID: " . $current_user_id_for_review . ", Service ID: " . $current_service_id_for_review); ?>
                            <?php else: ?>
                                <form action="process-review.php" method="POST" class="space-y-4">
                                    <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
                                    <input type="hidden" name="reviewed_id" value="<?php echo $service['provider_id']; ?>">
                                    <input type="hidden" name="booking_id" value="<?php echo $completed_bookings[0]['id']; ?>">
                                    <div>
                                        <label for="rating" class="block text-gray-300 font-semibold mb-2">Rating</label>
                                        <div class="flex items-center space-x-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <input type="radio" id="rating<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" class="hidden" <?php echo $i === 5 ? 'checked' : ''; ?> required>
                                                <label for="rating<?php echo $i; ?>" class="cursor-pointer">
                                                    <i class="fas fa-star text-2xl text-gray-600 hover:text-yellow-400 transition duration-300"></i>
                                                </label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="comment" class="block text-gray-300 font-semibold mb-2">Your Review</label>
                                        <textarea id="comment" name="comment" rows="4" required class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white"></textarea>
                                    </div>
                                    <button type="submit" class="bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                                        Submit Review
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="mt-8 text-center">
                            <p class="text-gray-300">Please <a href="login.php" class="text-purple-400 hover:text-purple-300 font-semibold">sign in</a> to write a review.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-xl p-8 shadow-lg sticky top-8">
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-bold text-purple-500 mb-2">R<?php echo number_format($service['price'], 2); ?></h3>
                        <p class="text-gray-300">per service</p>
                    </div>

                    <?php if (isLoggedIn()): ?>
                        <form action="redirect-messages.php" method="GET" class="space-y-6">
                            <input type="hidden" name="provider" value="<?php echo $service['provider_id']; ?>">
                            <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
                            <div>
                                <label for="date" class="block text-gray-300 font-semibold mb-2">Preferred Date</label>
                                <input type="date" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>"
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                            </div>
                            <div>
                                <label for="time" class="block text-gray-300 font-semibold mb-2">Preferred Time</label>
                                <input type="time" id="time" name="time" required
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                            </div>
                            <div>
                                <label for="payment_method" class="block text-gray-300 font-semibold mb-2">Payment Method</label>
                                <select id="payment_method" name="payment_method" required
                                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                                    <option value="cash">Cash</option>
                                    <option value="online">Online Payment</option>
                                </select>
                            </div>
                            <div>
                                <label for="notes" class="block text-gray-300 font-semibold mb-2">Additional Notes</label>
                                <textarea id="notes" name="notes" rows="4" 
                                          class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white"
                                          placeholder="Any specific requirements or questions?"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                                Contact Provider
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="text-center">
                            <p class="text-gray-300 mb-6">Please sign in to book this service.</p>
                            <a href="login.php" class="block w-full bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                                Sign In to Book
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="mt-8 pt-8 border-t border-gray-700">
                        <h4 class="text-lg font-semibold mb-4 text-white">Share this service</h4>
                        <div class="flex justify-center space-x-4">
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <i class="fab fa-facebook-f text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <i class="fab fa-twitter text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <i class="fab fa-linkedin-in text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <i class="fas fa-envelope text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reviewForm = document.querySelector('form[action="process-review.php"]');
    if (reviewForm) {
        const ratingStarsContainer = reviewForm.querySelector('.flex.items-center.space-x-2');
        if (ratingStarsContainer) {
            const stars = ratingStarsContainer.querySelectorAll('label i.fa-star');
            const radios = ratingStarsContainer.querySelectorAll('input[type="radio"][name="rating"]');

            function updateStarDisplay(currentRating) {
                stars.forEach((s, idx) => {
                    if (idx < currentRating) {
                        s.classList.remove('text-gray-600');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-600');
                    }
                });
            }

            function highlightStarsOnHover(hoverRating) {
                stars.forEach((s, idx) => {
                    if (idx < hoverRating) {
                        s.classList.remove('text-gray-600');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-600');
                    }
                });
            }

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    updateStarDisplay(this.value);
                });
            });

            stars.forEach((starIcon, index) => {
                const correspondingRadio = radios[index];
                starIcon.parentElement.addEventListener('mouseover', () => highlightStarsOnHover(correspondingRadio.value));
                starIcon.parentElement.addEventListener('mouseout', () => {
                    const checkedRadio = reviewForm.querySelector('input[type="radio"][name="rating"]:checked');
                    updateStarDisplay(checkedRadio ? checkedRadio.value : 0);
                });
            });

            // Initial display based on any pre-checked radio
            const initiallyCheckedRadio = reviewForm.querySelector('input[type="radio"][name="rating"]:checked');
            if (initiallyCheckedRadio) {
                updateStarDisplay(initiallyCheckedRadio.value);
            }
        }
    }
});
</script>
<script src="assets/js/main.js"></script>
</body>
</html> 