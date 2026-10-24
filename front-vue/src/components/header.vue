<template>
  <header
    class="fixed top-0 left-0 w-full z-50 transition-all duration-500"
    :class="scrolled
      ? 'bg-black/55 backdrop-blur-xl border-b border-white/10 shadow-xl'
      : 'bg-transparent'"
  >
    <nav class="fixed top-4 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] max-w-7xl z-50 px-4 py-3 bg-black/20 backdrop-blur-lg border border-white/10 rounded-2xl shadow-lg transition-all duration-300">

      <div class="flex items-center justify-between h-full">

        <!-- RIGHT -->
        <div class="flex items-center gap-10">

          <!-- Mobile Menu -->
          <button
            class="lg:hidden text-white"
            @click="isOpen = !isOpen"
          >
            <svg
              v-if="!isOpen"
              xmlns="http://www.w3.org/2000/svg"
              class="w-8 h-8"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>

            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="w-8 h-8"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>

          <!-- Logo -->
          <a
            href="/"
            class="absolute left-1/2 -translate-x-1/2 lg:static flex items-center gap-2"
          >
            <img
              class="h-10"
            >

            <span class="text-2xl font-bold text-white">
              AryaSys
            </span>
          </a>

          <!-- Desktop Menu -->
          <ul
            class="hidden lg:flex items-center gap-8 text-white font-medium -translate-x-8"
          >
            <li>
              <RouterLink class="menu-item" to="/">
                خانه
              </RouterLink>
            </li>

            <li>
              <RouterLink class="menu-item" to="/shop">
                فروشگاه
              </RouterLink>
            </li>

            <li>
              <RouterLink class="menu-item" to="/articles">
                مقالات
              </RouterLink>
            </li>

            <li>
              <RouterLink class="menu-item" to="/about">
                درباره ما
              </RouterLink>
            </li>

            <li>
              <RouterLink class="menu-item" to="/contact">
                تماس با ما
              </RouterLink>
            </li>
          </ul>

        </div>

        <!-- LEFT -->
        <div class="flex items-center gap-5 text-white">

          <!-- Desktop Icons -->
          <div class="hidden lg:flex items-center gap-5">

            <Search class="w-6 h-6" />

            <Heart class="w-6 h-6" />

            <GitCompare class="w-6 h-6" />

            <!-- User -->
            <RouterLink
              :to="isLoggedIn ? '/dashboard' : '/auth'"
              class="icon-btn"
            >
              <User class="w-6 h-6" />
            </RouterLink>

          </div>

          <!-- Shopping Cart -->
          <button class="flex items-center">
            <ShoppingCart class="w-6 h-6" />
          </button>

        </div>

      </div>

      <!-- Mobile Menu -->
      <transition
        enter-active-class="duration-300"
        leave-active-class="duration-200"
        enter-from-class="opacity-0 -translate-y-3"
        enter-to-class="opacity-100 translate-y-0"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-3"
      >

        <div
          v-show="isOpen"
          class="lg:hidden mt-4 rounded-3xl bg-black/80 backdrop-blur-xl border border-white/10 overflow-hidden"
        >

          <RouterLink
            class="mobile-item"
            to="/"
            @click="isOpen = false"
          >
            خانه
          </RouterLink>

          <RouterLink
            class="mobile-item"
            to="/shop"
            @click="isOpen = false"
          >
            فروشگاه
          </RouterLink>

          <RouterLink
            class="mobile-item"
            to="/articles"
            @click="isOpen = false"
          >
            مقالات
          </RouterLink>

          <RouterLink
            class="mobile-item"
            to="/about"
            @click="isOpen = false"
          >
            درباره ما
          </RouterLink>

          <RouterLink
            class="mobile-item"
            to="/contact"
            @click="isOpen = false"
          >
            تماس با ما
          </RouterLink>

          <div class="border-t border-white/10"></div>

          <a class="mobile-item" href="#">
            جستجو
          </a>

          <a class="mobile-item" href="#">
            علاقه‌مندی‌ها
          </a>

          <a class="mobile-item" href="#">
            مقایسه
          </a>

          <!-- Account -->
          <RouterLink
            class="mobile-item"
            :to="isLoggedIn ? '/dashboard' : '/auth'"
            @click="isOpen = false"
          >
            {{ isLoggedIn ? 'حساب کاربری' : 'ورود / ثبت‌نام' }}
          </RouterLink>

        </div>

      </transition>

    </nav>
  </header>
</template>

<script setup>

import {
  Search,
  Heart,
  GitCompare,
  User,
  ShoppingCart
} from 'lucide-vue-next'

import {
  ref,
  onMounted,
  onUnmounted
} from 'vue'

import api from '../services/api'

const isLoggedIn = ref(false)

const isOpen = ref(false)

const scrolled = ref(false)

const checkAuth = async () => {
  try {
    await api.get('/api/user')
    isLoggedIn.value = true
  } catch {
    isLoggedIn.value = false
  }
}

const onScroll = () => {
  scrolled.value = window.scrollY > 40
}

onMounted(() => {
  checkAuth()
  window.addEventListener('scroll', onScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
})

</script>

<style scoped>

.menu-item {
  position: relative;
  padding-bottom: 6px;
  transition: .3s;
}

.menu-item::after {
  content: "";
  position: absolute;
  right: 0;
  bottom: 0;
  width: 0;
  height: 2px;
  background: white;
  transition: .3s;
}

.menu-item:hover::after {
  width: 100%;
}

.menu-item.router-link-active::after {
  width: 100%;
}

.icon-btn {
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  transition: .3s;
}

.icon-btn:hover {
  background: rgba(255,255,255,.15);
  transform: translateY(-2px);
}

.mobile-item {
  display: block;
  padding: 18px 24px;
  color: white;
  text-decoration: none;
  transition: .3s;
}

.mobile-item:hover {
  background: rgba(255,255,255,.08);
}

</style>