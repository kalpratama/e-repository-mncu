import { createRouter, createWebHistory } from 'vue-router';
import DashboardPage from '../components/LibDashboardPage.vue';
import LoginPage from '../components/LoginPage.vue';
import ArticlePage from '../components/ArticlePage.vue';
import ArticleInputPage from '../components/ArticleInputPage.vue';
import ProfilePage from '../components/ProfilePage.vue';
import CategoryPage from '../components/CategoryPage.vue';
import SearchPage from '../components/SearchPage.vue';
import ArticleEditPage from '../components/ArticleEditPage.vue';
import YearPage from '../components/YearPage.vue';

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: DashboardPage,
    meta: {title: 'Dashboard | MNCU-IR'},
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    meta: {title: 'Login | MNCU-IR'},
  },
  {
    // The ':id' makes this a dynamic route for any article
    path: '/article/:id', 
    name: 'Article',
    component: ArticlePage,
    meta: {title: 'Artikel | MNCU-IR'},
  }, 
  {
    path: '/admin/articles/create',
    name: 'ArticleCreate',
    component: ArticleInputPage,
    meta: { title: 'Unggah Artikel' }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfilePage,
    meta: {title: 'Profile | MNCU-IR'},
  },
  {
    path: '/category/:slug', // The ':slug' makes it dynamic
    name: 'Category',
    component: CategoryPage,
  },
  {
    path: '/search',
    name: 'Search',
    component: SearchPage,
  },
  {
    path: '/admin/articles/:id/edit',
    name: 'ArticleEdit',
    component: ArticleEditPage,
    meta: { title: 'Edit Artikel' },
  },
  {
    path: '/category/:slug/:year',
    name: 'CategoryYear',
    component: YearPage,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.afterEach((to, from) => {
  document.title = to.meta.title || 'Default Title';
});

export default router;