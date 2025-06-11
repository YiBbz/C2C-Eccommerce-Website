<template>
    <form @submit.prevent="submitForm" class="mb-6 p-4 bg-white rounded shadow">
      <h3 class="text-lg font-medium mb-4">Add New Service</h3>
      <div class="mb-4">
        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          placeholder="Service Title"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          required
        />
        <p v-if="form.errors.title" class="mt-2 text-sm text-red-600">{{ form.errors.title }}</p>
      </div>

      <div class="mb-4">
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea
          id="description"
          v-model="form.description"
          placeholder="Service Description"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          required
        ></textarea>
        <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
      </div>

      <div class="mb-4">
        <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
        <input
          id="price"
          v-model.number="form.price"
          type="number"
          step="0.01"
          placeholder="0.00"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          required
        />
        <p v-if="form.errors.price" class="mt-2 text-sm text-red-600">{{ form.errors.price }}</p>
      </div>

      <div class="mb-4">
        <label for="cover_image" class="block text-sm font-medium text-gray-700">Cover Image (Optional)</label>
        <input
          id="cover_image"
          type="file"
          @change="handleImageUpload"
          class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
        />
        <p v-if="form.errors.cover_image" class="mt-2 text-sm text-red-600">{{ form.errors.cover_image }}</p>
      </div>

      <div v-if="form.hasErrors" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
          <p>There were errors with your submission:</p>
          <ul>
              <li v-for="error in Object.values(form.errors)" :key="error" class="text-sm">{{ error }}</li>
          </ul>
      </div>

      <button
        type="submit"
        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        :disabled="form.processing"
      >
        Save Service
      </button>
    </form>
</template>
  
<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    description: '',
    price: null,
    cover_image: null,
});

const emit = defineEmits(['service-saved']);

const handleImageUpload = (event) => {
    form.cover_image = event.target.files[0];
};

const submitForm = () => {
    form.post(route('services.store'), {
        onSuccess: () => {
            form.reset();
            emit('service-saved');
        },
        onError: (errors) => {
            console.error('Service creation failed:', errors);
            // Inertia's useForm automatically populates form.errors
        },
    });
};

</script>
  
<style scoped>
/* Add styling here if needed */
</style>
  