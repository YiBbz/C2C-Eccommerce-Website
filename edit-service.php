<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Ensure user is logged in and is a provider
requireServiceProvider();

$user_id = $_SESSION['user_id'];
$service_id = null;
$service_name = '';
$service_description = '';
$service_price = '';
$service_category = '';
$error_message = '';
$success_message = '';

// Check if a service ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $service_id = intval($_GET['id']);

    // Fetch service details from the database
    $conn = getDB();
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ? AND provider_id = ?");
    $stmt->execute([$service_id, $user_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($service) {
        $service_name = htmlspecialchars($service['title']);
        $service_description = htmlspecialchars($service['description']);
        $service_price = htmlspecialchars($service['price']);
        $service_category = htmlspecialchars($service['category']);
    } else {
        $error_message = "Service not found or you do not have permission to edit it.";
        $service_id = null;
    }
} else {
    $error_message = "No service ID provided.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_service']) && $service_id) {
    // Sanitize and validate input
    $new_service_name = trim($_POST['service_name']);
    $new_service_description = trim($_POST['service_description']);
    $new_service_price = trim($_POST['service_price']);
    $new_service_category = trim($_POST['service_category']);
    $new_service_status = trim($_POST['status']);

    // Basic validation
    if (empty($new_service_name)) {
        $error_message .= "Service name is required.<br>";
    }
    if (empty($new_service_description)) {
        $error_message .= "Service description is required.<br>";
    }
    if (!is_numeric($new_service_price) || $new_service_price < 0) {
        $error_message .= "Valid service price is required.<br>";
    }
    if (empty($new_service_category)) {
        $error_message .= "Service category is required.<br>";
    }

    // Handle image upload if a new image was provided
    $image_path = $service['image'] ?? null;
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $upload_result = handleImageUpload($_FILES['image'], 'service', $service['image'] ?? null);
        if ($upload_result['success']) {
            $image_path = $upload_result['path'];
        } else {
            $error_message .= $upload_result['error'] . "<br>";
        }
    }

    if (empty($error_message)) {
        try {
            $conn = getDB();
            // Update the service in the database
            $update_stmt = $conn->prepare("UPDATE services SET title = ?, description = ?, price = ?, category = ?, status = ?, image = ? WHERE id = ? AND provider_id = ?");
            
            if ($update_stmt->execute([
                $new_service_name, 
                $new_service_description, 
                $new_service_price, 
                $new_service_category,
                $new_service_status,
                $image_path,
                $service_id, 
                $user_id
            ])) {
                $success_message = "Service updated successfully!";
                // Refresh current values for the form
                $service_name = htmlspecialchars($new_service_name);
                $service_description = htmlspecialchars($new_service_description);
                $service_price = htmlspecialchars($new_service_price);
                $service_category = htmlspecialchars($new_service_category);
                $service['status'] = $new_service_status;
                $service['image'] = $image_path;
            } else {
                $error_message = "Failed to update service. Please try again.";
            }
        } catch (PDOException $e) {
            error_log("Service Update Error: " . $e->getMessage());
            $error_message = "An error occurred while updating the service.";
        }
    }
}

// Get categories for dropdown
$conn = getDB();
$stmt = $conn->query("SELECT DISTINCT category FROM services ORDER BY category");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Include header
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="bg-grey-900text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Edit Service</h1>
        <p class="text-xl text-purple-200">Update your service details</p>
    </div>
</section>

<!-- Edit Service Form -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <?php if (!empty($error_message)): ?>
                    <div class="bg-red-900/50 border border-red-800 text-red-200 px-4 py-3 rounded-lg mb-6">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="bg-green-900/50 border border-green-800 text-green-200 px-4 py-3 rounded-lg mb-6">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($service_id && empty($error_message) || (!empty($error_message) && $_SERVER["REQUEST_METHOD"] == "POST")): ?>
                <form action="edit-service.php?id=<?php echo htmlspecialchars($service_id); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
                    
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Service Title</label>
                        <input type="text" id="title" name="service_name" required
                               value="<?php echo htmlspecialchars($service_name); ?>"
                               class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Enter a descriptive title for your service">
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                        <select id="category" name="service_category" required
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>" <?php echo ($service_category == $category) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <textarea id="description" name="service_description" rows="6" required
                                  class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                  placeholder="Describe your service in detail"><?php echo htmlspecialchars($service_description); ?></textarea>
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Price (R)</label>
                        <input type="number" id="price" name="service_price" min="1" step="0.01" required
                               value="<?php echo htmlspecialchars($service_price); ?>"
                               class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Enter your service price">
                    </div>

                    <!-- Current Image -->
                    <?php if (!empty($service['image'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Current Image</label>
                            <div class="relative w-32 h-32">
                                <img src="<?php echo htmlspecialchars($service['image']); ?>" 
                                     alt="Current service image" 
                                     class="w-full h-full object-cover rounded-lg">
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- New Image -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-300 mb-2">
                            <?php echo !empty($service['image']) ? 'Change Service Image' : 'Service Image'; ?>
                        </label>
                        <div class="mt-1 flex items-center">
                            <input type="file" id="image" name="image" accept="image/*"
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <p class="mt-1 text-sm text-gray-400">
                            <?php echo !empty($service['image']) ? 'Upload a new image to replace the current one' : 'Upload an image for your service'; ?>
                            (JPG, PNG, or GIF, max 5MB)
                        </p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="active" <?php echo ($service['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($service['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4">
                        <a href="dashboard.php" 
                           class="bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-500 transition duration-300">
                            Cancel
                        </a>
                        <button type="submit" name="update_service"
                                class="bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                            Update Service
                        </button>
                    </div>
                </form>
                <?php elseif (!$service_id && !empty($error_message)): ?>
                    <div class="bg-yellow-900/50 border border-yellow-800 text-yellow-200 px-4 py-3 rounded-lg">
                        Please return to your dashboard and select a valid service to edit.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
