<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$conn = getDB();
$service_id = $_GET['id'];

// Get service details
$stmt = $conn->prepare("
    SELECT s.*, u.full_name as provider_name, u.email as provider_email, u.id as provider_id
    FROM services s 
    JOIN users u ON s.provider_id = u.id 
    WHERE s.id = ?
");
$stmt->execute([$service_id]);
$service = $stmt->fetch();

if (!$service) {
    header('Location: index.php');
    exit;
}

// Get provider's other services
$stmt = $conn->prepare("
    SELECT * FROM services 
    WHERE provider_id = ? AND id != ? AND status = 'active' 
    LIMIT 3
");
$stmt->execute([$service['provider_id'], $service_id]);
$other_services = $stmt->fetchAll();

// Handle contact form submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $message = $_POST['message'] ?? '';
    
    try {
        if (empty($message)) {
            throw new Exception('Please enter a message');
        }

        // Insert message
        $stmt = $conn->prepare("
            INSERT INTO messages (sender_id, receiver_id, service_id, message) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$_SESSION['user_id'], $service['provider_id'], $service_id, $message]);

        $success = 'Message sent successfully';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title"><?php echo htmlspecialchars($service['title']); ?></h2>
                    <p class="text-muted">Category: <?php echo htmlspecialchars($service['category']); ?></p>
                    
                    <?php if ($service['image']): ?>
                        <img src="<?php echo htmlspecialchars($service['image']); ?>" class="img-fluid rounded mb-3" alt="Service Image">
                    <?php endif; ?>

                    <h5>Description</h5>
                    <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <h3 class="mb-0">$<?php echo number_format($service['price'], 2); ?></h3>
                        <?php if (isLoggedIn()): ?>
                            <?php if ($_SESSION['role'] === 'customer'): ?>
                                <a href="customer-messages.php?provider=<?php echo $service['provider_id']; ?>&service_id=<?php echo $service['id']; ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-calendar-check"></i> Book Now
                                </a>
                            <?php elseif ($_SESSION['role'] === 'service_provider'): ?>
                                <a href="provider-messages.php?service_id=<?php echo $service['id']; ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-comments"></i> View Messages
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Login to Book
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($other_services)): ?>
                <div class="card">
                    <div class="card-header">
                        <h4>Other Services by <?php echo htmlspecialchars($service['provider_name']); ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($other_services as $other): ?>
                                <div class="col-md-4">
                                    <div class="card h-100">
                                        <?php if ($other['image']): ?>
                                            <img src="<?php echo htmlspecialchars($other['image']); ?>" class="card-img-top" alt="Service Image">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($other['title']); ?></h5>
                                            <p class="card-text">R<?php echo number_format($other['price'], 2); ?></p>
                                            <a href="view-service.php?id=<?php echo $other['id']; ?>" class="btn btn-outline-primary">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>About the Provider</h4>
                </div>
                <div class="card-body">
                    <h5><?php echo htmlspecialchars($service['provider_name']); ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($service['provider_email']); ?></p>
                    
                    <?php if (isLoggedIn()): ?>
                        <?php if ($_SESSION['role'] === 'customer'): ?>
                            <a href="customer-messages.php?provider=<?php echo $service['provider_id']; ?>&service_id=<?php echo $service['id']; ?>" 
                               class="btn btn-primary w-100">
                                <i class="fas fa-calendar-check"></i> Book Now
                            </a>
                        <?php elseif ($_SESSION['role'] === 'service_provider'): ?>
                            <a href="provider-messages.php?service_id=<?php echo $service['id']; ?>" 
                               class="btn btn-primary w-100">
                                <i class="fas fa-comments"></i> View Messages
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt"></i> Login to Book
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<?php if (isLoggedIn() && $_SESSION['user_type'] === 'customer'): ?>
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Contact Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 