<template>
  <PublicLayout>
    <div class="min-h-screen bg-gray-50">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16">
          <h1 class="text-4xl font-bold text-gray-900 mb-4">Our Services</h1>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Discover our comprehensive range of services designed to meet your needs
          </p>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Service Card Template -->
          <div
            v-for="service in services"
            :key="service.id"
            class="bg-white rounded-lg shadow-lg overflow-hidden"
          >
            <img
              v-if="service.cover_image"
              :src="'/storage/' + service.cover_image"
              alt="Service Cover Image"
              class="w-full h-48 object-cover"
            />
            <div class="p-6">
              <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ service.title }}</h3>
              <p class="text-gray-600 mb-4">
                {{ service.description }}
              </p>
              <p class="text-lg font-bold text-gray-800 mb-4">${{ service.price }}</p>
              
              <!-- Book Service Button -->
              <button
                @click="bookService(service)"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Book Service
              </button>
            </div>
          </div>
        </div>

        <!-- Call to Action -->
        <div class="mt-16 text-center">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Ready to Get Started?</h2>
          <p class="text-gray-600 mb-8">
            Contact us today to learn more about our services and how we can help you.
          </p>
          <Link
            :href="route('contact')"
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
          >
            Contact Us
          </Link>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { defineProps } from 'vue';

const props = defineProps({
    services: Array,
})

const form = useForm({
    service_id: null,
    booking_date: null, // Add booking date if needed for initial request
    // Add other initial booking fields here
});

const bookService = (service) => {
    form.service_id = service.id;
    // Potentially set a default or prompt for booking_date here
    
    form.post(route('bookings.store'), {
        onSuccess: () => {
            console.log('Booking initiated successfully!');
            // Redirect to customer dashboard or booking details page
            // For now, redirect to customer dashboard
            // You might want to show a success message to the user
             window.location.href = route('customer.dashboard');
        },
        onError: (errors) => {
            console.error('Error initiating booking:', errors);
            // Handle errors, e.g., display validation messages
            alert('Failed to book service. Please try again.');
        },
    });
};

</script>

<style scoped>
/* Add styling here if needed */
</style> 