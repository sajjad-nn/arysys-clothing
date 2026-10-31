
<template>
  <main class="article-detail">

    <!-- Loading -->
    <div v-if="loading" class="status-message">
      در حال دریافت مقاله...
    </div>

    <!-- Error -->
    <div v-else-if="error" class="status-message">
      مقاله موردنظر پیدا نشد.
      <router-link to="/articles">بازگشت به مقالات</router-link>
    </div>

    <!-- Article -->
    <article v-else-if="article" class="article-container">

      <router-link to="/articles" class="back-link">
        ← بازگشت به مقالات
      </router-link>

      <div class="article-header">

        <span v-if="article.category" class="category">
          {{ article.category.name }}
        </span>

        <h1>{{ article.title }}</h1>

        <div class="article-meta">
          <span v-if="article.published_at">
            {{ formatDate(article.published_at) }}
          </span>
        </div>

      </div>

      <img
        v-if="article.image_url"
        :src="article.image_url"
        :alt="article.title"
        class="article-cover"
      />

      <p v-if="article.excerpt" class="article-excerpt">
        {{ article.excerpt }}
      </p>

      <div
        class="article-content"
        v-html="article.content"
      ></div>

    </article>

  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import articleService from '../services/articleService'

const route = useRoute()

const article = ref(null)
const loading = ref(true)
const error = ref(false)

const fetchArticle = async () => {
  try {
    loading.value = true
    error.value = false

    const response = await articleService.getArticle(route.params.slug)

    article.value = response.data

  } catch (err) {
    console.error('Article error:', err.response || err)
    error.value = true
  } finally {
    loading.value = false
  }
}

const formatDate = (date) => {
  return new Intl.DateTimeFormat('fa-IR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }).format(new Date(date))
}

onMounted(fetchArticle)
</script>

<style scoped>
.article-detail {
  direction: rtl;
  min-height: 70vh;
  padding: 60px 20px;
  background: #fafafa;
}

.article-container {
  max-width: 900px;
  margin: 0 auto;
}

.back-link {
  display: inline-block;
  margin-bottom: 35px;
  color: #2563eb;
  text-decoration: none;
  font-size: 14px;
}

.article-header {
  margin-bottom: 30px;
}

.category {
  display: inline-block;
  padding: 7px 15px;
  background: #eff6ff;
  color: #2563eb;
  border-radius: 20px;
  font-size: 13px;
  margin-bottom: 15px;
}

.article-header h1 {
  font-size: 32px;
  line-height: 1.8;
  color: #222;
  margin: 10px 0;
}

.article-meta {
  color: #888;
  font-size: 14px;
}

.article-cover {
  width: 100%;
  max-height: 480px;
  object-fit: cover;
  border-radius: 18px;
  margin-bottom: 30px;
}

.article-excerpt {
  font-size: 18px;
  line-height: 2;
  color: #555;
  padding: 20px;
  background: #f1f5f9;
  border-radius: 12px;
  margin-bottom: 30px;
}

.article-content {
  font-size: 16px;
  line-height: 2.2;
  color: #333;
  overflow-wrap: anywhere;
}

.article-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 12px;
}

.article-content :deep(h1),
.article-content :deep(h2),
.article-content :deep(h3) {
  margin: 30px 0 15px;
  line-height: 1.8;
}

.article-content :deep(p) {
  margin-bottom: 20px;
}

.status-message {
  text-align: center;
  padding: 100px 20px;
  color: #555;
}

.status-message a {
  display: block;
  margin-top: 15px;
  color: #2563eb;
}

@media (max-width: 600px) {
  .article-detail {
    padding: 35px 16px;
  }

  .article-header h1 {
    font-size: 23px;
  }

  .article-excerpt {
    font-size: 16px;
  }
}
</style>
