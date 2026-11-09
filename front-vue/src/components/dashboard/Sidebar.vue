<script setup>
import { RouterLink, useRouter } from 'vue-router'

const router = useRouter()
const menuItems = [
  {
    title: 'داشبورد',
    icon: '⌂',
    path: '/dashboard',
  },
  {
    title: 'سفارش‌های من',
    icon: '▤',
    path: '/dashboard/orders',
  },
  {
    title: 'علاقه‌مندی‌ها',
    icon: '♡',
    path: '/dashboard/wishlist',
  },
  {
    title: 'آدرس‌های من',
    icon: '⌖',
    path: '/dashboard/addresses',
  },
  {
    title: 'اطلاعات حساب',
    icon: '♙',
    path: '/dashboard/profile',
  },
]

function handleLogout() {
  router.push('/auth')
}
</script>

<template>
  <aside class="account-sidebar">
    <div class="sidebar-heading">
      <h3>حساب کاربری</h3>
      <span>مدیریت حساب شما</span>
    </div>

    <nav class="sidebar-nav">
      <RouterLink
        v-for="item in menuItems"
        :key="item.path"
        :to="item.path"
        class="sidebar-link"
        active-class="active"
        exact-active-class="active"
      >
        <span class="menu-icon">{{ item.icon }}</span>
        <span>{{ item.title }}</span>
      </RouterLink>
    </nav>

    <div class="sidebar-bottom">
      <button class="logout-button" @click="handleLogout">
        <span class="menu-icon">↪</span>
        خروج از حساب
      </button>
    </div>
  </aside>
</template>

<style scoped>
.account-sidebar {
  flex: 0 0 210px;
  width: 210px;
  padding: 22px 14px;
  box-sizing: border-box;
  background: #fff;
  border: 1px solid #e9edf2;
  border-radius: 16px;
  box-shadow: 0 3px 15px rgba(0, 0, 0, 0.03);
}

.sidebar-heading {
  padding: 0 8px 20px;
  margin-bottom: 12px;
  border-bottom: 1px solid #edf0f4;
}

.sidebar-heading h3 {
  margin: 0 0 7px;
  font-size: 17px;
  color: #202938;
}

.sidebar-heading span {
  font-size: 12px;
  color: #9299a5;
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sidebar-link,
.logout-button {
  width: 100%;
  min-height: 43px;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border: 0;
  border-radius: 9px;
  background: transparent;
  color: #606978;
  text-decoration: none;
  font-family: inherit;
  font-size: 13px;
  text-align: right;
  cursor: pointer;
  box-sizing: border-box;
  transition: 0.2s ease;
}

.sidebar-link:hover,
.logout-button:hover {
  background: #f3f6fb;
  color: #2563eb;
}

.sidebar-link.active {
  background: #edf4ff;
  color: #2563eb;
  font-weight: 600;
}

.menu-icon {
  width: 20px;
  flex-shrink: 0;
  font-size: 19px;
  text-align: center;
}

.sidebar-bottom {
  margin-top: 18px;
  padding-top: 14px;
  border-top: 1px solid #edf0f4;
}

.logout-button {
  color: #dc4545;
}

.logout-button:hover {
  background: #fff1f1;
  color: #c62828;
}

@media (max-width: 768px) {
  .account-sidebar {
    width: 100%;
    flex: none;
    padding: 18px 14px;
  }
}
</style>