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
          <span class="count-num">{{ totalProducts.toLocaleString('fa-IR') }}</span>
          <span>محصول</span>
        </div>
      </section>

      <div class="shop-layout">

        <!-- ستون فیلترها -->
        <aside class="filter-sidebar">

          <!-- دکمه باز و بسته کردن فیلتر (فقط موبایل) -->
          <button
            type="button"
            class="mobile-filter-toggle"
            :class="{ open: filtersOpen }"
            :aria-expanded="filtersOpen"
            aria-controls="filter-panel"
            @click="filtersOpen = !filtersOpen"
          >
            <span class="toggle-title">
              <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <line x1="4" y1="6" x2="20" y2="6" />
                <line x1="7" y1="12" x2="17" y2="12" />
                <line x1="10" y1="18" x2="14" y2="18" />
              </svg>
              فیلتر محصولات
              <span v-if="activeFiltersCount" class="filter-badge">
                {{ activeFiltersCount.toLocaleString('fa-IR') }}
              </span>
            </span>

            <svg
              class="toggle-arrow"
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="6 9 12 15 18 9" />
            </svg>
          </button>

          <section
            id="filter-panel"
            class="filter-panel"
            v-show="filtersVisible"
          >

            <div class="filter-header">
              <div>
                <h2>فیلتر محصولات</h2>
                <p>محصول مورد نظرت را راحت‌تر پیدا کن.</p>
              </div>

              <button
                type="button"
                class="clear-filters"
                @click="clearFilters"
              >
                پاک کردن
              </button>
            </div>

            <div class="filters-grid">

              <div class="filter-field">
                <label for="category-filter">دسته‌بندی</label>
                <select id="category-filter" v-model="draft.category">
                  <option value="">همه دسته‌بندی‌ها</option>
                  <option
                    v-for="category in filterOptions.categories"
                    :key="category.id"
                    :value="category.slug"
                  >
                    {{ category.name }}
                  </option>
                </select>
              </div>

              <div class="filter-field">
                <label for="color-filter">رنگ</label>
                <select id="color-filter" v-model="draft.color">
                  <option value="">همه رنگ‌ها</option>
                  <option
                    v-for="color in filterOptions.colors"
                    :key="color"
                    :value="color"
                  >
                    {{ color }}
                  </option>
                </select>
              </div>

              <div class="filter-field">
                <label for="size-filter">سایز</label>
                <select id="size-filter" v-model="draft.size">
                  <option value="">همه سایزها</option>
                  <option
                    v-for="size in filterOptions.sizes"
                    :key="size"
                    :value="size"
                  >
                    {{ size }}
                  </option>
                </select>
              </div>

              <!-- مرتب‌سازی بلافاصله اعمال می‌شود -->
              <div class="filter-field">
                <label for="sort-filter">مرتب‌سازی</label>
                <select
                  id="sort-filter"
                  v-model="draft.sort"
                  @change="applySort"
                >
                  <option value="latest">جدیدترین</option>
                  <option value="price_asc">ارزان‌ترین</option>
                  <option value="price_desc">گران‌ترین</option>
                </select>
              </div>

              <div class="filter-field">
                <label for="min-price">حداقل قیمت (تومان)</label>
                <input
                  id="min-price"
                  v-model="draft.min_price"
                  type="number"
                  min="0"
                  inputmode="numeric"
                  placeholder="از قیمت"
                  @keyup.enter="applyFilters"
                />
              </div>

              <div class="filter-field">
                <label for="max-price">حداکثر قیمت (تومان)</label>
                <input
                  id="max-price"
                  v-model="draft.max_price"
                  type="number"
                  min="0"
                  inputmode="numeric"
                  placeholder="تا قیمت"
                  @keyup.enter="applyFilters"
                />
              </div>

            </div>

            <p v-if="priceError" class="price-error">{{ priceError }}</p>

            <div class="filter-bottom">
              <div class="filter-checks">

                <label class="check-option">
                  <input v-model="draft.on_sale" type="checkbox" />
                  <span>فقط محصولات تخفیف‌دار</span>
                </label>

                <label class="check-option">
                  <input v-model="draft.in_stock" type="checkbox" />
                  <span>فقط محصولات موجود</span>
                </label>

              </div>

              <button
                type="button"
                class="apply-filters"
                @click="applyFilters"
                :disabled="loading || !hasPendingChanges || !!priceError"
              >
                اعمال فیلترها
              </button>
            </div>

          </section>
        </aside>

        <!-- ستون محصولات -->
        <section class="products-content" ref="productsRef">

          <!-- نوار ابزار -->
          <div class="products-toolbar" v-if="!loading && !error">
            <span>
              نمایش {{ products.length.toLocaleString('fa-IR') }}
              محصول از {{ totalProducts.toLocaleString('fa-IR') }}
            </span>

            <div class="grid-controls">
              <span>تعداد ستون:</span>

              <button
                v-for="n in columnOptions"
                :key="n"
                type="button"
                :class="{ active: activeColumns === n }"
                :aria-label="`${n.toLocaleString('fa-IR')} ستون`"
                :title="`${n.toLocaleString('fa-IR')} ستون`"
                :aria-pressed="activeColumns === n"
                @click="setColumns(n)"
              >
                <span
                  class="grid-icon"
                  :style="{
                    gridTemplateColumns: `repeat(${n}, 1fr)`,
                    gridTemplateRows: `repeat(${Math.min(n, 3)}, 1fr)`
                  }"
                >
                  <i v-for="cell in n * Math.min(n, 3)" :key="cell"></i>
                </span>
              </button>
            </div>
          </div>

          <!-- بارگذاری -->
          <div v-if="loading" class="message">
            <span class="loader"></span>
            <p>در حال بارگذاری محصولات...</p>
          </div>

          <!-- خطا -->
          <div v-else-if="error" class="message error">
            <p>{{ error }}</p>
            <button type="button" class="retry-btn" @click="fetchProducts">
              تلاش مجدد
            </button>
          </div>

          <!-- محصولی پیدا نشد -->
          <div v-else-if="products.length === 0" class="message">
            <p>محصولی با این مشخصات پیدا نشد.</p>
            <button type="button" class="retry-btn" @click="clearFilters">
              حذف فیلترها
            </button>
          </div>

          <!-- کارت محصولات -->
          <section
            v-else
            class="products-grid"
            :class="gridClass"
            :style="{ '--cols': activeColumns }"
          >
            <article
              v-for="product in products"
              :key="product.id"
              class="product-card"
            >

              <div class="product-media">

                <button
                  class="product-image-link"
                  type="button"
                  @click="goToProduct(product)"
                  :aria-label="`مشاهده ${product.name}`"
                >
                  <img
                    v-if="hasValidImage(product)"
                    :key="`${product.id}-${getCurrentImage(product)}`"
                    :src="imageUrl(getCurrentImage(product))"
                    :alt="product.name"
                    class="product-image"
                    loading="lazy"
                    decoding="async"
                    @error="onImageError(product)"
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
                    {{ getDiscountPercent(product).toLocaleString('fa-IR') }}٪ تخفیف
                  </span>
                </div>
              </div>

              <div class="product-info">

                <span class="category-name">
                  {{ product.category?.name || 'کالکشن اختصاصی' }}
                </span>

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
                  type="button"
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

          <!-- صفحه‌بندی -->
          <nav
            v-if="!loading && !error && lastPage > 1"
            class="pagination"
            aria-label="صفحه‌بندی محصولات"
          >
            <button
              type="button"
              class="page-btn"
              :disabled="currentPage <= 1"
              @click="changePage(currentPage - 1)"
            >
              قبلی
            </button>

            <button
              v-for="page in visiblePages"
              :key="page"
              type="button"
              class="page-number"
              :class="{ active: page === currentPage }"
              @click="changePage(page)"
            >
              {{ page.toLocaleString('fa-IR') }}
            </button>

            <button
              type="button"
              class="page-btn"
              :disabled="currentPage >= lastPage"
              @click="changePage(currentPage + 1)"
            >
              بعدی
            </button>
          </nav>

        </section>
      </div>

      <!-- پیام سبد خرید -->
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
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'

const router = useRouter()
const cart = useCartStore()

// در صورت خالی بودن VITE_API_URL روی هاست هم‌دامنه، به صورت نسبی ('') عمل می‌کند
const API_BASE = (import.meta.env.VITE_API_URL || '').trim().replace(/\/+$/, '')

const products = ref([])
const loading = ref(true)
const error = ref('')
const selectedVariants = ref({})
const brokenImages = ref({})
const toastMessage = ref('')

const totalProducts = ref(0)
const currentPage = ref(1)
const lastPage = ref(1)
const perPage = 12

const productsRef = ref(null)

const filterOptions = ref({
  colors: [],
  sizes: [],
  categories: []
})

/* ---------------- فیلترها ---------------- */

const defaultFilters = () => ({
  category: '',
  color: '',
  size: '',
  min_price: '',
  max_price: '',
  on_sale: false,
  in_stock: false,
  sort: 'latest'
})

// filters = فیلترهای اعمال‌شده | draft = آنچه کاربر در حال انتخاب است
const filters = ref(defaultFilters())
const draft = ref(defaultFilters())

const hasPendingChanges = computed(
  () => JSON.stringify(draft.value) !== JSON.stringify(filters.value)
)

const priceError = computed(() => {
  const { min_price, max_price } = draft.value

  if (
    min_price !== '' &&
    max_price !== '' &&
    min_price !== null &&
    max_price !== null &&
    Number(min_price) > Number(max_price)
  ) {
    return 'حداقل قیمت نمی‌تواند بیشتر از حداکثر قیمت باشد.'
  }

  return ''
})

// تعداد فیلترهای اعمال‌شده (برای نشان روی دکمه موبایل)
const activeFiltersCount = computed(() => {
  const f = filters.value
  let count = 0

  if (f.category) count++
  if (f.color) count++
  if (f.size) count++
  if (f.min_price !== '' && f.min_price !== null) count++
  if (f.max_price !== '' && f.max_price !== null) count++
  if (f.on_sale) count++
  if (f.in_stock) count++

  return count
})

/* ---------------- موبایل / دسکتاپ ---------------- */

const isMobile = ref(false)
const filtersOpen = ref(false)

// روی دسکتاپ همیشه نمایش داده می‌شود، روی موبایل با دکمه
const filtersVisible = computed(() => !isMobile.value || filtersOpen.value)

let mediaQuery = null

function onMediaChange(event) {
  isMobile.value = event.matches

  // با برگشت به دسکتاپ، حالت باز/بسته موبایل ریست شود
  if (!event.matches) {
    filtersOpen.value = false
  }
}

/* ---------------- تعداد ستون‌ها ---------------- */

const mobileColumns = ref(2)
const desktopColumns = ref(3)

const columnOptions = computed(() =>
  isMobile.value ? [2, 3] : [2, 3, 4]
)

const activeColumns = computed(() =>
  isMobile.value ? mobileColumns.value : desktopColumns.value
)

function setColumns(count) {
  if (isMobile.value) {
    mobileColumns.value = count
  } else {
    desktopColumns.value = count
  }
}

const gridClass = computed(() => ({
  // موبایل ۳ ستونه: خیلی فشرده
  dense: isMobile.value && mobileColumns.value === 3,
  // دسکتاپ ۴ ستونه: کمی فشرده
  compact: !isMobile.value && desktopColumns.value === 4
}))

let toastTimer = null
let activeController = null

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(lastPage.value, currentPage.value + 2)

  for (let page = start; page <= end; page++) {
    pages.push(page)
  }

  return pages
})

// ساخت پارامترهای درخواست (از فیلترهای اعمال‌شده)
function buildQueryParams() {
  const params = new URLSearchParams()

  params.set('page', String(currentPage.value))
  params.set('per_page', String(perPage))
  params.set('sort', filters.value.sort || 'latest')

  const normalFilters = [
    'category',
    'color',
    'size',
    'min_price',
    'max_price'
  ]

  normalFilters.forEach(key => {
    const value = filters.value[key]

    if (value !== '' && value !== null && value !== undefined) {
      params.set(key, String(value))
    }
  })

  // ارسال Boolean به شکل عددی مورد قبول Laravel
  if (filters.value.on_sale) {
    params.set('on_sale', '1')
  }

  if (filters.value.in_stock) {
    params.set('in_stock', '1')
  }

  return params.toString()
}

/* ---------------- تنوع محصول ---------------- */

function getVariants(product) {
  return (product.variants || []).filter(
    variant => variant.is_active !== false && variant.is_active !== 0
  )
}

function hasVariants(product) {
  return getVariants(product).length > 0
}

function getColors(product) {
  return [
    ...new Set(
      getVariants(product)
        .map(variant => variant.color)
        .filter(color => color !== null && color !== undefined && color !== '')
    )
  ]
}

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

/* ---------------- قیمت، موجودی، تصویر ---------------- */

function getCurrentPricing(product) {
  const variant = getSelectedVariant(product)
  const item = variant || product

  const original = item.price
  const discount = item.discount_price

  const hasOriginal =
    original !== null && original !== undefined && original !== ''

  const hasDiscountValue =
    discount !== null && discount !== undefined && discount !== ''

  const originalNumber = Number(original)
  const discountNumber = Number(discount)

  const discounted =
    hasOriginal &&
    hasDiscountValue &&
    Number.isFinite(originalNumber) &&
    Number.isFinite(discountNumber) &&
    discountNumber >= 0 &&
    discountNumber < originalNumber

  return {
    original:
      hasOriginal && Number.isFinite(originalNumber)
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

function getCurrentStock(product) {
  const variant = hasVariants(product)
    ? getSelectedVariant(product)
    : null

  const stock = hasVariants(product)
    ? variant?.stock
    : product.stock

  return Math.max(0, Number(stock) || 0)
}

function getRawImagesForProduct(product) {
  const variant = hasVariants(product)
    ? getSelectedVariant(product)
    : null

  const candidates = [
    variant?.image_url,
    variant?.image,
    product.image_url,
    product.image
  ].filter(img => typeof img === 'string' && img.trim() !== '')

  return [...new Set(candidates)]
}

function getCurrentImage(product) {
  const candidates = getRawImagesForProduct(product)

  for (const raw of candidates) {
    const key = `${product.id}::${raw}`
    if (!brokenImages.value[key]) {
      return raw
    }
  }

  return ''
}

function hasValidImage(product) {
  return Boolean(getCurrentImage(product))
}

function imageUrl(image) {
  if (!image || typeof image !== 'string') return ''

  const value = image.trim()
  if (!value) return ''

  // اگر آدرس کامل با پروتکل باشد، در محیط HTTPS روی هاست پروتکل را هم‌تراز می‌کنیم تا Mixed Content رخ ندهد
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

  return API_BASE ? `${API_BASE}/storage/${cleanPath}` : `/storage/${cleanPath}`
}

function onImageError(product) {
  const current = getCurrentImage(product)
  if (!current) return

  brokenImages.value = {
    ...brokenImages.value,
    [`${product.id}::${current}`]: true
  }
}

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

/* ---------------- دریافت محصولات ---------------- */

async function fetchProducts() {
  if (activeController) {
    activeController.abort()
  }

  const controller = new AbortController()
  activeController = controller

  loading.value = true
  error.value = ''

  try {
    const query = buildQueryParams()
    const url = `${API_BASE}/api/products?${query}`

    const response = await fetch(url, {
      method: 'GET',
      signal: controller.signal,
      headers: {
        Accept: 'application/json'
      }
    })

    const rawText = await response.text()
    let data = {}

    if (rawText) {
      try {
        data = JSON.parse(rawText)
      } catch {
        throw new Error('پاسخ سرور قابل خواندن نیست.')
      }
    }

    if (!response.ok) {
      const validationErrors = data.errors
        ? Object.values(data.errors).flat().join(' | ')
        : ''

      throw new Error(
        validationErrors ||
          data.message ||
          `خطای سرور: ${response.status}`
      )
    }

    const list = Array.isArray(data.data)
      ? data.data
      : Array.isArray(data)
        ? data
        : []

    products.value = list

    totalProducts.value = Number(
      data.meta?.total ?? data.total ?? list.length
    )

    currentPage.value = Number(
      data.meta?.current_page ?? data.current_page ?? currentPage.value
    )

    lastPage.value = Math.max(
      1,
      Number(data.meta?.last_page ?? data.last_page ?? 1)
    )

    if (data.filters) {
      filterOptions.value = {
        colors: data.filters.colors || [],
        sizes: data.filters.sizes || [],
        categories: data.filters.categories || []
      }
    }

    selectedVariants.value = {}
    brokenImages.value = {}

    products.value.forEach(initializeVariant)
  } catch (err) {
    if (err.name === 'AbortError') return

    console.error('Shop products fetch error:', err)
    error.value = err.message || 'ارتباط با سرور برقرار نشد.'
  } finally {
    if (activeController === controller) {
      loading.value = false
      activeController = null
    }
  }
}

/* ---------------- اعمال و پاک کردن فیلتر ---------------- */

// بعد از اعمال فیلتر در موبایل، پنل بسته و به محصولات اسکرول می‌شود
async function afterApplyOnMobile() {
  if (!isMobile.value) return

  filtersOpen.value = false
  await nextTick()

  productsRef.value?.scrollIntoView({
    behavior: 'smooth',
    block: 'start'
  })
}

// اعمال فیلترها (با دکمه یا Enter)
function applyFilters() {
  if (loading.value || priceError.value) return

  filters.value = { ...draft.value }
  currentPage.value = 1
  fetchProducts()
  afterApplyOnMobile()
}

// مرتب‌سازی بلافاصله اعمال می‌شود؛ بقیه پیش‌نویس‌ها دست‌نخورده می‌مانند
function applySort() {
  filters.value = { ...filters.value, sort: draft.value.sort }
  currentPage.value = 1
  fetchProducts()
}

// حذف فیلترها
function clearFilters() {
  filters.value = defaultFilters()
  draft.value = defaultFilters()
  currentPage.value = 1
  fetchProducts()
}

/* ---------------- صفحه‌بندی و ناوبری ---------------- */

function changePage(page) {
  if (page < 1 || page > lastPage.value) return

  currentPage.value = page
  fetchProducts()

  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

function goToProduct(product) {
  router.push({
    name: 'product-detail',
    params: {
      id: product.id
    }
  })
}

/* ---------------- سبد خرید ---------------- */

function showToast(message) {
  toastMessage.value = message

  if (toastTimer) {
    clearTimeout(toastTimer)
  }

  toastTimer = setTimeout(() => {
    toastMessage.value = ''
  }, 2500)
}

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

  cart.addToCart(productData)

  showToast(`«${product.name}» به سبد خرید اضافه شد.`)
}

/* ---------------- چرخه حیات ---------------- */

onMounted(() => {
  mediaQuery = window.matchMedia('(max-width: 768px)')
  isMobile.value = mediaQuery.matches
  mediaQuery.addEventListener('change', onMediaChange)

  // در تبلت (بین ۷۶۸ تا ۱۱۰۰) پیش‌فرض ۲ ستون است
  if (!mediaQuery.matches && window.innerWidth <= 1100) {
    desktopColumns.value = 2
  }

  fetchProducts()
})

onBeforeUnmount(() => {
  if (toastTimer) {
    clearTimeout(toastTimer)
  }

  if (activeController) {
    activeController.abort()
  }

  mediaQuery?.removeEventListener('change', onMediaChange)
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
  padding: 96px 28px 100px;
  box-sizing: border-box;
}

/* هدر */
.shop-heading {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 24px;
  margin-bottom: 30px;
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
  background: #fff;
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

/* چیدمان اصلی فروشگاه */
.shop-layout {
  display: grid;
  grid-template-columns: 280px minmax(0, 1fr);
  gap: 25px;
  direction: rtl;
  align-items: start;
}

.filter-sidebar {
  grid-column: 1;
  min-width: 0;
}

.products-content {
  grid-column: 2;
  min-width: 0;
  scroll-margin-top: 90px;
}

/* دکمه باز و بسته کردن فیلتر (پیش‌فرض مخفی، فقط موبایل) */
.mobile-filter-toggle {
  display: none;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 13px 16px;
  background: #fff;
  border: 1px solid #eeeef0;
  border-radius: 14px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.025);
  color: #18181b;
  font-family: inherit;
  font-size: 13.5px;
  font-weight: 700;
  cursor: pointer;
}

.toggle-title {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.filter-badge {
  display: inline-grid;
  place-items: center;
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  background: #a38253;
  color: #fff;
  border-radius: 9999px;
  font-size: 11px;
  font-weight: 700;
}

.toggle-arrow {
  color: #71717a;
  transition: transform 0.25s ease;
}

.mobile-filter-toggle.open .toggle-arrow {
  transform: rotate(180deg);
}

/* فیلتر */
.filter-panel {
  padding: 20px;
  background: #fff;
  border: 1px solid #eeeef0;
  border-radius: 18px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.025);
}

.filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
}

.filter-header h2 {
  margin: 0;
  font-size: 16px;
  font-weight: 800;
}

.filter-header p {
  margin: 5px 0 0;
  color: #85858d;
  font-size: 12px;
  line-height: 1.8;
}

.clear-filters {
  flex-shrink: 0;
  padding: 7px 10px;
  color: #a38253;
  background: #fbf8f3;
  border: 1px solid #eee5d8;
  border-radius: 9px;
  font-family: inherit;
  font-size: 11px;
  cursor: pointer;
}

.filters-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 15px;
}

.filter-field {
  display: flex;
  flex-direction: column;
  gap: 7px;
  min-width: 0;
}

.filter-field label {
  font-size: 12px;
  font-weight: 600;
  color: #52525b;
}

.filter-field select,
.filter-field input {
  width: 100%;
  height: 42px;
  padding: 0 12px;
  background: #fafafa;
  border: 1px solid #e4e4e7;
  border-radius: 9px;
  outline: none;
  color: #27272a;
  font-family: inherit;
  font-size: 12px;
  box-sizing: border-box;
  transition: border-color 0.2s;
}

.filter-field select:focus,
.filter-field input:focus {
  border-color: #a38253;
}

.price-error {
  margin: 12px 0 0;
  color: #dc2626;
  font-size: 11.5px;
  line-height: 1.8;
}

.filter-bottom {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-top: 20px;
  padding-top: 17px;
  border-top: 1px solid #f0f0f2;
}

.filter-checks {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.check-option {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  color: #52525b;
  font-size: 12px;
  cursor: pointer;
}

.check-option input {
  width: 15px;
  height: 15px;
  accent-color: #a38253;
  cursor: pointer;
}

.apply-filters {
  width: 100%;
  padding: 11px 18px;
  background: #18181b;
  color: #fff;
  border: 1px solid #18181b;
  border-radius: 9px;
  font-family: inherit;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: opacity 0.2s;
}

.apply-filters:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* نوار ابزار */
.products-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 15px;
  margin-bottom: 18px;
  color: #71717a;
  font-size: 12px;
}

.grid-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

.grid-controls > span {
  margin-left: 4px;
}

.grid-controls button {
  display: grid;
  place-items: center;
  width: 34px;
  height: 34px;
  padding: 6px;
  background: #fff;
  border: 1px solid #e4e4e7;
  border-radius: 8px;
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s;
}

.grid-controls button.active {
  border-color: #a38253;
  background: #fbf8f3;
}

.grid-controls button.active .grid-icon i {
  background: #a38253;
}

.grid-icon {
  display: grid;
  gap: 2px;
  width: 18px;
  height: 18px;
}

.grid-icon i {
  display: block;
  background: #71717a;
  border-radius: 1px;
}

/* گرید محصولات: تعداد ستون از طریق متغیر --cols تعیین می‌شود */
.products-grid {
  --cols: 3;
  display: grid;
  grid-template-columns: repeat(var(--cols), minmax(0, 1fr));
  gap: 20px;
  align-items: stretch;
}

/* کارت محصول */
.product-card {
  position: relative;
  display: flex;
  flex-direction: column;
  min-width: 0;
  background: #fff;
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

/* تصویر */
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
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
}

/* اطلاعات کارت */
.product-info {
  display: flex;
  flex-direction: column;
  flex: 1;
  padding: 18px 16px;
}

.category-name {
  display: block;
  font-size: 11.5px;
  font-weight: 600;
  color: #a38253;
  margin-bottom: 6px;
}

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

/* تنوع */
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
  background: #fff;
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

/* قیمت و موجودی */
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
  color: #fff;
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

/* حالت فشرده دسکتاپ (۴ ستون) */
.products-grid.compact {
  gap: 14px;
}

.products-grid.compact .product-card {
  border-radius: 16px;
}

.products-grid.compact .product-info {
  padding: 13px 12px;
}

.products-grid.compact .product-title-link {
  font-size: 13.5px;
  min-height: 40px;
  margin-bottom: 12px;
}

.products-grid.compact .variant-options {
  grid-template-columns: 1fr;
  gap: 8px;
  padding: 9px;
  margin-bottom: 12px;
}

.products-grid.compact .card-footer-area {
  flex-direction: column;
  align-items: flex-start;
  gap: 8px;
}

.products-grid.compact .current-price .amount {
  font-size: 16px;
}

.products-grid.compact .add-to-cart-btn {
  padding: 10px 6px;
  font-size: 12px;
  margin-top: 12px;
}

/* صفحه‌بندی */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 42px;
}

.page-btn,
.page-number {
  min-width: 38px;
  height: 38px;
  padding: 0 12px;
  background: #fff;
  border: 1px solid #e4e4e7;
  border-radius: 9px;
  color: #52525b;
  font-family: inherit;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s;
}

.page-number.active {
  background: #18181b;
  color: #fff;
  border-color: #18181b;
}

.page-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
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
  overflow-wrap: anywhere;
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

/* پیام سبد خرید */
.cart-toast {
  position: fixed;
  bottom: 84px;
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
  max-width: calc(100vw - 32px);
  overflow: hidden;
  text-overflow: ellipsis;
}

.toast-check {
  display: grid;
  place-items: center;
  flex-shrink: 0;
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

/* تبلت */
@media (max-width: 1100px) {
  .shop-layout {
    grid-template-columns: 245px minmax(0, 1fr);
    gap: 18px;
  }

  .products-grid {
    gap: 16px;
  }
}

/* موبایل */
@media (max-width: 768px) {
  .shop-container {
    padding: 96px 16px 96px;
  }

  .shop-layout {
    grid-template-columns: 1fr;
    gap: 18px;
  }

  .filter-sidebar,
  .products-content {
    grid-column: 1;
  }

  /* نمایش دکمه جمع‌شونده */
  .mobile-filter-toggle {
    display: flex;
  }

  .filter-panel {
    margin-top: 10px;
    padding: 16px;
  }

  /* در موبایل عنوان داخل پنل تکراری است */
  .filter-header p {
    display: none;
  }

  .filters-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }

  .filter-bottom {
    align-items: flex-start;
    flex-direction: column;
  }

  .filter-checks {
    flex-direction: row;
    flex-wrap: wrap;
    gap: 15px;
  }

  .apply-filters {
    width: 100%;
  }

  .products-grid {
    gap: 12px;
  }

  .products-grid.dense {
    gap: 8px;
  }

  .shop-heading {
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 25px;
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

  /* ۲ ستون موبایل: متن دکمه و تنوع کمی کوچک‌تر */
  .variant-options {
    grid-template-columns: 1fr;
    gap: 8px;
    padding: 10px;
    margin-bottom: 14px;
  }

  .add-to-cart-btn {
    padding: 10px 6px;
    font-size: 12px;
  }

  .product-card {
    border-radius: 16px;
  }

  .product-card:hover {
    transform: none;
  }

  /* ۳ ستون موبایل: خیلی فشرده */
  .products-grid.dense .product-card {
    border-radius: 12px;
  }

  .products-grid.dense .product-info {
    padding: 8px;
  }

  .products-grid.dense .product-title-link {
    font-size: 11px;
    min-height: 34px;
    margin-bottom: 8px;
  }

  .products-grid.dense .category-name {
    font-size: 9px;
  }

  .products-grid.dense .current-price .amount {
    font-size: 12px;
  }

  .products-grid.dense .current-price .unit {
    font-size: 9px;
  }

  .products-grid.dense .old-price {
    font-size: 10px;
  }

  .products-grid.dense .stock-status {
    font-size: 9px;
    padding: 3px 5px;
  }

  .products-grid.dense .variant-options {
    grid-template-columns: 1fr;
    gap: 6px;
    padding: 7px;
    margin-bottom: 9px;
  }

  .products-grid.dense .variant-field label {
    font-size: 9px;
  }

  .products-grid.dense .variant-select {
    font-size: 10px;
    padding: 5px 7px;
    padding-left: 20px;
  }

  .products-grid.dense .add-to-cart-btn {
    gap: 4px;
    padding: 8px 4px;
    font-size: 10px;
    margin-top: 10px;
  }

  .products-grid.dense .add-to-cart-btn svg {
    display: none;
  }

  .products-grid.dense .discount-badge {
    padding: 3px 6px;
    font-size: 9px;
    border-radius: 6px;
  }

  .products-grid.dense .media-badges {
    top: 8px;
    right: 8px;
  }
}

/* موبایل کوچک */
@media (max-width: 480px) {
  .products-toolbar {
    align-items: flex-start;
    flex-direction: column;
  }

  .filter-header {
    align-items: flex-start;
  }

  .filters-grid {
    grid-template-columns: 1fr;
  }
}
</style>