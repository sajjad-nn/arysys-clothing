
<template>
  <section class="hero-slider" dir="rtl">
    <div class="hero-content">
      <div class="hero-text">
        <span class="hero-label">ARYASYS COLLECTION</span>

        <h1>
          استایل خاص خودت را
          <span>انتخاب کن</span>
        </h1>

        <p>
          جدیدترین ترندهای دنیای مد و پوشاک؛
          استایلی متفاوت برای هر روز شما.
        </p>

        <RouterLink to="/shop" class="shop-button">
          مشاهده محصولات
          <span aria-hidden="true">←</span>
        </RouterLink>
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

// مسیرها با توجه به محل فایل:
// src/components/hero/HeroSlider.vue

import womanImage from '../asset/images/woman.webp'
import menImage from '../asset/images/men.webp'
import modernImage from '../asset/images/modern.webp'


const slides = [
  {
    id: 1,
    image: womanImage,
    title: 'استایل زنانه',
    subtitle: 'WOMEN COLLECTION',
  },
  {
    id: 2,
    image: menImage,
    title: 'استایل مردانه',
    subtitle: 'MEN COLLECTION',
  },
  {
    id: 3,
    image: modernImage,
    title: 'مدرن و متفاوت',
    subtitle: 'MODERN STYLE',
  },
]

const activeIndex = ref(0)

let autoplayTimer

function nextSlide() {
  activeIndex.value = (activeIndex.value + 1) % slides.length
}

function prevSlide() {
  activeIndex.value =
    (activeIndex.value - 1 + slides.length) % slides.length
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
  min-height: 650px;
  overflow: hidden;
  background:
    radial-gradient(
      ellipse at 70% 45%,
      rgba(88, 88, 88, 0.2) 0%,
      transparent 48%
    ),
    #080808;
  color: #fff;
  display: flex;
  align-items: center;
  padding: 55px 6%;
  box-sizing: border-box;
}

.hero-content {
  width: 100%;
  max-width: 1440px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 0.8fr 1.2fr;
  align-items: center;
  gap: 25px;
}

.hero-text {
  position: relative;
  z-index: 2;
  text-align: right;
  padding: 25px 0;
}

.hero-label {
  display: inline-block;
  color: #c7a66a;
  font-size: 12px;
  letter-spacing: 3px;
  margin-bottom: 24px;
  direction: ltr;
}

.hero-text h1 {
  font-size: clamp(32px, 4vw, 58px);
  line-height: 1.6;
  font-weight: 800;
  margin: 0 0 18px;
}

.hero-text h1 span {
  display: block;
  color: #c7a66a;
}

.hero-text p {
  color: #b7b7b7;
  font-size: 15px;
  line-height: 2.2;
  max-width: 410px;
  margin: 0 0 30px;
}

.shop-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 20px;
  padding: 14px 25px;
  color: #111;
  background: #c7a66a;
  text-decoration: none;
  font-size: 14px;
  font-weight: 700;
  border: 1px solid #c7a66a;
  border-radius: 5px;
  transition: background 0.25s ease, color 0.25s ease;
}

.shop-button:hover {
  background: transparent;
  color: #c7a66a;
}

.carousel-wrapper {
  position: relative;
  min-width: 0;
  height: 475px;
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
  border-radius: 4px;
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
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.45);
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
  padding: 45px 18px 22px;
  color: #fff;
  text-align: center;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.85));
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
  font-size: clamp(17px, 2vw, 25px);
  font-weight: 700;
}

.slide-card.left .slide-caption,
.slide-card.right .slide-caption {
  opacity: 0.8;
}

.nav-button {
  position: absolute;
  z-index: 5;
  top: 50%;
  width: 42px;
  height: 42px;
  padding: 0;
  transform: translateY(-50%);
  display: grid;
  place-items: center;
  color: #fff;
  background: rgba(20, 20, 20, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 50%;
  font-size: 30px;
  line-height: 1;
  cursor: pointer;
  transition: background 0.2s ease, color 0.2s ease;
}

.nav-button:hover {
  color: #080808;
  background: #c7a66a;
}

.prev-button {
  left: 0;
}

.next-button {
  right: 0;
}

.carousel-footer {
  grid-column: 2;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 5px 30px 0;
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
  transition: width 0.25s ease, background 0.25s ease;
}

.carousel-dots button.active {
  width: 24px;
  border-radius: 10px;
  background: #c7a66a;
}

@media (min-width: 1500px) {
  .hero-slider {
    min-height: 720px;
  }

  .carousel-wrapper {
    height: 530px;
  }
}


@media (max-width: 900px) {
  .hero-slider {
    min-height: auto;
    padding: 42px 20px 32px;
  }

  .hero-content {
    grid-template-columns: minmax(0, 1fr);
    gap: 12px;
    width: 100%;
  }

  .hero-text {
    text-align: center;
    padding: 0;
    min-width: 0;
  }

  .hero-text h1 {
    font-size: clamp(28px, 5.5vw, 42px);
    line-height: 1.55;
    margin-bottom: 12px;
  }

  .hero-text p {
    max-width: 360px;
    margin: 0 auto 22px;
    font-size: 14px;
    line-height: 1.9;
  }

  .carousel-wrapper {
    width: 100%;
    height: clamp(320px, 58vw, 420px);
    min-width: 0;
    overflow: hidden;
  }

  .carousel {
    width: 100%;
    min-width: 0;
  }

  .slide-card {
    width: 48%;
    height: 84%;
  }

  .slide-card.left {
    transform: translate(-100%, -50%) scale(0.76);
  }

  .slide-card.right {
    transform: translate(0%, -50%) scale(0.76);
  }

  .prev-button {
    left: 4px;
  }

  .next-button {
    right: 4px;
  }

  .carousel-footer {
    grid-column: 1;
    width: 100%;
    box-sizing: border-box;
    padding: 12px 8px 0;
  }
}

@media (max-width: 560px) {
  .hero-slider {
    padding: 32px 12px 26px;
  }

  .hero-content {
    gap: 8px;
  }

  .hero-label {
    font-size: 9px;
    letter-spacing: 2px;
    margin-bottom: 10px;
  }

  .hero-text h1 {
    font-size: clamp(25px, 7vw, 32px);
    line-height: 1.6;
    margin-bottom: 10px;
  }

  .hero-text p {
    max-width: 310px;
    font-size: 12px;
    line-height: 1.9;
    margin-bottom: 18px;
  }

  .shop-button {
    padding: 11px 18px;
    gap: 12px;
    font-size: 12px;
  }

  .carousel-wrapper {
    height: clamp(280px, 82vw, 340px);
    margin: 0 auto;
  }

  .slide-card {
    width: 58%;
    height: 82%;
  }

  .slide-card.center {
    transform: translate(-50%, -50%) scale(1);
  }

  .slide-card.left {
    transform: translate(-83%, -50%) scale(0.72);
  }

  .slide-card.right {
    transform: translate(-17%, -50%) scale(0.72);
  }

  .slide-caption {
    padding: 32px 8px 13px;
  }

  .slide-caption h2 {
    font-size: 15px;
  }

  .slide-caption span {
    font-size: 8px;
    letter-spacing: 1px;
  }

  .nav-button {
    width: 32px;
    height: 32px;
    font-size: 23px;
  }

  .prev-button {
    left: 2px;
  }

  .next-button {
    right: 2px;
  }

  .carousel-footer {
    padding: 8px 6px 0;
  }

  .slide-counter {
    gap: 8px;
    font-size: 11px;
  }

  .counter-line {
    width: 24px;
  }

  .carousel-dots {
    gap: 6px;
  }
}

@media (max-width: 360px) {
  .hero-slider {
    padding-right: 8px;
    padding-left: 8px;
  }

  .carousel-wrapper {
    height: 270px;
  }

  .slide-card {
    width: 60%;
  }

  .slide-card.left {
    transform: translate(-76%, -50%) scale(0.68);
  }

  .slide-card.right {
    transform: translate(-24%, -50%) scale(0.68);
  }

  .nav-button {
    width: 28px;
    height: 28px;
  }
}


@media (prefers-reduced-motion: reduce) {
  .slide-card,
  .shop-button,
  .nav-button,
  .carousel-dots button {
    transition: none;
  }
}



.nav-button {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  line-height: 0;
  box-sizing: border-box;
}

.nav-button svg {
  display: block;
  width: 20px;
  height: 20px;
  flex-shrink: 0;
  fill: none;
  stroke: currentColor;
  stroke-width: 1.8;
  stroke-linecap: round;
  stroke-linejoin: round;
}

@media (max-width: 560px) {
  .nav-button svg {
    width: 17px;
    height: 17px;
  }
}

</style>
