<template>
  <div>
    <div v-if="messages && messages.length > 0">
      <div v-for="message in messages" :key="message.id" class="border p-3 mb-3 rounded">
        <p><strong>From:</strong> {{ message.user ? message.user.name : 'N/A' }}</p>
        <p><strong>Message:</strong> {{ message.content }}</p>
        <!-- Add more message details as needed, e.g., timestamp -->
      </div>
    </div>
    <div v-else>
      <p>No messages found.</p>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  messages: Array,
});
</script>

<style scoped>
/* Add styling here if needed */
</style> 