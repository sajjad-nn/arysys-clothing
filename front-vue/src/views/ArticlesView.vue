<template>
  <main class="articles-page" dir="rtl">
    <section class="articles-header">
      <span class="subtitle">ARYSYS MAGAZINE</span>
      <h1>مجله آریسیس</h1>
      <p>تازه‌ترین مطالب دنیای مد، پوشاک و استایل را دنبال کنید.</p>
    </section>

    <section class="articles-container">
      <div v-if="loading" class="message">
        در حال دریافت مقالات...
      </div>

      <div v-else-if="error" class="message error">
        دریافت مقالات با مشکل مواجه شد.
      </div>

      <div v-else-if="articles.length === 0" class="message">
        هنوز مقاله‌ای منتشر نشده است.
      </div>

      <div v-else class="articles-grid">
        <article
          v-for="article in articles"
          :key="article.id"
          class="article-card"
        >
          <img
            v-if="article.image"
            :src="article.image_url || article.image"
            :alt="article.title"
            class="article-image"
          />

          <div v-else class="article-placeholder">
            ARYSYS
          </div>

          <div class="article-content">
            <span class="article-category">
              {{ article.category?.name || 'مقالات' }}
            </span>

            <h2>{{ article.title }}</h2>

            <p>{{ article.excerpt }}</p>

            <router-link
              :to="`/articles/${article.slug}`"
              class="read-more"
            >
              مطالعه مقاله
              <span>←</span>
            </router-link>
          </div>
        </article>
      </div>
    </section>
  </main>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import articleService from '../services/articleService'

const articles = ref([])
const loading = ref(true)
const error = ref(false)

const fetchArticles = async () => {
  try {
    const response = await articleService.getArticles()

    console.log('Articles response:', response)

    articles.value = response.data || []

  } catch (err) {
    console.error('Articles error:', err.response || err)
    error.value = true
  } finally {
    loading.value = false
  }
}

onMounted(fetchArticles)
</script>
<style scoped>
.articles-page {
  min-height: 100vh;
  background: #fafafa;
  padding-bottom: 80px;
}

.articles-header {
  text-align: center;
  padding: 90px 20px 65px;
  background: #f0f4f8;
}

.subtitle {
  color: #2563eb;
  font-size: 13px;
  letter-spacing: 3px;
}

.articles-header h1 {
  font-size: 36px;
  margin: 18px 0;
  color: #222;
}

.articles-header p {
  color: #777;
  font-size: 15px;
}

.articles-container {
  max-width: 1200px;
  margin: 60px auto;
  padding: 0 20px;
}

.articles-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 28px;
}

.article-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
  transition: 0.3s;
}

.article-card:hover {
  transform: translateY(-6px);
}

.article-image,
.article-placeholder {
  width: 100%;
  height: 230px;
  object-fit: cover;
}

.article-placeholder {
  display: grid;
  place-items: center;
  background: #e8edf5;
  color: #2563eb;
  font-weight: bold;
  letter-spacing: 4px;
}

.article-content {
  padding: 24px;
}

.article-category {
  color: #2563eb;
  font-size: 13px;
}

.article-content h2 {
  font-size: 19px;
  margin: 15px 0;
  color: #222;
  line-height: 1.8;
}

.article-content p {
  color: #777;
  font-size: 14px;
  line-height: 2;
  min-height: 56px;
}

.read-more {
  display: inline-flex;
  gap: 10px;
  align-items: center;
  margin-top: 15px;
  color: #2563eb;
  text-decoration: none;
  font-size: 14px;
}

@media (max-width: 900px) {
  .articles-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 600px) {
  .articles-grid {
    grid-template-columns: 1fr;
  }

  .articles-header h1 {
    font-size: 28px;
  }
}
</style>