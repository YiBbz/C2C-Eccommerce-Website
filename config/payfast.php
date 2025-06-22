<?php
// PayFast Sandbox Configuration
define('PAYFAST_MERCHANT_ID', '10039536'); // Sandbox merchant ID
define('PAYFAST_MERCHANT_KEY', 's4arohgrd1ffl'); // Sandbox merchant key
define('PAYFAST_PASSPHRASE', 'Rammala65123'); // Sandbox passphrase
define('PAYFAST_TEST_MODE', true); // Set to false for production

// Base URL - Replace with your ngrok URL
define('BASE_URL', 'https://7998-102-66-138-148.ngrok-free.app'); // Replace with your actual ngrok URL

// PayFast URLs
define('PAYFAST_PROCESS_URL', PAYFAST_TEST_MODE ? 
    'https://sandbox.payfast.co.za/eng/process' : 
    'https://www.payfast.co.za/eng/process');

// Your site URLs for PayFast callbacks
define('PAYFAST_RETURN_URL', BASE_URL . '/customer-messages.php?payment=success');
define('PAYFAST_CANCEL_URL', BASE_URL . '/customer-messages.php?payment=cancelled');
define('PAYFAST_NOTIFY_URL', BASE_URL . '/payment-notify.php');

// PayFast IPN Validation
define('PAYFAST_IPN_VALIDATION_URL', PAYFAST_TEST_MODE ?
    'https://sandbox.payfast.co.za/eng/query/validate' :
    'https://www.payfast.co.za/eng/query/validate');

// Enable error logging for PayFast
error_log("PayFast Configuration Loaded - Base URL: " . BASE_URL);
error_log("PayFast Configuration Loaded - Process URL: " . PAYFAST_PROCESS_URL);
error_log("PayFast Configuration Loaded - Test Mode: " . (PAYFAST_TEST_MODE ? 'Yes' : 'No')); 