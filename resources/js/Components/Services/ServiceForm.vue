<template>
    <form @submit.prevent="submitForm" class="mb-6">
      <input v-model="form.title" type="text" placeholder="Title" class="input" required />
      <textarea v-model="form.description" placeholder="Description" class="input" required></textarea>
      <input v-model.number="form.price" type="number" placeholder="Price" class="input" required />
      <button type="submit" class="btn">Save Service</button>
    </form>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import axios from 'axios'
  import AppLayout from '@/Layouts/AppLayout.vue'
  
  const form = ref({
    title: '',
    description: '',
    price: 0,
  })
  
  const emit = defineEmits(['service-saved'])
  
  const submitForm = async () => {
    await axios.post('/api/services', form.value)
    form.value = { title: '', description: '', price: 0 }
    emit('service-saved')
  }
  </script>
  
  <style scoped>
  .input {
    display: block;
    margin-bottom: 10px;
    padding: 8px;
    width: 100%;
  }
  .btn {
    background-color: #4f46e5;
    color: white;
    padding: 8px 16px;
    border: none;
    cursor: pointer;
  }
  </style>
  