import { createRouter, createWebHistory } from 'vue-router'
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
      path:'/aboutus',
      name:'AboutUs',
      component:AboutUs


    },


    {
  path: '/cart',
  name: 'Cart',
  component: () => import('../views/Cart.vue')
},

    {
      path: '/dashboard',
      component: Dashboard,
      children: [
        { path: '', component: AccountOverview },
        { path: 'orders', component: OrdersView },
        { path: 'wishlist', component: WishlistView },
        { path: 'addresses', component: AddressesView },
        { path: 'profile', component: ProfileView },
      ],
    },
  ],
})

export default router
