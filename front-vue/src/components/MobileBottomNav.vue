<template>
  <nav
    dir="rtl"
    aria-label="منوی پایین موبایل"
    class="lg:hidden fixed bottom-3 left-1/2 -translate-x-1/2 w-[calc(100%-1.5rem)] max-w-md z-[10000] px-1.5 py-2 rounded-2xl bg-black/75 backdrop-blur-2xl border border-white/15 shadow-[0_15px_35px_-10px_rgba(0,0,0,0.85)]"
  >
    <div class="grid grid-cols-5 items-end gap-0.5">
      <!-- ۱. خانه -->
      <RouterLink
        to="/"
        class="bottom-nav-item"
        :class="{ 'bottom-nav-active': homeActive }"
      >
        <span class="nav-icon">
          <House class="w-5 h-5" />
        </span>
        <span class="nav-label">خانه</span>
      </RouterLink>

      <!-- ۲. فروشگاه -->
      <RouterLink
        to="/shop"
        class="bottom-nav-item"
        :class="{ 'bottom-nav-active': shopActive }"
      >
        <span class="nav-icon">
          <ShoppingBag class="w-5 h-5" />
        </span>
        <span class="nav-label">فروشگاه</span>
      </RouterLink>

      <!-- ۳. سبد خرید: وقتی مینی‌کارت باز است یا در صفحه سبد/پرداخت هستی فعال می‌شود -->
      <button
        type="button"
        class="bottom-nav-item"
        :class="{ 'bottom-nav-active': cartActive }"
        :aria-expanded="isCartOpen"
        @click="toggleCart"
      >
        <span class="nav-icon">
          <ShoppingCart class="w-5 h-5" />

          <span
            v-if="totalItems > 0"
            class="absolute -top-1 -right-1.5 min-w-4 h-4 px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center ring-2 ring-black/70"
          >
            {{ cartBadge }}
          </span>
        </span>
        <span class="nav-label">سبد خرید</span>
      </button>

      <!-- ۴. تماس با ما -->
      <RouterLink
        to="/contact"
        class="bottom-nav-item"
        :class="{ 'bottom-nav-active': contactActive }"
      >
        <span class="nav-icon">
          <Phone class="w-5 h-5" />
        </span>
        <span class="nav-label">تماس</span>
      </RouterLink>

      <!-- ۵. حساب کاربری -->
      <RouterLink
        :to="auth.isLoggedIn ? '/dashboard' : '/auth'"
        class="bottom-nav-item"
        :class="{ 'bottom-nav-active': accountActive }"
      >
        <span class="nav-icon">
          <User class="w-5 h-5" />
        </span>
        <span class="nav-label">
          {{ auth.isLoggedIn ? auth.user?.name : 'حساب' }}
        </span>
      </RouterLink>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { House, ShoppingBag, ShoppingCart, Phone, User } from 'lucide-vue-next'
import { storeToRefs } from 'pinia'
import { auth } from '@/services/auth'
import { useCartStore } from '@/stores/cart'

const route = useRoute()
const cart = useCartStore()
const { totalItems, isCartOpen } = storeToRefs(cart)

// عدد فارسی؛ بالای ۹۹ به‌صورت ۹۹+
const cartBadge = computed(() =>
  totalItems.value > 99
    ? '۹۹+'
    : Number(totalItems.value).toLocaleString('fa-IR')
)

// آیا مسیر فعلی با یکی از این مسیرها (یا زیرمسیرهایشان) شروع می‌شود؟
const inSection = (...bases) =>
  bases.some((base) => route.path === base || route.path.startsWith(`${base}/`))

const onCartPage = computed(() => inSection('/cart', '/checkout'))

// فقط یک آیتم فعال باشد: وقتی مینی‌کارت باز است، سبد فعال است و بقیه نه
const cartActive = computed(() => isCartOpen.value || onCartPage.value)

const homeActive = computed(() => !isCartOpen.value && route.path === '/')
const shopActive = computed(() => !isCartOpen.value && inSection('/shop'))
const contactActive = computed(() => !isCartOpen.value && inSection('/contact'))
const accountActive = computed(
  () => !isCartOpen.value && inSection('/dashboard', '/auth')
)

const toggleCart = () => {
  // مینی‌کارت باز است: با زدن دوباره بسته شود
  if (isCartOpen.value) {
    isCartOpen.value = false
    return
  }

  // در صفحه اصلی سبد خرید نیازی به مینی‌کارت نیست
  if (onCartPage.value) return

  cart.openCart()
}
</script>

<style scoped>
.bottom-nav-item {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
  gap: 4px;
  padding: 6px 2px 4px;
  border: 0;
  border-radius: 14px;
  background: transparent;
  color: rgba(255, 255, 255, 0.6);
  font-family: inherit;
  font-size: 10.5px;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  -webkit-tap-highlight-color: transparent;
  transition: color 0.25s ease;
}

/* آیکون داخل دایره: در حالت عادی بی‌رنگ، در حالت فعال بالا می‌آید */
.nav-icon {
  position: relative;
  display: grid;
  place-items: center;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  transition:
    transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1),
    background 0.3s ease,
    box-shadow 0.3s ease,
    color 0.25s ease;
}

.nav-label {
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  transition:
    transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1),
    color 0.25s ease;
}

/* لمس / کلیک روی آیتم‌های غیرفعال */
.bottom-nav-item:active:not(.bottom-nav-active) .nav-icon {
  transform: scale(0.88);
}

.bottom-nav-item:focus-visible {
  outline: 2px solid #fda4af;
  outline-offset: 2px;
}

/* ---------- حالت فعال ---------- */

/* هاله ملایم پشت آیتم فعال */
.bottom-nav-active::before {
  content: '';
  position: absolute;
  inset: -6px 0 0;
  border-radius: 16px;
  background: radial-gradient(
    65% 75% at 50% 35%,
    rgba(244, 63, 94, 0.24),
    transparent 72%
  );
  pointer-events: none;
}

.bottom-nav-active {
  color: #fff;
  font-weight: 700;
}

.bottom-nav-active .nav-icon {
  transform: translateY(-13px) scale(1.12);
  color: #fff;
  background: linear-gradient(135deg, #fb7185, #e11d48);
  box-shadow:
    0 10px 20px -6px rgba(244, 63, 94, 0.7),
    0 0 0 4px rgba(244, 63, 94, 0.16);
}

.bottom-nav-active .nav-icon svg {
  stroke-width: 2.2;
}

.bottom-nav-active .nav-label {
  transform: translateY(-4px);
}

@media (prefers-reduced-motion: reduce) {
  .bottom-nav-item,
  .nav-icon,
  .nav-label {
    transition: none;
  }
}
</style>