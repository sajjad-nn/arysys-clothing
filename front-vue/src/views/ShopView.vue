```vue
<template>
  <div class="shop-page">
    <main class="shop-container">

      <!-- عنوان فروشگاه -->
      <section class="shop-heading">
        <div class="heading-content">
          <span class="eyebrow">ARYASYS COLLECTION</span>
          <h1>فروشگاه</h1>
          <p>استایل و انتخاب مورد علاقه‌ات را کشف کن.</p>
        </div>

        <div class="product-count" v-if="!loading && !error">
          <span class="count-num">{{ products.length.toLocaleString('fa-IR') }}</span>
          <span>محصول</span>
        </div>
      </section>

      <!-- وضعیت دریافت اطلاعات -->
      <div v-if="loading" class="message">
        <span class="loader"></span>
        <p>در حال بارگذاری محصولات...</p>
      </div>

      <div v-else-if="error" class="message error">
        <p>{{ error }}</p>
        <button class="retry-btn" @click="fetchProducts">
          تلاش مجدد
        </button>
      </div>

      <div v-else-if="products.length === 0" class="message">
        در حال حاضر محصولی برای نمایش وجود ندارد.
      </div>

      <!-- محصولات -->
      <section v-else class="products-grid">
        <article
          v-for="product in products"
          :key="product.id"
          class="product-card"
        >

          <!-- تصویر محصول -->
          <div class="product-media">

            <button
              class="product-image-link"
              type="button"
              @click="goToProduct(product)"
              :aria-label="`مشاهده ${product.name}`"
            >
              <img
                v-if="getCurrentImage(product)"
                :key="getCurrentImage(product)"
                :src="imageUrl(getCurrentImage(product))"
                :alt="product.name"
                class="product-image"
                loading="lazy"
                @error="onImageError($event, product)"
              />

              <div v-else class="no-image">
                <svg
                  width="32"
                  height="32"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <rect x="3" y="3" width="18" height="18" rx="2" />
                  <circle cx="8.5" cy="8.5" r="1.5" />
                  <polyline points="21 15 16 10 5 21" />
                </svg>
                <span>فاقد تصویر</span>
              </div>
            </button>

            <div class="media-badges">
              <span v-if="hasDiscount(product)" class="discount-badge">
                {{ getDiscountPercent(product) }}٪ تخفیف
              </span>
            </div>
          </div>

          <div class="product-info">

            <span class="category-name">
              {{ product.category?.name || 'کالکشن اختصاصی' }}
            </span>

            <!-- نام محصول -->
            <button
              class="product-title-link"
              type="button"
              @click="goToProduct(product)"
              :title="product.name"
            >
              {{ product.name }}
            </button>

            <!-- انتخاب تنوع -->
            <div v-if="hasVariants(product)" class="variant-options">

              <div
                class="variant-field"
                v-if="getColors(product).length"
              >
                <label :for="`color-${product.id}`">رنگ</label>

                <div class="select-wrapper">
                  <select
                    :id="`color-${product.id}`"
                    v-model="selectedVariants[product.id].color"
                    @change="onColorChange(product)"
                    class="variant-select"
                  >
                    <option
                      v-for="color in getColors(product)"
                      :key="color"
                      :value="color"
                    >
                      {{ color }}
                    </option>
                  </select>
                </div>
              </div>

              <div
                class="variant-field"
                v-if="getSizes(product).length"
              >
                <label :for="`size-${product.id}`">سایز</label>

                <div class="select-wrapper">
                  <select
                    :id="`size-${product.id}`"
                    v-model="selectedVariants[product.id].size"
                    @change="onSizeChange(product)"
                    class="variant-select"
                  >
                    <option
                      v-for="size in getSizes(product)"
                      :key="size"
                      :value="size"
                    >
                      {{ size }}
                    </option>
                  </select>
                </div>
              </div>

            </div>

            <!-- قیمت و موجودی -->
            <div class="card-footer-area">

              <div class="price-area">
                <div
                  v-if="getCurrentPricing(product).discounted"
                  class="old-price"
                >
                  {{ formatPrice(getCurrentPricing(product).original) }}
                </div>

                <div class="current-price">
                  <span class="amount">
                    {{ formatPrice(getCurrentPricing(product).current) }}
                  </span>
                  <span class="unit">تومان</span>
                </div>
              </div>

              <div
                class="stock-status"
                :class="getCurrentStock(product) > 0 ? 'available' : 'unavailable'"
              >
                <span class="status-pulse"></span>
                <span>
                  {{
                    getCurrentStock(product) > 0
                      ? `${formatPrice(getCurrentStock(product))} عدد موجود`
                      : 'ناموجود'
                  }}
                </span>
              </div>

            </div>

            <!-- افزودن به سبد خرید -->
            <button
              class="add-to-cart-btn"
              :disabled="
                getCurrentStock(product) <= 0 ||
                (hasVariants(product) && !getSelectedVariant(product)) ||
                getCurrentPricing(product).current === null
              "
              @click="addToCart(product)"
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

              <span>
                {{ getCurrentStock(product) > 0 ? 'افزودن به سبد خرید' : 'اتمام موجودی' }}
              </span>
            </button>

          </div>
        </article>
      </section>

      <!-- پیام موفقیت -->
      <transition name="toast">
        <div v-if="toastMessage" class="cart-toast">
          <span class="toast-check">✓</span>
          {{ toastMessage }}
        </div>
      </transition>

    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'

const router = useRouter()
const cart = useCartStore()

const API_BASE = 'http://127.0.0.1:8000'

const products = ref([])
const loading = ref(true)
const error = ref('')
const selectedVariants = ref({})
const toastMessage = ref('')

let toastTimer = null

// فیلتر تنوع‌های فعال
function getVariants(product) {
  return (product.variants || []).filter(
    variant => variant.is_active !== false && variant.is_active !== 0
  )
}

function hasVariants(product) {
  return getVariants(product).length > 0
}

// رنگ‌های محصول
function getColors(product) {
  return [
    ...new Set(
      getVariants(product)
        .map(variant => variant.color)
        .filter(color => color !== null && color !== undefined && color !== '')
    )
  ]
}

// سایزهای قابل انتخاب با توجه به رنگ
function getSizes(product) {
  const selected = selectedVariants.value[product.id]
  const variants = getVariants(product)

  const matchingColor = selected?.color
    ? variants.filter(variant => variant.color === selected.color)
    : variants

  return [
    ...new Set(
      matchingColor
        .map(variant => variant.size)
        .filter(size => size !== null && size !== undefined && size !== '')
    )
  ]
}

// تنوع انتخاب‌شده
function getSelectedVariant(product) {
  const selected = selectedVariants.value[product.id]

  if (!selected) return null

  return (
    getVariants(product).find(variant => {
      const colorMatches =
        !selected.color || variant.color === selected.color

      const sizeMatches =
        !selected.size || variant.size === selected.size

      return colorMatches && sizeMatches
    }) || null
  )
}

// مقداردهی اولیه رنگ و سایز
function initializeVariant(product) {
  const variants = getVariants(product)

  if (!variants.length) return

  const first = variants[0]

  selectedVariants.value[product.id] = {
    color: first.color || '',
    size: first.size || ''
  }

  syncVariantSelection(product)
}

// هماهنگ کردن انتخاب‌ها
function syncVariantSelection(product) {
  const selected = selectedVariants.value[product.id]

  if (!selected) return

  const variants = getVariants(product)

  if (
    selected.color &&
    !variants.some(variant => variant.color === selected.color)
  ) {
    selected.color = variants[0]?.color || ''
  }

  const matchingVariants = variants.filter(
    variant => !selected.color || variant.color === selected.color
  )

  const validSizes = matchingVariants
    .map(variant => variant.size)
    .filter(Boolean)

  if (selected.size && !validSizes.includes(selected.size)) {
    selected.size = validSizes[0] || ''
  }

  if (!selected.color && selected.size) {
    const matching = variants.find(
      variant => variant.size === selected.size
    )

    if (matching?.color) {
      selected.color = matching.color
    }
  }
}

function onColorChange(product) {
  syncVariantSelection(product)
}

function onSizeChange(product) {
  syncVariantSelection(product)
}

// قیمت محصول یا تنوع
function getCurrentPricing(product) {
  const variant = getSelectedVariant(product)
  const item = variant || product

  const original = item.price
  const discount = item.discount_price

  const hasOriginal =
    original !== null && original !== undefined && original !== ''

  const hasDiscount =
    discount !== null && discount !== undefined && discount !== ''

  const originalNumber = Number(original)
  const discountNumber = Number(discount)

  const discounted =
    hasOriginal &&
    hasDiscount &&
    Number.isFinite(originalNumber) &&
    Number.isFinite(discountNumber) &&
    discountNumber >= 0 &&
    discountNumber < originalNumber

  return {
    original: hasOriginal && Number.isFinite(originalNumber)
      ? originalNumber
      : null,

    current: discounted
      ? discountNumber
      : hasOriginal && Number.isFinite(originalNumber)
        ? originalNumber
        : null,

    discounted
  }
}

function hasDiscount(product) {
  return getCurrentPricing(product).discounted
}

function getDiscountPercent(product) {
  const pricing = getCurrentPricing(product)

  if (!pricing.discounted || !pricing.original) return 0

  return Math.round(
    ((pricing.original - pricing.current) / pricing.original) * 100
  )
}

// موجودی تنوع یا محصول
function getCurrentStock(product) {
  const variant = hasVariants(product)
    ? getSelectedVariant(product)
    : null

  const stock = hasVariants(product)
    ? variant?.stock
    : product.stock

  return Math.max(0, Number(stock) || 0)
}

// تصویر تنوع یا تصویر اصلی
function getCurrentImage(product) {
  const variant = hasVariants(product)
    ? getSelectedVariant(product)
    : null

  return variant?.image || product.image || ''
}

// ساخت آدرس تصویر
function imageUrl(image) {
  if (!image || typeof image !== 'string') return ''

  const value = image.trim()

  if (/^https?:\/\//i.test(value)) {
    return value
  }

  const cleanPath = value
    .replace(/^\/+/, '')
    .replace(/^storage\//, '')

  return `${API_BASE}/storage/${cleanPath}`
}

// اگر تصویر تنوع بارگذاری نشد
function onImageError(event, product) {
  const imageElement = event.target

  const variant = hasVariants(product)
    ? getSelectedVariant(product)
    : null

  if (variant?.image && product.image) {
    const variantUrl = imageUrl(variant.image)
    const mainUrl = imageUrl(product.image)

    if (
      imageElement.dataset.fallbackUsed !== 'true' &&
      variantUrl !== mainUrl
    ) {
      imageElement.dataset.fallbackUsed = 'true'
      imageElement.src = mainUrl
      return
    }
  }

  imageElement.style.display = 'none'
}

// فرمت قیمت
function formatPrice(price) {
  if (price === null || price === undefined || price === '') {
    return 'قیمت ثبت نشده'
  }

  const number = Number(price)

  if (!Number.isFinite(number)) {
    return 'قیمت ثبت نشده'
  }

  return number.toLocaleString('fa-IR')
}

// دریافت محصولات
async function fetchProducts() {
  loading.value = true
  error.value = ''

  try {
    const response = await fetch(`${API_BASE}/api/products`)

    if (!response.ok) {
      throw new Error('دریافت محصولات با خطا مواجه شد.')
    }

    const data = await response.json()

    products.value = Array.isArray(data)
      ? data
      : (data.data ?? [])

    selectedVariants.value = {}

    products.value.forEach(initializeVariant)

  } catch (err) {
    error.value =
      err.message || 'ارتباط با سرور برقرار نشد.'
  } finally {
    loading.value = false
  }
}

// رفتن به صفحه محصول تکی
function goToProduct(product) {
  router.push({
    name: 'product-detail',
    params: {
      id: product.id
    }
  })
}

// نمایش پیام موفقیت
function showToast(message) {
  toastMessage.value = message

  if (toastTimer) {
    clearTimeout(toastTimer)
  }

  toastTimer = setTimeout(() => {
    toastMessage.value = ''
  }, 2500)
}

// افزودن محصول به سبد خرید از طریق Pinia
function addToCart(product) {
  const variant = hasVariants(product)
    ? getSelectedVariant(product)
    : null

  if (hasVariants(product) && !variant) {
    showToast('لطفاً رنگ و سایز محصول را انتخاب کن.')
    return
  }

  const stock = getCurrentStock(product)

  if (stock <= 0) {
    showToast('این محصول موجود نیست.')
    return
  }

  const pricing = getCurrentPricing(product)

  if (pricing.current === null) {
    showToast('قیمت این محصول ثبت نشده است.')
    return
  }

  const productData = {
    id: product.id,
    product_id: product.id,
    variant_id: variant?.id ?? null,

    name: product.name,
    image: getCurrentImage(product),

    price: pricing.original,
    discount_price: pricing.discounted
      ? pricing.current
      : null,

    final_price: pricing.current,

    color: variant?.color || '',
    size: variant?.size || '',

    quantity: 1,
    stock
  }

  // استفاده از استور مشترک سبد خرید
  cart.addToCart(productData)

  showToast(`«${product.name}» به سبد خرید اضافه شد.`)
}

onMounted(fetchProducts)

onBeforeUnmount(() => {
  if (toastTimer) {
    clearTimeout(toastTimer)
  }
})
</script>

<style scoped>
.shop-page {
  min-height: 100vh;
  background-color: #fbfbfd;
  color: #1a1a1a;
  direction: rtl;
  font-family: inherit;
}

.shop-container {
  max-width: 1360px;
  margin: 0 auto;
  padding: 56px 28px 100px;
  box-sizing: border-box;
}

/* هدر صفحه */
.shop-heading {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 24px;
  margin-bottom: 44px;
  padding-bottom: 20px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.eyebrow {
  display: inline-block;
  margin-bottom: 8px;
  color: #a38253;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 2.5px;
  text-transform: uppercase;
  direction: ltr;
}

.shop-heading h1 {
  margin: 0;
  font-size: clamp(26px, 3.5vw, 36px);
  font-weight: 800;
  color: #111;
  letter-spacing: -0.5px;
}

.shop-heading p {
  margin: 8px 0 0;
  color: #71717a;
  font-size: 14.5px;
}

.product-count {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: #ffffff;
  border: 1px solid #e4e4e7;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
  border-radius: 9999px;
  color: #71717a;
  font-size: 13px;
  white-space: nowrap;
}

.count-num {
  font-weight: 700;
  color: #18181b;
}

/* گرید محصولات */
.products-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 28px;
  align-items: stretch;
}

/* کارت محصول */
.product-card {
  position: relative;
  display: flex;
  flex-direction: column;
  min-width: 0;
  background: #ffffff;
  border-radius: 20px;
  border: 1px solid #f0f0f2;
  box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
  overflow: hidden;
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.product-card:hover {
  transform: translateY(-6px);
  border-color: #e4e4e7;
  box-shadow: 0 20px 32px -8px rgba(0, 0, 0, 0.08);
}

/* تصویر محصول */
.product-media {
  position: relative;
  overflow: hidden;
  width: 100%;
  aspect-ratio: 1 / 1.15;
  background: #f4f4f5;
}

.product-image-link {
  display: block;
  width: 100%;
  height: 100%;
  padding: 0;
  border: 0;
  background: transparent;
  cursor: pointer;
  overflow: hidden;
}

.product-image {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.product-card:hover .product-image {
  transform: scale(1.06);
}

.no-image {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  height: 100%;
  color: #a1a1aa;
  font-size: 12px;
}

.media-badges {
  position: absolute;
  top: 14px;
  right: 14px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  z-index: 2;
  pointer-events: none;
}

.discount-badge {
  padding: 5px 10px;
  border-radius: 8px;
  background: rgba(220, 38, 38, 0.9);
  backdrop-filter: blur(8px);
  color: #ffffff;
  font-size: 11px;
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
}

/* جزئیات کارت */
.product-info {
  display: flex;
  flex-direction: column;
  flex: 1;
  padding: 20px 18px 18px;
}

.category-name {
  display: block;
  font-size: 11.5px;
  font-weight: 600;
  color: #a38253;
  margin-bottom: 6px;
  text-transform: uppercase;
}

/* نام محصول */
.product-title-link {
  display: -webkit-box;
  width: 100%;
  min-height: 45px;
  padding: 0;
  margin: 0 0 16px;
  border: 0;
  background: transparent;
  color: #18181b;
  font-family: inherit;
  font-size: 15px;
  font-weight: 700;
  line-height: 1.5;
  text-align: right;
  cursor: pointer;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-title-link:hover {
  color: #a38253;
}

/* انتخاب رنگ و سایز */
.variant-options {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-bottom: 18px;
  padding: 12px;
  background: #fafafa;
  border-radius: 12px;
  border: 1px solid #f2f2f4;
}

.variant-field label {
  display: block;
  font-size: 11px;
  font-weight: 600;
  color: #71717a;
  margin-bottom: 5px;
}

.select-wrapper {
  position: relative;
}

.variant-select {
  width: 100%;
  appearance: none;
  background: #ffffff;
  border: 1px solid #e4e4e7;
  border-radius: 8px;
  padding: 6px 10px;
  font-size: 12px;
  font-family: inherit;
  color: #27272a;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  cursor: pointer;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2371717a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: left 8px center;
  padding-left: 24px;
}

.variant-select:focus {
  border-color: #18181b;
  box-shadow: 0 0 0 2px rgba(24, 24, 27, 0.06);
}

/* فوتر کارت */
.card-footer-area {
  margin-top: auto;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 8px;
  padding-top: 14px;
  border-top: 1px dashed #e4e4e7;
}

.price-area {
  display: flex;
  flex-direction: column;
}

.old-price {
  font-size: 12px;
  color: #a1a1aa;
  text-decoration: line-through;
  margin-bottom: 2px;
}

.current-price {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.current-price .amount {
  font-size: 18px;
  font-weight: 800;
  color: #09090b;
}

.current-price .unit {
  font-size: 11px;
  color: #71717a;
}

/* وضعیت موجودی */
.stock-status {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 6px;
}

.status-pulse {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.available {
  background: #f0fdf4;
  color: #166534;
}

.available .status-pulse {
  background: #22c55e;
  box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
}

.unavailable {
  background: #fef2f2;
  color: #991b1b;
}

.unavailable .status-pulse {
  background: #ef4444;
  box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
}

/* دکمه خرید */
.add-to-cart-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  margin-top: 16px;
  padding: 12px;
  background: #18181b;
  color: #ffffff;
  border: 1px solid #18181b;
  border-radius: 12px;
  font-family: inherit;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.25s ease;
}

.add-to-cart-btn:hover:not(:disabled) {
  background: #27272a;
  border-color: #27272a;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px -2px rgba(0, 0, 0, 0.15);
}

.add-to-cart-btn:active:not(:disabled) {
  transform: translateY(0);
}

.add-to-cart-btn:disabled {
  background: #f4f4f5;
  border-color: #e4e4e7;
  color: #a1a1aa;
  cursor: not-allowed;
  box-shadow: none;
}

/* پیام‌ها */
.message {
  padding: 80px 20px;
  text-align: center;
  color: #71717a;
  font-size: 15px;
}

.error {
  color: #dc2626;
}

.retry-btn {
  margin-top: 14px;
  padding: 10px 24px;
  background: #18181b;
  color: #fff;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  font-family: inherit;
  transition: opacity 0.2s;
}

.retry-btn:hover {
  opacity: 0.9;
}

.loader {
  display: inline-block;
  width: 32px;
  height: 32px;
  border: 3px solid #e4e4e7;
  border-top-color: #18181b;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 12px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* پیام افزودن به سبد خرید */
.cart-toast {
  position: fixed;
  bottom: 28px;
  right: 50%;
  transform: translateX(50%);
  z-index: 9999;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 22px;
  background: #18181b;
  color: white;
  border-radius: 12px;
  font-size: 13px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.18);
  white-space: nowrap;
}

.toast-check {
  display: grid;
  place-items: center;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: #16a34a;
  color: #fff;
  font-weight: 800;
}

.toast-enter-active,
.toast-leave-active {
  transition: all 0.25s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translate(50%, 15px);
}

/* ریسپانسیو */
@media (max-width: 1100px) {
  .products-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
  }
}

@media (max-width: 768px) {
  .shop-container {
    padding: 36px 16px 64px;
  }

  .products-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
  }

  .shop-heading {
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 28px;
    gap: 16px;
  }

  .product-info {
    padding: 14px;
  }

  .product-title-link {
    font-size: 13.5px;
    min-height: 40px;
    margin-bottom: 12px;
  }

  .card-footer-area {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }

  .current-price .amount {
    font-size: 16px;
  }
}

@media (max-width: 480px) {
  .products-grid {
    grid-template-columns: 1fr;
    max-width: 380px;
    margin: 0 auto;
  }

  .variant-options {
    grid-template-columns: 1fr 1fr;
  }
}
</style>
```