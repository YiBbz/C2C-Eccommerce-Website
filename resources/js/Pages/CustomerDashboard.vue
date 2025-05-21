<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Customer Dashboard</h1>
    <p>Welcome, {{ user.name }}! Browse and manage your hired services here.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const user = ref({})

onMounted(async () => {
  const response = await axios.get('/api/user')
  user.value = response.data
})
</script>
