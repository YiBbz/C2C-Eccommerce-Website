<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Check if the user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$conn = getDB();

$success = $error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getDB();
        
        // Create settings table if it doesn't exist
        $db->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INT PRIMARY KEY AUTO_INCREMENT,
                setting_key VARCHAR(50) UNIQUE NOT NULL,
                setting_value TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        // Update or insert settings
        $stmt = $db->prepare("
            INSERT INTO settings (setting_key, setting_value) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        
        $settings = [
            'site_name' => $_POST['site_name'] ?? '',
            'site_description' => $_POST['site_description'] ?? '',
            'contact_email' => $_POST['contact_email'] ?? '',
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? '1' : '0',
            'contact_phone' => $_POST['contact_phone'] ?? '',
            'address' => $_POST['address'] ?? '',
            'facebook_url' => $_POST['facebook_url'] ?? '',
            'twitter_url' => $_POST['twitter_url'] ?? '',
            'instagram_url' => $_POST['instagram_url'] ?? ''
        ];
        
        foreach ($settings as $key => $value) {
            $stmt->execute([$key, $value]);
        }
        
        $success = "Settings updated successfully!";
    } catch(PDOException $e) {
        $error = "Error updating settings: " . $e->getMessage();
    }
}

// Get current settings
try {
    $db = getDB();
    
    // Create settings table if it doesn't exist
    $db->exec("
        CREATE TABLE IF NOT EXISTS settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            setting_key VARCHAR(50) UNIQUE NOT NULL,
            setting_value TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    $stmt = $db->query("SELECT * FROM settings WHERE id = 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error loading settings: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - Admin Dashboard</title>
    <link href="../assets/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
<?php include 'admin-navbar.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Site Settings</h1>
        <p class="text-xl text-blue-100">Manage your site configuration</p>
    </div>
</section>

<!-- Settings Form -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="bg-white rounded-xl shadow-lg p-6">
                <!-- Site Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Site Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                            <input type="text" name="site_name" id="site_name" 
                                   value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="site_description" class="block text-sm font-medium text-gray-700">Site Description</label>
                            <textarea name="site_description" id="site_description" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($settings['site_description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Contact Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                            <input type="email" name="contact_email" id="contact_email" 
                                   value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                            <input type="tel" name="contact_phone" id="contact_phone" 
                                   value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="address" id="address" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($settings['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Social Media Links -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Social Media Links</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="facebook_url" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                            <input type="url" name="facebook_url" id="facebook_url" 
                                   value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="twitter_url" class="block text-sm font-medium text-gray-700">Twitter URL</label>
                            <input type="url" name="twitter_url" id="twitter_url" 
                                   value="<?php echo htmlspecialchars($settings['twitter_url'] ?? ''); ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="instagram_url" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                            <input type="url" name="instagram_url" id="instagram_url" 
                                   value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?> 