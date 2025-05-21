<template>
    <div class="border p-4 mb-4 rounded">
      <h3 class="text-lg font-semibold">{{ service.title }}</h3>
      <p>{{ service.description }}</p>
      <p class="text-sm text-gray-600">Price: R{{ service.price }}</p>
      <div class="mt-2">
        <button @click="editMode = !editMode" class="btn">Edit</button>
        <button @click="deleteService" class="btn bg-red-500 ml-2">Delete</button>
      </div>
  
      <div v-if="editMode" class="mt-4">
        <input v-model="form.title" class="input" />
        <textarea v-model="form.description" class="input"></textarea>
        <input v-model.number="form.price" type="number" class="input" />
        <button @click="updateService" class="btn">Update</button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import axios from 'axios'
  
  const props = defineProps(['service'])
  const emit = defineEmits(['updated', 'deleted'])
  
  const editMode = ref(false)
  const form = ref({ ...props.service })
  
  const updateService = async () => {
    await axios.put(`/api/services/${props.service.id}`, form.value)
    editMode.value = false
    emit('updated')
  }
  
  const deleteService = async () => {
    await axios.delete(`/api/services/${props.service.id}`)
    emit('deleted')
  }
  </script>
  
  <style scoped>
  .btn {
    background-color: #4f46e5;
    color: white;
    padding: 6px 12px;
    border: none;
    cursor: pointer;
  }
  .input {
    display: block;
    margin-bottom: 8px;
    padding: 6px;
    width: 100%;
  }
  </style>
  