<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'config/pusher.php';

// Ensure user is logged in and is a service provider
requireServiceProvider();

$user_id = $_SESSION['user_id'];

// Initialize database connection
$conn = getDB();

// Get booking ID from URL parameter
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

// Get conversation partner ID
$partner_id = isset($_GET['customer']) ? intval($_GET['customer']) : 0;
$booking = null;

if ($booking_id) {
    // Fetch booking details
    try {
        $stmt = $conn->prepare("
            SELECT b.*, s.title as service_title, s.price as service_price, s.image as service_image,
                   cust.full_name as customer_name, cust.id as customer_id,
                   prov.full_name as provider_name, prov.id as provider_id
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            JOIN users cust ON b.customer_id = cust.id
            JOIN users prov ON b.provider_id = prov.id
            WHERE b.id = :booking_id AND b.provider_id = :user_id
        ");
        $stmt->execute([':booking_id' => $booking_id, ':user_id' => $user_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            $partner_id = $booking['customer_id'];
        } else {
            $booking_id = 0;
        }
    } catch (PDOException $e) {
        error_log("Error fetching booking details: " . $e->getMessage());
        $booking_id = 0;
    }
}

// Get all conversations for this provider
$stmt = $conn->prepare("
    SELECT DISTINCT 
        b.customer_id as partner_id,
        u.full_name as partner_name,
        u.profile_picture as partner_image,
        b.id as booking_id,
        b.status as booking_status,
        b.completion_status,
        s.title as service_title,
        (SELECT message FROM messages 
         WHERE booking_id = b.id
         ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM messages 
         WHERE booking_id = b.id
         ORDER BY created_at DESC LIMIT 1) as last_message_time,
        (SELECT COUNT(*) FROM messages 
         WHERE booking_id = b.id AND receiver_id = :user_id AND is_read = 0) as unread_count
    FROM bookings b
    JOIN users u ON b.customer_id = u.id
    JOIN services s ON b.service_id = s.id
    WHERE b.provider_id = :user_id
    ORDER BY last_message_time DESC
");
$stmt->execute([':user_id' => $user_id]);
$conversations = $stmt->fetchAll();

// Get messages for selected conversation
$messages = [];
if ($partner_id && $booking_id) {
    $stmt = $conn->prepare("
        SELECT m.*, u.full_name as sender_name, u.profile_picture as sender_image
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.booking_id = :booking_id
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([':booking_id' => $booking_id]);
    $messages = $stmt->fetchAll();

    // Mark messages as read
    $stmt = $conn->prepare("
        UPDATE messages 
        SET is_read = 1 
        WHERE booking_id = :booking_id AND receiver_id = :user_id AND is_read = 0
    ");
    $stmt->execute([':booking_id' => $booking_id, ':user_id' => $user_id]);
}

// Get partner details
$partner = null;
if ($partner_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $partner_id]);
    $partner = $stmt->fetch();
}

// Prepare partner details for JavaScript
$partner_js = $partner ? json_encode([
    'profile_picture' => $partner['profile_picture'] ?? null,
    'full_name' => $partner['full_name'] ?? $partner['username'] ?? 'User'
]) : 'null';

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="bg-grey text-white py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4">Service Provider Messages</h1>
        <p class="text-xl text-purple-200">Manage your service bookings and customer communications</p>
    </div>
</section>

<!-- Messages Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Conversations List -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-700">
                        <h2 class="text-xl font-bold text-white">Bookings & Conversations</h2>
                    </div>
                    <div class="divide-y divide-gray-700">
                        <?php foreach ($conversations as $conv): ?>
                            <a href="?customer=<?php echo $conv['partner_id']; ?>&booking_id=<?php echo $conv['booking_id']; ?>" 
                               class="block p-4 hover:bg-gray-700 transition duration-150 <?php echo $booking_id == $conv['booking_id'] ? 'bg-purple-900' : ''; ?>">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <?php if ($conv['partner_image']): ?>
                                            <img src="<?php echo htmlspecialchars($conv['partner_image']); ?>" 
                                                 class="w-12 h-12 rounded-full object-cover border-2 border-purple-500" 
                                                 alt="<?php echo htmlspecialchars($conv['partner_name']); ?>">
                                        <?php else: ?>
                                            <div class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center">
                                                <i class="fas fa-user text-purple-500"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-center">
                                            <p class="text-sm font-medium text-white truncate">
                                                <?php echo htmlspecialchars($conv['partner_name']); ?>
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                <?php echo getTimeAgo($conv['last_message_time']); ?>
                                            </p>
                                        </div>
                                        <p class="text-sm text-gray-300 truncate">
                                            <?php echo htmlspecialchars($conv['service_title']); ?>
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            Status: <?php echo ucfirst($conv['booking_status']); ?>
                                            <?php if ($conv['completion_status']): ?>
                                                (<?php echo ucfirst($conv['completion_status']); ?>)
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <?php if ($conv['unread_count'] > 0): ?>
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-purple-600 rounded-full">
                                                <?php echo $conv['unread_count']; ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="lg:col-span-2">
                <?php if ($partner && $booking): ?>
                    <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-700">
                            <div class="flex items-center space-x-4">
                                <?php if ($partner['profile_picture']): ?>
                                    <img src="<?php echo htmlspecialchars($partner['profile_picture']); ?>" 
                                         class="w-10 h-10 rounded-full object-cover border-2 border-purple-500" 
                                         alt="<?php echo htmlspecialchars($partner['full_name'] ?? $partner['username']); ?>">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                                        <i class="fas fa-user text-purple-500"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">
                                        <?php echo htmlspecialchars($partner['full_name'] ?? $partner['username']); ?>
                                    </h3>
                                    <p class="text-sm text-gray-400">
                                        Booking Status: <?php echo ucfirst($booking['status']); ?>
                                        <?php if ($booking['completion_status']): ?>
                                            (<?php echo ucfirst($booking['completion_status']); ?>)
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <?php if ($booking): ?>
                            <!-- Booking Details Box -->
                            <div class="p-6 bg-purple-900/50 border-b border-purple-800">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <?php if ($booking['service_image']): ?>
                                            <img src="<?php echo htmlspecialchars($booking['service_image']); ?>" 
                                                 class="w-16 h-16 rounded-lg object-cover mr-4" 
                                                 alt="<?php echo htmlspecialchars($booking['service_title']); ?>">
                                        <?php else: ?>
                                            <div class="w-16 h-16 rounded-lg bg-gray-700 flex items-center justify-center mr-4">
                                                <i class="fas fa-briefcase text-purple-500 text-2xl"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-white mb-1">
                                            <?php echo htmlspecialchars($booking['service_title']); ?>
                                        </h4>
                                        <p class="text-sm text-purple-200">
                                            Date: <?php echo date('M d, Y h:i A', strtotime($booking['booking_date'])); ?>
                                        </p>
                                        <p class="text-sm text-purple-200">
                                            Amount: R<?php echo number_format($booking['total_amount'], 2); ?>
                                        </p>
                                        <p class="text-sm text-purple-200">
                                            Payment Method: <?php echo ucfirst($booking['payment_method']); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Booking Actions -->
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <div class="mt-4 border-t border-purple-800 pt-4">
                                        <h5 class="text-sm font-semibold text-white mb-3">Booking Actions:</h5>
                                        <div class="flex space-x-4">
                                            <button onclick="handleBookingAction(<?php echo $booking['id']; ?>, 'accepted')" 
                                                    class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">
                                                Accept
                                            </button>
                                            <button onclick="handleBookingAction(<?php echo $booking['id']; ?>, 'rejected')" 
                                                    class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700">
                                                Reject
                                            </button>
                                        </div>
                                    </div>
                                <?php elseif ($booking['status'] === 'accepted'): ?>
                                    <div class="mt-4 border-t border-purple-800 pt-4">
                                        <h5 class="text-sm font-semibold text-white mb-3">Service Actions:</h5>
                                        <div class="flex space-x-4">
                                            <?php if ($booking['completion_status'] === 'not_started'): ?>
                                                <button onclick="handleCompletionAction(<?php echo $booking['id']; ?>, 'in_progress')" 
                                                        class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                                                    Start Service
                                                </button>
                                            <?php elseif ($booking['completion_status'] === 'in_progress'): ?>
                                                <button onclick="handleCompletionAction(<?php echo $booking['id']; ?>, 'completed')" 
                                                        class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">
                                                    Mark as Completed
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Messages List -->
                        <div class="h-96 overflow-y-auto p-6 space-y-4" id="messages-container">
                            <?php foreach ($messages as $message): ?>
                                <div class="flex <?php echo $message['sender_id'] == $user_id ? 'justify-end' : 'justify-start'; ?>">
                                    <div class="flex items-end space-x-2">
                                        <?php if ($message['sender_id'] != $user_id): ?>
                                            <div class="flex-shrink-0">
                                            <?php if ($message['sender_image']): ?>
                                                <img src="<?php echo htmlspecialchars($message['sender_image']); ?>" 
                                                         class="w-8 h-8 rounded-full object-cover border-2 border-purple-500" 
                                                         alt="<?php echo htmlspecialchars($message['sender_name']); ?>">
                                            <?php else: ?>
                                                    <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center">
                                                        <i class="fas fa-user text-purple-500 text-sm"></i>
                                                </div>
                                            <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="max-w-lg">
                                            <div class="px-4 py-2 rounded-lg <?php echo $message['sender_id'] == $user_id ? 'bg-purple-600 text-white' : 'bg-gray-700 text-white'; ?>">
                                                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                            </div>
                                            <p class="text-xs text-gray-400 mt-1">
                                                <?php echo date('M d, Y h:i A', strtotime($message['created_at'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Message Input -->
                        <div class="p-6 border-t border-gray-700">
                            <form id="message-form" class="flex space-x-4">
                                <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                                <input type="hidden" name="receiver_id" value="<?php echo $partner_id; ?>">
                                <div class="flex-1">
                                    <textarea name="message" rows="1" required
                                              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white resize-none"
                                              placeholder="Type your message..."></textarea>
                                </div>
                                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition duration-300">
                                    Send
                                </button>
                            </form>
                            </div>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-800 rounded-xl shadow-lg p-8 text-center">
                        <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comments text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">No Booking Selected</h3>
                        <p class="text-gray-400">Select a booking from the list to view details and chat</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Modify Booking Modal -->
<div id="modifyBookingModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900">Modify Booking</h3>
            <form id="modifyBookingForm" class="mt-4 space-y-4">
                <input type="hidden" id="modifyBookingId" name="booking_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">New Date</label>
                    <input type="datetime-local" id="newBookingDate" name="new_date" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">New Amount</label>
                    <input type="number" id="newAmount" name="new_amount" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModifyBookingModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md text-sm hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                        Send Modification Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
// Initialize Pusher
const pusher = new Pusher('<?php echo PUSHER_APP_KEY; ?>', {
    cluster: '<?php echo PUSHER_APP_CLUSTER; ?>'
});

// Subscribe to the channel
const channel = pusher.subscribe('booking-<?php echo $booking_id; ?>');

// Listen for new messages
channel.bind('new-message', function(data) {
    const messagesContainer = document.getElementById('messages-container');
    const messageForm = document.getElementById('message-form');
    const messageInput = messageForm.querySelector('textarea');
    
    // Add the new message to the chat
    const messageDiv = document.createElement('div');
    messageDiv.className = 'flex justify-start';
    messageDiv.innerHTML = `
        <div class="flex items-end space-x-2">
            <div class="flex-shrink-0">
                ${data.sender_image ? 
                    `<img src="${data.sender_image}" class="w-8 h-8 rounded-full object-cover border-2 border-purple-500" alt="${data.sender_name}">` :
                    `<div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-user text-purple-500 text-sm"></i>
                    </div>`
                }
            </div>
            <div class="max-w-lg">
                <div class="px-4 py-2 rounded-lg bg-gray-700 text-white">
                    ${data.message.replace(/\n/g, '<br>')}
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    ${new Date(data.created_at).toLocaleString()}
                </p>
            </div>
        </div>
    `;
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // Clear the input
    messageInput.value = '';
});

// Handle message form submission
document.getElementById('message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('process-message.php', {
            method: 'POST',
        body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
            const messagesContainer = document.getElementById('messages-container');
            const messageForm = document.getElementById('message-form');
            const messageInput = messageForm.querySelector('textarea');
            
            // Add the sent message to the chat
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex justify-end';
            messageDiv.innerHTML = `
                <div class="flex items-end space-x-2">
                    <div class="max-w-lg">
                        <div class="px-4 py-2 rounded-lg bg-purple-600 text-white">
                            ${data.message.replace(/\n/g, '<br>')}
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            ${new Date(data.created_at).toLocaleString()}
                </p>
            </div>
        </div>
    `;
            messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Clear the input
            messageInput.value = '';
        }
    });
});

function handleBookingAction(bookingId, action) {
    let confirmMessage = '';
    switch(action) {
        case 'accepted':
            confirmMessage = 'Are you sure you want to accept this booking?';
            break;
        case 'rejected':
            confirmMessage = 'Are you sure you want to reject this booking?';
            break;
        case 'completed':
            confirmMessage = 'Are you sure you want to mark this service as completed?';
            break;
        default:
            confirmMessage = 'Are you sure you want to proceed?';
    }

    if (confirm(confirmMessage)) {
        fetch('process-booking-action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                booking_id: bookingId,
                action: action
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let successMessage = '';
                switch(action) {
                    case 'accepted':
                        successMessage = 'Booking accepted successfully!';
                        break;
                    case 'rejected':
                        successMessage = 'Booking rejected successfully!';
                        break;
                    case 'completed':
                        successMessage = 'Service marked as completed!';
                        if (data.payment_required) {
                            window.location.href = data.payment_url;
                            return;
                        }
                        break;
                    default:
                        successMessage = 'Action completed successfully!';
                }
                alert(successMessage);
                window.location.reload();
            } else {
                console.error(`Failed to ${action} booking:`, data.message);
                alert(`Failed to ${action} booking: ` + data.message);
            }
        })
        .catch(error => {
            console.error(`Error processing ${action} booking:`, error);
            alert(`An error occurred while processing your ${action} request.`);
        });
    }
}

function handleCompletionAction(bookingId, action) {
    let confirmMessage = '';
    switch(action) {
        case 'in_progress':
            confirmMessage = 'Are you sure you want to start this service?';
            break;
        case 'completed':
            confirmMessage = 'Are you sure you want to mark this service as completed?';
            break;
        default:
            confirmMessage = 'Are you sure you want to proceed?';
    }

    if (confirm(confirmMessage)) {
        fetch('process-completion-action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                booking_id: bookingId,
                action: action
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let successMessage = '';
                switch(action) {
                    case 'in_progress':
                        successMessage = 'Service started successfully!';
                        break;
                    case 'completed':
                        successMessage = 'Service marked as completed!';
                        if (data.payment_required) {
                            window.location.href = data.payment_url;
                            return;
                        }
                        break;
                    default:
                        successMessage = 'Action completed successfully!';
                }
                alert(successMessage);
                window.location.reload();
            } else {
                console.error(`Failed to update completion status:`, data.message);
                alert(`Failed to update completion status: ` + data.message);
            }
        })
        .catch(error => {
            console.error(`Error processing completion action:`, error);
            alert(`An error occurred while processing your request.`);
        });
    }
}

function showModifyBookingModal(bookingId) {
    document.getElementById('modifyBookingModal').classList.remove('hidden');
    document.getElementById('modifyBookingId').value = bookingId;
}

function closeModifyBookingModal() {
    document.getElementById('modifyBookingModal').classList.add('hidden');
}

document.getElementById('modifyBookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const bookingId = document.getElementById('modifyBookingId').value;
    const newDate = document.getElementById('newBookingDate').value;
    const newAmount = document.getElementById('newAmount').value;
    
    fetch('process-booking-action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            booking_id: bookingId,
            action: 'modify',
            new_date: newDate,
            new_amount: newAmount
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModifyBookingModal();
            window.location.reload();
        } else {
            alert('Failed to modify booking: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while modifying the booking.');
    });
});
</script> 