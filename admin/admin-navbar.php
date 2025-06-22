<?php
// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<nav class="bg-blue-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="index.php" class="text-black font-bold text-xl">Admin Dashboard</a>
                </div>
                <div class="hidden sm:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="index.php" class="text-blue-800 hover:bg-blue-100 hover:text-blue-900 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <a href="users.php" class="text-gray-800 hover:bg-blue-100 hover:text-blue-900 px-3 py-2 rounded-md text-sm font-medium">Users</a>
                        <a href="services.php" class="text-gray-800 hover:bg-blue-100 hover:text-blue-900 px-3 py-2 rounded-md text-sm font-medium">Services</a>
                        <a href="bookings.php" class="text-gray-800 hover:bg-blue-100 hover:text-blue-900 px-3 py-2 rounded-md text-sm font-medium">Bookings</a>
                        <a href="reviews.php" class="text-gray-800 hover:bg-blue-100 hover:text-blue-900 px-3 py-2 rounded-md text-sm font-medium">Reviews</a>
                        <a href="settings.php" class="text-gray-800 hover:bg-blue-100 hover:text-blue-900 px-3 py-2 rounded-md text-sm font-medium">Settings</a>
                    </div>
                </div>
            </div>
            <div class="hidden sm:block">
                <div class="ml-4 flex items-center md:ml-6">
                    <div class="relative ml-3">
                        <div>
                            <button type="button" class="max-w-xs bg-gray-900 rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-white" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <span class="text-white px-3 py-2 font-medium">Admin</span>
                            </button>
                        </div>
                        <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" id="user-menu">
                            <a href="../profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">View Profile</a>
                            <a href="../logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="-mr-2 flex sm:hidden">
                <button type="button" class="bg-gray-800 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white" aria-controls="mobile-menu" aria-expanded="false" id="mobile-menu-button">
                    <span class="sr-only">Open main menu</span>
                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="hidden lg:hidden w-full" id="mobile-menu">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="index.php" class="text-gray-700 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
            <a href="users.php" class="text-gray-700 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Users</a>
            <a href="services.php" class="text-gray-700 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Services</a>
            <a href="bookings.php" class="text-gray-700 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Bookings</a>
            <a href="reviews.php" class="text-gray-700 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Reviews</a>
            <a href="settings.php" class="text-gray-700 hover:bg-gray-700 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Settings</a>
        </div>
        <div class="pt-4 pb-3 border-t border-gray-700">
            <div class="flex items-center px-5">
                <div class="flex-shrink-0">
                    <span class="text-gray-800">Admin</span>
                </div>
            </div>
            <div class="mt-3 px-2 space-y-1">
                <a href="../profile.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-white hover:bg-gray-700">View Profile</a>
                <a href="../logout.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-white hover:bg-gray-700">Logout</a>
            </div>
        </div>
    </div>
</nav>

<script>
// Toggle mobile menu
document.getElementById('mobile-menu-button').addEventListener('click', function() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
});

// Toggle user menu
document.getElementById('user-menu-button').addEventListener('click', function() {
    document.getElementById('user-menu').classList.toggle('hidden');
});
</script> 