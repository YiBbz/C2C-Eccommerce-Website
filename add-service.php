<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'config/database.php';

// Get database connection
$conn = getDB();

// Check if user is logged in and is a service provider
if (!isLoggedIn() || !isServiceProvider()) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="bg-grey-900 text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Add New Service</h1>
        <p class="text-xl text-purple-200">Create a new service listing for your customers</p>
    </div>
</section>

<!-- Add Service Form -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-gray-800 rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-white mb-6">Service Details</h2>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-900/50 border border-red-800 text-red-200 px-4 py-3 rounded-lg mb-6">
                        <?php 
                        echo htmlspecialchars($_SESSION['error']);
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="process-add-service.php" enctype="multipart/form-data" class="space-y-6">
                    <!-- Service Title -->
                    <div>
                        <label for="title" class="block text-gray-300 font-semibold mb-2">Service Title</label>
                        <input type="text" id="title" name="title" required
                               placeholder="Enter a descriptive title for your service"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white placeholder-gray-400">
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-gray-300 font-semibold mb-2">Category</label>
                        <select id="category" name="category" required
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white">
                            <option value="">Select a category</option>
                            <?php
                            $categories = getCategories();
                            if ($categories) {
                                foreach ($categories as $cat) {
                                    echo '<option value="' . htmlspecialchars($cat['name']) . '">' . htmlspecialchars($cat['name']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-gray-300 font-semibold mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" required
                                  placeholder="Describe your service in detail"
                                  class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white placeholder-gray-400"></textarea>
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-gray-300 font-semibold mb-2">Price (R)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required
                               placeholder="Enter your service price"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white placeholder-gray-400">
                    </div>

                    <!-- Service Images -->
                    <div>
                        <label class="block text-gray-300 font-semibold mb-2">Service Images</label>
                        <div class="mt-1 flex items-center">
                            <input type="file" 
                                   name="service_images[]" 
                                   accept="image/*" 
                                   multiple
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                        </div>
                        <p class="mt-1 text-sm text-gray-400">
                            Upload multiple images for your service (JPG, PNG, or GIF, max 5MB each)
                        </p>
                        <div id="image-preview" class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4">
                            <!-- Image previews will be shown here -->
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                            Add Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.querySelector('input[name="service_images[]"]');
    const imagePreview = document.getElementById('image-preview');

    imageInput.addEventListener('change', function() {
        imagePreview.innerHTML = ''; // Clear existing previews
        
        if (this.files) {
            Array.from(this.files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    
                    reader.onload = function(e) {
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                <button type="button" class="text-white hover:text-red-400" onclick="this.parentElement.parentElement.remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    }
                    
                    reader.readAsDataURL(file);
                    imagePreview.appendChild(div);
                }
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?> 