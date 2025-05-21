<template>
  <nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <Link :href="route('welcome')" class="text-xl font-bold text-gray-800">C2C E-commerce</Link>
          </div>
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8" color="blue">
            <Link :href="route('welcome')" class="nav-link">Home</Link>
            <Link href="/products" class="nav-link">Services</Link>
            <Link href="/about" class="nav-link">About</Link>
            <Link href="/contact" class="nav-link">Contact</Link>
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <template v-if="isAuthenticated">
            <Link :href="dashboardUrl" class="nav-link">Dashboard</Link>
            <form @submit.prevent="logout" class="inline">
              <button type="submit" class="btn bg-red-500">Logout</button>
            </form>
          </template>
          <template v-else>
            <Link :href="route('login')" class="nav-link">Login</Link>
            <Link :href="route('register')" class="nav-link">Register</Link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const isAuthenticated = ref(!!localStorage.getItem('token'))
const userRole = ref(localStorage.getItem('userRole'))

const dashboardUrl = computed(() => {
  switch (userRole.value) {
    case 'admin':
      return route('admin.dashboard')
    case 'provider':
      return route('provider.dashboard')
    case 'customer':
      return route('customer.dashboard')
    default:
      return route('welcome')
  }
})

const logout = () => {
  router.post(route('logout'), {}, {
    onSuccess: () => {
      localStorage.removeItem('token')
      localStorage.removeItem('userRole')
      router.visit(route('login'))
    }
  })
}
</script>

<style scoped>
.nav-link {
  @apply px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50;
}

.btn {
  @apply px-4 py-2 text-white rounded hover:opacity-90;
}
</style>