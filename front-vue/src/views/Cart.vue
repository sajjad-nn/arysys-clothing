<script setup>
import { computed, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useCartStore } from '@/stores/cart'
import {
  ShoppingBag,
  Trash2,
  Plus,
  Minus,
  ArrowRight,
  ShoppingCart
} from 'lucide-vue-next'

const cart = useCartStore()

const { items, totalItems, subtotal } = storeToRefs(cart)

const apiBase = (import.meta.env.VITE_API_URL || '').trim().replace(/\/+$/, '')

// نگهداری وضعیت تصاویر خراب بدون دستکاری مستقیم آبجکت استور
const brokenImages = ref(new Set())

const itemKey = (item) =>
  `${item.id}-${item.variant_id ?? ''}-${item.size ?? ''}-${item.color ?? ''}`

const onImageError = (item) => {
  const next = new Set(brokenImages.value)
  next.add(itemKey(item))
  brokenImages.value = next
}

const hasImage = (item) => Boolean(item.image) && !brokenImages.value.has(itemKey(item))

const formatPrice = (price) =>
  new Intl.NumberFormat('fa-IR').format(Number(price) || 0)

const unitPrice = (item) =>
  Number(item.final_price ?? item.discount_price ?? item.price ?? 0)

const originalPrice = (item) =>
  Number(item.price ?? 0)

const discountTotal = computed(() =>
  items.value.reduce((total, item) => {
    const discount = Math.max(0, originalPrice(item) - unitPrice(item))
    return total + discount * item.quantity
  }, 0)
)

const changeQuantity = (item, amount) => {
  const next = item.quantity + amount

  if (next < 1) return
  if (item.stock != null && next > item.stock) return

  cart.updateQuantity(item.id, item.size, item.color, next)
}

const removeItem = (item) => {
  if (typeof cart.removeFromCart === 'function') {
    cart.removeFromCart(item.id, item.size, item.color)
  } else if (typeof cart.removeItem === 'function') {
    cart.removeItem(item.id, item.size, item.color)
  }
}

const imageUrl = (image) => {
  if (!image) return ''

  const value = String(image).trim()
  if (!value) return ''

  if (value.startsWith('data:')) {
    return value
  }

  if (/^https?:\/\//i.test(value)) {
    if (
      typeof window !== 'undefined' &&
      window.location.protocol === 'https:' &&
      value.startsWith('http://')
    ) {
      return value.replace(/^http:\/\//i, 'https://')
    }
    return value
  }

  if (value.startsWith('//')) {
    const proto = typeof window !== 'undefined' ? window.location.protocol : 'https:'
    return `${proto}${value}`
  }

  const cleanPath = value
    .replace(/^\/+/, '')
    .replace(/^storage\//, '')

  return apiBase ? `${apiBase}/storage/${cleanPath}` : `/storage/${cleanPath}`
}
</script>

<template>
  <main class="cart-page" dir="rtl">
    <div class="container">
      <!-- عنوان صفحه -->
      <div class="page-heading">
        <div>
          <span class="eyebrow">ARYSYS SHOP</span>
          <h1>سبد خرید شما</h1>
          <p>محصولات انتخاب‌شده خود را بررسی کنید.</p>
        </div>

        <RouterLink to="/shop" class="back-shop">
          <ArrowRight :size="17" />
          <span>ادامه خرید</span>
        </RouterLink>
      </div>

      <!-- سبد خالی -->
      <section v-if="!items.length" class="empty-cart">
        <div class="empty-icon">
          <ShoppingBag :size="45" />
        </div>

        <h2>سبد خرید شما خالی است</h2>
        <p>هنوز محصولی به سبد خرید خود اضافه نکرده‌اید.</p>

        <RouterLink to="/shop" class="shop-btn">
          <ShoppingCart :size="18" />
          <span>مشاهده محصولات</span>
        </RouterLink>
      </section>

      <!-- محصولات سبد خرید -->
      <section v-else class="cart-layout">
        <div class="cart-content">
          <div class="cart-content-header">
            <h2>محصولات انتخاب‌شده</h2>
            <span class="items-badge">{{ totalItems.toLocaleString('fa-IR') }} عدد</span>
          </div>

          <div class="products-list">
            <article
              v-for="item in items"
              :key="itemKey(item)"
              class="cart-product"
            >
              <!-- تصویر -->
              <div class="product-image">
                <img
                  v-if="hasImage(item)"
                  :src="imageUrl(item.image)"
                  :alt="item.name"
                  loading="lazy"
                  decoding="async"
                  @error="onImageError(item)"
                />
                <div v-else class="image-placeholder">
                  <ShoppingBag :size="28" />
                </div>
              </div>

              <!-- اطلاعات محصول -->
              <div class="product-info">
                <div class="product-top">
                  <h3>{{ item.name }}</h3>

                  <button
                    type="button"
                    class="remove-btn"
                    aria-label="حذف محصول"
                    @click="removeItem(item)"
                  >
                    <Trash2 :size="16" />
                    <span class="remove-text">حذف</span>
                  </button>
                </div>

                <div v-if="item.color || item.size" class="product-options">
                  <span v-if="item.color" class="option-pill">رنگ: {{ item.color }}</span>
                  <span v-if="item.size" class="option-pill">سایز: {{ item.size }}</span>
                </div>

                <div class="product-prices">
                  <span
                    v-if="originalPrice(item) > unitPrice(item)"
                    class="old-price"
                  >
                    {{ formatPrice(originalPrice(item)) }} تومان
                  </span>

                  <div class="unit-price">
                    <span>قیمت واحد:</span>
                    <strong>{{ formatPrice(unitPrice(item)) }}</strong>
                    <small>تومان</small>
                  </div>
                </div>

                <!-- تعداد و قیمت کل ردیف -->
                <div class="product-bottom">
                  <div class="quantity-control">
                    <button
                      type="button"
                      aria-label="افزایش تعداد"
                      :disabled="item.stock != null && item.quantity >= item.stock"
                      @click="changeQuantity(item, 1)"
                    >
                      <Plus :size="15" />
                    </button>

                    <span>{{ item.quantity.toLocaleString('fa-IR') }}</span>

                    <button
                      type="button"
                      aria-label="کاهش تعداد"
                      :disabled="item.quantity <= 1"
                      @click="changeQuantity(item, -1)"
                    >
                      <Minus :size="15" />
                    </button>
                  </div>

                  <div class="product-total">
                    <span class="total-label">جمع:</span>
                    <strong>{{ formatPrice(unitPrice(item) * item.quantity) }}</strong>
                    <small>تومان</small>
                  </div>
                </div>
              </div>
            </article>
          </div>

          <RouterLink to="/shop" class="continue-link">
            <ArrowRight :size="16" />
            <span>بازگشت به فروشگاه</span>
          </RouterLink>
        </div>

        <!-- خلاصه سفارش -->
        <aside class="order-summary">
          <h2>خلاصه سفارش</h2>

          <div class="summary-row">
            <span>تعداد محصولات</span>
            <span>{{ totalItems.toLocaleString('fa-IR') }} عدد</span>
          </div>

          <div class="summary-row">
            <span>جمع محصولات</span>
            <span>{{ formatPrice(subtotal + discountTotal) }} تومان</span>
          </div>

          <div v-if="discountTotal > 0" class="summary-row discount-row">
            <span>سود شما از تخفیف</span>
            <span>{{ formatPrice(discountTotal) }} تومان</span>
          </div>

          <div class="summary-divider"></div>

          <div class="final-total">
            <span>مبلغ قابل پرداخت</span>
            <div class="final-amount">
              <strong>{{ formatPrice(subtotal) }}</strong>
              <small>تومان</small>
            </div>
          </div>

          <p class="shipping-note">
            هزینه ارسال در مرحله بعد محاسبه می‌شود.
          </p>

          <RouterLink to="/checkout" class="checkout-btn">
            ادامه جهت تسویه‌حساب
          </RouterLink>

          <div class="secure-note">
            <ShoppingBag :size="16" />
            <span>خرید آسان و مطمئن از ARYSYS</span>
          </div>
        </aside>
      </section>
    </div>
  </main>
</template>

<style scoped>
.cart-page {
  --gold: #c7a66a;
  --gold-dark: #8b7657;
  --ink: #17140f;
  --muted: #858078;
  --border: #eee9e0;

  min-height: 100vh;
  padding: 96px 20px 100px;
  background: #faf9f6;
  color: var(--ink);
  box-sizing: border-box;
}

.container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}

/* عنوان */

.page-heading {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 20px;
  margin-bottom: 32px;
  padding-bottom: 18px;
  border-bottom: 1px solid var(--border);
}

.eyebrow {
  display: inline-block;
  color: var(--gold-dark);
  font-size: 11px;
  letter-spacing: 3px;
  font-weight: 700;
  direction: ltr;
}

.page-heading h1 {
  margin: 8px 0 6px;
  font-size: clamp(22px, 3vw, 30px);
  font-weight: 800;
}

.page-heading p {
  margin: 0;
  color: var(--muted);
  font-size: 13.5px;
}

.back-shop,
.continue-link {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  color: var(--gold-dark);
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  transition: color 0.2s;
}

.back-shop:hover,
.continue-link:hover {
  color: var(--ink);
}

/* سبد خالی */

.empty-cart {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
  padding: 32px 20px;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 18px;
  text-align: center;
}

.empty-icon {
  display: grid;
  place-items: center;
  width: 90px;
  height: 90px;
  margin-bottom: 18px;
  border-radius: 50%;
  background: #f7f3ea;
  color: var(--gold);
}

.empty-cart h2 {
  margin: 0 0 8px;
  font-size: 20px;
}

.empty-cart p {
  margin: 0;
  color: var(--muted);
  font-size: 13.5px;
}

.shop-btn {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  margin-top: 22px;
  padding: 13px 26px;
  border-radius: 11px;
  background: var(--ink);
  color: #fff;
  font-size: 13.5px;
  font-weight: 700;
  text-decoration: none;
  transition: background 0.25s;
}

.shop-btn:hover {
  background: var(--gold-dark);
}

/* چیدمان */

.cart-layout {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 350px;
  align-items: start;
  gap: 24px;
}

.cart-content {
  min-width: 0;
  padding: 22px;
  border: 1px solid var(--border);
  border-radius: 18px;
  background: #fff;
}

.cart-content-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--border);
}

.cart-content-header h2 {
  margin: 0;
  font-size: 17px;
  font-weight: 800;
}

.items-badge {
  padding: 4px 12px;
  border-radius: 999px;
  background: #f7f3ea;
  color: var(--gold-dark);
  font-size: 12px;
  font-weight: 700;
}

/* محصول */

.cart-product {
  display: grid;
  grid-template-columns: 110px minmax(0, 1fr);
  gap: 18px;
  padding: 20px 0;
  border-bottom: 1px solid var(--border);
}

.cart-product:last-child {
  border-bottom: 0;
  padding-bottom: 6px;
}

.product-image {
  width: 110px;
  height: 132px;
  overflow: hidden;
  border-radius: 14px;
  background: #f4f0e9;
  border: 1px solid #f0ece3;
}

.product-image img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-placeholder {
  display: grid;
  place-items: center;
  width: 100%;
  height: 100%;
  color: #b8b0a2;
}

.product-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.product-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 10px;
}

.product-top h3 {
  margin: 0;
  font-size: 15px;
  font-weight: 700;
  line-height: 1.5;
}

.remove-btn {
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 6px 10px;
  border: 0;
  border-radius: 8px;
  background: #fdf2f2;
  color: #c15c50;
  font-family: inherit;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition:
    background 0.2s,
    color 0.2s;
}

.remove-btn:hover {
  background: #c15c50;
  color: #fff;
}

.product-options {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 12px;
}

.option-pill {
  padding: 3px 10px;
  border-radius: 999px;
  background: #f7f3ea;
  color: var(--gold-dark);
  font-size: 11.5px;
  font-weight: 600;
}

.product-prices {
  display: flex;
  flex-direction: column;
  gap: 3px;
  margin-bottom: 14px;
}

.old-price {
  color: #aaa;
  font-size: 11.5px;
  text-decoration: line-through;
}

.unit-price {
  display: flex;
  align-items: baseline;
  gap: 5px;
  font-size: 12.5px;
  color: var(--muted);
}

.unit-price strong {
  color: var(--ink);
  font-size: 14.5px;
  font-weight: 700;
}

.unit-price small,
.product-total small {
  font-size: 11px;
  color: var(--muted);
}

.product-bottom {
  margin-top: auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding-top: 12px;
  border-top: 1px dashed #eee9e0;
}

.quantity-control {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 4px;
  border: 1px solid var(--border);
  border-radius: 10px;
  background: #faf9f6;
}

.quantity-control button {
  display: grid;
  place-items: center;
  width: 28px;
  height: 28px;
  border: 0;
  border-radius: 7px;
  background: #fff;
  color: var(--ink);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  cursor: pointer;
  transition:
    background 0.2s,
    color 0.2s;
}

.quantity-control button:hover:not(:disabled) {
  background: var(--gold);
  color: #fff;
}

.quantity-control button:disabled {
  opacity: 0.35;
  cursor: not-allowed;
}

.quantity-control span {
  min-width: 22px;
  font-size: 13.5px;
  font-weight: 700;
  text-align: center;
}

.product-total {
  display: flex;
  align-items: baseline;
  gap: 5px;
}

.total-label {
  color: var(--muted);
  font-size: 12px;
}

.product-total strong {
  font-size: 16px;
  font-weight: 800;
  color: var(--ink);
}

.continue-link {
  margin-top: 18px;
  padding-top: 16px;
  border-top: 1px solid var(--border);
  width: 100%;
}

/* خلاصه سفارش */

.order-summary {
  position: sticky;
  top: 96px;
  padding: 24px;
  border: 1px solid var(--border);
  border-radius: 18px;
  background: #fff;
}

.order-summary h2 {
  margin: 0 0 22px;
  font-size: 17px;
  font-weight: 800;
}

.summary-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-bottom: 16px;
  font-size: 13px;
}

.summary-row > span:first-child {
  color: var(--muted);
}

.discount-row {
  color: #29966c;
  font-weight: 600;
}

.summary-divider {
  height: 1px;
  margin: 20px 0;
  background: var(--border);
}

.final-total {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 10px;
}

.final-total > span {
  font-size: 13.5px;
  font-weight: 700;
}

.final-amount {
  display: flex;
  align-items: baseline;
  gap: 5px;
}

.final-amount strong {
  font-size: 21px;
  font-weight: 800;
}

.final-amount small {
  color: var(--muted);
  font-size: 11px;
}

.shipping-note {
  margin: 16px 0 20px;
  padding: 10px 12px;
  border-radius: 10px;
  background: #faf9f6;
  color: var(--muted);
  font-size: 11.5px;
  line-height: 1.8;
}

.checkout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 48px;
  padding: 14px 16px;
  border-radius: 12px;
  background: var(--ink);
  color: #fff;
  font-size: 13.5px;
  font-weight: 700;
  text-decoration: none;
  transition: background 0.25s;
}

.checkout-btn:hover {
  background: var(--gold-dark);
}

.secure-note {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 16px;
  color: var(--muted);
  font-size: 11.5px;
}

/* تبلت */

@media (max-width: 900px) {
  .cart-layout {
    grid-template-columns: 1fr;
  }

  .order-summary {
    position: static;
  }
}

/* موبایل */

@media (max-width: 600px) {
  .cart-page {
    padding: 92px 14px 96px;
  }

  .page-heading {
    align-items: flex-start;
    margin-bottom: 22px;
    padding-bottom: 14px;
  }

  .page-heading h1 {
    font-size: 22px;
  }

  .back-shop {
    padding: 7px 12px;
    border-radius: 8px;
    background: #fff;
    border: 1px solid var(--border);
    font-size: 12px;
    white-space: nowrap;
  }

  .cart-content {
    padding: 14px;
    border-radius: 16px;
  }

  .cart-product {
    grid-template-columns: 82px minmax(0, 1fr);
    gap: 12px;
    padding: 16px 0;
  }

  .product-image {
    width: 82px;
    height: 104px;
    border-radius: 11px;
  }

  .product-top h3 {
    font-size: 13.5px;
  }

  .remove-btn {
    padding: 6px 8px;
  }

  .remove-text {
    display: none;
  }

  .product-bottom {
    flex-wrap: wrap;
    gap: 10px;
    padding-top: 10px;
  }

  .quantity-control {
    gap: 6px;
    padding: 3px;
  }

  .quantity-control button {
    width: 26px;
    height: 26px;
  }

  .product-total strong {
    font-size: 14.5px;
  }

  .order-summary {
    padding: 18px;
    border-radius: 16px;
  }
}
</style>