<template>
    <div class="p-6">
      <h2 class="text-xl font-bold mb-4">My Services</h2>
      
      <!-- Content -->
      <div>
        <ServiceForm @service-saved="$emit('service-saved')" />
  
        <div v-if="services && services.length > 0" class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <ServiceItem
            v-for="service in services"
            :key="service.id"
            :service="service"
            @updated="$emit('service-updated')"
            @deleted="$emit('service-deleted')"
          />
        </div>
        <p v-else class="text-gray-500 text-center py-4">No services found.</p>
      </div>
    </div>
</template>
  
<script setup>
import ServiceItem from './ServiceItem.vue'
import ServiceForm from './ServiceForm.vue'
import { defineProps, defineEmits } from 'vue';

// Define the services prop received from the parent (ProviderDashboard.vue)
const props = defineProps({
    services: {
        type: Array,
        default: () => [] // Provide a default empty array to avoid errors if services is not passed
    }
});

// Define emits to let the parent know when services are saved, updated, or deleted
const emit = defineEmits(['service-saved', 'service-updated', 'service-deleted']);

// Removed local state and fetch logic as services are passed via prop
</script>
  
<style scoped>
/* Add styling here if needed */
</style>
  