<template>
  <main class="product-page">

    <!-- وضعیت بارگذاری -->
    <div v-if="loading" class="message">
      <span class="loader"></span>
      <p>در حال دریافت اطلاعات محصول...</p>
    </div>

    <!-- خطا -->
    <div v-else-if="error" class="message error">
      <p>{{ error }}</p>
      <button type="button" class="back-button" @click="goBack">
        بازگشت به فروشگاه
      </button>
    </div>

    <!-- اطلاعات محصول و نظرات -->
    <div v-else-if="product" class="product-content">

      <div class="product-container">

        <!-- تصویر محصول -->
        <div class="product-image">
          <img
            v-if="hasValidImage"
            :key="`${product.id}-${currentImage}`"
            :src="imageUrl(currentImage)"
            :alt="product.name"
            loading="eager"
            decoding="async"
            @error="onImageError"
          />

          <div v-else class="no-image">
            تصویر محصول موجود نیست
          </div>
        </div>

        <!-- مشخصات محصول -->
        <div class="product-details">

          <span class="category">
            {{ product.category?.name || 'کالکشن اختصاصی' }}
          </span>

          <h1>{{ product.name }}</h1>

          <p class="description">
            {{ product.description || 'توضیحاتی برای این محصول ثبت نشده است.' }}
          </p>

          <!-- قیمت -->
          <div class="price">
            <span v-if="hasDiscount" class="old-price">
              {{ formatPrice(originalPrice) }} تومان
            </span>

            <strong v-if="currentPrice !== null">
              {{ formatPrice(currentPrice) }} تومان
            </strong>

            <strong v-else class="no-price">
              قیمت ثبت نشده
            </strong>
          </div>

          <!-- انتخاب رنگ و سایز -->
          <div v-if="hasVariants" class="variant-section">

            <div v-if="colors.length" class="variant-field">
              <label for="detail-color-select">انتخاب رنگ</label>

              <select id="detail-color-select" v-model="selectedColor">
                <option
                  v-for="color in colors"
                  :key="color"
                  :value="color"
                >
                  {{ color }}
                </option>
              </select>
            </div>

            <div v-if="sizes.length" class="variant-field">
              <label for="detail-size-select">انتخاب سایز</label>

              <select id="detail-size-select" v-model="selectedSize">
                <option
                  v-for="size in sizes"
                  :key="size"
                  :value="size"
                >
                  {{ size }}
                </option>
              </select>
            </div>

          </div>

          <!-- موجودی -->
          <div
            class="stock"
            :class="{ 'out-stock': currentStock <= 0 }"
          >
            <span class="stock-dot"></span>

            {{
              currentStock > 0
                ? `${formatPrice(currentStock)} عدد موجود`
                : 'ناموجود'
            }}
          </div>

          <!-- تعداد -->
          <div class="quantity-section">
            <label>تعداد</label>

            <div class="quantity-control">
              <button
                type="button"
                @click="decreaseQuantity"
                :disabled="quantity <= 1"
                aria-label="کاهش تعداد"
              >
                −
              </button>

              <span>{{ quantity.toLocaleString('fa-IR') }}</span>

              <button
                type="button"
                @click="increaseQuantity"
                :disabled="quantity >= currentStock"
                aria-label="افزایش تعداد"
              >
                +
              </button>
            </div>
          </div>

          <!-- افزودن به سبد خرید -->
          <button
            type="button"
            class="add-to-cart-btn"
            @click="addToCart"
            :disabled="currentStock <= 0 || currentPrice === null"
          >
            <span>
              {{ currentStock > 0 ? 'افزودن به سبد خرید' : 'ناموجود' }}
            </span>
          </button>

          <p v-if="toastMessage" class="toast-message">
            {{ toastMessage }}
          </p>

          <button type="button" class="back-button" @click="goBack">
            بازگشت به فروشگاه
          </button>

        </div>
      </div>

      <!-- بخش نظرات -->
      <section class="reviews-section">

        <div class="reviews-heading">
          <div>
            <span class="reviews-subtitle">نظرات خریداران</span>
            <h2>نظرات کاربران درباره این محصول</h2>
          </div>

          <span class="reviews-count">
            {{ reviews.length.toLocaleString('fa-IR') }} نظر
          </span>
        </div>

        <!-- بارگذاری نظرات -->
        <div v-if="reviewsLoading" class="reviews-message">
          در حال دریافت نظرات...
        </div>

        <!-- خطای دریافت نظرات -->
        <div v-else-if="reviewsError" class="reviews-message error">
          {{ reviewsError }}
          <button type="button" class="retry-button" @click="fetchReviews">
            تلاش مجدد
          </button>
        </div>

        <template v-else>

          <!-- فهرست نظرات -->
          <div v-if="reviews.length" class="reviews-list">

            <article
              v-for="review in reviews"
              :key="review.id"
              class="review-card"
            >
              <div class="review-top">
                <div class="review-user">
                  <div class="user-avatar">
                    {{ (review.user?.name || 'ک').charAt(0) }}
                  </div>

                  <div>
                    <strong>
                      {{ review.user?.name || 'کاربر سایت' }}
                    </strong>

                    <span class="review-date">
                      {{ formatDate(review.created_at) }}
                    </span>
                  </div>
                </div>

                <div class="review-stars" :aria-label="`امتیاز ${review.rating} از ۵`">
                  <span
                    v-for="star in 5"
                    :key="star"
                    :class="{ active: star <= Number(review.rating) }"
                  >
                    ★
                  </span>
                </div>
              </div>

              <p class="review-body">
                {{ review.body }}
              </p>
            </article>

          </div>

          <div v-else class="empty-reviews">
            <div class="empty-icon">☆</div>
            <h3>هنوز نظری ثبت نشده است</h3>
            <p>اولین نفری باشید که تجربه خود را درباره این محصول به اشتراک می‌گذارد.</p>
          </div>

        </template>

        <!-- فرم ثبت نظر -->
        <div class="review-form-wrapper">

          <h3>نظر شما درباره این محصول</h3>

          <!-- کاربر مهمان -->
          <div v-if="!auth.isLoggedIn" class="login-required">
            <p>برای ثبت نظر ابتدا وارد حساب کاربری خود شوید.</p>

            <button
              type="button"
              class="login-button"
              @click="goToLogin"
            >
              ورود به حساب کاربری
            </button>
          </div>

          <!-- کاربر واردشده -->
          <form
            v-else
            class="review-form"
            @submit.prevent="submitReview"
          >

            <label class="form-label">
              امتیاز شما
            </label>

            <div class="rating-selector" dir="ltr">
              <button
                v-for="star in 5"
                :key="star"
                type="button"
                :class="{ selected: star <= reviewRating }"
                @click="reviewRating = star"
                :aria-label="`امتیاز ${star}`"
              >
                ★
              </button>
            </div>

            <label for="review-body" class="form-label">
              متن نظر
            </label>

            <textarea
              id="review-body"
              v-model="reviewBody"
              rows="5"
              maxlength="2000"
              placeholder="تجربه خود را درباره این محصول بنویسید..."
              required
            ></textarea>

            <div class="textarea-footer">
              <span>
                {{ reviewBody.length.toLocaleString('fa-IR') }} / ۲۰۰۰
              </span>
            </div>

            <p v-if="reviewError" class="form-error">
              {{ reviewError }}
            </p>

            <p v-if="reviewSuccess" class="form-success">
              {{ reviewSuccess }}
            </p>

            <button
              type="submit"
              class="submit-review-btn"
              :disabled="reviewSubmitting || reviewRating === 0"
            >
              {{
                reviewSubmitting
                  ? 'در حال ثبت نظر...'
                  : 'ثبت نظر'
              }}
            </button>

            <p class="review-notice">
              نظر شما پس از بررسی و تأیید مدیریت در سایت نمایش داده می‌شود.
            </p>

          </form>
        </div>

      </section>
    </div>

  </main>
</template>

<script setup>
import {
  ref,
  computed,
  watch,
  onMounted,
  onBeforeUnmount
} from 'vue'

import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'

import api from '@/services/api'
import { auth, checkAuth } from '@/services/auth'

const props = defineProps({
  id: {
    type: [String, Number],
    required: true
  }
})

const router = useRouter()
const cart = useCartStore()

const API_BASE = (import.meta.env.VITE_API_URL || '').trim().replace(/\/+$/, '')

const product = ref(null)
const loading = ref(true)
const error = ref('')

const selectedColor = ref('')
const selectedSize = ref('')
const quantity = ref(1)

const toastMessage = ref('')
const brokenImages = ref({})

let toastTimer = null

// وضعیت نظرات
const reviews = ref([])
const reviewsLoading = ref(false)
const reviewsError = ref('')

// وضعیت فرم نظر
const reviewBody = ref('')
const reviewRating = ref(0)
const reviewSubmitting = ref(false)
const reviewError = ref('')
const reviewSuccess = ref('')

// تنوع‌های فعال محصول
const variants = computed(() => {
  return (product.value?.variants || []).filter(
    variant =>
      variant.is_active !== false &&
      variant.is_active !== 0
  )
})

const hasVariants = computed(() => variants.value.length > 0)

// رنگ‌های موجود
const colors = computed(() => {
  return [
    ...new Set(
      variants.value
        .map(variant => variant.color)
        .filter(Boolean)
    )
  ]
})

// سایزهای موجود برای رنگ انتخاب‌شده
const sizes = computed(() => {
  const matchingVariants = variants.value.filter(
    variant =>
      !selectedColor.value ||
      variant.color === selectedColor.value
  )

  return [
    ...new Set(
      matchingVariants
        .map(variant => variant.size)
        .filter(Boolean)
    )
  ]
})

// تنوع انتخاب‌شده
const selectedVariant = computed(() => {
  if (!hasVariants.value) return null

  return variants.value.find(variant => {
    const colorMatches =
      !selectedColor.value ||
      variant.color === selectedColor.value

    const sizeMatches =
      !selectedSize.value ||
      variant.size === selectedSize.value

    return colorMatches && sizeMatches
  }) || null
})

// تغییر رنگ و هماهنگی سایز
watch(selectedColor, () => {
  if (!sizes.value.includes(selectedSize.value)) {
    selectedSize.value = sizes.value[0] || ''
  }

  quantity.value = 1
})

// تغییر سایز
watch(selectedSize, () => {
  quantity.value = 1
})

// قیمت اصلی
const originalPrice = computed(() => {
  const item = selectedVariant.value || product.value

  if (!item) return null

  const value = Number(item.price)

  return item.price !== null &&
    item.price !== undefined &&
    item.price !== '' &&
    Number.isFinite(value)
      ? value
      : null
})

// قیمت با تخفیف
const currentPrice = computed(() => {
  const item = selectedVariant.value || product.value

  if (!item || originalPrice.value === null) return null

  const original = Number(item.price)
  const discount = Number(item.discount_price)

  const validDiscount =
    item.discount_price !== null &&
    item.discount_price !== undefined &&
    item.discount_price !== '' &&
    Number.isFinite(discount) &&
    discount >= 0 &&
    discount < original

  return validDiscount
    ? discount
    : originalPrice.value
})

// بررسی تخفیف
const hasDiscount = computed(() => {
  return (
    originalPrice.value !== null &&
    currentPrice.value !== null &&
    currentPrice.value < originalPrice.value
  )
})

// موجودی
const currentStock = computed(() => {
  const item = selectedVariant.value || product.value

  if (!item) return 0

  return Math.max(0, Number(item.stock) || 0)
})

// تصویر تنوع یا تصویر اصلی با fallback مرحله‌ای
const currentImage = computed(() => {
  const candidates = [
    selectedVariant.value?.image_url,
    selectedVariant.value?.image,
    product.value?.image_url,
    product.value?.image
  ].filter(img => typeof img === 'string' && img.trim() !== '')

  const uniqueCandidates = [...new Set(candidates)]
  const pid = product.value?.id ?? props.id

  for (const raw of uniqueCandidates) {
    if (!brokenImages.value[`${pid}::${raw}`]) {
      return raw
    }
  }

  return ''
})

const hasValidImage = computed(() => Boolean(currentImage.value))

// اصلاح موجودی انتخاب‌شده در صورت تغییر تنوع
watch(currentStock, (stock) => {
  if (stock <= 0) {
    quantity.value = 1
  } else if (quantity.value > stock) {
    quantity.value = stock
  }
})

// افزایش تعداد
function increaseQuantity() {
  if (quantity.value < currentStock.value) {
    quantity.value++
  }
}

// کاهش تعداد
function decreaseQuantity() {
  if (quantity.value > 1) {
    quantity.value--
  }
}

// فرمت قیمت
function formatPrice(value) {
  if (value === null || value === undefined || value === '') {
    return 'ثبت نشده'
  }

  const number = Number(value)

  return Number.isFinite(number)
    ? number.toLocaleString('fa-IR')
    : 'ثبت نشده'
}

// آدرس تصویر با سازگاری برای هاست و HTTPS
function imageUrl(image) {
  if (!image) return ''

  const value = String(image).trim()
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

  const cleanPath = value
    .replace(/^\/+/, '')
    .replace(/^storage\/+/, '')

  return API_BASE ? `${API_BASE}/storage/${cleanPath}` : `/storage/${cleanPath}`
}

function onImageError() {
  const current = currentImage.value
  if (!current) return

  const pid = product.value?.id ?? props.id
  brokenImages.value = {
    ...brokenImages.value,
    [`${pid}::${current}`]: true
  }
}

// دریافت محصول
async function fetchProduct() {
  loading.value = true
  error.value = ''
  selectedColor.value = ''
  selectedSize.value = ''
  quantity.value = 1
  brokenImages.value = {}

  try {
    const response = await fetch(
      `${API_BASE}/api/products/${props.id}`,
      {
        headers: {
          Accept: 'application/json'
        }
      }
    )

    if (!response.ok) {
      throw new Error('محصول مورد نظر پیدا نشد.')
    }

    const data = await response.json()

    product.value = data.data ?? data

    // انتخاب اولین تنوع
    if (variants.value.length) {
      const first = variants.value[0]

      selectedColor.value = first.color || ''
      selectedSize.value = first.size || ''
    }

  } catch (err) {
    error.value =
      err.message || 'دریافت اطلاعات محصول با خطا مواجه شد.'
  } finally {
    loading.value = false
  }
}

// دریافت نظرات تأییدشده
async function fetchReviews() {
  reviewsLoading.value = true
  reviewsError.value = ''

  try {
    const response = await api.get(
      `/api/products/${props.id}/reviews`
    )

    const result = response.data

    reviews.value = Array.isArray(result)
      ? result
      : (result.data || [])

  } catch (err) {
    console.error('Fetch reviews error:', err)

    reviewsError.value =
      'دریافت نظرات با خطا مواجه شد.'
  } finally {
    reviewsLoading.value = false
  }
}

// تاریخ ثبت نظر
function formatDate(date) {
  if (!date) return ''

  const value = new Date(date)

  if (Number.isNaN(value.getTime())) return ''

  return value.toLocaleDateString('fa-IR')
}

// ثبت نظر
async function submitReview() {
  reviewError.value = ''
  reviewSuccess.value = ''

  if (!auth.isLoggedIn) {
    reviewError.value = 'برای ثبت نظر ابتدا وارد حساب کاربری شوید.'
    return
  }

  if (reviewRating.value < 1 || reviewRating.value > 5) {
    reviewError.value = 'لطفاً امتیاز خود را انتخاب کنید.'
    return
  }

  if (reviewBody.value.trim().length < 3) {
    reviewError.value = 'متن نظر باید حداقل ۳ حرف داشته باشد.'
    return
  }

  reviewSubmitting.value = true

  try {
    const response = await api.post(
      `/api/products/${props.id}/reviews`,
      {
        body: reviewBody.value.trim(),
        rating: reviewRating.value
      }
    )

    reviewSuccess.value =
      response.data.message ||
      'نظر شما ثبت شد و پس از تأیید مدیریت نمایش داده می‌شود.'

    reviewBody.value = ''
    reviewRating.value = 0

  } catch (err) {
    console.error('Submit review error:', err)

    if (err.response?.status === 409) {
      reviewError.value =
        err.response.data.message ||
        'شما قبلاً برای این محصول نظر ثبت کرده‌اید.'
    } else if (err.response?.status === 401) {
      reviewError.value =
        'نشست کاربری شما منقضی شده است. دوباره وارد شوید.'
      await checkAuth(true)
    } else if (err.response?.status === 422) {
      reviewError.value =
        Object.values(err.response.data.errors || {})
          .flat()
          .join(' ')
        || 'اطلاعات واردشده معتبر نیست.'
    } else {
      reviewError.value =
        err.response?.data?.message ||
        'ثبت نظر با خطا مواجه شد. دوباره تلاش کنید.'
    }

  } finally {
    reviewSubmitting.value = false
  }
}

// نمایش پیام
function showToast(message) {
  toastMessage.value = message

  if (toastTimer) {
    clearTimeout(toastTimer)
  }

  toastTimer = setTimeout(() => {
    toastMessage.value = ''
  }, 3000)
}

// افزودن محصول به Pinia
function addToCart() {
  if (!product.value) return

  if (hasVariants.value && !selectedVariant.value) {
    showToast('لطفاً رنگ و سایز محصول را انتخاب کنید.')
    return
  }

  if (currentStock.value <= 0) {
    showToast('این محصول موجود نیست.')
    return
  }

  if (quantity.value > currentStock.value) {
    showToast('تعداد انتخاب‌شده بیشتر از موجودی است.')
    return
  }

  if (currentPrice.value === null) {
    showToast('قیمت این محصول ثبت نشده است.')
    return
  }

  const variant = selectedVariant.value

  const productData = {
    id: product.value.id,
    product_id: product.value.id,
    variant_id: variant?.id ?? null,

    name: product.value.name,
    image: currentImage.value,

    price: originalPrice.value,
    discount_price: hasDiscount.value
      ? currentPrice.value
      : null,

    final_price: currentPrice.value,

    color: variant?.color || '',
    size: variant?.size || '',

    quantity: quantity.value,
    stock: currentStock.value
  }

  cart.addToCart(productData)

  showToast('محصول با موفقیت به سبد خرید اضافه شد.')
}

// بازگشت به فروشگاه
function goBack() {
  router.push({ name: 'shop' })
}

// رفتن به صفحه ورود
function goToLogin() {
  router.push('/auth')
}

// بارگذاری مجدد هنگام تغییر شناسه محصول در روتر
watch(
  () => props.id,
  async (newId, oldId) => {
    if (newId && newId !== oldId) {
      reviewError.value = ''
      reviewSuccess.value = ''
      await Promise.all([
        fetchProduct(),
        fetchReviews()
      ])
    }
  }
)

// راه‌اندازی صفحه
onMounted(async () => {
  await checkAuth()

  await Promise.all([
    fetchProduct(),
    fetchReviews()
  ])
})

// پاک‌سازی تایمر
onBeforeUnmount(() => {
  if (toastTimer) {
    clearTimeout(toastTimer)
  }
})
</script>

<style scoped>
.product-page {
  min-height: 100vh;
  padding: 140px 24px 60px;
  background: #fbfbfd;
  direction: rtl;
  color: #18181b;
  box-sizing: border-box;
}

.product-content {
  max-width: 1100px;
  margin: 0 auto;
}

.product-container {
  display: grid;
  grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
  gap: 55px;
  align-items: start;
}

/* تصویر */
.product-image {
  width: 100%;
  max-width: 480px;
  max-height: 520px;
  aspect-ratio: 1 / 1.1;
  overflow: hidden;
  border-radius: 20px;
  background: #f4f4f5;
  margin: 0 auto;
}

.product-image img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-image {
  display: grid;
  place-items: center;
  height: 100%;
  color: #71717a;
  font-size: 14px;
}

/* مشخصات */
.product-details {
  padding-top: 15px;
}

.category {
  color: #a38253;
  font-size: 13px;
  font-weight: 600;
}

.product-details h1 {
  margin: 14px 0;
  font-size: 28px;
  font-weight: 800;
  line-height: 1.8;
}

.description {
  color: #71717a;
  line-height: 2.2;
  margin: 20px 0;
  font-size: 14px;
}

/* قیمت */
.price {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin: 25px 0;
}

.price strong {
  font-size: 24px;
  color: #18181b;
}

.old-price {
  color: #a1a1aa;
  text-decoration: line-through;
  font-size: 14px;
}

.no-price {
  color: #a1a1aa !important;
  font-size: 15px !important;
}

/* رنگ و سایز */
.variant-section {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  margin: 25px 0;
}

.variant-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 130px;
}

.variant-field label,
.quantity-section label {
  font-size: 13px;
  font-weight: 700;
  color: #3f3f46;
}

.variant-field select {
  min-width: 130px;
  padding: 11px 14px;
  border: 1px solid #e4e4e7;
  border-radius: 9px;
  background: white;
  color: #27272a;
  font-family: inherit;
  outline: none;
}

.variant-field select:focus {
  border-color: #a38253;
}

/* موجودی */
.stock {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 20px 0;
  color: #166534;
  font-size: 13px;
  font-weight: 600;
}

.stock-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #22c55e;
}

.out-stock {
  color: #dc2626;
}

.out-stock .stock-dot {
  background: #dc2626;
}

/* تعداد */
.quantity-section {
  display: flex;
  align-items: center;
  gap: 20px;
  margin: 25px 0;
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 5px 10px;
  border: 1px solid #e4e4e7;
  border-radius: 10px;
  background: white;
}

.quantity-control button {
  width: 28px;
  height: 28px;
  border: none;
  background: #f4f4f5;
  border-radius: 7px;
  cursor: pointer;
  font-size: 18px;
}

.quantity-control button:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.quantity-control span {
  min-width: 20px;
  text-align: center;
  font-size: 14px;
}

/* دکمه سبد خرید */
.add-to-cart-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  max-width: 360px;
  padding: 15px 20px;
  margin: 25px 0 12px;
  border: none;
  border-radius: 12px;
  background: #18181b;
  color: white;
  font-family: inherit;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: 0.25s;
}

.add-to-cart-btn:hover:not(:disabled) {
  background: #a38253;
  transform: translateY(-2px);
}

.add-to-cart-btn:disabled {
  background: #d4d4d8;
  cursor: not-allowed;
}

.toast-message {
  color: #166534;
  font-size: 13px;
  margin: 12px 0;
}

/* بازگشت */
.back-button {
  display: block;
  padding: 11px 20px;
  border: 1px solid #e4e4e7;
  border-radius: 10px;
  background: white;
  color: #27272a;
  font-family: inherit;
  cursor: pointer;
  margin-top: 15px;
}

.back-button:hover {
  border-color: #a38253;
}

/* پیام‌ها */
.message {
  text-align: center;
  padding: 100px 20px;
}

.error {
  color: #dc2626;
}

.loader {
  display: inline-block;
  width: 32px;
  height: 32px;
  border: 3px solid #e4e4e7;
  border-top-color: #18181b;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* =========================
   بخش نظرات
========================= */

.reviews-section {
  margin-top: 90px;
  padding-top: 45px;
  border-top: 1px solid #e4e4e7;
}

.reviews-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 15px;
  margin-bottom: 30px;
}

.reviews-subtitle {
  color: #a38253;
  font-size: 13px;
  font-weight: 700;
}

.reviews-heading h2 {
  margin: 10px 0 0;
  font-size: 22px;
  font-weight: 800;
}

.reviews-count {
  padding: 8px 14px;
  border-radius: 20px;
  background: #f4f4f5;
  color: #52525b;
  font-size: 13px;
}

.reviews-message {
  padding: 25px;
  text-align: center;
  color: #71717a;
  background: white;
  border-radius: 12px;
  margin-bottom: 25px;
}

.reviews-message.error {
  color: #dc2626;
}

.retry-button {
  display: block;
  margin: 12px auto 0;
  border: none;
  background: transparent;
  color: #a38253;
  cursor: pointer;
  font-family: inherit;
}

/* کارت نظر */
.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.review-card {
  padding: 22px;
  border: 1px solid #eeeef0;
  border-radius: 14px;
  background: white;
}

.review-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 15px;
}

.review-user {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 42px;
  height: 42px;
  display: grid;
  place-items: center;
  flex-shrink: 0;
  border-radius: 50%;
  background: #f3eee7;
  color: #a38253;
  font-size: 18px;
  font-weight: 800;
}

.review-user strong {
  display: block;
  font-size: 14px;
}

.review-date {
  display: block;
  margin-top: 5px;
  color: #a1a1aa;
  font-size: 11px;
}

.review-stars {
  display: flex;
  gap: 3px;
  color: #d4d4d8;
  font-size: 18px;
}

.review-stars span.active {
  color: #e5a323;
}

.review-body {
  margin: 18px 0 0;
  color: #52525b;
  font-size: 14px;
  line-height: 2.2;
  white-space: pre-wrap;
}

/* بدون نظر */
.empty-reviews {
  padding: 35px 20px;
  text-align: center;
  border-radius: 14px;
  background: white;
}

.empty-icon {
  color: #d4d4d8;
  font-size: 42px;
}

.empty-reviews h3 {
  margin: 12px 0;
  font-size: 17px;
}

.empty-reviews p {
  color: #71717a;
  font-size: 13px;
  line-height: 2;
}

/* فرم ثبت نظر */
.review-form-wrapper {
  margin-top: 40px;
  padding: 28px;
  border: 1px solid #eeeef0;
  border-radius: 16px;
  background: white;
}

.review-form-wrapper h3 {
  margin: 0 0 25px;
  font-size: 18px;
}

.login-required {
  padding: 20px;
  border-radius: 12px;
  background: #fafafa;
}

.login-required p {
  color: #52525b;
  font-size: 14px;
}

.login-button {
  padding: 11px 22px;
  border: none;
  border-radius: 9px;
  background: #18181b;
  color: white;
  font-family: inherit;
  cursor: pointer;
  margin-top: 10px;
}

.review-form {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.form-label {
  margin: 15px 0 10px;
  color: #3f3f46;
  font-size: 13px;
  font-weight: 700;
}

.rating-selector {
  display: flex;
  gap: 5px;
  margin-bottom: 10px;
}

.rating-selector button {
  padding: 0;
  border: none;
  background: transparent;
  color: #d4d4d8;
  font-size: 32px;
  cursor: pointer;
  transition: 0.2s;
}

.rating-selector button.selected {
  color: #e5a323;
}

.rating-selector button:hover {
  transform: scale(1.15);
}

.review-form textarea {
  width: 100%;
  box-sizing: border-box;
  padding: 15px;
  border: 1px solid #e4e4e7;
  border-radius: 10px;
  outline: none;
  resize: vertical;
  font-family: inherit;
  font-size: 13px;
  line-height: 2;
  color: #27272a;
}

.review-form textarea:focus {
  border-color: #a38253;
}

.textarea-footer {
  width: 100%;
  margin-top: 7px;
  text-align: left;
  color: #a1a1aa;
  font-size: 11px;
}

.submit-review-btn {
  margin-top: 20px;
  padding: 13px 35px;
  border: none;
  border-radius: 10px;
  background: #18181b;
  color: white;
  font-family: inherit;
  font-weight: 700;
  cursor: pointer;
  transition: 0.2s;
}

.submit-review-btn:hover:not(:disabled) {
  background: #a38253;
}

.submit-review-btn:disabled {
  background: #d4d4d8;
  cursor: not-allowed;
}

.review-notice {
  margin: 15px 0 0;
  color: #a1a1aa;
  font-size: 12px;
  line-height: 2;
}

.form-error {
  margin: 12px 0 0;
  color: #dc2626;
  font-size: 13px;
  line-height: 2;
}

.form-success {
  margin: 12px 0 0;
  color: #15803d;
  font-size: 13px;
  line-height: 2;
}

/* موبایل و تبلت */
@media (max-width: 768px) {
  .product-page {
    padding: 110px 16px 45px;
  }

  .product-container {
    grid-template-columns: 1fr;
    gap: 25px;
  }

  .product-image {
    max-width: 380px;
    max-height: 400px;
  }

  .product-details {
    padding-top: 0;
  }

  .product-details h1 {
    font-size: 23px;
  }

  .add-to-cart-btn {
    max-width: 100%;
  }

  .reviews-section {
    margin-top: 60px;
    padding-top: 30px;
  }

  .reviews-heading h2 {
    font-size: 18px;
  }

  .review-form-wrapper {
    padding: 20px 15px;
  }

  .review-card {
    padding: 17px;
  }

  .review-top {
    align-items: flex-start;
    flex-direction: column;
  }

  .review-stars {
    margin-right: 54px;
  }
}
</style>