<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Add New Service</h1>

    <form @submit.prevent="submit">
      <div class="mb-4">
        <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
        <input 
          type="text" 
          id="title" 
          v-model="form.title" 
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
          required
        />
        <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
      </div>

      <div class="mb-4">
        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
        <textarea 
          id="description" 
          v-model="form.description" 
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-32"
          required
        ></textarea>
        <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
      </div>

      <div class="mb-4">
        <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price:</label>
        <input 
          type="number" 
          id="price" 
          v-model="form.price" 
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
          step="0.01"
          required
        />
        <div v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</div>
      </div>

      <div class="mb-4">
        <label for="cover_image" class="block text-gray-700 text-sm font-bold mb-2">Cover Image:</label>
        <input 
          type="file" 
          id="cover_image" 
          @change="handleFileUpload"
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
          accept="image/*"
        />
        <div v-if="form.errors.cover_image" class="text-red-500 text-xs mt-1">{{ form.errors.cover_image }}</div>
      </div>

      <div class="flex items-center justify-between">
        <button 
          type="submit" 
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Creating...' : 'Create Service' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  title: '',
  description: '',
  price: '',
  cover_image: null,
});

const handleFileUpload = (event) => {
  form.cover_image = event.target.files[0];
};

const submit = () => {
  form.post(route('services.store'), {
    onSuccess: () => {
      window.location.href = route('provider.dashboard');
    },
    onError: (errors) => {
      console.error('Error creating service:', errors);
    },
    preserveScroll: true,
  });
};
</script>

<style scoped>
/* Add styling here if needed */
</style> 