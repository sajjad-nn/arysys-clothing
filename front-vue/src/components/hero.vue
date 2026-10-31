<template>
  <section class="hero-slider" dir="rtl">
    <div class="hero-content">
      <div class="hero-text">
        <span class="hero-label">ARYASYS COLLECTION</span>

        <div class="hero-heading-wrapper">
          <Transition name="text-swap" mode="out-in">
            <div :key="activeIndex" class="hero-heading">
              <h1 class="single-line-title">
                {{ slides[activeIndex].heading }}
                <span class="highlight-text">{{ slides[activeIndex].highlight }}</span>
              </h1>

              <p class="hero-desc">
                {{ slides[activeIndex].description }}
              </p>
            </div>
          </Transition>
        </div>

        <div class="hero-actions">
          <RouterLink to="/shop" class="shop-button">
            مشاهده محصولات
            <span class="button-arrow" aria-hidden="true">←</span>
          </RouterLink>

          <RouterLink to="/contact" class="contact-button">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path
                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.34 1.78.64 2.62a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.46-1.21a2 2 0 0 1 2.11-.45c.84.3 1.72.52 2.62.64A2 2 0 0 1 22 16.92z"
              />
            </svg>
            تماس با ما
          </RouterLink>
        </div>
      </div>

      <div class="carousel-wrapper">
        <button
          class="nav-button prev-button"
          type="button"
          aria-label="تصویر قبلی"
          @click="prevSlide"
        >
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M15 18l-6-6 6-6" />
          </svg>
        </button>

        <div class="carousel">
          <button
            v-for="(slide, index) in slides"
            :key="slide.id"
            type="button"
            class="slide-card"
            :class="getPosition(index)"
            :aria-label="`نمایش ${slide.title}`"
            :aria-current="index === activeIndex ? 'true' : undefined"
            @click="goToSlide(index)"
          >
            <img :src="slide.image" :alt="slide.title" />

            <div class="slide-caption">
              <span>{{ slide.subtitle }}</span>
              <h2>{{ slide.title }}</h2>
            </div>
          </button>
        </div>

        <button
          class="nav-button next-button"
          type="button"
          aria-label="تصویر بعدی"
          @click="nextSlide"
        >
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M9 18l6-6-6-6" />
          </svg>
        </button>
      </div>

      <div class="carousel-footer">
        <div class="slide-counter">
          <span>{{ String(activeIndex + 1).padStart(2, '0') }}</span>
          <span class="counter-line"></span>
          <span>{{ String(slides.length).padStart(2, '0') }}</span>
        </div>

        <div class="carousel-dots">
          <button
            v-for="(slide, index) in slides"
            :key="slide.id"
            type="button"
            :class="{ active: index === activeIndex }"
            :aria-label="`رفتن به اسلاید ${index + 1}`"
            :aria-pressed="index === activeIndex"
            @click="goToSlide(index)"
          />
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'

import womanImage from '../asset/images/woman.webp'
import menImage from '../asset/images/men.webp'
import modernImage from '../asset/images/modern.webp'

const slides = [
  {
    id: 1,
    image: womanImage,
    title: 'استایل زنانه',
    subtitle: 'WOMEN COLLECTION',
    heading: 'استایل خاص خودت را',
    highlight: 'انتخاب کن',
    description: 'جدیدترین ترندهای دنیای مد و پوشاک؛ استایلی متفاوت برای هر روز شما.',
  },
  {
    id: 2,
    image: menImage,
    title: 'استایل مردانه',
    subtitle: 'MEN COLLECTION',
    heading: 'اعتماد به نفس را',
    highlight: 'بپوش',
    description: 'کالکشن مردانه با برش‌های مدرن؛ برای هر موقعیت، از محیط کار تا دورهمی.',
  },
  {
    id: 3,
    image: modernImage,
    title: 'مدرن و متفاوت',
    subtitle: 'MODERN STYLE',
    heading: 'مرز ترند و کلاسیک را',
    highlight: 'جابه‌جا کن',
    description: 'طرح‌های امروزی و متفاوت برای کسانی که دوست دارند دیده شوند.',
  },
]

const activeIndex = ref(0)
let autoplayTimer

function nextSlide() {
  activeIndex.value = (activeIndex.value + 1) % slides.length
}

function prevSlide() {
  activeIndex.value = (activeIndex.value - 1 + slides.length) % slides.length
}

function goToSlide(index) {
  activeIndex.value = index
}

function getPosition(index) {
  if (index === activeIndex.value) return 'center'

  if (index === (activeIndex.value + 1) % slides.length) {
    return 'right'
  }

  return 'left'
}

onMounted(() => {
  autoplayTimer = setInterval(nextSlide, 5000)
})

onUnmounted(() => {
  clearInterval(autoplayTimer)
})
</script>

<style scoped>
.hero-slider {
  position: relative;
  width: 100%;
  min-height: 750px;
  overflow: hidden;
  background:
    radial-gradient(ellipse at 70% 45%, rgba(88, 88, 88, 0.2) 0%, transparent 48%), #080808;
  color: #fff;
  display: flex;
  align-items: center;
  padding: 60px 6%;
  box-sizing: border-box;
}

.hero-content {
  width: 100%;
  max-width: 1440px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 0.85fr 1.15fr;
  align-items: center;
  gap: 36px;
}

.hero-text {
  position: relative;
  z-index: 2;
  text-align: right;
  padding: 20px 0;
  display: flex;
  flex-direction: column;
}

.hero-label {
  display: inline-block;
  color: #c7a66a;
  font-size: 11.5px;
  font-weight: 700;
  letter-spacing: 3.5px;
  margin-bottom: 20px;
  direction: ltr;
  text-transform: uppercase;
}

.hero-heading-wrapper {
  min-height: 190px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

.hero-heading {
  width: 100%;
}

.single-line-title {
  font-size: clamp(24px, 2.7vw, 42px);
  line-height: 1.4;
  font-weight: 800;
  margin: 0 0 16px;
  white-space: nowrap;
  letter-spacing: -0.5px;
}

.highlight-text {
  display: inline;
  margin-right: 8px;
  background: linear-gradient(135deg, #f3deb5 0%, #c7a66a 60%, #b08d52 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-desc {
  color: #a3a3a3;
  font-size: 15px;
  line-height: 2;
  max-width: 440px;
  margin: 0;
}

.text-swap-enter-active,
.text-swap-leave-active {
  transition:
    opacity 0.3s ease,
    transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.text-swap-enter-from {
  opacity: 0;
  transform: translateY(12px);
}

.text-swap-leave-to {
  opacity: 0;
  transform: translateY(-12px);
}

.hero-actions {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 14px;
  margin-top: 28px;
}

.shop-button,
.contact-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 14px;
  height: 52px;
  box-sizing: border-box;
  border-radius: 14px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 700;
  white-space: nowrap;
  transition:
    transform 0.25s ease,
    box-shadow 0.25s ease,
    background 0.25s ease,
    border-color 0.25s ease,
    color 0.25s ease;
}

.shop-button {
  padding: 0 10px 0 22px;
  color: #17130b;
  background: linear-gradient(135deg, #e0c48c 0%, #c7a66a 55%, #b08d52 100%);
  border: 1px solid rgba(255, 255, 255, 0.25);
  box-shadow:
    0 10px 24px -8px rgba(199, 166, 106, 0.65),
    inset 0 1px 0 rgba(255, 255, 255, 0.4);
}

.shop-button:hover {
  transform: translateY(-3px);
  box-shadow:
    0 16px 32px -8px rgba(199, 166, 106, 0.8),
    inset 0 1px 0 rgba(255, 255, 255, 0.5);
}

.shop-button:active {
  transform: translateY(0);
}

.button-arrow {
  width: 34px;
  height: 34px;
  display: grid;
  place-items: center;
  border-radius: 10px;
  background: rgba(0, 0, 0, 0.14);
  font-size: 16px;
  transition:
    transform 0.25s ease,
    background 0.25s ease;
}

.shop-button:hover .button-arrow {
  transform: translateX(-4px);
  background: rgba(0, 0, 0, 0.22);
}

.contact-button {
  padding: 0 24px;
  color: #fff;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.16);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
}

.contact-button svg {
  width: 18px;
  height: 18px;
  fill: none;
  stroke: currentColor;
  stroke-width: 1.8;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.contact-button:hover {
  transform: translateY(-3px);
  color: #e0c48c;
  background: rgba(199, 166, 106, 0.1);
  border-color: rgba(199, 166, 106, 0.55);
}

.contact-button:active {
  transform: translateY(0);
}

.shop-button:focus-visible,
.contact-button:focus-visible {
  outline: 2px solid #e0c48c;
  outline-offset: 3px;
}

.carousel-wrapper {
  position: relative;
  min-width: 0;
  height: 545px;
  display: flex;
  align-items: center;
  justify-content: center;
  direction: ltr;
}

.carousel {
  position: relative;
  width: 100%;
  height: 100%;
  perspective: 1200px;
}

.slide-card {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 48%;
  height: 88%;
  padding: 0;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 6px;
  overflow: hidden;
  background: #171717;
  cursor: pointer;
  transform-origin: center center;
  transition:
    transform 0.65s cubic-bezier(0.2, 0.7, 0.2, 1),
    opacity 0.65s ease,
    filter 0.65s ease,
    z-index 0.65s ease;
}

.slide-card img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.slide-card.center {
  z-index: 3;
  opacity: 1;
  filter: grayscale(0%);
  transform: translate(-50%, -50%) scale(1);
  box-shadow: 0 25px 70px rgba(0, 0, 0, 0.55);
  cursor: default;
}

.slide-card.left {
  z-index: 2;
  opacity: 0.65;
  filter: grayscale(85%) brightness(0.65);
  transform: translate(-115%, -50%) scale(0.77) rotateY(10deg);
}

.slide-card.right {
  z-index: 2;
  opacity: 0.65;
  filter: grayscale(85%) brightness(0.65);
  transform: translate(15%, -50%) scale(0.77) rotateY(-10deg);
}

.slide-caption {
  position: absolute;
  inset: auto 0 0;
  padding: 50px 18px 24px;
  color: #fff;
  text-align: center;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.88));
}

.slide-caption span {
  display: block;
  color: #d6bc8c;
  font-size: 10px;
  letter-spacing: 2px;
  margin-bottom: 8px;
}

.slide-caption h2 {
  margin: 0;
  font-size: clamp(17px, 2vw, 24px);
  font-weight: 700;
}

.nav-button {
  position: absolute;
  z-index: 5;
  top: 50%;
  width: 44px;
  height: 44px;
  padding: 0;
  transform: translateY(-50%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  background: rgba(20, 20, 20, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 50%;
  cursor: pointer;
  transition:
    background 0.2s ease,
    color 0.2s ease,
    transform 0.2s ease;
}

.nav-button:hover {
  color: #080808;
  background: #c7a66a;
  transform: translateY(-50%) scale(1.05);
}

.prev-button {
  left: 0;
}

.next-button {
  right: 0;
}

.nav-button svg {
  width: 20px;
  height: 20px;
  fill: none;
  stroke: currentColor;
  stroke-width: 1.8;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.carousel-footer {
  grid-column: 2;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 30px 0;
  direction: ltr;
}

.slide-counter {
  display: flex;
  align-items: center;
  gap: 12px;
  color: #777;
  font-size: 12px;
}

.slide-counter span:first-child {
  color: #c7a66a;
  font-weight: 700;
}

.counter-line {
  width: 35px;
  height: 1px;
  background: #555;
}

.carousel-dots {
  display: flex;
  align-items: center;
  gap: 8px;
}

.carousel-dots button {
  width: 7px;
  height: 7px;
  padding: 0;
  border: 0;
  border-radius: 50%;
  background: #555;
  cursor: pointer;
  transition:
    width 0.25s ease,
    background 0.25s ease;
}

.carousel-dots button.active {
  width: 24px;
  border-radius: 10px;
  background: #c7a66a;
}

/* نمایشگرهای بزرگ دسکتاپ */
@media (min-width: 1500px) {
  .hero-slider {
    min-height: 830px;
  }

  .carousel-wrapper {
    height: 610px;
  }
}

/* تبلت و صفحات متوسط */
@media (max-width: 1024px) {
  .single-line-title {
    white-space: normal;
  }
}

/* تبلت و موبایل */
@media (max-width: 900px) {
  .hero-slider {
    min-height: auto;
    padding: 60px 24px 44px;
  }

  .hero-content {
    grid-template-columns: 1fr;
    gap: 32px;
  }

  .hero-text {
    text-align: center;
    padding: 0 12px;
  }

  /* فضای یکسان برای عنوان و توضیحات */
  .hero-heading-wrapper {
    min-height: 190px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    margin-bottom: 0;
  }

  .hero-heading {
    width: 100%;
  }

  .single-line-title {
    min-height: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    align-content: center;
    font-size: clamp(23px, 5.5vw, 36px);
    line-height: 1.5;
    white-space: normal;
    margin: 0 0 12px;
  }

  .hero-desc {
    min-height: 64px;
    max-width: 480px;
    margin: 0 auto;
    font-size: 14px;
    line-height: 1.9;
    display: flex;
    align-items: flex-start;
    justify-content: center;
  }

  .hero-actions {
    justify-content: center;
    align-items: center;
    margin-top: 20px;
  }

  .carousel-wrapper {
    width: 100%;
    height: clamp(330px, 60vw, 420px);
    padding: 10px 0;
  }

  .carousel-footer {
    grid-column: 1;
    width: 100%;
    padding: 16px 12px 0;
  }
}

/* موبایل */
@media (max-width: 560px) {
  .hero-slider {
    padding: 48px 18px 36px;
  }

  .hero-text {
    padding: 0 6px;
  }

  .hero-label {
    font-size: 10px;
    letter-spacing: 2.5px;
    margin-bottom: 14px;
  }

  .hero-heading-wrapper {
    min-height: 190px;
  }

  .single-line-title {
    min-height: 90px;
    font-size: clamp(21px, 6.2vw, 28px);
    line-height: 1.5;
    margin-bottom: 10px;
  }

  .hero-desc {
    min-height: 64px;
    font-size: 13px;
    line-height: 1.85;
    padding: 0 4px;
  }

  .hero-actions {
    justify-content: center;
    align-items: center;
    flex-wrap: nowrap;
    gap: 10px;
    margin-top: 18px;
  }

  .shop-button,
  .contact-button {
    height: 48px;
    font-size: 12.5px;
  }

  .carousel-wrapper {
    height: 320px;
    margin: 10px 0;
  }

  .slide-card {
    width: 62%;
    height: 84%;
  }

  .slide-card.left {
    transform: translate(-85%, -50%) scale(0.72);
  }

  .slide-card.right {
    transform: translate(-15%, -50%) scale(0.72);
  }

  .nav-button {
    width: 36px;
    height: 36px;
  }

  .prev-button {
    left: 2px;
  }

  .next-button {
    right: 2px;
  }
}

@media (prefers-reduced-motion: reduce) {
  .slide-card,
  .shop-button,
  .contact-button,
  .button-arrow,
  .nav-button,
  .carousel-dots button,
  .text-swap-enter-active,
  .text-swap-leave-active {
    transition: none;
  }
}
</style>
