<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useCartStore } from '@/stores/cart'
import { X, ShoppingBag, Trash2, Plus, Minus, ImageOff } from 'lucide-vue-next'

const cart = useCartStore()

const { items, isCartOpen, totalItems, subtotal } = storeToRefs(cart)

const API_BASE = (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/+$/, '')

// کلیدهایی که عکسشان خطا داده
const brokenImages = ref(new Set())

const itemKey = (item) =>
  `${item.id}-${item.variant_id ?? ''}-${item.size ?? ''}-${item.color ?? ''}`

const imageUrl = (image) => {
  if (!image) return ''

  const value = String(image).trim()

  if (/^(https?:)?\/\//i.test(value) || value.startsWith('data:')) {
    return value
  }

  const cleanPath = value.replace(/^\/+/, '').replace(/^storage\//, '')

  return `${API_BASE}/storage/${cleanPath}`
}

const hasImage = (item) => item.image && !brokenImages.value.has(itemKey(item))

const onImageError = (item) => {
  brokenImages.value = new Set(brokenImages.value).add(itemKey(item))
}

const formatPrice = (price) => new Intl.NumberFormat('fa-IR').format(Number(price) || 0)

const unitPrice = (item) => item.final_price ?? item.discount_price ?? item.price

const changeQuantity = (item, amount) => {
  const next = item.quantity + amount

  if (next < 1) return
  if (item.stock && next > item.stock) return

  cart.updateQuantity(item.id, item.size, item.color, next)
}

const removeItem = (item) => {
  if (typeof cart.removeFromCart === 'function') {
    cart.removeFromCart(item.id, item.size, item.color)
  } else if (typeof cart.removeItem === 'function') {
    cart.removeItem(item.id, item.size, item.color)
  }
}

const closeCart = () => {
  isCartOpen.value = false
}
</script>

<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="isCartOpen" class="cart-overlay" @click="closeCart"></div>
    </Transition>

    <Transition name="slide">
      <aside v-if="isCartOpen" class="cart-drawer" dir="rtl" aria-label="سبد خرید">
        <!-- سربرگ -->
        <header class="cart-header">
          <div class="cart-title">
            <ShoppingBag :size="20" />
            <h2>سبد خرید</h2>
            <span v-if="totalItems" class="cart-count">{{
              totalItems.toLocaleString('fa-IR')
            }}</span>
          </div>

          <button type="button" class="icon-btn" aria-label="بستن" @click="closeCart">
            <X :size="18" />
          </button>
        </header>

        <!-- سبد خالی -->
        <div v-if="!items.length" class="cart-empty">
          <span class="empty-icon"><ShoppingBag :size="34" /></span>
          <p>سبد خرید شما خالی است</p>
          <RouterLink to="/shop" class="shop-link" @click="closeCart">مشاهده فروشگاه</RouterLink>
        </div>

        <!-- لیست محصولات -->
        <template v-else>
          <ul class="cart-items">
            <li v-for="item in items" :key="itemKey(item)" class="cart-item">
              <div class="item-image">
                <img
                  v-if="hasImage(item)"
                  :src="imageUrl(item.image)"
                  :alt="item.name"
                  loading="lazy"
                  @error="onImageError(item)"
                />
                <span v-else class="item-no-image"><ImageOff :size="22" /></span>
              </div>

              <div class="item-info">
                <h3>{{ item.name }}</h3>

                <div v-if="item.color || item.size" class="item-meta">
                  <span v-if="item.color">رنگ: {{ item.color }}</span>
                  <span v-if="item.size">سایز: {{ item.size }}</span>
                </div>

                <div class="item-price">
                  <span v-if="item.discount_price && item.price > item.discount_price" class="old">
                    {{ formatPrice(item.price) }}
                  </span>
                  <strong>{{ formatPrice(unitPrice(item)) }} <small>تومان</small></strong>
                </div>

                <div class="item-actions">
                  <div class="qty">
                    <button
                      type="button"
                      aria-label="کاهش"
                      :disabled="item.quantity <= 1"
                      @click="changeQuantity(item, -1)"
                    >
                      <Minus :size="14" />
                    </button>

                    <span>{{ item.quantity.toLocaleString('fa-IR') }}</span>

                    <button
                      type="button"
                      aria-label="افزایش"
                      :disabled="item.stock && item.quantity >= item.stock"
                      @click="changeQuantity(item, 1)"
                    >
                      <Plus :size="14" />
                    </button>
                  </div>

                  <button type="button" class="remove" aria-label="حذف" @click="removeItem(item)">
                    <Trash2 :size="16" />
                  </button>
                </div>
              </div>
            </li>
          </ul>

          <!-- جمع و دکمه‌ها -->
          <footer class="cart-footer">
            <div class="subtotal">
              <span>جمع کل</span>
              <strong>{{ formatPrice(subtotal) }} <small>تومان</small></strong>
            </div>

            <RouterLink to="/checkout" class="checkout-btn" @click="closeCart">
              ادامه و ثبت سفارش
            </RouterLink>

            <RouterLink to="/cart" class="checkout-btn"> مشاهده سبد خرید </RouterLink>
          </footer>
        </template>
      </aside>
    </Transition>
  </Teleport>
</template>

<style scoped>
.cart-overlay {
  position: fixed;
  inset: 0;
  z-index: 9998;
  background: rgba(23, 20, 15, 0.45);
  backdrop-filter: blur(3px);
}

.cart-drawer {
  --gold: #c7a66a;
  --gold-deep: #8b7657;
  --ink: #17140f;

  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  width: min(100%, 420px);
  background: #fff;
  box-shadow: 20px 0 50px -20px rgba(23, 20, 15, 0.35);
  color: var(--ink);
}

/* سربرگ */

.cart-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 20px;
  border-bottom: 1px solid #eee9e0;
}

.cart-title {
  display: flex;
  align-items: center;
  gap: 10px;
  color: var(--gold-deep);
}

.cart-title h2 {
  margin: 0;
  font-size: 17px;
  font-weight: 800;
  color: var(--ink);
}

.cart-count {
  display: grid;
  place-items: center;
  min-width: 22px;
  height: 22px;
  padding: 0 6px;
  border-radius: 999px;
  background: var(--gold);
  color: #fff;
  font-size: 12px;
  font-weight: 700;
}

.icon-btn {
  display: grid;
  place-items: center;
  width: 36px;
  height: 36px;
  border: 0;
  border-radius: 50%;
  background: #f5f2ec;
  color: var(--ink);
  cursor: pointer;
  transition:
    background 0.25s,
    transform 0.25s;
}

.icon-btn:hover {
  background: #ebe5d8;
  transform: rotate(90deg);
}

/* سبد خالی */

.cart-empty {
  display: flex;
  flex: 1;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 14px;
  padding: 30px;
  color: #857e72;
  text-align: center;
}

.empty-icon {
  display: grid;
  place-items: center;
  width: 78px;
  height: 78px;
  border-radius: 50%;
  background: #f7f3ea;
  color: var(--gold);
}

.cart-empty p {
  margin: 0;
  font-size: 14px;
}

.shop-link {
  padding: 11px 24px;
  border-radius: 10px;
  background: var(--ink);
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  transition: background 0.25s;
}

.shop-link:hover {
  background: var(--gold-deep);
}

/* لیست */

.cart-items {
  flex: 1;
  margin: 0;
  padding: 14px 16px;
  list-style: none;
  overflow-y: auto;
  overscroll-behavior: contain;
}

.cart-item {
  display: flex;
  gap: 14px;
  padding: 12px;
  margin-bottom: 12px;
  border: 1px solid #eee9e0;
  border-radius: 16px;
  background: #fff;
  transition: box-shadow 0.3s;
}

.cart-item:hover {
  box-shadow: 0 10px 24px -14px rgba(23, 20, 15, 0.25);
}

.item-image {
  flex: 0 0 88px;
  width: 88px;
  height: 104px;
  overflow: hidden;
  border-radius: 12px;
  background: #f3efe9;
}

.item-image img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-no-image {
  display: grid;
  place-items: center;
  width: 100%;
  height: 100%;
  color: #b8b0a2;
}

.item-info {
  display: flex;
  flex: 1;
  flex-direction: column;
  gap: 6px;
  min-width: 0;
}

.item-info h3 {
  margin: 0;
  overflow: hidden;
  font-size: 13.5px;
  font-weight: 700;
  line-height: 1.7;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.item-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.item-meta span {
  padding: 2px 9px;
  border-radius: 999px;
  background: #f7f3ea;
  color: var(--gold-deep);
  font-size: 11px;
}

.item-price {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.item-price .old {
  color: #a39b8e;
  font-size: 11px;
  text-decoration: line-through;
}

.item-price strong {
  font-size: 14px;
  font-weight: 800;
}

small {
  font-size: 10px;
  font-weight: 400;
  color: #857e72;
}

.item-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: auto;
}

.qty {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 3px;
  border: 1px solid #eee9e0;
  border-radius: 10px;
}

.qty button {
  display: grid;
  place-items: center;
  width: 26px;
  height: 26px;
  border: 0;
  border-radius: 7px;
  background: #f5f2ec;
  color: var(--ink);
  cursor: pointer;
  transition: background 0.2s;
}

.qty button:hover:not(:disabled) {
  background: var(--gold);
  color: #fff;
}

.qty button:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.qty span {
  min-width: 24px;
  font-size: 13px;
  font-weight: 700;
  text-align: center;
}

.remove {
  display: grid;
  place-items: center;
  width: 32px;
  height: 32px;
  border: 0;
  border-radius: 9px;
  background: #fdf0f0;
  color: #c0392b;
  cursor: pointer;
  transition:
    background 0.2s,
    color 0.2s;
}

.remove:hover {
  background: #c0392b;
  color: #fff;
}

/* پایین سبد */

.cart-footer {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 16px 20px calc(18px + env(safe-area-inset-bottom));
  border-top: 1px solid #eee9e0;
  background: #fdfcf9;
}

.subtotal {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 4px;
  font-size: 14px;
}

.subtotal strong {
  font-size: 18px;
  font-weight: 800;
}

.checkout-btn,
.continue-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 13px;
  border-radius: 12px;
  font-family: inherit;
  font-size: 13.5px;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition:
    background 0.25s,
    color 0.25s,
    transform 0.25s,
    border-color 0.25s;
}

.checkout-btn {
  border: 0;
  background: var(--ink);
  color: #fff;
}

.checkout-btn:hover {
  background: var(--gold-deep);
  transform: translateY(-2px);
}

.continue-btn {
  border: 1px solid #e4ddd0;
  background: #fff;
  color: var(--ink);
}

.continue-btn:hover {
  border-color: var(--gold);
}

/* انیمیشن‌ها */

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
  transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.slide-enter-from,
.slide-leave-to {
  transform: translateX(-100%);
}

@media (prefers-reduced-motion: reduce) {
  .fade-enter-active,
  .fade-leave-active,
  .slide-enter-active,
  .slide-leave-active,
  .icon-btn,
  .checkout-btn {
    transition: none;
  }
}
</style>
