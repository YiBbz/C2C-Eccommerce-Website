<?php 
require_once 'config/database.php';
require_once 'classes/Service.php';
require_once 'includes/header.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Service class
$service = new Service($db);

// Get featured services
$featured_services = $service->getFeaturedServices();
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4">Find Top South African Freelance Services</h1>
        <p class="lead">Connect with talented freelancers across South Africa</p>
        <div class="search-box mt-4">
            <form action="<?= BASE_URL ?>/services.php" method="GET">
                <div class="input-group mb-3 w-75 mx-auto">
                    <input type="text" name="search" class="form-control" placeholder="Search for services...">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Popular Categories -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Popular Categories</h2>
        <div class="row g-4">
            <?php
            $categories = [
                ['title' => 'Digital Marketing', 'description' => 'Social media, SEO, and more'],
                ['title' => 'Web Development', 'description' => 'Websites and web applications'],
                ['title' => 'Content Writing', 'description' => 'Articles and copywriting'],
                ['title' => 'Graphic Design', 'description' => 'Logos and branding']
            ];
            
            foreach($categories as $category):
            ?>
            <div class="col-md-3">
                <div class="category-card card">
                    <img src="<?= BASE_URL ?>/assets/images/categories/<?= strtolower(str_replace(' ', '-', $category['title'])) ?>.jpg" 
                         class="card-img-top" alt="<?= $category['title'] ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= $category['title'] ?></h5>
                        <p class="card-text"><?= $category['description'] ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Services -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Featured Services</h2>
        <div class="row g-4">
            <?php while($service = $featured_services->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="col-md-4">
                <div class="service-card card h-100">
                    <img src="<?= BASE_URL ?>/uploads/services/<?= $service['image'] ?>" class="card-img-top" alt="<?= $service['title'] ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="seller-rating">★★★★★ (<?= number_format($service['average_rating'], 1) ?>)</span>
                            <span class="price-tag">R<?= number_format($service['price']) ?></span>
                        </div>
                        <h5 class="card-title"><?= $service['title'] ?></h5>
                        <p class="card-text"><?= $service['description'] ?></p>
                        <div class="seller-info d-flex align-items-center mt-3">
                            <img src="<?= BASE_URL ?>/uploads/profiles/<?= $service['seller_image'] ?>" 
                                 class="rounded-circle me-2" alt="<?= $service['seller_name'] ?>" 
                                 style="width: 40px; height: 40px;">
                            <span><?= $service['seller_name'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- How It Works section remains the same -->
<?php include 'includes/footer.php'; ?>
