<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useCartStore } from '../stores/cart'
import api, { API_BASE } from '../services/api'

const cartStore = useCartStore()

const normalizedApiBase = (API_BASE || '').trim().replace(/\/+$/, '')

const rawProducts = ref([])
const isLoading = ref(true)
const hasError = ref(false)
const brokenImages = ref({})

const trackRef = ref(null)
const activeSlideIndex = ref(0)

let timerId = null

// دریافت محصولات
const loadOfferProducts = async () => {
  try {
    isLoading.value = true
    hasError.value = false
    brokenImages.value = {}

    const res = await api.get('/api/products')
    const list = Array.isArray(res?.data?.data)
      ? res.data.data
      : Array.isArray(res?.data)
        ? res.data
        : []

    rawProducts.value = list
    activeSlideIndex.value = 0
  } catch (err) {
    console.error('خطا در دریافت محصولات:', err?.response || err)
    hasError.value = true
  } finally {
    isLoading.value = false
  }
}

// آماده‌سازی محصولات تخفیف‌دار
const discountedList = computed(() => {
  return rawProducts.value
    .filter((item) => item.is_active !== false && item.is_active !== 0)
    .map((item) => {
      const activeVariants = (item.variants ?? []).filter(
        (v) => v.is_active !== false && v.is_active !== 0,
      )

      const isSaleVariant = (v) => {
        const price = Number(v.price)
        const discount = Number(v.discount_price)
        return (
          v.price !== null &&
          v.price !== undefined &&
          v.price !== '' &&
          v.discount_price !== null &&
          v.discount_price !== undefined &&
          v.discount_price !== '' &&
          Number.isFinite(price) &&
          Number.isFinite(discount) &&
          price > 0 &&
          discount >= 0 &&
          discount < price
        )
      }

      const saleVariant =
        activeVariants.find((v) => isSaleVariant(v) && Number(v.stock) > 0) ??
        activeVariants.find((v) => isSaleVariant(v))

      const targetVariant =
        saleVariant ??
        activeVariants.find((v) => Number(v.stock) > 0 && Number(v.price) > 0) ??
        activeVariants.find((v) => Number(v.price) > 0) ??
        null

      const itemPrice = Number(item.price)
      const itemDiscount = Number(item.discount_price)

      const hasItemPrice =
        item.price !== null &&
        item.price !== undefined &&
        item.price !== '' &&
        Number.isFinite(itemPrice) &&
        itemPrice > 0

      const hasItemDiscount =
        item.discount_price !== null &&
        item.discount_price !== undefined &&
        item.discount_price !== '' &&
        Number.isFinite(itemDiscount) &&
        itemDiscount >= 0

      const directDiscount = hasItemPrice && hasItemDiscount && itemDiscount < itemPrice

      const isDiscounted = directDiscount || Boolean(saleVariant)

      const finalBase = directDiscount
        ? itemPrice
        : Number(saleVariant?.price ?? (hasItemPrice ? itemPrice : 0))

      const finalSale = directDiscount ? itemDiscount : Number(saleVariant?.discount_price ?? 0)

      const thumbCandidates = [
        targetVariant?.image_url,
        targetVariant?.image,
        item.image_url,
        item.image,
      ].filter((img) => typeof img === 'string' && img.trim() !== '')

      const uniqueThumbs = [...new Set(thumbCandidates)]
      const validThumb = uniqueThumbs.find((img) => !brokenImages.value[`${item.id}::${img}`]) || ''

      return {
        ...item,
        selectedVariant: targetVariant,
        thumbUrl: validThumb,
        baseAmount: finalBase,
        saleAmount: finalSale,
        offPercent:
          isDiscounted && finalBase > 0
            ? Math.round(((finalBase - finalSale) / finalBase) * 100)
            : 0,
        isDiscounted,
      }
    })
    .filter((item) => item.isDiscounted && item.baseAmount > 0)
})

// بررسی موجودی
const getAvailableStock = (item) => {
  const source = item.selectedVariant ?? item
  return Math.max(0, Number(source?.stock ?? 0))
}

// افزودن به سبد خرید
const handleAddToCart = (item) => {
  const stockCount = getAvailableStock(item)

  if (stockCount <= 0 || item.saleAmount <= 0) return

  cartStore.addToCart({
    id: item.id,
    product_id: item.id,
    variant_id: item.selectedVariant?.id ?? null,
    name: item.name,
    image: item.thumbUrl,
    price: item.baseAmount,
    discount_price: item.saleAmount,
    final_price: item.saleAmount,
    color: item.selectedVariant?.color || '',
    size: item.selectedVariant?.size || '',
    quantity: 1,
    stock: stockCount,
  })
}

// ساخت آدرس تصویر سازگار با هاست و HTTPS
const buildImgSrc = (imgPath) => {
  if (!imgPath || typeof imgPath !== 'string') return ''

  const value = imgPath.trim()
  if (!value) return ''

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

  const clean = value.replace(/^\/+/, '').replace(/^storage\/+/, '')

  return normalizedApiBase ? `${normalizedApiBase}/storage/${clean}` : `/storage/${clean}`
}

const onOfferImageError = (item) => {
  if (!item?.thumbUrl) return

  brokenImages.value = {
    ...brokenImages.value,
    [`${item.id}::${item.thumbUrl}`]: true,
  }
}

// فرمت قیمت
const toPersianCurrency = (val) => {
  const num = Number(val)
  return Number.isFinite(num) ? new Intl.NumberFormat('fa-IR').format(num) : '۰'
}

// تایمر شمارش معکوس
const timerState = ref({
  days: '۰۰',
  hours: '۰۰',
  minutes: '۰۰',
  seconds: '۰۰',
})

const refreshTimer = () => {
  const endTimestamp = new Date('2026-11-30T23:59:59').getTime()
  const diff = Math.max(0, endTimestamp - Date.now())

  const d = Math.floor(diff / (1000 * 60 * 60 * 24))
  const h = Math.floor((diff / (1000 * 60 * 60)) % 24)
  const m = Math.floor((diff / (1000 * 60)) % 60)
  const s = Math.floor((diff / 1000) % 60)

  const toFaDigits = (n) =>
    String(n)
      .padStart(2, '0')
      .replace(/\d/g, (dig) => '۰۱۲۳۴۵۶۷۸۹'[Number(dig)])

  timerState.value = {
    days: toFaDigits(d),
    hours: toFaDigits(h),
    minutes: toFaDigits(m),
    seconds: toFaDigits(s),
  }
}

// تعداد کارت‌های قابل مشاهده
const calcVisibleCards = () => {
  if (typeof window === 'undefined') return 1
  if (window.innerWidth <= 600) return 1
  if (window.innerWidth <= 1050) return 2

  return Math.max(1, Math.floor((trackRef.value?.clientWidth ?? 600) / 254))
}

// همگام‌سازی ایندکس اسلاید هنگام اسکرول دستی
const syncSlideIndexOnScroll = () => {
  const container = trackRef.value
  if (!container || !container.children.length) return

  const computed = getComputedStyle(container)
  const rtlMode = computed.direction === 'rtl'
  const padOffset = parseFloat(rtlMode ? computed.paddingRight : computed.paddingLeft) || 0
  const cRect = container.getBoundingClientRect()
  const anchor = rtlMode ? cRect.right - padOffset : cRect.left + padOffset

  let closestIndex = 0
  let minDistance = Infinity

  Array.from(container.children).forEach((child, idx) => {
    const tRect = child.getBoundingClientRect()
    const edge = rtlMode ? tRect.right : tRect.left
    const dist = Math.abs(edge - anchor)
    if (dist < minDistance) {
      minDistance = dist
      closestIndex = idx
    }
  })

  const upperLimit = Math.max(0, discountedList.value.length - calcVisibleCards())
  activeSlideIndex.value = Math.min(closestIndex, upperLimit)
}

// حرکت اسلایدر
const slideTrack = (stepDirection) => {
  if (!trackRef.value) return

  const visible = calcVisibleCards()
  const upperLimit = Math.max(0, discountedList.value.length - visible)

  activeSlideIndex.value = Math.max(
    0,
    Math.min(upperLimit, activeSlideIndex.value + stepDirection * visible),
  )

  const container = trackRef.value
  const targetEl = container.children[activeSlideIndex.value]

  if (targetEl) {
    const computed = getComputedStyle(container)
    const rtlMode = computed.direction === 'rtl'

    const padOffset = parseFloat(rtlMode ? computed.paddingRight : computed.paddingLeft) || 0

    const cRect = container.getBoundingClientRect()
    const tRect = targetEl.getBoundingClientRect()

    const scrollDelta = rtlMode
      ? tRect.right - (cRect.right - padOffset)
      : tRect.left - (cRect.left + padOffset)

    container.scrollBy({
      left: scrollDelta,
      behavior: 'smooth',
    })
  }
}

const onWindowResize = () => {
  if (!trackRef.value) return

  const upperLimit = Math.max(0, discountedList.value.length - calcVisibleCards())

  activeSlideIndex.value = Math.min(activeSlideIndex.value, upperLimit)
}

onMounted(() => {
  loadOfferProducts()
  refreshTimer()

  timerId = setInterval(refreshTimer, 1000)
  window.addEventListener('resize', onWindowResize)
})

onUnmounted(() => {
  if (timerId) clearInterval(timerId)

  window.removeEventListener('resize', onWindowResize)
})
</script>

<template>
  <section class="special-offers-wrapper" dir="rtl">
    <div class="special-offers-container">
      <!-- پنل فروش ویژه -->
      <aside class="promo-panel">
        <div class="ring-decor ring-top"></div>
        <div class="ring-decor ring-bottom"></div>

        <div class="promo-inner">
          <div class="promo-badge-icon">
            <svg viewBox="0 0 64 64" fill="none" aria-hidden="true">
              <path
                d="M23 24v-5a9 9 0 0 1 18 0v5"
                stroke="#fff"
                stroke-width="3.5"
                stroke-linecap="round"
              />
              <path d="M11 23h42l-3 31.2a4 4 0 0 1-4 3.6H18a4 4 0 0 1-4-3.6L11 23Z" fill="#fff" />
              <path
                d="M39.5 33.5 24.5 51"
                stroke="#18181b"
                stroke-width="3.6"
                stroke-linecap="round"
              />
              <circle cx="25" cy="36" r="3.6" fill="#18181b" />
              <circle cx="39" cy="48.5" r="3.6" fill="#18181b" />
              <path
                d="M54 5l1.9 4.6L60.5 11.5l-4.6 1.9L54 18l-1.9-4.6-4.6-1.9 4.6-1.9L54 5Z"
                fill="#fff"
                opacity="0.95"
              />
              <path
                d="M9 8l1.2 3L13.2 12.2l-3 1.2L9 16.4l-1.2-3L4.8 12.2l3-1.2L9 8Z"
                fill="#fff"
                opacity="0.75"
              />
            </svg>
          </div>

          <div class="promo-headline">
            <span>%</span>
            <strong>SALE</strong>
          </div>

          <p class="promo-subtext">محصولات منتخب با تخفیف‌های ویژه؛ فرصت رو از دست نده!</p>

          <!-- شمارش معکوس -->
          <div class="timer-grid">
            <div class="timer-cell">
              <strong>{{ timerState.days }}</strong>
              <span>روز</span>
            </div>

            <span class="timer-colon">:</span>

            <div class="timer-cell">
              <strong>{{ timerState.hours }}</strong>
              <span>ساعت</span>
            </div>

            <span class="timer-colon">:</span>

            <div class="timer-cell">
              <strong>{{ timerState.minutes }}</strong>
              <span>دقیقه</span>
            </div>

            <span class="timer-colon">:</span>

            <div class="timer-cell">
              <strong>{{ timerState.seconds }}</strong>
              <span>ثانیه</span>
            </div>
          </div>

          <RouterLink to="/shop" class="promo-cta">
            مشاهده فروشگاه
            <span>←</span>
          </RouterLink>
        </div>
      </aside>

      <!-- اسلایدر محصولات -->
      <div class="offers-carousel-box">
        <div v-if="isLoading" class="state-placeholder">در حال دریافت محصولات...</div>

        <div v-else-if="hasError" class="state-placeholder">دریافت محصولات با خطا مواجه شد.</div>

        <div v-else-if="discountedList.length === 0" class="state-placeholder">
          در حال حاضر محصول تخفیف‌داری وجود ندارد.
        </div>

        <div v-else class="carousel-stage">
          <button
            class="nav-circle-btn nav-prev"
            type="button"
            aria-label="محصولات قبلی"
            :disabled="activeSlideIndex === 0"
            @click="slideTrack(-1)"
          >
            <svg viewBox="0 0 24 24" fill="none">
              <path
                d="M15 18L9 12L15 6"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
          </button>

          <div class="cards-track" ref="trackRef" @scroll.passive="syncSlideIndexOnScroll">
            <div v-for="item in discountedList" :key="item.id" class="offer-card-holder">
              <article class="offer-card">
                <!-- تصویر محصول -->
                <RouterLink
                  :to="{
                    name: 'product-detail',
                    params: { id: item.id },
                  }"
                  class="offer-thumb"
                >
                  <img
                    v-if="item.thumbUrl"
                    :src="buildImgSrc(item.thumbUrl)"
                    :alt="item.name"
                    loading="lazy"
                    decoding="async"
                    @error="onOfferImageError(item)"
                  />

                  <div v-else class="brand-fallback">ARYSYS</div>

                  <span class="off-pill">
                    {{ item.offPercent.toLocaleString('fa-IR') }}٪ تخفیف
                  </span>
                </RouterLink>

                <!-- جزئیات محصول -->
                <div class="offer-body">
                  <div class="offer-meta">
                    <span class="offer-cat">
                      {{ item.category?.name || 'فروش ویژه' }}
                    </span>

                    <div
                      class="stock-badge"
                      :class="getAvailableStock(item) > 0 ? 'in-stock' : 'out-stock'"
                    >
                      <span class="dot"></span>

                      <span>
                        {{ getAvailableStock(item) > 0 ? 'موجود' : 'ناموجود' }}
                      </span>
                    </div>
                  </div>

                  <h4 class="offer-title" :title="item.name">
                    <RouterLink
                      :to="{
                        name: 'product-detail',
                        params: { id: item.id },
                      }"
                    >
                      {{ item.name }}
                    </RouterLink>
                  </h4>

                  <!-- قیمت -->
                  <div class="offer-footer">
                    <div class="price-stack">
                      <span class="strike-price">
                        {{ toPersianCurrency(item.baseAmount) }}
                        <small>تومان</small>
                      </span>

                      <strong class="final-price">
                        {{ toPersianCurrency(item.saleAmount) }}
                        <small>تومان</small>
                      </strong>
                    </div>
                  </div>
                </div>

                <!-- افزودن به سبد خرید -->
                <button
                  type="button"
                  class="inverted-add-btn"
                  :disabled="getAvailableStock(item) <= 0"
                  :title="getAvailableStock(item) > 0 ? 'افزودن به سبد خرید' : 'ناموجود'"
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
          </div>

          <button
            class="nav-circle-btn nav-next"
            type="button"
            aria-label="محصولات بعدی"
            :disabled="activeSlideIndex >= Math.max(0, discountedList.length - calcVisibleCards())"
            @click="slideTrack(1)"
          >
            <svg viewBox="0 0 24 24" fill="none">
              <path
                d="M9 18L15 12L9 6"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.special-offers-wrapper {
  --brand: #18181b;
  --gold-deep: #8b7657;
  --ink: #1f1a1c;
  --muted: #8a8286;

  width: 100%;
  padding: 50px 0;
  background: #f3f1ed;
  box-sizing: border-box;
  direction: rtl;
  font-family: inherit;
}

.special-offers-container {
  width: 82%;
  max-width: 1360px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 30% minmax(0, 70%);
  gap: 24px;
  box-sizing: border-box;
}

.special-offers-container > * {
  min-width: 0;
  max-width: 100%;
  box-sizing: border-box;
}

/* پنل فروش ویژه */
.promo-panel {
  min-height: 390px;
  position: relative;
  overflow: hidden;
  border-radius: 28px;
  background:
    radial-gradient(120% 90% at 100% 0%, rgba(255, 255, 255, 0.08) 0%, transparent 55%),
    linear-gradient(150deg, #27272a 0%, #18181b 55%, #09090b 100%);
  box-shadow:
    0 24px 50px -22px rgba(0, 0, 0, 0.4),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 28px;
  color: #ffffff;
}

.promo-inner {
  width: 100%;
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.ring-decor {
  position: absolute;
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: 50%;
  pointer-events: none;
}

.ring-top {
  width: 280px;
  height: 280px;
  top: -110px;
  right: -110px;
  box-shadow: 0 0 0 36px rgba(255, 255, 255, 0.04);
}

.ring-bottom {
  width: 230px;
  height: 230px;
  bottom: -110px;
  left: -90px;
  box-shadow: 0 0 0 30px rgba(255, 255, 255, 0.04);
}

.promo-badge-icon {
  width: 72px;
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.18);
  border: 1px solid rgba(255, 255, 255, 0.4);
  box-shadow:
    0 14px 28px -12px rgba(0, 0, 0, 0.3),
    inset 0 1px 0 rgba(255, 255, 255, 0.45);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  margin-bottom: 10px;
}

.promo-badge-icon svg {
  width: 48px;
  height: 48px;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

.promo-headline {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  margin: 12px 0 6px;
}

.promo-headline span {
  font-size: 54px;
  font-weight: 900;
  line-height: 1;
}

.promo-headline strong {
  font-size: 26px;
  letter-spacing: 3px;
}

.promo-subtext {
  max-width: 240px;
  margin: 0 0 20px;
  font-size: 12.5px;
  line-height: 2;
  color: rgba(255, 255, 255, 0.92);
}

/* تایمر */
.timer-grid {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  direction: ltr;
  margin-bottom: 20px;
}

.timer-cell {
  min-width: 46px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 3px;
  padding: 9px 6px 7px;
  border-radius: 13px;
  background: rgba(255, 255, 255, 0.16);
  border: 1px solid rgba(255, 255, 255, 0.28);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}

.timer-cell strong {
  font-size: 19px;
  font-weight: 800;
  font-variant-numeric: tabular-nums;
}

.timer-cell span {
  font-size: 10px;
  opacity: 0.85;
}

.timer-colon {
  font-size: 16px;
  opacity: 0.6;
  margin-bottom: 14px;
}

.promo-cta {
  min-width: 165px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 11px 18px;
  border-radius: 999px;
  background: #ffffff;
  color: #18181b;
  font-size: 12px;
  font-weight: 800;
  text-decoration: none;
  transition: 0.25s;
}

.promo-cta:hover {
  background: var(--gold-deep);
  color: #ffffff;
  transform: translateY(-2px);
}

/* کانتینر اسلایدر */
.offers-carousel-box {
  min-width: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 6px 0;
}

.carousel-stage {
  position: relative;
  min-width: 0;
  width: 100%;
  box-sizing: border-box;
  padding: 0 38px;
}

.cards-track {
  display: flex;
  align-items: stretch;
  gap: 16px;
  width: 100%;
  box-sizing: border-box;
  overflow-x: auto;
  padding: 10px 6px 24px;
  scroll-padding-inline: 6px;
  scroll-snap-type: x mandatory;
  scroll-behavior: smooth;
  overscroll-behavior-x: contain;
  scrollbar-width: none;
  -webkit-overflow-scrolling: touch;
}

.cards-track::-webkit-scrollbar {
  display: none;
}

/* کارت */
.offer-card-holder {
  flex: 0 0 240px;
  min-width: 0;
  display: flex;
  scroll-snap-align: start;
  scroll-snap-stop: always;
  filter: drop-shadow(0 6px 16px rgba(23, 20, 15, 0.06));
  transition:
    transform 0.35s cubic-bezier(0.165, 0.84, 0.44, 1),
    filter 0.35s ease;
}

.offer-card-holder:hover {
  transform: translateY(-6px);
  filter: drop-shadow(0 14px 24px rgba(139, 118, 87, 0.16));
}

.offer-card {
  position: relative;
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  z-index: 1;
}

/* برش منحنی کارت */
.offer-card::before {
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

/* تصویر محصول */
.offer-thumb {
  position: relative;
  width: 100%;
  aspect-ratio: 1 / 1.15;
  flex-shrink: 0;
  overflow: hidden;
  border-radius: 20px 20px 0 0;
  background: #f4f4f5;
  display: block;
  text-decoration: none;
}

.offer-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.offer-card-holder:hover .offer-thumb img {
  transform: scale(1.07);
}

.brand-fallback {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #b9aeb2;
  font-size: 16px;
  letter-spacing: 3px;
}

.off-pill {
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

/* جزئیات کارت */
.offer-body {
  display: flex;
  flex-direction: column;
  flex: 1;
  padding: 14px;
  box-sizing: border-box;
}

.offer-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 6px;
  margin-bottom: 8px;
}

.offer-cat {
  font-size: 11px;
  font-weight: 600;
  color: var(--gold-deep);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.stock-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 10.5px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 6px;
  white-space: nowrap;
}

.stock-badge .dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
}

.in-stock {
  background: #f0fdf4;
  color: #166534;
}

.in-stock .dot {
  background: #22c55e;
}

.out-stock {
  background: #fef2f2;
  color: #991b1b;
}

.out-stock .dot {
  background: #ef4444;
}

.offer-title {
  margin: 0 0 14px;
  font-size: 13.5px;
  font-weight: 700;
  line-height: 1.5;
  height: 40px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.offer-title a {
  color: var(--ink);
  text-decoration: none;
  transition: color 0.2s ease;
}

.offer-title a:hover {
  color: var(--gold-deep);
}

/* قیمت */
.offer-footer {
  margin-top: auto;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding-top: 12px;
  padding-left: 48px;
  border-top: 1px dashed #e4e4e7;
  min-height: 42px;
}

.price-stack {
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
}

.strike-price {
  color: #a1a1aa;
  font-size: 11px;
  text-decoration: line-through;
  line-height: 1.3;
  margin-bottom: 2px;
}

.strike-price small,
.final-price small {
  font-size: 10px;
  font-weight: 400;
  color: #71717a;
}

.final-price {
  color: #09090b;
  font-size: 15.5px;
  font-weight: 800;
  display: flex;
  align-items: baseline;
  gap: 3px;
}

/* دکمه سبد خرید */
.inverted-add-btn {
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

.inverted-add-btn:hover:not(:disabled) {
  background: var(--gold-deep);
  transform: scale(1.08);
  box-shadow: 0 4px 12px rgba(139, 118, 87, 0.35);
}

.inverted-add-btn:disabled {
  background: #d4d4d8;
  color: #71717a;
  cursor: not-allowed;
}

/* دکمه‌های اسلایدر */
.nav-circle-btn {
  position: absolute;
  z-index: 4;
  top: 50%;
  transform: translateY(-50%);
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(228, 228, 231, 0.9);
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  box-shadow: 0 6px 18px -4px rgba(0, 0, 0, 0.12);
  color: var(--ink);
  cursor: pointer;
  transition: all 0.25s ease;
}

.nav-circle-btn svg {
  width: 18px;
  height: 18px;
}

.nav-circle-btn:hover:not(:disabled) {
  background: var(--gold-deep);
  color: #ffffff;
  border-color: var(--gold-deep);
  transform: translateY(-50%) scale(1.05);
}

.nav-circle-btn:disabled {
  opacity: 0.35;
  cursor: not-allowed;
}

.nav-prev {
  right: 0;
}

.nav-next {
  left: 0;
}

/* وضعیت دریافت محصولات */
.state-placeholder {
  min-height: 260px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #ffffff;
  border-radius: 20px;
  color: var(--muted);
  font-size: 14px;
  text-align: center;
}

/* تبلت */
@media (max-width: 1050px) {
  .special-offers-container {
    width: 90%;
    grid-template-columns: minmax(0, 1fr);
    gap: 20px;
  }

  .promo-panel {
    min-height: 300px;
  }

  .offer-card-holder {
    flex-basis: 240px;
  }
}

/* موبایل */
@media (max-width: 600px) {
  .special-offers-wrapper {
    padding: 30px 0;
  }

  .special-offers-container {
    width: 92%;
  }

  .promo-panel {
    min-height: 0;
    padding: 28px 16px;
    border-radius: 22px;
  }

  .promo-badge-icon {
    width: 64px;
    height: 64px;
    border-radius: 20px;
  }

  .promo-badge-icon svg {
    width: 42px;
    height: 42px;
  }

  .promo-headline span {
    font-size: 46px;
  }

  .promo-headline strong {
    font-size: 22px;
  }

  .timer-grid {
    gap: 4px;
  }

  .timer-cell {
    min-width: 42px;
    padding: 8px 4px 6px;
  }

  .timer-cell strong {
    font-size: 17px;
  }

  .carousel-stage {
    padding: 0;
  }

  .cards-track {
    gap: 14px;
    padding: 8px 4px 20px;
  }

  .offer-card-holder {
    flex: 0 0 82%;
  }

  .nav-circle-btn {
    display: none;
  }
}
</style>
