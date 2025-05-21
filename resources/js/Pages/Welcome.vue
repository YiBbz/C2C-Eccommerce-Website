<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Navbar from '@/Components/Navbar.vue';

const isAuthenticated = ref(false);
const userRole = ref(null);

onMounted(async () => {
  try {
    const token = localStorage.getItem('token');
    if (token) {
      const response = await axios.get('/api/user');
      isAuthenticated.value = true;
      userRole.value = response.data.role;
    }
  } catch (error) {
    console.error('Error checking authentication:', error);
  }
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}

const logout = async () => {
  try {
    await axios.post('/logout');
    localStorage.removeItem('token');
    localStorage.removeItem('userRole');
    router.visit('/');
  } catch (error) {
    console.error('Error logging out:', error);
  }
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <Navbar />

        <!-- Hero Section -->
        <div class="relative bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                    <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                        <div class="sm:text-center lg:text-left">
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span class="block">Find the perfect</span>
                                <span class="block text-indigo-600">freelance services</span>
                            </h1>
                            <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Connect with talented freelancers and get your projects done. From web development to graphic design, find the perfect match for your needs.
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                                <div class="rounded-md shadow">
                                    <Link 
                                        :href="route('register')" 
                                        class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10"
                                    >
                                        Get Started
                                    </Link>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <Link 
                                        :href="route('login')" 
                                        class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 md:py-4 md:text-lg md:px-10"
                                    >
                                        Sign In
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
            <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
                <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80" alt="Freelance marketplace">
            </div>
        </div>

        <!-- Popular Categories -->
        <div class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                        Popular Categories
                    </h2>
                    <p class="mt-4 text-lg text-gray-500">
                        Find the perfect service for your needs
                    </p>
                </div>

                <div class="mt-10">
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Web Development -->
                        <div class="relative group">
                            <div class="relative h-80 w-full overflow-hidden rounded-lg bg-white group-hover:opacity-75 sm:aspect-w-2 sm:aspect-h-1 sm:h-64 lg:aspect-w-1 lg:aspect-h-1">
                                <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2852&q=80" alt="Web Development" class="h-full w-full object-cover object-center">
                            </div>
                            <h3 class="mt-6 text-sm text-gray-500">
                                <Link href="#">
                                    <span class="absolute inset-0"></span>
                                    Web Development
                                </Link>
                            </h3>
                            <p class="text-base font-semibold text-gray-900">Custom websites, web apps, and more</p>
                        </div>

                        <!-- Graphic Design -->
                        <div class="relative group">
                            <div class="relative h-80 w-full overflow-hidden rounded-lg bg-white group-hover:opacity-75 sm:aspect-w-2 sm:aspect-h-1 sm:h-64 lg:aspect-w-1 lg:aspect-h-1">
                                <img src="https://images.unsplash.com/photo-1626785774573-4b799315345d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2851&q=80" alt="Graphic Design" class="h-full w-full object-cover object-center">
                            </div>
                            <h3 class="mt-6 text-sm text-gray-500">
                                <Link href="#">
                                    <span class="absolute inset-0"></span>
                                    Graphic Design
                                </Link>
                            </h3>
                            <p class="text-base font-semibold text-gray-900">Logos, branding, and visual content</p>
                        </div>

                        <!-- Digital Marketing -->
                        <div class="relative group">
                            <div class="relative h-80 w-full overflow-hidden rounded-lg bg-white group-hover:opacity-75 sm:aspect-w-2 sm:aspect-h-1 sm:h-64 lg:aspect-w-1 lg:aspect-h-1">
                                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80" alt="Digital Marketing" class="h-full w-full object-cover object-center">
                            </div>
                            <h3 class="mt-6 text-sm text-gray-500">
                                <Link href="#">
                                    <span class="absolute inset-0"></span>
                                    Digital Marketing
                                </Link>
                            </h3>
                            <p class="text-base font-semibold text-gray-900">SEO, social media, and content marketing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- How It Works -->
        <div class="bg-gray-50 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                        How It Works
                    </h2>
                    <p class="mt-4 text-lg text-gray-500">
                        Simple steps to get your project done
                    </p>
                </div>

                <div class="mt-10">
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Step 1 -->
                        <div class="relative">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                1
                            </div>
                            <h3 class="mt-6 text-lg font-medium text-gray-900">Post a Project</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Describe your project and requirements in detail
                            </p>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                2
                            </div>
                            <h3 class="mt-6 text-lg font-medium text-gray-900">Choose a Freelancer</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Review proposals and select the best match
                            </p>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                3
                            </div>
                            <h3 class="mt-6 text-lg font-medium text-gray-900">Get It Done</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Collaborate and receive your completed project
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="bg-indigo-700">
            <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                    <span class="block">Ready to get started?</span>
                    <span class="block">Join our marketplace today.</span>
                </h2>
                <p class="mt-4 text-lg leading-6 text-indigo-200">
                    Connect with talented freelancers and start your project right away.
                </p>
                <Link 
                    :href="route('register')" 
                    class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 sm:w-auto"
                >
                    Sign up for free
                </Link>
            </div>
        </div>
    </div>
</template>
