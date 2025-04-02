// Custom JavaScript for ServiceHub

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Service card hover effect
    $('.service-card').hover(
        function() {
            $(this).find('.btn-primary').removeClass('btn-sm btn-primary').addClass('btn-sm btn-dark');
        },
        function() {
            $(this).find('.btn-dark').removeClass('btn-sm btn-dark').addClass('btn-sm btn-primary');
        }
    );

    // Add to cart functionality
    $('.add-to-cart').on('click', function(e) {
        e.preventDefault();
        
        // Get service information
        var serviceId = $(this).data('id');
        var serviceTitle = $(this).data('title');
        var servicePrice = $(this).data('price');
        
        // Send AJAX request to add item to cart
        $.ajax({
            url: 'add-to-cart.php',
            type: 'POST',
            data: {
                id: serviceId,
                title: serviceTitle,
                price: servicePrice
            },
            success: function(response) {
                // Parse the response
                var result = JSON.parse(response);
                
                if (result.success) {
                    // Update cart count
                    $('.cart-count').text(result.cartCount);
                    
                    // Show success message
                    showToast('Success', serviceTitle + ' added to cart!', 'success');
                } else {
                    // Show error message
                    showToast('Error', result.message, 'danger');
                }
            },
            error: function() {
                showToast('Error', 'Failed to add service to cart. Please try again.', 'danger');
            }
        });
    });

    // Custom toast notification function
    function showToast(title, message, type) {
        // Create toast HTML
        var toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}:</strong> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        // Add toast to container
        $('#toast-container').append(toastHtml);
        
        // Initialize and show the toast
        var toastElement = document.querySelector('#toast-container .toast:last-child');
        var toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 3000
        });
        toast.show();
        
        // Remove toast after it's hidden
        $(toastElement).on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }