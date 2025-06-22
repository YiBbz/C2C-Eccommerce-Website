<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    try {
        // Start transaction
        $conn = getDB();
        $conn->beginTransaction();

        // Check if email is already taken by another user
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->rowCount() > 0) {
            throw new Exception('Email is already taken by another user');
        }

        // Update basic info
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, bio = ? WHERE id = ?");
        $stmt->execute([$full_name, $email, $bio, $_SESSION['user_id']]);

        // Update password if provided
        if (!empty($current_password)) {
            if (empty($new_password) || empty($confirm_password)) {
                throw new Exception('New password and confirmation are required');
            }
            if ($new_password !== $confirm_password) {
                throw new Exception('New passwords do not match');
            }
            if (strlen($new_password) < 6) {
                throw new Exception('New password must be at least 6 characters long');
            }

            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $current_hash = $stmt->fetchColumn();

            if (!password_verify($current_password, $current_hash)) {
                throw new Exception('Current password is incorrect');
            }

            // Update password
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$new_hash, $_SESSION['user_id']]);
        }

        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['size'] > 0) {
            $upload_result = handleImageUpload($_FILES['profile_picture'], 'profile', $user['profile_picture'] ?? null);
            if ($upload_result['success']) {
                // Update profile picture in database
                $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                $stmt->execute([$upload_result['path'], $_SESSION['user_id']]);
                // Update session variable
                $_SESSION['profile_image'] = $upload_result['path'];
            } else {
                throw new Exception($upload_result['error']);
            }
        }

        $conn->commit();
        $success = 'Profile updated successfully';
        $user = getCurrentUser(); // Refresh user data
    } catch (Exception $e) {
        $conn->rollBack();
        $error = $e->getMessage();
    }
}
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-grey-900 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Profile Settings</h1>
        <p class="text-xl text-purple-200">Manage your account information and preferences</p>
    </div>
</section>

<!-- Profile Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                    <div class="text-center">
                        <div class="relative w-32 h-32 mx-auto mb-4">
                            <img src="<?php echo !empty($_SESSION['profile_image']) ? htmlspecialchars($_SESSION['profile_image']) : 'assets/images/default-profile.png'; ?>" 
                                 alt="Profile Picture"
                                 class="w-full h-full rounded-full object-cover border-4 border-purple-500 shadow-lg">
                            <div class="absolute bottom-0 right-0 bg-purple-600 rounded-full p-2 shadow-lg">
                                <i class="fas fa-camera text-white"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                        <p class="text-purple-400 font-semibold mb-4"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
                        <p class="text-gray-300 mb-6"><?php echo htmlspecialchars($user['bio'] ?? 'No bio added yet.'); ?></p>
                        <div class="space-y-2">
                            <p class="text-gray-300">
                                <i class="fas fa-envelope text-purple-500 mr-2"></i>
                                <?php echo htmlspecialchars($user['email']); ?>
                            </p>
                            <p class="text-gray-300">
                                <i class="fas fa-calendar text-purple-500 mr-2"></i>
                                Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div class="lg:col-span-2">
                <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-white mb-6">Edit Profile</h2>

                    <?php if ($error): ?>
                        <div class="bg-red-900/50 border border-red-800 text-red-200 px-4 py-3 rounded-lg mb-6">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="bg-green-900/50 border border-green-800 text-green-200 px-4 py-3 rounded-lg mb-6">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                        <!-- Profile Picture -->
                        <div>
                            <label for="profile_picture" class="block text-gray-300 font-semibold mb-2">
                                <?php echo !empty($user['profile_picture']) ? 'Change Profile Picture' : 'Profile Picture'; ?>
                            </label>
                            <div class="mt-1 flex items-center">
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                            </div>
                            <p class="mt-1 text-sm text-gray-400">
                                <?php echo !empty($user['profile_picture']) ? 'Upload a new image to replace the current one' : 'Upload a profile picture'; ?>
                                (JPG, PNG, or GIF, max 5MB)
                            </p>
                        </div>

                        <!-- Basic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name" class="block text-gray-300 font-semibold mb-2">Full Name</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                            </div>
                            <div>
                                <label for="email" class="block text-gray-300 font-semibold mb-2">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                            </div>
                        </div>

                        <!-- Bio -->
                        <div>
                            <label for="bio" class="block text-gray-300 font-semibold mb-2">Bio</label>
                            <textarea id="bio" name="bio" rows="4"
                                      class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>

                        <!-- Change Password Section -->
                        <div class="border-t border-gray-700 pt-6 mt-6">
                            <h3 class="text-xl font-semibold text-white mb-4">Change Password</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="current_password" class="block text-gray-300 font-semibold mb-2">Current Password</label>
                                    <input type="password" id="current_password" name="current_password"
                                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                                </div>
                                <div>
                                    <label for="new_password" class="block text-gray-300 font-semibold mb-2">New Password</label>
                                    <input type="password" id="new_password" name="new_password"
                                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                                </div>
                                <div>
                                    <label for="confirm_password" class="block text-gray-300 font-semibold mb-2">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password"
                                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 