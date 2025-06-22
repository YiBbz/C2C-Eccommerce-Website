<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Get user type from URL parameter
$user_type = isset($_GET['type']) ? $_GET['type'] : 'customer';
if (!in_array($user_type, ['customer', 'service_provider'])) {
    $user_type = 'customer';
}
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-grey-900  text-white py-20">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <div class="lg:w-1/2">
                <h1 class="text-5xl font-bold mb-6">Create Your Account</h1>
                <p class="text-xl text-purple-200">Join our community and start your journey</p>
            </div>
            <div class="lg:w-1/2">
                <img src="assets/images/register-hero.svg" alt="Register" class="rounded-lg shadow-2xl">
            </div>
        </div>
    </div>
</section>

<!-- Registration Form Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded-lg mb-8">
                    <?php 
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="bg-gray-800 rounded-xl p-8 shadow-lg">
                <h2 class="text-3xl font-bold mb-8 text-center text-white">Sign Up</h2>
                
                <!-- User Type Selection -->
                <div class="flex justify-center mb-8">
                    <div class="inline-flex rounded-lg border border-gray-700 p-1">
                        <a href="?type=customer" class="px-6 py-2 rounded-lg <?php echo $user_type === 'customer' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'; ?> transition duration-300">
                            Customer
                        </a>
                        <a href="?type=service_provider" class="px-6 py-2 rounded-lg <?php echo $user_type === 'service_provider' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700'; ?> transition duration-300">
                            Service Provider
                        </a>
                    </div>
                </div>

                <form action="process-register.php" method="POST" class="space-y-6">
                    <input type="hidden" name="role" value="<?php echo htmlspecialchars($user_type); ?>">
                    
                    <div>
                        <label for="username" class="block text-gray-300 font-semibold mb-2">Username</label>
                        <input type="text" id="username" name="username" required
                               value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                    </div>

                    <div>
                        <label for="full_name" class="block text-gray-300 font-semibold mb-2">Full Name</label>
                        <input type="text" id="full_name" name="full_name" required
                               value="<?php echo isset($_SESSION['form_data']['full_name']) ? htmlspecialchars($_SESSION['form_data']['full_name']) : ''; ?>"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                    </div>

                    <div>
                        <label for="email" class="block text-gray-300 font-semibold mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required
                               value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-gray-300 font-semibold mb-2">Password</label>
                            <input type="password" id="password" name="password" required
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-gray-300 font-semibold mb-2">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                        </div>
                    </div>

                    <?php if ($user_type === 'service_provider'): ?>
                        <div>
                            <label for="business_name" class="block text-gray-300 font-semibold mb-2">Business Name (Optional)</label>
                            <input type="text" id="business_name" name="business_name"
                                   value="<?php echo isset($_SESSION['form_data']['business_name']) ? htmlspecialchars($_SESSION['form_data']['business_name']) : ''; ?>"
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                        </div>
                         <div>
                            <label for="business_description" class="block text-gray-300 font-semibold mb-2">Business Description (Optional)</label>
                            <textarea id="business_description" name="business_description" rows="4"
                                      class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white"><?php echo isset($_SESSION['form_data']['business_description']) ? htmlspecialchars($_SESSION['form_data']['business_description']) : ''; ?></textarea>
                        </div>
                    <?php endif; ?>

                    <div class="flex items-center">
                        <input type="checkbox" id="terms" name="terms" required class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                        <label for="terms" class="ml-2 block text-sm text-gray-300">
                            I agree to the <a href="terms.php" class="text-purple-400 hover:text-purple-300">Terms of Service</a> and <a href="privacy.php" class="text-purple-400 hover:text-purple-300">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                        Create Account
                    </button>
                </form>
                <?php unset($_SESSION['form_data']); ?>
                
                <div class="mt-8 text-center">
                    <p class="text-gray-300">Already have an account? <a href="login.php" class="text-purple-400 hover:text-purple-300 font-semibold">Sign in</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?> 