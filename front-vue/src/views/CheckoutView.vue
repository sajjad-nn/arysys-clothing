<template>
  <main class="checkout-page" dir="rtl">
    <!-- هدر صفحه -->
    <div class="checkout-header">
      <div class="checkout-header-content">
        <div>
          <span class="checkout-eyebrow">ARYASYS CHECKOUT</span>
          <h1>تکمیل سفارش</h1>
          <p>فقط چند قدم تا نهایی شدن خرید شما باقی مانده است.</p>
        </div>

        <RouterLink to="/shop" class="back-to-shop">
          ادامه خرید
          <span>←</span>
        </RouterLink>
      </div>
    </div>

    <!-- سبد خرید خالی -->
    <section v-if="cartStore.items.length === 0" class="empty-checkout">
      <div class="empty-icon">🛍</div>
      <h2>سبد خرید شما خالی است</h2>
      <p>برای ثبت سفارش، ابتدا محصولی به سبد خرید خود اضافه کنید.</p>

      <RouterLink to="/shop" class="main-button">
        مشاهده محصولات
        <span>←</span>
      </RouterLink>
    </section>

    <!-- محتوای پرداخت -->
    <div v-else class="checkout-container">
      <!-- ستون فرم -->
      <form class="checkout-form" @submit.prevent="submitOrder">
        <!-- اطلاعات گیرنده -->
        <section class="checkout-card">
          <div class="card-heading">
            <div class="heading-icon">01</div>

            <div>
              <h2>اطلاعات گیرنده</h2>
              <p>مشخصات فرد تحویل‌گیرنده سفارش را وارد کنید.</p>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label for="firstName">نام</label>
              <input
                id="firstName"
                v-model.trim="form.firstName"
                type="text"
                placeholder="نام خود را وارد کنید"
                required
                maxlength="100"
              />
            </div>

            <div class="form-group">
              <label for="lastName">نام خانوادگی</label>
              <input
                id="lastName"
                v-model.trim="form.lastName"
                type="text"
                placeholder="نام خانوادگی خود را وارد کنید"
                required
                maxlength="100"
              />
            </div>

            <div class="form-group full-width">
              <label for="mobile">شماره موبایل</label>
              <input
                id="mobile"
                v-model.trim="form.mobile"
                type="tel"
                inputmode="numeric"
                placeholder="09123456789"
                pattern="09[0-9]{9}"
                maxlength="11"
                required
              />
              <small>شماره موبایل باید با 09 شروع شود.</small>
            </div>
          </div>
        </section>

        <!-- آدرس تحویل -->
        <section class="checkout-card">
          <div class="card-heading">
            <div class="heading-icon">02</div>

            <div>
              <h2>آدرس تحویل سفارش</h2>
              <p>آدرس دقیق دریافت سفارش را وارد کنید.</p>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label for="province">استان</label>
              <input
                id="province"
                v-model.trim="form.province"
                type="text"
                placeholder="مثلاً تهران"
                required
              />
            </div>

            <div class="form-group">
              <label for="city">شهر</label>
              <input
                id="city"
                v-model.trim="form.city"
                type="text"
                placeholder="مثلاً تهران"
                required
              />
            </div>

            <div class="form-group full-width">
              <label for="address">آدرس کامل</label>
              <textarea
                id="address"
                v-model.trim="form.address"
                rows="4"
                placeholder="خیابان، کوچه، پلاک، واحد و سایر جزئیات..."
                required
                maxlength="1000"
              ></textarea>
            </div>

            <div class="form-group full-width">
              <label for="postalCode">کد پستی</label>
              <input
                id="postalCode"
                v-model.trim="form.postalCode"
                type="text"
                inputmode="numeric"
                placeholder="کد پستی ۱۰ رقمی"
                pattern="[0-9]{10}"
                maxlength="10"
                required
              />
            </div>
          </div>
        </section>

        <!-- روش ارسال -->
        <section class="checkout-card">
          <div class="card-heading">
            <div class="heading-icon">03</div>

            <div>
              <h2>روش ارسال</h2>
              <p>روش دریافت سفارش خود را انتخاب کنید.</p>
            </div>
          </div>

          <div class="shipping-options">
            <label class="shipping-option" :class="{ selected: shippingMethod === 'standard' }">
              <input v-model="shippingMethod" type="radio" value="standard" name="shipping" />

              <div class="shipping-radio"></div>

              <div class="shipping-info">
                <strong>ارسال عادی</strong>
                <span>تحویل در بازه ۳ تا ۵ روز کاری</span>
              </div>

              <strong class="shipping-price">
                {{ formatPrice(standardShipping) }}
                <small>تومان</small>
              </strong>
            </label>

            <label class="shipping-option" :class="{ selected: shippingMethod === 'express' }">
              <input v-model="shippingMethod" type="radio" value="express" name="shipping" />

              <div class="shipping-radio"></div>

              <div class="shipping-info">
                <strong>ارسال سریع</strong>
                <span>تحویل در بازه ۱ تا ۲ روز کاری</span>
              </div>

              <strong class="shipping-price">
                {{ formatPrice(expressShipping) }}
                <small>تومان</small>
              </strong>
            </label>
          </div>
        </section>

        <!-- توضیحات سفارش -->
        <section class="checkout-card">
          <div class="card-heading">
            <div class="heading-icon">04</div>

            <div>
              <h2>توضیحات سفارش</h2>
              <p>در صورت نیاز، توضیحات بیشتری ثبت کنید.</p>
            </div>
          </div>

          <div class="form-group">
            <label for="description">توضیحات (اختیاری)</label>
            <textarea
              id="description"
              v-model.trim="form.description"
              rows="3"
              placeholder="توضیحات مربوط به سفارش یا زمان تحویل..."
              maxlength="1000"
            ></textarea>
          </div>
        </section>

        <!-- روش پرداخت -->
        <section class="checkout-card payment-card">
          <div class="card-heading">
            <div class="heading-icon">05</div>

            <div>
              <h2>روش پرداخت</h2>
              <p>پرداخت آنلاین پس از اتصال درگاه فعال خواهد شد.</p>
            </div>
          </div>

          <div class="payment-method">
            <div class="payment-icon">⌑</div>

            <div class="payment-info">
              <strong>پرداخت آنلاین</strong>
              <span>درگاه پرداخت در حال آماده‌سازی است.</span>
            </div>

            <span class="coming-soon">به‌زودی</span>
          </div>

          <p class="payment-note">
            در حال حاضر این صفحه صرفاً نمایشی است و هیچ مبلغی از شما دریافت نمی‌شود.
          </p>
        </section>

        <!-- دکمه ثبت -->
        <button type="submit" class="submit-order">
          <span>ثبت سفارش آزمایشی</span>
          <span class="submit-arrow">←</span>
        </button>

        <p class="submit-description">ثبت سفارش در این نسخه آزمایشی، پرداخت واقعی انجام نمی‌دهد.</p>
      </form>

      <!-- ستون خلاصه سفارش -->
      <aside class="order-sidebar">
        <div class="order-summary">
          <div class="summary-heading">
            <h2>خلاصه سفارش</h2>
            <span>{{ cartStore.totalItems }} کالا</span>
          </div>

          <!-- محصولات -->
          <div class="summary-products">
            <article
              v-for="item in cartStore.items"
              :key="getItemKey(item)"
              class="summary-product"
            >
              <div class="product-image">
                <img v-if="getImage(item)" :src="getImage(item)" :alt="item.name" />

                <div v-else class="no-image">AR</div>
              </div>

              <div class="product-details">
                <h3>{{ item.name }}</h3>

                <div class="product-variants">
                  <span v-if="item.size"> سایز: {{ item.size }} </span>

                  <span v-if="item.color"> رنگ: {{ item.color }} </span>
                </div>

                <div class="quantity-control">
                  <button type="button" aria-label="افزایش تعداد" @click="changeQuantity(item, 1)">
                    +
                  </button>

                  <span>{{ item.quantity }}</span>

                  <button
                    type="button"
                    aria-label="کاهش تعداد"
                    :disabled="item.quantity <= 1"
                    @click="changeQuantity(item, -1)"
                  >
                    −
                  </button>
                </div>

                <button type="button" class="remove-product" @click="removeItem(item)">
                  حذف محصول
                </button>
              </div>

              <div class="product-prices">
                <strong>
                  {{ formatPrice(getFinalPrice(item) * item.quantity) }}
                </strong>

                <del v-if="getOriginalPrice(item) > getFinalPrice(item)">
                  {{ formatPrice(getOriginalPrice(item) * item.quantity) }}
                </del>

                <small>تومان</small>
              </div>
            </article>
          </div>

          <!-- محاسبات -->
          <div class="summary-calculations">
            <div class="calculation-row">
              <span>مبلغ محصولات</span>
              <strong>{{ formatPrice(subtotal) }} تومان</strong>
            </div>

            <div class="calculation-row discount-row">
              <span>تخفیف محصولات</span>
              <strong>
                {{ discountTotal > 0 ? `− ${formatPrice(discountTotal)}` : '۰' }}
                تومان
              </strong>
            </div>

            <div class="calculation-row">
              <span>هزینه ارسال</span>
              <strong>{{ formatPrice(shippingCost) }} تومان</strong>
            </div>
          </div>

          <!-- مبلغ نهایی -->
          <div class="final-price">
            <div>
              <span>مبلغ قابل پرداخت</span>
              <small>مجموع نهایی سفارش</small>
            </div>

            <strong>
              {{ formatPrice(totalPrice) }}
              <small>تومان</small>
            </strong>
          </div>

          <div class="secure-note">
            <span>✓</span>
            <p>اطلاعات شما در این نسخه فقط در همین صفحه استفاده می‌شود.</p>
          </div>
        </div>

        <RouterLink to="/shop" class="continue-shopping">
          <span>←</span>
          بازگشت به فروشگاه
        </RouterLink>
      </aside>
    </div>

    <!-- پیام موفقیت نمایشی -->
    <Transition name="modal">
      <div v-if="showSuccess" class="success-overlay" @click.self="showSuccess = false">
        <div class="success-modal">
          <button type="button" class="modal-close" aria-label="بستن" @click="showSuccess = false">
            ×
          </button>

          <div class="success-icon">✓</div>

          <span class="success-eyebrow">ARYASYS</span>

          <h2>اطلاعات سفارش ثبت شد!</h2>

          <p>
            این یک ثبت سفارش آزمایشی است. برای نهایی شدن خرید، درگاه پرداخت باید به سایت متصل شود.
          </p>

          <button type="button" class="success-button" @click="showSuccess = false">
            متوجه شدم
          </button>
        </div>
      </div>
    </Transition>
  </main>
</template>

<script setup>
import { ref, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { useCartStore } from '../stores/cart'

const cartStore = useCartStore()

// اطلاعات فرم
const form = ref({
  firstName: '',
  lastName: '',
  mobile: '',
  province: '',
  city: '',
  address: '',
  postalCode: '',
  description: '',
})

// روش ارسال
const shippingMethod = ref('standard')

// هزینه‌های نمایشی ارسال
const standardShipping = 45000
const expressShipping = 85000

// وضعیت پنجره موفقیت
const showSuccess = ref(false)

// مبلغ هر محصول با در نظر گرفتن تخفیف
const getFinalPrice = (item) => {
  return Number(item.final_price ?? item.price ?? 0)
}

// قیمت اولیه محصول
const getOriginalPrice = (item) => {
  return Number(item.price ?? 0)
}

// مبلغ محصولات
const subtotal = computed(() => {
  return cartStore.items.reduce((total, item) => {
    return total + getFinalPrice(item) * Number(item.quantity || 0)
  }, 0)
})

// مجموع تخفیف محصولات
const discountTotal = computed(() => {
  return cartStore.items.reduce((total, item) => {
    const original = getOriginalPrice(item)
    const final = getFinalPrice(item)
    const quantity = Number(item.quantity || 0)

    return total + Math.max(0, original - final) * quantity
  }, 0)
})

// هزینه ارسال
const shippingCost = computed(() => {
  return shippingMethod.value === 'express' ? expressShipping : standardShipping
})

// مبلغ نهایی
const totalPrice = computed(() => {
  return subtotal.value + shippingCost.value
})

// فرمت قیمت
const formatPrice = (price) => {
  return new Intl.NumberFormat('fa-IR').format(Math.round(Number(price) || 0))
}

// تصویر محصول
const getImage = (item) => {
  const image = item.image || item.image_url

  if (!image) return ''

  // اگر آدرس تصویر کامل باشد
  if (image.startsWith('http://') || image.startsWith('https://') || image.startsWith('data:')) {
    return image
  }

  // حذف اسلش ابتدایی و storage تکراری
  const cleanPath = image.replace(/^\/+/, '').replace(/^storage\//, '')

  // اتصال مسیر تصویر به آدرس بک‌اند
  const apiUrl = import.meta.env.VITE_API_URL.replace(/\/+$/, '')

  return `${apiUrl}/storage/${cleanPath}`
}

// شناسه یکتای هر آیتم سبد خرید
const getItemKey = (item) => {
  return [
    item.id ?? item.product_id,
    item.variant_id ?? '',
    item.size ?? '',
    item.color ?? '',
  ].join('-')
}

// تغییر تعداد محصول
const changeQuantity = (item, amount) => {
  const newQuantity = Number(item.quantity) + amount

  if (newQuantity < 1) return

  const id = item.id ?? item.product_id

  cartStore.updateQuantity(id, item.size, item.color, newQuantity)
}

// حذف محصول
const removeItem = (item) => {
  const id = item.id ?? item.product_id

  cartStore.removeFromCart(id, item.size, item.color)
}

// ثبت سفارش نمایشی
const submitOrder = () => {
  if (cartStore.items.length === 0) return

  showSuccess.value = true
}
</script>

<style scoped>
.checkout-page {
  min-height: 100vh;
  background: #f8f7f5;
  color: #211e19;
  direction: rtl;
  padding-bottom: 80px;
  font-family: inherit;
}

/* هدر */
.checkout-header {
  width: 100%;
  padding: 45px 0 35px;
  background: #fff;
  border-bottom: 1px solid #eee9e1;
}

.checkout-header-content {
  width: 86%;
  max-width: 1320px;
  margin: 0 auto;

  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.checkout-eyebrow {
  color: #a68a5b;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 3px;
}

.checkout-header h1 {
  margin: 9px 0 5px;
  font-size: clamp(25px, 3vw, 36px);
  font-weight: 900;
}

.checkout-header p {
  margin: 0;
  color: #89847c;
  font-size: 13px;
  line-height: 2;
}

.back-to-shop {
  display: inline-flex;
  align-items: center;
  gap: 12px;

  padding: 12px 20px;
  border: 1px solid #e5d7bd;
  border-radius: 30px;

  color: #8b7657;
  font-size: 12px;
  font-weight: 700;
  text-decoration: none;

  transition: 0.3s ease;
}

.back-to-shop:hover {
  background: #17140f;
  color: #fff;
  border-color: #17140f;
}

/* چیدمان اصلی */
.checkout-container {
  width: 86%;
  max-width: 1320px;
  margin: 35px auto 0;

  display: grid;
  grid-template-columns: minmax(0, 1.55fr) minmax(300px, 0.85fr);
  align-items: start;
  gap: 28px;
}

/* فرم */
.checkout-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
  min-width: 0;
}

.checkout-card {
  background: #fff;
  border: 1px solid #eeeae4;
  border-radius: 17px;
  padding: 25px;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.025);
}

.card-heading {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 25px;
}

.heading-icon {
  width: 43px;
  height: 43px;
  flex-shrink: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: 13px;
  background: #f5f0e7;
  color: #a68a5b;

  font-size: 13px;
  font-weight: 900;
}

.card-heading h2 {
  margin: 0 0 5px;
  font-size: 16px;
  font-weight: 800;
}

.card-heading p {
  margin: 0;
  color: #99938a;
  font-size: 11px;
  line-height: 1.8;
}

/* فیلدها */
.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 20px 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 9px;
  min-width: 0;
}

.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-size: 12px;
  font-weight: 700;
  color: #39342d;
}

.form-group input,
.form-group textarea {
  width: 100%;
  box-sizing: border-box;

  padding: 13px 15px;
  border: 1px solid #e9e5df;
  border-radius: 10px;

  background: #fcfbfa;
  color: #28241f;

  font-family: inherit;
  font-size: 12px;
  outline: none;

  transition:
    border-color 0.25s ease,
    box-shadow 0.25s ease;
}

.form-group input {
  height: 47px;
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
  line-height: 2;
}

.form-group input::placeholder,
.form-group textarea::placeholder {
  color: #b8b2aa;
}

.form-group input:focus,
.form-group textarea:focus {
  border-color: #c7a66a;
  box-shadow: 0 0 0 3px rgba(199, 166, 106, 0.1);
  background: #fff;
}

.form-group small {
  color: #a49c91;
  font-size: 10px;
}

/* روش ارسال */
.shipping-options {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.shipping-option {
  position: relative;

  display: flex;
  align-items: center;
  gap: 13px;

  padding: 16px;
  border: 1px solid #ebe7e0;
  border-radius: 12px;

  cursor: pointer;
  transition: 0.25s ease;
}

.shipping-option.selected {
  border-color: #c7a66a;
  background: #fcfaf6;
}

.shipping-option input {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.shipping-radio {
  width: 17px;
  height: 17px;
  flex-shrink: 0;

  border: 1.5px solid #c9c2b7;
  border-radius: 50%;

  position: relative;
}

.shipping-option.selected .shipping-radio {
  border-color: #a68a5b;
}

.shipping-option.selected .shipping-radio::after {
  content: '';
  position: absolute;
  inset: 4px;
  border-radius: 50%;
  background: #a68a5b;
}

.shipping-info {
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-width: 0;
}

.shipping-info strong {
  font-size: 12px;
}

.shipping-info span {
  color: #99938a;
  font-size: 10px;
}

.shipping-price {
  margin-right: auto;
  white-space: nowrap;
  font-size: 12px;
}

.shipping-price small {
  color: #99938a;
  font-size: 9px;
  font-weight: 400;
}

/* پرداخت */
.payment-card {
  border-color: #e8dfcf;
}

.payment-method {
  display: flex;
  align-items: center;
  gap: 13px;

  padding: 16px;
  border: 1px solid #eee8dc;
  border-radius: 12px;
  background: #fcfaf6;
}

.payment-icon {
  width: 43px;
  height: 43px;

  display: flex;
  align-items: center;
  justify-content: center;

  background: #f1e9dc;
  color: #a68a5b;
  border-radius: 12px;
  font-size: 24px;
}

.payment-info {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.payment-info strong {
  font-size: 12px;
}

.payment-info span {
  color: #99938a;
  font-size: 10px;
}

.coming-soon {
  margin-right: auto;
  padding: 6px 10px;
  border-radius: 20px;
  background: #f0e9dd;
  color: #92774d;
  font-size: 9px;
  font-weight: 700;
  white-space: nowrap;
}

.payment-note {
  margin: 13px 0 0;
  color: #a49c91;
  font-size: 10px;
  line-height: 2;
}

/* ثبت سفارش */
.submit-order {
  width: 100%;
  min-height: 55px;

  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;

  border: 0;
  border-radius: 12px;

  background: #17140f;
  color: #fff;

  font-family: inherit;
  font-size: 13px;
  font-weight: 800;

  cursor: pointer;
  transition: 0.3s ease;
}

.submit-order:hover {
  background: #8b7657;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(139, 118, 87, 0.17);
}

.submit-arrow {
  font-size: 18px;
  transition: transform 0.3s ease;
}

.submit-order:hover .submit-arrow {
  transform: translateX(-5px);
}

.submit-description {
  margin: -12px 0 0;
  color: #a49c91;
  font-size: 10px;
  text-align: center;
}

/* سایدبار خلاصه سفارش */
.order-sidebar {
  position: sticky;
  top: 25px;
  min-width: 0;
}

.order-summary {
  background: #fff;
  border: 1px solid #eeeae4;
  border-radius: 17px;
  padding: 23px;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.025);
}

.summary-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 18px;
  border-bottom: 1px solid #eeeae4;
}

.summary-heading h2 {
  margin: 0;
  font-size: 16px;
  font-weight: 900;
}

.summary-heading > span {
  padding: 6px 11px;
  background: #f5f0e7;
  color: #92774d;
  border-radius: 20px;
  font-size: 10px;
  font-weight: 700;
}

/* محصولات */
.summary-products {
  display: flex;
  flex-direction: column;
}

.summary-product {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 18px 0;
  border-bottom: 1px solid #f0ede8;
}

.product-image {
  width: 75px;
  height: 90px;
  flex-shrink: 0;

  border-radius: 10px;
  overflow: hidden;
  background: #f4f1ec;
}

.product-image img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
}

.no-image {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #a68a5b;
  font-size: 18px;
  font-weight: 900;
}

.product-details {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 7px;
  min-width: 0;
  flex: 1;
}

.product-details h3 {
  margin: 0;
  color: #28241f;
  font-size: 12px;
  font-weight: 800;
  line-height: 1.8;
  overflow-wrap: anywhere;
}

.product-variants {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.product-variants span {
  color: #99938a;
  font-size: 10px;
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 12px;

  padding: 4px 8px;
  border: 1px solid #eeeae4;
  border-radius: 7px;
}

.quantity-control button {
  border: 0;
  background: transparent;
  color: #8b7657;
  font-size: 16px;
  cursor: pointer;
  line-height: 1;
}

.quantity-control button:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.quantity-control span {
  font-size: 11px;
  font-weight: 800;
  min-width: 12px;
  text-align: center;
}

.remove-product {
  padding: 0;
  border: 0;
  background: transparent;
  color: #b36c62;
  font-family: inherit;
  font-size: 10px;
  cursor: pointer;
}

.product-prices {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 5px;
  white-space: nowrap;
}

.product-prices strong {
  color: #29251f;
  font-size: 11px;
}

.product-prices del {
  color: #aaa39a;
  font-size: 9px;
}

.product-prices small {
  color: #99938a;
  font-size: 9px;
}

/* محاسبات */
.summary-calculations {
  display: flex;
  flex-direction: column;
  gap: 15px;
  padding: 20px 0;
}

.calculation-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  font-size: 11px;
}

.calculation-row span {
  color: #89847c;
}

.calculation-row strong {
  color: #39342d;
  font-size: 11px;
  white-space: nowrap;
}

.discount-row strong {
  color: #63946c;
}

/* قیمت نهایی */
.final-price {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;

  padding: 18px 15px;
  border-radius: 12px;
  background: #f7f3eb;
}

.final-price > div {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.final-price > div span {
  font-size: 12px;
  font-weight: 800;
}

.final-price > div small {
  color: #99938a;
  font-size: 9px;
}

.final-price > strong {
  color: #92774d;
  font-size: 18px;
  white-space: nowrap;
}

.final-price > strong small {
  font-size: 10px;
  font-weight: 500;
}

/* یادداشت امنیت */
.secure-note {
  display: flex;
  align-items: center;
  gap: 9px;
  margin-top: 17px;
}

.secure-note > span {
  width: 20px;
  height: 20px;
  flex-shrink: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: 50%;
  background: #edf5ed;
  color: #65946b;
  font-size: 11px;
}

.secure-note p {
  margin: 0;
  color: #99938a;
  font-size: 9px;
  line-height: 1.8;
}

.continue-shopping {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 9px;

  margin-top: 15px;
  padding: 14px;

  color: #8b7657;
  text-decoration: none;
  font-size: 11px;
  font-weight: 700;

  border: 1px solid #e5d7bd;
  border-radius: 12px;
  transition: 0.3s ease;
}

.continue-shopping:hover {
  background: #fff;
}

/* سبد خالی */
.empty-checkout {
  width: 90%;
  max-width: 500px;
  margin: 90px auto;
  padding: 45px 25px;

  text-align: center;
  background: #fff;
  border: 1px solid #eeeae4;
  border-radius: 20px;
}

.empty-icon {
  font-size: 45px;
  margin-bottom: 15px;
}

.empty-checkout h2 {
  margin: 0 0 10px;
  font-size: 20px;
}

.empty-checkout p {
  margin: 0 0 25px;
  color: #99938a;
  font-size: 12px;
  line-height: 2;
}

.main-button {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 13px 25px;

  border-radius: 30px;
  background: #17140f;
  color: #fff;

  text-decoration: none;
  font-size: 12px;
  font-weight: 700;
  transition: 0.3s ease;
}

.main-button:hover {
  background: #8b7657;
}

/* پنجره موفقیت */
.success-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;

  display: flex;
  align-items: center;
  justify-content: center;

  padding: 20px;
  background: rgba(20, 18, 15, 0.65);
  backdrop-filter: blur(5px);
}

.success-modal {
  position: relative;
  width: 100%;
  max-width: 420px;

  padding: 40px 30px;
  border-radius: 20px;
  background: #fff;

  text-align: center;
  box-shadow: 0 20px 70px rgba(0, 0, 0, 0.2);
}

.modal-close {
  position: absolute;
  top: 13px;
  left: 15px;

  border: 0;
  background: transparent;
  color: #999;
  font-size: 25px;
  cursor: pointer;
}

.success-icon {
  width: 65px;
  height: 65px;
  margin: 0 auto 20px;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: 50%;
  background: #edf5ed;
  color: #65946b;
  font-size: 30px;
  font-weight: 800;
}

.success-eyebrow {
  color: #a68a5b;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 3px;
}

.success-modal h2 {
  margin: 13px 0;
  font-size: 20px;
}

.success-modal p {
  margin: 0;
  color: #89847c;
  font-size: 12px;
  line-height: 2.2;
}

.success-button {
  width: 100%;
  margin-top: 25px;
  padding: 13px;

  border: 0;
  border-radius: 10px;
  background: #17140f;
  color: #fff;

  font-family: inherit;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: 0.3s ease;
}

.success-button:hover {
  background: #8b7657;
}

/* انیمیشن پنجره */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.25s ease;
}

.modal-enter-active .success-modal,
.modal-leave-active .success-modal {
  transition: transform 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .success-modal,
.modal-leave-to .success-modal {
  transform: translateY(20px) scale(0.97);
}

/* تبلت */
@media (max-width: 1024px) {
  .checkout-container,
  .checkout-header-content {
    width: 92%;
  }

  .checkout-container {
    grid-template-columns: minmax(0, 1.3fr) minmax(280px, 0.9fr);
    gap: 18px;
  }

  .checkout-card {
    padding: 20px;
  }

  .order-summary {
    padding: 18px;
  }
}

/* موبایل */
@media (max-width: 768px) {
  .checkout-page {
    padding-bottom: 45px;
  }

  .checkout-header {
    padding: 30px 0 25px;
  }

  .checkout-header-content {
    align-items: flex-start;
  }

  .checkout-header h1 {
    font-size: 25px;
  }

  .checkout-header p {
    font-size: 11px;
  }

  .back-to-shop {
    padding: 10px 13px;
    font-size: 10px;
    white-space: nowrap;
  }

  .checkout-container {
    width: 92%;
    margin-top: 20px;
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .checkout-form {
    gap: 15px;
  }

  .checkout-card {
    padding: 18px 15px;
    border-radius: 14px;
  }

  .card-heading {
    margin-bottom: 20px;
  }

  .card-heading h2 {
    font-size: 14px;
  }

  .card-heading p {
    font-size: 10px;
  }

  .form-grid {
    gap: 16px 10px;
  }

  .form-group input {
    height: 44px;
  }

  .form-group input,
  .form-group textarea {
    padding: 11px;
    font-size: 11px;
  }

  .form-group label {
    font-size: 11px;
  }

  .shipping-option {
    padding: 13px 10px;
    gap: 9px;
  }

  .shipping-info strong,
  .shipping-price {
    font-size: 10px;
  }

  .shipping-info span {
    font-size: 9px;
  }

  .order-sidebar {
    position: static;
  }

  .order-summary {
    padding: 18px 15px;
  }

  .summary-heading h2 {
    font-size: 14px;
  }

  .product-image {
    width: 68px;
    height: 82px;
  }

  .final-price > strong {
    font-size: 16px;
  }

  .empty-checkout {
    margin: 45px auto;
    padding: 35px 20px;
  }
}

/* موبایل کوچک */
@media (max-width: 420px) {
  .checkout-header-content {
    flex-direction: column;
    gap: 15px;
  }

  .back-to-shop {
    align-self: flex-start;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .full-width {
    grid-column: auto;
  }

  .shipping-option {
    flex-wrap: wrap;
  }

  .shipping-price {
    margin-right: auto;
  }

  .summary-product {
    gap: 8px;
  }

  .product-image {
    width: 58px;
    height: 75px;
  }

  .product-details h3 {
    font-size: 11px;
  }

  .product-prices strong {
    font-size: 10px;
  }
}
</style>
