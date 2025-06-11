<template>
  <div class="chat-container">
    <div class="messages-list">
      <div v-for="message in messages" :key="message.id" class="message">
        <strong>{{ message.sender.name }}:</strong> {{ message.message }}
      </div>
    </div>
    <div class="message-input">
      <input 
        v-model="newMessage" 
        @keyup.enter="sendMessage" 
        placeholder="Type your message..."
      >
      <button @click="sendMessage">Send</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    booking: Object,
});

const messages = ref(props.booking.messages || []);
const newMessage = ref('');
const page = usePage();
const user = page.props.auth.user;

const sendMessage = async () => {
    if (newMessage.value.trim() === '') return;

    try {
        const response = await axios.post(route('messages.store', props.booking.id), {
            message: newMessage.value,
        });
        // Add the sent message to the list immediately (optional, can also wait for broadcast)
        // messages.value.push(response.data.message);
        newMessage.value = '';
        // Auto-scroll to the latest message (might need refinement)
        nextTick(() => {
            const messagesList = document.querySelector('.messages-list');
            if (messagesList) {
                messagesList.scrollTop = messagesList.scrollHeight;
            }
        });

    } catch (error) {
        console.error('Error sending message:', error);
        alert('Failed to send message.');
    }
};

// Pusher Integration
let channel;

onMounted(() => {
    // Subscribe to the booking channel
    channel = Echo.private(`booking.${props.booking.id}`);

    channel.listen('MessageSent', (e) => {
        console.log('Message received:', e.message);
        // Only add message if it's not the one sent by the current user (to avoid duplicates)
        if (e.message.sender_id !== user.id) {
             // Fetch the message with sender details if needed, or rely on broadcast data
            messages.value.push({ 
                id: Date.now(), // Temporary ID if full message object is not broadcast
                message: e.message.message,
                sender: { name: 'Other User' } // Placeholder sender name
                 // Or if full message object with sender is broadcast:
                // ...e.message
            });
             nextTick(() => {
                const messagesList = document.querySelector('.messages-list');
                if (messagesList) {
                    messagesList.scrollTop = messagesList.scrollHeight;
                }
            });
        }
    });
});

onUnmounted(() => {
    // Unsubscribe from the channel when the component is unmounted
    if (channel) {
        channel.leave();
    }
});

</script>

<style scoped>
.chat-container {
  display: flex;
  flex-direction: column;
  height: 400px; /* Adjust as needed */
  border: 1px solid #ccc;
  rounded: 8px;
  overflow: hidden;
}

.messages-list {
  flex-grow: 1;
  padding: 10px;
  overflow-y: auto;
}

.message {
  margin-bottom: 10px;
}

.message-input {
  display: flex;
  padding: 10px;
  border-top: 1px solid #ccc;
}

.message-input input {
  flex-grow: 1;
  margin-right: 10px;
  padding: 8px;
  border: 1px solid #ccc;
  rounded: 4px;
}

.message-input button {
  padding: 8px 15px;
  background-color: #007bff;
  color: white;
  border: none;
  rounded: 4px;
  cursor: pointer;
}

.message-input button:hover {
  background-color: #0056b3;
}
</style> 