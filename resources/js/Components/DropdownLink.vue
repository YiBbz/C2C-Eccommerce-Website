<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
    },
});
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
    >
        <slot />
    </Link>
    <button
        v-else
        type="submit"
        class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
    >
        <slot />
    </button>
</template>
