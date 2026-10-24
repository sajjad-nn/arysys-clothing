```vue
<template>
  <section class="hero-section">
    <Swiper
      :modules="modules"
      :slides-per-view="1"
      :loop="true"
      effect="fade"
      :fade-effect="{ crossFade: true }"
      :autoplay="{
        delay: 5000,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
      }"
      :navigation="true"
      :pagination="{ clickable: true }"
      class="hero-swiper"
    >
      <SwiperSlide v-for="slide in slides" :key="slide.id">
        <section
          class="hero-slide"
          :style="{
            backgroundImage: `url('${slide.image}')`,
            backgroundPosition: slide.position || '55% center',
          }"
        >
          <div class="hero-overlay"></div>

          <div class="hero-content">
            <h1 class="hero-title">
              <span>{{ slide.highlight }}</span>
              {{ slide.title }}
            </h1>

            <p class="hero-description">
              {{ slide.description }}
            </p>

            <div class="hero-buttons">
              <RouterLink :to="slide.link1" class="follow-btn">
                {{ slide.button1 }}
              </RouterLink>

              <RouterLink :to="slide.link2" class="btn1">
                {{ slide.button2 }}
              </RouterLink>
            </div>
          </div>
        </section>
      </SwiperSlide>
    </Swiper>
  </section>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue'
import {
  Autoplay,
  Navigation,
  Pagination,
  EffectFade,
} from 'swiper/modules'

import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'swiper/css/effect-fade'

import woman from '../asset/images/woman.webp'
import men from '../asset/images/men.webp'
import modern from '../asset/images/modern.webp'

const modules = [Autoplay, Navigation, Pagination, EffectFade]

const slides = [
  {
    id: 1,
    image: woman,
    position: '60% center',
    highlight: 'کالکشن زنانه',
    title: 'زیبایی در انتخاب توست',
    description:
      'جدیدترین مدل‌های لباس زنانه را کشف کن و استایل مورد علاقه‌ات را بساز.',
    button1: 'مشاهده محصولات',
    button2: 'درباره ما',
    link1: '/shop',
    link2: '/about',
  },
  {
    id: 2,
    image: men,
    position: '55% center',
    highlight: 'کالکشن مردانه',
    title: 'استایل متفاوت خودت را بساز',
    description:
      'لباس‌های مردانه با طراحی مدرن و جذاب برای استایل روزمره و خاص.',
    button1: 'مشاهده محصولات',
    button2: 'درباره ما',
    link1: '/shop',
    link2: '/about',
  },
  {
    id: 3,
    image: modern,
    position: '50% center',
    highlight: 'استایل مدرن',
    title: 'متفاوت لباس بپوش',
    description:
      'مدل‌های جدید را ببین و انتخابی متناسب با سلیقه خودت داشته باش.',
    button1: 'کالکشن جدید',
    button2: 'درباره ما',
    link1: '/shop',
    link2: '/about',
  },
]
</script>

<style scoped>
.hero-section {
  width: 100%;
  overflow: hidden;
}

.hero-swiper {
  width: 100%;
}

/* تصویر تمام صفحه */
.hero-slide {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  min-height: 100vh;

  background-color: #171717;
  background-repeat: no-repeat;
  background-size: cover;
}
/* لایه تیره روی تصویر */
.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    90deg,
    rgba(0, 0, 0, 0.42) 0%,
    rgba(0, 0, 0, 0.18) 55%,
    rgba(0, 0, 0, 0.06) 100%
  );
}

/* پنل شیشه‌ای متن */
.hero-content {
  position: relative;
  z-index: 1;

  width: min(90%, 720px);
  margin: 0;
  padding: 36px;

  color: white;
  text-align: center;

  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 24px;

  -webkit-backdrop-filter: blur(16px);
  backdrop-filter: blur(16px);

  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}
/* عنوان */
.hero-title {
  margin-bottom: 24px;
  font-size: clamp(2rem, 4vw, 4rem);
  font-weight: 800;
  line-height: 1.5;
}

.hero-title span {
  display: block;
  margin-bottom: 8px;
  color: #4ade80;
}

/* توضیحات */
.hero-description {
  max-width: 600px;
  margin-bottom: 32px;

  color: #e5e7eb;
  font-size: 1rem;
  line-height: 2;
}

/* دکمه‌ها */
.hero-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
}

.follow-btn,
.btn1 {
  position: relative;
  display: inline-flex;
  flex: 1;
  min-width: 140px;
  align-items: center;
  justify-content: center;

  overflow: hidden;
  padding: 12px 20px;

  border: 1px solid white;
  border-radius: 16px;

  color: white;
  font-weight: 600;
  text-align: center;
  text-decoration: none;

  transition:
    background 0.3s ease,
    border-color 0.3s ease;
}

.follow-btn {
  background: #f43f5e;
  border-color: #f43f5e;
}

.follow-btn:hover {
  background: #be123c;
  border-color: #be123c;
}

/* دکمه دوم با افکت هاور */
.btn1 {
  isolation: isolate;
  background: transparent;
}

.btn1::before {
  content: '';
  position: absolute;
  inset: 0;
  z-index: -1;

  background: #dc2626;
  transform: scaleX(0);
  transform-origin: left;

  transition: transform 0.5s ease;
}

.btn1:hover::before {
  transform: scaleX(1);
}

/* فلش‌های اسلایدر */
.hero-swiper :deep(.swiper-button-prev),
.hero-swiper :deep(.swiper-button-next) {
  color: white;
}

.hero-swiper :deep(.swiper-button-prev)::after,
.hero-swiper :deep(.swiper-button-next)::after {
  font-size: 24px;
}

/* نقطه‌های پایین */
.hero-swiper :deep(.swiper-pagination) {
  bottom: 24px;
}

.hero-swiper :deep(.swiper-pagination-bullet) {
  width: 9px;
  height: 9px;

  background: white;
  opacity: 0.55;

  transition: all 0.3s ease;
}

.hero-swiper :deep(.swiper-pagination-bullet-active) {
  width: 28px;
  border-radius: 8px;

  background: #4ade80;
  opacity: 1;
}

/* موبایل و تبلت */
@media (max-width: 767px) {
  .hero-slide {
    min-height: 85vh;
  }

  .hero-overlay {
    background: rgba(0, 0, 0, 0.4);
  }

  .hero-content {
    width: calc(100% - 32px);
    margin: 0 16px;
    padding: 24px 18px;

    border-radius: 20px;
    text-align: center;
  }

  .hero-title {
    font-size: 2rem;
  }

  .hero-description {
    margin-right: auto;
    margin-left: auto;
    font-size: 0.9rem;
  }

  .hero-buttons {
    justify-content: center;
  }

  .hero-swiper :deep(.swiper-button-prev),
  .hero-swiper :deep(.swiper-button-next) {
    display: none;
  }
}

/* کاهش انیمیشن برای کاربرانی که این گزینه را فعال کرده‌اند */
@media (prefers-reduced-motion: reduce) {
  .follow-btn,
  .btn1,
  .hero-swiper :deep(.swiper-pagination-bullet) {
    transition: none;
  }
}
</style>
```