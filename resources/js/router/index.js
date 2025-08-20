import { createRouter, createWebHistory } from 'vue-router';
import DashboardPage from '../components/LibDashboardPage.vue';
import LoginPage from '../components/LoginPage.vue';
import ArticlePage from '../components/ArticlePage.vue';
import ArticleInputPage from '../components/ArticleInputPage.vue';
import ProfilePage from '../components/ProfilePage.vue';
import CategoryPage from '../components/CategoryPage.vue';
import SearchPage from '../components/SearchPage.vue';
import ArticleEditPage from '../components/ArticleEditPage.vue';
import AdminBackgroundImages from '../components/AdminBackgroundImages.vue';
import RegistrationPage from '../components/RegistrationPage.vue';
import VerifyOTP from '../components/VerifyOTP.vue';
import ManageUserPage from '../components/ManageUserPage.vue';

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: DashboardPage,
    meta: {title: 'Dashboard | MNCU-IR'},
  },
  {
    path: '/register',
    name: 'Register',
    component: RegistrationPage,
    meta: {title: 'Buat Akun | MNCU-IR'},
  },
  {
    path: '/verify-otp',
    name: 'VerifyOTP',
    component: VerifyOTP,
    meta: {title: 'Verifikasi OTP | MNCU-IR'},
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
    meta: { title: 'Unggah Dokumen | MNCU-IR' }
  },
  {
    path: '/admin/articles/:id/edit',
    name: 'ArticleEdit',
    component: ArticleEditPage,
    meta: { title: 'Edit Dokumen | MNCU-IR' },
  },
  {
    path: '/admin/users/manage',
    name: 'UserManage',
    component: ManageUserPage,
    meta: { title: 'Kelola Pengguna | MNCU-IR' },
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfilePage,
    meta: {title: 'Profil | MNCU-IR'},
  },
  {
    path: '/category/:slug?', // The ':slug' makes it dynamic
    name: 'Category',
    component: CategoryPage,
    props: true,
  },
  {
    path: '/search',
    name: 'Search',
    component: SearchPage,
  },
  {
    path: '/admin/background-images',
    name: 'AdminBackgroundImages',
    component: AdminBackgroundImages,
    meta: { title: 'BG Imgs | MNCU-IR' },
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.afterEach((to, from) => {
  document.title = to.meta.title || 'Not Found | MNCU-IR';
});

export default router;