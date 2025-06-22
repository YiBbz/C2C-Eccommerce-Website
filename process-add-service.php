<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'config/database.php';

// Check if user is logged in and is a service provider
if (!isLoggedIn() || !isServiceProvider()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $category = $_POST['category'] ?? '';

    try {
        // Validate input
        if (empty($title) || empty($description) || empty($price) || empty($category)) {
            throw new Exception('All fields are required');
        }

        if (!is_numeric($price) || $price <= 0) {
            throw new Exception('Price must be a positive number');
        }

        // Start transaction
        $conn = getDB();
        $conn->beginTransaction();

        // Handle image uploads
        $image_paths = [];
        if (isset($_FILES['service_images']) && is_array($_FILES['service_images']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            foreach ($_FILES['service_images']['name'] as $index => $filename) {
                if ($_FILES['service_images']['error'][$index] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    if (!in_array($ext, $allowed)) {
                        throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowed));
                    }

                    if ($_FILES['service_images']['size'][$index] > $max_size) {
                        throw new Exception('File size exceeds 5MB');
                    }

                    $upload_dir = 'uploads/services/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $new_filename = uniqid() . '.' . $ext;
                    $destination = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['service_images']['tmp_name'][$index], $destination)) {
                        $image_paths[] = $destination;
                    }
                }
            }
        }

        // Insert service
        $stmt = $conn->prepare("
            INSERT INTO services (provider_id, title, description, price, category, status)
            VALUES (?, ?, ?, ?, ?, 'active')
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $description,
            $price,
            $category
        ]);

        $service_id = $conn->lastInsertId();

        // Insert service images
        if (!empty($image_paths)) {
            $stmt = $conn->prepare("
                INSERT INTO service_images (service_id, image_path, is_primary)
                VALUES (?, ?, ?)
            ");
            
            foreach ($image_paths as $index => $path) {
                $stmt->execute([
                    $service_id,
                    $path,
                    $index === 0 ? 1 : 0 // First image is primary
                ]);
            }
        }

        $conn->commit();
        $_SESSION['success'] = 'Service added successfully';
        header('Location: dashboard.php');
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = $e->getMessage();
        header('Location: add-service.php');
        exit;
    }
} else {
    header('Location: add-service.php');
    exit;
} 