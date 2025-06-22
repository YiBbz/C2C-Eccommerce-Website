<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Pusher configuration
define('PUSHER_APP_ID', '1999470');
define('PUSHER_APP_KEY', '796daab0bc3a42d73b73');
define('PUSHER_APP_SECRET', '3f8042201acba6703d36');
define('PUSHER_APP_CLUSTER', 'mt1');

// Initialize Pusher
$pusher = new Pusher\Pusher(
    PUSHER_APP_KEY,
    PUSHER_APP_SECRET,
    PUSHER_APP_ID,
    [
        'cluster' => PUSHER_APP_CLUSTER,
        'useTLS' => true
    ]
); 