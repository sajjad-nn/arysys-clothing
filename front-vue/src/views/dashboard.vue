<script setup>
import { RouterView } from 'vue-router'
import Sidebar from '../components/dashboard/Sidebar.vue'
</script>

<template>
  <div class="account-page">
    <div class="account-container">
      <Sidebar />

      <main class="account-content">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<style scoped>
.account-page {
  direction: rtl;
  width: 100%;
  min-height: 500px;
  padding: 120px 20px 35px;
  box-sizing: border-box;
  background: #f8f9fb;
}

.account-container {
  width: 100%;
  max-width: 1250px;
  margin: 0 auto;

  display: flex;
  flex-direction: row;
  align-items: flex-start;
  gap: 22px;
}

.account-content {
  flex: 1;
  min-width: 0;
  min-height: 350px;
  padding: 28px;
  background: #fff;
  border: 1px solid #e9edf2;
  border-radius: 16px;
  box-sizing: border-box;
  box-shadow: 0 3px 15px rgba(0, 0, 0, 0.03);
}

@media (max-width: 768px) {
  .account-page {
    padding: 120px 14px 30px;
  }

  .account-container {
    flex-direction: column;
    gap: 16px;
  }

  .account-content {
    width: 100%;
    padding: 20px 15px;
  }
}
</style>