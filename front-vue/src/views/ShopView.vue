<template>
  <div class="shop-page">
    <Header />

    <main class="shop-container">
      <h1>فروشگاه Arysys</h1>
      <p class="shop-description">محصولات مورد علاقه‌ات رو پیدا کن.</p>

      <div v-if="loading" class="message">در حال دریافت محصولات...</div>

      <div v-else-if="error" class="message error">
        {{ error }}
      </div>

      <div v-else-if="products.length === 0" class="message">
        در حال حاضر محصولی برای نمایش وجود ندارد.
      </div>

      <div v-else class="products-grid">
        <article v-for="product in products" :key="product.id" class="product-card">
          <img
            v-if="product.image"
            :src="imageUrl(product.image)"
            :alt="product.name"
            class="product-image"
          />

          <div v-else class="no-image">بدون تصویر</div>

          <div class="product-info">
  <h2>{{ product.name }}</h2>

  <p v-if="product.category">
    {{ product.category.name }}
  </p>

  <template v-if="hasVariants(product)">
    <label class="variant-label">رنگ</label>

    <select
      v-model="selectedVariants[product.id].color"
      class="variant-select"
      @change="onColorChange(product)"
    >
      <option
        v-for="color in getColors(product)"
        :key="color"
        :value="color"
      >
        {{ color }}
      </option>
    </select>

    <label class="variant-label">سایز</label>

    <select
      v-model="selectedVariants[product.id].size"
      class="variant-select"
      @change="onSizeChange(product)"
    >
      <option
        v-for="size in getSizes(product)"
        :key="size"
        :value="size"
      >
        {{ size }}
      </option>
    </select>
  </template>

  <div class="product-price">
    {{ formatPrice(getCurrentPrice(product)) }} تومان
  </div>

  <button
    class="add-to-cart-btn"
    :disabled="getCurrentStock(product) <= 0"
    @click="addToCart(product)"
  >
    {{ getCurrentStock(product) > 0 ? 'افزودن به سبد خرید' : 'ناموجود' }}
  </button>

  <span v-if="getCurrentStock(product) > 0" class="stock">
    موجود در انبار: {{ getCurrentStock(product) }} عدد
  </span>

  <span v-else class="out-of-stock">
    ناموجود
  </span>
</div>
        </article>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Header from '../components/header.vue'
import Footer from '../components/footer.vue'

const products = ref([])
const loading = ref(true)
const error = ref('')

const selectedVariants = ref({})

function hasVariants(product) {
  return Array.isArray(product.variants) &&
    product.variants.some(variant => variant.is_active)
}

function getVariants(product) {
  return (product.variants || []).filter(variant => variant.is_active)
}

function getColors(product) {
  return [...new Set(
    getVariants(product)
      .map(variant => variant.color)
      .filter(Boolean)
  )]
}

function getSizes(product) {
  const color = selectedVariants.value[product.id]?.color

  return [...new Set(
    getVariants(product)
      .filter(variant => !color || variant.color === color)
      .map(variant => variant.size)
      .filter(Boolean)
  )]
}

function getSelectedVariant(product) {
  const selected = selectedVariants.value[product.id]

  if (!selected) return null

  return getVariants(product).find(variant =>
    variant.color === selected.color &&
    variant.size === selected.size
  ) || null
}

function initializeVariant(product) {
  if (!hasVariants(product)) return

  const firstVariant = getVariants(product)[0]

  selectedVariants.value[product.id] = {
    color: firstVariant.color || '',
    size: firstVariant.size || '',
  }

  syncVariantSelection(product)
}

function syncVariantSelection(product) {
  const selected = selectedVariants.value[product.id]

  if (!selected) return

  const variants = getVariants(product)

  const matchingColor = variants.find(
    variant => variant.color === selected.color
  )

  if (!matchingColor) {
    selected.color = variants[0]?.color || ''
  }

  const sizes = getSizes(product)

  if (!sizes.includes(selected.size)) {
    selected.size = sizes[0] || ''
  }
}

function onColorChange(product) {
  syncVariantSelection(product)
}

function onSizeChange(product) {
  syncVariantSelection(product)
}

function getCurrentPrice(product) {
  const variant = getSelectedVariant(product)

  if (!variant) {
    return product.discount_price ?? product.price
  }

  return variant.discount_price ?? variant.price
}

function getCurrentStock(product) {
  const variant = getSelectedVariant(product)

  return variant ? variant.stock : product.stock
}

// دریافت محصولات از Laravel
async function fetchProducts() {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/products')

    if (!response.ok) {
      throw new Error('دریافت محصولات با خطا مواجه شد.')
    }

    const data = await response.json()

    // پشتیبانی از پاسخ آرایه‌ای و پاسخ صفحه‌بندی‌شده Laravel
    products.value = Array.isArray(data) ? data : (data.data ?? [])
    products.value.forEach(initializeVariant)
  } catch (err) {
    error.value = err.message || 'ارتباط با سرور برقرار نشد.'
  } finally {
    loading.value = false
  }
}

function imageUrl(image) {
  if (image.startsWith('http://') || image.startsWith('https://')) {
    return image
  }

  return `http://127.0.0.1:8000/storage/${image.replace(/^\/+/, '')}`
}

function formatPrice(price) {
  return Number(price).toLocaleString('fa-IR')
}

function addToCart(product) {
  const variant = getSelectedVariant(product)

  if (hasVariants(product) && !variant) {
    alert('لطفاً یک ترکیب رنگ و سایز معتبر انتخاب کن.')
    return
  }

  if (getCurrentStock(product) <= 0) {
    alert('این محصول در حال حاضر ناموجود است.')
    return
  }

  const details = variant
    ? `\nرنگ: ${variant.color || 'انتخاب نشده'}\nسایز: ${variant.size || 'انتخاب نشده'}`
    : ''

  alert(`محصول «${product.name}» انتخاب شد.${details}`)
}
onMounted(fetchProducts)
</script>

<style scoped>
.shop-container {
  direction: rtl;
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px 60px;
  min-height: 400px;
}

.shop-container h1 {
  font-size: 28px;
  margin: 0 0 10px;
}

.shop-description {
  color: #777;
  margin-bottom: 30px;
}

/* شبکه محصولات */
.products-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  align-items: stretch;
  gap: 24px;
}

/* کارت محصول */
.product-card {
  display: flex;
  flex-direction: column;
  min-width: 0;
  height: 100%;
  overflow: hidden;
  border: 1px solid #eee;
  border-radius: 12px;
  background: #fff;
  transition:
    transform 0.2s,
    box-shadow 0.2s;
  box-sizing: border-box;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px #00000012;
}

/* تصویر محصول */
.product-image,
.no-image {
  display: block;
  width: 100%;
  height: 240px;
  flex-shrink: 0;
  object-fit: cover;
}

.no-image {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  color: #888;
}

/* اطلاعات محصول */
.product-info {
  display: flex;
  flex-direction: column;
  flex: 1;
  padding: 16px;
  box-sizing: border-box;
}

/* عنوان‌ها ارتفاع یکسان دارند */
.product-info h2 {
  font-size: 17px;
  line-height: 24px;
  min-height: 48px;
  margin: 0 0 10px;
}

/* فضای دسته‌بندی حتی برای محصول بدون دسته حفظ می‌شود */
.product-info p {
  min-height: 21px;
  line-height: 21px;
  margin: 0;
  color: #777;
  font-size: 14px;
}

.variant-label {
  display: block;
  margin-top: 12px;
  margin-bottom: 6px;
  color: #555;
  font-size: 13px;
}

.variant-select {
  width: 100%;
  min-height: 38px;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 7px;
  background: #fff;
  font-family: inherit;
  font-size: 13px;
}

.add-to-cart-btn:disabled {
  background: #aaa;
  cursor: not-allowed;
}

/* قیمت‌ها در پایین کارت تراز می‌شوند */
.product-price {
  margin-top: auto;
  padding-top: 14px;
  font-weight: bold;
  line-height: 24px;
}

/* وضعیت موجودی */
.stock,
.out-of-stock {
  display: block;
  min-height: 20px;
  margin-top: 8px;
  font-size: 13px;
  line-height: 20px;
}

.stock {
  color: #16834a;
}

.out-of-stock {
  color: #c0392b;
}

/* دکمه خرید */
.add-to-cart-btn {
  width: 100%;
  min-height: 44px;
  margin-top: 14px;
  padding: 12px;
  border: none;
  border-radius: 8px;
  background: #222;
  color: #fff;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.2s;
  box-sizing: border-box;
}

.add-to-cart-btn:hover {
  background: #444;
}

/* پیام‌ها */
.message {
  padding: 30px;
  text-align: center;
}

.error {
  color: #c0392b;
}

/* تبلت */
@media (max-width: 900px) {
  .products-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
  }
}

/* موبایل */
@media (max-width: 480px) {
  .shop-container {
    padding: 25px 12px 40px;
  }

  .shop-container h1 {
    font-size: 23px;
  }

  .shop-description {
    margin-bottom: 22px;
  }

  .products-grid {
    gap: 12px;
  }

  .product-image,
  .no-image {
    height: 170px;
  }

  .product-info {
    padding: 12px;
  }

  .product-info h2 {
    font-size: 15px;
    line-height: 22px;
    min-height: 44px;
  }

  .product-info p {
    font-size: 12px;
  }

  .product-price {
    font-size: 14px;
  }

  .add-to-cart-btn {
    padding: 10px 6px;
    font-size: 12px;
  }
}
</style>
