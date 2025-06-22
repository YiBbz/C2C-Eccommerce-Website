<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mošomo - Find the Perfect Service Provider</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/tailwind.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobileMenu'); // Corrected ID from mobile-menu to mobileMenu if it was a typo, or ensure HTML has mobile-menu
            const mobileMenuButton = document.getElementById('mobileMenuButton'); // Corrected to use getElementById
            // Ensure elements exist before trying to access their properties
            if (mobileMenu && mobileMenuButton) {
                if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });
    </script>
</head>
<body class="bg-gray-900 text-white">
    <!-- Header -->
    <header class="bg-gray-800 shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="index.php" class="flex items-center space-x-2">
                    <i class="fas fa-briefcase text-purple-500 text-2xl"></i>
                    <span class="text-xl font-bold text-white">Mošomo</span>
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-8">
                    <a href="index.php" class="text-white hover:text-purple-400 transition duration-300">Home</a>
                    <a href="services.php" class="text-white hover:text-purple-400 transition duration-300">Services</a>
                    <a href="about.php" class="text-white hover:text-purple-400 transition duration-300">About</a>
                    <a href="contact.php" class="text-white hover:text-purple-400 transition duration-300">Contact</a>
                </nav>
                <!-- User Menu -->
                <div class="hidden lg:flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="relative" id="userMenu">
                            <button class="flex items-center space-x-2 text-white hover:text-purple-400 transition duration-300 focus:outline-none">
                                <img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?? 'assets/images/default-avatar.png'); ?>" 
                                     alt="Profile" 
                                     class="w-8 h-8 rounded-full object-cover border-2 border-purple-500">
                                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl py-2 hidden" id="userDropdown">
                                <a href="profile.php" class="block px-4 py-2 text-white hover:bg-gray-700 transition duration-300">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="dashboard.php" class="block px-4 py-2 text-white hover:bg-gray-700 transition duration-300">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <?php if ($_SESSION['role'] === 'customer'): ?>
                                    <a href="customer-messages.php" class="block px-4 py-2 text-white hover:bg-gray-700 transition duration-300">
                                        <i class="fas fa-envelope mr-2"></i>Messages
                                    </a>
                                <?php elseif ($_SESSION['role'] === 'service_provider'): ?>
                                    <a href="provider-messages.php" class="block px-4 py-2 text-white hover:bg-gray-700 transition duration-300">
                                        <i class="fas fa-envelope mr-2"></i>Messages
                                    </a>
                                <?php endif; ?>
                                <div class="border-t border-gray-700 my-2"></div>
                                <a href="logout.php" class="block px-4 py-2 text-white hover:bg-gray-700 transition duration-300">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="text-white hover:text-purple-400 transition duration-300">Login</a>
                        <a href="register.php" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-300">Sign Up</a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-white hover:text-purple-400 transition duration-300 focus:outline-none" id="mobileMenuButton">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden hidden bg-gray-800 border-t border-gray-700" id="mobileMenu">
            <div class="container mx-auto px-4 py-4">
                <nav class="flex flex-col space-y-4">
                    <a href="index.php" class="text-white hover:text-purple-400 transition duration-300 py-2">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                    <a href="services.php" class="text-white hover:text-purple-400 transition duration-300 py-2">
                        <i class="fas fa-briefcase mr-2"></i>Services
                    </a>
                    <a href="about.php" class="text-white hover:text-purple-400 transition duration-300 py-2">
                        <i class="fas fa-info-circle mr-2"></i>About
                    </a>
                    <a href="contact.php" class="text-white hover:text-purple-400 transition duration-300 py-2">
                        <i class="fas fa-envelope mr-2"></i>Contact
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="border-t border-gray-700 pt-4">
                            <a href="profile.php" class="block text-white hover:text-purple-400 transition duration-300 py-2">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="dashboard.php" class="block text-white hover:text-purple-400 transition duration-300 py-2">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <?php if ($_SESSION['role'] === 'customer'): ?>
                                <a href="customer-messages.php" class="block text-white hover:text-purple-400 transition duration-300 py-2">
                                    <i class="fas fa-envelope mr-2"></i>Messages
                                </a>
                            <?php elseif ($_SESSION['role'] === 'provider'): ?>
                                <a href="provider-messages.php" class="block text-white hover:text-purple-400 transition duration-300 py-2">
                                    <i class="fas fa-envelope mr-2"></i>Messages
                                </a>
                            <?php endif; ?>
                            <a href="logout.php" class="block text-white hover:text-purple-400 transition duration-300 py-2">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="border-t border-gray-700 pt-4">
                            <a href="login.php" class="block text-white hover:text-purple-400 transition duration-300 py-2">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                            <a href="register.php" class="block text-white hover:text-purple-400 transition duration-300 py-2">
                                <i class="fas fa-user-plus mr-2"></i>Sign Up
                            </a>
                        </div>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-16">
</body>
</html> 