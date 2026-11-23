<template>
  <main class="article-page" dir="rtl">
    <!-- نوار پیشرفت مطالعه -->
    <div v-if="article" class="read-progress" aria-hidden="true">
      <span :style="{ transform: `scaleX(${progress})` }"></span>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="wrap" aria-busy="true" aria-live="polite">
      <div class="skeleton sk-back"></div>
      <div class="skeleton sk-chip"></div>
      <div class="skeleton sk-title"></div>
      <div class="skeleton sk-title short"></div>
      <div class="skeleton sk-cover"></div>
      <div class="skeleton sk-line"></div>
      <div class="skeleton sk-line"></div>
      <div class="skeleton sk-line short"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="wrap">
      <div class="state-card">
        <span class="state-icon">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.6"
            aria-hidden="true"
          >
            <circle cx="12" cy="12" r="9" />
            <path d="M12 7.5v5.5M12 16.5h.01" stroke-linecap="round" />
          </svg>
        </span>

        <h1>مقاله موردنظر پیدا نشد</h1>
        <p>ممکن است آدرس تغییر کرده یا مقاله حذف شده باشد.</p>

        <div class="state-actions">
          <button type="button" class="btn-dark" @click="fetchArticle">تلاش دوباره</button>

          <router-link to="/articles" class="btn-line"> بازگشت به مقالات </router-link>
        </div>
      </div>
    </div>

    <!-- Article -->
    <article v-else-if="article" class="wrap">
      <router-link to="/articles" class="back-link">
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          aria-hidden="true"
        >
          <path d="m9 6 6 6-6 6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        بازگشت به مقالات
      </router-link>

      <header class="article-header">
        <span v-if="article.category" class="category">
          {{ article.category.name }}
        </span>

        <h1>{{ article.title }}</h1>

        <div class="article-meta">
          <span v-if="article.published_at" class="meta-item">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.7"
              aria-hidden="true"
            >
              <rect x="3.5" y="5" width="17" height="15" rx="3" />
              <path d="M3.5 10h17M8 3v4M16 3v4" stroke-linecap="round" />
            </svg>

            {{ formatDate(article.published_at) }}
          </span>

          <span v-if="readingTime" class="meta-item">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.7"
              aria-hidden="true"
            >
              <circle cx="12" cy="12" r="9" />
              <path d="M12 7v5l3 2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>

            {{ readingTime }} دقیقه مطالعه
          </span>

          <button type="button" class="share-btn" @click="copyLink">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
              aria-hidden="true"
            >
              <path
                d="M10 14a4.5 4.5 0 0 0 6.4 0l3-3a4.5 4.5 0 0 0-6.4-6.4l-1 1"
                stroke-linecap="round"
              />
              <path
                d="M14 10a4.5 4.5 0 0 0-6.4 0l-3 3a4.5 4.5 0 0 0 6.4 6.4l1-1"
                stroke-linecap="round"
              />
            </svg>

            {{ copied ? 'کپی شد' : 'کپی لینک' }}
          </button>
        </div>
      </header>

      <figure v-if="coverImageSrc && !coverImageError" class="cover">
        <img
          :src="coverImageSrc"
          :alt="article.title"
          decoding="async"
          @error="coverImageError = true"
        />
      </figure>

      <div class="article-body">
        <p v-if="article.excerpt" class="article-excerpt">
          {{ article.excerpt }}
        </p>

        <div class="article-content" v-html="article.content"></div>

        <!-- بخش نظرات -->
        <section class="comments-section">
          <div class="comments-heading">
            <div>
              <span class="comments-eyebrow">نظرات کاربران</span>
              <h2>دیدگاه شما درباره این مقاله</h2>
              <p>نظرات خود را با دیگران به اشتراک بگذارید.</p>
            </div>

            <span class="comments-count">
              {{ commentsMeta.total.toLocaleString('fa-IR') }} نظر
            </span>
          </div>

          <!-- فرم ثبت نظر برای کاربران واردشده -->
          <div v-if="auth.isLoggedIn" class="comment-form-card">
            <h3>ثبت دیدگاه جدید</h3>

            <form @submit.prevent="submitComment">
              <label for="comment-body">متن دیدگاه</label>

              <textarea
                id="comment-body"
                v-model="commentBody"
                placeholder="نظر خود را درباره این مقاله بنویسید..."
                rows="5"
                minlength="3"
                maxlength="2000"
                required
                :disabled="submittingComment"
              ></textarea>

              <div class="comment-form-footer">
                <span> {{ commentBody.length.toLocaleString('fa-IR') }} / ۲۰۰۰ </span>

                <button
                  type="submit"
                  class="btn-dark"
                  :disabled="submittingComment || commentBody.trim().length < 3"
                >
                  {{ submittingComment ? 'در حال ارسال...' : 'ارسال دیدگاه' }}
                </button>
              </div>
            </form>

            <p v-if="commentSuccess" class="comment-message success">
              {{ commentSuccess }}
            </p>

            <p v-if="commentError" class="comment-message error">
              {{ commentError }}
            </p>
          </div>

          <!-- پیام کاربران مهمان -->
          <div v-else class="comment-login-card">
            <p>برای ثبت دیدگاه ابتدا وارد حساب کاربری خود شوید.</p>

            <router-link to="/auth" class="btn-dark"> ورود به حساب کاربری </router-link>
          </div>

          <!-- بارگذاری نظرات -->
          <div v-if="commentsLoading" class="comments-loading">در حال دریافت نظرات...</div>

          <!-- خطای دریافت نظرات -->
          <div v-else-if="commentsError" class="comments-empty">
            <p>{{ commentsError }}</p>

            <button
              type="button"
              class="btn-line"
              @click="loadComments(commentsMeta.current_page || 1)"
            >
              تلاش دوباره
            </button>
          </div>

          <!-- فهرست نظرات -->
          <div v-else-if="comments.length" class="comments-list">
            <article v-for="comment in comments" :key="comment.id" class="comment-card">
              <div class="comment-avatar">
                {{ (comment.user?.name || 'ک').charAt(0) }}
              </div>

              <div class="comment-main">
                <div class="comment-author">
                  <strong>{{ comment.user?.name || 'کاربر' }}</strong>

                  <span v-if="comment.created_at">
                    {{ formatDate(comment.created_at) }}
                  </span>
                </div>

                <p>{{ comment.body }}</p>
              </div>
            </article>

            <!-- صفحه‌بندی نظرات -->
            <div v-if="commentsMeta.last_page > 1" class="comments-pagination">
              <button
                type="button"
                class="pagination-btn"
                :disabled="commentsMeta.current_page <= 1"
                @click="changeCommentsPage(commentsMeta.current_page - 1)"
              >
                قبلی
              </button>

              <span>
                صفحه
                {{ commentsMeta.current_page.toLocaleString('fa-IR') }}
                از
                {{ commentsMeta.last_page.toLocaleString('fa-IR') }}
              </span>

              <button
                type="button"
                class="pagination-btn"
                :disabled="commentsMeta.current_page >= commentsMeta.last_page"
                @click="changeCommentsPage(commentsMeta.current_page + 1)"
              >
                بعدی
              </button>
            </div>
          </div>

          <!-- بدون نظر -->
          <div v-else class="comments-empty">
            <span class="empty-comment-icon">✦</span>
            <p>هنوز دیدگاهی برای این مقاله ثبت نشده است.</p>
            <span>اولین نفری باشید که دیدگاه خود را ثبت می‌کند.</span>
          </div>
        </section>

        <!-- پایان مقاله -->
        <footer class="article-footer">
          <router-link to="/articles" class="btn-line"> مقالات بیشتر </router-link>

          <router-link to="/shop" class="btn-dark"> مشاهده فروشگاه </router-link>
        </footer>
      </div>
    </article>
  </main>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import articleService from '../services/articleService'
import { auth, checkAuth } from '../services/auth'
import { API_BASE } from '@/services/api'

const route = useRoute()

const normalizedApiBase = (API_BASE || '').trim().replace(/\/+$/, '')

const article = ref(null)
const loading = ref(true)
const error = ref(false)
const progress = ref(0)
const copied = ref(false)
const coverImageError = ref(false)

let copiedTimer = null

// وضعیت نظرات
const comments = ref([])
const commentsLoading = ref(false)
const commentsError = ref('')

const commentBody = ref('')
const submittingComment = ref(false)
const commentSuccess = ref('')
const commentError = ref('')

const commentsMeta = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
})

// آدرس تصویر شاخص سازگار با هاست و HTTPS
const coverImageSrc = computed(() => {
  const raw = (article.value?.image_url || article.value?.image || '').trim()
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

  const cleanPath = raw.replace(/^\/+/, '').replace(/^storage\/+/, '')

  return normalizedApiBase ? `${normalizedApiBase}/storage/${cleanPath}` : `/storage/${cleanPath}`
})

// تاریخ
const formatDate = (date) => {
  if (!date) return ''

  const parsed = new Date(date)
  if (Number.isNaN(parsed.getTime())) return ''

  return new Intl.DateTimeFormat('fa-IR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }).format(parsed)
}

// دریافت مقاله
const fetchArticle = async () => {
  try {
    loading.value = true
    error.value = false
    coverImageError.value = false
    commentError.value = ''
    commentSuccess.value = ''

    article.value = null
    comments.value = []

    commentsMeta.value = {
      current_page: 1,
      last_page: 1,
      total: 0,
    }

    const response = await articleService.getArticle(route.params.slug)

    article.value = response?.data?.data ?? response?.data ?? null

    if (!article.value) {
      error.value = true
      return
    }

    // دریافت نظرات مقاله
    await loadComments(1)
  } catch (err) {
    console.error('Article error:', err?.response || err)
    error.value = true
  } finally {
    loading.value = false
    updateProgress()
  }
}

// دریافت نظرات
const loadComments = async (page = 1) => {
  if (!article.value?.id) return

  try {
    commentsLoading.value = true
    commentsError.value = ''

    const response = await articleService.getComments(article.value.id, page)
    const payload = response?.data ?? response ?? {}
    const meta = payload.meta ?? payload

    comments.value = Array.isArray(payload.data)
      ? payload.data
      : Array.isArray(payload)
        ? payload
        : []

    commentsMeta.value = {
      current_page: Number(meta.current_page || 1),
      last_page: Math.max(1, Number(meta.last_page || 1)),
      total: Number(meta.total ?? comments.value.length),
    }
  } catch (err) {
    console.error('Comments error:', err?.response || err)
    commentsError.value = 'دریافت نظرات با مشکل مواجه شد.'
  } finally {
    commentsLoading.value = false
  }
}

// تغییر صفحه نظرات
const changeCommentsPage = (page) => {
  if (page < 1 || page > commentsMeta.value.last_page) {
    return
  }

  loadComments(page)
}

// ثبت نظر
const submitComment = async () => {
  if (!article.value?.id || submittingComment.value) return

  const body = commentBody.value.trim()

  if (body.length < 3) {
    commentError.value = 'متن دیدگاه باید حداقل ۳ کاراکتر باشد.'
    return
  }

  try {
    submittingComment.value = true
    commentError.value = ''
    commentSuccess.value = ''

    const response = await articleService.addComment(article.value.id, body)
    const payload = response?.data ?? response ?? {}

    commentSuccess.value =
      payload.message || 'نظر شما ثبت شد و پس از تأیید مدیریت نمایش داده می‌شود.'

    commentBody.value = ''
  } catch (err) {
    console.error('Submit comment error:', err?.response || err)

    if (err?.response?.status === 401) {
      commentError.value = 'برای ثبت نظر باید وارد حساب کاربری شوید.'
    } else if (err?.response?.status === 422) {
      commentError.value = err.response.data?.message || 'لطفاً متن دیدگاه را بررسی کنید.'
    } else {
      commentError.value = 'ثبت دیدگاه با مشکل مواجه شد. دوباره تلاش کنید.'
    }
  } finally {
    submittingComment.value = false
  }
}

// زمان مطالعه
const readingTime = computed(() => {
  if (!article.value?.content) return null

  const text = article.value.content.replace(/<[^>]*>/g, ' ')
  const words = text.trim().split(/\s+/).filter(Boolean).length

  if (!words) return null

  return Math.max(1, Math.round(words / 180)).toLocaleString('fa-IR')
})

// کپی لینک مقاله
const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href)

    copied.value = true

    clearTimeout(copiedTimer)

    copiedTimer = setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch {
    /* مرورگر اجازه کپی نداد */
  }
}

// نوار پیشرفت مطالعه
const updateProgress = () => {
  const doc = document.documentElement
  const total = doc.scrollHeight - window.innerHeight

  progress.value = total > 0 ? Math.min(1, Math.max(0, window.scrollY / total)) : 0
}

// تغییر مقاله
watch(
  () => route.params.slug,
  (slug) => {
    if (slug) {
      window.scrollTo({ top: 0 })
      fetchArticle()
    }
  },
)

// شروع کامپوننت
onMounted(() => {
  checkAuth()
  fetchArticle()

  window.addEventListener('scroll', updateProgress, {
    passive: true,
  })
})

// پاک‌سازی
onBeforeUnmount(() => {
  window.removeEventListener('scroll', updateProgress)
  clearTimeout(copiedTimer)
})
</script>

<style scoped>
.article-page {
  --gold: #a38253;
  --gold-soft: #fbf8f3;
  --gold-line: #eee5d8;
  --dark: #18181b;
  --ink: #27272a;
  --muted: #71717a;
  --line: #e4e4e7;

  position: relative;
  min-height: 70vh;
  padding: 40px var(--gutter, 24px) 80px;
  background: #fbfbfd;
  color: var(--ink);
  overflow-x: clip;
}

.wrap {
  width: 100%;
  max-width: 820px;
  margin-inline: auto;
}

/* ---------- نوار پیشرفت ---------- */
.read-progress {
  position: fixed;
  inset: 0 0 auto 0;
  z-index: 60;
  height: 3px;
  background: transparent;
  pointer-events: none;
}

.read-progress span {
  display: block;
  height: 100%;
  background: linear-gradient(90deg, #a38253, #e0c48c);
  transform-origin: right center;
  transition: transform 0.1s linear;
}

/* ---------- بازگشت ---------- */
.back-link {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  min-height: 40px;
  margin-bottom: 20px;
  color: var(--muted);
  font-size: 13px;
  font-weight: 600;
  transition: color 0.25s ease;
}

.back-link svg {
  width: 16px;
  height: 16px;
  transition: transform 0.25s ease;
}

.back-link:hover {
  color: var(--gold);
}

.back-link:hover svg {
  transform: translateX(3px);
}

/* ---------- سرتیتر ---------- */
.article-header {
  margin-bottom: 28px;
}

.category {
  display: inline-flex;
  align-items: center;
  padding: 6px 14px;
  margin-bottom: 16px;
  border: 1px solid var(--gold-line);
  border-radius: 999px;
  background: var(--gold-soft);
  color: var(--gold);
  font-size: 12.5px;
  font-weight: 700;
}

.article-header h1 {
  margin: 0 0 20px;
  color: var(--dark);
  font-size: clamp(24px, 4.2vw, 38px);
  font-weight: 800;
  line-height: 1.7;
  text-wrap: balance;
}

.article-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px 18px;
  padding-block: 14px;
  border-block: 1px solid var(--line);
  color: var(--muted);
  font-size: 13px;
}

.meta-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.meta-item svg {
  width: 16px;
  height: 16px;
  color: var(--gold);
}

.share-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  min-height: 36px;
  margin-inline-start: auto;
  padding: 0 14px;
  border: 1px solid var(--line);
  border-radius: 10px;
  background: #fff;
  color: var(--ink);
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  transition:
    border-color 0.25s ease,
    color 0.25s ease,
    background 0.25s ease;
}

.share-btn svg {
  width: 15px;
  height: 15px;
}

.share-btn:hover {
  border-color: var(--gold);
  color: var(--gold);
  background: var(--gold-soft);
}

/* ---------- تصویر شاخص ---------- */
.cover {
  margin: 0 0 32px;
  overflow: hidden;
  border-radius: 20px;
  background: #f1f1f3;
  box-shadow: 0 20px 40px -24px rgba(0, 0, 0, 0.35);
}

.cover img {
  display: block;
  width: 100%;
  max-height: 480px;
  object-fit: cover;
  aspect-ratio: 16 / 9;
}

/* ---------- متن ---------- */
.article-excerpt {
  margin: 0 0 32px;
  padding: 18px 22px;
  border-inline-start: 3px solid var(--gold);
  border-radius: 4px 14px 14px 4px;
  background: var(--gold-soft);
  color: #52525b;
  font-size: 17px;
  line-height: 2;
}

.article-content {
  font-size: 16.5px;
  line-height: 2.1;
  color: #3f3f46;
  overflow-wrap: anywhere;
}

.article-content :deep(h1),
.article-content :deep(h2),
.article-content :deep(h3),
.article-content :deep(h4) {
  margin: 2em 0 0.6em;
  color: var(--dark);
  font-weight: 800;
  line-height: 1.7;
}

.article-content :deep(h2) {
  position: relative;
  padding-inline-start: 14px;
  font-size: 1.4em;
}

.article-content :deep(h2)::before {
  content: '';
  position: absolute;
  inset-block: 0.35em;
  inset-inline-start: 0;
  width: 4px;
  border-radius: 4px;
  background: var(--gold);
}

.article-content :deep(h3) {
  font-size: 1.2em;
}

.article-content :deep(p) {
  margin: 0 0 1.2em;
}

.article-content :deep(a) {
  color: var(--gold);
  text-decoration: underline;
  text-underline-offset: 4px;
}

.article-content :deep(a:hover) {
  color: var(--dark);
}

.article-content :deep(ul),
.article-content :deep(ol) {
  margin: 0 0 1.2em;
  padding-inline-start: 1.6em;
}

.article-content :deep(li) {
  margin-bottom: 0.4em;
}

.article-content :deep(li)::marker {
  color: var(--gold);
}

.article-content :deep(blockquote) {
  margin: 1.6em 0;
  padding: 16px 20px;
  border-inline-start: 3px solid var(--gold);
  border-radius: 4px 14px 14px 4px;
  background: var(--gold-soft);
  color: #52525b;
}

.article-content :deep(img) {
  display: block;
  max-width: 100%;
  height: auto;
  margin: 1.6em auto;
  border-radius: 14px;
}

.article-content :deep(figure) {
  margin: 1.6em 0;
}

.article-content :deep(table) {
  display: block;
  width: 100%;
  margin: 1.6em 0;
  overflow-x: auto;
  border-collapse: collapse;
  font-size: 14.5px;
}

.article-content :deep(th),
.article-content :deep(td) {
  padding: 10px 14px;
  border: 1px solid var(--line);
  text-align: right;
}

.article-content :deep(th) {
  background: var(--gold-soft);
  color: var(--dark);
}

.article-content :deep(iframe),
.article-content :deep(video) {
  max-width: 100%;
  border-radius: 14px;
}

.article-content :deep(hr) {
  margin: 2em 0;
  border: 0;
  border-top: 1px solid var(--line);
}

/* ---------- بخش نظرات ---------- */
.comments-section {
  margin-top: 60px;
  padding-top: 35px;
  border-top: 1px solid var(--line);
}

.comments-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 15px;
  margin-bottom: 28px;
}

.comments-eyebrow {
  display: inline-block;
  margin-bottom: 8px;
  color: var(--gold);
  font-size: 12px;
  font-weight: 800;
}

.comments-heading h2 {
  margin: 0 0 8px;
  color: var(--dark);
  font-size: 22px;
  font-weight: 800;
}

.comments-heading p {
  margin: 0;
  color: var(--muted);
  font-size: 13px;
}

.comments-count {
  flex-shrink: 0;
  padding: 8px 14px;
  border: 1px solid var(--gold-line);
  border-radius: 30px;
  background: var(--gold-soft);
  color: var(--gold);
  font-size: 12px;
  font-weight: 700;
}

.comment-form-card,
.comment-login-card {
  margin-bottom: 32px;
  padding: 24px;
  border: 1px solid var(--gold-line);
  border-radius: 16px;
  background: #fff;
}

.comment-form-card h3 {
  margin: 0 0 20px;
  color: var(--dark);
  font-size: 17px;
  font-weight: 800;
}

.comment-form-card label {
  display: block;
  margin-bottom: 10px;
  color: var(--ink);
  font-size: 13px;
  font-weight: 700;
}

.comment-form-card textarea {
  display: block;
  width: 100%;
  min-height: 130px;
  padding: 14px;
  border: 1px solid var(--line);
  border-radius: 12px;
  outline: none;
  background: #fbfbfd;
  color: var(--ink);
  font-family: inherit;
  font-size: 14px;
  line-height: 1.9;
  resize: vertical;
  transition: border-color 0.25s ease;
}

.comment-form-card textarea:focus {
  border-color: var(--gold);
}

.comment-form-card textarea:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.comment-form-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-top: 14px;
}

.comment-form-footer > span {
  color: var(--muted);
  font-size: 12px;
}

.comment-form-footer button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.comment-message {
  margin: 16px 0 0;
  padding: 12px 14px;
  border-radius: 10px;
  font-size: 13px;
  line-height: 1.8;
}

.comment-message.success {
  border: 1px solid #bbf7d0;
  background: #f0fdf4;
  color: #166534;
}

.comment-message.error {
  border: 1px solid #fecaca;
  background: #fef2f2;
  color: #b91c1c;
}

.comment-login-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 15px;
}

.comment-login-card p {
  margin: 0;
  color: var(--muted);
  font-size: 13px;
}

.comments-loading,
.comments-empty {
  padding: 35px 20px;
  border: 1px solid var(--line);
  border-radius: 14px;
  background: #fff;
  text-align: center;
  color: var(--muted);
  font-size: 13px;
}

.comments-empty p {
  margin: 0 0 8px;
}

.comments-empty > span:not(.empty-comment-icon) {
  display: block;
  font-size: 12px;
}

.empty-comment-icon {
  display: block;
  margin-bottom: 12px;
  color: var(--gold);
  font-size: 25px;
}

.comments-empty .btn-line {
  margin-top: 15px;
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.comment-card {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 20px;
  border: 1px solid var(--line);
  border-radius: 14px;
  background: #fff;
  transition: border-color 0.25s ease;
}

.comment-card:hover {
  border-color: var(--gold-line);
}

.comment-avatar {
  display: grid;
  place-items: center;
  flex-shrink: 0;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: var(--gold-soft);
  color: var(--gold);
  font-size: 17px;
  font-weight: 800;
}

.comment-main {
  flex: 1;
  min-width: 0;
}

.comment-author {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px 14px;
  margin-bottom: 10px;
}

.comment-author strong {
  color: var(--dark);
  font-size: 13px;
  font-weight: 800;
}

.comment-author span {
  color: var(--muted);
  font-size: 11px;
}

.comment-main > p {
  margin: 0;
  color: #52525b;
  font-size: 13.5px;
  line-height: 2;
  overflow-wrap: anywhere;
}

.comments-pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 15px;
  margin-top: 25px;
  color: var(--muted);
  font-size: 12px;
}

.pagination-btn {
  min-width: 75px;
  min-height: 36px;
  padding: 0 14px;
  border: 1px solid var(--gold-line);
  border-radius: 9px;
  background: #fff;
  color: var(--ink);
  font-family: inherit;
  font-size: 12px;
  cursor: pointer;
  transition: 0.25s ease;
}

.pagination-btn:hover:not(:disabled) {
  border-color: var(--gold);
  color: var(--gold);
}

.pagination-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* ---------- پایان مقاله ---------- */
.article-footer {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 44px;
  padding-top: 28px;
  border-top: 1px solid var(--line);
}

.btn-dark,
.btn-line {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 46px;
  padding: 0 24px;
  border: 1px solid var(--dark);
  border-radius: 12px;
  font-family: inherit;
  font-size: 13.5px;
  font-weight: 700;
  cursor: pointer;
  transition:
    background 0.25s ease,
    color 0.25s ease,
    transform 0.25s ease,
    border-color 0.25s ease;
}

.btn-dark {
  background: var(--dark);
  color: #fff;
}

.btn-dark:hover {
  background: var(--gold);
  border-color: var(--gold);
  transform: translateY(-2px);
}

.btn-line {
  background: transparent;
  color: var(--dark);
}

.btn-line:hover {
  background: var(--dark);
  color: #fff;
}

/* ---------- حالت خطا ---------- */
.state-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 56px 24px;
  border: 1px solid var(--gold-line);
  border-radius: 20px;
  background: #fff;
  text-align: center;
}

.state-icon {
  display: grid;
  place-items: center;
  width: 64px;
  height: 64px;
  margin-bottom: 18px;
  border-radius: 50%;
  background: var(--gold-soft);
  color: var(--gold);
}

.state-icon svg {
  width: 30px;
  height: 30px;
}

.state-card h1 {
  margin: 0 0 8px;
  color: var(--dark);
  font-size: 20px;
  font-weight: 800;
}

.state-card p {
  margin: 0 0 24px;
  color: var(--muted);
  font-size: 14px;
}

.state-actions {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 10px;
}

/* ---------- اسکلت بارگذاری ---------- */
.skeleton {
  margin-bottom: 14px;
  border-radius: 10px;
  background: linear-gradient(90deg, #efeff1 25%, #f8f8fa 50%, #efeff1 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s linear infinite;
}

.sk-back {
  width: 130px;
  height: 18px;
  margin-bottom: 26px;
}

.sk-chip {
  width: 90px;
  height: 28px;
  border-radius: 999px;
}

.sk-title {
  height: 30px;
}

.sk-title.short {
  width: 60%;
  margin-bottom: 28px;
}

.sk-cover {
  height: 300px;
  margin-bottom: 28px;
  border-radius: 20px;
}

.sk-line {
  height: 14px;
}

.sk-line.short {
  width: 70%;
}

@keyframes shimmer {
  to {
    background-position: -200% 0;
  }
}

/* ---------- ریسپانسیو ---------- */
@media (max-width: 767px) {
  .article-page {
    padding-top: 24px;
    padding-bottom: 7rem;
  }

  .article-content {
    font-size: 15.5px;
    line-height: 2;
  }

  .article-excerpt {
    padding: 14px 16px;
    font-size: 15px;
  }

  .cover {
    margin-inline: calc(var(--gutter, 16px) * -1);
    border-radius: 0;
  }

  .cover img {
    aspect-ratio: 4 / 3;
  }

  .sk-cover {
    height: 200px;
  }

  .share-btn {
    margin-inline-start: 0;
  }

  .article-footer > * {
    flex: 1 1 100%;
  }

  .comments-section {
    margin-top: 42px;
    padding-top: 25px;
  }

  .comments-heading {
    align-items: flex-start;
  }

  .comments-heading h2 {
    font-size: 19px;
  }

  .comment-form-card,
  .comment-login-card {
    padding: 17px;
  }

  .comment-login-card {
    align-items: flex-start;
    flex-direction: column;
  }

  .comment-login-card .btn-dark {
    width: 100%;
  }

  .comment-card {
    gap: 10px;
    padding: 15px;
  }

  .comment-avatar {
    width: 36px;
    height: 36px;
    font-size: 15px;
  }

  .comments-pagination {
    gap: 9px;
  }
}

@media (min-width: 768px) and (max-width: 1023px) {
  .article-page {
    padding-bottom: 7rem;
  }
}

@media (prefers-reduced-motion: reduce) {
  .skeleton {
    animation: none;
  }
}
</style>
