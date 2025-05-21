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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const stats = ref({
  users: 0,
  services: 0,
  providers: 0,
})

onMounted(async () => {
  const res = await axios.get('/api/admin/stats')
  stats.value = res.data
})
</script>
