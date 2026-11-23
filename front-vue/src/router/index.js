import { createRouter, createWebHistory } from 'vue-router'
import { auth, checkAuth } from '@/services/auth'

import HomeView from '../views/HomeView.vue'
import Auth from '../components/Auth/Auth.vue'
import ShopView from '../views/ShopView.vue'
import ArticlesView from '../views/ArticlesView.vue'
import ArticleDetailView from '../views/ArticleDetailView.vue'
import Dashboard from '../views/Dashboard.vue'
import AccountOverview from '../views/account/AccountOverview.vue'
import OrdersView from '../views/account/OrdersView.vue'
import WishlistView from '../views/account/WishlistView.vue'
import AddressesView from '../views/account/AddressesView.vue'
import ProfileView from '../views/account/ProfileView.vue'
import ProductView from '../views/ProductView.vue'
import AboutUs from '../views/AboutUs.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior() {
    return { top: 0 }
  },
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
      path: '/product/:id',
      name: 'product-detail',
      component: ProductView,
      props: true,
    },
    {
      path: '/auth',
      name: 'auth',
      component: Auth,
      meta: { guestOnly: true },
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
    {
      path: '/aboutus',
      name: 'AboutUs',
      component: AboutUs,
    },
    {
      path: '/cart',
      name: 'Cart',
      component: () => import('../views/Cart.vue'),
    },

    {
      path: '/contact',
      name: 'contact',
      component: () => import('../views/ContactView.vue'),
    },

    {
  path: '/checkout',
  name: 'checkout',
  component: () => import('../views/CheckoutView.vue'),
},

    {
      path: '/dashboard',
      component: Dashboard,
      meta: { requiresAuth: true },
      children: [
        { path: '', name: 'dashboard', component: AccountOverview },
        { path: 'orders', name: 'dashboard-orders', component: OrdersView },
        { path: 'wishlist', name: 'dashboard-wishlist', component: WishlistView },
        { path: 'addresses', name: 'dashboard-addresses', component: AddressesView },
        { path: 'profile', name: 'dashboard-profile', component: ProfileView },
      ],
    },
  ],
})

// محافظت از مسیرهای داشبورد و جلوگیری از ورود مجدد کاربر لاگین به صفحه Auth
router.beforeEach(async (to) => {
  if (!auth.checked) {
    await checkAuth()
  }

  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    return { name: 'auth' }
  }

  if (to.meta.guestOnly && auth.isLoggedIn) {
    return { name: 'dashboard' }
  }
})

export default router