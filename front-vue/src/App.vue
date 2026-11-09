<script setup>
import { RouterView } from 'vue-router'
import Header from './components/header.vue'
import Footer from './components/footer.vue'
import MiniCart from './components/cart/MiniCart.vue'
</script>

<template>
  <div class="app-layout">
    <Header />

    <main class="page-content">
      <RouterView />
    </main>

    <Footer />
    <MiniCart />
  </div>
</template>

<style>
html,
body,
#app {
  min-height: 100%;
  margin: 0;
}

body {
  min-height: 100vh;
}

.app-layout {
  min-height: 100vh;
  min-height: 100svh;
  display: flex;
  flex-direction: column;
}

.page-content {
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
}
</style>
