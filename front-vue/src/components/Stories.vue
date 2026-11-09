<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { FreeMode } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/free-mode'
import api from '../services/api'

const IMAGE_DURATION = 5000 // میلی‌ثانیه
const SEEN_KEY = 'seen_stories'

const stories = ref([])
const activeIndex = ref(-1)
const progress = ref(0)
const seen = ref(new Set())

const barBegin = ref(true)
const barEnd = ref(false)

let raf = 0
let viewerSwiper = null
let barSwiper = null

const current = computed(() => stories.value[activeIndex.value] ?? null)
const isOpen = computed(() => activeIndex.value >= 0)
const isFirst = computed(() => activeIndex.value <= 0)
const isLast = computed(() => activeIndex.value >= stories.value.length - 1)

const isInternal = (url) => typeof url === 'string' && url.startsWith('/')

const loadSeen = () => {
  try {
    seen.value = new Set(JSON.parse(localStorage.getItem(SEEN_KEY) || '[]'))
  } catch {
    seen.value = new Set()
  }
}

const markSeen = (id) => {
  seen.value.add(id)
  seen.value = new Set(seen.value)
  try {
    localStorage.setItem(SEEN_KEY, JSON.stringify([...seen.value]))
  } catch {}
}

const fetchStories = async () => {
  try {
    const res = await api.get('/api/stories')
    const data = res.data.data ?? res.data
    stories.value = Array.isArray(data) ? data : []
  } catch (err) {
    console.error('خطای دریافت استوری‌ها:', err)
  }
}

/* ---------- نوار دایره‌ها ---------- */

const onBarSwiper = (swiper) => {
  barSwiper = swiper
  syncBar(swiper)
}

const syncBar = (swiper) => {
  barBegin.value = swiper.isBeginning
  barEnd.value = swiper.isEnd
}

const barPrev = () => barSwiper?.slidePrev()
const barNext = () => barSwiper?.slideNext()

/* ---------- نمایشگر ---------- */

const segmentWidth = (i) => {
  if (i < activeIndex.value) return '100%'
  if (i === activeIndex.value) return `${progress.value}%`
  return '0%'
}

const open = (index) => {
  activeIndex.value = index
}

const close = () => {
  cancelAnimationFrame(raf)
  viewerSwiper = null
  activeIndex.value = -1
  progress.value = 0
}

const next = () => {
  if (!isLast.value) viewerSwiper?.slideNext()
  else close()
}

const prev = () => {
  if (!isFirst.value) viewerSwiper?.slidePrev()
  else startTimer()
}

const startTimer = () => {
  cancelAnimationFrame(raf)
  progress.value = 0
  if (!current.value || current.value.type === 'video') return

  const start = performance.now()
  const tick = (now) => {
    progress.value = Math.min(((now - start) / IMAGE_DURATION) * 100, 100)
    if (progress.value >= 100) {
      next()
      return
    }
    raf = requestAnimationFrame(tick)
  }
  raf = requestAnimationFrame(tick)
}

const onVideoTime = (e) => {
  const v = e.target
  if (v.duration) progress.value = (v.currentTime / v.duration) * 100
}

const onViewerSwiper = (swiper) => {
  viewerSwiper = swiper
}

const onSlideChange = (swiper) => {
  activeIndex.value = swiper.activeIndex
}

// کلیک روی کارت کناری: برو به همان استوری
// کلیک روی کارت فعال: در RTL سمت چپ = بعدی، سمت راست = قبلی
const onCardClick = (e, i) => {
  if (i !== activeIndex.value) {
    viewerSwiper?.slideTo(i)
    return
  }
  const rect = e.currentTarget.getBoundingClientRect()
  const x = e.clientX - rect.left
  if (x < rect.width / 2) next()
  else prev()
}

const onKey = (e) => {
  if (!isOpen.value) return
  if (e.key === 'Escape') close()
  if (e.key === 'ArrowLeft') next()
  if (e.key === 'ArrowRight') prev()
}

watch(activeIndex, async (val) => {
  document.body.style.overflow = val >= 0 ? 'hidden' : ''
  if (val < 0) return
  markSeen(stories.value[val].id)
  await nextTick()
  startTimer()
})

onMounted(() => {
  loadSeen()
  fetchStories()
  window.addEventListener('keydown', onKey)
})

onUnmounted(() => {
  cancelAnimationFrame(raf)
  document.body.style.overflow = ''
  window.removeEventListener('keydown', onKey)
})
</script>

<template>
  <section v-if="stories.length" class="stories-section" dir="rtl">

    <!-- نوار دایره‌ها (اسلایدر) -->
    <div class="stories-wrap">

      <button
        type="button"
        class="bar-arrow bar-arrow-prev"
        :disabled="barBegin"
        aria-label="قبلی"
        @click="barPrev"
      >
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" aria-hidden="true">
          <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>

      <Swiper
        class="stories-bar"
        dir="rtl"
        :modules="[FreeMode]"
        :slides-per-view="'auto'"
        :space-between="22"
        :free-mode="true"
        :slides-offset-before="4"
        :slides-offset-after="4"
        @swiper="onBarSwiper"
        @progress="syncBar"
        @resize="syncBar"
      >
        <SwiperSlide
          v-for="(story, i) in stories"
          :key="story.id"
          class="story-slide"
        >
          <button
            type="button"
            class="story-item"
            :class="{ seen: seen.has(story.id) }"
            @click="open(i)"
          >
            <span class="story-ring">
              <span class="story-cover">
                <img :src="story.cover" :alt="story.title" loading="lazy" />
              </span>
            </span>
            <span class="story-title">{{ story.title }}</span>
          </button>
        </SwiperSlide>
      </Swiper>

      <button
        type="button"
        class="bar-arrow bar-arrow-next"
        :disabled="barEnd"
        aria-label="بعدی"
        @click="barNext"
      >
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" aria-hidden="true">
          <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>

    </div>

    <!-- نمایشگر تمام‌صفحه -->
    <Teleport to="body">
      <div v-if="isOpen && current" class="story-viewer" dir="rtl" @click.self="close">

        <button type="button" class="story-close" aria-label="بستن" @click="close">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" aria-hidden="true">
            <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
          </svg>
        </button>

        <button
          type="button"
          class="viewer-arrow viewer-arrow-prev"
          :disabled="isFirst"
          aria-label="استوری قبلی"
          @click="prev"
        >
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" aria-hidden="true">
            <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>

        <button
          type="button"
          class="viewer-arrow viewer-arrow-next"
          :disabled="isLast"
          aria-label="استوری بعدی"
          @click="next"
        >
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" aria-hidden="true">
            <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>

        <!-- اسلایدر استوری‌ها -->
        <Swiper
          class="story-swiper"
          dir="rtl"
          :slides-per-view="'auto'"
          :centered-slides="true"
          :space-between="18"
          :initial-slide="activeIndex"
          :speed="400"
          :threshold="8"
          @swiper="onViewerSwiper"
          @slide-change="onSlideChange"
          @click.self="close"
        >
          <SwiperSlide
            v-for="(story, i) in stories"
            :key="story.id"
            class="viewer-slide"
          >
            <div class="story-card" :class="{ active: i === activeIndex }">

              <template v-if="i === activeIndex">
                <!-- نوار پیشرفت: یک تکه برای هر استوری -->
                <div class="story-progress">
                  <div v-for="(s, k) in stories" :key="s.id" class="story-progress-seg">
                    <span :style="{ width: segmentWidth(k) }"></span>
                  </div>
                </div>

                <div class="story-head">
                  <span class="story-head-avatar">
                    <img :src="story.cover" :alt="story.title" />
                  </span>
                  <span class="story-head-title">{{ story.title }}</span>
                </div>
              </template>

              <div class="story-media" @click="onCardClick($event, i)">
                <template v-if="i === activeIndex">
                  <video
                    v-if="story.type === 'video'"
                    :src="story.media"
                    autoplay
                    muted
                    playsinline
                    @timeupdate="onVideoTime"
                    @ended="next"
                  ></video>
                  <img v-else :src="story.media" :alt="story.title" />
                </template>
                <img
                  v-else
                  :src="story.type === 'video' ? story.cover : story.media"
                  :alt="story.title"
                  loading="lazy"
                />
              </div>

              <div
                v-if="i === activeIndex && story.button_text && story.button_url"
                class="story-cta"
              >
                <RouterLink
                  v-if="isInternal(story.button_url)"
                  :to="story.button_url"
                  class="story-btn"
                  @click="close"
                >
                  {{ story.button_text }}
                  <span aria-hidden="true">←</span>
                </RouterLink>
                <a
                  v-else
                  :href="story.button_url"
                  class="story-btn"
                  target="_blank"
                  rel="noopener"
                >
                  {{ story.button_text }}
                  <span aria-hidden="true">←</span>
                </a>
              </div>

            </div>
          </SwiperSlide>
        </Swiper>

      </div>
    </Teleport>

  </section>
</template>

<style scoped>
.stories-section {
  --gold: #c7a66a;
  --gold-light: #e0c48c;
  --gold-deep: #8b7657;
  --ink: #17140f;

  width: 82%;
  max-width: 1360px;
  margin: 40px auto 0;
}

/* =========================
   نوار دایره‌ها
========================= */

.stories-wrap {
  position: relative;
}

.stories-bar {
  padding: 6px 0 10px;
}

.story-slide {
  width: 84px;
}

.story-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 9px;
  width: 100%;

  padding: 0;
  border: 0;
  background: none;
  cursor: pointer;
  font-family: inherit;
  -webkit-tap-highlight-color: transparent;
}

.story-ring {
  display: grid;
  place-items: center;
  width: 78px;
  height: 78px;
  padding: 3px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--gold-light), var(--gold), var(--gold-deep));
  transition: transform 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.story-item.seen .story-ring {
  background: #d9d4cb;
}

.story-cover {
  display: block;
  width: 100%;
  height: 100%;
  padding: 3px;
  border-radius: 50%;
  background: #fff;
  overflow: hidden;
}

.story-cover img {
  display: block;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.story-title {
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-size: 12.5px;
  font-weight: 700;
  color: var(--ink);
}

@media (hover: hover) {
  .story-item:hover .story-ring {
    transform: scale(1.06);
  }
}

.story-item:focus-visible .story-ring {
  outline: 2px solid var(--gold);
  outline-offset: 3px;
}

/* دکمه‌های قبلی/بعدی نوار دایره‌ها (فقط دسکتاپ) */

.bar-arrow {
  position: absolute;
  top: 36px;
  z-index: 5;
  display: grid;
  place-items: center;
  width: 36px;
  height: 36px;
  padding: 0;

  border: 1px solid rgba(235, 230, 222, 0.95);
  border-radius: 50%;
  background: #fff;
  color: var(--gold-deep);
  box-shadow: 0 6px 16px -6px rgba(23, 20, 15, 0.25);
  cursor: pointer;
  transition: background 0.3s ease, color 0.3s ease, opacity 0.3s ease, transform 0.3s ease;
}

.bar-arrow-prev {
  right: -14px;
}

.bar-arrow-next {
  left: -14px;
}

.bar-arrow:hover:not(:disabled) {
  background: var(--gold);
  color: #fff;
  transform: scale(1.06);
}

.bar-arrow:disabled {
  opacity: 0;
  pointer-events: none;
}

/* =========================
   نمایشگر
========================= */

.story-viewer {
  --card-h: min(88dvh, 720px);
  --card-w: calc(var(--card-h) * 9 / 16);

  position: fixed;
  inset: 0;
  z-index: 9999;
  display: grid;
  align-items: center;
  background: rgba(10, 8, 5, 0.92);
  backdrop-filter: blur(8px);
}

.story-swiper {
  width: 100%;
  height: var(--card-h);
}

.viewer-slide {
  width: var(--card-w);
  height: var(--card-h);
}

.story-card {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 22px;
  background: #000;
  overflow: hidden;

  transform: scale(0.82);
  opacity: 0.45;
  transition:
    transform 0.45s cubic-bezier(0.165, 0.84, 0.44, 1),
    opacity 0.45s ease;
}

.story-card.active {
  transform: scale(1);
  opacity: 1;
}

.story-media {
  position: absolute;
  inset: 0;
  z-index: 1;
  cursor: pointer;
  -webkit-tap-highlight-color: transparent;
}

.story-media img,
.story-media video {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* گرادیان بالا و پایین برای خوانایی */
.story-card::before,
.story-card::after {
  content: '';
  position: absolute;
  right: 0;
  left: 0;
  z-index: 2;
  pointer-events: none;
}

.story-card::before {
  top: 0;
  height: 120px;
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), transparent);
}

.story-card::after {
  bottom: 0;
  height: 170px;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.55), transparent);
}

/* نوار پیشرفت تکه‌تکه */

.story-progress {
  position: absolute;
  top: 12px;
  right: 14px;
  left: 14px;
  z-index: 5;
  display: flex;
  gap: 4px;
  direction: rtl;
  pointer-events: none;
}

.story-progress-seg {
  flex: 1;
  height: 3px;
  border-radius: 3px;
  background: rgba(255, 255, 255, 0.3);
  overflow: hidden;
}

.story-progress-seg span {
  display: block;
  height: 100%;
  border-radius: 3px;
  background: linear-gradient(270deg, var(--gold-light), var(--gold));
}

.story-head {
  position: absolute;
  top: 24px;
  right: 14px;
  left: 60px;
  z-index: 5;
  display: flex;
  align-items: center;
  gap: 10px;
  pointer-events: none;
}

.story-head-avatar {
  flex: 0 0 auto;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid var(--gold-light);
  overflow: hidden;
}

.story-head-avatar img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.story-head-title {
  flex: 1;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.45);
}

/* دکمه بستن */

.story-close {
  position: fixed;
  top: 20px;
  left: 20px;
  z-index: 20;
  display: grid;
  place-items: center;
  width: 38px;
  height: 38px;
  padding: 0;
  border: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.18);
  backdrop-filter: blur(6px);
  color: #fff;
  line-height: 0;
  cursor: pointer;
  transition: background 0.3s ease, transform 0.3s ease;
}

.story-close svg {
  display: block;
}

.story-close:hover {
  background: rgba(255, 255, 255, 0.32);
  transform: rotate(90deg);
}

.story-close:focus-visible,
.viewer-arrow:focus-visible,
.bar-arrow:focus-visible {
  outline: 2px solid var(--gold);
  outline-offset: 2px;
}

/* دکمه‌های قبلی/بعدی نمایشگر */

.viewer-arrow {
  position: fixed;
  top: 50%;
  z-index: 20;
  display: grid;
  place-items: center;
  width: 48px;
  height: 48px;
  padding: 0;
  margin-top: -24px;

  border: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.16);
  backdrop-filter: blur(6px);
  color: #fff;
  cursor: pointer;
  transition: background 0.3s ease, color 0.3s ease, opacity 0.3s ease, transform 0.3s ease;
}

.viewer-arrow-prev {
  right: 28px;
}

.viewer-arrow-next {
  left: 28px;
}

.viewer-arrow:hover:not(:disabled) {
  background: var(--gold);
  color: #29251f;
  transform: scale(1.08);
}

.viewer-arrow:disabled {
  opacity: 0.25;
  cursor: default;
}

/* دکمه */

.story-cta {
  position: absolute;
  right: 0;
  bottom: 28px;
  left: 0;
  z-index: 5;
  display: flex;
  justify-content: center;
}

.story-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 12px 26px;
  border-radius: 10px;
  background: #e8d4ad;
  color: #29251f;
  font-size: 13.5px;
  font-weight: 700;
  text-decoration: none;
  transition: transform 0.3s ease, background 0.3s ease;
}

.story-btn:hover {
  background: #fff;
  transform: translateY(-3px);
}

/* =========================
   تبلت
========================= */

@media (max-width: 1100px) {
  .viewer-arrow-prev {
    right: 14px;
  }

  .viewer-arrow-next {
    left: 14px;
  }
}

/* =========================
   موبایل: تمام‌صفحه، بدون دکمه‌های کناری
========================= */

@media (max-width: 768px) {
  .stories-section {
    width: 92%;
    margin-top: 28px;
  }

  .story-slide {
    width: 76px;
  }

  .story-ring {
    width: 70px;
    height: 70px;
  }

  .bar-arrow,
  .viewer-arrow {
    display: none;
  }

  .story-viewer {
    --card-h: 100dvh;
    --card-w: 100vw;
    background: #000;
    backdrop-filter: none;
  }

  .story-swiper {
    height: 100dvh;
  }

  .viewer-slide {
    width: 100vw;
    height: 100dvh;
  }

  .story-card {
    border-radius: 0;
    transform: none;
    opacity: 1;
  }

  .story-progress {
    top: calc(10px + env(safe-area-inset-top));
  }

  .story-head {
    top: calc(22px + env(safe-area-inset-top));
  }

  .story-close {
    top: calc(21px + env(safe-area-inset-top));
    left: 14px;
    width: 36px;
    height: 36px;
  }

  .story-cta {
    bottom: calc(24px + env(safe-area-inset-bottom));
  }

  .story-btn {
    min-width: 200px;
    justify-content: center;
    padding: 13px 26px;
  }
}

@media (prefers-reduced-motion: reduce) {
  .story-ring,
  .story-btn,
  .story-close,
  .story-card,
  .viewer-arrow,
  .bar-arrow {
    transition: none !important;
  }
}
</style>