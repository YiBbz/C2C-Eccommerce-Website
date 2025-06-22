<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
?>
<?php include 'includes/header.php'; ?>

<div class="min-h-screen bg-gray-900">
    <div class="container mx-auto px-4 py-12">
        <!-- Header Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Contact Us</h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">Have questions? We're here to help. Get in touch with our team.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-gray-800 rounded-2xl shadow-xl p-8 backdrop-blur-sm bg-opacity-90">
                <h2 class="text-2xl font-bold text-white mb-6">Send us a Message</h2>
                <form action="process-contact.php" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-gray-300 mb-2">Your Name</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   required 
                                   class="w-full px-4 py-3 rounded-xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300"
                                   placeholder="John Doe">
                        </div>
                        <div>
                            <label for="email" class="block text-gray-300 mb-2">Email Address</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   required 
                                   class="w-full px-4 py-3 rounded-xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300"
                                   placeholder="john@example.com">
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-gray-300 mb-2">Subject</label>
                        <input type="text" 
                               id="subject" 
                               name="subject" 
                               required 
                               class="w-full px-4 py-3 rounded-xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300"
                               placeholder="How can we help?">
                    </div>

                    <div>
                        <label for="message" class="block text-gray-300 mb-2">Message</label>
                        <textarea id="message" 
                                  name="message" 
                                  required 
                                  rows="6" 
                                  class="w-full px-4 py-3 rounded-xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300 resize-none"
                                  placeholder="Tell us more about your inquiry..."></textarea>
                    </div>

                    <button type="submit" 
                            class="w-full bg-purple-600 text-white px-8 py-3 rounded-xl hover:bg-purple-700 transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <!-- Office Location -->
                <div class="bg-gray-800 rounded-2xl shadow-xl p-8 backdrop-blur-sm bg-opacity-90">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2">Our Office</h3>
                            <p class="text-gray-400">123 Business Street<br>Suite 100<br>Midrand, 1687</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Methods -->
                <div class="bg-gray-800 rounded-2xl shadow-xl p-8 backdrop-blur-sm bg-opacity-90">
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white mb-1">Phone</h3>
                                <p class="text-gray-400">+27 61 123-4567</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white mb-1">Email</h3>
                                <p class="text-gray-400">support@Mošomo.com</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white mb-1">Business Hours</h3>
                                <p class="text-gray-400">Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="bg-gray-800 rounded-2xl shadow-xl p-8 backdrop-blur-sm bg-opacity-90">
                    <h3 class="text-xl font-bold text-white mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center hover:bg-purple-600 transition-all duration-300 transform hover:scale-105">
                            <i class="fab fa-facebook-f text-white text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center hover:bg-purple-600 transition-all duration-300 transform hover:scale-105">
                            <i class="fab fa-twitter text-white text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center hover:bg-purple-600 transition-all duration-300 transform hover:scale-105">
                            <i class="fab fa-instagram text-white text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center hover:bg-purple-600 transition-all duration-300 transform hover:scale-105">
                            <i class="fab fa-linkedin-in text-white text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="mt-12">
            <div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm bg-opacity-90">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.15830869428!2d-74.11976397304903!3d40.69766374874431!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1645564750981!5m2!1sen!2s" 
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy"
                    class="rounded-2xl">
                </iframe>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<style>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #1f2937;
}

::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Form focus styles */
input:focus, textarea:focus {
    box-shadow: 0 0 0 2px rgba(147, 51, 234, 0.5);
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style> 