<template>
  <Link :href="href" class="nav-link" :class="{ active: isActive }">
    <slot></slot>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  href: {
    type: String,
    required: true
  }
})

const isActive = computed(() => {
  return window.location.pathname === props.href
})
</script>

<style scoped>
.nav-link {
  padding: 0.5rem 1rem;
  text-decoration: none;
  color: #4B5563;
}

.nav-link.active {
  color: #4f46e5;
  font-weight: 600;
}
</style>