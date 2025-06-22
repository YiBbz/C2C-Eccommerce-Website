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

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'update') {
                // Log the update attempt
                error_log("Attempting to update user ID: " . $user_id);
                
                $stmt = $conn->prepare("
                    UPDATE users 
                    SET full_name = ?, email = ?, role = ?, username = ?, bio = ?
                    WHERE id = ?
                ");
                
                $params = [
                    $_POST['full_name'],
                    $_POST['email'],
                    $_POST['role'],
                    $_POST['username'],
                    $_POST['bio'],
                    $user_id
                ];
                
                // Log the parameters
                error_log("Update parameters: " . print_r($params, true));
                
                $result = $stmt->execute($params);
                
                // Handle profile picture upload
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../uploads/profiles/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
                    $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                        // Update profile picture in database
                        $stmt_pic = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                        $stmt_pic->execute([$new_filename, $user_id]);
                    }
                }
                
                if ($result) {
                    $success = "User updated successfully!";
                    error_log("User update successful");
                } else {
                    $error = "Failed to update user";
                    error_log("User update failed");
                }
            } elseif ($_POST['action'] === 'delete') {
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $result = $stmt->execute([$user_id]);
                
                if ($result) {
                    header("Location: users.php");
                    exit;
                } else {
                    $error = "Failed to delete user";
                }
            }
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
            error_log("Database error: " . $e->getMessage());
        }
    }
}

// Get user details
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: users.php");
        exit;
    }
} catch(PDOException $e) {
    $error = "Error loading user: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - Admin Dashboard</title>
    <link href="../assets/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
<?php include 'admin-navbar.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">User Details</h1>
        <p class="text-xl text-blue-100">View and manage user information</p>
    </div>
</section>

<!-- User Details -->
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
                <h2 class="text-2xl font-bold text-gray-800">Edit User</h2>
            </div>
            <div class="p-6">
                <form method="POST" class="space-y-6" enctype="multipart/form-data">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <?php if (!empty($user['profile_picture'])): ?>
                                <img src="../uploads/profiles/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                                     alt="Profile Picture" 
                                     class="h-24 w-24 object-cover rounded-full">
                            <?php else: ?>
                                <div class="h-24 w-24 bg-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400 text-4xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Profile Picture</label>
                            <input type="file" name="profile_picture" accept="image/*"
                                   class="mt-1 block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-full file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-blue-50 file:text-blue-700
                                          hover:file:bg-blue-100">
                            <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF up to 5MB</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bio</label>
                        <textarea name="bio" rows="4" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="customer" <?php echo $user['role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                            <option value="provider" <?php echo $user['role'] === 'provider' ? 'selected' : ''; ?>>Service Provider</option>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>

                    <div class="flex justify-between">
                        <button type="submit" name="action" value="update" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Update User
                        </button>
                        <button type="submit" name="action" value="delete" 
                                class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to delete this user?')">
                            Delete User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?> 