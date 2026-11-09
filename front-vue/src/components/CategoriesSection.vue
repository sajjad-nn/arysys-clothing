<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, Navigation } from 'swiper/modules'

import 'swiper/css'
import 'swiper/css/navigation'

import api from '../services/api'

const categories = ref([])
const loading = ref(true)
const error = ref(false)

const swiperModules = [Autoplay, Navigation]

const autoplayOptions = {
  delay: 3500,
  disableOnInteraction: false,
  pauseOnMouseEnter: true,
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

    <div v-else-if="categories.length" class="slider-container">
      <Swiper
        class="categories-slider"
        dir="rtl"
        :modules="swiperModules"
        :slides-per-view="1.5"
        :space-between="10"
        :navigation="true"
        :autoplay="autoplayOptions"
        :rewind="true"
        :breakpoints="{
          601: {
            slidesPerView: 3,
            spaceBetween: 12,
          },
          1101: {
            slidesPerView: 6,
            spaceBetween: 12,
          },
        }"
      >
        <SwiperSlide v-for="category in categories" :key="category.id" class="category-slide">
          <div class="card-wrapper">
            <RouterLink :to="`/shop?category=${category.slug}`" class="category-card">
              <!-- تصویر دسته‌بندی از دیتابیس -->

              <img
                v-if="category.image_url"
                class="card-cover"
                :src="category.image_url"
                :alt="category.name"
              />

              <div v-else class="image-placeholder">تصویر موجود نیست</div>

              <!-- گرادیان تیره -->

              <div class="card-overlay"></div>

              <!-- درخشش شیشه‌ای -->

              <div class="card-glare"></div>

              <!-- عنوان -->

              <div class="card-title-box">
                <h3>{{ category.name }}</h3>
              </div>
            </RouterLink>
          </div>
        </SwiperSlide>
      </Swiper>
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

/* عنوان بخش */

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

/* اسلایدر */

.slider-container {
  position: relative;
  padding: 12px 2px;
}

.categories-slider {
  overflow: visible;
}

:deep(.swiper-wrapper) {
  align-items: stretch;
}

:deep(.swiper-slide) {
  height: auto;
}

/* دکمه‌های قبلی و بعدی */

:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
  width: 38px;
  height: 38px;
  border-radius: 50%;

  background: #fff;
  color: var(--gold-deep);

  border: 1px solid rgba(199, 166, 106, 0.3);

  box-shadow: 0 5px 15px rgba(23, 20, 15, 0.12);

  transition:
    background 0.3s ease,
    color 0.3s ease,
    transform 0.3s ease;
}

:deep(.swiper-button-next::after),
:deep(.swiper-button-prev::after) {
  font-size: 14px;
  font-weight: 900;
}

:deep(.swiper-button-next:hover),
:deep(.swiper-button-prev:hover) {
  background: var(--gold);
  color: #fff;
  transform: scale(1.08);
}

/* کارت */

.card-wrapper {
  width: 100%;
  height: 100%;
  perspective: 1200px;
}

.category-card {
  position: relative;
  display: block;
  width: 100%;
  aspect-ratio: 3 / 4;
  min-width: 0;

  border-radius: 24px;
  background: #f3efe9;
  border: 1px solid rgba(235, 230, 222, 0.85);

  box-shadow:
    0 4px 6px -1px rgba(23, 20, 15, 0.03),
    0 10px 20px -8px rgba(23, 20, 15, 0.06);

  color: #fff;
  text-decoration: none;

  overflow: hidden;
  isolation: isolate;
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

/* تصویر */

.card-cover {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
  z-index: 0;

  transition:
    transform 0.65s cubic-bezier(0.165, 0.84, 0.44, 1),
    filter 0.4s ease;
}

/* گرادیان پایین کارت */

.card-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
  pointer-events: none;

  background: linear-gradient(
    to top,
    rgba(23, 20, 15, 0.78) 0%,
    rgba(23, 20, 15, 0.35) 32%,
    transparent 62%
  );
}

/* عنوان */

.card-title-box {
  position: absolute;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 3;
  padding: 16px 10px 18px;
  text-align: center;
}

.category-card h3 {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0;

  margin: 0;
  padding: 0 4px;

  font-size: 15px;
  font-weight: 700;
  color: #fff;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);

  transition:
    color 0.3s ease,
    gap 0.3s ease;
}

.category-card h3::after {
  content: '←';

  max-width: 0;
  overflow: hidden;
  opacity: 0;

  color: var(--gold-light);
  transform: translateX(4px);

  transition:
    max-width 0.3s ease,
    opacity 0.3s ease,
    transform 0.3s ease;
}

/* بازتاب نور */

.card-glare {
  position: absolute;
  inset: -100%;
  z-index: 2;
  pointer-events: none;

  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0) 35%,
    rgba(255, 255, 255, 0.45) 50%,
    rgba(255, 255, 255, 0) 65%
  );

  transform: rotate(30deg) translateY(-80%);
  transition: transform 0.85s cubic-bezier(0.2, 0.8, 0.2, 1);
}

/* هاور سه‌بعدی و بزرگ شدن کارت */

@media (hover: hover) and (pointer: fine) {
  .category-card:hover {
    transform: translateY(-10px) rotateX(8deg) rotateY(-8deg) scale(1.08);

    position: relative;
    z-index: 5;

    border-color: rgba(199, 166, 106, 0.4);

    box-shadow:
      0 12px 24px -10px rgba(139, 118, 87, 0.25),
      -12px 28px 45px -14px rgba(23, 20, 15, 0.16),
      0 0 0 1px rgba(199, 166, 106, 0.15);
  }

  .category-card:hover .card-cover {
    transform: scale(1.08);
  }

  .category-card:hover h3 {
    color: var(--gold-light);
    gap: 6px;
  }

  .category-card:hover h3::after {
    max-width: 18px;
    opacity: 1;
    transform: translateX(0);
  }

  .category-card:hover .card-glare {
    transform: rotate(30deg) translateY(80%);
  }

  /* بلور شدن تصاویر کارت‌های دیگر */

  .categories-slider:has(.category-card:hover) .category-card:not(:hover) .card-cover {
    filter: blur(3px);
    transform: scale(1.04);
  }
}

/* کاهش حرکت */

@media (prefers-reduced-motion: reduce) {
  .category-card,
  .card-cover,
  .card-glare {
    transition: none !important;
    transform: none !important;
  }
}

/* تصویر ناموجود */

.image-placeholder {
  position: absolute;
  inset: 0;
  z-index: 0;

  display: grid;
  place-items: center;

  background: #f3efe9;
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

/* تبلت */

@media (max-width: 1100px) {
  .categories-section {
    width: 90%;
  }
}

/* موبایل */

@media (max-width: 600px) {
  .categories-section {
    width: 90%;
    margin: 45px auto;
  }

  .section-heading {
    margin-bottom: 24px;
  }

  .section-heading h2 {
    font-size: 21px;
  }

  .category-card {
    border-radius: 20px;
  }

  .card-title-box {
    padding: 14px 6px 16px;
  }

  .category-card h3 {
    font-size: 13px;
  }

  :deep(.swiper-button-next),
  :deep(.swiper-button-prev) {
    width: 30px;
    height: 30px;
  }

  :deep(.swiper-button-next::after),
  :deep(.swiper-button-prev::after) {
    font-size: 11px;
  }
}
</style>
