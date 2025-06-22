    </main>
    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="space-y-4 lg:max-w-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-briefcase text-purple-500 text-2xl"></i>
                        <span class="text-xl font-bold">Mošomo</span>
                    </div>
                    <p class="text-gray-400">Find the perfect service provider for your needs. Connect with trusted professionals and get your work done.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>

                    

                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-purple-400 transition duration-300">Home</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-purple-400 transition duration-300">Services</a></li>
                        <li><a href="about.php" class="text-gray-400 hover:text-purple-400 transition duration-300">About Us</a></li>
                        <li><a href="contact.php" class="text-gray-400 hover:text-purple-400 transition duration-300">Contact</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <li><a href="services.php?category=Web%20Development" class="text-gray-400 hover:text-purple-400 transition duration-300">Web Development</a></li>
                        <li><a href="services.php?category=Graphic%20Design" class="text-gray-400 hover:text-purple-400 transition duration-300">Graphic Design</a></li>
                        <li><a href="services.php?category=Digital%20Marketing" class="text-gray-400 hover:text-purple-400 transition duration-300">Digital Marketing</a></li>
                        <li><a href="services.php?category=Writing" class="text-gray-400 hover:text-purple-400 transition duration-300">Writing</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center space-x-2 text-gray-400">
                            <i class="fas fa-envelope text-purple-500"></i>
                            <span>support@Mošomo.com</span>
                        </li>
                        <li class="flex items-center space-x-2 text-gray-400">
                            <i class="fas fa-phone text-purple-500"></i>
                            <span>+27 61 123-4567</span>
                        </li>
                        <li class="flex items-center space-x-2 text-gray-400">
                            <i class="fas fa-map-marker-alt text-purple-500"></i>
                            <span>123 Business Street, Midrand, South Africa</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-700 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">&copy; <?php echo date('Y'); ?> Mošomo. All rights reserved.</p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="privacy.php" class="text-gray-400 hover:text-purple-400 transition duration-300 text-sm">Privacy Policy</a>
                        <a href="terms.php" class="text-gray-400 hover:text-purple-400 transition duration-300 text-sm">Terms of Service</a>
                        <a href="faq.php" class="text-gray-400 hover:text-purple-400 transition duration-300 text-sm">FAQ</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuButton').addEventListener('click', function() {
            document.getElementById('mobileMenu').classList.toggle('hidden');
        });

        // User menu toggle
        const userMenu = document.getElementById('userMenu');
        const userDropdown = document.getElementById('userDropdown');
        
        if (userMenu) {
            userMenu.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userMenu.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html> 