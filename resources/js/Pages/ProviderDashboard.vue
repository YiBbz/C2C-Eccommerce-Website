<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Service Provider Dashboard</h1>
    <!-- Display Services -->
    <div class="flex justify-between items-center mb-2">
      <h2 class="text-xl font-semibold">My Services</h2>
      <Link :href="route('services.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Add New Service
      </Link>
    </div>
    <ServiceList :services="providerData.services" />

    <!-- Display Bookings -->
    <h2 class="text-xl font-semibold mt-6 mb-2">My Bookings</h2>
    <!-- Assuming bookings are loaded with services -->
    <BookingList :bookings="providerData.services.flatMap(service => service.bookings)" />
    
    <!-- Display Messages -->
    <h2 class="text-xl font-semibold mt-6 mb-2">My Messages</h2>
    <!-- Assuming messages are loaded with services and bookings -->
    <MessageList 
      :messages="providerData.services.flatMap(service => service.messages).concat(providerData.services.flatMap(service => service.bookings.flatMap(booking => booking.messages)))" 
    />

  </div>
</template>

<script setup>
import { defineProps } from 'vue'
import { Link } from '@inertiajs/vue3';
import ServiceList from '@/Components/Services/ServiceList.vue'
import BookingList from '@/Components/Bookings/BookingList.vue'
import MessageList from '@/Components/Messages/MessageList.vue'

const props = defineProps({
    providerData: Object,
})

// Removed onMounted hook as data is now passed via props

</script>

<style scoped>
/* Add styling here if needed */
</style>