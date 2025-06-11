<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>
    <p>Welcome, Admin! Here are some quick stats:</p>

    <div class="grid grid-cols-3 gap-4 mt-6">
      <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold">Total Users</h2>
        <p class="text-2xl">{{ stats.users }}</p>
      </div>
      <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold">Total Services</h2>
        <p class="text-2xl">{{ stats.services }}</p>
      </div>
      <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold">Active Providers</h2>
        <p class="text-2xl">{{ stats.providers }}</p>
      </div>
    </div>

    <!-- Display Users in a table -->
    <h2 class="text-xl font-semibold mt-6 mb-2">All Users</h2>
    <div v-if="users && users.length > 0">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ user.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ user.role }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ new Date(user.created_at).toLocaleDateString() }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-else>
      <p>No users found.</p>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, defineProps } from 'vue'
import axios from 'axios'

const stats = ref({
  users: 0,
  services: 0,
  providers: 0,
})

const props = defineProps({
  users: Array,
})

onMounted(async () => {
  // Assuming you have an API endpoint for admin stats
  try {
    const res = await axios.get('/api/admin/stats')
    stats.value = res.data
  } catch (e) {
    console.error('Failed to load admin stats:', e)
  }
})
</script>

<style scoped>
/* Add styling here if needed */
</style>
