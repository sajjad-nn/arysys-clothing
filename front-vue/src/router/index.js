import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import dashboard from '../views/dashboard.vue'
import Auth from '../components/Auth/Auth.vue'
import ShopView from '../views/ShopView.vue'
import ArticlesView from '../views/ArticlesView.vue'
import ArticleDetailView from '../views/ArticleDetailView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/shop',
      name: 'shop',
      component: ShopView,
    },
    {
      path: '/auth',
      name: 'auth',
      component: Auth,
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: dashboard,
    },

    {
      path: '/articles',
      name: 'articles',
      component: ArticlesView,
    },

    {
    path: '/articles/:slug',
    name: 'article-detail',
    component: ArticleDetailView,
},
  ],
})

export default router
