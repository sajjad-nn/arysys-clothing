<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import api from '../services/api'

// تصاویر دسته‌بندی‌ها
import menImage from '../asset/images/categories/men.webp'
import womenImage from '../asset/images/categories/women.webp'
import shoesImage from '../asset/images/categories/shoes.webp'
import kidsImage from '../asset/images/categories/kids.webp'
import accessoriesImage from '../asset/images/categories/accessories.webp'
import sportswearImage from '../asset/images/categories/sportswear.webp'

const categories = ref([])
const loading = ref(true)
const error = ref(false)

// اتصال تصاویر به دسته‌بندی‌ها
const categoryImages = {
  Sportswear: sportswearImage,
  'Bags & Accessories': accessoriesImage,
  shoes: shoesImage,
  kids: kidsImage,
  'men-clothing': menImage,
  'women-clothing': womenImage,
}

const getCategoryImage = (category) => {
  return categoryImages[category.slug] || ''
}

const fetchCategories = async () => {
  try {
    loading.value = true
    error.value = false

    const response = await api.get('/api/categories')
    const data = response.data.data ?? response.data

    categories.value = Array.isArray(data)
      ? data.filter((category) => category.is_active !== false)
      : []
  } catch (err) {
    console.error('خطای دریافت دسته‌بندی‌ها:', err)
    error.value = true
  } finally {
    loading.value = false
  }
}

onMounted(fetchCategories)
</script>

<template>
  <section class="categories-section" dir="rtl">
    <div class="section-heading">
      <h2>دسته‌بندی محصولات</h2>
      <p>محصولات مورد علاقه‌ات را از دسته‌بندی‌های مختلف پیدا کن</p>
    </div>

    <div v-if="loading" class="message">در حال دریافت دسته‌بندی‌ها...</div>

    <div v-else-if="error" class="message error">دریافت دسته‌بندی‌ها با مشکل مواجه شد.</div>

    <div v-else-if="categories.length" class="categories-grid">
      <div v-for="category in categories" :key="category.id" class="card-wrapper">
        <RouterLink :to="`/shop?category=${category.slug}`" class="category-card">
          <!-- لایه درخشش شیشه‌ای سه‌بعدی -->
          <div class="card-glare"></div>

          <div class="category-image">
            <img
              v-if="getCategoryImage(category)"
              :src="getCategoryImage(category)"
              :alt="category.name"
            />
            <div v-else class="image-placeholder">تصویر موجود نیست</div>
          </div>

          <div class="card-title-box">
            <h3>{{ category.name }}</h3>
          </div>
        </RouterLink>
      </div>
    </div>

    <div v-else class="message">دسته‌بندی‌ای برای نمایش وجود ندارد.</div>
  </section>
</template>

<style scoped>
.categories-section {
  --gold: #c7a66a;
  --gold-light: #e0c48c;
  --gold-deep: #8b7657;
  --ink: #17140f;

  width: 82%;
  max-width: 1360px;
  margin: 70px auto;
}

/* ───────── عنوان بخش ───────── */

.section-heading {
  text-align: right;
  margin-bottom: 38px;
}

.section-heading h2 {
  position: relative;
  display: inline-block;
  margin: 0 0 12px;
  padding-bottom: 14px;
  font-size: 28px;
  font-weight: 800;
  color: var(--ink);
  letter-spacing: -0.5px;
}

.section-heading h2::after {
  content: '';
  position: absolute;
  right: 0;
  bottom: 0;
  width: 44px;
  height: 3px;
  border-radius: 3px;
  background: linear-gradient(90deg, var(--gold-light), var(--gold));
}

.section-heading p {
  margin: 0;
  color: #857e72;
  font-size: 14px;
}

/* ───────── شبکه دسته‌بندی‌ها ───────── */

.categories-grid {
  display: grid;
  grid-template-columns: repeat(6, minmax(0, 1fr));
  gap: 22px;
}

/* کانتینر پرسپکتیو برای هر کارت تا زاویه چرخش طبیعی و مستقل داشته باشد */
.card-wrapper {
  perspective: 1200px;
}

.category-card {
  position: relative;
  display: flex;
  flex-direction: column;
  min-width: 0;
  padding: 10px 10px 16px;
  border-radius: 24px;
  background: #ffffff;
  border: 1px solid rgba(235, 230, 222, 0.85);
  box-shadow:
    0 4px 6px -1px rgba(23, 20, 15, 0.03),
    0 10px 20px -8px rgba(23, 20, 15, 0.06);
  color: var(--ink);
  text-align: center;
  text-decoration: none;
  overflow: hidden;
  transform-style: preserve-3d;
  will-change: transform, box-shadow;
  transition:
    transform 0.5s cubic-bezier(0.165, 0.84, 0.44, 1),
    box-shadow 0.5s cubic-bezier(0.165, 0.84, 0.44, 1),
    border-color 0.4s ease;
}

.category-card:focus-visible {
  outline: 2px solid var(--gold);
  outline-offset: 4px;
}

/* ───────── تصویر و محفظه آن ───────── */

.category-image {
  position: relative;
  width: 100%;
  aspect-ratio: 1 / 1;
  overflow: hidden;
  border-radius: 18px;
  background: #f3efe9;
  margin-bottom: 14px;
  transform-style: preserve-3d;
  transition:
    transform 0.5s cubic-bezier(0.165, 0.84, 0.44, 1),
    box-shadow 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.category-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.65s cubic-bezier(0.165, 0.84, 0.44, 1);
}

/* هاله تیره نامحسوس پایین تصویر */
.category-image::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(23, 20, 15, 0.25) 0%, transparent 60%);
  opacity: 0;
  transition: opacity 0.4s ease;
  pointer-events: none;
}

/* عنوان و فلش هدایت */
.card-title-box {
  transform-style: preserve-3d;
  transition: transform 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.category-card h3 {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  margin: 0;
  padding: 0 4px;
  font-size: 14.5px;
  font-weight: 700;
  color: var(--ink);
  transition:
    color 0.3s ease,
    gap 0.3s ease;
}

.category-card h3::after {
  content: '←';
  max-width: 0;
  overflow: hidden;
  opacity: 0;
  color: var(--gold-deep);
  transform: translateX(4px);
  transition:
    max-width 0.3s ease,
    opacity 0.3s ease,
    transform 0.3s ease;
}

/* بازتاب نور شیشه‌ای روی کل کارت */
.card-glare {
  position: absolute;
  inset: -100%;
  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0) 35%,
    rgba(255, 255, 255, 0.55) 50%,
    rgba(255, 255, 255, 0) 65%
  );
  transform: rotate(30deg) translateY(-80%);
  transition: transform 0.85s cubic-bezier(0.2, 0.8, 0.2, 1);
  pointer-events: none;
  z-index: 10;
}

/* ───────── افکت هاور سه‌بعدی پیشرفته ───────── */

@media (hover: hover) and (pointer: fine) {
  .category-card:hover {
    /* بلند شدن و چرخش متعادل در دو محور */
    transform: translateY(-10px) rotateX(8deg) rotateY(-8deg) scale(1.02);
    border-color: rgba(199, 166, 106, 0.4);
    box-shadow:
      0 12px 24px -10px rgba(139, 118, 87, 0.25),
      -12px 28px 45px -14px rgba(23, 20, 15, 0.16),
      0 0 0 1px rgba(199, 166, 106, 0.15);
  }

  /* عکس با عمق مشخصی به سمت جلو می‌آید */
  .category-card:hover .category-image {
    transform: translateZ(32px);
    box-shadow: 0 14px 28px -10px rgba(23, 20, 15, 0.28);
  }

  .category-card:hover .category-image img {
    transform: scale(1.08);
  }

  .category-card:hover .category-image::after {
    opacity: 1;
  }

  /* عنوان بالاتر از صفحه و همراه با المان تصویر شناور می‌شود */
  .category-card:hover .card-title-box {
    transform: translateZ(24px);
  }

  .category-card:hover h3 {
    color: var(--gold-deep);
    gap: 6px;
  }

  .category-card:hover h3::after {
    max-width: 18px;
    opacity: 1;
    transform: translateX(0);
  }

  /* حرکت شیک بازتاب نور از گوشه کارت */
  .category-card:hover .card-glare {
    transform: rotate(30deg) translateY(80%);
  }
}

/* خاموش کردن ترنزیشن‌ها برای کاربرانی که reduced-motion دارند */
@media (prefers-reduced-motion: reduce) {
  .category-card,
  .category-image,
  .category-image img,
  .card-title-box,
  .card-glare {
    transition: none !important;
    transform: none !important;
  }
}

.image-placeholder {
  width: 100%;
  height: 100%;
  display: grid;
  place-items: center;
  color: #a39b8e;
  font-size: 12px;
}

/* پیام‌ها */
.message {
  max-width: 520px;
  margin: 0 auto;
  padding: 40px 20px;
  border: 1px solid rgba(235, 230, 222, 0.9);
  border-radius: 20px;
  background: #ffffff;
  box-shadow: 0 12px 30px -15px rgba(23, 20, 15, 0.08);
  text-align: center;
  color: #857e72;
}

.error {
  color: #c0392b;
}

/* ریسپانسیو تبلت */
@media (max-width: 1100px) {
  .categories-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
  }
}

/* ریسپانسیو موبایل */
@media (max-width: 600px) {
  .categories-section {
    width: 90%;
    margin: 45px auto;
  }

  .categories-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
  }

  .section-heading {
    margin-bottom: 24px;
  }

  .section-heading h2 {
    font-size: 21px;
  }

  .category-card {
    padding: 8px 8px 12px;
    border-radius: 20px;
  }

  .category-image {
    border-radius: 15px;
    margin-bottom: 10px;
  }

  .category-card h3 {
    font-size: 13px;
  }
}
</style>
