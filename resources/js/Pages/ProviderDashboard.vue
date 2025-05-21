
<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Service Provider Dashboard</h1>
    <ServiceList />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import ServiceList from '@/Components/Services/ServiceList.vue'

const user = ref({})
const services = ref([])

onMounted(async () => {
  const userRes = await axios.get('/api/user')
  user.value = userRes.data

  const serviceRes = await axios.get('/api/services')
  services.value = serviceRes.data
})
</script>
