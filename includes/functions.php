<?php
// Service related functions
function getPopularServices($conn, $limit = 6) {
    $sql = "SELECT s.*, u.username as provider_name, 
            (SELECT AVG(rating) FROM reviews r WHERE r.reviewed_id = s.provider_id) as avg_rating,
            GROUP_CONCAT(si.image_path ORDER BY si.is_primary DESC, si.id ASC SEPARATOR '||') as service_images
            FROM services s
            JOIN users u ON s.provider_id = u.id
            LEFT JOIN service_images si ON s.id = si.service_id
            WHERE s.status = 'active'
            GROUP BY s.id
            ORDER BY avg_rating DESC, s.created_at DESC
            LIMIT :limit";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $services = $stmt->fetchAll();

    // Process the images for each service
    foreach ($services as &$service) {
        if (!empty($service['service_images'])) {
            $service['service_images'] = explode('||', $service['service_images']);
        } else if (!empty($service['image'])) {
            // Fallback to the old image column if no service_images exist
            $service['service_images'] = [$service['image']];
        } else {
            $service['service_images'] = [];
        }
    }

    return $services;
}

function getServicesByCategory($conn, $category, $limit = 12) {
    $sql = "SELECT s.*, u.username as provider_name,
            (SELECT AVG(rating) FROM reviews r WHERE r.reviewed_id = s.provider_id) as avg_rating
            FROM services s
            JOIN users u ON s.provider_id = u.id
            WHERE s.category = :category AND s.status = 'active'
            ORDER BY avg_rating DESC, s.created_at DESC
            LIMIT :limit";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function searchServices($conn, $query, $category = null, $min_price = null, $max_price = null) {
    $sql = "SELECT s.*, u.username as provider_name,
            (SELECT AVG(rating) FROM reviews r WHERE r.reviewed_id = s.provider_id) as avg_rating
            FROM services s
            JOIN users u ON s.provider_id = u.id
            WHERE s.status = 'active' AND (s.title LIKE :query OR s.description LIKE :query)";
    
    $params = [':query' => "%$query%"];
    
    if ($category) {
        $sql .= " AND s.category = :category";
        $params[':category'] = $category;
    }
    
    if ($min_price !== null) {
        $sql .= " AND s.price >= :min_price";
        $params[':min_price'] = $min_price;
    }
    
    if ($max_price !== null) {
        $sql .= " AND s.price <= :max_price";
        $params[':max_price'] = $max_price;
    }
    
    $sql .= " ORDER BY avg_rating DESC, s.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    return $stmt->fetchAll();
}

// Booking related functions
function createBooking($conn, $service_id, $customer_id, $provider_id, $booking_date, $total_amount, $payment_method) {
    $sql = "INSERT INTO bookings (service_id, customer_id, provider_id, booking_date, total_amount, payment_method)
            VALUES (:service_id, :customer_id, :provider_id, :booking_date, :total_amount, :payment_method)";
    
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':service_id' => $service_id,
        ':customer_id' => $customer_id,
        ':provider_id' => $provider_id,
        ':booking_date' => $booking_date,
        ':total_amount' => $total_amount,
        ':payment_method' => $payment_method
    ]);
}

// Review related functions
function addReview($conn, $booking_id, $reviewer_id, $reviewed_id, $rating, $comment) {
    $sql = "INSERT INTO reviews (booking_id, reviewer_id, reviewed_id, rating, comment)
            VALUES (:booking_id, :reviewer_id, :reviewed_id, :rating, :comment)";
    
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':booking_id' => $booking_id,
        ':reviewer_id' => $reviewer_id,
        ':reviewed_id' => $reviewed_id,
        ':rating' => $rating,
        ':comment' => $comment
    ]);
}

// Message related functions
function sendMessage($conn, $sender_id, $receiver_id, $message, $booking_id = null) {
    $sql = "INSERT INTO messages (sender_id, receiver_id, message, booking_id)
            VALUES (:sender_id, :receiver_id, :message, :booking_id)";
    
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':sender_id' => $sender_id,
        ':receiver_id' => $receiver_id,
        ':message' => $message,
        ':booking_id' => $booking_id
    ]);
}

function getMessages($conn, $user1_id, $user2_id, $booking_id = null) {
    $sql = "SELECT m.*, u.username as sender_name
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE ((m.sender_id = :user1_id AND m.receiver_id = :user2_id)
            OR (m.sender_id = :user2_id AND m.receiver_id = :user1_id))";
    
    $params = [
        ':user1_id' => $user1_id,
        ':user2_id' => $user2_id
    ];
    
    if ($booking_id) {
        $sql .= " AND m.booking_id = :booking_id";
        $params[':booking_id'] = $booking_id;
    }
    
    $sql .= " ORDER BY m.created_at ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Utility functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generatePagination($total_items, $items_per_page, $current_page) {
    $total_pages = ceil($total_items / $items_per_page);
    $pagination = [];
    
    if ($total_pages > 1) {
        $pagination['current_page'] = $current_page;
        $pagination['total_pages'] = $total_pages;
        $pagination['has_previous'] = $current_page > 1;
        $pagination['has_next'] = $current_page < $total_pages;
        $pagination['previous_page'] = $current_page - 1;
        $pagination['next_page'] = $current_page + 1;
    }
    
    return $pagination;
}

function formatPrice($price) {
    return number_format($price, 2);
}

function getTimeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return "just now";
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . " minute" . ($mins > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    } else {
        return date("M j, Y", $time);
    }
}

/**
 * Handle image upload for services and profiles
 * @param array $file The $_FILES array element
 * @param string $type Either 'service' or 'profile'
 * @param string $old_image Optional path to old image to delete
 * @return array ['success' => bool, 'path' => string, 'error' => string]
 */
function handleImageUpload($file, $type = 'service', $old_image = null) {
    // Validate file
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }

    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG and GIF are allowed'];
    }

    // Validate file size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'error' => 'File too large. Maximum size is 5MB'];
    }

    // Create upload directory if it doesn't exist
    $upload_dir = 'uploads/' . ($type === 'service' ? 'services' : 'profiles');
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . '/' . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Delete old image if exists
        if ($old_image && file_exists($old_image)) {
            unlink($old_image);
        }
        return ['success' => true, 'path' => $filepath];
    }

    return ['success' => false, 'error' => 'Failed to save file'];
}

function getCategories() {
    global $conn;
    try {
        $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching categories: " . $e->getMessage());
        return [];
    }
} 