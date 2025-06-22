<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Check if the user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$conn = getDB();

// Get all services with provider details
try {
    $stmt = $conn->query("
        SELECT s.*, u.full_name as provider_name
        FROM services s
        JOIN users u ON s.provider_id = u.id
        ORDER BY s.created_at DESC
    ");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error loading services: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Admin Dashboard</title>
    <link href="../assets/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
<?php include 'admin-navbar.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Manage Services</h1>
        <p class="text-xl text-blue-100">View and manage all service listings</p>
    </div>
</section>

<!-- Services List -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <?php if (isset($error)): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">All Services</h2>
            </div>
            <div class="p-6">
                <?php if (empty($services)): ?>
                    <p class="text-gray-600 text-center py-4">No services found</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($services as $service): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($service['title']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if (!empty($service['image'])): ?>
                                                <img src="../uploads/services/<?php echo htmlspecialchars($service['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($service['title']); ?>" 
                                                     class="h-12 w-12 object-cover rounded-lg">
                                            <?php else: ?>
                                                <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($service['provider_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($service['category']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php 
                                            $description = htmlspecialchars($service['description']);
                                            echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            $<?php echo number_format($service['price'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                <?php echo $service['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo ucfirst($service['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('M d, Y', strtotime($service['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="service-details.php?id=<?php echo $service['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="service-details.php?id=<?php echo $service['id']; ?>&action=edit" 
                                               class="text-yellow-600 hover:text-yellow-900 mr-3">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="service-details.php?id=<?php echo $service['id']; ?>&action=delete" 
                                               class="text-red-600 hover:text-red-900"
                                               onclick="return confirm('Are you sure you want to delete this service?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?> 