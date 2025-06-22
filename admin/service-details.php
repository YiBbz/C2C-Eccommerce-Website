<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Check if the user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$conn = getDB();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get service ID from URL
$service_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'update') {
                // Log the update attempt
                error_log("Attempting to update service ID: " . $service_id);
                
                $stmt = $conn->prepare("
                    UPDATE services 
                    SET title = ?, description = ?, price = ?, category = ?, status = ?
                    WHERE id = ?
                ");
                
                $params = [
                    $_POST['title'],
                    $_POST['description'],
                    $_POST['price'],
                    $_POST['category'],
                    $_POST['status'],
                    $service_id
                ];
                
                // Log the parameters
                error_log("Update parameters: " . print_r($params, true));
                
                $result = $stmt->execute($params);
                
                if ($result) {
                    $success = "Service updated successfully!";
                    error_log("Service update successful");
                } else {
                    $error = "Failed to update service";
                    error_log("Service update failed");
                }
            } elseif ($_POST['action'] === 'delete') {
                $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
                $result = $stmt->execute([$service_id]);
                
                if ($result) {
                    header("Location: services.php");
                    exit;
                } else {
                    $error = "Failed to delete service";
                }
            }
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
            error_log("Database error: " . $e->getMessage());
        }
    }
}

// Get service details
try {
    $stmt = $conn->prepare("
        SELECT s.*, u.full_name as provider_name, c.name as category_name
        FROM services s
        JOIN users u ON s.provider_id = u.id
        LEFT JOIN categories c ON s.category = c.name
        WHERE s.id = ?
    ");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        header("Location: services.php");
        exit;
    }

    // Get all categories for the dropdown
    $stmt = $conn->query("SELECT name FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) {
    $error = "Error loading service: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Details - Admin Dashboard</title>
    <link href="../assets/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
<?php include 'admin-navbar.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Service Details</h1>
        <p class="text-xl text-blue-100">View and manage service information</p>
    </div>
</section>

<!-- Service Details -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <?php if (isset($error)): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">Edit Service</h2>
            </div>
            <div class="p-6">
                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($service['title']); ?>" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="4" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($service['description']); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price</label>
                        <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($service['price']); ?>" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>" 
                                        <?php echo $category === $service['category'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="active" <?php echo $service['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $service['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="flex justify-between">
                        <button type="submit" name="action" value="update" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Update Service
                        </button>
                        <button type="submit" name="action" value="delete" 
                                class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to delete this service?')">
                            Delete Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?> 