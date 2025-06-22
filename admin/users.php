<?php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if the user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$conn = getDB();

// Get filter parameters
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$query = "SELECT * FROM users WHERE 1=1";
$params = [];

if ($role_filter) {
    $query .= " AND role = ?";
    $params[] = $role_filter;
}

if ($search) {
    $query .= " AND (username LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY created_at DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error loading users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link href="../assets/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
<?php include 'admin-navbar.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Manage Users</h1>
        <p class="text-xl text-blue-100">View and manage all user accounts</p>
    </div>
</section>

<!-- Users List -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">All Users</h2>
            </div>
            <div class="p-6">
                <?php if (empty($users)): ?>
                    <p class="text-gray-600 text-center py-4">No users found</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profile</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($users as $user): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if (!empty($user['profile_picture'])): ?>
                                                <img src="../uploads/profiles/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                                                     alt="<?php echo htmlspecialchars($user['full_name']); ?>" 
                                                     class="h-12 w-12 object-cover rounded-full">
                                            <?php else: ?>
                                                <div class="h-12 w-12 bg-gray-200 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($user['full_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($user['email']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php 
                                            $bio = htmlspecialchars($user['bio'] ?? '');
                                            echo strlen($bio) > 50 ? substr($bio, 0, 50) . '...' : $bio;
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                <?php echo match($user['role']) {
                                                    'admin' => 'bg-purple-100 text-purple-800',
                                                    'provider' => 'bg-blue-100 text-blue-800',
                                                    'customer' => 'bg-green-100 text-green-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                }; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="user-details.php?id=<?php echo $user['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="user-details.php?id=<?php echo $user['id']; ?>&action=edit" 
                                               class="text-yellow-600 hover:text-yellow-900 mr-3">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="user-details.php?id=<?php echo $user['id']; ?>&action=delete" 
                                               class="text-red-600 hover:text-red-900"
                                               onclick="return confirm('Are you sure you want to delete this user?')">
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