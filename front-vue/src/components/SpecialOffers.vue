<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import api from '../services/api'

const products = ref([])
const loading = ref(true)
const error = ref(false)

const sliderRef = ref(null)
const currentIndex = ref(0)

let countdownInterval = null

// دریافت محصولات از API
const fetchProducts = async () => {
  try {
    loading.value = true
    error.value = false

    const response = await api.get('/api/products')
    products.value = response.data.data ?? response.data

    currentIndex.value = 0
  } catch (err) {
    console.error('خطا در دریافت محصولات:', err)
    error.value = true
  } finally {
    loading.value = false
  }
}

// آماده‌سازی محصولات دارای تخفیف
const displayProducts = computed(() => {
  return products.value
    .filter((product) => product.is_active !== false)
    .map((product) => {
      const variants = (product.variants ?? []).filter((variant) => variant.is_active !== false)

      // پیدا کردن تنوع دارای تخفیف
      const discountedVariant = variants.find((variant) => {
        const price = Number(variant.price)
        const discount = Number(variant.discount_price)

        return price > 0 && discount > 0 && discount < price
      })

      const selectedVariant =
        discountedVariant ?? variants.find((variant) => Number(variant.price) > 0)

      const originalPrice = Number(product.price ?? selectedVariant?.price ?? 0)

      const discountPrice = Number(product.discount_price ?? selectedVariant?.discount_price ?? 0)

      const hasProductDiscount =
        originalPrice > 0 && discountPrice > 0 && discountPrice < originalPrice

      const hasDiscount = hasProductDiscount || Boolean(discountedVariant)

      const finalOriginalPrice = hasProductDiscount
        ? originalPrice
        : Number(discountedVariant?.price ?? originalPrice)

      const finalDiscountPrice = hasProductDiscount
        ? discountPrice
        : Number(discountedVariant?.discount_price ?? 0)

      return {
        ...product,
        displayImage: product.image || selectedVariant?.image || null,
        displayPrice: finalOriginalPrice,
        displayDiscountPrice: finalDiscountPrice,
        discountPercent: hasDiscount
          ? Math.round(((finalOriginalPrice - finalDiscountPrice) / finalOriginalPrice) * 100)
          : 0,
        hasDiscount,
      }
    })
    .filter((product) => product.hasDiscount)
})

// ساخت آدرس تصویر
const imageUrl = (image) => {
  if (!image) return ''

  if (/^https?:\/\//i.test(image)) {
    return image
  }

  const baseUrl = (import.meta.env.VITE_API_URL || 'http://localhost:8000').replace(/\/+$/, '')

  const path = String(image).replace(/^\/+/, '')

  if (path.startsWith('storage/')) {
    return `${baseUrl}/${path}`
  }

  return `${baseUrl}/storage/${path}`
}

// فرمت قیمت
const formatPrice = (price) => {
  return new Intl.NumberFormat('fa-IR').format(Number(price))
}

// شمارش معکوس تا پایان فروش ویژه
const countdown = ref({
  days: '۰۰',
  hours: '۰۰',
  minutes: '۰۰',
  seconds: '۰۰',
})

const updateCountdown = () => {
  const targetDate = new Date('2026-11-30T23:59:59').getTime()
  const now = Date.now()
  const difference = Math.max(0, targetDate - now)

  const days = Math.floor(difference / (1000 * 60 * 60 * 24))
  const hours = Math.floor((difference / (1000 * 60 * 60)) % 24)
  const minutes = Math.floor((difference / (1000 * 60)) % 60)
  const seconds = Math.floor((difference / 1000) % 60)

  const fa = (number) =>
    String(number)
      .padStart(2, '0')
      .replace(/\d/g, (digit) => '۰۱۲۳۴۵۶۷۸۹'[Number(digit)])

  countdown.value = {
    days: fa(days),
    hours: fa(hours),
    minutes: fa(minutes),
    seconds: fa(seconds),
  }
}

// تعداد کارت‌های قابل مشاهده
const getVisibleCount = () => {
  if (window.innerWidth <= 600) return 1
  if (window.innerWidth <= 1050) return 2
  return 4
}

// حرکت اسلایدر
const moveSlider = (direction) => {
  if (!sliderRef.value) return

  const visibleCount = getVisibleCount()

  const maxIndex = Math.max(0, displayProducts.value.length - visibleCount)

  currentIndex.value = Math.max(
    0,
    Math.min(maxIndex, currentIndex.value + direction * visibleCount),
  )

  const list = sliderRef.value
  const target = list.children[currentIndex.value]

  if (target) {
    // scrollIntoView در حالت راست‌به‌چپ کارت را به لبه می‌چسباند؛
    // به‌جای آن، فاصله‌ی لازم را مستقیم از روی موقعیت کارت و padding حساب می‌کنیم.
    const style = getComputedStyle(list)
    const isRtl = style.direction === 'rtl'
    const padding = parseFloat(isRtl ? style.paddingRight : style.paddingLeft) || 0

    const listRect = list.getBoundingClientRect()
    const targetRect = target.getBoundingClientRect()

    const delta = isRtl
      ? targetRect.right - (listRect.right - padding)
      : targetRect.left - (listRect.left + padding)

    list.scrollBy({ left: delta, behavior: 'smooth' })
  }
}

// اصلاح موقعیت اسلایدر هنگام تغییر اندازه صفحه
const handleResize = () => {
  if (!sliderRef.value) return

  const maxIndex = Math.max(0, displayProducts.value.length - getVisibleCount())

  currentIndex.value = Math.min(currentIndex.value, maxIndex)
}

// اجرای اولیه
onMounted(() => {
  fetchProducts()

  updateCountdown()
  countdownInterval = setInterval(updateCountdown, 1000)

  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
  }

  window.removeEventListener('resize', handleResize)
})
</script>

<template>
  <section class="special-offers" dir="rtl">
    <!-- پنل فروش ویژه سمت راست -->
    <div class="offer-banner">
      <div class="banner-decoration decoration-one"></div>
      <div class="banner-decoration decoration-two"></div>

      <div class="banner-content">
        <!-- تصویر جدید: کیف خرید با نشان تخفیف -->
        <div class="banner-icon">
          <svg viewBox="0 0 64 64" fill="none" aria-hidden="true">
            <!-- دسته کیف -->
            <path
              d="M23 24v-5a9 9 0 0 1 18 0v5"
              stroke="#fff"
              stroke-width="3.5"
              stroke-linecap="round"
            />
            <!-- بدنه کیف -->
            <path d="M11 23h42l-3 31.2a4 4 0 0 1-4 3.6H18a4 4 0 0 1-4-3.6L11 23Z" fill="#fff" />
            <!-- نشان درصد روی کیف -->
            <path
              d="M39.5 33.5 24.5 51"
              stroke="#e11d48"
              stroke-width="3.6"
              stroke-linecap="round"
            />
            <circle cx="25" cy="36" r="3.6" fill="#e11d48" />
            <circle cx="39" cy="48.5" r="3.6" fill="#e11d48" />
            <!-- ستاره‌های درخشان -->
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

        <div class="banner-percent">
          <span>%</span>
          <strong>SALE</strong>
        </div>

        <p class="banner-description">محصولات منتخب با تخفیف‌های ویژه؛ فرصت رو از دست نده!</p>

        <!-- شمارش معکوس -->
        <div class="countdown">
          <div class="countdown-item">
            <strong>{{ countdown.days }}</strong>
            <span>روز</span>
          </div>

          <span class="countdown-separator">:</span>

          <div class="countdown-item">
            <strong>{{ countdown.hours }}</strong>
            <span>ساعت</span>
          </div>

          <span class="countdown-separator">:</span>

          <div class="countdown-item">
            <strong>{{ countdown.minutes }}</strong>
            <span>دقیقه</span>
          </div>

          <span class="countdown-separator">:</span>

          <div class="countdown-item">
            <strong>{{ countdown.seconds }}</strong>
            <span>ثانیه</span>
          </div>
        </div>
        <RouterLink to="/shop" class="banner-button">
          مشاهده فروشگاه
          <span>←</span>
        </RouterLink>

        <!-- دکمه مشاهده فروشگاه در صورت نیاز -->
        <!--
        <RouterLink to="/shop" class="banner-button">
          مشاهده فروشگاه
          <span>←</span>
        </RouterLink>
        -->
      </div>
    </div>

    <!-- بخش محصولات -->
    <div class="products-wrapper">
      <!--
      <div class="products-header">
        <div>
          <span class="products-subtitle">پیشنهادهای جذاب برای شما</span>
          <h3>منتخب محصولات ARYSYS</h3>
        </div>

        <RouterLink to="/shop" class="all-products">
          مشاهده همه
          <span>←</span>
        </RouterLink>
      </div>
      -->

      <div v-if="loading" class="products-message">در حال دریافت محصولات...</div>

      <div v-else-if="error" class="products-message">دریافت محصولات با خطا مواجه شد.</div>

      <div v-else-if="displayProducts.length === 0" class="products-message">
        در حال حاضر محصول تخفیف‌داری وجود ندارد.
      </div>

      <div v-else class="slider-container">
        <!-- دکمه قبلی -->
        <button
          class="slider-arrow prev-arrow"
          type="button"
          aria-label="محصولات قبلی"
          :disabled="currentIndex === 0"
          @click="moveSlider(-1)"
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

        <!-- کارت‌های محصولات -->
        <div class="products-list" ref="sliderRef">
          <article v-for="product in displayProducts" :key="product.id" class="product-card">
            <RouterLink :to="`/product/${product.slug}`" class="product-image">
              <img
                v-if="product.displayImage"
                :src="imageUrl(product.displayImage)"
                :alt="product.name"
                loading="lazy"
                @error="$event.target.style.display = 'none'"
              />

              <div v-if="!product.displayImage" class="image-placeholder">ARYSYS</div>

              <span class="discount-label"> {{ product.discountPercent }}٪ تخفیف </span>
            </RouterLink>

            <div class="product-details">
              <h4>
                <RouterLink :to="`/product/${product.slug}`">
                  {{ product.name }}
                </RouterLink>
              </h4>

              <div class="price-container">
                <span class="old-price">
                  {{ formatPrice(product.displayPrice) }}
                  <small>تومان</small>
                </span>

                <strong class="new-price">
                  {{ formatPrice(product.displayDiscountPrice) }}
                  <small>تومان</small>
                </strong>
              </div>

              <!-- دکمه مشاهده محصول -->
              <RouterLink :to="`/product/${product.slug}`" class="product-button">
                مشاهده محصول
                <span>←</span>
              </RouterLink>
            </div>
          </article>
        </div>

        <!-- دکمه بعدی -->
        <button
          class="slider-arrow next-arrow"
          type="button"
          aria-label="محصولات بعدی"
          :disabled="currentIndex >= Math.max(0, displayProducts.length - getVisibleCount())"
          @click="moveSlider(1)"
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
  </section>
</template>

<style scoped>
/* ───────── توکن‌های رنگ ───────── */

.special-offers {
  --brand: #e11d48;
  --brand-dark: #be123c;
  --brand-soft: #fff1f3;
  --ink: #1f1a1c;
  --muted: #8a8286;
  --glass-bg: rgba(255, 255, 255, 0.55);
  --glass-border: rgba(255, 255, 255, 0.75);
  --glass-shadow: 0 20px 50px -20px rgba(190, 18, 60, 0.28), 0 2px 6px rgba(31, 26, 28, 0.04);

  position: relative;
  isolation: isolate;
  overflow: hidden;
  width: 100%;
  max-width: 100%;
  display: grid;
  grid-template-columns: 30% minmax(0, 70%);
  gap: 24px;
  padding: 56px 5%;
  box-sizing: border-box;
  direction: rtl;
  font-family: inherit;
  background: linear-gradient(180deg, #fff7f8 0%, #fdf2f4 100%);
}

/* هر دو ستون اجازه کوچک شدن دارند تا چیزی از کادر بیرون نزند */
.special-offers > * {
  min-width: 0;
  max-width: 100%;
  box-sizing: border-box;
}

/* کره‌های رنگی پشت شیشه تا اثر بلور دیده شود */

.special-offers::before,
.special-offers::after {
  content: '';
  position: absolute;
  z-index: -1;
  border-radius: 50%;
  filter: blur(70px);
  pointer-events: none;
}

.special-offers::before {
  width: 380px;
  height: 380px;
  top: -120px;
  left: 18%;
  background: rgba(244, 63, 94, 0.28);
}

.special-offers::after {
  width: 340px;
  height: 340px;
  bottom: -140px;
  left: -60px;
  background: rgba(251, 146, 60, 0.22);
}

/* ───────── پنل فروش ویژه ───────── */

.offer-banner {
  min-height: 390px;
  position: relative;
  overflow: hidden;
  border-radius: 28px;
  background:
    radial-gradient(120% 90% at 100% 0%, rgba(255, 255, 255, 0.22) 0%, transparent 55%),
    linear-gradient(150deg, #fb4a63 0%, #e11d48 55%, #9f1239 100%);
  box-shadow:
    0 24px 50px -22px rgba(190, 18, 60, 0.7),
    inset 0 1px 0 rgba(255, 255, 255, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 28px;
  box-sizing: border-box;
  color: white;
}

.banner-content {
  width: 100%;
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.banner-decoration {
  position: absolute;
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: 50%;
  pointer-events: none;
}

.decoration-one {
  width: 280px;
  height: 280px;
  top: -110px;
  right: -110px;
  box-shadow: 0 0 0 36px rgba(255, 255, 255, 0.04);
}

.decoration-two {
  width: 230px;
  height: 230px;
  bottom: -110px;
  left: -90px;
  box-shadow: 0 0 0 30px rgba(255, 255, 255, 0.04);
}

/* آیکون/تصویر بالای پنل */

.banner-icon {
  width: 72px;
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.18);
  border: 1px solid rgba(255, 255, 255, 0.4);
  box-shadow:
    0 14px 28px -12px rgba(120, 10, 40, 0.55),
    inset 0 1px 0 rgba(255, 255, 255, 0.45);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  margin-bottom: 10px;
}

.banner-icon svg {
  width: 48px;
  height: 48px;
  filter: drop-shadow(0 4px 8px rgba(120, 10, 40, 0.3));
}

.banner-eyebrow {
  font-size: 12px;
  opacity: 0.9;
}

.banner-percent {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  margin: 12px 0 6px;
}

.banner-percent span {
  font-size: 54px;
  font-weight: 900;
  line-height: 1;
  text-shadow: 0 6px 20px rgba(0, 0, 0, 0.18);
}

.banner-percent strong {
  font-size: 26px;
  letter-spacing: 3px;
}

.banner-content h2 {
  margin: 5px 0 10px;
  font-size: 27px;
  font-weight: 900;
  line-height: 1.5;
}

.banner-content h2 span {
  display: block;
  font-size: 19px;
  letter-spacing: 3px;
}

.banner-description {
  max-width: 240px;
  margin: 0 0 20px;
  font-size: 12.5px;
  line-height: 2;
  color: rgba(255, 255, 255, 0.92);
}

/* شمارش معکوس */

.countdown {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  direction: ltr;
  margin-bottom: 20px;
}

.countdown-item {
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

.countdown-item strong {
  font-size: 19px;
  font-weight: 800;
  font-variant-numeric: tabular-nums;
}

.countdown-item span {
  font-size: 10px;
  opacity: 0.85;
}

.countdown-separator {
  font-size: 16px;
  opacity: 0.6;
  margin-bottom: 14px;
}

.banner-button {
  min-width: 165px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 11px 18px;
  border-radius: 999px;
  background: white;
  color: var(--brand);
  font-size: 12px;
  font-weight: 800;
  text-decoration: none;
  transition: 0.25s;
}

.banner-button:hover {
  background: #292929;
  color: white;
  transform: translateY(-2px);
}

/* ───────── بخش محصولات (پس‌زمینه شیشه‌ای) ───────── */

.products-wrapper {
  min-width: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 26px 18px 14px;
  border-radius: 28px;
  background: var(--glass-bg);
  backdrop-filter: blur(22px) saturate(170%);
  -webkit-backdrop-filter: blur(22px) saturate(170%);
  border: 1px solid var(--glass-border);
  box-shadow:
    var(--glass-shadow),
    inset 0 1px 0 rgba(255, 255, 255, 0.9);
}

.products-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 22px;
}

.products-subtitle {
  display: block;
  color: var(--brand);
  font-size: 12px;
  margin-bottom: 7px;
}

.products-header h3 {
  margin: 0;
  font-size: 22px;
  font-weight: 800;
  color: var(--ink);
}

.all-products {
  display: flex;
  align-items: center;
  gap: 9px;
  color: var(--brand);
  font-size: 12px;
  text-decoration: none;
  white-space: nowrap;
}

/* ───────── اسلایدر ───────── */

.slider-container {
  position: relative;
  min-width: 0;
  width: 100%;
  box-sizing: border-box;
  padding: 0 38px;
}

.products-list {
  display: flex;
  gap: 14px;
  width: 100%;
  box-sizing: border-box;
  overflow-x: auto;
  padding: 8px 4px 20px;
  /* کارت‌ها دقیقاً با فاصله‌ی padding جا می‌افتند و به لبه نمی‌چسبند */
  scroll-padding-inline: 4px;
  scroll-snap-type: x mandatory;
  scroll-behavior: smooth;
  overscroll-behavior-x: contain;
  scrollbar-width: none;
  -webkit-overflow-scrolling: touch;
}

.products-list::-webkit-scrollbar {
  display: none;
}

/* ───────── کارت محصول ───────── */

.product-card {
  flex: 0 0 calc((100% - 42px) / 4);
  min-width: 0;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid rgba(255, 255, 255, 0.9);
  border-radius: 20px;
  box-shadow: 0 6px 18px -8px rgba(31, 26, 28, 0.14);
  scroll-snap-align: start;
  scroll-snap-stop: always;
  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 18px 32px -14px rgba(190, 18, 60, 0.35);
}

.product-image {
  height: 210px;
  display: block;
  position: relative;
  overflow: hidden;
  margin: 6px 6px 0;
  border-radius: 15px;
  background: #f6f1f2;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.product-card:hover .product-image img {
  transform: scale(1.06);
}

.image-placeholder {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #b9aeb2;
  font-size: 17px;
  letter-spacing: 3px;
}

.discount-label {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(225, 29, 72, 0.88);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  border: 1px solid rgba(255, 255, 255, 0.35);
  color: white;
  padding: 5px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 800;
}

.product-details {
  padding: 14px 12px 12px;
}

.product-details h4 {
  margin: 0 0 12px;
  font-size: 13px;
  font-weight: 700;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.product-details h4 a {
  color: var(--ink);
  text-decoration: none;
}

.price-container {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.old-price {
  color: var(--muted);
  font-size: 11px;
  text-decoration: line-through;
}

.old-price small,
.new-price small {
  font-size: 9px;
  font-weight: 400;
}

.new-price {
  color: var(--brand);
  font-size: 16px;
  font-weight: 800;
}

/* دکمه مشاهده محصول */

.product-button {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 14px;
  padding: 10px;
  box-sizing: border-box;
  border: 1px solid var(--brand);
  border-radius: 12px;
  background: linear-gradient(135deg, var(--brand), var(--brand-dark));
  color: white;
  font-size: 12px;
  font-weight: 700;
  text-decoration: none;
  transition: 0.25s;
}

.product-button:hover {
  background: white;
  color: var(--brand);
}

.product-button span {
  transition: transform 0.25s;
}

.product-button:hover span {
  transform: translateX(-4px);
}

/* ───────── دکمه‌های اسلایدر ───────── */

.slider-arrow {
  position: absolute;
  z-index: 2;
  top: 50%;
  transform: translateY(-50%);
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(255, 255, 255, 0.85);
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.75);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  box-shadow: 0 6px 16px -6px rgba(31, 26, 28, 0.25);
  color: var(--ink);
  cursor: pointer;
  transition: 0.2s;
}

.slider-arrow svg {
  width: 18px;
  height: 18px;
}

.slider-arrow:hover:not(:disabled) {
  background: var(--brand);
  color: white;
  border-color: var(--brand);
}

.slider-arrow:disabled {
  opacity: 0.35;
  cursor: not-allowed;
}

.prev-arrow {
  right: 0;
}

.next-arrow {
  left: 0;
}

.products-message {
  min-height: 220px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--muted);
  font-size: 14px;
  text-align: center;
}

/* دسترسی‌پذیری */

.slider-arrow:focus-visible,
.product-button:focus-visible,
.product-image:focus-visible,
.product-details h4 a:focus-visible {
  outline: 2px solid var(--brand);
  outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
  .product-card,
  .product-image img,
  .product-button,
  .product-button span,
  .slider-arrow {
    transition: none;
  }

  .products-list {
    scroll-behavior: auto;
  }
}

/* ───────── تبلت ───────── */

@media (max-width: 1050px) {
  .special-offers {
    grid-template-columns: minmax(0, 1fr);
    gap: 28px;
  }

  .offer-banner {
    min-height: 300px;
  }

  .product-card {
    flex-basis: calc((100% - 14px) / 2);
  }
}

/* ───────── موبایل ───────── */

@media (max-width: 600px) {
  .special-offers {
    padding: 28px 4%;
    gap: 20px;
  }

  .offer-banner {
    min-height: 0;
    padding: 28px 16px;
    border-radius: 22px;
  }

  .banner-icon {
    width: 64px;
    height: 64px;
    border-radius: 20px;
  }

  .banner-icon svg {
    width: 42px;
    height: 42px;
  }

  .banner-percent span {
    font-size: 46px;
  }

  .banner-percent strong {
    font-size: 22px;
  }

  .banner-content h2 {
    font-size: 24px;
  }

  .countdown {
    gap: 4px;
  }

  .countdown-item {
    min-width: 42px;
    padding: 8px 4px 6px;
  }

  .countdown-item strong {
    font-size: 17px;
  }

  .products-wrapper {
    padding: 14px 6px 6px;
    border-radius: 22px;
  }

  .products-header h3 {
    font-size: 18px;
  }

  /* کارت تمام‌عرض و وسط؛ فلش‌ها روی تصویر قرار می‌گیرند تا فضایی از کارت نگیرند */
  .slider-container {
    padding: 0;
  }

  .products-list {
    gap: 12px;
    padding: 8px 4px 16px;
  }

  .product-card {
    flex: 0 0 100%;
  }

  .product-image {
    height: 260px;
  }

  .slider-arrow {
    top: 144px;
    width: 34px;
    height: 34px;
  }

  .prev-arrow {
    right: 14px;
  }

  .next-arrow {
    left: 14px;
  }

  .all-products {
    font-size: 11px;
  }
}
</style>
