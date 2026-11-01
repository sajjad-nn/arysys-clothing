<script setup>
import { computed } from 'vue'
import { RouterView, useRoute } from 'vue-router'

import Header from './components/Header.vue'
import Footer from './components/Footer.vue'

const route = useRoute()

const isDashboard = computed(() => {
  return route.path === '/dashboard' ||
         route.path.startsWith('/orders') ||
         route.path.startsWith('/wishlist') ||
         route.path.startsWith('/addresses') ||
         route.path.startsWith('/profile')
})
</script>

<template>
  <Header v-if="!isDashboard" />

  <RouterView />

  <Footer v-if="!isDashboard" />
</template>