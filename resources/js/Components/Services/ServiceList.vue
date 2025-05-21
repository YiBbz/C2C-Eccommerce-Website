<template>
    <div class="p-6">
      <h2 class="text-xl font-bold mb-4">My Services</h2>
      
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ error }}</span>
        <button @click="fetchServices" class="mt-2 text-sm text-red-600 hover:text-red-800">
          Try Again
        </button>
      </div>

      <!-- Content -->
      <template v-else>
        <ServiceForm @service-saved="fetchServices" />
  
        <div v-if="services.length" class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <ServiceItem
            v-for="service in services"
            :key="service.id"
            :service="service"
            @updated="fetchServices"
            @deleted="fetchServices"
          />
        </div>
        <p v-else class="text-gray-500 text-center py-4">No services found.</p>
      </template>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  import ServiceItem from './ServiceItem.vue'
  import ServiceForm from './ServiceForm.vue'
  
  const services = ref([])
  const loading = ref(true)
  const error = ref(null)
  
  const fetchServices = async () => {
    loading.value = true
    error.value = null
    
    try {
      const res = await axios.get('/api/services')
      services.value = res.data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch services'
    } finally {
      loading.value = false
    }
  }
  
  onMounted(fetchServices)
  </script>
  