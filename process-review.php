<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log'); // Log errors to php_error.log in the current directory (demo2)

require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

error_log("process-review.php accessed");

// Ensure the user is logged in
if (!isLoggedIn()) {
    $_SESSION['error_message'] = 'You must be logged in to submit a review.';
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
     error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    header('Location: index.php'); // Redirect to homepage or an error page
    exit();
}

// Log received POST data
error_log("Received POST data: " . print_r($_POST, true));

$reviewer_id = $user_id; // Reviewer is the logged-in user
$service_id = $_POST['service_id'] ?? null;
$reviewed_id = $_POST['reviewed_id'] ?? null; // The provider's user ID
$booking_id = $_POST['booking_id'] ?? null; // Get the booking ID
$rating = $_POST['rating'] ?? null;
$comment = $_POST['comment'] ?? '';

// Basic validation (add more as needed)
if (empty($service_id) || empty($reviewed_id) || empty($booking_id) || $rating === null || $rating === '') {
    $errorMessage = 'Missing required review information (service_id, reviewed_id, booking_id, or rating).';
    error_log("Validation failed: " . $errorMessage . ", POST data: " . print_r($_POST, true));
    $_SESSION['error_message'] = $errorMessage;
    // Redirect back to the service details page
    header('Location: service-details.php?id=' . $service_id);
    exit();
}

// Validate rating is a number and within a valid range (e.g., 1-5)
if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
    $errorMessage = 'Invalid rating value: ' . htmlspecialchars($rating);
    error_log("Validation failed: " . $errorMessage);
    $_SESSION['error_message'] = $errorMessage;
    header('Location: service-details.php?id=' . $service_id);
    exit();
}

try {
    $conn = getDB();
    error_log("Database connection obtained.");

    // Check if the user has already reviewed this specific booking_id
    $stmt_already_reviewed = $conn->prepare("SELECT id FROM reviews WHERE booking_id = ? AND reviewer_id = ?");
    error_log("Checking for existing review with: booking_id=$booking_id, reviewer_id=$reviewer_id");
    $stmt_already_reviewed->execute([$booking_id, $reviewer_id]);
    if ($stmt_already_reviewed->rowCount() > 0) {
        $errorMessage = 'You have already submitted a review for this booking (Booking ID: ' . htmlspecialchars($booking_id) . ').';
        error_log("Validation failed (already reviewed): " . $errorMessage);
        $_SESSION['error_message'] = $errorMessage;
        header('Location: service-details.php?id=' . $service_id);
        exit();
    }

    // Check if the user has an accepted and completed booking for this service before allowing review
    $stmt_booking = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND customer_id = ? AND service_id = ? AND status = 'accepted' AND completion_status = 'completed'");
    error_log("Executing booking eligibility check with: booking_id=$booking_id, reviewer_id=$reviewer_id, service_id=$service_id");
    $stmt_booking->execute([$booking_id, $reviewer_id, $service_id]);
    
    if ($stmt_booking->rowCount() == 0) {
        $errorMessage = "Review eligibility check failed. No matching 'accepted' and 'completed' booking found for Booking ID: " . htmlspecialchars($booking_id) . ", Customer ID: " . htmlspecialchars($reviewer_id) . ", Service ID: " . htmlspecialchars($service_id) . ". Please ensure the booking is fully completed by the provider.";
        error_log("Validation failed (booking eligibility): " . $errorMessage);
        $_SESSION['error_message'] = $errorMessage;
        header('Location: service-details.php?id=' . $service_id);
        exit();
    }

    error_log("Attempting to insert review with data: booking_id=" . $booking_id . ", service_id=" . $service_id . ", reviewer_id=" . $reviewer_id . ", reviewed_id=" . $reviewed_id . ", rating=" . $rating . ", comment=" . $comment);

    $stmt = $conn->prepare("INSERT INTO reviews (booking_id, service_id, reviewer_id, reviewed_id, rating, comment) VALUES (?, ?, ?, ?, ?, ?)");
    
    // Check execute result
    $execute_result = $stmt->execute([$booking_id, $service_id, $reviewer_id, $reviewed_id, $rating, $comment]);

    if ($execute_result) {
        // Review successful
        $lastInsertId = $conn->lastInsertId();
        error_log("Review inserted successfully. Review ID: " . $lastInsertId);
        $_SESSION['success_message'] = 'Review submitted successfully!';
        // Redirect back to the service details page
        header('Location: service-details.php?id=' . $service_id);
        exit();
    } else {
        // Review failed
        $errorInfo = $stmt->errorInfo();
        error_log("Review insertion failed. Error Info: " . print_r($errorInfo, true));
        $_SESSION['error_message'] = 'Failed to submit review. Please try again.';
        header('Location: service-details.php?id=' . $service_id);
        exit();
    }

} catch (PDOException $e) {
    // Log the error and set a user-friendly message
    error_log("PDOException in process-review.php: " . $e->getMessage());
    $_SESSION['error_message'] = 'An error occurred while processing your review. Please try again later.';
    header('Location: service-details.php?id=' . $service_id);
    exit();
}

// Redirect if somehow execution reaches here
error_log("Execution reached end of script without redirect.");
header('Location: index.php');
exit();

?>
