<template>
  <section class="products-showcase" dir="rtl">
    <!-- هدر بخش محصولات -->
    <header class="showcase-header">
      <div class="header-text">
        <h2>محصولات فروشگاه</h2>
        <p>انتخابی خاص برای درخشش در استایل شما</p>
      </div>

      <RouterLink class="all-products-link" to="/shop">
        <span>مشاهده همه</span>
        <span class="icon-arrow">←</span>
      </RouterLink>
    </header>

    <!-- وضعیت در حال بارگذاری -->
    <div v-if="isLoading" class="status-box" role="status">
      <span class="spinner" aria-hidden="true"></span>
      <p>در حال دریافت محصولات...</p>
    </div>

    <!-- وضعیت بروز خطا -->
    <div v-else-if="errorMessage" class="status-box is-error" role="alert">
      <p>{{ errorMessage }}</p>
      <button type="button" class="btn-retry" @click="loadProducts">تلاش مجدد</button>
    </div>

    <!-- وضعیت خالی بودن لیست -->
    <div v-else-if="!productList.length" class="status-box">محصولی برای نمایش وجود ندارد.</div>

    <!-- اسلایدر محصولات -->
    <Swiper
      v-else
      :modules="[Navigation]"
      :slides-per-view="1.3"
      :space-between="14"
      :navigation="true"
      :breakpoints="swiperBreakpoints"
      class="products-swiper"
    >
      <SwiperSlide v-for="item in productList" :key="item.id">
        <div class="card-shadow-wrap">
          <article class="product-card">
            <!-- تصویر محصول -->
            <RouterLink
              :to="{ name: 'product-detail', params: { id: item.id } }"
              class="product-thumb"
            >
              <img
                v-if="hasValidImage(item)"
                :key="`${item.id}-${resolveImage(item)}`"
                :src="buildImageUrl(resolveImage(item))"
                :alt="item.name"
                loading="lazy"
                decoding="async"
                @error="onImageError(item)"
              />

              <div v-else class="empty-thumb">
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

              <!-- بج تخفیف -->
              <span v-if="calcPricing(item).hasDiscount" class="discount-tag">
                {{ calcDiscountPercent(item).toLocaleString('fa-IR') }}٪ تخفیف
              </span>
            </RouterLink>

            <!-- بدنه کارت -->
            <div class="product-body">
              <div class="top-meta">
                <span class="category-name">
                  {{ item.category?.name || 'کالکشن اختصاصی' }}
                </span>

                <div class="stock-pill" :class="checkStock(item) > 0 ? 'is-in' : 'is-out'">
                  <span class="dot"></span>
                  <span>{{ checkStock(item) > 0 ? 'موجود' : 'ناموجود' }}</span>
                </div>
              </div>

              <h3 class="product-title" :title="item.name">
                <RouterLink :to="{ name: 'product-detail', params: { id: item.id } }">
                  {{ item.name }}
                </RouterLink>
              </h3>

              <!-- فوتر قیمت -->
              <div class="card-footer">
                <div class="price-col">
                  <span
                    class="old-price"
                    :class="{ 'is-invisible': !calcPricing(item).hasDiscount }"
                  >
                    {{
                      calcPricing(item).hasDiscount
                        ? toPersianPrice(calcPricing(item).basePrice)
                        : '-'
                    }}
                  </span>

                  <div class="current-price">
                    <span class="amount">{{ toPersianPrice(calcPricing(item).finalPrice) }}</span>
                    <small v-if="calcPricing(item).finalPrice !== null">تومان</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- دکمه دایره‌ای افزودن به سبد خرید -->
            <button
              type="button"
              class="inverted-cart-btn"
              :disabled="checkStock(item) <= 0 || calcPricing(item).finalPrice === null"
              :title="checkStock(item) > 0 ? 'افزودن به سبد خرید' : 'ناموجود'"
              @click="handleAddToCart(item)"
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

const cartStore = useCartStore()

const BASE_URL = (import.meta.env.VITE_API_URL || '').trim().replace(/\/+$/, '')

const productList = ref([])
const isLoading = ref(true)
const errorMessage = ref('')
const brokenImages = ref({})

const swiperBreakpoints = {
  520: { slidesPerView: 2.2, spaceBetween: 16 },
  768: { slidesPerView: 3.2, spaceBetween: 18 },
  1024: { slidesPerView: 4.2, spaceBetween: 20 },
  1280: { slidesPerView: 5, spaceBetween: 22 },
}

const getPrimaryVariant = (item) => {
  const activeList = (item.variants || []).filter((v) => v.is_active !== false && v.is_active !== 0)

  return activeList.find((v) => Number(v.stock) > 0) || activeList[0] || null
}

const resolveImage = (item) => {
  const activeVar = getPrimaryVariant(item)
  const candidates = [activeVar?.image_url, activeVar?.image, item.image_url, item.image].filter(
    (img) => typeof img === 'string' && img.trim() !== '',
  )

  const uniqueCandidates = [...new Set(candidates)]

  for (const raw of uniqueCandidates) {
    if (!brokenImages.value[`${item.id}::${raw}`]) {
      return raw
    }
  }

  return ''
}

const hasValidImage = (item) => Boolean(resolveImage(item))

const buildImageUrl = (rawPath) => {
  if (!rawPath || typeof rawPath !== 'string') return ''

  const trimmed = rawPath.trim()
  if (!trimmed) return ''

  if (/^https?:\/\//i.test(trimmed)) {
    if (
      typeof window !== 'undefined' &&
      window.location.protocol === 'https:' &&
      trimmed.startsWith('http://')
    ) {
      return trimmed.replace(/^http:\/\//i, 'https://')
    }
    return trimmed
  }

  if (trimmed.startsWith('//')) {
    const proto = typeof window !== 'undefined' ? window.location.protocol : 'https:'
    return `${proto}${trimmed}`
  }

  const normalized = trimmed.replace(/^\/+/, '').replace(/^storage\/+/, '')

  return BASE_URL ? `${BASE_URL}/storage/${normalized}` : `/storage/${normalized}`
}

const onImageError = (item) => {
  const current = resolveImage(item)
  if (!current) return

  brokenImages.value = {
    ...brokenImages.value,
    [`${item.id}::${current}`]: true,
  }
}

const calcPricing = (item) => {
  const target = getPrimaryVariant(item) || item

  const hasBase = target.price !== null && target.price !== undefined && target.price !== ''

  const basePrice = Number(target.price)
  const salePrice = Number(target.discount_price)

  const hasDiscount =
    hasBase &&
    target.discount_price !== null &&
    target.discount_price !== undefined &&
    target.discount_price !== '' &&
    Number.isFinite(basePrice) &&
    Number.isFinite(salePrice) &&
    salePrice >= 0 &&
    salePrice < basePrice

  const validBase = hasBase && Number.isFinite(basePrice) ? basePrice : null

  return {
    basePrice: validBase,
    finalPrice: hasDiscount ? salePrice : validBase,
    hasDiscount,
  }
}

const calcDiscountPercent = (item) => {
  const { hasDiscount, basePrice, finalPrice } = calcPricing(item)

  if (!hasDiscount || !basePrice) return 0

  return Math.round(((basePrice - finalPrice) / basePrice) * 100)
}

const checkStock = (item) => {
  const activeVar = getPrimaryVariant(item)
  const count = activeVar ? activeVar.stock : item.stock

  return Math.max(0, Number(count) || 0)
}

const toPersianPrice = (val) => {
  if (val === null || val === undefined || val === '' || !Number.isFinite(Number(val))) {
    return 'قیمت ثبت نشده'
  }

  return Number(val).toLocaleString('fa-IR')
}

const handleAddToCart = (item) => {
  const currentStock = checkStock(item)
  const { basePrice, finalPrice, hasDiscount } = calcPricing(item)

  if (currentStock <= 0 || finalPrice === null) return

  const activeVar = getPrimaryVariant(item)

  cartStore.addToCart({
    id: item.id,
    product_id: item.id,
    variant_id: activeVar?.id ?? null,
    name: item.name,
    image: resolveImage(item),
    price: basePrice,
    discount_price: hasDiscount ? finalPrice : null,
    final_price: finalPrice,
    variant_id_or_size: activeVar?.size ?? null,
    size: activeVar?.size ?? '',
    color: activeVar?.color ?? '',
    quantity: 1,
    stock: currentStock,
  })
}

const loadProducts = async () => {
  try {
    isLoading.value = true
    errorMessage.value = ''
    brokenImages.value = {}

    const endpoint = BASE_URL ? `${BASE_URL}/api/products` : '/api/products'
    const res = await fetch(endpoint, {
      headers: {
        Accept: 'application/json',
      },
    })

    if (!res.ok) throw new Error('دریافت محصولات با خطا مواجه شد.')

    const json = await res.json()
    const list = Array.isArray(json) ? json : (json.data ?? [])

    productList.value = list.filter((item) => item.is_active !== false && item.is_active !== 0)
  } catch (err) {
    console.error(err)
    errorMessage.value = err.message || 'ارتباط با سرور برقرار نشد.'
  } finally {
    isLoading.value = false
  }
}

onMounted(loadProducts)
</script>

<style scoped>
.products-showcase {
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

.showcase-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 34px;
}

.header-text h2 {
  position: relative;
  display: inline-block;
  margin: 0 0 10px;
  padding-bottom: 12px;
  font-size: 26px;
  font-weight: 800;
  color: var(--ink);
  letter-spacing: -0.5px;
}

.header-text h2::after {
  content: '';
  position: absolute;
  right: 0;
  bottom: 0;
  width: 44px;
  height: 3px;
  border-radius: 3px;
  background: linear-gradient(90deg, var(--gold-light), var(--gold));
}

.header-text p {
  margin: 0;
  font-size: 14px;
  color: #857e72;
}

.all-products-link {
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

.all-products-link:hover {
  background: var(--gold-deep);
  color: #ffffff;
}

.icon-arrow {
  transition: transform 0.25s ease;
}

.all-products-link:hover .icon-arrow {
  transform: translateX(-4px);
}

.products-swiper {
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

.card-shadow-wrap {
  width: 100%;
  display: flex;
  filter: drop-shadow(0 4px 14px rgba(23, 20, 15, 0.06));
  transition:
    transform 0.35s cubic-bezier(0.165, 0.84, 0.44, 1),
    filter 0.35s ease;
}

.card-shadow-wrap:hover {
  transform: translateY(-6px);
  filter: drop-shadow(0 14px 24px rgba(139, 118, 87, 0.16));
}

.product-card {
  position: relative;
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  z-index: 1;
}

.product-card::before {
  --r: 16px;
  --s: 20px;
  --x: 6px;
  --y: 6px;

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

.product-thumb {
  position: relative;
  width: 100%;
  aspect-ratio: 1 / 1.18;
  flex-shrink: 0;
  overflow: hidden;
  background: #f4f4f5;
  border-radius: 20px 20px 0 0;
  display: block;
  text-decoration: none;
}

.product-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.card-shadow-wrap:hover .product-thumb img {
  transform: scale(1.07);
}

.empty-thumb {
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  color: #a1a1aa;
  font-size: 11px;
}

.discount-tag {
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

.product-body {
  display: flex;
  flex-direction: column;
  flex: 1;
  padding: 14px;
  box-sizing: border-box;
}

.top-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 6px;
  margin-bottom: 8px;
}

.category-name {
  font-size: 11px;
  font-weight: 600;
  color: var(--gold-deep);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.stock-pill {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 10.5px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 6px;
  white-space: nowrap;
}

.stock-pill .dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
}

.is-in {
  background: #f0fdf4;
  color: #166534;
}

.is-in .dot {
  background: #22c55e;
}

.is-out {
  background: #fef2f2;
  color: #991b1b;
}

.is-out .dot {
  background: #ef4444;
}

.product-title {
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

.product-title a {
  color: var(--ink);
  text-decoration: none;
  transition: color 0.2s ease;
}

.product-title a:hover {
  color: var(--gold-deep);
}

.card-footer {
  margin-top: auto;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding-top: 12px;
  padding-left: 48px;
  border-top: 1px dashed #e4e4e7;
  min-height: 42px;
}

.price-col {
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
}

.old-price {
  font-size: 11px;
  color: #a1a1aa;
  text-decoration: line-through;
  line-height: 1.3;
  margin-bottom: 2px;
  display: block;
  height: 15px;
}

.old-price.is-invisible {
  visibility: hidden;
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

.inverted-cart-btn {
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

.inverted-cart-btn:hover:not(:disabled) {
  background: var(--gold-deep);
  transform: scale(1.08);
  box-shadow: 0 4px 12px rgba(139, 118, 87, 0.35);
}

.inverted-cart-btn:disabled {
  background: #d4d4d8;
  color: #71717a;
  cursor: not-allowed;
}

.status-box {
  text-align: center;
  padding: 50px 20px;
  color: #857e72;
  font-size: 14px;
  background: #ffffff;
  border-radius: 20px;
  border: 1px solid #f0f0f2;
}

.status-box.is-error {
  color: #dc2626;
}

.btn-retry {
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

.spinner {
  display: inline-block;
  width: 28px;
  height: 28px;
  border: 3px solid #e4e4e7;
  border-top-color: var(--gold-deep);
  border-radius: 50%;
  animation: rotateSpinner 0.8s linear infinite;
  margin-bottom: 10px;
}

@keyframes rotateSpinner {
  to {
    transform: rotate(360deg);
  }
}

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

@media (max-width: 1024px) {
  .products-showcase {
    width: 90%;
    margin: 55px auto;
  }
}

@media (max-width: 768px) {
  .products-showcase {
    width: 92%;
    margin: 45px auto;
  }

  .showcase-header {
    margin-bottom: 24px;
  }

  .header-text h2 {
    font-size: 20px;
  }

  .header-text p {
    font-size: 12px;
  }

  .all-products-link {
    font-size: 12px;
    padding: 6px 10px;
  }

  .product-body {
    padding: 12px;
  }

  .product-title {
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
