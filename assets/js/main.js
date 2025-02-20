$(document).ready(function() {
    // Search functionality
    $('.search-box button').click(function() {
        const searchTerm = $('.search-box input').val();
        if(searchTerm.trim()) {
            window.location.href = `${BASE_URL}/services.php?search=${encodeURIComponent(searchTerm)}`;
        }
    });

    // Category card hover effect
    $('.category-card').hover(
        function() { $(this).addClass('shadow-lg'); },
        function() { $(this).removeClass('shadow-lg'); }
    );

    // Service card click handler
    $('.service-card').click(function() {
        const serviceId = $(this).data('service-id');
        window.location.href = `${BASE_URL}/service-details.php?id=${serviceId}`;
    });
});