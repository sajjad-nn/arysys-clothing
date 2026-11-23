<template>
  <main class="articles-page" dir="rtl">
    <!-- بنر بالای مجله هم‌هویت با برند -->
    <header class="articles-hero">
      <div class="articles-wrap hero-inner">
        <span class="eyebrow">ARYSYS MAGAZINE</span>
        <h1>مجله <span>آریاسیس</span></h1>
        <p>
          تازه‌ترین مطالب دنیای مد، انتخاب استایل، راهنمای ست کردن پوشاک
          و ترندهای فصلی را دنبال کنید.
        </p>
      </div>
    </header>

    <section class="articles-wrap articles-container">
      <!-- حالت بارگذاری -->
      <div v-if="loading" class="state-box" role="status">
        <span class="spinner" aria-hidden="true"></span>
        <p>در حال دریافت مقالات...</p>
      </div>

      <!-- حالت خطا -->
      <div v-else-if="error" class="state-box is-error" role="alert">
        <p>{{ errorMessage || 'دریافت مقالات با مشکل مواجه شد.' }}</p>
        <button type="button" class="retry-btn" @click="fetchArticles">
          تلاش مجدد
        </button>
      </div>

      <!-- حالت خالی -->
      <div v-else-if="!articles.length" class="state-box">
        <p>هنوز مقاله‌ای منتشر نشده است.</p>
      </div>

      <!-- گرید مقالات -->
      <div v-else class="articles-grid">
        <article
          v-for="article in articles"
          :key="article.id"
          class="article-card"
        >
          <RouterLink :to="articleLink(article)" class="media-link">
            <img
              v-if="hasValidImage(article)"
              :key="`${article.id}-${getRawImage(article)}`"
              :src="resolveImage(article)"
              :alt="article.title"
              class="article-image"
              loading="lazy"
              decoding="async"
              @error="onImageError(article)"
            />
            <div v-else class="article-placeholder">
              <span>ARYSYS</span>
            </div>
          </RouterLink>

          <div class="article-content">
            <span class="article-category">
              {{ article.category?.name || 'سبک و استایل' }}
            </span>

            <h2>
              <RouterLink :to="articleLink(article)">
                {{ article.title }}
              </RouterLink>
            </h2>

            <p v-if="article.excerpt">{{ article.excerpt }}</p>

            <RouterLink
              :to="articleLink(article)"
              class="read-more"
            >
              <span>مطالعه مقاله</span>
              <span class="arrow" aria-hidden="true">←</span>
            </RouterLink>
          </div>
        </article>
      </div>
    </section>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import articleService from '../services/articleService'
import { API_BASE } from '@/services/api'

const articles = ref([])
const loading = ref(true)
const error = ref(false)
const errorMessage = ref('')
const brokenImages = ref({})

const normalizedApiBase = (API_BASE || '').trim().replace(/\/+$/, '')

const articleLink = (article) => {
  const target = article?.slug || article?.id
  return target ? `/articles/${target}` : '/articles'
}

const getRawImage = (article) => {
  return (article?.image_url || article?.image || '').trim()
}

const hasValidImage = (article) => {
  const raw = getRawImage(article)
  if (!raw) return false
  return !brokenImages.value[`${article.id}::${raw}`]
}

const resolveImage = (article) => {
  const raw = getRawImage(article)
  if (!raw) return ''

  if (/^https?:\/\//i.test(raw)) {
    if (
      typeof window !== 'undefined' &&
      window.location.protocol === 'https:' &&
      raw.startsWith('http://')
    ) {
      return raw.replace(/^http:\/\//i, 'https://')
    }
    return raw
  }

  if (raw.startsWith('//')) {
    const proto = typeof window !== 'undefined' ? window.location.protocol : 'https:'
    return `${proto}${raw}`
  }

  const cleanPath = raw
    .replace(/^\/+/, '')
    .replace(/^storage\/+/, '')

  return normalizedApiBase
    ? `${normalizedApiBase}/storage/${cleanPath}`
    : `/storage/${cleanPath}`
}

const onImageError = (article) => {
  const raw = getRawImage(article)
  if (!raw) return

  brokenImages.value = {
    ...brokenImages.value,
    [`${article.id}::${raw}`]: true
  }
}

const fetchArticles = async () => {
  loading.value = true
  error.value = false
  errorMessage.value = ''
  brokenImages.value = {}

  try {
    const response = await articleService.getArticles()
    const list = Array.isArray(response?.data)
      ? response.data
      : Array.isArray(response?.data?.data)
        ? response.data.data
        : []

    articles.value = list
  } catch (err) {
    console.error('Articles error:', err?.response || err)
    error.value = true
    errorMessage.value =
      err?.response?.data?.message ||
      'دریافت مقالات با مشکل مواجه شد.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchArticles)
</script>

<style scoped>
.articles-page {
  --gold: #a38253;
  --gold-light: #e0c48c;
  --ink: #18181b;
  --muted: #71717a;
  --line-soft: #eee9e0;

  min-height: 100vh;
  background: var(--color-canvas, #fbfbfd);
  color: #1a1a1a;
  padding-bottom: var(--section-space, 80px);
  overflow-x: clip;
}

.articles-wrap {
  width: 100%;
  max-width: var(--site-max, 1360px);
  margin-inline: auto;
  padding-inline: var(--gutter, 28px);
  box-sizing: border-box;
}

/* بنر بالای صفحه */
.articles-hero {
  position: relative;
  overflow: hidden;
  padding: calc(var(--header-h, 80px) + 36px) 0 88px;
  background:
    radial-gradient(50% 70% at 90% 0%, rgba(199, 166, 106, 0.18) 0%, transparent 65%),
    radial-gradient(40% 60% at 0% 100%, rgba(199, 166, 106, 0.09) 0%, transparent 70%),
    #0a0908;
  color: #fff;
  text-align: center;
}

.articles-hero::after {
  content: '';
  position: absolute;
  width: 280px;
  height: 280px;
  bottom: -150px;
  left: 8%;
  border: 1px solid rgba(199, 166, 106, 0.14);
  border-radius: 50%;
  box-shadow: 0 0 0 36px rgba(199, 166, 106, 0.03);
  pointer-events: none;
}

.hero-inner {
  position: relative;
  z-index: 1;
}

.eyebrow {
  display: inline-block;
  margin-bottom: 14px;
  color: #c7a66a;
  font-size: 11.5px;
  font-weight: 700;
  letter-spacing: 3.5px;
  direction: ltr;
}

.articles-hero h1 {
  margin: 0 0 14px;
  font-size: clamp(28px, 4vw, 42px);
  font-weight: 800;
  line-height: 1.4;
}

.articles-hero h1 span {
  background: linear-gradient(135deg, #f3deb5 0%, #c7a66a 60%, #b08d52 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.articles-hero p {
  max-width: 540px;
  margin: 0 auto;
  color: #a3a3a3;
  font-size: 14.5px;
  line-height: 2;
}

/* کانتینر اصلی */
.articles-container {
  margin-top: 48px;
}

.articles-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 24px;
}

/* کارت مقاله */
.article-card {
  display: flex;
  flex-direction: column;
  min-width: 0;
  background: #fff;
  border: 1px solid var(--line-soft);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 6px 24px -10px rgba(0, 0, 0, 0.08);
  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease,
    border-color 0.3s ease;
}

@media (hover: hover) {
  .article-card:hover {
    transform: translateY(-6px);
    border-color: #e3d5bd;
    box-shadow: 0 18px 36px -16px rgba(163, 130, 83, 0.28);
  }

  .article-card:hover .article-image {
    transform: scale(1.04);
  }

  .article-card:hover .read-more .arrow {
    transform: translateX(-4px);
  }
}

.media-link {
  display: block;
  overflow: hidden;
  background: #f5f2ec;
  aspect-ratio: 16 / 11;
}

.article-image {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.article-placeholder {
  display: grid;
  place-items: center;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #161412 0%, #26221d 100%);
  color: #c7a66a;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 4px;
}

.article-content {
  display: flex;
  flex: 1;
  flex-direction: column;
  padding: 22px 24px 24px;
}

.article-category {
  align-self: flex-start;
  padding: 4px 12px;
  border-radius: 999px;
  background: #fbf8f3;
  border: 1px solid #eee5d8;
  color: var(--gold);
  font-size: 11.5px;
  font-weight: 700;
}

.article-content h2 {
  margin: 14px 0 10px;
  font-size: 18px;
  font-weight: 800;
  line-height: 1.75;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.article-content h2 a {
  color: var(--ink);
  text-decoration: none;
  transition: color 0.2s ease;
}

.article-content h2 a:hover {
  color: var(--gold);
}

.article-content p {
  margin: 0 0 18px;
  color: var(--muted);
  font-size: 13.5px;
  line-height: 1.95;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.read-more {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-top: auto;
  padding-top: 12px;
  border-top: 1px solid #f4f1ea;
  color: var(--ink);
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  transition: color 0.2s ease;
}

.read-more:hover {
  color: var(--gold);
}

.read-more .arrow {
  color: var(--gold);
  transition: transform 0.25s ease;
}

/* وضعیت‌ها */
.state-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 14px;
  min-height: 240px;
  padding: 32px 20px;
  border: 1px solid var(--line-soft);
  border-radius: 20px;
  background: #fff;
  color: var(--muted);
  font-size: 14px;
  text-align: center;
}

.state-box.is-error {
  border-color: #fecaca;
  background: #fffafa;
  color: #b91c1c;
}

.spinner {
  width: 28px;
  height: 28px;
  border: 3px solid #eee5d8;
  border-top-color: var(--gold);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.retry-btn {
  padding: 10px 20px;
  border: 1px solid var(--ink);
  border-radius: 10px;
  background: var(--ink);
  color: #fff;
  font-family: inherit;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
}

/* تبلت */
@media (max-width: 992px) {
  .articles-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
  }
}

/* موبایل */
@media (max-width: 640px) {
  .articles-hero {
    padding-bottom: 64px;
  }

  .articles-hero p {
    font-size: 13.5px;
  }

  .articles-container {
    margin-top: 28px;
  }

  .articles-grid {
    grid-template-columns: minmax(0, 1fr);
    gap: 16px;
  }

  .article-content {
    padding: 18px;
  }

  .article-content h2 {
    font-size: 16.5px;
    margin: 12px 0 8px;
  }
}
</style>