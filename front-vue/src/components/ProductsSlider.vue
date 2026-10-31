<template>
  <section class="products-section" dir="rtl">
    <div class="section-header">
      <div class="header-titles">
        <h2>محصولات فروشگاه</h2>
        <p>انتخابی خاص برای درخشش در استایل شما</p>
      </div>

      <RouterLink to="/shop" class="view-all">
        <span>مشاهده همه</span>
        <span class="arrow-icon">←</span>
      </RouterLink>
    </div>

    <div v-if="loading" class="message">
      <span class="loader"></span>
      <p>در حال دریافت محصولات...</p>
    </div>

    <div v-else-if="error" class="message error">
      <p>{{ error }}</p>
      <button class="retry-btn" @click="fetchProducts">تلاش مجدد</button>
    </div>

    <div v-else-if="products.length === 0" class="message">محصولی برای نمایش وجود ندارد.</div>

    <Swiper
      v-else
      :modules="[Navigation]"
      :slides-per-view="1.3"
      :space-between="14"
      :navigation="true"
      :breakpoints="{
        520: {
          slidesPerView: 2.2,
          spaceBetween: 16,
        },
        768: {
          slidesPerView: 3.2,
          spaceBetween: 18,
        },
        1024: {
          slidesPerView: 4.2,
          spaceBetween: 20,
        },
        1280: {
          slidesPerView: 5,
          spaceBetween: 22,
        },
      }"
      class="products-swiper"
    >
      <SwiperSlide v-for="product in products" :key="product.id">
        <article class="product-card">
          <!-- تصویر محصول با نسبت ابعاد دقیق و ثابت -->
          <div class="product-image">
            <img
              v-if="getCurrentImage(product)"
              :src="imageUrl(getCurrentImage(product))"
              :alt="product.name"
              loading="lazy"
              @error="onImageError($event)"
            />

            <div v-else class="no-image">
              <svg
                width="28"
                height="28"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
              >
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
              </svg>
              <span>فاقد تصویر</span>
            </div>

            <!-- بج تخفیف -->
            <span v-if="getCurrentPricing(product).discounted" class="discount-badge">
              {{ getDiscountPercent(product) }}٪ تخفیف
            </span>
          </div>

          <!-- بدنه کارت با ارتفاع یکدست -->
          <div class="product-info">
            <span class="category">
              {{ product.category?.name || 'کالکشن اختصاصی' }}
            </span>

            <h3 :title="product.name">
              <RouterLink :to="`/product/${product.slug || product.id}`">
                {{ product.name }}
              </RouterLink>
            </h3>

            <!-- فوتر قیمت و وضعیت موجودی -->
            <div class="card-footer-area">
              <div class="price-area">
                <span
                  class="old-price"
                  :class="{ invisible: !getCurrentPricing(product).discounted }"
                >
                  {{
                    getCurrentPricing(product).discounted
                      ? formatPrice(getCurrentPricing(product).original)
                      : '-'
                  }}
                </span>

                <div class="current-price">
                  <span class="amount">{{ formatPrice(getCurrentPricing(product).current) }}</span>
                  <small>تومان</small>
                </div>
              </div>

              <div
                class="stock-status"
                :class="getCurrentStock(product) > 0 ? 'available' : 'unavailable'"
              >
                <span class="status-dot"></span>
                <span>{{ getCurrentStock(product) > 0 ? 'موجود' : 'ناموجود' }}</span>
              </div>
            </div>

            <!-- دکمه خرید قفل‌شده در پایین کارت -->
            <RouterLink :to="`/product/${product.slug || product.id}`" class="product-button">
              <span>مشاهده محصول</span>
              <span class="btn-arrow">←</span>
            </RouterLink>
          </div>
        </article>
      </SwiperSlide>
    </Swiper>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation } from 'swiper/modules'

import 'swiper/css'
import 'swiper/css/navigation'

const API_BASE = 'http://127.0.0.1:8000'

const products = ref([])
const loading = ref(true)
const error = ref('')

function getVariants(product) {
  return (product.variants || []).filter(
    (variant) => variant.is_active !== false && variant.is_active !== 0,
  )
}

function getFirstVariant(product) {
  return getVariants(product)[0] || null
}

function getCurrentImage(product) {
  const variant = getFirstVariant(product)
  return variant?.image || product.image || ''
}

function imageUrl(image) {
  if (!image || typeof image !== 'string') return ''

  const value = image.trim()

  if (/^https?:\/\//i.test(value)) {
    return value
  }

  const cleanPath = value.replace(/^\/+/, '').replace(/^storage\/+/, '')

  return `${API_BASE}/storage/${cleanPath}`
}

function onImageError(event) {
  event.target.style.display = 'none'
}

function getCurrentPricing(product) {
  const variant = getFirstVariant(product)
  const item = variant || product

  const original = Number(item.price)
  const discount = Number(item.discount_price)

  const discounted =
    item.discount_price !== null &&
    item.discount_price !== undefined &&
    item.discount_price !== '' &&
    Number.isFinite(original) &&
    Number.isFinite(discount) &&
    discount >= 0 &&
    discount < original

  return {
    original: Number.isFinite(original) ? original : null,
    current: discounted ? discount : original,
    discounted,
  }
}

function getDiscountPercent(product) {
  const pricing = getCurrentPricing(product)

  if (!pricing.discounted || !pricing.original) return 0

  return Math.round(((pricing.original - pricing.current) / pricing.original) * 100)
}

function getCurrentStock(product) {
  const variant = getFirstVariant(product)

  const stock = variant ? variant.stock : product.stock

  return Math.max(0, Number(stock) || 0)
}

function formatPrice(price) {
  if (price === null || price === undefined || !Number.isFinite(Number(price))) {
    return 'قیمت ثبت نشده'
  }

  return Number(price).toLocaleString('fa-IR')
}

async function fetchProducts() {
  try {
    loading.value = true
    error.value = ''

    const response = await fetch(`${API_BASE}/api/products`)

    if (!response.ok) {
      throw new Error('دریافت محصولات با خطا مواجه شد.')
    }

    const data = await response.json()

    products.value = Array.isArray(data) ? data : (data.data ?? [])
  } catch (err) {
    console.error(err)
    error.value = err.message || 'ارتباط با سرور برقرار نشد.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchProducts)
</script>

<style scoped>
.products-section {
  --gold: #c7a66a;
  --gold-light: #e0c48c;
  --gold-deep: #8b7657;
  --ink: #17140f;

  width: 82%;
  max-width: 1360px;
  margin: 70px auto;
  color: var(--ink);
  direction: rtl;
  font-family: inherit;
}

/* ───────── هدر بخش ───────── */

.section-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 34px;
}

.header-titles h2 {
  position: relative;
  display: inline-block;
  margin: 0 0 10px;
  padding-bottom: 12px;
  font-size: 26px;
  font-weight: 800;
  color: var(--ink);
  letter-spacing: -0.5px;
}

.header-titles h2::after {
  content: '';
  position: absolute;
  right: 0;
  bottom: 0;
  width: 44px;
  height: 3px;
  border-radius: 3px;
  background: linear-gradient(90deg, var(--gold-light), var(--gold));
}

.header-titles p {
  margin: 0;
  font-size: 14px;
  color: #857e72;
}

.view-all {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--gold-deep);
  text-decoration: none;
  font-size: 13.5px;
  font-weight: 700;
  padding: 8px 14px;
  border-radius: 10px;
  background: rgba(199, 166, 106, 0.08);
  border: 1px solid rgba(199, 166, 106, 0.2);
  transition: all 0.25s ease;
  white-space: nowrap;
}

.view-all:hover {
  background: var(--gold-deep);
  color: #ffffff;
}

.view-all .arrow-icon {
  transition: transform 0.25s ease;
}

.view-all:hover .arrow-icon {
  transform: translateX(-4px);
}

/* ───────── اجبار Swiper به تراز یکدست ارتفاع ───────── */

.products-swiper {
  width: 100%;
  padding: 10px 4px 40px;
}

:deep(.swiper-wrapper) {
  display: flex;
  align-items: stretch; /* تمام اسلایدها هم‌ارتفاع بلندترین اسلاید می‌شوند */
}

:deep(.swiper-slide) {
  height: auto; /* ارتفاع وابسته به کل اسلایدر */
  display: flex;
}

/* ───────── کارت یکدست محصول ───────── */

.product-card {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%; /* کشیده شدن کامل در اسلاید */
  background: #ffffff;
  border-radius: 20px;
  border: 1px solid #f0f0f2;
  box-shadow: 0 4px 18px -4px rgba(23, 20, 15, 0.05);
  overflow: hidden;
  transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
  box-sizing: border-box;
}

.product-card:hover {
  transform: translateY(-6px);
  border-color: rgba(199, 166, 106, 0.4);
  box-shadow:
    0 16px 32px -10px rgba(139, 118, 87, 0.2),
    0 4px 12px rgba(23, 20, 15, 0.04);
}

/* تصویر */
.product-image {
  position: relative;
  width: 100%;
  aspect-ratio: 1 / 1.15;
  flex-shrink: 0; /* عکس کوچک نشود */
  overflow: hidden;
  background: #f4f4f5;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.product-card:hover .product-image img {
  transform: scale(1.07);
}

.no-image {
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  color: #a1a1aa;
  font-size: 11px;
}

.discount-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(220, 38, 38, 0.92);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  color: #ffffff;
  padding: 4px 9px;
  border-radius: 8px;
  font-size: 10.5px;
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
}

/* محتوای کارت با چینش عمودی Flex */
.product-info {
  display: flex;
  flex-direction: column;
  flex: 1; /* اشغال تمام فضای باقی‌مانده عمودی */
  padding: 16px 14px 14px;
  box-sizing: border-box;
}

.category {
  display: block;
  font-size: 11px;
  font-weight: 600;
  color: var(--gold-deep);
  margin-bottom: 6px;
  text-transform: uppercase;
}

.product-info h3 {
  font-size: 14px;
  font-weight: 700;
  line-height: 1.5;
  margin: 0 0 14px;
  height: 42px; /* فیکس شدن ارتفاع دقیقاً ۲ خط */
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-info h3 a {
  color: var(--ink);
  text-decoration: none;
  transition: color 0.2s ease;
}

.product-info h3 a:hover {
  color: var(--gold-deep);
}

/* فوتر قیمت و وضعیت انبار */
.card-footer-area {
  margin-top: auto; /* چسبیدن به پایین محتوا */
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 6px;
  padding-top: 12px;
  border-top: 1px dashed #e4e4e7;
}

.price-area {
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  min-height: 42px; /* حفظ جایگاه حتی در صورت نبود تخفیف */
}

.old-price {
  font-size: 11px;
  color: #a1a1aa;
  text-decoration: line-through;
  line-height: 1.3;
  margin-bottom: 2px;
  display: block;
  height: 15px; /* حفظ فضا */
}

.old-price.invisible {
  visibility: hidden; /* حفظ ارتفاع خط بدون نمایش متن */
}

.current-price {
  display: flex;
  align-items: baseline;
  gap: 3px;
}

.current-price .amount {
  font-size: 15.5px;
  font-weight: 800;
  color: #09090b;
}

.current-price small {
  font-size: 10px;
  color: #71717a;
  font-weight: 400;
}

/* وضعیت انبار */
.stock-status {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 7px;
  border-radius: 6px;
  white-space: nowrap;
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.available {
  background: #f0fdf4;
  color: #166534;
}

.available .status-dot {
  background: #22c55e;
  box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
}

.unavailable {
  background: #fef2f2;
  color: #991b1b;
}

.unavailable .status-dot {
  background: #ef4444;
  box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
}

/* دکمه مشاهده محصول - دقیقاً در یک تراز در انتهای تمام کارت‌ها */
.product-button {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 14px;
  padding: 10px;
  box-sizing: border-box;
  border-radius: 10px;
  background: #18181b;
  color: #ffffff;
  font-size: 12px;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.25s ease;
}

.product-button:hover {
  background: var(--gold-deep);
  transform: translateY(-2px);
  box-shadow: 0 6px 14px -3px rgba(139, 118, 87, 0.4);
}

.btn-arrow {
  transition: transform 0.25s ease;
}

.product-button:hover .btn-arrow {
  transform: translateX(-4px);
}

/* ───────── پیام‌ها و وضعیت لودینگ ───────── */

.message {
  text-align: center;
  padding: 50px 20px;
  color: #857e72;
  font-size: 14px;
  background: #ffffff;
  border-radius: 20px;
  border: 1px solid #f0f0f2;
}

.error {
  color: #dc2626;
}

.retry-btn {
  margin-top: 12px;
  padding: 8px 18px;
  background: #18181b;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-family: inherit;
  font-size: 12px;
}

.loader {
  display: inline-block;
  width: 28px;
  height: 28px;
  border: 3px solid #e4e4e7;
  border-top-color: var(--gold-deep);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 10px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* ───────── ناوبری Swiper ───────── */

:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(228, 228, 231, 0.9);
  color: #18181b;
  box-shadow: 0 6px 18px -4px rgba(0, 0, 0, 0.12);
  transition: all 0.25s ease;
}

:deep(.swiper-button-next:hover),
:deep(.swiper-button-prev:hover) {
  background: var(--gold-deep);
  color: #ffffff;
  border-color: var(--gold-deep);
  transform: scale(1.05);
}

:deep(.swiper-button-next::after),
:deep(.swiper-button-prev::after) {
  font-size: 14px;
  font-weight: 700;
}

/* ───────── ریسپانسیو ───────── */

@media (max-width: 1024px) {
  .products-section {
    width: 90%;
    margin: 55px auto;
  }
}

@media (max-width: 768px) {
  .products-section {
    width: 92%;
    margin: 45px auto;
  }

  .section-header {
    margin-bottom: 24px;
  }

  .header-titles h2 {
    font-size: 20px;
  }

  .header-titles p {
    font-size: 12px;
  }

  .view-all {
    font-size: 12px;
    padding: 6px 10px;
  }

  .product-info {
    padding: 12px;
  }

  .product-info h3 {
    font-size: 13px;
    height: 38px;
  }

  .current-price .amount {
    font-size: 14px;
  }

  :deep(.swiper-button-next),
  :deep(.swiper-button-prev) {
    display: none;
  }
}
</style>
