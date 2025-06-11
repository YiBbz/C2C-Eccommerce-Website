<template>
    <div>
        <Navbar />
        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import Navbar from '@/Components/Navbar.vue';
</script> 