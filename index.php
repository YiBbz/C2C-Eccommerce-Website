<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$conn = getDB();

require_once 'includes/functions.php';

// Get popular services
$popular_services = getPopularServices($conn);

// Debug: Check the service data
echo "<!-- Debug: Service Data -->\n";
foreach ($popular_services as $service) {
    echo "<!-- Service ID: " . $service['id'] . " -->\n";
    echo "<!-- Images: " . (is_array($service['service_images']) ? implode(', ', $service['service_images']) : 'No images') . " -->\n";
}

// Get a limited number of categories for Popular Categories section
$stmt_categories = $conn->query("SELECT * FROM categories ORDER BY name LIMIT 4");
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/header.php'; ?>

<!-- Add this in the head section, after the existing styles -->
<style>
.service-slider {
    position: relative;
    width: 100%;
    height: 100%;
}

.service-slide {
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.5s ease, visibility 0.5s ease;
}

.service-slide.active {
    opacity: 1;
    visibility: visible;
}

.slider-dot.active {
    background-color: white;
}
</style>

<!-- Hero Section -->
<div class="relative min-h-[200vh] w-full" style="background-image: url('assets/images/pexels-hson-32357249.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;">
    <div class="absolute inset-0 bg-black bg-opacity-70"></div>
    <div class="sticky top-0 h-screen flex items-center justify-center">
        <div class="relative w-full max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <!-- Title and Subtitle -->
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
                Find the Perfect Service Provider
            </h1>
            <p class="text-lg sm:text-xl md:text-2xl text-gray-300 mb-10">
                Connect with trusted professionals for all your needs
            </p>
            
            <!-- Search Bar -->
            <div class="mb-8 max-w-xl mx-auto">
                <form action="services.php" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" 
                           name="search" 
                           placeholder="Search for services (e.g., plumbing, web design)" 
                           class="flex-grow px-5 py-3.5 rounded-lg text-white bg-gray-800/80 focus:outline-none focus:ring-2 focus:ring-purple-500 text-base placeholder-gray-400">
                    <button type="submit" 
                            class="bg-purple-600 text-white px-8 py-3.5 rounded-lg font-medium hover:bg-purple-700 transition duration-300 text-base">
                        Search
                    </button>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="login.php" 
                   class="w-full sm:w-auto bg-gray-800/80 backdrop-blur-sm text-white px-8 py-3.5 rounded-lg hover:bg-gray-700 transition duration-300 font-medium text-base text-center">
                    Sign In
                </a>
                <a href="register.php" 
                   class="w-full sm:w-auto bg-purple-600 text-white px-8 py-3.5 rounded-lg hover:bg-purple-700 transition duration-300 font-medium text-base text-center">
                    Get Started
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Popular Services Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12 text-white">Popular Services</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($popular_services as $service): ?>
            <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                <div class="relative h-48">
                    <?php 
                    $images = is_array($service['service_images']) ? $service['service_images'] : [];
                    if (!empty($images)): 
                    ?>
                        <div class="relative h-full">
                            <div class="service-slider">
                                <?php foreach ($images as $index => $image): ?>
                                    <div class="service-slide <?php echo $index === 0 ? 'active' : ''; ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                        <img src="<?php echo htmlspecialchars($image); ?>" 
                                             class="w-full h-full object-cover" 
                                             alt="<?php echo htmlspecialchars($service['title']); ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($images) > 1): ?>
                                <button class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-colors slider-prev z-10">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-colors slider-next z-10">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1 z-10">
                                    <?php for ($i = 0; $i < count($images); $i++): ?>
                                        <button class="w-2 h-2 rounded-full bg-white/50 hover:bg-white/80 transition-colors slider-dot <?php echo $i === 0 ? 'active' : ''; ?>"></button>
                                    <?php endfor; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="w-full h-full bg-gray-700 flex items-center justify-center text-gray-400">
                            No Image
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <h5 class="text-xl font-semibold mb-2 text-white"><?php echo htmlspecialchars($service['title']); ?></h5>
                    <p class="text-gray-400 mb-4"><?php echo htmlspecialchars(substr($service['description'], 0, 100)) . '...'; ?></p>
                    <p class="text-lg font-bold text-purple-500 mb-4">R<?php echo formatPrice($service['price']); ?></p>
                    <a href="service-details.php?id=<?php echo $service['id']; ?>" class="inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition duration-300">View Details</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Popular Categories Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12 text-white">Popular Categories</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-10">
            <?php foreach ($categories as $category_item): ?>
                <a href="services.php?category=<?php echo urlencode($category_item['name']); ?>" class="flex flex-col items-center justify-center bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition duration-300 text-center p-6">
                     <div class="text-purple-500 text-4xl mb-4">
                         <i class="fas fa-tag"></i>
                     </div>
                    <h3 class="text-lg font-semibold text-white"><?php echo htmlspecialchars($category_item['name']); ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12 text-white">How It Works</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6">
                <div class="bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300">
                    <i class="fas fa-search text-5xl text-purple-500 mb-6"></i>
                    <h4 class="text-xl font-semibold mb-4 text-white">Search Services</h4>
                    <p class="text-gray-400">Browse through our wide range of services</p>
                </div>
            </div>
            <div class="text-center p-6">
                <div class="bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300">
                    <i class="fas fa-comments text-5xl text-purple-500 mb-6"></i>
                    <h4 class="text-xl font-semibold mb-4 text-white">Connect & Chat</h4>
                    <p class="text-gray-400">Discuss your requirements with service providers</p>
                </div>
            </div>
            <div class="text-center p-6">
                <div class="bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300">
                    <i class="fas fa-check-circle text-5xl text-purple-500 mb-6"></i>
                    <h4 class="text-xl font-semibold mb-4 text-white">Book & Pay</h4>
                    <p class="text-gray-400">Book the service and make secure payments</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all service sliders
    document.querySelectorAll('.service-slider').forEach(slider => {
        const slides = slider.querySelectorAll('.service-slide');
        const prevBtn = slider.parentElement.querySelector('.slider-prev');
        const nextBtn = slider.parentElement.querySelector('.slider-next');
        const dots = slider.parentElement.querySelectorAll('.slider-dot');
        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach(slide => {
                slide.classList.remove('active');
                slide.style.opacity = '0';
                slide.style.visibility = 'hidden';
            });
            dots.forEach(dot => dot.classList.remove('active'));
            
            slides[index].classList.add('active');
            slides[index].style.opacity = '1';
            slides[index].style.visibility = 'visible';
            dots[index].classList.add('active');
            currentSlide = index;
        }

        function startSlideInterval() {
            if (slides.length > 1) {
                slideInterval = setInterval(() => {
                    let newIndex = currentSlide + 1;
                    if (newIndex >= slides.length) newIndex = 0;
                    showSlide(newIndex);
                }, 5000);
            }
        }

        function stopSlideInterval() {
            if (slideInterval) {
                clearInterval(slideInterval);
            }
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                stopSlideInterval();
                let newIndex = currentSlide - 1;
                if (newIndex < 0) newIndex = slides.length - 1;
                showSlide(newIndex);
                startSlideInterval();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                stopSlideInterval();
                let newIndex = currentSlide + 1;
                if (newIndex >= slides.length) newIndex = 0;
                showSlide(newIndex);
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

        // Start the auto-advance
        startSlideInterval();

        // Pause on hover
        slider.addEventListener('mouseenter', stopSlideInterval);
        slider.addEventListener('mouseleave', startSlideInterval);
    });
});
</script> 