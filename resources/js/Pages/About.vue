<template>
  <PublicLayout>
    <div class="min-h-screen bg-gray-50">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16">
          <h1 class="text-4xl font-bold text-gray-900 mb-4">About Us</h1>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Learn more about our mission, values, and the team behind our success
          </p>
        </div>

        <!-- Mission Section -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Mission</h2>
          <p class="text-gray-600 text-lg leading-relaxed">
            We are dedicated to providing exceptional services and solutions that empower our customers
            to achieve their goals. Our commitment to excellence, innovation, and customer satisfaction
            drives everything we do.
          </p>
        </div>

        <!-- Values Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
          <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Excellence</h3>
            <p class="text-gray-600">
              We strive for excellence in everything we do, delivering the highest quality services
              and solutions to our customers.
            </p>
          </div>

          <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Innovation</h3>
            <p class="text-gray-600">
              We embrace innovation and continuously seek new ways to improve and enhance our services
              for the benefit of our customers.
            </p>
          </div>

          <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Integrity</h3>
            <p class="text-gray-600">
              We conduct our business with the highest standards of integrity, honesty, and transparency
              in all our dealings.
            </p>
          </div>
        </div>

        <!-- Team Section -->
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Team</h2>
          <p class="text-gray-600 text-lg max-w-3xl mx-auto">
            Meet the dedicated professionals who make it all possible
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Team Member 1 -->
          <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
              <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4"></div>
              <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">John Doe</h3>
              <p class="text-gray-600 text-center mb-2">CEO & Founder</p>
              <p class="text-gray-600 text-center">
                Visionary leader with over 15 years of industry experience.
              </p>
            </div>
          </div>

          <!-- Team Member 2 -->
          <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
              <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4"></div>
              <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">Jane Smith</h3>
              <p class="text-gray-600 text-center mb-2">CTO</p>
              <p class="text-gray-600 text-center">
                Technology expert driving innovation and digital transformation.
              </p>
            </div>
          </div>

          <!-- Team Member 3 -->
          <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
              <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4"></div>
              <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">Mike Johnson</h3>
              <p class="text-gray-600 text-center mb-2">Operations Director</p>
              <p class="text-gray-600 text-center">
                Ensuring smooth operations and exceptional service delivery.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
</script> 