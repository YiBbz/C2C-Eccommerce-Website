<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-grey-900 text-white py-20">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <div class="lg:w-1/2">
                <h1 class="text-5xl font-bold mb-6">Welcome Back</h1>
                <p class="text-xl text-purple-200">Sign in to access your account</p>
            </div>
            <div class="lg:w-1/2">
                <img src="assets/images/login-hero.svg" alt="Login" class="rounded-lg shadow-2xl">
            </div>
        </div>
    </div>
</section>

<!-- Login Form Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded-lg mb-8">
                    <?php 
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="bg-gray-800 rounded-xl p-8 shadow-lg">
                <h2 class="text-3xl font-bold mb-8 text-center text-white">Sign In</h2>
                <form action="process-login.php" method="POST" class="space-y-6">
                    <div>
                        <label for="email" class="block text-gray-300 font-semibold mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required
                               value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                    </div>
                    <div>
                        <label for="password" class="block text-gray-300 font-semibold mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                            <label for="remember" class="ml-2 block text-sm text-gray-300">Remember me</label>
                        </div>
                        <a href="forgot-password.php" class="text-sm text-purple-400 hover:text-purple-300">Forgot password?</a>
                    </div>
                    <button type="submit" class="w-full bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                        Sign In
                    </button>
                </form>
                <?php unset($_SESSION['form_data']); ?>
                
                <div class="mt-8 text-center">
                    <p class="text-gray-300">Don't have an account? <a href="register.php" class="text-purple-400 hover:text-purple-300 font-semibold">Sign up</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?> 