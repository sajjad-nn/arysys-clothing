
<template>
  <section class="articles-section" dir="rtl">

    <div class="section-header">
      <div class="header-titles">
        <h2>آخرین مقالات</h2>
        <p>تازه‌ترین مطالب دنیای مد و پوشاک</p>
      </div>

      <RouterLink to="/articles" class="view-all">
        <span>مشاهده همه</span>
        <span class="arrow-icon">←</span>
      </RouterLink>
    </div>

    <div v-if="loading" class="message">
      <span class="loader"></span>
      <p>در حال دریافت مقالات...</p>
    </div>

    <div v-else-if="error" class="message error">
      <p>دریافت مقالات با خطا مواجه شد.</p>
      <button class="retry-btn" @click="fetchArticles">
        تلاش مجدد
      </button>
    </div>

    <div v-else-if="articles.length === 0" class="message">
      مقاله‌ای برای نمایش وجود ندارد.
    </div>

    <Swiper
      v-else
      :modules="[Navigation]"
      :slides-per-view="1.3"
      :space-between="14"
      :navigation="true"
      :breakpoints="{
        520: {
          slidesPerView: 2.2,
          spaceBetween: 16,
        },
        768: {
          slidesPerView: 3.2,
          spaceBetween: 18,
        },
        1024: {
          slidesPerView: 4.2,
          spaceBetween: 20,
        },
        1280: {
          slidesPerView: 5,
          spaceBetween: 22,
        },
      }"
      class="articles-swiper"
    >

      <SwiperSlide
        v-for="article in articles"
        :key="article.id"
      >
        <article class="article-card">

          <!-- تصویر مقاله -->
          <RouterLink
            :to="`/articles/${article.slug}`"
            class="article-image"
          >
            <img
              v-if="article.image_url"
              :src="article.image_url"
              :alt="article.title"
              loading="lazy"
              @error="onImageError($event)"
            />

            <div v-else class="no-image">
              <span>ARYSYS</span>
              <small>فاقد تصویر</small>
            </div>
          </RouterLink>

          <!-- محتوای مقاله -->
          <div class="article-info">

            <span class="category">
              {{ article.category?.name || 'مجله مد' }}
            </span>

            <h3 :title="article.title">
              <RouterLink :to="`/articles/${article.slug}`">
                {{ article.title }}
              </RouterLink>
            </h3>

            <p class="article-excerpt">
              {{ article.excerpt || 'برای مطالعه ادامه این مقاله وارد صفحه آن شوید.' }}
            </p>

            <!-- دکمه در پایین کارت -->
            <RouterLink
              :to="`/articles/${article.slug}`"
              class="article-button"
            >
              <span>ادامه مطلب</span>
              <span class="btn-arrow">←</span>
            </RouterLink>

          </div>

        </article>
      </SwiperSlide>

    </Swiper>

  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation } from 'swiper/modules'

import 'swiper/css'
import 'swiper/css/navigation'

import articleService from '../services/articleService'

const articles = ref([])
const loading = ref(true)
const error = ref(false)

async function fetchArticles() {
  try {
    loading.value = true
    error.value = false

    const response = await articleService.getArticles()

    articles.value = response.data || []

  } catch (err) {
    console.error('Latest articles error:', err)
    error.value = true
  } finally {
    loading.value = false
  }
}

function onImageError(event) {
  event.target.style.display = 'none'
}

onMounted(fetchArticles)
</script>

<style scoped>
.articles-section {
  --gold: #c7a66a;
  --gold-light: #e0c48c;
  --gold-deep: #8b7657;
  --ink: #17140f;

  width: 82%;
  max-width: 1360px;
  margin: 70px auto;
  color: var(--ink);
  direction: rtl;
  font-family: inherit;
}

/* هدر بخش */

.section-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 34px;
}

.header-titles h2 {
  position: relative;
  display: inline-block;
  margin: 0 0 10px;
  padding-bottom: 12px;
  font-size: 26px;
  font-weight: 800;
  color: var(--ink);
  letter-spacing: -0.5px;
}

.header-titles h2::after {
  content: '';
  position: absolute;
  right: 0;
  bottom: 0;
  width: 44px;
  height: 3px;
  border-radius: 3px;
  background: linear-gradient(90deg, var(--gold-light), var(--gold));
}

.header-titles p {
  margin: 0;
  font-size: 14px;
  color: #857e72;
}

.view-all {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--gold-deep);
  text-decoration: none;
  font-size: 13.5px;
  font-weight: 700;
  padding: 8px 14px;
  border-radius: 10px;
  background: rgba(199, 166, 106, 0.08);
  border: 1px solid rgba(199, 166, 106, 0.2);
  transition: all 0.25s ease;
  white-space: nowrap;
}

.view-all:hover {
  background: var(--gold-deep);
  color: #fff;
}

.view-all .arrow-icon {
  transition: transform 0.25s ease;
}

.view-all:hover .arrow-icon {
  transform: translateX(-4px);
}

/* اسلایدر و تراز ارتفاع کارت‌ها */

.articles-swiper {
  width: 100%;
  padding: 10px 4px 40px;
}

:deep(.swiper-wrapper) {
  display: flex;
  align-items: stretch;
}

:deep(.swiper-slide) {
  height: auto;
  display: flex;
}

/* کارت مقاله */

.article-card {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
  background: #fff;
  border-radius: 20px;
  border: 1px solid #f0f0f2;
  box-shadow: 0 4px 18px -4px rgba(23, 20, 15, 0.05);
  overflow: hidden;
  transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
  box-sizing: border-box;
}

.article-card:hover {
  transform: translateY(-6px);
  border-color: rgba(199, 166, 106, 0.4);
  box-shadow:
    0 16px 32px -10px rgba(139, 118, 87, 0.2),
    0 4px 12px rgba(23, 20, 15, 0.04);
}

/* تصویر با نسبت مشابه کارت محصولات */

.article-image {
  position: relative;
  display: block;
  width: 100%;
  aspect-ratio: 1 / 1.15;
  flex-shrink: 0;
  overflow: hidden;
  background: #f4f4f5;
}

.article-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.article-card:hover .article-image img {
  transform: scale(1.07);
}

.no-image {
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #a1a1aa;
}

.no-image span {
  letter-spacing: 4px;
  font-size: 17px;
}

.no-image small {
  font-size: 11px;
}

/* محتوای کارت */

.article-info {
  display: flex;
  flex-direction: column;
  flex: 1;
  padding: 16px 14px 14px;
  box-sizing: border-box;
}

.category {
  display: block;
  font-size: 11px;
  font-weight: 600;
  color: var(--gold-deep);
  margin-bottom: 6px;
}

/* عنوان حداکثر دو خط */

.article-info h3 {
  font-size: 14px;
  font-weight: 700;
  line-height: 1.5;
  margin: 0 0 10px;
  height: 42px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.article-info h3 a {
  color: var(--ink);
  text-decoration: none;
  transition: color 0.2s ease;
}

.article-info h3 a:hover {
  color: var(--gold-deep);
}

/* توضیحات حداکثر دو خط */

.article-excerpt {
  font-size: 11px;
  line-height: 1.9;
  color: #85858b;
  margin: 0;
  height: 42px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* دکمه در انتهای تمام کارت‌ها */

.article-button {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: auto;
  padding: 10px;
  box-sizing: border-box;
  border-radius: 10px;
  background: #18181b;
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.25s ease;
}

.article-button:hover {
  background: var(--gold-deep);
  transform: translateY(-2px);
  box-shadow: 0 6px 14px -3px rgba(139, 118, 87, 0.4);
}

.btn-arrow {
  transition: transform 0.25s ease;
}

.article-button:hover .btn-arrow {
  transform: translateX(-4px);
}

/* پیام‌ها */

.message {
  text-align: center;
  padding: 50px 20px;
  color: #857e72;
  font-size: 14px;
  background: #fff;
  border-radius: 20px;
  border: 1px solid #f0f0f2;
}

.error {
  color: #dc2626;
}

.retry-btn {
  margin-top: 12px;
  padding: 8px 18px;
  background: #18181b;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-family: inherit;
  font-size: 12px;
}

.loader {
  display: inline-block;
  width: 28px;
  height: 28px;
  border: 3px solid #e4e4e7;
  border-top-color: var(--gold-deep);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 10px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* فلش‌های Swiper */

:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(228, 228, 231, 0.9);
  color: #18181b;
  box-shadow: 0 6px 18px -4px rgba(0, 0, 0, 0.12);
  transition: all 0.25s ease;
}

:deep(.swiper-button-next:hover),
:deep(.swiper-button-prev:hover) {
  background: var(--gold-deep);
  color: #fff;
  border-color: var(--gold-deep);
  transform: scale(1.05);
}

:deep(.swiper-button-next::after),
:deep(.swiper-button-prev::after) {
  font-size: 14px;
  font-weight: 700;
}

/* ریسپانسیو */

@media (max-width: 1024px) {
  .articles-section {
    width: 90%;
    margin: 55px auto;
  }
}

@media (max-width: 768px) {
  .articles-section {
    width: 92%;
    margin: 45px auto;
  }

  .section-header {
    margin-bottom: 24px;
  }

  .header-titles h2 {
    font-size: 20px;
  }

  .header-titles p {
    font-size: 12px;
  }

  .view-all {
    font-size: 12px;
    padding: 6px 10px;
  }

  .article-info {
    padding: 12px;
  }

  .article-info h3 {
    font-size: 13px;
    height: 38px;
  }

  .article-excerpt {
    font-size: 11px;
  }

  :deep(.swiper-button-next),
  :deep(.swiper-button-prev) {
    display: none;
  }
}
</style>
