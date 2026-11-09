
<script setup>
import { computed } from 'vue'
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

  cart.updateQuantity(
    item.id,
    item.size,
    item.color,
    next
  )
}

const removeItem = (item) => {
  cart.removeFromCart(item.id, item.size, item.color)
}

const imageUrl = (image) => {
  if (!image) return ''

  const value = String(image).trim()

  if (/^(https?:)?\/\//i.test(value) || value.startsWith('data:')) {
    return value
  }

  const apiBase = (
    import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000'
  ).replace(/\/+$/, '')

  const cleanPath = value
    .replace(/^\/+/, '')
    .replace(/^storage\//, '')

  return `${apiBase}/storage/${cleanPath}`
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
          ادامه خرید
        </RouterLink>
      </div>

      <!-- سبد خالی -->
      <section v-if="!items.length" class="empty-cart">

        <div class="empty-icon">
          <ShoppingBag :size="45" />
        </div>

        <h2>سبد خرید شما خالی است</h2>

        <p>
          هنوز محصولی به سبد خرید خود اضافه نکرده‌اید.
        </p>

        <RouterLink to="/shop" class="shop-btn">
          <ShoppingCart :size="18" />
          مشاهده محصولات
        </RouterLink>

      </section>

      <!-- محصولات سبد خرید -->
      <section v-else class="cart-layout">

        <div class="cart-content">

          <div class="cart-content-header">
            <h2>محصولات انتخاب‌شده</h2>
            <span>{{ totalItems.toLocaleString('fa-IR') }} عدد</span>
          </div>

          <article
            v-for="item in items"
            :key="`${item.id}-${item.variant_id ?? ''}-${item.size}-${item.color}`"
            class="cart-product"
          >

            <!-- تصویر -->
            <div class="product-image">

              <img
                v-if="item.image"
                :src="imageUrl(item.image)"
                :alt="item.name"
                @error="item.imageFailed = true"
                v-show="!item.imageFailed"
              />

              <div
                v-if="!item.image || item.imageFailed"
                class="image-placeholder"
              >
                <ShoppingBag :size="28" />
              </div>

            </div>

            <!-- اطلاعات محصول -->
            <div class="product-info">

              <h3>{{ item.name }}</h3>

              <div class="product-options">
                <span v-if="item.color">
                  رنگ: {{ item.color }}
                </span>

                <span v-if="item.size">
                  سایز: {{ item.size }}
                </span>
              </div>

              <div class="product-prices">

                <span
                  v-if="originalPrice(item) > unitPrice(item)"
                  class="old-price"
                >
                  {{ formatPrice(originalPrice(item)) }} تومان
                </span>

                <strong>
                  {{ formatPrice(unitPrice(item)) }}
                  <small>تومان</small>
                </strong>

              </div>

              <!-- تعداد و حذف -->
              <div class="product-actions">

                <div class="quantity-control">

                  <button
                    type="button"
                    aria-label="افزایش تعداد"
                    :disabled="item.stock != null && item.quantity >= item.stock"
                    @click="changeQuantity(item, 1)"
                  >
                    <Plus :size="16" />
                  </button>

                  <span>
                    {{ item.quantity.toLocaleString('fa-IR') }}
                  </span>

                  <button
                    type="button"
                    aria-label="کاهش تعداد"
                    :disabled="item.quantity <= 1"
                    @click="changeQuantity(item, -1)"
                  >
                    <Minus :size="16" />
                  </button>

                </div>

                <button
                  type="button"
                  class="remove-btn"
                  @click="removeItem(item)"
                >
                  <Trash2 :size="16" />
                  حذف محصول
                </button>

              </div>

            </div>

            <!-- قیمت کل این محصول -->
            <div class="product-total">
              <span>قیمت کل</span>
              <strong>
                {{ formatPrice(unitPrice(item) * item.quantity) }}
              </strong>
              <small>تومان</small>
            </div>

          </article>

          <RouterLink to="/shop" class="continue-link">
            <ArrowRight :size="16" />
            بازگشت به فروشگاه
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

          <div class="summary-row discount-row">
            <span>تخفیف محصولات</span>
            <span>{{ formatPrice(discountTotal) }} تومان</span>
          </div>

          <div class="summary-divider"></div>

          <div class="final-total">
            <span>مبلغ قابل پرداخت</span>
            <strong>{{ formatPrice(subtotal) }}</strong>
            <small>تومان</small>
          </div>

          <p class="shipping-note">
            هزینه ارسال در مرحله بعد محاسبه می‌شود.
          </p>

          <RouterLink to="/checkout" class="checkout-btn">
            ادامه جهت تسویه‌حساب
          </RouterLink>

          <div class="secure-note">
            <ShoppingBag :size="16" />
            خرید آسان و مطمئن از ARYSYS
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

  min-height: 70vh;
  padding: 55px 20px 90px;
  background: #faf9f6;
  color: var(--ink);
}

.container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}

/* عنوان */

.page-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  margin-bottom: 35px;
}

.eyebrow {
  color: var(--gold-dark);
  font-size: 11px;
  letter-spacing: 3px;
  font-weight: 700;
}

.page-heading h1 {
  margin: 10px 0;
  font-size: 29px;
  font-weight: 800;
}

.page-heading p {
  margin: 0;
  color: var(--muted);
  font-size: 13px;
}

.back-shop,
.continue-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: var(--gold-dark);
  font-size: 13px;
  text-decoration: none;
}

/* سبد خالی */

.empty-cart {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 420px;
  padding: 30px;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 18px;
  text-align: center;
}

.empty-icon {
  display: grid;
  place-items: center;
  width: 95px;
  height: 95px;
  margin-bottom: 20px;
  border-radius: 50%;
  background: #f7f3ea;
  color: var(--gold);
}

.empty-cart h2 {
  font-size: 20px;
}

.empty-cart p {
  color: var(--muted);
  font-size: 13px;
}

.shop-btn {
  display: flex;
  align-items: center;
  gap: 9px;
  margin-top: 20px;
  padding: 13px 25px;
  border-radius: 10px;
  background: var(--ink);
  color: white;
  font-size: 13px;
  text-decoration: none;
}

/* چیدمان */

.cart-layout {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 340px;
  align-items: start;
  gap: 25px;
}

.cart-content {
  min-width: 0;
  padding: 22px;
  border: 1px solid var(--border);
  border-radius: 16px;
  background: #fff;
}

.cart-content-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 18px;
  border-bottom: 1px solid var(--border);
}

.cart-content-header h2 {
  margin: 0;
  font-size: 17px;
}

.cart-content-header span {
  color: var(--muted);
  font-size: 12px;
}

/* محصول */

.cart-product {
  display: grid;
  grid-template-columns: 115px minmax(0, 1fr) auto;
  align-items: center;
  gap: 20px;
  padding: 22px 0;
  border-bottom: 1px solid var(--border);
}

.product-image {
  width: 115px;
  height: 135px;
  overflow: hidden;
  border-radius: 12px;
  background: #f4f0e9;
}

.product-image img {
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
  min-width: 0;
}

.product-info h3 {
  margin: 0 0 12px;
  font-size: 15px;
  font-weight: 700;
}

.product-options {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  color: var(--muted);
  font-size: 12px;
}

.product-prices {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-top: 12px;
}

.product-prices strong {
  font-size: 15px;
}

.product-prices small,
.product-total small {
  font-size: 11px;
  color: var(--muted);
}

.old-price {
  color: #aaa;
  font-size: 11px;
  text-decoration: line-through;
}

.product-actions {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-top: 15px;
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 5px 8px;
  border: 1px solid var(--border);
  border-radius: 9px;
}

.quantity-control button {
  display: grid;
  place-items: center;
  width: 25px;
  height: 25px;
  border: 0;
  background: transparent;
  color: var(--ink);
  cursor: pointer;
}

.quantity-control button:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.quantity-control span {
  min-width: 20px;
  font-size: 13px;
  text-align: center;
}

.remove-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  border: 0;
  background: transparent;
  color: #c15c50;
  font-family: inherit;
  font-size: 12px;
  cursor: pointer;
}

.product-total {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 5px;
  font-size: 12px;
}

.product-total span {
  color: var(--muted);
}

.product-total strong {
  font-size: 15px;
}

.continue-link {
  margin-top: 22px;
}

/* خلاصه سفارش */

.order-summary {
  position: sticky;
  top: 25px;
  padding: 23px;
  border: 1px solid var(--border);
  border-radius: 16px;
  background: #fff;
}

.order-summary h2 {
  margin: 0 0 24px;
  font-size: 17px;
}

.summary-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-bottom: 18px;
  font-size: 12px;
}

.summary-row > span:first-child {
  color: var(--muted);
}

.discount-row {
  color: #29966c;
}

.summary-divider {
  height: 1px;
  margin: 22px 0;
  background: var(--border);
}

.final-total {
  display: grid;
  grid-template-columns: 1fr auto;
  align-items: center;
  gap: 6px;
}

.final-total > span {
  font-size: 13px;
}

.final-total strong {
  font-size: 21px;
}

.final-total small {
  grid-column: 2;
  justify-self: end;
  color: var(--muted);
  font-size: 11px;
}

.shipping-note {
  margin: 18px 0;
  color: var(--muted);
  font-size: 11px;
  line-height: 1.9;
}

.checkout-btn {
  display: block;
  padding: 15px 10px;
  border-radius: 11px;
  background: var(--ink);
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  text-align: center;
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
  margin-top: 18px;
  color: var(--muted);
  font-size: 11px;
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
    padding: 30px 14px 60px;
  }

  .page-heading {
    align-items: flex-start;
    margin-bottom: 25px;
  }

  .page-heading h1 {
    font-size: 23px;
  }

  .back-shop {
    font-size: 11px;
    white-space: nowrap;
  }

  .cart-content {
    padding: 14px;
  }

  .cart-product {
    grid-template-columns: 85px minmax(0, 1fr);
    gap: 12px;
    padding: 17px 0;
  }

  .product-image {
    width: 85px;
    height: 110px;
  }

  .product-info h3 {
    font-size: 13px;
  }

  .product-total {
    grid-column: 2;
    flex-direction: row;
    align-items: center;
    justify-content: flex-start;
  }

  .product-actions {
    gap: 10px;
  }

  .remove-btn {
    font-size: 11px;
  }

  .order-summary {
    padding: 18px;
  }
}

@media (max-width: 380px) {
  .page-heading {
    flex-direction: column;
  }

  .product-actions {
    flex-wrap: wrap;
  }
}
</style>
