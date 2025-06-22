<?php
ini_set('display_errors', 1); // Temporarily display errors on screen
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1); // Ensure logging is on
ini_set('error_log', __DIR__ . '/process_payment_specific_error.log'); // Force a log file in the same directory

error_log('--- process-payment.php SCRIPT EXECUTION STARTED ---');
require_once 'includes/auth.php';
error_log('--- process-payment.php: auth.php included ---');
require_once 'config/database.php';
error_log('--- process-payment.php: database.php included ---');
require_once 'config/payfast.php';
error_log('--- process-payment.php: payfast.php included ---');

if (!isLoggedIn()) {
    error_log("Process Payment Error - User not logged in. Redirecting to login.php");
    header('Location: login.php');
    exit;
}

$conn = getDB();
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

error_log("Process Payment Debug - Booking ID from GET: " . $booking_id);
error_log("Process Payment Debug - User ID from Session: " . ($_SESSION['user_id'] ?? 'NOT SET'));

if (!$booking_id) {
    error_log("Process Payment Error - No booking ID provided. Redirecting to customer-messages.php");
    $_SESSION['error'] = "No booking ID provided.";
    header('Location: customer-messages.php');
    exit;
}

try {
    // Get booking details
    $stmt = $conn->prepare("
        SELECT b.*, s.title as service_title,
               cust.full_name as customer_name, cust.email as customer_email,
               prov.full_name as provider_name
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users cust ON b.customer_id = cust.id
        JOIN users prov ON b.provider_id = prov.id
        WHERE b.id = :booking_id AND b.customer_id = :customer_id AND b.payment_method = 'online'
    ");
    $stmt->execute([':booking_id' => $booking_id, ':customer_id' => $_SESSION['user_id']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    error_log("Process Payment Debug - Booking Data from DB: " . print_r($booking, true));

    if (!$booking) {
        error_log("Process Payment Error - Invalid booking, user mismatch, or not an 'online' payment method for booking ID: " . $booking_id);
        throw new Exception('Invalid booking, or it is not an online payment method.');
    }

    if ($booking['payment_status'] !== 'pending') {
        error_log("Process Payment Error - Payment status is not 'pending'. Current status: " . $booking['payment_status'] . " for booking ID: " . $booking_id);
        throw new Exception('Payment for this booking has already been processed or is not pending.');
    }

    // Customer name handling
    $customer_name_parts = explode(' ', trim($booking['customer_name']), 2);
    $name_first = $customer_name_parts[0];
    $name_last = $customer_name_parts[1] ?? '';

    // Generate unique payment ID
    $payment_id = 'PF_' . time() . '_' . $booking_id;
    error_log("Process Payment Debug - Generated m_payment_id: " . $payment_id);

    // Prepare payment data array
    $data = array(
        // Merchant details
        'merchant_id' => PAYFAST_MERCHANT_ID,
        'merchant_key' => PAYFAST_MERCHANT_KEY,
        'return_url' => PAYFAST_RETURN_URL,
        'cancel_url' => PAYFAST_CANCEL_URL,
        'notify_url' => PAYFAST_NOTIFY_URL,

        // Buyer details
        'name_first' => $name_first,
        'name_last' => $name_last,
        'email_address' => $booking['customer_email'],

        // Transaction details
        'm_payment_id' => $payment_id, // Your unique payment ID
        'amount' => number_format($booking['total_amount'], 2, '.', ''),
        'item_name' => $booking['service_title'],
        // 'item_description' => 'Optional: More details about ' . $booking['service_title'],

        // Custom parameters
        'custom_str1' => (string)$booking_id,       // Example: Booking ID
        'custom_str2' => (string)$_SESSION['user_id'] // Example: Customer User ID
    );

    error_log("Process Payment Debug - Payment Data (before signature sorting): " . print_r($data, true));

    // For signature generation, PayFast examples sometimes show data sorted by key.
    // While not always strictly required if all fields are present, it ensures consistency.
    // ksort($data); // Optional: Sort data by key for consistent signature string generation
    // error_log("Process Payment Debug - Payment Data (after signature sorting): " . print_r($data, true));


    // Generate PayFast signature string
    $output = '';
    foreach ($data as $key => $val) {
        if ($val !== '' && $val !== null) { // PayFast generally expects empty values to be excluded
            $output .= $key . '=' . urlencode(trim($val)) . '&';
        }
    }
    // Remove the last '&'
    $output = rtrim($output, '&');

    // Append passphrase if it's set
    $passphrase = defined('PAYFAST_PASSPHRASE') ? PAYFAST_PASSPHRASE : null;
    if (!empty($passphrase)) {
        $output .= '&passphrase=' . urlencode(trim($passphrase)); // Ensure passphrase is trimmed before urlencoding
    }

    $data['signature'] = md5($output);
    error_log("Process Payment Debug - Generated Signature: " . $data['signature']);
    error_log("Process Payment Debug - String to Hash (for signature): " . $output);


    // Update booking with the generated payment_id (m_payment_id)
    $stmt_update_booking = $conn->prepare("UPDATE bookings SET payment_id = :payment_id WHERE id = :booking_id");
    $stmt_update_booking->execute([':payment_id' => $payment_id, ':booking_id' => $booking_id]);
    error_log("Process Payment Debug - Booking ID " . $booking_id . " updated with payment_id " . $payment_id);

    error_log("Process Payment Debug - PayFast Process URL: " . PAYFAST_PROCESS_URL);
    error_log("Process Payment Debug - About to output HTML for PayFast form submission.");

    // Build the HTML form and auto-submit to PayFast
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Redirecting to PayFast...</title>
        <style>
            body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f3f4f6; }
            .loading { text-align: center; padding: 2rem; background: white; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 1rem; }
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        </style>
    </head>
    <body>
        <div class="loading">
            <div class="spinner"></div>
            <p>Redirecting to secure payment gateway...</p>
        </div>
        <form action="<?php echo htmlspecialchars(PAYFAST_PROCESS_URL); ?>" method="post" id="payfast-form">
            <?php foreach ($data as $key => $value): ?>
                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
            <?php endforeach; ?>
        </form>
        <script>
            setTimeout(function() {
                console.log('Submitting PayFast form with data:', <?php echo json_encode($data); ?>);
                document.getElementById('payfast-form').submit();
            }, 1000);
        </script>
    </body>
    </html>
    <?php
    exit; // Ensure no further script execution

} catch (PDOException $e) {
    error_log("Process Payment Database Error: " . $e->getMessage() . " for booking ID: " . $booking_id);
    $_SESSION['error'] = "A database error occurred. Please try again later.";
    header('Location: customer-messages.php');
    exit;
} catch (Exception $e) {
    error_log("Process Payment General Error: " . $e->getMessage() . " for booking ID: " . $booking_id);
    $_SESSION['error'] = $e->getMessage();
    header('Location: customer-messages.php');
    exit;
}
