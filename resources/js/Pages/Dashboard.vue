<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Welcome to your dashboard!</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            You're logged in as {{ $page.props.auth.user.name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
