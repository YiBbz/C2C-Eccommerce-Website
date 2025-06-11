<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Booking Details</h1>

    <div v-if="booking" class="bg-white shadow rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4">Booking #{{ booking.id }}</h2>
      <p><strong>Service:</strong> {{ booking.service ? booking.service.title : 'N/A' }}</p>
      <p><strong>Provider:</strong> {{ booking.service && booking.service.provider && booking.service.provider.user ? booking.service.provider.user.name : 'N/A' }}</p>
      <p><strong>Customer:</strong> {{ booking.customer ? booking.customer.name : 'N/A' }}</p>
      <p><strong>Status:</strong> {{ booking.status }}</p>
      <p><strong>Date:</strong> {{ booking.booking_date ? new Date(booking.booking_date).toLocaleString() : 'N/A' }}</p>
      <p><strong>Amount:</strong> {{ booking.total_amount ? '$' + booking.total_amount : 'Negotiate in chat' }}</p>
      
      <!-- Chat interface -->
      <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Chat</h3>
        <BookingChat :booking="booking" />
      </div>
    </div>
    <div v-else>
      <p>Booking not found.</p>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';
import BookingChat from '@/Components/Chat/BookingChat.vue';

const props = defineProps({
    booking: Object,
})

</script>

<style scoped>
/* Add styling here if needed */
</style> 