<template>
  <nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <Link :href="route('welcome')" class="text-2xl font-bold text-indigo-700">Mosomo</Link>
        </div>
        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center space-x-6">
          <Link :href="route('welcome')" class="nav-link">Home</Link>
          <Link href="/products" class="nav-link">Services</Link>
          <Link href="/about" class="nav-link">About</Link>
          <Link href="/contact" class="nav-link">Contact</Link>
        </div>
        <!-- Right Side -->
        <div class="hidden md:flex items-center space-x-4">
          <template v-if="isAuthenticated">
            <!-- Profile Dropdown -->
            <div class="relative" @mouseleave="dropdownOpen = false">
              <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 nav-link focus:outline-none">
                <span class="font-semibold">{{ userName || 'Profile' }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-44 bg-white border rounded shadow-lg z-50">
                <Link :href="route('profile.edit')" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</Link>
                <Link :href="dashboardUrl" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Dashboard</Link>
                <form @submit.prevent="logout">
                  <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">Logout</button>
                </form>
              </div>
            </div>
          </template>
          <template v-else>
            <Link :href="route('login')" class="nav-link">Login</Link>
            <Link :href="route('register')" class="nav-link">Register</Link>
          </template>
        </div>
        <!-- Mobile menu button -->
        <div class="md:hidden flex items-center">
          <button @click="mobileOpen = !mobileOpen" class="text-gray-700 hover:text-indigo-700 focus:outline-none">
            <svg v-if="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
    <!-- Mobile Menu -->
    <div v-if="mobileOpen" class="md:hidden px-4 pb-4">
      <div class="flex flex-col space-y-2">
        <Link :href="route('welcome')" class="nav-link">Home</Link>
        <Link href="/products" class="nav-link">Services</Link>
        <Link href="/about" class="nav-link">About</Link>
        <Link href="/contact" class="nav-link">Contact</Link>
        <template v-if="isAuthenticated">
          <Link :href="route('profile.edit')" class="nav-link">Profile</Link>
          <Link :href="dashboardUrl" class="nav-link">Dashboard</Link>
          <form @submit.prevent="logout">
            <button type="submit" class="nav-link text-red-600">Logout</button>
          </form>
        </template>
        <template v-else>
          <Link :href="route('login')" class="nav-link">Login</Link>
          <Link :href="route('register')" class="nav-link">Register</Link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const isAuthenticated = computed(() => !!user.value)
const userName = computed(() => user.value?.name)

const userRole = computed(() => user.value?.role)
const dropdownOpen = ref(false)
const mobileOpen = ref(false)

const dashboardUrl = computed(() => {
  switch (userRole.value) {
    case 'admin':
      return route('admin.dashboard')
    case 'provider':
      return route('provider.dashboard')
    case 'customer':
      return route('customer.dashboard')
    default:
      return route('dashboard')
  }
})

const logout = () => {
  router.post(route('logout'))
}

// Optional: Close dropdown on outside click
onMounted(() => {
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.relative')) {
      dropdownOpen.value = false
    }
  })
})
</script>

<style scoped>
.nav-link {
  @apply px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-indigo-700 hover:bg-gray-50 transition;
}
</style>