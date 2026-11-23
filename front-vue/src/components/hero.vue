<template>
  <section class="hero-slider" dir="rtl">
    <div v-if="loading" class="hero-status">در حال بارگذاری اسلایدر...</div>

    <div v-else-if="error" class="hero-status error">
      {{ error }}
    </div>

    <div v-else-if="slides.length === 0" class="hero-status">
      در حال حاضر اسلاید فعالی برای نمایش وجود ندارد.
    </div>

    <div v-else class="hero-content">
      <!-- بخش متن -->
      <div class="hero-text">
        <span class="hero-label">ARYASYS COLLECTION</span>

        <div class="hero-heading-wrapper">
          <Transition name="text-swap" mode="out-in">
            <div v-if="activeSlide" :key="activeSlide.id" class="hero-heading">
              <h1 class="single-line-title">
                {{ activeSlide.heading }}
                <span v-if="activeSlide.highlight" class="highlight-text">
                  {{ activeSlide.highlight }}
                </span>
              </h1>

              <!-- توضیحات کوتاه در حالت عادی -->
              <p class="hero-desc">
                {{ shortDescription(activeSlide.description) }}
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

      <!-- کاروسل تصاویر -->
      <div class="carousel-wrapper">
        <button
          class="nav-button prev-button"
          type="button"
          aria-label="تصویر قبلی"
          @click="goPrev"
        >
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M15 18l-6-6 6-6" />
          </svg>
        </button>

        <div class="carousel" @touchstart.passive="onTouchStart" @touchend.passive="onTouchEnd">
          <button
            v-for="(slide, index) in slides"
            :key="slide.id"
            type="button"
            class="slide-card"
            :class="getPosition(index)"
            :tabindex="isHidden(index) ? -1 : 0"
            :aria-hidden="isHidden(index) ? 'true' : undefined"
            :aria-label="`نمایش جزئیات ${slide.title}`"
            :aria-current="index === activeIndex ? 'true' : undefined"
            @click="openSlide(index)"
          >
            <img
              :src="slide.image"
              :alt="slide.title"
              :loading="index === 0 ? 'eager' : 'lazy'"
              decoding="async"
              draggable="false"
            />

            <div class="slide-caption">
              <span>{{ slide.subtitle }}</span>
              <h2>{{ slide.title }}</h2>
              <small class="view-details">
                مشاهده جزئیات
                <span>←</span>
              </small>
            </div>
          </button>
        </div>

        <button
          class="nav-button next-button"
          type="button"
          aria-label="تصویر بعدی"
          @click="goNext"
        >
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M9 18l6-6-6-6" />
          </svg>
        </button>
      </div>

      <!-- شمارنده و نقطه‌ها -->
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

    <!-- پنجره نمایش تصویر بزرگ و اطلاعات کامل -->
    <Transition name="modal">
      <div
        v-if="selectedSlide"
        class="slide-modal"
        role="dialog"
        aria-modal="true"
        :aria-label="`جزئیات ${selectedSlide.title}`"
        @click.self="closeModal"
      >
        <!-- نورپردازی تار از روی خود تصویر -->
        <div
          class="modal-bg"
          :style="{ backgroundImage: `url(${selectedSlide.image})` }"
          aria-hidden="true"
        ></div>

        <div class="modal-content">
          <button type="button" class="modal-close" aria-label="بستن" @click="closeModal">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>

          <div class="modal-image">
            <img :src="selectedSlide.image" :alt="selectedSlide.title" />
            <div class="modal-image-overlay"></div>
          </div>

          <div class="modal-info">
            <span class="modal-subtitle">
              {{ selectedSlide.subtitle }}
            </span>

            <span class="modal-accent" aria-hidden="true"></span>

            <h2>
              {{ selectedSlide.heading }}
              <span v-if="selectedSlide.highlight">
                {{ selectedSlide.highlight }}
              </span>
            </h2>

            <p class="modal-description">
              {{ selectedSlide.description || 'توضیحاتی برای این اسلاید ثبت نشده است.' }}
            </p>

            <div class="modal-actions">
              <RouterLink to="/shop" class="modal-shop-button" @click="closeModal">
                مشاهده محصولات
                <span>←</span>
              </RouterLink>

              <button type="button" class="modal-back-button" @click="closeModal">
                بازگشت به اسلایدر
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import api from '../services/api'

const slides = ref([])
const activeIndex = ref(0)
const selectedSlide = ref(null)

const loading = ref(true)
const error = ref('')

let autoplayTimer = null
let touchStartX = 0
let touchStartY = 0
let previousBodyOverflow = ''

const activeSlide = computed(() => {
  return slides.value[activeIndex.value] || null
})

// دریافت اسلایدها از API لاراول
async function fetchSlides() {
  loading.value = true
  error.value = ''

  try {
    const response = await api.get('/api/hero-slides')
    const rawSlides = Array.isArray(response.data) ? response.data : (response.data?.data ?? [])

    slides.value = rawSlides
      .map((slide) => ({
        id: slide.id,
        image: slide.image_url,
        title: slide.title || '',
        subtitle: slide.subtitle || '',
        heading: slide.title || '',
        highlight: slide.highlighted_title || '',
        description: slide.description || '',
        sortOrder: Number(slide.sort_order ?? 0),
      }))
      .sort((a, b) => a.sortOrder - b.sortOrder)

    activeIndex.value = 0
  } catch (err) {
    console.error('خطا در دریافت اسلایدر:', err)
    error.value = 'دریافت اطلاعات اسلایدر با مشکل مواجه شد.'
  } finally {
    loading.value = false
  }
}

// کوتاه کردن توضیحات در حالت عادی
function shortDescription(description) {
  if (!description) return ''

  const maxLength = 105

  if (description.length <= maxLength) {
    return description
  }

  return description.slice(0, maxLength).trim() + '...'
}

// رفتن به اسلاید بعدی
function nextSlide() {
  if (slides.value.length <= 1) return

  activeIndex.value = (activeIndex.value + 1) % slides.value.length
}

// رفتن به اسلاید قبلی
function prevSlide() {
  if (slides.value.length <= 1) return

  activeIndex.value = (activeIndex.value - 1 + slides.value.length) % slides.value.length
}

// مدیریت پخش خودکار
function stopAutoplay() {
  if (autoplayTimer) {
    clearInterval(autoplayTimer)
    autoplayTimer = null
  }
}

function startAutoplay() {
  stopAutoplay()

  if (slides.value.length <= 1) return

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    return
  }

  if (selectedSlide.value) return

  autoplayTimer = setInterval(nextSlide, 5000)
}

// کنترل دستی
function goNext() {
  nextSlide()
  startAutoplay()
}

function goPrev() {
  prevSlide()
  startAutoplay()
}

function goToSlide(index) {
  if (index < 0 || index >= slides.value.length) return

  activeIndex.value = index
  startAutoplay()
}

// باز کردن تصویر بزرگ
function openSlide(index) {
  if (!slides.value[index]) return

  activeIndex.value = index
  selectedSlide.value = slides.value[index]

  stopAutoplay()

  previousBodyOverflow = document.body.style.overflow
  document.body.style.overflow = 'hidden'
}

// بستن پنجره جزئیات
function closeModal() {
  if (!selectedSlide.value) return

  selectedSlide.value = null
  document.body.style.overflow = previousBodyOverflow || ''

  startAutoplay()
}

// بستن پنجره با کلید Escape
function handleKeydown(event) {
  if (event.key === 'Escape' && selectedSlide.value) {
    closeModal()
  }
}

// کنترل کشیدن با انگشت
function onTouchStart(event) {
  const touch = event.changedTouches[0]

  touchStartX = touch.clientX
  touchStartY = touch.clientY
}

function onTouchEnd(event) {
  const touch = event.changedTouches[0]

  const dx = touch.clientX - touchStartX
  const dy = touch.clientY - touchStartY

  if (Math.abs(dx) < 40 || Math.abs(dx) < Math.abs(dy)) return

  if (dx < 0) {
    goNext()
  } else {
    goPrev()
  }
}

// تعیین موقعیت تصاویر در کاروسل
function getPosition(index) {
  const total = slides.value.length
  if (total <= 1) return 'center'

  if (total === 2) {
    return index === activeIndex.value ? 'center' : 'left'
  }

  const d = (((index - activeIndex.value) % total) + total) % total

  if (d === 0) return 'center'
  if (d === 1) return 'right'
  if (d === total - 1) return 'left'

  return d <= total / 2 ? 'hidden-right' : 'hidden-left'
}

function isHidden(index) {
  return getPosition(index).startsWith('hidden')
}

onMounted(async () => {
  await fetchSlides()

  startAutoplay()
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  stopAutoplay()
  window.removeEventListener('keydown', handleKeydown)

  if (selectedSlide.value) {
    document.body.style.overflow = previousBodyOverflow || ''
  }
})
</script>

<style scoped>
/* ================================
   ساختار اصلی
================================ */

.hero-slider {
  position: relative;
  width: 100%;
  min-height: 750px;
  overflow: hidden;
  background: radial-gradient(ellipse at 70% 45%, rgba(88, 88, 88, 0.2), transparent 48%), #080808;
  color: #fff;
  display: flex;
  align-items: center;
  padding: calc(var(--header-h, 80px) + 20px) var(--gutter, 28px) 60px;
  box-sizing: border-box;
}

.hero-status {
  width: 100%;
  text-align: center;
  color: #c7a66a;
  font-size: 15px;
}

.hero-status.error {
  color: #e58c8c;
}

.hero-content {
  width: 100%;
  max-width: calc(var(--site-max, 1360px) - 2 * var(--gutter, 28px));
  margin: 0 auto;
  display: grid;
  grid-template-columns: 0.85fr 1.15fr;
  align-items: center;
  gap: 36px;
  min-width: 0;
}

/* ================================
   بخش متن
================================ */

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
  line-height: 1.5;
  font-weight: 800;
  margin: 0 0 16px;
  white-space: normal;
  letter-spacing: -0.5px;
}

.highlight-text {
  display: inline;
  margin-right: 8px;
  background: linear-gradient(135deg, #f3deb5 0%, #c7a66a 60%, #b08d52 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-desc {
  color: #a3a3a3;
  font-size: 15px;
  line-height: 2;
  max-width: 440px;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  overflow-wrap: anywhere;
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

/* ================================
   دکمه‌ها
================================ */

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
  border-radius: 12px;
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

.shop-button:active,
.contact-button:active {
  transform: translateY(0);
}

.button-arrow {
  width: 34px;
  height: 34px;
  display: grid;
  place-items: center;
  border-radius: 9px;
  background: rgba(0, 0, 0, 0.14);
  font-size: 16px;
  transition: transform 0.25s ease;
}

.shop-button:hover .button-arrow {
  transform: translateX(-4px);
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

/* ================================
   کاروسل
================================ */

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
  touch-action: pan-y;
}

.slide-card {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 48%;
  height: 88%;
  padding: 0;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 8px;
  overflow: hidden;
  background: #171717;
  cursor: pointer;
  transform-origin: center center;
  -webkit-tap-highlight-color: transparent;
  backface-visibility: hidden;
  will-change: transform, opacity;
  transition:
    transform 0.85s cubic-bezier(0.22, 0.85, 0.25, 1),
    opacity 0.6s ease,
    filter 0.7s ease,
    box-shadow 0.7s ease;
}

.slide-card img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
  -webkit-user-drag: none;
  user-select: none;
  transition: transform 0.7s ease;
}

.slide-card:hover img {
  transform: scale(1.04);
}

.slide-card.center {
  z-index: 3;
  opacity: 1;
  filter: grayscale(0%);
  transform: translate(-50%, -50%) scale(1);
  box-shadow: 0 25px 70px rgba(0, 0, 0, 0.55);
}

.slide-card.left {
  z-index: 2;
  opacity: 0.65;
  filter: grayscale(85%) brightness(0.65);
  transform: translate3d(-115%, -50%, -60px) scale(0.77) rotateY(10deg);
}

.slide-card.right {
  z-index: 2;
  opacity: 0.65;
  filter: grayscale(85%) brightness(0.65);
  transform: translate3d(15%, -50%, -60px) scale(0.77) rotateY(-10deg);
}

.slide-card.hidden-left,
.slide-card.hidden-right {
  z-index: 1;
  opacity: 0;
  pointer-events: none;
  filter: grayscale(100%) brightness(0.4);
  transition:
    transform 0.85s cubic-bezier(0.22, 0.85, 0.25, 1),
    opacity 0.3s ease,
    filter 0.7s ease;
}

.slide-card.hidden-left {
  transform: translate3d(-170%, -50%, -140px) scale(0.6) rotateY(16deg);
}

.slide-card.hidden-right {
  transform: translate3d(70%, -50%, -140px) scale(0.6) rotateY(-16deg);
}

.slide-caption {
  position: absolute;
  inset: auto 0 0;
  padding: 55px 18px 24px;
  color: #fff;
  text-align: center;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.92));
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

.view-details {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  margin-top: 12px;
  color: #e0c48c;
  font-size: 11px;
  opacity: 0;
  transform: translateY(7px);
  transition: 0.3s ease;
}

.slide-card:hover .view-details,
.slide-card.center .view-details {
  opacity: 1;
  transform: translateY(0);
}

/* دکمه‌های جابه‌جایی */

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

/* ================================
   شمارنده و نقطه‌ها
================================ */

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

/* ================================
   پنجره تصویر بزرگ (شیشه‌ای)
================================ */

.slide-modal {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  overflow: hidden;
  background: rgba(5, 5, 5, 0.55);
  backdrop-filter: blur(10px) saturate(120%);
  -webkit-backdrop-filter: blur(10px) saturate(120%);
}

.modal-bg {
  position: absolute;
  inset: -10%;
  background-size: cover;
  background-position: center;
  filter: blur(70px) saturate(160%) brightness(0.55);
  opacity: 0.75;
  transform: scale(1.1);
  pointer-events: none;
}

.modal-content {
  position: relative;
  display: grid;
  grid-template-columns: 1fr 0.95fr;
  gap: 8px;
  width: min(100%, 1020px);
  max-height: min(86vh, 760px);
  padding: 10px;
  overflow: hidden;
  direction: rtl;
  border-radius: 28px;
  background: linear-gradient(
    145deg,
    rgba(255, 255, 255, 0.14) 0%,
    rgba(255, 255, 255, 0.05) 45%,
    rgba(199, 166, 106, 0.08) 100%
  );
  border: 1px solid rgba(255, 255, 255, 0.18);
  backdrop-filter: blur(32px) saturate(180%);
  -webkit-backdrop-filter: blur(32px) saturate(180%);
  box-shadow:
    0 40px 120px rgba(0, 0, 0, 0.6),
    inset 0 1px 0 rgba(255, 255, 255, 0.35),
    inset 0 -1px 0 rgba(255, 255, 255, 0.05);
}

.modal-content::before {
  content: '';
  position: absolute;
  top: -40%;
  right: -20%;
  width: 70%;
  height: 80%;
  background: radial-gradient(closest-side, rgba(224, 196, 140, 0.22), transparent);
  pointer-events: none;
}

.modal-close {
  position: absolute;
  top: 18px;
  left: 18px;
  z-index: 5;
  width: 42px;
  height: 42px;
  display: grid;
  place-items: center;
  padding: 0;
  color: #fff;
  cursor: pointer;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.28);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.35);
  transition: 0.3s ease;
}

.modal-close:hover {
  color: #17130b;
  background: #e0c48c;
  transform: rotate(90deg);
}

.modal-close svg {
  width: 20px;
  height: 20px;
  fill: none;
  stroke: currentColor;
  stroke-width: 1.8;
  stroke-linecap: round;
}

.modal-image {
  position: relative;
  min-height: 450px;
  overflow: hidden;
  border-radius: 20px;
  background: #080808;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.45);
}

.modal-image img {
  width: 100%;
  height: 100%;
  min-height: 450px;
  display: block;
  object-fit: cover;
}

.modal-image-overlay {
  position: absolute;
  inset: 0;
  border-radius: inherit;
  background: linear-gradient(0deg, rgba(0, 0, 0, 0.35), transparent 45%);
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
  pointer-events: none;
}

.modal-info {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: clamp(22px, 3.5vw, 46px);
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(199, 166, 106, 0.4) transparent;
}

.modal-subtitle {
  align-self: flex-start;
  padding: 6px 14px;
  color: #e0c48c;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 2.5px;
  direction: ltr;
  border-radius: 999px;
  background: rgba(199, 166, 106, 0.12);
  border: 1px solid rgba(199, 166, 106, 0.35);
}

.modal-accent {
  display: block;
  width: 48px;
  height: 3px;
  margin-top: 18px;
  border-radius: 3px;
  background: linear-gradient(270deg, #e0c48c, transparent);
}

.modal-info h2 {
  margin: 16px 0;
  color: #fff;
  font-size: clamp(24px, 3vw, 36px);
  line-height: 1.6;
  font-weight: 800;
  text-shadow: 0 2px 20px rgba(0, 0, 0, 0.35);
}

.modal-info h2 span {
  display: inline-block;
  margin-right: 6px;
  background: linear-gradient(135deg, #f3deb5, #c7a66a);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.modal-description {
  margin: 0;
  padding: 16px 18px;
  color: #d4d4d4;
  font-size: 14px;
  line-height: 2.3;
  white-space: pre-line;
  overflow-wrap: anywhere;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.09);
}

.modal-actions {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 26px;
}

.modal-shop-button,
.modal-back-button {
  min-height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 0 22px;
  border-radius: 14px;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition: 0.3s ease;
}

.modal-shop-button {
  color: #17130b;
  background: linear-gradient(135deg, #f0d9a8, #c7a66a 60%, #b08d52);
  border: 1px solid rgba(255, 255, 255, 0.35);
  box-shadow:
    0 10px 28px -8px rgba(199, 166, 106, 0.7),
    inset 0 1px 0 rgba(255, 255, 255, 0.5);
}

.modal-shop-button:hover {
  transform: translateY(-3px);
  box-shadow:
    0 16px 34px -8px rgba(199, 166, 106, 0.85),
    inset 0 1px 0 rgba(255, 255, 255, 0.6);
}

.modal-back-button {
  color: #eee;
  background: rgba(255, 255, 255, 0.07);
  border: 1px solid rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
}

.modal-back-button:hover {
  color: #e0c48c;
  background: rgba(199, 166, 106, 0.12);
  border-color: rgba(199, 166, 106, 0.55);
}

/* انیمیشن باز و بسته شدن پنجره */

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-active .modal-content {
  transition:
    transform 0.5s cubic-bezier(0.16, 1, 0.3, 1),
    opacity 0.35s ease;
}

.modal-leave-active .modal-content {
  transition:
    transform 0.3s ease,
    opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  opacity: 0;
  transform: translateY(30px) scale(0.95);
}

@supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
  .modal-content {
    background: rgba(24, 24, 24, 0.92);
  }
}

/* ================================
   دسکتاپ بزرگ
================================ */

@media (min-width: 1500px) {
  .hero-slider {
    min-height: 830px;
  }

  .carousel-wrapper {
    height: 610px;
  }
}

/* ================================
   تبلت
================================ */

@media (max-width: 1023px) {
  .hero-slider {
    padding-bottom: calc(7rem + env(safe-area-inset-bottom, 0px));
  }
}

@media (max-width: 900px) {
  .hero-slider {
    min-height: 100vh;
    min-height: 100svh;
    padding-top: calc(var(--header-h, 64px) + 28px);
  }

  .hero-content {
    grid-template-columns: 1fr;
    gap: 0;
  }

  .hero-text {
    display: contents;
    text-align: center;
  }

  .hero-label {
    order: 1;
    justify-self: center;
    margin-bottom: 14px;
  }

  .hero-heading-wrapper {
    order: 2;
    min-height: 128px;
    margin-bottom: 6px;
  }

  .single-line-title {
    font-size: clamp(23px, 5.5vw, 34px);
    line-height: 1.5;
    margin: 0 0 10px;
  }

  .hero-desc {
    max-width: 480px;
    margin: 0 auto;
    font-size: 14px;
    line-height: 1.9;
  }

  .hero-actions {
    order: 5;
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 10px;
    width: 100%;
    max-width: 420px;
    margin: 18px auto 0;
  }

  .shop-button,
  .contact-button {
    width: 100%;
    height: 48px;
    font-size: 13px;
  }

  .shop-button {
    justify-content: space-between;
    padding: 0 8px 0 16px;
    gap: 8px;
  }

  .button-arrow {
    width: 30px;
    height: 30px;
  }

  .contact-button {
    gap: 8px;
    padding: 0 12px;
  }

  .carousel-wrapper {
    order: 3;
    width: 100%;
    height: clamp(300px, 52vw, 400px);
    padding: 0;
  }

  .carousel-footer {
    order: 4;
    grid-column: 1;
    width: 100%;
    padding: 4px 10px 0;
    margin-top: 4px;
  }

  .modal-content {
    grid-template-columns: 1fr 1fr;
  }

  .modal-image,
  .modal-image img {
    min-height: 380px;
  }
}

/* ================================
   موبایل
================================ */

@media (max-width: 560px) {
  .hero-slider {
    padding-inline: 18px;
    padding-top: calc(var(--header-h, 64px) + 22px);
  }

  .hero-label {
    font-size: 10px;
    letter-spacing: 2.5px;
    margin-bottom: 10px;
  }

  .hero-heading-wrapper {
    min-height: 112px;
    margin-bottom: 0;
  }

  .single-line-title {
    font-size: clamp(21px, 6.2vw, 26px);
    margin-bottom: 6px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .hero-desc {
    -webkit-line-clamp: 2;
    font-size: 12.5px;
    line-height: 1.8;
    padding: 0 2px;
  }

  .hero-actions {
    margin-top: 14px;
  }

  .shop-button,
  .contact-button {
    height: 46px;
    font-size: 12.5px;
  }

  .carousel-wrapper {
    height: clamp(240px, 36vh, 320px);
    height: clamp(240px, 36svh, 320px);
    margin: 0;
  }

  .slide-card {
    width: 60%;
    height: 90%;
  }

  .slide-card.left {
    transform: translate3d(-85%, -50%, -50px) scale(0.72);
  }

  .slide-card.right {
    transform: translate3d(-15%, -50%, -50px) scale(0.72);
  }

  .slide-card.hidden-left {
    transform: translate3d(-120%, -50%, -120px) scale(0.6);
  }

  .slide-card.hidden-right {
    transform: translate3d(20%, -50%, -120px) scale(0.6);
  }

  .slide-caption {
    padding: 36px 12px 14px;
  }

  .slide-caption h2 {
    font-size: 15px;
  }

  .slide-caption span {
    font-size: 9px;
    margin-bottom: 5px;
  }

  .view-details {
    opacity: 1;
    transform: none;
    font-size: 10px;
  }

  .nav-button {
    width: 34px;
    height: 34px;
  }

  .nav-button svg {
    width: 17px;
    height: 17px;
  }

  .carousel-footer {
    justify-content: center;
    padding: 0;
  }

  .slide-counter {
    display: none;
  }

  .slide-modal {
    padding: 12px;
    align-items: center;
  }

  .modal-content {
    display: flex;
    flex-direction: column;
    width: 100%;
    max-height: 90vh;
    max-height: 90svh;
    padding: 8px;
    border-radius: 22px;
  }

  .modal-image {
    flex: 0 0 auto;
    min-height: 0;
    height: 34vh;
    height: 34svh;
    max-height: 280px;
    border-radius: 16px;
  }

  .modal-image img {
    min-height: 0;
    height: 100%;
    object-fit: cover;
  }

  .modal-info {
    padding: 18px 12px 16px;
    overflow-y: auto;
  }

  .modal-subtitle {
    font-size: 9px;
    padding: 5px 12px;
  }

  .modal-accent {
    margin-top: 14px;
  }

  .modal-info h2 {
    font-size: 23px;
    line-height: 1.5;
    margin: 10px 0;
  }

  .modal-description {
    padding: 12px 14px;
    font-size: 12.5px;
    line-height: 2.1;
  }

  .modal-actions {
    margin-top: 18px;
    display: grid;
    grid-template-columns: 1fr;
    gap: 9px;
  }

  .modal-shop-button,
  .modal-back-button {
    width: 100%;
    min-height: 44px;
    box-sizing: border-box;
  }

  .modal-close {
    top: 14px;
    left: 14px;
    width: 36px;
    height: 36px;
  }
}

/* موبایل خیلی کوچک */
@media (max-width: 360px) {
  .hero-actions {
    grid-template-columns: 1fr 1fr;
    padding-top: 10px;
  }

  .hero-heading-wrapper {
    min-height: 104px;
  }

  .modal-info {
    padding: 16px 10px 14px;
  }
}

/* کاهش حرکت برای افرادی که این گزینه را فعال کرده‌اند */
@media (prefers-reduced-motion: reduce) {
  .slide-card,
  .slide-card img,
  .shop-button,
  .contact-button,
  .button-arrow,
  .nav-button,
  .carousel-dots button,
  .modal-close,
  .modal-shop-button,
  .modal-back-button,
  .text-swap-enter-active,
  .text-swap-leave-active,
  .modal-enter-active,
  .modal-leave-active,
  .modal-enter-active .modal-content,
  .modal-leave-active .modal-content {
    transition: none !important;
  }
}
</style>