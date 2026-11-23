<script setup>
import { RouterView } from 'vue-router'
import Header from './components/header.vue'
import Footer from './components/footer.vue'
import MiniCart from './components/cart/MiniCart.vue'
import MobileBottomNav from './components/MobileBottomNav.vue'
import FloatingSocial from './components/FloatingSocial.vue'
</script>

<template>
  <div class="app-layout">
    <Header />

    <main class="page-content">
      <RouterView />
      <FloatingSocial />
    </main>

    <Footer />
    <MiniCart />
    <MobileBottomNav />
  </div>
</template>