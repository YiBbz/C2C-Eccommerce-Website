<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Customer Dashboard</h1>

    <!-- Display Bookings -->
    <h2 class="text-xl font-semibold mt-6 mb-2">My Bookings</h2>
    <div v-if="customerData && customerData.bookings && customerData.bookings.length > 0">
      <div v-for="booking in customerData.bookings" :key="booking.id" class="border p-4 mb-4 rounded">
        <h3 class="text-lg font-medium">Booking ID: {{ booking.id }}</h3>
        <p><strong>Service:</strong> {{ booking.service ? booking.service.name : 'N/A' }}</p>
        <p><strong>Provider:</strong> {{ booking.service && booking.service.provider && booking.service.provider.user ? booking.service.provider.user.name : 'N/A' }}</p>
        <p><strong>Date:</strong> {{ booking.date }}</p>
        <p><strong>Status:</strong> {{ booking.status }}</p>

        <!-- Display messages for this booking -->
        <div v-if="booking.messages && booking.messages.length > 0" class="mt-4">
          <h4 class="text-md font-medium mb-1">Messages:</h4>
          <div v-for="message in booking.messages" :key="message.id" class="border-t mt-2 pt-2">
            <p><strong>{{ message.user ? message.user.name : 'Unknown' }}:</strong> {{ message.content }}</p>
            <!-- Add more message details like timestamp if available -->
          </div>
        </div>
         <div v-else class="mt-4">
          <p>No messages for this booking yet.</p>
        </div>

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
    customerData: Object,
})

</script>

<style scoped>
/* Add styling here if needed */
</style>
