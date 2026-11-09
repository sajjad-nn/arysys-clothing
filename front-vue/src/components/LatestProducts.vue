<template>
  <section class="latest-products-section" dir="rtl">
    <!-- هدر بخش جدیدترین محصولات -->
    <header class="section-top-bar">
      <div class="title-group">
        <h2>جدیدترین محصولات</h2>
        <p>تازه‌ترین انتخاب‌ها برای استایل خاص شما</p>
      </div>

      <RouterLink class="see-all-btn" to="/shop">
        <span>مشاهده همه</span>
        <span class="arrow-symbol">←</span>
      </RouterLink>
    </header>

    <!-- وضعیت در حال بارگذاری -->
    <div v-if="isLoading" class="state-box">
      <span class="loading-ring"></span>
      <p>در حال دریافت محصولات...</p>
    </div>

    <!-- وضعیت خطا -->
    <div v-else-if="errorText" class="state-box has-error">
      <p>{{ errorText }}</p>
      <button class="retry-action" @click="loadLatestProducts">تلاش مجدد</button>
    </div>

    <!-- وضعیت خالی بودن لیست -->
    <div v-else-if="!latestList.length" class="state-box">محصولی برای نمایش وجود ندارد.</div>

    <!-- اسلایدر جدیدترین محصولات -->
    <Swiper
      v-else
      :modules="[Navigation]"
      :slides-per-view="1.3"
      :space-between="14"
      :navigation="true"
      :breakpoints="sliderBreakpoints"
      class="latest-swiper"
    >
      <SwiperSlide v-for="item in latestList" :key="item.id">
        <div class="card-drop-shadow">
          <article class="item-card">
            <!-- بخش تصویر محصول -->
            <div class="item-figure">
              <RouterLink :to="`/product/${item.slug || item.id}`" class="thumb-link">
                <img
                  v-if="resolveImage(item)"
                  :src="buildImageUrl(resolveImage(item))"
                  :alt="item.name"
                  loading="lazy"
                  @error="handleBrokenImg"
                />
              </RouterLink>

              <div v-if="!resolveImage(item)" class="no-thumb">
                <svg
                  width="28"
                  height="28"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                  <circle cx="8.5" cy="8.5" r="1.5" />
                  <polyline points="21 15 16 10 5 21" />
                </svg>
                <span>فاقد تصویر</span>
              </div>

              <span v-if="calcPricing(item).hasDiscount" class="sale-badge">
                {{ calcDiscountPercent(item) }}٪ تخفیف
              </span>
            </div>

            <!-- اطلاعات محصول -->
            <div class="item-details">
              <div class="meta-row">
                <span class="cat-label">
                  {{ item.category?.name || 'کالکشن اختصاصی' }}
                </span>

                <div
                  class="inventory-tag"
                  :class="checkStock(item) > 0 ? 'in-stock' : 'out-of-stock'"
                >
                  <span class="indicator"></span>
                  <span>{{ checkStock(item) > 0 ? 'موجود' : 'ناموجود' }}</span>
                </div>
              </div>

              <h3 class="item-heading" :title="item.name">
                <RouterLink :to="`/product/${item.slug || item.id}`">
                  {{ item.name }}
                </RouterLink>
              </h3>

              <!-- فوتر قیمت (تراز با دکمه انحنای چپ) -->
              <div class="item-bottom-bar">
                <div class="pricing-col">
                  <span class="prev-price" :class="{ 'is-hidden': !calcPricing(item).hasDiscount }">
                    {{
                      calcPricing(item).hasDiscount
                        ? toPersianDigits(calcPricing(item).basePrice)
                        : '-'
                    }}
                  </span>

                  <div class="active-price">
                    <span class="val">{{ toPersianDigits(calcPricing(item).finalPrice) }}</span>
                    <small>تومان</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- دکمه دایره‌ای داخل برش منحنی (Inverted Radius) -->
            <button
              type="button"
              class="corner-cart-btn"
              :disabled="checkStock(item) <= 0"
              :title="checkStock(item) > 0 ? 'افزودن به سبد خرید' : 'ناموجود'"
              @click="triggerAddToCart(item)"
            >
              <svg
                width="17"
                height="17"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                <line x1="3" y1="6" x2="21" y2="6" />
                <path d="M16 10a4 4 0 0 1-8 0" />
              </svg>
            </button>
          </article>
        </div>
      </SwiperSlide>
    </Swiper>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation } from 'swiper/modules'
import { useCartStore } from '../stores/cart'

import 'swiper/css'
import 'swiper/css/navigation'

const BACKEND_URL = (import.meta.env?.VITE_API_URL || 'http://127.0.0.1:8000').replace(/\/+$/, '')

const cartStore = useCartStore()

const latestList = ref([])
const isLoading = ref(true)
const errorText = ref('')

const sliderBreakpoints = {
  520: { slidesPerView: 2.2, spaceBetween: 16 },
  768: { slidesPerView: 3.2, spaceBetween: 18 },
  1024: { slidesPerView: 4.2, spaceBetween: 20 },
  1280: { slidesPerView: 5, spaceBetween: 22 },
}

const getActiveVariants = (item) => {
  return (item.variants || []).filter((v) => v.is_active !== false && v.is_active !== 0)
}

const getPrimaryVariant = (item) => {
  return getActiveVariants(item)[0] || null
}

const resolveImage = (item) => {
  const primary = getPrimaryVariant(item)
  return primary?.image || item.image || ''
}

const buildImageUrl = (raw) => {
  if (!raw || typeof raw !== 'string') return ''
  const trimmed = raw.trim()
  if (/^https?:\/\//i.test(trimmed)) return trimmed

  const normalized = trimmed.replace(/^\/+/, '').replace(/^storage\/+/, '')
  return `${BACKEND_URL}/storage/${normalized}`
}

const handleBrokenImg = (e) => {
  e.target.style.display = 'none'
}

const calcPricing = (item) => {
  const source = getPrimaryVariant(item) || item
  const basePrice = Number(source.price)
  const salePrice = Number(source.discount_price)

  const hasDiscount =
    source.discount_price !== null &&
    source.discount_price !== undefined &&
    source.discount_price !== '' &&
    Number.isFinite(basePrice) &&
    Number.isFinite(salePrice) &&
    salePrice >= 0 &&
    salePrice < basePrice

  return {
    basePrice: Number.isFinite(basePrice) ? basePrice : null,
    finalPrice: hasDiscount ? salePrice : basePrice,
    hasDiscount,
  }
}

const calcDiscountPercent = (item) => {
  const { hasDiscount, basePrice, finalPrice } = calcPricing(item)
  if (!hasDiscount || !basePrice) return 0
  return Math.round(((basePrice - finalPrice) / basePrice) * 100)
}

const checkStock = (item) => {
  const primary = getPrimaryVariant(item)
  const rawStock = primary ? primary.stock : item.stock
  return Math.max(0, Number(rawStock) || 0)
}

const toPersianDigits = (val) => {
  if (val === null || val === undefined || !Number.isFinite(Number(val))) {
    return 'قیمت ثبت نشده'
  }
  return Number(val).toLocaleString('fa-IR')
}

const triggerAddToCart = (item) => {
  const availableStock = checkStock(item)
  if (availableStock <= 0) return

  const primary = getPrimaryVariant(item)
  const { basePrice, finalPrice } = calcPricing(item)

  cartStore.addToCart({
    ...item,
    image: resolveImage(item),
    price: basePrice,
    final_price: finalPrice,
    variant_id: primary?.id ?? null,
    size: primary?.size ?? null,
    color: primary?.color ?? null,
    stock: availableStock,
  })
}

const loadLatestProducts = async () => {
  try {
    isLoading.value = true
    errorText.value = ''

    const res = await fetch(`${BACKEND_URL}/api/products`)
    if (!res.ok) throw new Error('دریافت محصولات با خطا مواجه شد.')

    const payload = await res.json()
    const rawItems = Array.isArray(payload) ? payload : (payload.data ?? [])

    // فیلتر محصولات فعال و مرتب‌سازی بر اساس جدیدترین تاریخ ایجاد
    latestList.value = rawItems
      .filter((item) => item.is_active !== false && item.is_active !== 0)
      .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
  } catch (err) {
    console.error(err)
    errorText.value = err.message || 'ارتباط با سرور برقرار نشد.'
  } finally {
    isLoading.value = false
  }
}

onMounted(loadLatestProducts)
</script>

<style scoped>
.latest-products-section {
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

/* هدر */
.section-top-bar {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 34px;
}

.title-group h2 {
  position: relative;
  display: inline-block;
  margin: 0 0 10px;
  padding-bottom: 12px;
  font-size: 26px;
  font-weight: 800;
  color: var(--ink);
  letter-spacing: -0.5px;
}

.title-group h2::after {
  content: '';
  position: absolute;
  right: 0;
  bottom: 0;
  width: 44px;
  height: 3px;
  border-radius: 3px;
  background: linear-gradient(90deg, var(--gold-light), var(--gold));
}

.title-group p {
  margin: 0;
  font-size: 14px;
  color: #857e72;
}

.see-all-btn {
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

.see-all-btn:hover {
  background: var(--gold-deep);
  color: #ffffff;
}

.arrow-symbol {
  transition: transform 0.25s ease;
}

.see-all-btn:hover .arrow-symbol {
  transform: translateX(-4px);
}

/* اسلایدر */
.latest-swiper {
  width: 100%;
  padding: 10px 4px 40px;
}

:deep(.swiper-wrapper) {
  display: flex;
  align-items: stretch;
}

:deep(.swiper-slide) {
  height: auto;
  display: flex;
}

/* لایه سایه کارت */
.card-drop-shadow {
  width: 100%;
  display: flex;
  filter: drop-shadow(0 4px 14px rgba(23, 20, 15, 0.06));
  transition:
    transform 0.35s cubic-bezier(0.165, 0.84, 0.44, 1),
    filter 0.35s ease;
}

.card-drop-shadow:hover {
  transform: translateY(-6px);
  filter: drop-shadow(0 14px 24px rgba(139, 118, 87, 0.16));
}

/* ۱. بدنه کارت عمودی */
.item-card {
  position: relative;
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  z-index: 1;
}

/* ۲. اعمال برش Inverted Radius ظریف روی پس‌زمینه کارت */
.item-card::before {
  --r: 16px; /* شعاع گردی لبه‌های برش */
  --s: 20px; /* شعاع فرورفتگی داخلی */
  --x: 6px; /* افست افقی */
  --y: 6px; /* افست عمودی */

  content: '';
  position: absolute;
  inset: 0;
  background: #ffffff;
  border-radius: 20px;
  z-index: -1;

  --_m: /calc(2 * var(--r)) calc(2 * var(--r)) radial-gradient(#000 70%, #0000 72%);
  --_g: conic-gradient(from 180deg at var(--r) calc(100% - var(--r)), #0000 25%, #000 0);
  --_d: (var(--s) + var(--r));

  -webkit-mask:
    calc(var(--_d) + var(--x)) 100% var(--_m),
    0 calc(100% - var(--_d) - var(--y)) var(--_m),
    radial-gradient(var(--s) at 0 100%, #0000 99%, #000 calc(100% + 1px)) calc(var(--r) + var(--x))
      calc(-1 * var(--r) - var(--y)),
    var(--_g) calc(var(--_d) + var(--x)) 0,
    var(--_g) 0 calc(-1 * var(--_d) - var(--y));
  mask:
    calc(var(--_d) + var(--x)) 100% var(--_m),
    0 calc(100% - var(--_d) - var(--y)) var(--_m),
    radial-gradient(var(--s) at 0 100%, #0000 99%, #000 calc(100% + 1px)) calc(var(--r) + var(--x))
      calc(-1 * var(--r) - var(--y)),
    var(--_g) calc(var(--_d) + var(--x)) 0,
    var(--_g) 0 calc(-1 * var(--_d) - var(--y));
  -webkit-mask-repeat: no-repeat;
  mask-repeat: no-repeat;
}

/* ۳. بخش تصویر عمودی و کشیده */
.item-figure {
  position: relative;
  width: 100%;
  aspect-ratio: 1 / 1.18;
  flex-shrink: 0;
  overflow: hidden;
  background: #f4f4f5;
  border-radius: 20px 20px 0 0;
}

.thumb-link {
  display: block;
  width: 100%;
  height: 100%;
}

.item-figure img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.card-drop-shadow:hover .item-figure img {
  transform: scale(1.07);
}

.no-thumb {
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  color: #a1a1aa;
  font-size: 11px;
}

.sale-badge {
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

/* ۴. جزئیات کارت */
.item-details {
  display: flex;
  flex-direction: column;
  flex: 1;
  padding: 14px;
  box-sizing: border-box;
}

.meta-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 6px;
  margin-bottom: 8px;
}

.cat-label {
  font-size: 11px;
  font-weight: 600;
  color: var(--gold-deep);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.inventory-tag {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 10.5px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 6px;
  white-space: nowrap;
}

.inventory-tag .indicator {
  width: 5px;
  height: 5px;
  border-radius: 50%;
}

.in-stock {
  background: #f0fdf4;
  color: #166534;
}

.in-stock .indicator {
  background: #22c55e;
}

.out-of-stock {
  background: #fef2f2;
  color: #991b1b;
}

.out-of-stock .indicator {
  background: #ef4444;
}

.item-heading {
  font-size: 14px;
  font-weight: 700;
  line-height: 1.5;
  margin: 0 0 14px;
  height: 42px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.item-heading a {
  color: var(--ink);
  text-decoration: none;
  transition: color 0.2s ease;
}

.item-heading a:hover {
  color: var(--gold-deep);
}

/* ۵. فوتر قیمت و تراز شدن با دکمه انحنا */
.item-bottom-bar {
  margin-top: auto;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding-top: 12px;
  padding-left: 48px; /* حاشیه امن برای عدم برخورد قیمت با انحنای گوشه چپ */
  border-top: 1px dashed #e4e4e7;
  min-height: 42px;
}

.pricing-col {
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
}

.prev-price {
  font-size: 11px;
  color: #a1a1aa;
  text-decoration: line-through;
  line-height: 1.3;
  margin-bottom: 2px;
  display: block;
  height: 15px;
}

.prev-price.is-hidden {
  visibility: hidden;
}

.active-price {
  display: flex;
  align-items: baseline;
  gap: 3px;
}

.active-price .val {
  font-size: 15.5px;
  font-weight: 800;
  color: #09090b;
}

.active-price small {
  font-size: 10px;
  color: #71717a;
  font-weight: 400;
}

/* ۶. دکمه دایره‌ای کوچک و تراز شده در مرکز انحنای برش */
.corner-cart-btn {
  position: absolute;
  left: 0;
  bottom: 0;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: none;
  background: #18181b;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.25s ease;
  z-index: 3;
}

.corner-cart-btn:hover:not(:disabled) {
  background: var(--gold-deep);
  transform: scale(1.08);
  box-shadow: 0 4px 12px rgba(139, 118, 87, 0.35);
}

.corner-cart-btn:disabled {
  background: #d4d4d8;
  color: #71717a;
  cursor: not-allowed;
}

/* وضعیت‌ها */
.state-box {
  text-align: center;
  padding: 50px 20px;
  color: #857e72;
  font-size: 14px;
  background: #ffffff;
  border-radius: 20px;
  border: 1px solid #f0f0f2;
}

.state-box.has-error {
  color: #dc2626;
}

.retry-action {
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

.loading-ring {
  display: inline-block;
  width: 28px;
  height: 28px;
  border: 3px solid #e4e4e7;
  border-top-color: var(--gold-deep);
  border-radius: 50%;
  animation: spinLoader 0.8s linear infinite;
  margin-bottom: 10px;
}

@keyframes spinLoader {
  to {
    transform: rotate(360deg);
  }
}

/* دکمه‌های ناوبری اسلایدر */
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

/* ریسپانسیو */
@media (max-width: 1024px) {
  .latest-products-section {
    width: 90%;
    margin: 55px auto;
  }
}

@media (max-width: 768px) {
  .latest-products-section {
    width: 92%;
    margin: 45px auto;
  }

  .section-top-bar {
    margin-bottom: 24px;
  }

  .title-group h2 {
    font-size: 20px;
  }

  .title-group p {
    font-size: 12px;
  }

  .see-all-btn {
    font-size: 12px;
    padding: 6px 10px;
  }

  .item-details {
    padding: 12px;
  }

  .item-heading {
    font-size: 13px;
    height: 38px;
  }

  .active-price .val {
    font-size: 14px;
  }

  :deep(.swiper-button-next),
  :deep(.swiper-button-prev) {
    display: none;
  }
}
</style>
