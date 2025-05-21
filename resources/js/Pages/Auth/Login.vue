<template>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
      <h2 class="text-2xl font-bold mb-4">Login</h2>
      <form @submit.prevent="submit">
        <input v-model="form.email" type="email" placeholder="Email" class="input" required />
        <input v-model="form.password" type="password" placeholder="Password" class="input" required />
        <button type="submit" class="btn" :disabled="form.processing">Login</button>
        <p v-if="form.errors.email" class="text-red-500 mt-2">{{ form.errors.email }}</p>
        <p v-if="form.errors.password" class="text-red-500 mt-2">{{ form.errors.password }}</p>
      </form>
    </div>
  </template>
  
  <script setup>
  import { useForm } from '@inertiajs/vue3'
  
  const form = useForm({
    email: '',
    password: '',
  })
  
  const submit = () => {
    form.post(route('login'), {
      onSuccess: () => {
        form.reset('password')
      },
    })
  }
  </script>
  
  <style scoped>
  .input {
    display: block;
    margin-bottom: 10px;
    padding: 8px;
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
  }
  .btn {
    background-color: #4f46e5;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
  }
  .btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }
  </style>