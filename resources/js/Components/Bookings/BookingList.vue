<template>
  <div>
    <div v-if="bookings && bookings.length > 0">
      <div v-for="booking in bookings" :key="booking.id" class="border p-4 mb-4 rounded">
        <p><strong>Booking ID:</strong> {{ booking.id }}</p>
        <p><strong>Service:</strong> {{ booking.service ? booking.service.name : 'N/A' }}</p>
        <p><strong>Customer:</strong> {{ booking.user ? booking.user.name : 'N/A' }}</p>
        <p><strong>Date:</strong> {{ booking.date }}</p>
        <p><strong>Status:</strong> {{ booking.status }}</p>
        <!-- Add more booking details as needed -->
      </div>
    </div>
    <div v-else>
      <p>No bookings found.</p>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  bookings: Array,
});
</script>

<style scoped>
/* Add styling here if needed */
</style> 