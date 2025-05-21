<template>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
      <h2 class="text-2xl font-bold mb-4">Register</h2>
      <form @submit.prevent="submit">
        <input v-model="form.name" type="text" placeholder="Name" class="input" required />
        <input v-model="form.email" type="email" placeholder="Email" class="input" required />
        <input v-model="form.password" type="password" placeholder="Password" class="input" required />
        <input v-model="form.password_confirmation" type="password" placeholder="Confirm Password" class="input" required />
        <select v-model="form.role" class="input" required>
          <option disabled value="">Select Role</option>
          <option value="customer">Customer</option>
          <option value="provider">Service Provider</option>
        </select>
        <button type="submit" class="btn" :disabled="form.processing">Register</button>
        <p v-if="form.errors.name" class="text-red-500 mt-2">{{ form.errors.name }}</p>
        <p v-if="form.errors.email" class="text-red-500 mt-2">{{ form.errors.email }}</p>
        <p v-if="form.errors.password" class="text-red-500 mt-2">{{ form.errors.password }}</p>
        <p v-if="form.errors.role" class="text-red-500 mt-2">{{ form.errors.role }}</p>
      </form>
    </div>
  </template>
  
  <script setup>
  import { useForm } from '@inertiajs/vue3'
  
  const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
  })
  
  const submit = () => {
    form.post(route('register'), {
      onSuccess: () => {
        form.reset('password', 'password_confirmation')
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