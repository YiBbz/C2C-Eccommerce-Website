<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$conn = getDB();

require_once 'includes/functions.php';

// Get search parameters
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : null;
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$items_per_page = 12;

// Get all categories for filter
$stmt = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// Get services based on search parameters
$services = searchServices($conn, $search, $category, $min_price, $max_price);

// Calculate pagination
$total_items = count($services);
$pagination = generatePagination($total_items, $items_per_page, $page);

// Slice the array for current page
$offset = ($page - 1) * $items_per_page;
$services = array_slice($services, $offset, $items_per_page);
?>
<?php include 'includes/header.php'; ?>

<div class="min-h-screen bg-gray-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Browse Services</h1>
            <p class="text-gray-400">Find the perfect service for your needs</p>
        </div>

        <!-- Search and Filter Form -->
        <div class="bg-gray-800 rounded-2xl shadow-xl p-6 mb-8 backdrop-blur-sm bg-opacity-90">
            <form action="services.php" method="GET" class="space-y-4 md:space-y-0">
                <!-- Search Bar -->
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           class="w-full px-4 py-3 pl-12 rounded-xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300" 
                           placeholder="Search services..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>

                <!-- Filters Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Category Filter -->
                    <div class="relative">
                        <select name="category" 
                                class="w-full px-4 py-3 pl-12 rounded-xl bg-gray-700 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 appearance-none transition-all duration-300">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo $category === $cat['name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fas fa-tag absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>

                    <!-- Price Range -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <input type="number" 
                                   name="min_price" 
                                   class="w-full px-4 py-3 pl-12 rounded-xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300" 
                                   placeholder="Min Price" 
                                   value="<?php echo $min_price; ?>">
                            <i class="fas fa-dollar-sign absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <div class="relative">
                            <input type="number" 
                                   name="max_price" 
                                   class="w-full px-4 py-3 pl-12 rounded-xl bg-gray-700 border border-gray-600 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300" 
                                   placeholder="Max Price" 
                                   value="<?php echo $max_price; ?>">
                            <i class="fas fa-dollar-sign absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Filter Button -->
                    <button type="submit" 
                            class="w-full bg-purple-600 text-white px-6 py-3 rounded-xl hover:bg-purple-700 transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($services)): ?>
                <div class="col-span-full">
                    <div class="bg-gray-800 text-gray-300 p-8 rounded-2xl text-center">
                        <i class="fas fa-search text-4xl mb-4 text-gray-500"></i>
                        <p class="text-xl">No services found matching your criteria.</p>
                        <p class="text-gray-400 mt-2">Try adjusting your filters or search terms.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex flex-col md:flex-row h-full">
                            <!-- Service Content -->
                            <div class="w-full md:w-1/3 p-6 flex flex-col">
                                <h5 class="text-xl font-semibold mb-2 text-white line-clamp-2"><?php echo htmlspecialchars($service['title']); ?></h5>
                                <p class="text-gray-400 mb-4 text-sm line-clamp-3"><?php echo htmlspecialchars($service['description']); ?></p>
                                <div class="mt-auto">
                                    <p class="text-sm text-gray-400 mb-2">
                                        <i class="fas fa-user mr-2"></i><?php echo htmlspecialchars($service['provider_name']); ?>
                                        <?php if ($service['avg_rating']): ?>
                                            <span class="ml-4">
                                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                                <?php echo number_format($service['avg_rating'], 1); ?>/5
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-2xl font-bold text-purple-500 mb-4">R<?php echo formatPrice($service['price']); ?></p>
                                    <a href="service-details.php?id=<?php echo $service['id']; ?>" 
                                       class="block w-full bg-purple-600 text-white px-6 py-3 rounded-xl text-center hover:bg-purple-700 transition-all duration-300 transform hover:scale-105">
                                        Book Now
                                    </a>
                                </div>
                            </div>
                            <!-- Service Image -->
                            <div class="w-full md:w-2/3 relative">
                                <?php 
                                $images = !empty($service['image']) ? explode(',', $service['image']) : [];
                                if (!empty($images)): 
                                ?>
                                    <div class="relative h-full">
                                        <div class="service-images-slider h-full">
                                            <?php foreach ($images as $index => $image): ?>
                                                <div class="service-image-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                                                    <img src="<?php echo htmlspecialchars($image); ?>" 
                                                         class="w-full h-full object-cover" 
                                                         alt="<?php echo htmlspecialchars($service['title']); ?>">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php if (count($images) > 1): ?>
                                            <button class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-75 transition-all duration-300 prev-slide">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-75 transition-all duration-300 next-slide">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                                                <?php for ($i = 0; $i < count($images); $i++): ?>
                                                    <button class="w-2 h-2 rounded-full bg-white bg-opacity-50 hover:bg-opacity-100 transition-all duration-300 slide-dot <?php echo $i === 0 ? 'active' : ''; ?>"></button>
                                                <?php endfor; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="w-full h-full bg-gray-700 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-500 text-4xl"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (!empty($pagination)): ?>
            <div class="flex justify-center mt-12">
                <nav class="flex items-center space-x-2">
                    <?php if ($pagination['has_previous']): ?>
                        <a href="?page=<?php echo $pagination['previous_page']; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>" 
                           class="px-6 py-3 text-gray-400 bg-gray-800 rounded-xl hover:bg-gray-700 hover:text-white transition-all duration-300">
                            <i class="fas fa-chevron-left mr-2"></i>Previous
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>" 
                           class="px-6 py-3 rounded-xl transition-all duration-300 <?php echo $i === $pagination['current_page'] ? 'bg-purple-600 text-white' : 'text-gray-400 bg-gray-800 hover:bg-gray-700 hover:text-white'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($pagination['has_next']): ?>
                        <a href="?page=<?php echo $pagination['next_page']; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>" 
                           class="px-6 py-3 text-gray-400 bg-gray-800 rounded-xl hover:bg-gray-700 hover:text-white transition-all duration-300">
                            Next<i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<style>
.service-image-slide {
    display: none;
    width: 100%;
    height: 100%;
}

.service-image-slide.active {
    display: block;
}

.slide-dot.active {
    background-color: white;
    opacity: 1;
}

/* Line clamp utilities */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sliders = document.querySelectorAll('.service-images-slider');
    
    sliders.forEach(slider => {
        const slides = slider.querySelectorAll('.service-image-slide');
        const prevBtn = slider.parentElement.querySelector('.prev-slide');
        const nextBtn = slider.parentElement.querySelector('.next-slide');
        const dots = slider.parentElement.querySelectorAll('.slide-dot');
        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            slides[index].classList.add('active');
            dots[index].classList.add('active');
            currentSlide = index;
        }

        function startSlideInterval() {
            if (slides.length > 1) {
                slideInterval = setInterval(() => {
                    currentSlide = (currentSlide + 1) % slides.length;
                    showSlide(currentSlide);
                }, 5000);
            }
        }

        function stopSlideInterval() {
            clearInterval(slideInterval);
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                stopSlideInterval();
                currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(currentSlide);
                startSlideInterval();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                stopSlideInterval();
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
                startSlideInterval();
            });
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                stopSlideInterval();
                showSlide(index);
                startSlideInterval();
            });
        });

        // Start automatic slideshow
        startSlideInterval();

        // Pause slideshow on hover
        slider.addEventListener('mouseenter', stopSlideInterval);
        slider.addEventListener('mouseleave', startSlideInterval);
    });
});
</script> 