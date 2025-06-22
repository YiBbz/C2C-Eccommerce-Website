<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-gray-900 text-white py-20">
    <div class="container mx-auto px-4">
        <div class="flex flex-col items-center text-center max-w-4xl mx-auto">
            <h1 class="text-5xl font-bold mb-6">About Our Platform</h1>
            <p class="text-xl text-purple-200 max-w-3xl">
                We strive to create a seamless platform that connects service providers with customers, 
                making it easier than ever to find and book quality services. Our goal is to empower 
                both service providers and customers through technology, transparency, and trust.
            </p>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12 text-white">How It Works</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300 text-center">
                <i class="fas fa-search text-5xl text-purple-500 mb-6"></i>
                <h4 class="text-xl font-semibold mb-4 text-white">Find Services</h4>
                <p class="text-gray-400">Browse through our wide range of services or use our search and filter options to find exactly what you need.</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300 text-center">
                <i class="fas fa-comments text-5xl text-purple-500 mb-6"></i>
                <h4 class="text-xl font-semibold mb-4 text-white">Connect & Chat</h4>
                <p class="text-gray-400">Connect with service providers through our built-in chat system to discuss your requirements and get quotes.</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300 text-center">
                <i class="fas fa-check-circle text-5xl text-purple-500 mb-6"></i>
                <h4 class="text-xl font-semibold mb-4 text-white">Book & Pay</h4>
                <p class="text-gray-400">Book the service and make secure payments through our platform. Choose between cash or online payment methods.</p>
            </div>
        </div>
    </div>
</section>

<!-- For Service Providers Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12 text-white">For Service Providers</h2>
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <div class="lg:w-1/2">
                <img src="assets/images/pexels-equalstock-32399827.jpg" alt="Service Providers" class="rounded-lg shadow-xl">
            </div>
            <div class="lg:w-1/2">
                <h3 class="text-3xl font-bold mb-6 text-white">Showcase Your Skills</h3>
                <p class="text-gray-400 mb-8">Join our platform to showcase your expertise and connect with potential clients. Here's what you can do:</p>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-1 mr-3"></i>
                        <span class="text-gray-300">Create a professional profile highlighting your skills and experience</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-1 mr-3"></i>
                        <span class="text-gray-300">List your services with detailed descriptions and pricing</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-1 mr-3"></i>
                        <span class="text-gray-300">Receive booking requests and manage your schedule</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-1 mr-3"></i>
                        <span class="text-gray-300">Build your reputation through client reviews</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-1 mr-3"></i>
                        <span class="text-gray-300">Get paid securely through our platform</span>
                    </li>
                </ul>
                <?php if (!isLoggedIn()): ?>
                    <a href="register.php?type=service_provider" class="inline-block bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300 mt-8">Become a Service Provider</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- For Customers Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12 text-white">For Customers</h2>
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <div class="lg:w-1/2">
                <h3 class="text-3xl font-bold mb-6 text-white">Find the Perfect Service</h3>
                <p class="text-gray-400 mb-8">Looking for professional services? Our platform makes it easy to find and book the services you need:</p>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-1 mr-3"></i>
                        <span class="text-gray-300">Browse through a wide range of services</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-1 mr-3"></i>
                        <span class="text-gray-300">Filter services by category, price, and ratings</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-1 mr-3"></i>
                        <span class="text-gray-300">Chat with service providers to discuss your needs</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-1 mr-3"></i>
                        <span class="text-gray-300">Book services and make secure payments</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-500 mt-1 mr-3"></i>
                        <span class="text-gray-300">Leave reviews and ratings for service providers</span>
                    </li>
                </ul>
                <?php if (!isLoggedIn()): ?>
                    <a href="register.php?type=customer" class="inline-block bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300 mt-8">Create an Account</a>
                <?php endif; ?>
            </div>
            <div class="lg:w-1/2">
                <img src="assets/images/pexels-olly-927451.jpg" alt="Customers" class="rounded-lg shadow-xl">
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12 text-white">Why Choose Us</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300 text-center">
                <i class="fas fa-shield-alt text-5xl text-purple-500 mb-6"></i>
                <h4 class="text-xl font-semibold mb-4 text-white">Secure Platform</h4>
                <p class="text-gray-400">Your data and payments are protected with industry-standard security measures.</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300 text-center">
                <i class="fas fa-star text-5xl text-purple-500 mb-6"></i>
                <h4 class="text-xl font-semibold mb-4 text-white">Quality Services</h4>
                <p class="text-gray-400">All service providers are verified and rated by our community.</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300 text-center">
                <i class="fas fa-headset text-5xl text-purple-500 mb-6"></i>
                <h4 class="text-xl font-semibold mb-4 text-white">24/7 Support</h4>
                <p class="text-gray-400">Our support team is always ready to help you with any questions.</p>
            </div>
            <div class="bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300 text-center">
                <i class="fas fa-hand-holding-usd text-5xl text-purple-500 mb-6"></i>
                <h4 class="text-xl font-semibold mb-4 text-white">Fair Pricing</h4>
                <p class="text-gray-400">Competitive prices and transparent payment system.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?> 