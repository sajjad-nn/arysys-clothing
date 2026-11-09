
<template>
  <header
    class="fixed top-0 left-0 w-full z-50 transition-all duration-500"
    :class="
      scrolled
        ? 'bg-black/60 backdrop-blur-2xl border-b border-white/10 shadow-[0_12px_40px_-18px_rgba(0,0,0,0.7)]'
        : 'bg-transparent'
    "
  >
    <nav
      class="fixed top-4 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] max-w-7xl z-50 px-4 py-3 bg-black/30 backdrop-blur-xl border border-white/10 rounded-2xl shadow-[0_10px_30px_-12px_rgba(0,0,0,0.6)] transition-all duration-300 lg:static lg:translate-x-0 lg:w-full lg:max-w-none lg:px-8 lg:py-4 lg:bg-black/20 lg:backdrop-blur-xl lg:border-0 lg:border-b lg:border-white/10 lg:rounded-none lg:shadow-none"
    >
      <div class="flex items-center justify-between h-full relative">

        <!-- RIGHT: Desktop Hamburger + Mobile Menu -->
        <div class="flex items-center gap-4">

          <!-- Desktop Hamburger + Dropdown -->
          <div
            ref="desktopMenuRef"
            class="hidden lg:block relative"
            @mouseenter="openDesktopMenuOnHover"
            @mouseleave="closeDesktopMenuOnLeave"
          >
            <button
              class="group flex items-center gap-3 h-11 pl-4 pr-3 rounded-full border text-white transition-all duration-300"
              :class="
                desktopMenuOpen
                  ? 'bg-white/15 border-white/30'
                  : 'bg-white/5 border-white/15 hover:bg-white/10 hover:border-white/30'
              "
              @click="toggleDesktopMenu"
              :aria-expanded="desktopMenuOpen"
              aria-label="باز کردن منو"
            >
              <span class="relative w-6 h-6 flex items-center justify-center">
                <Menu
                  class="absolute w-6 h-6 transition-all duration-300"
                  :class="
                    desktopMenuOpen
                      ? 'opacity-0 rotate-90 scale-50'
                      : 'opacity-100 rotate-0 scale-100'
                  "
                />
                <X
                  class="absolute w-6 h-6 transition-all duration-300"
                  :class="
                    desktopMenuOpen
                      ? 'opacity-100 rotate-0 scale-100'
                      : 'opacity-0 -rotate-90 scale-50'
                  "
                />
              </span>

              <span class="text-sm font-medium">منو</span>
            </button>

            <!-- DESKTOP DROPDOWN MENU -->
            <transition
              enter-active-class="transition duration-300 ease-out"
              leave-active-class="transition duration-200 ease-in"
              enter-from-class="opacity-0 -translate-y-3 scale-95"
              enter-to-class="opacity-100 translate-y-0 scale-100"
              leave-from-class="opacity-100 translate-y-0 scale-100"
              leave-to-class="opacity-0 -translate-y-3 scale-95"
            >
              <div
                v-if="desktopMenuOpen"
                class="absolute top-full right-0 pt-3 w-80 origin-top-right"
              >
                <div
                  class="rounded-3xl bg-black/70 backdrop-blur-2xl border border-white/10 shadow-[0_30px_60px_-20px_rgba(0,0,0,0.8)] overflow-hidden"
                >
                  <div class="relative p-5 border-b border-white/10 overflow-hidden">
                    <div
                      class="absolute -top-10 -left-10 w-32 h-32 rounded-full bg-rose-500/25 blur-3xl pointer-events-none"
                    ></div>

                    <span class="relative text-white/50 text-xs">
                      منوی اصلی
                    </span>

                    <p class="relative text-white text-lg font-semibold mt-1">
                      AryaSys
                    </p>
                  </div>

                  <div class="p-3 space-y-1">
                    <RouterLink
                      to="/"
                      class="desktop-menu-item"
                      @click="closeDesktopMenu"
                    >
                      <span class="menu-icon">
                        <House class="w-5 h-5" />
                      </span>
                      خانه
                    </RouterLink>

                    <RouterLink
                      to="/shop"
                      class="desktop-menu-item"
                      @click="closeDesktopMenu"
                    >
                      <span class="menu-icon">
                        <ShoppingBag class="w-5 h-5" />
                      </span>
                      فروشگاه
                    </RouterLink>

                    <RouterLink
                      to="/articles"
                      class="desktop-menu-item"
                      @click="closeDesktopMenu"
                    >
                      <span class="menu-icon">
                        <Newspaper class="w-5 h-5" />
                      </span>
                      مقالات
                    </RouterLink>

                    <RouterLink
                      to="/aboutus"
                      class="desktop-menu-item"
                      @click="closeDesktopMenu"
                    >
                      <span class="menu-icon">
                        <Info class="w-5 h-5" />
                      </span>
                      درباره ما
                    </RouterLink>

                    <RouterLink
                      to="/contact"
                      class="desktop-menu-item"
                      @click="closeDesktopMenu"
                    >
                      <span class="menu-icon">
                        <Phone class="w-5 h-5" />
                      </span>
                      تماس با ما
                    </RouterLink>
                  </div>

                  <div class="border-t border-white/10 p-3 space-y-1 bg-white/[0.03]">
                    <button
                      type="button"
                      class="desktop-menu-item w-full text-right"
                      @click="cart.openCart(); closeDesktopMenu()"
                    >
                      <span class="menu-icon">
                        <ShoppingCart class="w-5 h-5" />
                      </span>

                      <span class="flex-1">سبد خرید</span>

                      <span
                        v-if="totalItems > 0"
                        class="min-w-6 h-6 px-1 rounded-full bg-rose-500 text-white text-xs flex items-center justify-center"
                      >
                        {{ totalItems }}
                      </span>
                    </button>

                    <RouterLink
                      to="/favorites"
                      class="desktop-menu-item"
                      @click="closeDesktopMenu"
                    >
                      <span class="menu-icon">
                        <Heart class="w-5 h-5" />
                      </span>
                      علاقه‌مندی‌ها
                    </RouterLink>

                    <RouterLink
                      to="/compare"
                      class="desktop-menu-item"
                      @click="closeDesktopMenu"
                    >
                      <span class="menu-icon">
                        <GitCompare class="w-5 h-5" />
                      </span>
                      مقایسه محصولات
                    </RouterLink>

                    <RouterLink
                      :to="auth.isLoggedIn ? '/dashboard' : '/auth'"
                      class="desktop-menu-item"
                      @click="closeDesktopMenu"
                    >
                      <span class="menu-icon">
                        <User class="w-5 h-5" />
                      </span>
                      {{ auth.isLoggedIn ? auth.user?.name : 'ورود / ثبت‌نام' }}
                    </RouterLink>
                  </div>
                </div>
              </div>
            </transition>
          </div>

          <!-- Mobile Menu Button -->
          <button
            class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl text-white bg-white/5 border border-white/10 active:scale-95 transition"
            @click="isOpen = !isOpen"
            aria-label="باز کردن منو"
          >
            <svg
              v-if="!isOpen"
              xmlns="http://www.w3.org/2000/svg"
              class="w-6 h-6"
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
              class="w-6 h-6"
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

          <!-- Mobile Logo -->
          <a
            href="/"
            class="absolute left-1/2 -translate-x-1/2 lg:hidden flex items-center gap-2"
          >
            <img class="h-10" />

            <span class="text-2xl font-bold text-white tracking-wide">
              Arya<span class="text-rose-400">Sys</span>
            </span>
          </a>
        </div>

        <!-- CENTER: Desktop Logo -->
        <RouterLink
          to="/"
          class="hidden lg:flex items-center gap-2 absolute left-1/2 -translate-x-1/2"
        >
          <img class="h-10" />

          <span class="text-2xl font-bold text-white tracking-wide">
            Arya<span class="text-rose-400">Sys</span>
          </span>
        </RouterLink>

        <!-- LEFT: Search + Account + Cart -->
        <div class="flex items-center gap-3 text-white">

          <!-- Desktop Search Icon -->
          <button
            type="button"
            @click="toggleSearch"
            class="desktop-action hidden lg:flex"
            title="جستجوی محصولات"
            aria-label="جستجوی محصولات"
          >
            <Search class="w-5 h-5" />
          </button>

          <!-- Desktop Account & Cart -->
          <div class="hidden lg:flex items-center gap-2">
            <RouterLink
              :to="auth.isLoggedIn ? '/dashboard' : '/auth'"
              class="desktop-action !w-auto !px-3 gap-2"
              title="حساب کاربری"
            >
              <User class="w-5 h-5 shrink-0" />

              <span
                v-if="auth.isLoggedIn"
                class="text-sm whitespace-nowrap"
              >
                {{ auth.user?.name }}
              </span>
            </RouterLink>

            <button
              type="button"
              class="desktop-action relative"
              title="سبد خرید"
              @click="cart.openCart()"
            >
              <ShoppingCart class="w-5 h-5" />

              <span
                v-if="totalItems > 0"
                class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-rose-500 text-white text-[10px] flex items-center justify-center"
              >
                {{ totalItems }}
              </span>
            </button>
          </div>

          <!-- Mobile Shopping Cart -->
          <button
            type="button"
            @click="cart.openCart()"
            class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 border border-white/10 lg:hidden active:scale-95 transition"
            title="سبد خرید"
          >
            <ShoppingCart class="w-5 h-5 text-white" />

            <span
              v-if="totalItems > 0"
              class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-rose-500 text-white text-[10px] flex items-center justify-center"
            >
              {{ totalItems }}
            </span>
          </button>
        </div>
      </div>

      <!-- SEARCH PANEL -->
      <transition
        enter-active-class="transition-all duration-300"
        leave-active-class="transition-all duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <form
          v-if="showSearch"
          @submit.prevent="submitSearch"
          class="absolute top-full left-0 mt-4 w-full lg:w-96 p-3 rounded-2xl bg-black/80 backdrop-blur-2xl border border-white/10 shadow-2xl flex items-center gap-3"
        >
          <Search class="w-5 h-5 text-white/60 shrink-0" />

          <input
            ref="searchInput"
            v-model="searchQuery"
            type="search"
            placeholder="جستجوی محصولات..."
            class="flex-1 min-w-0 bg-transparent outline-none text-sm text-white placeholder:text-white/50"
          />

          <button
            type="button"
            @click="closeSearch"
            class="text-white/60 hover:text-white transition"
            aria-label="بستن جستجو"
          >
            <X class="w-5 h-5" />
          </button>
        </form>
      </transition>

      <!-- MOBILE MENU -->
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
          class="lg:hidden mt-4 rounded-3xl bg-black/70 backdrop-blur-2xl border border-white/10 overflow-hidden"
        >
          <RouterLink
            class="mobile-item"
            to="/"
            @click="isOpen = false"
          >
            <House class="w-5 h-5" />
            خانه
          </RouterLink>

          <RouterLink
            class="mobile-item"
            to="/shop"
            @click="isOpen = false"
          >
            <ShoppingBag class="w-5 h-5" />
            فروشگاه
          </RouterLink>

          <RouterLink
            class="mobile-item"
            to="/articles"
            @click="isOpen = false"
          >
            <Newspaper class="w-5 h-5" />
            مقالات
          </RouterLink>

          <RouterLink
            class="mobile-item"
            to="/aboutus"
            @click="isOpen = false"
          >
            <Info class="w-5 h-5" />
            درباره ما
          </RouterLink>

          <RouterLink
            class="mobile-item"
            to="/contact"
            @click="isOpen = false"
          >
            <Phone class="w-5 h-5" />
            تماس با ما
          </RouterLink>

          <div class="border-t border-white/10"></div>

          <a
            class="mobile-item"
            href="#"
            @click.prevent="toggleSearch"
          >
            <Search class="w-5 h-5" />
            جستجو
          </a>

          <a class="mobile-item" href="#">
            <Heart class="w-5 h-5" />
            علاقه‌مندی‌ها
          </a>

          <a class="mobile-item" href="#">
            <GitCompare class="w-5 h-5" />
            مقایسه
          </a>

          <RouterLink
            class="mobile-item"
            :to="auth.isLoggedIn ? '/dashboard' : '/auth'"
            @click="isOpen = false"
          >
            <User class="w-5 h-5" />
            {{ auth.isLoggedIn ? auth.user?.name : 'ورود / ثبت‌نام' }}
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
  ShoppingCart,
  Menu,
  X,
  House,
  ShoppingBag,
  Newspaper,
  Info,
  Phone,
} from 'lucide-vue-next'

import {
  ref,
  onMounted,
  onUnmounted,
  nextTick
} from 'vue'

import { storeToRefs } from 'pinia'

import { useRouter } from 'vue-router'
import { auth, checkAuth } from '@/services/auth'
import { useCartStore } from '@/stores/cart'

const router = useRouter()

const cart = useCartStore()

// دریافت واکنشی تعداد محصولات سبد خرید
const { totalItems } = storeToRefs(cart)

const isOpen = ref(false)
const desktopMenuOpen = ref(false)
const scrolled = ref(false)
const searchQuery = ref('')
const showSearch = ref(false)
const searchInput = ref(null)

/* ---------- Desktop menu: hover + click ---------- */

const desktopMenuRef = ref(null)
const desktopMenuPinned = ref(false)

let closeTimer = null

const clearCloseTimer = () => {
  if (closeTimer) {
    clearTimeout(closeTimer)
    closeTimer = null
  }
}

const openDesktopMenuOnHover = () => {
  clearCloseTimer()
  desktopMenuOpen.value = true
}

const closeDesktopMenuOnLeave = () => {
  if (desktopMenuPinned.value) return

  clearCloseTimer()

  closeTimer = setTimeout(() => {
    desktopMenuOpen.value = false
  }, 180)
}

const closeDesktopMenu = () => {
  clearCloseTimer()
  desktopMenuOpen.value = false
  desktopMenuPinned.value = false
}

const toggleDesktopMenu = () => {
  clearCloseTimer()

  if (desktopMenuPinned.value) {
    closeDesktopMenu()
  } else {
    desktopMenuOpen.value = true
    desktopMenuPinned.value = true
  }
}

const onDocumentClick = (event) => {
  if (!desktopMenuOpen.value) return

  if (
    desktopMenuRef.value &&
    !desktopMenuRef.value.contains(event.target)
  ) {
    closeDesktopMenu()
  }
}

const onKeydown = (event) => {
  if (event.key === 'Escape') {
    closeDesktopMenu()
    closeSearch()
  }
}

/* ---------- Scroll ---------- */

const onScroll = () => {
  scrolled.value = window.scrollY > 40
}

/* ---------- Search ---------- */

const toggleSearch = async () => {
  showSearch.value = !showSearch.value

  if (showSearch.value) {
    await nextTick()
    searchInput.value?.focus()
  }
}

const closeSearch = () => {
  showSearch.value = false
}

const submitSearch = () => {
  const query = searchQuery.value.trim()

  if (!query) return

  router.push({
    path: '/shop',
    query: { search: query },
  })

  closeSearch()
  searchQuery.value = ''
}

/* ---------- Lifecycle ---------- */

onMounted(() => {
  checkAuth()

  window.addEventListener('scroll', onScroll)
  document.addEventListener('click', onDocumentClick)
  window.addEventListener('keydown', onKeydown)
})

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
  document.removeEventListener('click', onDocumentClick)
  window.removeEventListener('keydown', onKeydown)

  clearCloseTimer()
})
</script>

<style scoped>
.menu-item {
  position: relative;
  padding-bottom: 6px;
  transition: 0.3s;
}

.menu-item::after {
  content: '';
  position: absolute;
  right: 0;
  bottom: 0;
  width: 0;
  height: 2px;
  background: white;
  transition: 0.3s;
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
  transition: 0.3s;
}

.icon-btn:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: translateY(-2px);
}

/* Mobile menu items */

.mobile-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 24px;
  color: rgba(255, 255, 255, 0.9);
  text-decoration: none;
  font-size: 15px;
  transition: 0.3s;
}

.mobile-item svg {
  color: rgba(255, 255, 255, 0.55);
  transition: 0.3s;
}

.mobile-item:hover,
.mobile-item:active {
  background: rgba(255, 255, 255, 0.07);
  color: white;
}

.mobile-item:hover svg {
  color: #fb7185;
}

/* Desktop action icons */

.desktop-action {
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  border: 1px solid transparent;
  transition: 0.3s;
  color: white;
}

.desktop-action:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
}

/* Desktop dropdown menu */

.desktop-menu-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 9px 10px;
  color: rgba(255, 255, 255, 0.88);
  border-radius: 14px;
  font-size: 14px;
  transition: 0.25s;
  text-decoration: none;
}

.menu-icon {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 11px;
  background: rgba(255, 255, 255, 0.07);
  color: rgba(255, 255, 255, 0.75);
  transition: 0.25s;
}

.desktop-menu-item:hover {
  background: rgba(255, 255, 255, 0.08);
  color: white;
  transform: translateX(-3px);
}

.desktop-menu-item:hover .menu-icon {
  background: rgba(244, 63, 94, 0.22);
  color: #fda4af;
}

.desktop-menu-item.router-link-exact-active {
  color: white;
  background: rgba(244, 63, 94, 0.12);
}

.desktop-menu-item.router-link-exact-active .menu-icon {
  background: rgba(244, 63, 94, 0.28);
  color: #fda4af;
}

@media (prefers-reduced-motion: reduce) {
  .desktop-menu-item,
  .desktop-action,
  .menu-icon,
  .mobile-item {
    transition: none;
  }
}
</style>
