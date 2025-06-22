<?php
// Ensure full error reporting for debugging ITN issues.
ini_set('display_errors', 0); // Don't display errors to PayFast
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/payment_notify_error.log'); // Log ITN errors to a specific file
error_reporting(E_ALL);

require_once 'config/database.php';
require_once 'config/payfast.php';

error_log("--- payment-notify.php ITN Received ---");
error_log("Raw POST data: " . print_r($_POST, true));

// Get the POST data from PayFast
$pfDataReceived = $_POST;
$pfPassphrase = defined('PAYFAST_PASSPHRASE') ? PAYFAST_PASSPHRASE : null;

// --- 1. Verify the Signature (Local Check - Optional but good practice) ---
$signatureValid = true; // Assume valid if not performing local check or if server validation is primary
if (isset($pfDataReceived['signature'])) {
    // Create a string from the received data (excluding the signature itself)
    $dataForSignature = [];
    foreach ($pfDataReceived as $key => $val) {
        if ($key !== 'signature') {
            $dataForSignature[$key] = $val;
        }
    }

    // PayFast might expect data to be sorted by key for signature generation.
    // It's generally safer to sort, though not always strictly enforced by PayFast
    // if all expected fields are present in the $data array during initial POST.
    // For ITN, it's best to match how PayFast sends it or how their examples show it.
    // If PayFast sends ITN data unsorted, then don't sort here.
    // If PayFast documentation for ITN signature implies sorting, then ksort($dataForSignature);

    $output = '';
    // Construct the query string in the same order as PayFast would
    // This usually means iterating over the received POST data in the order it was received,
    // or alphabetically if PayFast specifies that for ITN signature.
    // For now, let's iterate as received (excluding signature).
    foreach ($dataForSignature as $key => $val) {
        if ($val !== '' && $val !== null) { // PayFast generally expects empty values to be excluded
            $output .= $key . '=' . urlencode(trim($val)) . '&';
        }
    }
    $output = rtrim($output, '&'); // Remove the last '&'

    // Append passphrase if it's set
    if (!empty($pfPassphrase)) {
        $output .= '&passphrase=' . urlencode(trim($pfPassphrase));
    }
    $calculatedSignature = md5($output);

    if ($calculatedSignature !== $pfDataReceived['signature']) {
        $signatureValid = false;
        error_log("PayFast ITN Signature Mismatch (Local Check). Expected: " . $calculatedSignature . ", Received: " . $pfDataReceived['signature'] . ". String to hash: " . $output);
    } else {
        error_log("PayFast ITN Signature Matched (Local Check).");
    }
} else {
    $signatureValid = false; // No signature received, definitely an issue if you expect one.
    error_log("PayFast ITN Error: No signature received in POST data for local check.");
}

// --- 2. Server-to-Server Validation (Most Secure Method) ---
// Create the validation string by URL encoding the received POST data
$validationStr = http_build_query($pfDataReceived); // http_build_query automatically URL encodes
error_log("PayFast ITN Validation String to send: " . $validationStr);

$ch = curl_init();
// Set the URL to PayFast's validation server
curl_setopt($ch, CURLOPT_URL, PAYFAST_IPN_VALIDATION_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $validationStr);
curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Set a timeout for the request
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Verify PayFast's SSL certificate
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);    // Check the common name exists and matches the host name of the server

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

error_log("PayFast ITN Validation Response from PayFast server: " . $response);
error_log("PayFast ITN Validation HTTP Status Code: " . $httpStatusCode);

if ($curlError) {
    error_log("PayFast ITN cURL Error: " . $curlError);
    // Decide how to handle cURL errors, e.g., retry later or investigate
    header('HTTP/1.0 500 Internal Server Error'); // Or a more appropriate error
    exit('ITN cURL Error');
}

// Check the response from PayFast
if (trim($response) === 'VALID') {
    // Optionally, you can also check $signatureValid here if you want both local and server validation to pass.
    // For maximum security, relying on the server-to-server 'VALID' response is key.
    // if (trim($response) === 'VALID' && $signatureValid) {
    error_log("PayFast ITN VALID (Server-to-Server). Processing payment.");
    $conn = getDB();
    $payment_id = $pfDataReceived['m_payment_id'] ?? null; // This is your custom m_payment_id
    $booking_id = $pfDataReceived['custom_str1'] ?? null; // Your booking ID
    $pf_payment_status = $pfDataReceived['payment_status'] ?? null; // e.g., COMPLETE, FAILED, PENDING
    $amount_gross = $pfDataReceived['amount_gross'] ?? null;
    $amount_fee = $pfDataReceived['amount_fee'] ?? null;
    $amount_net = $pfDataReceived['amount_net'] ?? null;
    $payfast_transaction_id = $pfDataReceived['pf_payment_id'] ?? null; // PayFast's own transaction ID

    if ($payment_id && $booking_id && $pf_payment_status) {
        try {
            // Map PayFast status to your application's status
            $db_payment_status = 'pending_payfast'; // Default
            if ($pf_payment_status === 'COMPLETE') {
                $db_payment_status = 'paid';
            } elseif ($pf_payment_status === 'FAILED') {
                $db_payment_status = 'failed';
            }
            // Add more mappings if needed (e.g., 'CANCELLED')

            // Additional checks (optional but recommended):
            // 1. Check if booking_id exists and if its current payment_status is 'pending'
            // 2. Verify that the amount_gross matches the expected total_amount for the booking_id
            // 3. Check if this pf_payment_id (PayFast's transaction ID) has already been processed to prevent duplicates.

            // $stmt_check = $conn->prepare("SELECT payment_status, total_amount FROM bookings WHERE id = :booking_id AND payment_id = :m_payment_id");
            // $stmt_check->execute([':booking_id' => $booking_id, ':m_payment_id' => $payment_id]);
            // $existing_booking = $stmt_check->fetch(PDO::FETCH_ASSOC);

            // if (!$existing_booking) {
            //     error_log("Booking ID: $booking_id with m_payment_id: $payment_id not found. Ignoring ITN.");
            //     header('HTTP/1.0 200 OK'); exit('OK - Booking Not Found for m_payment_id');
            // }
            // if ($existing_booking['payment_status'] === 'paid' && $existing_booking['payfast_pf_payment_id'] === $payfast_transaction_id) {
            //     error_log("Booking ID: $booking_id already marked as paid with this PayFast transaction. Ignoring ITN.");
            //     header('HTTP/1.0 200 OK'); exit('OK - Already Processed');
            // }
            // if ((float)$existing_booking['total_amount'] != (float)$amount_gross) {
            //    error_log("Amount mismatch for Booking ID: $booking_id. Expected: {$existing_booking['total_amount']}, Received: {$amount_gross}");
            //    // Handle amount mismatch - could be fraud or error. You might choose not to update or flag for review.
            // }

            $stmt = $conn->prepare("
                UPDATE bookings
                SET payment_status = :payment_status,
                    payment_date = CASE WHEN :pf_payment_status = 'COMPLETE' THEN NOW() ELSE payment_date END,
                    payfast_pf_payment_id = :payfast_pf_payment_id
                WHERE id = :booking_id AND payment_id = :m_payment_id -- Use your m_payment_id to identify the booking
            ");
            $stmt->execute([
                ':payment_status' => $db_payment_status,
                ':pf_payment_status' => $pf_payment_status,
                ':payfast_pf_payment_id' => $payfast_transaction_id,
                ':booking_id' => $booking_id,
                ':m_payment_id' => $payment_id // This is your m_payment_id
            ]);
            $rowsAffected = $stmt->rowCount();
            error_log("Booking ID: $booking_id update attempt. Rows affected: $rowsAffected. Payment Status: $db_payment_status, PayFast Status: $pf_payment_status, PayFast TXN ID: $payfast_transaction_id");

            // Log the ITN details
            $stmt_log = $conn->prepare("INSERT INTO payment_logs (payment_id, booking_id, status, amount_gross, amount_fee, amount_net, raw_data, pf_payment_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_log->execute([
                $payment_id, // Your m_payment_id
                $booking_id,
                $pf_payment_status,
                $amount_gross,
                $amount_fee,
                $amount_net,
                json_encode($pfDataReceived),
                $payfast_transaction_id
            ]);

            header('HTTP/1.0 200 OK'); // Acknowledge receipt to PayFast
            exit('OK');
        } catch (PDOException $e) {
            error_log("PayFast ITN Database Error: " . $e->getMessage());
            // Don't send 200 OK if DB error, PayFast might retry
            header('HTTP/1.0 500 Internal Server Error');
            exit('ITN Database Error');
        }
    } else {
        error_log("PayFast ITN Error: Missing m_payment_id, custom_str1, or payment_status in VALID response data.");
        header('HTTP/1.0 400 Bad Request');
        exit('ITN Missing Data');
    }
} elseif (trim($response) === 'INVALID') {
    error_log("PayFast ITN INVALID (Server-to-Server). ITN data: " . print_r($pfDataReceived, true));
    // Potentially fraudulent or tampered ITN
    header('HTTP/1.0 400 Bad Request'); // Tell PayFast something is wrong, but they won't retry for 'INVALID'
    exit('ITN Invalid');
} else {
    // This case includes empty response or any other non 'VALID'/'INVALID' string
    error_log("PayFast ITN Unrecognized or Empty Response (Server-to-Server): '" . $response . "'. ITN data: " . print_r($pfDataReceived, true));
    // If the response is empty or unexpected, it might indicate a communication issue.
    // PayFast might retry if they don't get a 200 OK.
    header('HTTP/1.0 500 Internal Server Error'); // Indicate an error on your side
    exit('ITN Unrecognized Response');
}

// Fallback if execution reaches here (should not happen with proper exits)
error_log("PayFast ITN processing reached end of script unexpectedly.");
header('HTTP/1.0 500 Internal Server Error');
exit('ITN Processing Error');
