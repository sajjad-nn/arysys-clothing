<script setup>
import { ref } from 'vue'

import Sidebar from '../components/dashboard/Sidebar.vue'
import DashboardHeader from '../components/dashboard/DashboardHeader.vue'

const sidebarOpen = ref(true)

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}
</script>

<template>
  <div class="dashboard" :class="{ 'sidebar-closed': !sidebarOpen }">
    <!-- Sidebar -->
    <Sidebar :is-open="sidebarOpen" />

    <!-- Main Content -->
    <main class="dashboard-content">
      <!-- Header -->
      <DashboardHeader
        user-name="سجاد"
        :sidebar-open="sidebarOpen"
        @toggle-sidebar="toggleSidebar"
      />

      <!-- Page Content -->
      <section class="dashboard-welcome">
        <h1>داشبورد</h1>

        <p>به پنل کاربری خوش آمدید 👋</p>

        <p class="description">
          از این بخش می‌توانید سفارش‌ها، علاقه‌مندی‌ها، آدرس‌ها و اطلاعات حساب خود را مدیریت کنید.
        </p>
      </section>
    </main>
  </div>
</template>

<style scoped>
.dashboard-content {
  margin-right: 250px;
  min-height: 100vh;
  padding: 30px;
  box-sizing: border-box;

  transition: margin-right 0.3s ease;
}

.sidebar-closed .dashboard-content {
  margin-right: 0;
}

.dashboard {
  min-height: 100vh;

  background: #f5f5f5;

  direction: rtl;
}

/* Main */

.dashboard-content {
  margin-right: 250px;

  min-height: 100vh;

  padding: 30px;

  box-sizing: border-box;

  transition: 0.3s;
}

/* وقتی Sidebar بسته شود */

.sidebar-closed .dashboard-content {
  margin-right: 0;
}

/* Welcome */

.dashboard-welcome {
  margin-top: 25px;

  background: white;

  border-radius: 16px;

  padding: 30px;

  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
}

.dashboard-welcome h1 {
  margin: 0 0 8px;

  font-size: 28px;

  color: #111827;
}

.dashboard-welcome p {
  margin: 0;

  color: #374151;

  font-size: 15px;
}

.dashboard-welcome .description {
  margin-top: 15px;

  color: #6b7280;

  line-height: 1.9;
}

/* Mobile */

@media (max-width: 768px) {
  .dashboard-content {
    margin-right: 0;

    padding: 15px;
  }
}
</style>
