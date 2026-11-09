
<template>
  <main class="product-page">

    <!-- وضعیت بارگذاری -->
    <div v-if="loading" class="message">
      <span class="loader"></span>
      <p>در حال دریافت اطلاعات محصول...</p>
    </div>

    <!-- خطا -->
    <div v-else-if="error" class="message error">
      {{ error }}

      <button class="back-button" @click="goBack">
        بازگشت به فروشگاه
      </button>
    </div>

    <!-- اطلاعات محصول -->
    <div v-else-if="product" class="product-container">

      <!-- تصویر محصول -->
      <div class="product-image">
        <img
          v-if="currentImage && !imageError"
          :src="imageUrl(currentImage)"
          :alt="product.name"
          @error="imageError = true"
        />

        <div v-if="!currentImage || imageError" class="no-image">
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
            <label>انتخاب رنگ</label>

            <select v-model="selectedColor">
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
            <label>انتخاب سایز</label>

            <select v-model="selectedSize">
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
            >
              −
            </button>

            <span>{{ quantity.toLocaleString('fa-IR') }}</span>

            <button
              type="button"
              @click="increaseQuantity"
              :disabled="quantity >= currentStock"
            >
              +
            </button>
          </div>
        </div>

        <!-- افزودن به سبد خرید -->
        <button
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

        <button class="back-button" @click="goBack">
          بازگشت به فروشگاه
        </button>

      </div>
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

const props = defineProps({
  id: {
    type: String,
    required: true
  }
})

const router = useRouter()
const cart = useCartStore()

const API_BASE = 'http://127.0.0.1:8000'

const product = ref(null)
const loading = ref(true)
const error = ref('')

const selectedColor = ref('')
const selectedSize = ref('')
const quantity = ref(1)

const toastMessage = ref('')
const imageError = ref(false)

let toastTimer = null


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


// تصویر تنوع یا تصویر اصلی
const currentImage = computed(() => {
  return (
    selectedVariant.value?.image ||
    product.value?.image ||
    ''
  )
})


// اصلاح خطای تصویر:
// این watch باید بعد از تعریف currentImage باشد.
watch(currentImage, () => {
  imageError.value = false
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


// آدرس تصویر
function imageUrl(image) {
  if (!image) return ''

  const value = String(image).trim()

  if (/^https?:\/\//i.test(value)) {
    return value
  }

  const cleanPath = value
    .replace(/^\/+/, '')
    .replace(/^storage\/+/, '')

  return `${API_BASE}/storage/${cleanPath}`
}


// دریافت محصول
async function fetchProduct() {
  loading.value = true
  error.value = ''

  try {
    const response = await fetch(
      `${API_BASE}/api/products/${props.id}`
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

  // افزودن به سبد خرید از طریق Pinia
  cart.addToCart(productData)

  showToast('محصول با موفقیت به سبد خرید اضافه شد.')
}


// بازگشت به فروشگاه
function goBack() {
  router.push({ name: 'shop' })
}


onMounted(fetchProduct)


// پاک‌سازی تایمر هنگام خروج از صفحه
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

.product-container {
  max-width: 1100px;
  margin: 0 auto;
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
}
</style>
