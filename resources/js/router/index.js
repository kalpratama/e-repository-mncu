import { createRouter, createWebHistory } from 'vue-router';
import DashboardPage from '../components/LibDashboardPage.vue';
import LoginPage from '../components/LoginPage.vue';
//import ArticlePage from '../components/ArticlePage.vue';

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: DashboardPage,
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
  },
//   {
//     // The ':id' makes this a dynamic route for any article
//     path: '/article/:id', 
//     name: 'Article',
//     component: ArticlePage,
//   },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;